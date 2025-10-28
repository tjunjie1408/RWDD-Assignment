<?php
// Includes the database connection script.
include 'db_connect.php';

// Security check: Ensures that the user is logged in and has administrator privileges (Role_ID = 2).
// If not, it denies access with a 403 Forbidden status.
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['role'] !== 2) {
    header('HTTP/1.1 403 Forbidden');
    echo json_encode(['success' => false, 'error' => 'Access denied. Administrator privileges required.']);
    exit();
}

// Initializes the response array to be sent back as JSON.
$response = ['success' => false, 'error' => ''];

// Checks if the request method is POST.
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Server-side validation: Checks if any of the required fields are empty.
    // If so, it returns a 400 Bad Request error.
    if (empty(trim($_POST['username'])) || empty(trim($_POST['email'])) || empty(trim($_POST['password'])) || empty(trim($_POST['company'])) || empty(trim($_POST['position']))) {
        header('HTTP/1.1 400 Bad Request');
        $response['error'] = "All fields are required.";
        echo json_encode($response);
        exit();
    }

    // Retrieves and sanitizes user input from the POST request.
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    // Hashes the password for secure storage.
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $company = trim($_POST['company']);
    $position = trim($_POST['position']);
    // Sets the default role for a new member to '1' (regular user).
    $role = 1; 
    
    // Validates the email format.
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header('HTTP/1.1 400 Bad Request');
        $response['error'] = "Invalid email format.";
        echo json_encode($response);
        exit();
    }

    // Prepares a statement to check if the username or email already exists in the database to prevent duplicates.
    $stmt_check = $conn->prepare("SELECT user_ID FROM users WHERE username = ? OR email = ?");
    $stmt_check->bind_param("ss", $username, $email);
    $stmt_check->execute();
    $stmt_check->store_result();
    
    // If a user with the same username or email is found, return a 409 Conflict error.
    if ($stmt_check->num_rows > 0) {
        header('HTTP/1.1 409 Conflict'); // 409 Conflict for duplicate resource
        $response['error'] = "Username or email already exists.";
        $stmt_check->close();
        echo json_encode($response);
        exit();
    }
    $stmt_check->close();

    // If the username and email are unique, proceed with inserting the new member into the 'users' table.
    $stmt_insert = $conn->prepare("INSERT INTO users (username, email, password, company, position, Role_ID) VALUES (?, ?, ?, ?, ?, ?)");
    // Binds the parameters to the SQL query and executes it.
    if ($stmt_insert->bind_param("sssssi", $username, $email, $password, $company, $position, $role) && $stmt_insert->execute()) {
        // If insertion is successful, set success to true.
        $response['success'] = true;
    } else {
        // If insertion fails, log the error and return a 500 Internal Server Error.
        error_log("Admin Add Member Error: " . $stmt_insert->error);
        header('HTTP/1.1 500 Internal Server Error');
        $response['error'] = 'Failed to add member.';
    }
    $stmt_insert->close();
} else {
    // If the request method is not POST, return a 405 Method Not Allowed error.
    header('HTTP/1.1 405 Method Not Allowed');
    $response['error'] = 'Invalid request method.';
}

// Closes the database connection.
$conn->close();
// Sets the content type of the response to JSON.
header('Content-Type: application/json');
// Encodes the response array into a JSON string and sends it.
echo json_encode($response);
?>