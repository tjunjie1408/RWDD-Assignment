<?php
// Includes the database connection script.
include 'db_connect.php';

// Security check: Ensures the user is logged in and has admin privileges.
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['role'] !== 2) {
    header('HTTP/1.1 403 Forbidden');
    echo json_encode(['success' => false, 'error' => 'Access denied. Administrator privileges required.']);
    exit();
}

// Initializes the response array.
$response = ['success' => false, 'error' => ''];

// Processes the request only if it's a POST method.
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieves and validates input from the POST request.
    $member_id = filter_var($_POST['memberId'] ?? '', FILTER_VALIDATE_INT);
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? ''; // Password is optional.
    $company = trim($_POST['company'] ?? '');
    $position = trim($_POST['position'] ?? '');

    // Validates that required fields are not empty and the member ID is valid.
    if ($member_id === false || empty($username) || empty($email) || empty($company) || empty($position)) {
        header('HTTP/1.1 400 Bad Request');
        $response['error'] = "Invalid input or missing required fields.";
        echo json_encode($response);
        exit();
    }

    // Validates the email format.
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header('HTTP/1.1 400 Bad Request');
        $response['error'] = "Invalid email format.";
        echo json_encode($response);
        exit();
    }

    // Security check: Verifies that the member to be updated exists and is a regular user (Role_ID = 1).
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

    // Dynamically builds the SQL UPDATE statement.
    $sql = "UPDATE users SET username = ?, email = ?, company = ?, position = ?";
    $params = "ssss";
    $values = [$username, $email, $company, $position];

    // If a new password is provided, it's hashed and added to the SQL query.
    if (!empty($password)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $sql .= ", password = ?";
        $params .= "s";
        $values[] = $hashed_password;
    }
    $sql .= " WHERE user_ID = ?";
    $params .= "i";
    $values[] = $member_id;

    // Prepares and executes the final SQL statement.
    $stmt = $conn->prepare($sql);
    if ($stmt->bind_param($params, ...$values) && $stmt->execute()) {
        $response['success'] = true;
    } else {
        // If the update fails, logs the error and returns a 500 Internal Server Error.
        error_log("Admin Update Member Error: " . $stmt->error);
        header('HTTP/1.1 500 Internal Server Error');
        $response['error'] = 'Failed to update member.';
    }
    $stmt->close();
} else {
    // If the request method is not POST, returns a 405 Method Not Allowed error.
    header('HTTP/1.1 405 Method Not Allowed');
    $response['error'] = 'Invalid request method.';
}

// Closes the database connection.
$conn->close();
// Sets the content type to JSON.
header('Content-Type: application/json');
// Sends the JSON response.
echo json_encode($response);
?>