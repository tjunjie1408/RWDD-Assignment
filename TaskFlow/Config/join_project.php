<?php
// Includes the database connection script.
include 'db_connect.php';

// Security check: Ensures the user is logged in before they can join a project.
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('HTTP/1.1 401 Unauthorized');
    echo json_encode(['success' => false, 'error' => 'You must be logged in to join a project.']);
    exit();
}

// Initializes the response array.
$response = ['success' => false, 'error' => ''];

// Processes the request only if it's a POST method.
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieves and validates the project ID from the POST data.
    $project_id = filter_var($_POST['projectId'] ?? '', FILTER_VALIDATE_INT);
    $user_id = $_SESSION['id']; // Gets the current user's ID from the session.

    // If the project ID is invalid, returns a 400 Bad Request error.
    if ($project_id === false) {
        header('HTTP/1.1 400 Bad Request');
        $response['error'] = "Invalid Project ID.";
        echo json_encode($response);
        exit();
    }

    // Checks if the user is already a member of the project to prevent duplicate entries.
    $stmt_check = $conn->prepare("SELECT Member_ID FROM project_members WHERE Project_ID = ? AND User_ID = ?");
    $stmt_check->bind_param("ii", $project_id, $user_id);
    $stmt_check->execute();
    $stmt_check->store_result();

    // If the user is already a member, redirects back to the project page with an error message.
    if ($stmt_check->num_rows > 0) {
        $stmt_check->close();
        header("Location: ../project.php?join_error=already_member");
        exit();
    }
    $stmt_check->close();

    // If the user is not a member, prepares and executes a statement to insert them into the 'project_members' table.
    $stmt_insert = $conn->prepare("INSERT INTO project_members (Project_ID, User_ID) VALUES (?, ?)");
    $stmt_insert->bind_param("ii", $project_id, $user_id);

    // On successful insertion, redirects to the project page with a success message.
    if ($stmt_insert->execute()) {
        $response['success'] = true;
        header("Location: ../project.php?join=success");
        exit();
    } else {
        // If insertion fails, logs the error and redirects with a database error message.
        error_log("Join Project Error: " . $stmt_insert->error);
        header("Location: ../project.php?join_error=db_error");
        exit();
    }
    $stmt_insert->close();

} else {
    // If the request method is not POST, redirects with an invalid request error.
    header("Location: ../project.php?join_error=invalid_request");
    exit();
}

// Closes the database connection.
$conn->close();
?>