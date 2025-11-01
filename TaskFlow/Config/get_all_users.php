<?php
// Sets the content type to JSON.
header('Content-Type: application/json');
// Includes the database connection script.
include 'db_connect.php';

// Security check: Ensures the user is logged in before they can access user data.
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    http_response_code(401);
    exit;
}

// Initializes the response array.
$response = ['success' => false, 'members' => [], 'error' => ''];

try {
    // Prepares a SQL statement to fetch details for all users.
    $stmt = $conn->prepare("SELECT user_ID, username, email, company, position FROM users");
    $stmt->execute();
    $result = $stmt->get_result();

    // Iterates through each user record.
    while ($row = $result->fetch_assoc()) {
        // Generates a Gravatar URL for each user based on their email address.
        // This is a common practice to display user avatars without storing image files.
        $email = trim(strtolower($row['email']));
        $md5_email = md5($email);
        $row['avatar_url'] = "https://www.gravatar.com/avatar/{$md5_email}?d=mp"; // 'mp' provides a default 'mystery person' image.

        // Adds the user data (including the new avatar URL) to the response array.
        $response['members'][] = $row;
    }

    $response['success'] = true;
    $stmt->close();
} catch (Exception $e) {
    // If any database error occurs, it's caught here.
    $response['error'] = 'Database error: ' . $e->getMessage();
    http_response_code(500);
}

// Closes the database connection.
$conn->close();
// Sends the final JSON response.
echo json_encode($response);
?>