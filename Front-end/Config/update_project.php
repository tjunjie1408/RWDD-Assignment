<?php
include 'db_connect.php';

// Check if the user is logged in and is an admin
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['role'] !== 2) {
    header("Location: ../admin_project.php?error=auth");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $project_id = filter_var($_POST['projectId'] ?? '', FILTER_VALIDATE_INT);
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $start_date = trim($_POST['startDate'] ?? '');
    $end_date = trim($_POST['endDate'] ?? '');
    $status = trim($_POST['status'] ?? '');

    if ($project_id === false || empty($title) || empty($start_date) || empty($end_date) || empty($status)) {
        header("Location: ../admin_project.php?error=validation");
        exit();
    }

    $stmt = $conn->prepare("UPDATE projects SET Title = ?, Description = ?, Project_Start_Date = ?, Project_End_Date = ?, Project_Status = ? WHERE Project_ID = ?");
    $stmt->bind_param("sssssi", $title, $description, $start_date, $end_date, $status, $project_id);

    if ($stmt->execute()) {
        header("Location: ../admin_project.php?success=updated");
        exit();
    } else {
        error_log("Update Project Error: " . $stmt->error);
        header("Location: ../admin_project.php?error=db");
        exit();
    }
    $stmt->close();
} else {
    header("Location: ../admin_project.php?error=invalid_request");
    exit();
}

$conn->close();
?>