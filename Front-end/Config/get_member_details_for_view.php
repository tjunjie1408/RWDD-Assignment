<?php
include 'db_connect.php';

// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('HTTP/1.1 401 Unauthorized');
    echo json_encode(['error' => 'User not logged in.']);
    exit();
}

$response = ['success' => false, 'member' => null];

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $member_id = filter_var($_GET['id'], FILTER_VALIDATE_INT);

    if ($member_id === false) {
        header('HTTP/1.1 400 Bad Request');
        $response['error'] = 'Invalid member ID.';
    } else {
        // Fetch details for a regular user (Role_ID = 1)
        $stmt = $conn->prepare("SELECT user_ID, username, email, company, position FROM users WHERE user_ID = ? AND Role_ID = 1");
        $stmt->bind_param("i", $member_id);

        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $response['success'] = true;
            $response['member'] = $result->fetch_assoc();
        } else {
            error_log("Get Member Details for View Error: " . $stmt->error);
            $response['error'] = 'Failed to fetch member details.';
        }
        $stmt->close();
    }
}
$conn->close();
header('Content-Type: application/json');
echo json_encode($response);
?>