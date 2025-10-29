<?php
// Includes the database connection script.
include 'db_connect.php';

// Security check: Verifies that the user is logged in and has an admin role (Role_ID = 2).
// If the check fails, it returns a 403 Forbidden error and stops execution.
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['role'] !== 2) {
    header('HTTP/1.1 403 Forbidden');
    echo json_encode(['success' => false, 'error' => 'Access denied. Administrator privileges required.']);
    exit();
}

// Initializes the response array that will be sent back in JSON format.
$response = ['success' => false, 'error' => ''];

// Processes the request only if the method is POST.
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieves and validates the member ID from the POST data.
    // 'filter_var' with 'FILTER_VALIDATE_INT' ensures the ID is an integer.
    $member_id = filter_var($_POST['memberId'] ?? '', FILTER_VALIDATE_INT);

    // If the member ID is not a valid integer, returns a 400 Bad Request error.
    if ($member_id === false) {
        header('HTTP/1.1 400 Bad Request');
        $response['error'] = "Invalid member ID.";
        echo json_encode($response);
        exit();
    }

    // Security measure: Prevents an admin from deleting their own account.
    if ($member_id == $_SESSION['id']) {
        header('HTTP/1.1 403 Forbidden');
        $response['error'] = "You cannot delete your own account.";
        echo json_encode($response);
        exit();
    }

    // Prepares a SQL statement to delete a user.
    // The 'AND Role_ID = 1' clause is a crucial security check to ensure admins can only delete regular users, not other admins.
    $stmt = $conn->prepare("DELETE FROM users WHERE user_ID = ? AND Role_ID = 1");
    $stmt->bind_param("i", $member_id);

    // Executes the delete statement.
    if ($stmt->execute()) {
        // If deletion is successful, sets the success flag to true.
        $response['success'] = true;
    } else {
        // If deletion fails, logs the error and returns a 500 Internal Server Error.
        error_log("Admin Delete Member Error: " . $stmt->error);
        header('HTTP/1.1 500 Internal Server Error');
        $response['error'] = 'Failed to delete member.';
    }
    $stmt->close();
}
// Closes the database connection.
$conn->close();
// Sets the response header to indicate JSON content.
header('Content-Type: application/json');
// Encodes the response array to JSON and outputs it.
echo json_encode($response);
?>