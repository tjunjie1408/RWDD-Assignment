<?php
// Includes the database connection script.
include 'db_connect.php';

// Security check: Ensures the user is logged in and has admin privileges (Role_ID = 2).
// If not, it returns a 403 Forbidden error.
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['role'] !== 2) {
    header('HTTP/1.1 403 Forbidden');
    echo json_encode(['error' => 'Access denied. Administrator privileges required.']);
    exit();
}

// Initializes the response array.
$response = ['success' => false, 'member' => null];

// Checks if a member ID is provided in the GET request.
if (isset($_GET['id']) && !empty($_GET['id'])) {
    // Validates the member ID to ensure it's an integer.
    $member_id = filter_var($_GET['id'], FILTER_VALIDATE_INT);

    // If the ID is invalid, returns a 400 Bad Request error.
    if ($member_id === false) {
        header('HTTP/1.1 400 Bad Request');
        $response['error'] = 'Invalid member ID.';
    } else {
        // Prepares a SQL statement to fetch details for a specific member.
        // The 'AND Role_ID = 1' clause ensures that admins can only fetch details for regular users.
        $stmt = $conn->prepare("SELECT user_ID, username, email, company, position FROM users WHERE user_ID = ? AND Role_ID = 1");
        $stmt->bind_param("i", $member_id);

        // Executes the statement.
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            // Fetches the member data.
            if ($member = $result->fetch_assoc()) {
                // If member is found, sets success to true and includes the member data in the response.
                $response['success'] = true;
                $response['member'] = $member;
            } else {
                // If no member is found (or the user is not a regular user), sets an error message.
                $response['error'] = 'Member not found or not a regular user.';
            }
        } else {
            // If the query fails, logs the error and sets an error message.
            error_log("Admin Get Member Details Error: " . $stmt->error);
            $response['error'] = 'Failed to fetch member details.';
        }
        $stmt->close();
    }
} else {
    // If no member ID is provided, returns a 400 Bad Request error.
    header('HTTP/1.1 400 Bad Request');
    $response['error'] = 'Member ID is required.';
}

// Closes the database connection.
$conn->close();
// Sets the content type to JSON.
header('Content-Type: application/json');
// Sends the JSON response.
echo json_encode($response);
?>