<?php
include 'db_connect.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('HTTP/1.1 401 Unauthorized');
    echo json_encode(['success' => false, 'error' => 'User not logged in.']);
    exit();
}

$response = ['success' => false];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $task_id = filter_var($_POST['taskId'] ?? null, FILTER_VALIDATE_INT);
    $project_id = filter_var($_POST['projectId'] ?? null, FILTER_VALIDATE_INT);
    $is_done = isset($_POST['status']); // Checkbox sends 'on' if checked, not set if unchecked
    $user_id = $_SESSION['id'];

    if ($task_id === false || $project_id === false) {
        header("Location: ../project.php?error=invalid_input");
        exit();
    }

    $new_status = $is_done ? 'Done' : 'Open';

    // Security Check: Verify the task is assigned to the current user
    $check_sql = "SELECT Task_ID FROM tasks WHERE Task_ID = ? AND User_ID = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("ii", $task_id, $user_id);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows === 0) {
        $check_stmt->close();
        header("Location: ../tasks.php?project_id=" . $project_id . "&error=auth_failed");
        exit();
    }
    $check_stmt->close();

    // Update the task status and completion date
    $completed_date = $is_done ? date("Y-m-d") : null;
    $update_sql = "UPDATE tasks SET Status = ?, Task_Completed_Date = ? WHERE Task_ID = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("ssi", $new_status, $completed_date, $task_id);
    $stmt->execute();
    $stmt->close();

    // Redirect back to the tasks page
    header("Location: ../tasks.php?project_id=" . $project_id);
    exit();

} else {
    header("Location: ../project.php?error=invalid_request");
    exit();
}
?>