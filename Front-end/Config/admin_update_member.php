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
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? ''; // Optional, only update if provided
    $company = trim($_POST['company'] ?? '');
    $position = trim($_POST['position'] ?? '');

    if ($member_id === false || empty($username) || empty($email) || empty($company) || empty($position)) {
        header('HTTP/1.1 400 Bad Request');
        $response['error'] = "Invalid input or missing required fields.";
        echo json_encode($response);
        exit();
    }

    // Basic email validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header('HTTP/1.1 400 Bad Request');
        $response['error'] = "Invalid email format.";
        echo json_encode($response);
        exit();
    }

    // Check if the member exists and is a regular user (Role_ID = 1)
    $stmt_check_role = $conn->prepare("SELECT user_ID FROM users WHERE user_ID = ? AND Role_ID = 1");
    $stmt_check_role->bind_param("i", $member_id);
    $stmt_check_role->execute();
    $stmt_check_role->store_result();
    if ($stmt_check_role->num_rows === 0) {
        header('HTTP/1.1 404 Not Found');
        $response['error'] = "Member not found or cannot be updated by this role.";
        $stmt_check_role->close();
        echo json_encode($response);
        exit();
    }
    $stmt_check_role->close();

    $sql = "UPDATE users SET username = ?, email = ?, company = ?, position = ?";
    $params = "ssss";
    $values = [$username, $email, $company, $position];

    if (!empty($password)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $sql .= ", password = ?";
        $params .= "s";
        $values[] = $hashed_password;
    }
    $sql .= " WHERE user_ID = ?";
    $params .= "i";
    $values[] = $member_id;

    $stmt = $conn->prepare($sql);
    if ($stmt->bind_param($params, ...$values) && $stmt->execute()) {
        $response['success'] = true;
    } else {
        error_log("Admin Update Member Error: " . $stmt->error);
        header('HTTP/1.1 500 Internal Server Error');
        $response['error'] = 'Failed to update member.';
    }
    $stmt->close();
} else {
    header('HTTP/1.1 405 Method Not Allowed');
    $response['error'] = 'Invalid request method.';
}

$conn->close();
header('Content-Type: application/json');
echo json_encode($response);
?>