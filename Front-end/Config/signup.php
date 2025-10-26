<?php
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // reCAPTCHA verification
    // TODO: Replace with your secret key
    $recaptcha_secret = "6LeIxAcTAAAAAGG-vFI1TnRWxVegKFDEasIURxT"; // IMPORTANT: Add your Google reCAPTCHA secret key here
    $recaptcha_response = $_POST['g-recaptcha-response'];
    $response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$recaptcha_secret&response=$recaptcha_response");
    $response_keys = json_decode($response, true);

    if (intval($response_keys["success"]) !== 1) {
        echo "Please complete the reCAPTCHA.";
        exit();
    }

    // Validate for empty fields
    if (empty(trim($_POST['username'])) || empty(trim($_POST['email'])) || empty(trim($_POST['password'])) || empty(trim($_POST['company'])) || empty(trim($_POST['position']))) {
        echo "All fields are required. Please fill out the entire form.";
        exit();
    }

    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $company = $_POST['company'];
    $position = $_POST['position'];
    $role = 1; // Default user role
    
    // Check if username or email already exists
    $stmt_check = $conn->prepare("SELECT user_ID FROM users WHERE username = ? OR email = ?");
    $stmt_check->bind_param("ss", $username, $email);
    $stmt_check->execute();
    $stmt_check->store_result();
    
    if ($stmt_check->num_rows > 0) {
        // Using a redirect with a query parameter for better user feedback
        header("location: ../signup.php?error=exists");
        $stmt_check->close();
        exit();
    }
    $stmt_check->close();

    // If no user exists, proceed with insertion
    $stmt_insert = $conn->prepare("INSERT INTO users (username, email, password, company, position, Role_ID) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt_insert->bind_param("sssssi", $username, $email, $password, $company, $position, $role);

    if ($stmt_insert->execute()) {
        // Redirect to login page with a success message
        header("location: ../signup.php?success=1");
        exit();
    } else {
        // Generic error for the user, log the specific error for the developer
        error_log("Signup Error: " . $stmt_insert->error);
        header("location: ../signup.php?error=dberror");
    }

    $stmt_insert->close();
    $conn->close();
}
?>