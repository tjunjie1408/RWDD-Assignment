<?php
// Sets the content type to JSON.
header('Content-Type: application/json');
// Includes the database connection script.
include 'db_connect.php';

// Security check: Ensures the user is logged in.
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    echo json_encode(['error' => 'Not logged in']);
    http_response_code(401);
    exit;
}

// Gets the current user's ID and role from the session.
$current_user_id = $_SESSION['user_id'];
$is_admin = (isset($_SESSION['role']) && $_SESSION['role'] === 2);

// The SQL query is customized based on the user's role.
if ($is_admin) {
    // Admins can see all projects and are considered a member of all of them.
    // 'TRUE AS is_member' ensures the 'is_member' flag is always true for admins.
    $sql = "SELECT Project_ID, Title, Description, Project_Start_Date, Project_End_Date, Project_Status, Progress_Percent, TRUE AS is_member
            FROM projects
            ORDER BY Project_Start_Date DESC";
    $stmt = $conn->prepare($sql);
} else {
    // Regular users see all projects, but with a flag indicating whether they are a member or not.
    // A LEFT JOIN on 'project_members' checks for membership.
    // '(pm.User_ID IS NOT NULL)' will be true (1) if they are a member, and false (0) otherwise.
    $sql = "SELECT p.Project_ID, p.Title, p.Description, p.Project_Start_Date, p.Project_End_Date, p.Project_Status, p.Progress_Percent,
                   (pm.User_ID IS NOT NULL) AS is_member
            FROM projects p
            LEFT JOIN project_members pm ON p.Project_ID = pm.Project_ID AND pm.User_ID = ?
            ORDER BY p.Project_Start_Date DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $current_user_id);
}

// Executes the prepared statement.
$stmt->execute();
$result = $stmt->get_result();

$projects = [];
if ($result->num_rows > 0) {
    // Iterates through the results and builds the projects array.
    while($row = $result->fetch_assoc()) {
        // Explicitly casts the 'is_member' flag to a boolean for consistent JSON output.
        $row['is_member'] = (bool)$row['is_member'];
        $projects[] = $row;
    }
}

// Encodes the projects array into JSON and sends the response.
echo json_encode($projects);

// Closes the statement and the database connection.
$stmt->close();
$conn->close();
?>