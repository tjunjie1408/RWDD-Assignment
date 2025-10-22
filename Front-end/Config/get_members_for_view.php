<?php
include 'db_connect.php';

// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('HTTP/1.1 401 Unauthorized');
    echo json_encode(['error' => 'User not logged in.']);
    exit();
}

$response = ['success' => false, 'members' => []];

// For a regular user, we might want to filter members by their company or team.
// For now, let's fetch all users with Role_ID = 1.
$stmt = $conn->prepare("SELECT user_ID, username, company, position FROM users WHERE Role_ID = 1 ORDER BY username ASC");

if ($stmt->execute()) {
    $result = $stmt->get_result();
    $members = [];
    while ($row = $result->fetch_assoc()) {
        $members[] = $row;
    }
    $response['success'] = true;
    $response['members'] = $members;
} else {
    error_log("Get Members for View Error: " . $stmt->error);
    $response['error'] = 'Failed to fetch members.';
}
$stmt->close();
$conn->close();
header('Content-Type: application/json');
echo json_encode($response);
?>