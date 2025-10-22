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
    // Validate for empty fields
    if (empty(trim($_POST['username'])) || empty(trim($_POST['email'])) || empty(trim($_POST['password'])) || empty(trim($_POST['company'])) || empty(trim($_POST['position']))) {
        header('HTTP/1.1 400 Bad Request');
        $response['error'] = "All fields are required.";
        echo json_encode($response);
        exit();
    }

    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $company = trim($_POST['company']);
    $position = trim($_POST['position']);
    $role = 1; // Default user role for members added by admin
    
    // Basic email validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header('HTTP/1.1 400 Bad Request');
        $response['error'] = "Invalid email format.";
        echo json_encode($response);
        exit();
    }

    // Check if username or email already exists
    $stmt_check = $conn->prepare("SELECT user_ID FROM users WHERE username = ? OR email = ?");
    $stmt_check->bind_param("ss", $username, $email);
    $stmt_check->execute();
    $stmt_check->store_result();
    
    if ($stmt_check->num_rows > 0) {
        header('HTTP/1.1 409 Conflict'); // 409 Conflict for duplicate resource
        $response['error'] = "Username or email already exists.";
        $stmt_check->close();
        echo json_encode($response);
        exit();
    }
    $stmt_check->close();

    // If no user exists, proceed with insertion
    $stmt_insert = $conn->prepare("INSERT INTO users (username, email, password, company, position, Role_ID) VALUES (?, ?, ?, ?, ?, ?)");
    if ($stmt_insert->bind_param("sssssi", $username, $email, $password, $company, $position, $role) && $stmt_insert->execute()) {
        $response['success'] = true;
    } else {
        error_log("Admin Add Member Error: " . $stmt_insert->error);
        header('HTTP/1.1 500 Internal Server Error');
        $response['error'] = 'Failed to add member.';
    }
    $stmt_insert->close();
} else {
    header('HTTP/1.1 405 Method Not Allowed');
    $response['error'] = 'Invalid request method.';
}

$conn->close();
header('Content-Type: application/json');
echo json_encode($response);
?>