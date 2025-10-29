<?php
// Includes the database connection script.
include 'db_connect.php';

// Security check: Ensures only an admin can update task details.
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 2) {
    header("location: ../project.php?error=auth");
    exit;
}

// Processes the request only if it's a POST method.
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieves task data from the POST request.
    $taskId = $_POST['taskId'];
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $endDate = $_POST['endDate'];
    $projectId = $_POST['projectId'];

    // Server-side validation for required fields.
    if (empty($title) || empty($endDate) || empty($taskId) || empty($projectId)) {
        header("location: ../tasks.php?project_id=$projectId&error=validation");
        exit;
    }

    // Prepares the SQL statement to update the task.
    $sql = "UPDATE tasks SET Title = ?, Description = ?, Task_End_Time = ? WHERE Task_ID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $title, $description, $endDate, $taskId);

    // Executes the statement.
    if ($stmt->execute()) {
        // On success, redirects to the project's tasks page with a success message.
        header("location: ../tasks.php?project_id=$projectId&success=task_updated");
    } else {
        // On failure, redirects with a database error message.
        header("location: ../tasks.php?project_id=$projectId&error=db");
    }

    // Closes the statement and the database connection.
    $stmt->close();
    $conn->close();
}
?>
