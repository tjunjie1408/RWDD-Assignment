<?php
// Includes the database connection script.
include 'db_connect.php';

// Security check: Ensures the user is logged in.
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('HTTP/1.1 401 Unauthorized');
    echo json_encode(['error' => 'User not logged in.']);
    exit();
}

// Initializes the response array.
$response = ['success' => false, 'members' => []];

// This script is for a "view-only" page where a regular user can see other members.
// It fetches all users with Role_ID = 1 (regular users).
// In a more complex application, this might be filtered to show only team or company members.
$stmt = $conn->prepare("SELECT user_ID, username, company, position FROM users WHERE Role_ID = 1 ORDER BY username ASC");

// Executes the statement.
if ($stmt->execute()) {
    $result = $stmt->get_result();
    $members = [];
    // Iterates through the results and builds the members array.
    while ($row = $result->fetch_assoc()) {
        $members[] = $row;
    }
    $response['success'] = true;
    $response['members'] = $members;
} else {
    // If the query fails, logs the error and sets an error message.
    error_log("Get Members for View Error: " . $stmt->error);
    $response['error'] = 'Failed to fetch members.';
}
// Closes the statement and the database connection.
$stmt->close();
$conn->close();
// Sets the content type to JSON.
header('Content-Type: application/json');
// Sends the JSON response.
echo json_encode($response);
?>