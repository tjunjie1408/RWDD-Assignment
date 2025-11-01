<?php
// Includes the database connection script.
include 'db_connect.php';

// Security check: Ensures only an admin can delete a task.
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 2) {
    header("location: ../project.php?error=auth");
    exit;
}

// Retrieves and validates the task and project IDs from the GET request.
$taskId = isset($_GET['task_id']) ? filter_var($_GET['task_id'], FILTER_VALIDATE_INT) : null;
$projectId = isset($_GET['project_id']) ? filter_var($_GET['project_id'], FILTER_VALIDATE_INT) : null;

// If either ID is invalid, redirects with an error.
if (!$taskId || !$projectId) {
    header("location: ../tasks.php?project_id=$projectId&error=invalid_id");
    exit;
}

// Step 1: Delete physical files associated with the task from the server.
// This prevents orphaned files from accumulating on the server's storage.
$stmt = $conn->prepare("SELECT File_URL FROM files WHERE Task_ID = ?");
$stmt->bind_param("i", $taskId);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    // Checks if the file exists before attempting to delete it.
    if (file_exists($row['File_URL'])) {
        unlink($row['File_URL']);
    }
}
$stmt->close();

// Step 2: Delete the file records from the 'files' table in the database.
$stmt = $conn->prepare("DELETE FROM files WHERE Task_ID = ?");
$stmt->bind_param("i", $taskId);
$stmt->execute();
$stmt->close();

// Step 3: Delete the task itself from the 'tasks' table.
$stmt = $conn->prepare("DELETE FROM tasks WHERE Task_ID = ?");
$stmt->bind_param("i", $taskId);

// Executes the final delete statement.
if ($stmt->execute()) {
    // On success, redirects to the project's tasks page with a success message.
    header("location: ../tasks.php?project_id=$projectId&success=task_deleted");
} else {
    // On failure, redirects with a database error message.
    header("location: ../tasks.php?project_id=$projectId&error=db");
}

// Closes the statement and the database connection.
$stmt->close();
$conn->close();
?>