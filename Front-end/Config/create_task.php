<?php
include 'db_connect.php';

// Admin-only access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 2) {
    header("location: ../project.php?error=auth");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $endDate = $_POST['endDate'];
    $assigneeId = $_POST['assigneeId'];
    $projectId = $_POST['projectId'];
    $assignerId = $_SESSION['id'];

    // Validation
    if (empty($title) || empty($endDate) || empty($assigneeId) || empty($projectId)) {
        header("location: ../tasks.php?project_id=$projectId&error=validation");
        exit;
    }

    $sql = "INSERT INTO tasks (Title, Description, Task_End_Time, User_ID, Project_ID, Assigner_ID, Status) VALUES (?, ?, ?, ?, ?, ?, 'Open')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssiis", $title, $description, $endDate, $assigneeId, $projectId, $assignerId);

    if ($stmt->execute()) {
        header("location: ../tasks.php?project_id=$projectId&success=task_created");
    } else {
        header("location: ../tasks.php?project_id=$projectId&error=db");
    }

    $stmt->close();
    $conn->close();
}
?>