<?php
// Includes the database connection script.
include 'db_connect.php';

// Security check: Ensures the user is logged in before they can view member details.
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('HTTP/1.1 401 Unauthorized');
    echo json_encode(['error' => 'User not logged in.']);
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
        // This is intended for a "view-only" context, so it might have different logic than the admin version.
        // The 'AND Role_ID = 1' ensures that only regular users' details are fetched.
        $stmt = $conn->prepare("SELECT user_ID, username, email, company, position FROM users WHERE user_ID = ? AND Role_ID = 1");
        $stmt->bind_param("i", $member_id);

        // Executes the statement.
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $response['success'] = true;
            // Fetches the member data and adds it to the response.
            $response['member'] = $result->fetch_assoc();
        } else {
            // If the query fails, logs the error and sets an error message.
            error_log("Get Member Details for View Error: " . $stmt->error);
            $response['error'] = 'Failed to fetch member details.';
        }
        $stmt->close();
    }
}
// Closes the database connection.
$conn->close();
// Sets the content type to JSON.
header('Content-Type: application/json');
// Sends the JSON response.
echo json_encode($response);
?>