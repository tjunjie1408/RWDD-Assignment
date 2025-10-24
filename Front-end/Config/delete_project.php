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

    if ($project_id === false) {
        header('HTTP/1.1 400 Bad Request');
        $response['error'] = "Invalid Project ID.";
        echo json_encode($response);
        exit();
    }

    $conn->begin_transaction();

    try {
        // Before deleting tasks, we need to get their IDs to delete associated files
        $stmt_get_tasks = $conn->prepare("SELECT Task_ID FROM tasks WHERE Project_ID = ?");
        $stmt_get_tasks->bind_param("i", $project_id);
        $stmt_get_tasks->execute();
        $result_tasks = $stmt_get_tasks->get_result();
        $task_ids = [];
        while ($row = $result_tasks->fetch_assoc()) {
            $task_ids[] = $row['Task_ID'];
        }
        $stmt_get_tasks->close();

        if (!empty($task_ids)) {
            // Delete files associated with the tasks of the project
            $task_ids_placeholders = implode(',', array_fill(0, count($task_ids), '?'));
            $stmt_delete_files = $conn->prepare("DELETE FROM files WHERE Task_ID IN ($task_ids_placeholders)");
            $types = str_repeat('i', count($task_ids));
            $stmt_delete_files->bind_param($types, ...$task_ids);
            $stmt_delete_files->execute();
            $stmt_delete_files->close();
        }

        // Delete tasks associated with the project
        $stmt_delete_tasks = $conn->prepare("DELETE FROM tasks WHERE Project_ID = ?");
        $stmt_delete_tasks->bind_param("i", $project_id);
        $stmt_delete_tasks->execute();
        $stmt_delete_tasks->close();

        // Finally, delete the project itself
        $stmt_delete_project = $conn->prepare("DELETE FROM projects WHERE Project_ID = ?");
        $stmt_delete_project->bind_param("i", $project_id);
        $stmt_delete_project->execute();
        
        if ($stmt_delete_project->affected_rows > 0) {
            $response['success'] = true;
        } else {
            $response['error'] = 'Project not found or already deleted.';
        }
        $stmt_delete_project->close();

        $conn->commit();

    } catch (Exception $e) {
        $conn->rollback();
        error_log("Delete Project Error: " . $e->getMessage());
        header('HTTP/1.1 500 Internal Server Error');
        $response['error'] = 'Failed to delete project and its related data.';
    }

} else {
    header('HTTP/1.1 405 Method Not Allowed');
    $response['error'] = 'Invalid request method.';
}

$conn->close();
header('Content-Type: application/json');
echo json_encode($response);
?>