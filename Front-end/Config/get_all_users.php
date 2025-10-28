<?php
header('Content-Type: application/json');
include 'db_connect.php';

// Security check: Ensure the user is logged in to access this data.
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    http_response_code(401);
    exit;
}

$response = ['success' => false, 'members' => [], 'error' => ''];

try {
    // Fetch all users and generate their Gravatar URL.
    $stmt = $conn->prepare("SELECT user_ID, username, email, company, position FROM users");
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        // Generate Gravatar URL from email
        $email = trim(strtolower($row['email']));
        $md5_email = md5($email);
        $row['avatar_url'] = "https://www.gravatar.com/avatar/{$md5_email}?d=mp";

        $response['members'][] = $row;
    }

    $response['success'] = true;
    $stmt->close();
} catch (Exception $e) {
    $response['error'] = 'Database error: ' . $e->getMessage();
    http_response_code(500);
}

$conn->close();
echo json_encode($response);
?>