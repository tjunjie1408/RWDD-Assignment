<?php
// Includes the database connection script.
include 'db_connect.php';

// Security check: Ensures the user is logged in.
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: ../goal.php?error=auth");
    exit();
}

// Processes the request only if it's a POST method.
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieves and sanitizes goal data from the POST request.
    $goal_id = trim($_POST['goalId']);
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $start_date = trim($_POST['startDate']);
    $end_date = trim($_POST['endDate']);
    $status = trim($_POST['status']);
    $user_id = $_SESSION['id'];

    // Server-side validation for required fields.
    if (empty($goal_id) || empty($title) || empty($start_date) || empty($end_date) || empty($status)) {
        header("Location: ../goal.php?error=validation");
        exit();
    }

    // --- Ownership Verification ---
    // Security check to ensure the goal being updated belongs to the logged-in user.
    $check_sql = "SELECT User_ID FROM goals WHERE Goal_ID = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("i", $goal_id);
    $check_stmt->execute();
    $check_stmt->store_result();

    // Checks if the goal exists.
    if ($check_stmt->num_rows == 0) {
        header("Location: ../goal.php?error=notfound");
        exit();
    }

    $check_stmt->bind_result($owner_id);
    $check_stmt->fetch();
    $check_stmt->close();

    // If the goal's owner is not the current user, access is denied.
    if ($owner_id != $user_id) {
        header("Location: ../goal.php?error=auth");
        exit();
    }

    // --- Database Update ---
    // Prepares the SQL statement to update the goal.
    $sql = "UPDATE goals SET Title = ?, Description = ?, Goal_Start_Time = ?, Goal_End_Time = ?, Status = ? WHERE Goal_ID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssi", $title, $description, $start_date, $end_date, $status, $goal_id);

    // Executes the statement.
    if ($stmt->execute()) {
        // On success, redirects to the goal page with a success message.
        header("Location: ../goal.php?success=updated");
    } else {
        // On failure, logs the error and redirects with a database error message.
        error_log("Update Goal Error: " . $stmt->error);
        header("Location: ../goal.php?error=db");
    }
    $stmt->close();
    $conn->close();
    exit();
}
?>