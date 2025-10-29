<?php
// Sets the content type of the response to JSON, ensuring the client interprets the output correctly.
header('Content-Type: application/json');
// Includes the database connection script.
include 'db_connect.php';

// Security check: Ensures that a user is logged in before allowing them to delete a goal.
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    echo json_encode(['success' => false, 'error' => 'Authentication required.']);
    exit();
}

// Processes the request only if the method is POST.
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieves and validates the goal ID from the POST data.
    $goal_id = filter_var($_POST['goalId'] ?? '', FILTER_VALIDATE_INT);
    $user_id = $_SESSION['id']; // Gets the current user's ID from the session.

    // If the goal ID is not a valid integer, returns an error.
    if ($goal_id === false) {
        echo json_encode(['success' => false, 'error' => 'Invalid Goal ID.']);
        exit();
    }

    // Security check: Verifies that the goal being deleted belongs to the currently logged-in user.
    // This prevents a user from deleting another user's goals.
    $check_sql = "SELECT User_ID FROM goals WHERE Goal_ID = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("i", $goal_id);
    $check_stmt->execute();
    $check_stmt->bind_result($owner_id);
    $check_stmt->fetch();
    $check_stmt->close();

    // If the owner ID from the database does not match the session user ID, access is denied.
    if ($owner_id != $user_id) {
        echo json_encode(['success' => false, 'error' => 'You do not have permission to delete this goal.']);
        exit();
    }

    // Prepares and executes the SQL statement to delete the goal.
    $sql = "DELETE FROM goals WHERE Goal_ID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $goal_id);

    // Checks if the deletion was successful.
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Database error: Could not delete the goal.']);
    }
    // Closes the statement and the database connection.
    $stmt->close();
    $conn->close();
    exit();
}
?>