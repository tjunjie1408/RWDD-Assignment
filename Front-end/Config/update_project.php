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
    $project_id = filter_var($_POST['projectId'] ?? '', FILTER_VALIDATE_INT);
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $start_date = trim($_POST['startDate'] ?? '');
    $end_date = trim($_POST['endDate'] ?? '');
    $status = trim($_POST['status'] ?? '');

    if ($project_id === false || empty($title) || empty($start_date) || empty($end_date) || empty($status)) {
        header('HTTP/1.1 400 Bad Request');
        $response['error'] = "Invalid input or missing required fields.";
        echo json_encode($response);
        exit();
    }

    $stmt = $conn->prepare("UPDATE projects SET Title = ?, Description = ?, Project_Start_Time = ?, Project_End_Time = ?, Project_Status = ? WHERE Project_ID = ?");
    $stmt->bind_param("sssssi", $title, $description, $start_date, $end_date, $status, $project_id);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            $response['success'] = true;
        } else {
            $response['success'] = true; // Still success, even if no data was changed
            $response['message'] = 'No changes were made to the project.';
        }
    } else {
        error_log("Update Project Error: " . $stmt->error);
        header('HTTP/1.1 500 Internal Server Error');
        $response['error'] = 'Failed to update project.';
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