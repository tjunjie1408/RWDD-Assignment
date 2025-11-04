<?php
// Initializes the session to access session data.
session_start();

// Unsets all session variables, effectively logging the user out from the application's perspective.
$_SESSION = array();

// Destroys the session cookie on the client side.
// This is a robust way to ensure the session is fully terminated.
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, // Sets the cookie's expiration time to the past.
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destroys all data registered to a session on the server side.
session_destroy();

// Redirects the user back to the main signup/login page.
header("location: ../signup.php");
exit;
?>