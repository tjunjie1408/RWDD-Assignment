<?php
// Includes the database connection script and starts the session.
include 'db_connect.php';

// Security check: Ensures the user is logged in before they can create a goal.
// If not logged in, redirects to the goal page with an authentication error.
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: ../goal.php?error=auth");
    exit();
}

// Checks if the request method is POST.
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieves and sanitizes input data from the POST request.
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    // The 'datetime-local' input type from HTML5 provides a string format that MySQL can typically handle.
    $start_date = trim($_POST['startDate']);
    $end_date = trim($_POST['endDate']);
    $status = trim($_POST['status']);
    $user_id = $_SESSION['id']; // Gets the user ID from the current session.

    // Server-side validation: Checks for empty required fields.
    if (empty($title) || empty($start_date) || empty($end_date) || empty($status)) {
        header("Location: ../goal.php?error=validation");
        exit();
    }

    // Prepares the SQL INSERT statement to add a new goal to the database.
    $sql = "INSERT INTO goals (Title, Description, Goal_Start_Time, Goal_End_Time, Status, User_ID) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    // Binds the variables to the prepared statement as parameters.
    $stmt->bind_param("sssssi", $title, $description, $start_date, $end_date, $status, $user_id);

    // Executes the statement.
    if ($stmt->execute()) {
        // If successful, redirects to the goal page with a success message.
        header("Location: ../goal.php?success=created");
    } else {
        // If it fails, logs the specific database error and redirects with a generic database error message.
        error_log("Create Goal Error: " . $stmt->error);
        header("Location: ../goal.php?error=db");
    }
    // Closes the statement and the database connection.
    $stmt->close();
    $conn->close();
    exit();
}
?>