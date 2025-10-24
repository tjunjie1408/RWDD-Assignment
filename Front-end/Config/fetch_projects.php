<?php
include 'db_connect.php';

// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('HTTP/1.1 401 Unauthorized');
    echo json_encode(['success' => false, 'error' => 'User not logged in.']);
    exit();
}

$response = ['success' => false, 'projects' => []];

$stmt = $conn->prepare("SELECT Project_ID, Title, Description, Project_Start_Time, Project_End_Time, Project_Status FROM projects ORDER BY Project_Start_Time DESC");

if ($stmt->execute()) {
    $result = $stmt->get_result();
    $projects = [];
    while ($row = $result->fetch_assoc()) {
        $projects[] = $row;
    }
    $response['success'] = true;
    $response['projects'] = $projects;
} else {
    error_log("Fetch Projects Error: " . $stmt->error);
    $response['error'] = 'Failed to fetch projects.';
}

$stmt->close();
$conn->close();
header('Content-Type: application/json');
echo json_encode($response);
?>