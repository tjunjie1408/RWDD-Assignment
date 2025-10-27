<?php
header('Content-Type: application/json');
include 'db_connect.php';

$project_id = isset($_GET['project_id']) ? filter_var($_GET['project_id'], FILTER_VALIDATE_INT) : null;

if (!$project_id) {
    echo json_encode(['success' => false, 'error' => 'Invalid project ID.']);
    exit;
}

// Fetch members of the project
$stmt = $conn->prepare("SELECT u.user_ID, u.username FROM users u JOIN project_members pm ON u.user_ID = pm.User_ID WHERE pm.Project_ID = ?");
$stmt->bind_param("i", $project_id);
$stmt->execute();
$result = $stmt->get_result();
$members = [];
while ($row = $result->fetch_assoc()) {
    $members[] = $row;
}

$stmt->close();
$conn->close();

echo json_encode(['success' => true, 'members' => $members]);
?>
