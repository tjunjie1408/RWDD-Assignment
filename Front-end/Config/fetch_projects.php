<?php
header('Content-Type: application/json');
include 'db_connect.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    echo json_encode(['error' => 'Not logged in']);
    http_response_code(401);
    exit;
}

$current_user_id = $_SESSION['user_id'];
$is_admin = (isset($_SESSION['role']) && $_SESSION['role'] === 2);

// Admins are treated as members of all projects.
// For regular users, we explicitly check the project_members table.
if ($is_admin) {
    $sql = "SELECT Project_ID, Title, Description, Project_Start_Date, Project_End_Date, Project_Status, Progress_Percent, TRUE AS is_member
            FROM projects
            ORDER BY Project_Start_Date DESC";
    $stmt = $conn->prepare($sql);
} else {
    $sql = "SELECT p.Project_ID, p.Title, p.Description, p.Project_Start_Date, p.Project_End_Date, p.Project_Status, p.Progress_Percent,
                   (pm.User_ID IS NOT NULL) AS is_member
            FROM projects p
            LEFT JOIN project_members pm ON p.Project_ID = pm.Project_ID AND pm.User_ID = ?
            ORDER BY p.Project_Start_Date DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $current_user_id);
}

$stmt->execute();
$result = $stmt->get_result();

$projects = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        // Ensure is_member is a boolean
        $row['is_member'] = (bool)$row['is_member'];
        $projects[] = $row;
    }
}

echo json_encode($projects);

$stmt->close();
$conn->close();
?>