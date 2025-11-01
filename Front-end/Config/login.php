<?php
// Includes the database connection script, which also starts the session.
include 'db_connect.php';

// Processes the login form submission.
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // --- reCAPTCHA Verification ---
    // This is a security measure to prevent bots from submitting the form.
    $recaptcha_secret = "6LcYvPgrAAAAAKyBYvylApS14TkVvSW9-wAHpjSr"; // IMPORTANT: This should be your actual Google reCAPTCHA secret key.
    $recaptcha_response = $_POST['g-recaptcha-response'];
    // Sends a request to the Google reCAPTCHA API to verify the user's response.
    $response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$recaptcha_secret&response=$recaptcha_response");
    $response_keys = json_decode($response, true);

    // If reCAPTCHA verification fails, redirects with an error.
    if (intval($response_keys["success"]) !== 1) {
        header("location: ../signup.php?error=recaptcha");
        exit();
    }

    // --- Form Validation ---
    // Checks if the login (username/email) or password fields are empty.
    if (empty(trim($_POST['login'])) || empty(trim($_POST['password']))) {
        header("location: ../signup.php?error=empty");
        exit();
    }

    // --- User Authentication ---
    $login = $_POST['login'];
    $password = $_POST['password'];

    // Prepares a SQL statement to find a user by their email or username.
    $stmt = $conn->prepare("SELECT user_ID, password, Role_ID, username FROM users WHERE email = ? OR username = ?");
    $stmt->bind_param("ss", $login, $login);
    $stmt->execute();
    $stmt->store_result();

    // Checks if a user was found.
    if ($stmt->num_rows > 0) {
        // Binds the result columns to variables.
        $stmt->bind_result($id, $hashed_password, $role_id, $username);
        $stmt->fetch();

        // Verifies the submitted password against the hashed password stored in the database.
        if (password_verify($password, $hashed_password)) {
            // --- Session Creation ---
            // If the password is correct, sets session variables to mark the user as logged in.
            $_SESSION['loggedin'] = true;
            $_SESSION['id'] = $id;
            $_SESSION['username'] = $username;
            $_SESSION['role'] = $role_id;

            // --- Role-Based Redirection ---
            // Redirects the user to the appropriate dashboard based on their role.
            if ($role_id == 1) { // Regular user
                header("location: ../dashboard.php");
            } else { // Admin user
                header("location: ../admin_dashboard.php");
            }
            $stmt->close();
            $conn->close();
            exit();
        } else {
            // If the password is incorrect, redirects with an invalid password error.
            $_SESSION['flash_message'] = "Invalid username or password.";
            header("location: ../signup.php");
        }
    } else {
        // If no user is found with the given username/email, redirects with a 'no user' error.
        $_SESSION['flash_message'] = "Invalid username or password.";
        header("location: ../signup.php");
    }
    $stmt->close();
    $conn->close();
    exit();
}
?>