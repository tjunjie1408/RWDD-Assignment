<?php
// Sets the content type to JSON.
header('Content-Type: application/json');
// Includes the database connection script.
include 'db_connect.php';

// Retrieves and validates the project ID from the GET request.
$project_id = isset($_GET['project_id']) ? filter_var($_GET['project_id'], FILTER_VALIDATE_INT) : null;

// If the project ID is invalid, returns an error.
if (!$project_id) {
    echo json_encode(['success' => false, 'error' => 'Invalid project ID.']);
    exit;
}

// Prepares a SQL statement to fetch the ID and username of all members of a specific project.
// It joins the 'users' and 'project_members' tables to link users to projects.
$stmt = $conn->prepare("SELECT u.user_ID, u.username FROM users u JOIN project_members pm ON u.user_ID = pm.User_ID WHERE pm.Project_ID = ?");
$stmt->bind_param("i", $project_id);
$stmt->execute();
$result = $stmt->get_result();
$members = [];
// Iterates through the results and builds the members array.
while ($row = $result->fetch_assoc()) {
    $members[] = $row;
}

// Closes the statement and the database connection.
$stmt->close();
$conn->close();

// Sends a success response containing the list of project members.
echo json_encode(['success' => true, 'members' => $members]);
?>
