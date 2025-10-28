<?php
// Includes the database connection script.
include 'db_connect.php';

// Security check: Ensures the user is logged in before fetching their profile.
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('HTTP/1.1 401 Unauthorized');
    echo json_encode(['error' => 'User not logged in']);
    exit();
}

// Gets the user ID from the current session.
$userId = $_SESSION['id'];

// Prepares a SQL statement to fetch the profile data for the logged-in user.
$stmt = $conn->prepare("SELECT username, email, company, position FROM users WHERE user_ID = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

// Fetches the user data.
if ($user = $result->fetch_assoc()) {
    // Generates a Gravatar URL for the user's avatar based on their email.
    $email = trim(strtolower($user['email']));
    $md5_email = md5($email);
    $user['avatar'] = "https://www.gravatar.com/avatar/{$md5_email}?d=mp";

    // Encodes the user data (including the avatar URL) into JSON and sends it as the response.
    echo json_encode($user);
} else {
    // If no user is found for the ID in the session, returns a 404 Not Found error.
    header('HTTP/1.1 404 Not Found');
    echo json_encode(['error' => 'User not found']);
}

// Closes the statement and the database connection.
$stmt->close();
$conn->close();
?>