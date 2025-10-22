<?php
include 'db_connect.php';

// Check if the user is logged in and is an admin
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['role'] !== 2) {
    header('HTTP/1.1 403 Forbidden');
    echo json_encode(['success' => false, 'error' => 'Access denied. Administrator privileges required.']);
    exit();
}

$response = ['success' => false, 'error' => ''];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $member_id = filter_var($_POST['memberId'] ?? '', FILTER_VALIDATE_INT);

    if ($member_id === false) {
        header('HTTP/1.1 400 Bad Request');
        $response['error'] = "Invalid member ID.";
        echo json_encode($response);
        exit();
    }

    // Ensure the admin is not trying to delete themselves or another admin, and only deletes Role_ID = 1 users
    if ($member_id == $_SESSION['id']) {
        header('HTTP/1.1 403 Forbidden');
        $response['error'] = "You cannot delete your own account.";
        echo json_encode($response);
        exit();
    }

    $stmt = $conn->prepare("DELETE FROM users WHERE user_ID = ? AND Role_ID = 1");
    $stmt->bind_param("i", $member_id);

    if ($stmt->execute()) {
        $response['success'] = true;
    } else {
        error_log("Admin Delete Member Error: " . $stmt->error);
        header('HTTP/1.1 500 Internal Server Error');
        $response['error'] = 'Failed to delete member.';
    }
    $stmt->close();
}
$conn->close();
header('Content-Type: application/json');
echo json_encode($response);
?>