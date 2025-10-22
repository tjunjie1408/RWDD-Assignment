<?php
include 'db_connect.php';

// Start session if not already started (db_connect.php should handle this)
// session_start(); 

// Check if the user is logged in and is an admin (Role_ID = 2 for admin, 1 for regular user)
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['role'] !== 2) {
    header('HTTP/1.1 403 Forbidden');
    echo json_encode(['error' => 'Access denied. Administrator privileges required.']);
    exit();
}

$response = ['success' => false, 'members' => []];

$stmt = $conn->prepare("SELECT user_ID, username, email, company, position FROM users WHERE Role_ID = 1 ORDER BY username ASC");

if ($stmt->execute()) {
    $result = $stmt->get_result();
    $members = [];
    while ($row = $result->fetch_assoc()) {
        $members[] = $row;
    }
    $response['success'] = true;
    $response['members'] = $members;
} else {
    error_log("Admin Fetch Members Error: " . $stmt->error);
    $response['error'] = 'Failed to fetch members.';
}

$stmt->close();
$conn->close();
header('Content-Type: application/json');
echo json_encode($response);
?>