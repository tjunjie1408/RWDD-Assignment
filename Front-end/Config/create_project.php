<?php
include 'db_connect.php';

// Check if the user is logged in and is an admin
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['role'] !== 2) {
    header('HTTP/1.1 403 Forbidden');
    echo json_encode(['success' => false, 'error' => 'Access denied. Administrator privileges required.']);
    exit();
}

$response = ['success' => false, 'error' => ''];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $start_date = trim($_POST['startDate'] ?? '');
    $end_date = trim($_POST['endDate'] ?? '');
    $admin_id = $_SESSION['id'];
    $status = 'Not Started';

    if (empty($title) || empty($start_date) || empty($end_date)) {
        header('HTTP/1.1 400 Bad Request');
        $response['error'] = "Title, start date, and end date are required.";
        echo json_encode($response);
        exit();
    }

    $stmt = $conn->prepare("INSERT INTO projects (Title, Description, Project_Start_Time, Project_End_Time, Project_Status, User_ID) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssi", $title, $description, $start_date, $end_date, $status, $admin_id);

    if ($stmt->execute()) {
        $response['success'] = true;
        $response['projectId'] = $conn->insert_id;
    } else {
        error_log("Create Project Error: " . $stmt->error);
        header('HTTP/1.1 500 Internal Server Error');
        $response['error'] = 'Failed to create project.';
    }
    $stmt->close();
} else {
    header('HTTP/1.1 405 Method Not Allowed');
    $response['error'] = 'Invalid request method.';
}

$conn->close();
header('Content-Type: application/json');
echo json_encode($response);
?>