<?php
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // reCAPTCHA verification
    // TODO: Replace with your secret key
    $recaptcha_secret = "6LcYvPgrAAAAAKyBYvylApS14TkVvSW9-wAHpjSr"; // IMPORTANT: Add your Google reCAPTCHA secret key here
    $recaptcha_response = $_POST['g-recaptcha-response'];
    $response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$recaptcha_secret&response=$recaptcha_response");
    $response_keys = json_decode($response, true);

    if (intval($response_keys["success"]) !== 1) {
        header("location: ../signup.php?error=recaptcha");
        exit();
    }

    // Validate for empty fields
    if (empty(trim($_POST['login'])) || empty(trim($_POST['password']))) {
        header("location: ../signup.php?error=empty");
        exit();
    }

    $login = $_POST['login'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT user_ID, password, Role_ID, username FROM users WHERE email = ? OR username = ?");
    $stmt->bind_param("ss", $login, $login);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $hashed_password, $role_id, $username);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            $_SESSION['loggedin'] = true;
            $_SESSION['id'] = $id;
            $_SESSION['username'] = $username;
            $_SESSION['role'] = $role_id;

            // Redirect user based on role
            if ($role_id == 1) {
                $stmt->close();
                $conn->close();
                header("location: ../dashboard.php"); // Redirect to user dashboard
            } else {
                $stmt->close();
                $conn->close();
                header("location: ../admin_dashboard.php"); // Redirect to admin dashboard
            }
            exit();
        } else {
            $stmt->close();
            $conn->close();
            header("location: ../signup.php?error=invalidpwd");
            exit();
        }
    } else {
        $stmt->close();
        $conn->close();
        header("location: ../signup.php?error=nouser");
        exit();
    }
}
?>