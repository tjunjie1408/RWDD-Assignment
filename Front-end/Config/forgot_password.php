<?php
include 'db_connect.php';

// This script handles both checking the email and updating the password.
// WARNING: This is an insecure password reset flow. It allows anyone who knows a user's email to change their password.

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Action 2: Update the password
    if (isset($_POST['email']) && isset($_POST['password'])) {
        $email = $_POST['email'];
        $password = $_POST['password'];
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
        $stmt->bind_param("ss", $hashed_password, $email);

        if ($stmt->execute()) {
            // Password updated, redirect to login page with a success message.
            header("location: ../signup.php?reset=success");
            exit();
        } else {
            // Database error
            header("location: ../forgot_password.php?email=" . urlencode($email) . "&error=updatefailed");
            exit();
        }

    // Action 1: Check if the email exists
    } elseif (isset($_POST['check_email'])) {
        $email = $_POST['check_email'];
        header("location: ../forgot_password.php?email=" . urlencode($email));
        exit();
    } else {
        header("location: ../forgot_password.php?error=invalid");
        exit();
    }
}
?>