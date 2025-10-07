<?php
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

    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $company = $_POST['company'];
    $position = $_POST['position'];
    $role = 1; // Default user role

    $stmt = $conn->prepare("INSERT INTO users (username, email, password, company, position, role) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssi", $username, $email, $password, $company_name, $position, $role_ID);

    if ($stmt->execute()) {
        // Redirect to login page after successful registration
        header("location: ../signup.html");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>