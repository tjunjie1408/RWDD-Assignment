<?php
session_start(); // Start the session
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // reCAPTCHA verification
    //* Ensure to replace with your actual secret key
    $recaptcha_secret = "6Lc_Xt8rAAAAAOgm_SgGzc5_w4LvWoU7qPsi6R9a";
    $recaptcha_response = $_POST['g-recaptcha-response'];
    $response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$recaptcha_secret&response=$recaptcha_response");
    $response_keys = json_decode($response, true);

    if (intval($response_keys["success"]) !== 1) {
        echo "Please complete the reCAPTCHA.";
        exit();
    }

    $login = $_POST['login'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, password, role, username FROM users WHERE email = ? OR username = ?");
    $stmt->bind_param("ss", $login, $login);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $hashed_password, $role, $username);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            $_SESSION['loggedin'] = true;
            $_SESSION['id'] = $id;
            $_SESSION['username'] = $username;
            $_SESSION['role'] = $role;

            // Redirect user based on role
            if ($role == 1) {
                header("location: ../dashboard.html"); // Redirect to user dashboard
            } else {
                header("location: ../admin_dashboard.html"); // Redirect to admin dashboard
            }
            exit();
        } else {
            echo "Invalid password.";
        }
    } else {
        echo "No account found with that email or username.";
    }

    $stmt->close();
    $conn->close();
}
?>