<?php
include 'db_connect.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('HTTP/1.1 401 Unauthorized');
    echo json_encode(['success' => false, 'error' => 'User not logged in.']);
    exit();
}

$project_id = isset($_GET['project_id']) ? filter_var($_GET['project_id'], FILTER_VALIDATE_INT) : null;
if (!$project_id) {
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['success' => false, 'error' => 'Invalid project ID.']);
    exit();
}

$user_id = $_SESSION['id'];

// Security check: ensure user is a member of the project
$check_stmt = $conn->prepare("SELECT Member_ID FROM project_members WHERE Project_ID = ? AND User_ID = ?");
$check_stmt->bind_param("ii", $project_id, $user_id);
$check_stmt->execute();
$check_stmt->store_result();
if ($check_stmt->num_rows === 0) {
    header('HTTP/1.1 403 Forbidden');
    echo json_encode(['success' => false, 'error' => 'Access denied to this project.']);
    exit();
}
$check_stmt->close();

// Fetch tasks for the project, joining with users table to get assigner/assignee names
$sql = "
    SELECT 
        t.*, 
        assigner.username AS assigner_name,
        assignee.username AS assignee_name
    FROM tasks t
    LEFT JOIN users AS assigner ON t.Assigner_ID = assigner.user_ID
    LEFT JOIN users AS assignee ON t.Assigned_User_ID = assignee.user_ID
    WHERE t.Project_ID = ?
    ORDER BY t.Task_Created_Time DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $project_id);

$response = ['success' => false, 'tasks' => []];

if ($stmt->execute()) {
    $result = $stmt->get_result();
    $tasks = [];
    while ($row = $result->fetch_assoc()) {
        $tasks[] = $row;
    }
    $response['success'] = true;
    $response['tasks'] = $tasks;
} else {
    $response['error'] = 'Failed to fetch tasks.';
}

$stmt->close();
$conn->close();

header('Content-Type: application/json');
echo json_encode($response);
?>