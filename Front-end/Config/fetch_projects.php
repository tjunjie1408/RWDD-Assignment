<?php
include 'db_connect.php';

// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('HTTP/1.1 401 Unauthorized');
    echo json_encode(['success' => false, 'error' => 'User not logged in.']);
    exit();
}

$user_id = $_SESSION['id'];
$response = ['success' => false, 'projects' => []];

$sql = "
    SELECT 
        p.*,
        pm.User_ID AS member_user_id
    FROM 
        projects p
    LEFT JOIN 
        project_members pm ON p.Project_ID = pm.Project_ID AND pm.User_ID = ?
    ORDER BY 
        p.Project_Start_Date DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);

if ($stmt->execute()) {
    $result = $stmt->get_result();
    $projects = [];
    while ($row = $result->fetch_assoc()) {
        $row['is_member'] = !is_null($row['member_user_id']);
        unset($row['member_user_id']); // Clean up the response
        $projects[] = $row;
    }
    $response['success'] = true;
    $response['projects'] = $projects;
} else {
    error_log("Fetch Projects Error: " . $stmt->error);
    $response['error'] = 'Failed to fetch projects.';
}

$stmt->close();
$conn->close();
header('Content-Type: application/json');
echo json_encode($response);
?>