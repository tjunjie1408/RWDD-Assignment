<?php
include 'db_connect.php';

// Admin-only access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 2) {
    header("location: ../project.php?error=auth");
    exit;
}

$taskId = isset($_GET['task_id']) ? filter_var($_GET['task_id'], FILTER_VALIDATE_INT) : null;
$projectId = isset($_GET['project_id']) ? filter_var($_GET['project_id'], FILTER_VALIDATE_INT) : null;

if (!$taskId || !$projectId) {
    header("location: ../tasks.php?project_id=$projectId&error=invalid_id");
    exit;
}

// First, delete any associated files from the server and the database
$stmt = $conn->prepare("SELECT File_Path FROM files WHERE Task_ID = ?");
$stmt->bind_param("i", $taskId);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    if (file_exists($row['File_Path'])) {
        unlink($row['File_Path']);
    }
}
$stmt->close();

$stmt = $conn->prepare("DELETE FROM files WHERE Task_ID = ?");
$stmt->bind_param("i", $taskId);
$stmt->execute();
$stmt->close();

// Now, delete the task itself
$stmt = $conn->prepare("DELETE FROM tasks WHERE Task_ID = ?");
$stmt->bind_param("i", $taskId);

if ($stmt->execute()) {
    header("location: ../tasks.php?project_id=$projectId&success=task_deleted");
} else {
    header("location: ../tasks.php?project_id=$projectId&error=db");
}

$stmt->close();
$conn->close();
?>
