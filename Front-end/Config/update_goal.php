<?php
include 'db_connect.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: ../goal.php?error=auth");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $goal_id = trim($_POST['goalId']);
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $start_date = trim($_POST['startDate']);
    $end_date = trim($_POST['endDate']);
    $status = trim($_POST['status']);
    $user_id = $_SESSION['id'];

    if (empty($goal_id) || empty($title) || empty($start_date) || empty($end_date) || empty($status)) {
        header("Location: ../goal.php?error=validation");
        exit();
    }

    // Security check: ensure the goal belongs to the user
    $check_sql = "SELECT User_ID FROM goals WHERE Goal_ID = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("i", $goal_id);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows == 0) {
        header("Location: ../goal.php?error=notfound");
        exit();
    }

    $check_stmt->bind_result($owner_id);
    $check_stmt->fetch();
    $check_stmt->close();

    if ($owner_id != $user_id) {
        header("Location: ../goal.php?error=auth");
        exit();
    }

    $sql = "UPDATE goals SET Title = ?, Description = ?, Goal_Start_Time = ?, Goal_End_Time = ?, Status = ? WHERE Goal_ID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssi", $title, $description, $start_date, $end_date, $status, $goal_id);

    if ($stmt->execute()) {
        header("Location: ../goal.php?success=updated");
    } else {
        error_log("Update Goal Error: " . $stmt->error);
        header("Location: ../goal.php?error=db");
    }
    $stmt->close();
    $conn->close();
    exit();
}
?>