<?php
// Includes the database connection script.
include 'db_connect.php';

// Security check: Ensures the user is logged in.
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('HTTP/1.1 401 Unauthorized');
    echo json_encode(['error' => 'User not logged in']);
    exit();
}

// Gets the user ID from the session.
$userId = $_SESSION['id'];
// Retrieves the JSON payload sent from the frontend and decodes it into a PHP associative array.
$data = json_decode(file_get_contents("php://input"), true);

// Extracts user data from the decoded JSON.
$username = $data['username'];
$email = $data['email'];
$company = $data['company'];
$position = $data['position'];

// Server-side validation for required fields.
if (empty($username) || empty($email)) {
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['success' => false, 'error' => 'Username and email are required.']);
    exit();
}
 
// Prepares the SQL statement to update the user's profile information.
$stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, company = ?, position = ? WHERE user_ID = ?");
$stmt->bind_param("ssssi", $username, $email, $company, $position, $userId);

// Executes the statement.
if ($stmt->execute()) {
    // If the username was changed, updates the session variable to reflect the new username immediately.
    if ($_SESSION['username'] !== $username) {
        $_SESSION['username'] = $username;
    }
    // Sends a success response.
    echo json_encode(['success' => true]);
} else {
    // If the update fails, returns a 500 Internal Server Error.
    header('HTTP/1.1 500 Internal Server Error');
    // Logs the detailed error for debugging purposes.
    $error_message = 'Database update failed: ' . $stmt->error;
    error_log($error_message);
    // Sends a generic error message to the client.
    echo json_encode(['success' => false, 'error' => 'Database update failed.']);
}

// Closes the statement and the database connection.
$stmt->close();
$conn->close();
?>