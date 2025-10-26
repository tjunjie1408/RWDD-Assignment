<?php
include 'db_connect.php';

// Auth check
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('HTTP/1.1 401 Unauthorized');
    echo json_encode(['success' => false, 'error' => 'User not logged in.']);
    exit();
}

$response = ['success' => false];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $project_id = filter_var($_POST['projectId'], FILTER_VALIDATE_INT);
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $end_date = trim($_POST['endDate']);
    $assignee_id = filter_var($_POST['assigneeId'], FILTER_VALIDATE_INT);
    $assigner_id = $_SESSION['id'];
    $status = 'Open'; // Default status

    // Basic validation
    if (!$project_id || empty($title) || empty($end_date) || !$assignee_id) {
        $response['error'] = 'Please fill in all required fields.';
        echo json_encode($response);
        exit();
    }

    // Security Check: Ensure assigner is a member of the project
    $check_stmt = $conn->prepare("SELECT Member_ID FROM project_members WHERE Project_ID = ? AND User_ID = ?");
    $check_stmt->bind_param("ii", $project_id, $assigner_id);
    $check_stmt->execute();
    $check_stmt->store_result();
    if ($check_stmt->num_rows === 0) {
        $response['error'] = 'You are not a member of this project.';
        echo json_encode($response);
        exit();
    }
    $check_stmt->close();

    // Insert the new task
    $sql = "INSERT INTO tasks (Project_ID, Title, Description, Status, Task_End_Time, Assigner_ID, User_ID) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isssiii", $project_id, $title, $description, $status, $end_date, $assigner_id, $assignee_id);

    if ($stmt->execute()) {
        header("Location: ../tasks.php?project_id=" . $project_id . "&task_action=created");
    } else {
        error_log("Create Task Error: " . $stmt->error);
        header("Location: ../tasks.php?project_id=" . $project_id . "&error=task_creation_failed");
    }
    $stmt->close();

} else {
    header("Location: ../tasks.php?error=invalid_request");
}

$conn->close();
exit();
?>