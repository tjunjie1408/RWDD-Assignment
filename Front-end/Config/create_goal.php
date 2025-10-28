<?php
include 'db_connect.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: ../goal.php?error=auth");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    // The datetime-local input sends a string in 'Y-m-d\TH:i' format, which MySQL understands.
    $start_date = trim($_POST['startDate']);
    $end_date = trim($_POST['endDate']);
    $status = trim($_POST['status']);
    $user_id = $_SESSION['id'];

    if (empty($title) || empty($start_date) || empty($end_date) || empty($status)) {
        header("Location: ../goal.php?error=validation");
        exit();
    }

    $sql = "INSERT INTO goals (Title, Description, Goal_Start_Time, Goal_End_Time, Status, User_ID) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssi", $title, $description, $start_date, $end_date, $status, $user_id);

    if ($stmt->execute()) {
        header("Location: ../goal.php?success=created");
    } else {
        error_log("Create Goal Error: " . $stmt->error);
        header("Location: ../goal.php?error=db");
    }
    $stmt->close();
    $conn->close();
    exit();
}
?>