<?php
    // Includes the database connection and session start.
    include 'Config/db_connect.php';

    // --- Page State Initialization ---
    $email_to_reset = '';
    $email_exists = false; // Flag to determine whether to show the email entry form or the password reset form.
    $error_message = '';

    // --- Email Verification ---
    // This block runs when the user first submits their email address.
    // The email is passed back to this same page as a GET parameter.
    if (isset($_GET['email'])) {
        $email_to_reset = $_GET['email'];
        
        // Verifies that the provided email exists and does not belong to an admin.
        $stmt = $conn->prepare("SELECT user_ID, Role_ID FROM users WHERE email = ?");
        $stmt->bind_param("s", $email_to_reset);
        $stmt->execute();
        $stmt->store_result();
        
        if ($stmt->num_rows > 0) {
            $stmt->bind_result($user_id, $role_id);
            $stmt->fetch();
            // If the email belongs to an admin, show an error.
            if ($role_id == 2) {
                $error_message = "Invalid process.";
            } else {
                $email_exists = true; // If it's a regular user, show the password reset form.
            }
        } else {
            $error_message = "Email not found. Please try again.";
        }
        $stmt->close();
    }

    // --- Error Handling ---
    // Catches other error messages passed in the URL from the processing script.
    if (isset($_GET['error'])) {
        if ($_GET['error'] == 'updatefailed') {
            $error_message = "Failed to update password. Please try again.";
        }
    }

    $conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link rel="icon" href="Pictures/icon.png">
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

            <?php if ($email_exists): ?>
                <!-- Form 2: Reset Password -->
                <form class="login-container" action="Config/forgot_password.php" method="POST">
                    <div class="top">
                        <header>Reset Your Password</header>
                    </div>
                    <input type="hidden" name="email" value="<?php echo htmlspecialchars($email_to_reset); ?>">
                    <div class="input-box">
                        <input type="password" name="password" class="input-field" placeholder="Enter new password" required>
                        <i class="bx bx-lock-alt"></i>
                    </div>
                    <div class="input-box" style="margin-top: 20px;">
                        <input type="submit" class="submit" value="Update Password">
                    </div>
                </form>
            <?php else: ?>
                <!-- Form 1: Enter Email -->
                <form class="login-container" action="Config/forgot_password.php" method="POST">
                    <div class="top">
                    <header>Forgot Password</header>
                    </div>
                    <div class="input-box">
                        <input type="email" name="check_email" class="input-field" placeholder="Enter your email address" required>
                        <i class="bx bx-envelope"></i>
                    </div>
                    <div class="input-box" style="margin-top: 20px;">
                        <input type="submit" class="submit" value="Find Account">
                    </div>
                    <div class="two-col" style="margin-top: 20px;">
                        <div class="two">
                            <span>Remembered your password? <a href="signup.php">Login</a></span>
                        </div>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>