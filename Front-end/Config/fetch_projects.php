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

// The query now joins with project_members to check if the current user is a member
$sql = "
    SELECT 
        p.Project_ID, 
        p.Title, 
        p.Description, 
        p.Project_Start_Date, 
        p.Project_End_Date, 
        p.Project_Status, 
        p.Progress_Percent,
        CASE WHEN pm.User_ID IS NOT NULL THEN 1 ELSE 0 END AS is_member
    FROM 
        projects p
    LEFT JOIN 
        project_memebers pm ON p.Project_ID = pm.Project_ID AND pm.User_ID = ?
    ORDER BY 
        p.Project_Start_Time DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);

if ($stmt->execute()) {
    $result = $stmt->get_result();
    $projects = [];
    while ($row = $result->fetch_assoc()) {
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