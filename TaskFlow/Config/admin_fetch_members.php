<?php
// Includes the database connection script.
include 'db_connect.php';

// Security check: Ensures the user is logged in and has admin privileges (Role_ID = 2).
// If not, it returns a 403 Forbidden error.
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['role'] !== 2) {
    header('HTTP/1.1 403 Forbidden');
    echo json_encode(['error' => 'Access denied. Administrator privileges required.']);
    exit();
}

// Initializes the response array with default values.
$response = ['success' => false, 'members' => []];

// Prepares a SQL statement to select all users who are regular members (Role_ID = 1).
// The results are ordered by username.
$stmt = $conn->prepare("SELECT user_ID, username, email, company, position FROM users WHERE Role_ID = 1 ORDER BY username ASC");

// Executes the prepared statement.
if ($stmt->execute()) {
    // Gets the result set from the executed statement.
    $result = $stmt->get_result();
    $members = [];
    // Iterates through each row of the result set.
    while ($row = $result->fetch_assoc()) {
        // Dynamically generates a Gravatar URL for each member based on their email.
        // This avoids storing avatars directly in the database.
        $email = trim(strtolower($row['email']));
        $md5_email = md5($email);
        $row['avatar'] = "https://www.gravatar.com/avatar/{$md5_email}?d=mp"; // 'mp' provides a default placeholder image.
        $members[] = $row;
    }
    // Sets the success flag to true and populates the 'members' array in the response.
    $response['success'] = true;
    $response['members'] = $members;
} else {
    // If the query fails, logs the error and sets an error message in the response.
    error_log("Admin Fetch Members Error: " . $stmt->error);
    $response['error'] = 'Failed to fetch members.';
}

// Closes the statement and the database connection.
$stmt->close();
$conn->close();
// Sets the content type to JSON for the response.
header('Content-Type: application/json');
// Encodes the response array into JSON and sends it.
echo json_encode($response);
?>