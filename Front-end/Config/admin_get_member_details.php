<?php
include 'db_connect.php';

// Check if the user is logged in and is an admin
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['role'] !== 2) {
    header('HTTP/1.1 403 Forbidden');
    echo json_encode(['error' => 'Access denied. Administrator privileges required.']);
    exit();
}

$response = ['success' => false, 'member' => null];

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $member_id = filter_var($_GET['id'], FILTER_VALIDATE_INT);

    if ($member_id === false) {
        header('HTTP/1.1 400 Bad Request');
        $response['error'] = 'Invalid member ID.';
    } else {
        $stmt = $conn->prepare("SELECT user_ID, username, email, company, position FROM users WHERE user_ID = ? AND Role_ID = 1");
        $stmt->bind_param("i", $member_id);

        if ($stmt->execute()) {
            $result = $stmt->get_result();
            if ($member = $result->fetch_assoc()) {
                $response['success'] = true;
                $response['member'] = $member;
            } else {
                $response['error'] = 'Member not found or not a regular user.';
            }
        } else {
            error_log("Admin Get Member Details Error: " . $stmt->error);
            $response['error'] = 'Failed to fetch member details.';
        }
        $stmt->close();
    }
} else {
    header('HTTP/1.1 400 Bad Request');
    $response['error'] = 'Member ID is required.';
}

$conn->close();
header('Content-Type: application/json');
echo json_encode($response);
?>