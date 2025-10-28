<?php
// Includes the database connection script.
include 'db_connect.php';

// Processes the request only if it's a POST method.
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieves IDs from the POST request.
    $taskId = $_POST['taskId'];
    $projectId = $_POST['projectId'];
    $userId = $_SESSION['id'];

    // --- Ownership/Permission Verification ---
    // Security check: Ensures the task being updated either belongs to the current user or the user is an admin.
    $check_stmt = $conn->prepare("SELECT User_ID FROM tasks WHERE Task_ID = ?");
    $check_stmt->bind_param("i", $taskId);
    $check_stmt->execute();
    $check_stmt->bind_result($task_user_id);
    $check_stmt->fetch();
    $check_stmt->close();

    // If the user is not the task owner and not an admin, access is denied.
    if ($task_user_id != $userId && $_SESSION['role'] !== 2) {
        header("location: ../tasks.php?project_id=$projectId&error=auth");
        exit;
    }

    // --- Toggle Task Status ---
    // Fetches the current status of the task.
    $status_stmt = $conn->prepare("SELECT Status FROM tasks WHERE Task_ID = ?");
    $status_stmt->bind_param("i", $taskId);
    $status_stmt->execute();
    $status_stmt->bind_result($current_status);
    $status_stmt->fetch();
    $status_stmt->close();

    // Toggles the status between 'Done' and 'Open'.
    $new_status = ($current_status === 'Done') ? 'Open' : 'Done';

    // Prepares and executes the statement to update the task's status.
    $update_stmt = $conn->prepare("UPDATE tasks SET Status = ? WHERE Task_ID = ?");
    $update_stmt->bind_param("si", $new_status, $taskId);

    if ($update_stmt->execute()) {
        // --- Recalculate Project Progress ---
        // After a task status changes, the overall project progress is recalculated.
        $progress_stmt = $conn->prepare("SELECT COUNT(*) as total_tasks, SUM(CASE WHEN Status = 'Done' THEN 1 ELSE 0 END) as completed_tasks FROM tasks WHERE Project_ID = ?");
        $progress_stmt->bind_param("i", $projectId);
        $progress_stmt->execute();
        $progress_result = $progress_stmt->get_result()->fetch_assoc();

        $total_tasks = $progress_result['total_tasks'];
        $completed_tasks = $progress_result['completed_tasks'];

        // Calculates the progress percentage.
        $progress_percent = ($total_tasks > 0) ? round(($completed_tasks / $total_tasks) * 100) : 0;

        // Updates the 'Progress_Percent' in the 'projects' table.
        $update_project_stmt = $conn->prepare("UPDATE projects SET Progress_Percent = ? WHERE Project_ID = ?");
        $update_project_stmt->bind_param("ii", $progress_percent, $projectId);
        $update_project_stmt->execute();
        $update_project_stmt->close();

        // Redirects with a success message.
        header("location: ../tasks.php?project_id=$projectId&success=status_updated");
    } else {
        // Redirects with an error message if the update fails.
        header("location: ../tasks.php?project_id=$projectId&error=db");
    }

    $update_stmt->close();
    $progress_stmt->close();
    $conn->close();
}
?>