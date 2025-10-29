<?php
include 'PHP/db_connect.php';

$token = $_GET['token'] ?? '';
$error_message = '';
$success_message = '';
$token_is_valid = false;

if (empty($token)) {
    $error_message = "Invalid or missing reset token.";
} else {
    // Check if token is valid and not expired
    $stmt = $conn->prepare("SELECT email, expires FROM password_resets WHERE token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        if (time() > $row['expires']) {
            $error_message = "This password reset link has expired. Please request a new one.";
        } else {
            $token_is_valid = true;
            $email = $row['email'];
        }
    } else {
        $error_message = "Invalid password reset link.";
    }
    $stmt->close();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && $token_is_valid) {
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];

    if (empty($password) || empty($password_confirm)) {
        $error_message = "Please enter and confirm your new password.";
    } elseif ($password !== $password_confirm) {
        $error_message = "Passwords do not match. Please try again.";
    } else {
        // Passwords match, update the user's password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $stmt_update = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
        $stmt_update->bind_param("ss", $hashed_password, $email);
        
        if ($stmt_update->execute()) {
            // Password updated successfully. Invalidate the token.
            $stmt_delete = $conn->prepare("DELETE FROM password_resets WHERE email = ?");
            $stmt_delete->bind_param("s", $email);
            $stmt_delete->execute();
            $stmt_delete->close();

            $success_message = "Your password has been successfully updated! You will be redirected to the login page shortly.";
            // Redirect to login page after a few seconds
            header("refresh:5;url=signup.php");
            $token_is_valid = false; // Hide the form after successful update
        } else {
            $error_message = "An error occurred. Please try again.";
        }
        $stmt_update->close();
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="stylesheet" href="CSS/signup.css">
</head>
<body>
    <div class="wrapper">
        <div class="form-box">
            <div class="logo-box">
                <img src="Pictures/logo.png" alt="TaskFlow">
            </div>
            
            <?php if ($error_message): ?>
                <div class="top" style="text-align: center; color: red; margin-bottom: 15px;"><?php echo $error_message; ?></div>
            <?php endif; ?>

            <?php if ($success_message): ?>
                <div class="top" style="text-align: center; color: green; margin-bottom: 15px;"><?php echo $success_message; ?></div>
            <?php endif; ?>

            <?php if ($token_is_valid): ?>
            <form class="login-container" id="resetPassword" action="reset_password.php?token=<?php echo htmlspecialchars($token); ?>" method="POST">
                <div class="top">
                    <header>Reset Your Password</header>
                </div>
                <div class="input-box">
                    <input type="password" name="password" class="input-field" placeholder="Enter new password" required>
                    <i class="bx bx-lock-alt"></i>
                </div>
                <div class="input-box">
                    <input type="password" name="password_confirm" class="input-field" placeholder="Confirm new password" required>
                    <i class="bx bx-lock-alt"></i>
                </div>
                <div class="input-box" style="margin-top: 20px;">
                    <input type="submit" class="submit" value="Update Password">
                </div>
            </form>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>