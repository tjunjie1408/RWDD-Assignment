<?php
// Includes the database connection script.
include 'db_connect.php';

// Security check: Ensures the user is logged in and has admin privileges.
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['role'] !== 2) {
    header('HTTP/1.1 403 Forbidden');
    echo json_encode(['success' => false, 'error' => 'Access denied. Administrator privileges required.']);
    exit();
}

// Initializes the response array.
$response = ['success' => false, 'error' => ''];

// Processes the request only if it's a POST method.
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieves and validates the project ID from the POST data.
    $project_id = filter_var($_POST['projectId'] ?? '', FILTER_VALIDATE_INT);

    // If the project ID is invalid, returns a 400 Bad Request error.
    if ($project_id === false) {
        header('HTTP/1.1 400 Bad Request');
        $response['error'] = "Invalid Project ID.";
        echo json_encode($response);
        exit();
    }

    // Starts a database transaction. This allows for rolling back changes if any part of the deletion process fails.
    $conn->begin_transaction();

    try {
        // Step 1: Find all tasks associated with the project to handle their related data.
        $stmt_get_tasks = $conn->prepare("SELECT Task_ID FROM tasks WHERE Project_ID = ?");
        $stmt_get_tasks->bind_param("i", $project_id);
        $stmt_get_tasks->execute();
        $result_tasks = $stmt_get_tasks->get_result();
        $task_ids = [];
        while ($row = $result_tasks->fetch_assoc()) {
            $task_ids[] = $row['Task_ID'];
        }
        $stmt_get_tasks->close();

        // Step 2: If tasks exist, delete all files associated with those tasks.
        if (!empty($task_ids)) {
            // Creates a placeholder string for the IN clause (e.g., "?,?,?").
            $task_ids_placeholders = implode(',', array_fill(0, count($task_ids), '?'));
            $stmt_delete_files = $conn->prepare("DELETE FROM files WHERE Task_ID IN ($task_ids_placeholders)");
            $types = str_repeat('i', count($task_ids)); // Creates a string of 'i' types for bind_param.
            $stmt_delete_files->bind_param($types, ...$task_ids);
            $stmt_delete_files->execute();
            $stmt_delete_files->close();
        }

        // Step 3: Delete all tasks associated with the project.
        $stmt_delete_tasks = $conn->prepare("DELETE FROM tasks WHERE Project_ID = ?");
        $stmt_delete_tasks->bind_param("i", $project_id);
        $stmt_delete_tasks->execute();
        $stmt_delete_tasks->close();

        // Step 4: Delete the project itself.
        $stmt_delete_project = $conn->prepare("DELETE FROM projects WHERE Project_ID = ?");
        $stmt_delete_project->bind_param("i", $project_id);
        $stmt_delete_project->execute();
        
        // Checks if the project was actually deleted.
        if ($stmt_delete_project->affected_rows > 0) {
            $response['success'] = true;
        } else {
            $response['error'] = 'Project not found or already deleted.';
        }
        $stmt_delete_project->close();

        // If all steps are successful, commit the transaction to make the changes permanent.
        $conn->commit();

    } catch (Exception $e) {
        // If any error occurs during the try block, roll back all database changes.
        $conn->rollback();
        error_log("Delete Project Error: " . $e->getMessage());
        header('HTTP/1.1 500 Internal Server Error');
        $response['error'] = 'Failed to delete project and its related data.';
    }

} else {
    // If the request method is not POST, returns a 405 Method Not Allowed error.
    header('HTTP/1.1 405 Method Not Allowed');
    $response['error'] = 'Invalid request method.';
}

// Closes the database connection.
$conn->close();
// Sets the content type to JSON.
header('Content-Type: application/json');
// Sends the JSON response.
echo json_encode($response);
?>