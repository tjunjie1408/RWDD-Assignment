<?php
include 'db_connect.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('HTTP/1.1 401 Unauthorized');
    echo json_encode(['error' => 'User not logged in']);
    exit();
}

$userId = $_SESSION['id'];
$data = json_decode(file_get_contents("php://input"), true);

$username = $data['username'];
$email = $data['email'];
$company = $data['company'];
$position = $data['position'];

// Basic validation
if (empty($username) || empty($email)) {
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['success' => false, 'error' => 'Username and email are required.']);
    exit();
}
 
// Prepare the statement to update user details, excluding the profile picture.
$stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, company = ?, position = ? WHERE user_ID = ?");
$stmt->bind_param("ssssi", $username, $email, $company, $position, $userId);

if ($stmt->execute()) {
    // If username was updated, update the session variable
    if ($_SESSION['username'] !== $username) {
        $_SESSION['username'] = $username;
    }
    echo json_encode(['success' => true]);
} else {
    header('HTTP/1.1 500 Internal Server Error');
    // Provide a more specific error for debugging if possible, but not in production
    $error_message = 'Database update failed: ' . $stmt->error;
    error_log($error_message); // Log the detailed error for the developer
    echo json_encode(['success' => false, 'error' => 'Database update failed.']);
}

$stmt->close();
$conn->close();
?>