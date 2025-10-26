<?php
include 'db_connect.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('HTTP/1.1 401 Unauthorized');
    exit();
}

$project_id = isset($_GET['project_id']) ? filter_var($_GET['project_id'], FILTER_VALIDATE_INT) : null;
if (!$project_id) {
    header('HTTP/1.1 400 Bad Request');
    exit();
}

// Security check could be added here to ensure the requester is part of the project

$sql = "
    SELECT u.user_ID, u.username 
    FROM users u
    JOIN project_members pm ON u.user_ID = pm.User_ID
    WHERE pm.Project_ID = ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $project_id);
$stmt->execute();
$result = $stmt->get_result();

$members = [];
while ($row = $result->fetch_assoc()) {
    $members[] = $row;
}

$stmt->close();
$conn->close();

header('Content-Type: application/json');
echo json_encode(['success' => true, 'members' => $members]);
?>