<?php
// Includes the database connection script.
include 'db_connect.php';

// Security check: Ensures the user is logged in and has admin privileges.
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['role'] !== 2) {
    header("Location: ../admin_project.php?error=auth");
    exit();
}

// Processes the request only if it's a POST method.
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieves and validates project data from the POST request.
    $project_id = filter_var($_POST['projectId'] ?? '', FILTER_VALIDATE_INT);
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $start_date = trim($_POST['startDate'] ?? '');
    $end_date = trim($_POST['endDate'] ?? '');
    $status = trim($_POST['status'] ?? '');

    // Server-side validation for required fields.
    if ($project_id === false || empty($title) || empty($start_date) || empty($end_date) || empty($status)) {
        header("Location: ../admin_project.php?error=validation");
        exit();
    }

    // Prepares the SQL statement to update the project details.
    $stmt = $conn->prepare("UPDATE projects SET Title = ?, Description = ?, Project_Start_Date = ?, Project_End_Date = ?, Project_Status = ? WHERE Project_ID = ?");
    $stmt->bind_param("sssssi", $title, $description, $start_date, $end_date, $status, $project_id);

    // Executes the statement.
    if ($stmt->execute()) {
        // On success, redirects to the admin project page with a success message.
        header("Location: ../admin_project.php?success=updated");
        exit();
    } else {
        // On failure, logs the error and redirects with a database error message.
        error_log("Update Project Error: " . $stmt->error);
        header("Location: ../admin_project.php?error=db");
        exit();
    }
    $stmt->close();
} else {
    // If the request method is not POST, redirects with an invalid request error.
    header("Location: ../admin_project.php?error=invalid_request");
    exit();
}

// Closes the database connection.
$conn->close();
?>