<?php
// Start session with enhanced security
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1);
ini_set('session.use_strict_mode', 1);
ini_set('session.cookie_samesite', 'Strict');
session_start();

// Regenerate session ID periodically to prevent session fixation
if (!isset($_SESSION['created'])) {
    $_SESSION['created'] = time();
} else if (time() - $_SESSION['created'] > 1800) {
    session_regenerate_id(true);
    $_SESSION['created'] = time();
}

// Set security headers
header("Content-Security-Policy: default-src 'self' https://use.fontawesome.com https://stackpath.bootstrapcdn.com https://cdnjs.cloudflare.com; script-src 'self' https://cdnjs.cloudflare.com 'unsafe-inline'; style-src 'self' https://use.fontawesome.com https://stackpath.bootstrapcdn.com 'unsafe-inline'; img-src 'self' data: https:; font-src 'self' https://use.fontawesome.com https://stackpath.bootstrapcdn.com");
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: SAMEORIGIN");
header("X-XSS-Protection: 1; mode=block");
header("Referrer-Policy: strict-origin-when-cross-origin");
//header("Permissions-Policy: geolocation=(), microphone=(), camera=()");

//if user has already logged in then take user to account page
if(!isset($_SESSION['logged_in'])){
	header('location: login.php?error=User Not Logged In.');
	exit;
}

include("connection.php");

if(isset($_SESSION['logged_in'])){
    try {
        // Get POST data
        $data = json_decode(file_get_contents('php://input'), true);

        // Initialize response array
        $dashboardData = array();

        // 1. Get total number of students
        $result = $conn->query("SELECT COUNT(DISTINCT flduserid) as total_students FROM users");
        $row = $result->fetch_assoc();
        $dashboardData['totalStudents'] = $row['total_students'];

        // 2. Get average score
        $result = $conn->query("SELECT AVG(fldscore) as average_score FROM results");
        $row = $result->fetch_assoc();
        $dashboardData['averageScore'] = round($row['average_score'], 1);

        // 3. Get total tests completed
        $result = $conn->query("SELECT COUNT(*) as tests_completed FROM results");
        $row = $result->fetch_assoc();
        $dashboardData['testsCompleted'] = $row['tests_completed'];

        // 4. Get average time taken
        $result = $conn->query("SELECT AVG(fldtimetaken) as average_time FROM results");
        $row = $result->fetch_assoc();
        $dashboardData['averageTime'] = round($row['average_time'], 1);

        // 5. Get score distribution
        $result = $conn->query("
            SELECT 
                CASE 
                    WHEN fldscore BETWEEN 0 AND 20 THEN '0-20'
                    WHEN fldscore BETWEEN 21 AND 40 THEN '21-40'
                    WHEN fldscore BETWEEN 41 AND 60 THEN '41-60'
                    WHEN fldscore BETWEEN 61 AND 80 THEN '61-80'
                    WHEN fldscore BETWEEN 81 AND 90 THEN '81-90'
                    ELSE '91-100'
                END as score_range,
                COUNT(*) as count
            FROM results
            GROUP BY score_range
            ORDER BY score_range
        ");
        
        $scoreDistribution = [];
        while ($row = $result->fetch_assoc()) {
            $scoreDistribution[] = $row['count'];
        }
        $dashboardData['scoreDistribution'] = $scoreDistribution;

        // 6. Get performance trend (last 6 months)
        $result = $conn->query("
            SELECT 
                DATE_FORMAT(fldcreatedat, '%b') as month,
                AVG(fldscore) as average_score
            FROM results
            WHERE fldcreatedat >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
            GROUP BY DATE_FORMAT(fldcreatedat, '%Y-%m')
            ORDER BY fldcreatedat DESC
            LIMIT 6
        ");
        
        $performanceTrend = [];
        while ($row = $result->fetch_assoc()) {
            $performanceTrend[] = $row;
        }
        $dashboardData['performanceTrend'] = [
            'labels' => array_reverse(array_column($performanceTrend, 'month')),
            'data' => array_reverse(array_map(function($item) {
                return round($item['average_score'], 1);
            }, $performanceTrend))
        ];

        // 7. Get recent test results
        $result = $conn->query("
            SELECT 
                u.flduserfirstname as name,
                u.fldusergrade as grade,
                r.fldscore as score,
                r.fldtimetaken as time_taken,
                r.fldcreatedat
            FROM results r
            JOIN users u ON r.flduserid = u.flduserid
            ORDER BY r.fldcreatedat DESC
            LIMIT 5
        ");
        
        $recentResults = [];
        while ($row = $result->fetch_assoc()) {
            $recentResults[] = [
                'name' => $row['name'],
                'grade' => $row['grade'],
                'score' => $row['score'],
                'time_taken' => $row['time_taken']
            ];
        }
        $dashboardData['recentResults'] = $recentResults;

        // If saving new test results
        if ($data && isset($data['score'])) {
            // Start transaction
            $conn->begin_transaction();

            // Insert test results (existing code)
            $stmt = $conn->prepare("
                INSERT INTO results 
                (flduserid, fldscore, fldtimetaken, fldquestionsattempted, fldcorrectanswers, fldcreatedat) 
                VALUES (?, ?, ?, ?, ?, NOW())
            ");
            
            $stmt->bind_param("iiiii", 
                $_SESSION['flduserid'],
                $data['score'],
                $data['timeTaken'],
                $data['questionsAttempted'],
                $data['correctAnswers']
            );
            
            $stmt->execute();
            $resultId = $conn->insert_id;
            $stmt->close();

            // Insert question responses
            if (isset($data['questions']) && is_array($data['questions'])) {
                $stmt = $conn->prepare("
                    INSERT INTO questionresponses 
                    (fldresultid, fldquestionnumber, fldquestiontext, fldselectedanswer, 
                    fldcorrectanswer, fldcorrectbool, fldcreatedat)
                    VALUES (?, ?, ?, ?, ?, ?, NOW())
                ");

                foreach ($data['questions'] as $index => $question) {
                    $questionNumber = $index + 1;
                    $isCorrect = $question['isCorrect'] ? 1 : 0;
                    
                    $stmt->bind_param("iisssi",
                        $resultId,
                        $questionNumber,
                        $question['questionText'],
                        $question['selectedAnswer'],
                        $question['correctAnswer'],
                        $isCorrect
                    );
                    
                    $stmt->execute();
                }
                
                $stmt->close();
            }

            // Commit transaction
            $conn->commit();

            $dashboardData['newResultId'] = $resultId;
        }

        // Success response
        echo json_encode([
            'status' => 'success',
            'data' => $dashboardData
        ]);

    } catch (Exception $e) {
        if (isset($conn)) {
            $conn->rollback();
        }
        
        http_response_code(500);
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);

    } finally {
        if (isset($conn)) {
            $conn->close();
        }
    }
} else{
    header('location: ../login.php?error=User Not Logged In.');
    exit;
}