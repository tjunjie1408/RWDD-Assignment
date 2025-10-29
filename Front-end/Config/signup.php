<?php
// Includes the database connection script.
include 'db_connect.php';

// Processes the registration form submission.
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // --- reCAPTCHA Verification ---
    $recaptcha_secret = "6LcYvPgrAAAAAKyBYvylApS14TkVvSW9-wAHpjSr"; // IMPORTANT: This should be your actual Google reCAPTCHA secret key.
    $recaptcha_response = $_POST['g-recaptcha-response'];
    $response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$recaptcha_secret&response=$recaptcha_response");
    $response_keys = json_decode($response, true);

    // If reCAPTCHA verification fails, stops execution and informs the user.
    if (intval($response_keys["success"]) !== 1) {
        echo "Please complete the reCAPTCHA.";
        exit();
    }

    // --- Form Validation ---
    // Checks for empty required fields.
    if (empty(trim($_POST['username'])) || empty(trim($_POST['email'])) || empty(trim($_POST['password'])) || empty(trim($_POST['company'])) || empty(trim($_POST['position']))) {
        echo "All fields are required. Please fill out the entire form.";
        exit();
    }

    // --- User Creation ---
    $username = $_POST['username'];
    $email = $_POST['email'];
    // Hashes the password for secure storage using PHP's default strong hashing algorithm.
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $company = $_POST['company'];
    $position = $_POST['position'];
    $role = 1; // Sets the default role for a new user to '1' (regular user).
    
    // --- Duplicate Check ---
    // Prepares a statement to check if the username or email already exists to prevent duplicates.
    $stmt_check = $conn->prepare("SELECT user_ID FROM users WHERE username = ? OR email = ?");
    $stmt_check->bind_param("ss", $username, $email);
    $stmt_check->execute();
    $stmt_check->store_result();
    
    // If a user with the same username or email is found, redirects with an error.
    if ($stmt_check->num_rows > 0) {
        header("location: ../signup.php?error=exists");
        $stmt_check->close();
        exit();
    }
    $stmt_check->close();

    // --- Database Insertion ---
    // If the user is unique, prepares and executes a statement to insert the new user into the database.
    $stmt_insert = $conn->prepare("INSERT INTO users (username, email, password, company, position, Role_ID) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt_insert->bind_param("sssssi", $username, $email, $password, $company, $position, $role);

    if ($stmt_insert->execute()) {
        // On success, redirects to the signup page with a success indicator.
        header("location: ../signup.php?success=1");
    } else {
        // If insertion fails, logs the specific error for developers and redirects with a generic error for the user.
        error_log("Signup Error: " . $stmt_insert->error);
        header("location: ../signup.php?error=dberror");
    }

    $stmt_insert->close();
    $conn->close();
    exit();
}
?>