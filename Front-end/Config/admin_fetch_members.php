<?php
include 'db_connect.php';

// Check if the user is logged in and is an admin
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['role'] !== 2) {
    header('HTTP/1.1 403 Forbidden');
    echo json_encode(['error' => 'Access denied. Administrator privileges required.']);
    exit();
}

$response = ['success' => false, 'members' => []];

// Select member data without the 'avatar' column
$stmt = $conn->prepare("SELECT user_ID, username, email, company, position FROM users WHERE Role_ID = 1 ORDER BY username ASC");

if ($stmt->execute()) {
    $result = $stmt->get_result();
    $members = [];
    while ($row = $result->fetch_assoc()) {
        // Generate Gravatar URL for each member
        $email = trim(strtolower($row['email']));
        $md5_email = md5($email);
        $row['avatar'] = "https://www.gravatar.com/avatar/{$md5_email}?d=mp";
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