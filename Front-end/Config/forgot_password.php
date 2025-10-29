<?php
// Includes the database connection script.
include 'db_connect.php';

// This script serves a dual purpose based on the POST data received.
// It can either check if an email exists or update a password for a given email.

// Processes the request only if the method is POST.
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Scenario 1: Update the password.
    // This block executes if both 'email' and 'password' are provided.
    if (isset($_POST['email']) && isset($_POST['password'])) {
        $email = $_POST['email'];
        $password = $_POST['password'];
        // Hashes the new password for secure storage.
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Prepares and executes the SQL statement to update the user's password.
        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
        $stmt->bind_param("ss", $hashed_password, $email);

        if ($stmt->execute()) {
            // On success, redirects to the main signup/login page with a success indicator.
            header("location: ../signup.php?reset=success");
            exit();
        } else {
            // If the update fails, redirects back to the forgot password page with an error.
            header("location: ../forgot_password.php?email=" . urlencode($email) . "&error=updatefailed");
            exit();
        }
    // Scenario 2: Check if the email exists.
    // This block executes if only 'check_email' is provided.
    } elseif (isset($_POST['check_email'])) {
        $email = $_POST['check_email'];
        // Redirects back to the forgot password page, passing the email in the URL.
        // The frontend page will then use this email to show the password reset form.
        header("location: ../forgot_password.php?email=" . urlencode($email));
        exit();
    } else {
        // If the POST data is invalid, redirects with a generic error.
        header("location: ../forgot_password.php?error=invalid");
        exit();
    }
}
?>