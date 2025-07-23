<?php
include("connection.php");

try {
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
        GROUP BY 
            CASE 
                WHEN fldscore BETWEEN 0 AND 20 THEN '0-20'
                WHEN fldscore BETWEEN 21 AND 40 THEN '21-40'
                WHEN fldscore BETWEEN 41 AND 60 THEN '41-60'
                WHEN fldscore BETWEEN 61 AND 80 THEN '61-80'
                WHEN fldscore BETWEEN 81 AND 90 THEN '81-90'
                ELSE '91-100'
            END
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
            u.flduserfirstname,
            u.flduserlastname,
            u.fldusergrade,
            r.fldscore,
            r.fldtimetaken,
            r.fldcreatedat
        FROM results r
        JOIN users u ON r.flduserid = u.flduserid
        ORDER BY r.fldcreatedat DESC
        LIMIT 5
    ");
    $recentResults = [];
    while ($row = $result->fetch_assoc()) {
        $recentResults[] = $row;
    }
    $dashboardData['recentResults'] = $recentResults;

    // 8. Get grade distribution
    $result = $conn->query("
        SELECT 
            fldusergrade,
            COUNT(*) as count
        FROM users
        GROUP BY fldusergrade
        ORDER BY fldusergrade
    ");
    $gradeDistribution = [];
    while ($row = $result->fetch_assoc()) {
        $gradeDistribution[] = $row;
    }
    $dashboardData['gradeDistribution'] = $gradeDistribution;

    // 9. Get performance metrics
    $result = $conn->query("
        SELECT 
            AVG(fldquestionsattempted) as avg_questions,
            AVG(fldcorrectanswers) as avg_correct,
            AVG(fldcorrectanswers/fldquestionsattempted * 100) as success_rate
        FROM results
        WHERE fldquestionsattempted > 0
    ");
    $performanceMetrics = $result->fetch_assoc();
    $dashboardData['performanceMetrics'] = [
        'avgQuestions' => round($performanceMetrics['avg_questions'], 1),
        'avgCorrect' => round($performanceMetrics['avg_correct'], 1),
        'successRate' => round($performanceMetrics['success_rate'], 1)
    ];

    // Success response
    echo json_encode([
        'status' => 'success',
        'data' => $dashboardData
    ]);

} catch (Exception $e) {
    // Error response
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
} finally {
    // Close connection
    $conn->close();
}

// Helper function to format numbers
function formatNumber($number) {
    if ($number > 1000000) {
        return round($number / 1000000, 1) . 'M';
    } elseif ($number > 1000) {
        return round($number / 1000, 1) . 'K';
    }
    return $number;
}