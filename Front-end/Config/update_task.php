<?php
include 'db_connect.php';

// Admin-only access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 2) {
    header("location: ../project.php?error=auth");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $taskId = $_POST['taskId'];
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $endDate = $_POST['endDate'];
    $projectId = $_POST['projectId'];

    // Validation
    if (empty($title) || empty($endDate) || empty($taskId) || empty($projectId)) {
        header("location: ../tasks.php?project_id=$projectId&error=validation");
        exit;
    }

    $sql = "UPDATE tasks SET Title = ?, Description = ?, Task_End_Time = ? WHERE Task_ID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $title, $description, $endDate, $taskId);

    if ($stmt->execute()) {
        header("location: ../tasks.php?project_id=$projectId&success=task_updated");
    } else {
        header("location: ../tasks.php?project_id=$projectId&error=db");
    }

    $stmt->close();
    $conn->close();
}
?>
