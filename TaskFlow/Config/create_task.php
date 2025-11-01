<?php
// Includes the database connection script.
include 'db_connect.php';

// Security check: This script is for admin use only (Role_ID = 2).
// If a non-admin tries to access it, they are redirected.
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 2) {
    header("location: ../project.php?error=auth");
    exit;
}

// Processes the request only if the method is POST.
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieves and sanitizes task data from the POST request.
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $endDate = $_POST['endDate'];
    $assigneeId = $_POST['assigneeId']; // The user the task is assigned to.
    $projectId = $_POST['projectId'];
    $assignerId = $_SESSION['id']; // The admin creating the task.

    // Server-side validation to ensure all required fields are filled.
    if (empty($title) || empty($endDate) || empty($assigneeId) || empty($projectId)) {
        header("location: ../tasks.php?project_id=$projectId&error=validation");
        exit;
    }

    // Prepares the SQL statement to insert a new task.
    // The default status for a new task is set to 'Open'.
    $sql = "INSERT INTO tasks (Title, Description, Task_End_Time, User_ID, Project_ID, Assigner_ID, Status) VALUES (?, ?, ?, ?, ?, ?, 'Open')";
    $stmt = $conn->prepare($sql);
    // Binds the variables to the prepared statement.
    $stmt->bind_param("sssiis", $title, $description, $endDate, $assigneeId, $projectId, $assignerId);

    // Executes the statement.
    if ($stmt->execute()) {
        // On success, redirects to the tasks page for the project with a success message.
        header("location: ../tasks.php?project_id=$projectId&success=task_created");
    } else {
        // On failure, redirects with a database error message.
        header("location: ../tasks.php?project_id=$projectId&error=db");
    }

    // Closes the statement and the database connection.
    $stmt->close();
    $conn->close();
}
?>