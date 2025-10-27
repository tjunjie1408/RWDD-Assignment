<?php
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $taskId = $_POST['taskId'];
    $projectId = $_POST['projectId'];
    $userId = $_SESSION['id'];

    // Security check: Ensure the task belongs to the user
    $check_stmt = $conn->prepare("SELECT User_ID FROM tasks WHERE Task_ID = ?");
    $check_stmt->bind_param("i", $taskId);
    $check_stmt->execute();
    $check_stmt->bind_result($task_user_id);
    $check_stmt->fetch();
    $check_stmt->close();

    if ($task_user_id != $userId && $_SESSION['role'] !== 2) {
        header("location: ../tasks.php?project_id=$projectId&error=auth");
        exit;
    }

    // Update task status to 'Done'
    $update_stmt = $conn->prepare("UPDATE tasks SET Status = 'Done' WHERE Task_ID = ?");
    $update_stmt->bind_param("i", $taskId);

    if ($update_stmt->execute()) {
        // Recalculate project progress
        $progress_stmt = $conn->prepare("SELECT COUNT(*) as total_tasks, SUM(CASE WHEN Status = 'Done' THEN 1 ELSE 0 END) as completed_tasks FROM tasks WHERE Project_ID = ?");
        $progress_stmt->bind_param("i", $projectId);
        $progress_stmt->execute();
        $progress_result = $progress_stmt->get_result()->fetch_assoc();

        $total_tasks = $progress_result['total_tasks'];
        $completed_tasks = $progress_result['completed_tasks'];

        $progress_percent = ($total_tasks > 0) ? ($completed_tasks / $total_tasks) * 100 : 0;

        $update_project_stmt = $conn->prepare("UPDATE projects SET Progress_Percent = ? WHERE Project_ID = ?");
        $update_project_stmt->bind_param("di", $progress_percent, $projectId);
        $update_project_stmt->execute();
        $update_project_stmt->close();

        header("location: ../tasks.php?project_id=$projectId&success=status_updated");
    } else {
        header("location: ../tasks.php?project_id=$projectId&error=db");
    }

    $update_stmt->close();
    $progress_stmt->close();
    $conn->close();
}
?>
