<?php
include("connection.php");

try {
    // Get POST data
    $data = json_decode(file_get_contents('php://input'), true);
    
    // If no data is posted, set default values
    if ($data === null) {
        $data = [
            'studentName' => '',
            'email' => '',
            'dateFrom' => '',
            'dateTo' => ''
        ];
    }

    // Build the base query
    $query = "
        SELECT 
            u.flduserfirstname,
            u.flduserlastname,
            u.flduseremail,
            r.fldtestdate,
            r.fldscore,
            r.fldtimetaken,
            r.fldquestionsattempted,
            r.fldcorrectanswers,
            r.fldresultid
        FROM 
            results r
            JOIN users u ON r.flduserid = u.flduserid
        WHERE 1=1
    ";
    
    $conditions = array();
    $params = array();

    // Add search conditions
    if (!empty($data['studentName'])) {
        $conditions[] = "(u.flduserfirstname LIKE ? OR u.flduserlastname LIKE ?)";
        $params[] = '%' . $data['studentName'] . '%';
        $params[] = '%' . $data['studentName'] . '%';
    }

    if (!empty($data['email'])) {
        $conditions[] = "u.flduseremail LIKE ?";
        $params[] = '%' . $data['email'] . '%';
    }

    if (!empty($data['dateFrom'])) {
        $conditions[] = "DATE(r.fldtestdate) >= ?";
        $params[] = $data['dateFrom'];
    }
    
    if (!empty($data['dateTo'])) {
        $conditions[] = "DATE(r.fldtestdate) <= ?";
        $params[] = $data['dateTo'];
    }

    // Add conditions to query
    if (!empty($conditions)) {
        $query .= " AND " . implode(" AND ", $conditions);
    }

    // Add limit to initial load if no search criteria
    if (empty($conditions)) {
        $query .= " ORDER BY r.fldcreatedat DESC LIMIT 10";
    } else {
        $query .= " ORDER BY r.fldcreatedat DESC";
    }

    // Prepare and execute the main query
    $stmt = $conn->prepare($query);

    // Bind parameters if any exist
    if (!empty($params)) {
        $types = str_repeat('s', count($params)); // Assume all parameters are strings
        $stmt->bind_param($types, ...$params);
    }

    $stmt->execute();
    $result = $stmt->get_result();
    $results = array();

    while ($row = $result->fetch_assoc()) {
        $results[] = $row;
    }

    // Get questions for each result
    foreach ($results as &$result) {
        $questionQuery = "
            SELECT 
                fldquestiontext,
                fldselectedanswer,
                fldcorrectanswer,
                fldcorrectbool
            FROM 
                questionresponses
            WHERE 
                fldresultid = ?
            ORDER BY 
                fldquestionnumber
        ";
        
        $questionStmt = $conn->prepare($questionQuery);
        $questionStmt->bind_param('i', $result['fldresultid']);
        $questionStmt->execute();
        $questionResult = $questionStmt->get_result();
        
        $questions = array();
        while ($questionRow = $questionResult->fetch_assoc()) {
            $questions[] = $questionRow;
        }
        
        $result['questions'] = $questions;
        $result['total_questions'] = count($questions);
        
        $questionStmt->close();
    }

    echo json_encode([
        'status' => 'success',
        'results' => $results
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Error retrieving test results: ' . $e->getMessage()
    ]);
} finally {
    if (isset($stmt)) {
        $stmt->close();
    }
    if (isset($conn)) {
        $conn->close();
    }
}