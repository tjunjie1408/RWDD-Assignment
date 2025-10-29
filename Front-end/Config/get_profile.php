<?php
include 'db_connect.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('HTTP/1.1 401 Unauthorized');
    echo json_encode(['error' => 'User not logged in']);
    exit();
}

$userId = $_SESSION['id'];

$stmt = $conn->prepare("SELECT username, email, company, position FROM users WHERE user_ID = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($user = $result->fetch_assoc()) {
    // Always generate Gravatar URL
    $email = trim(strtolower($user['email']));
    $md5_email = md5($email);
    $user['avatar'] = "https://www.gravatar.com/avatar/{$md5_email}?d=mp";

    echo json_encode($user);
} else {
    header('HTTP/1.1 404 Not Found');
    echo json_encode(['error' => 'User not found']);
}

$stmt->close();
$conn->close();
?>