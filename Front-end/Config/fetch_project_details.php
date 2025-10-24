<?php
include 'db_connect.php';

// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('HTTP/1.1 401 Unauthorized');
    echo json_encode(['success' => false, 'error' => 'User not logged in.']);
    exit();
}

$response = ['success' => false, 'project' => null];

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $project_id = filter_var($_GET['id'], FILTER_VALIDATE_INT);

    if ($project_id === false) {
        header('HTTP/1.1 400 Bad Request');
        $response['error'] = 'Invalid Project ID.';
    } else {
        $stmt = $conn->prepare("SELECT Project_ID, Title, Description, Project_Start_Time, Project_End_Time, Project_Status FROM projects WHERE Project_ID = ?");
        $stmt->bind_param("i", $project_id);

        if ($stmt->execute()) {
            $result = $stmt->get_result();
            if ($project = $result->fetch_assoc()) {
                $response['success'] = true;
                $response['project'] = $project;
            } else {
                $response['error'] = 'Project not found.';
            }
        } else {
            error_log("Fetch Project Details Error: " . $stmt->error);
            $response['error'] = 'Failed to fetch project details.';
        }
        $stmt->close();
    }
} else {
    header('HTTP/1.1 400 Bad Request');
    $response['error'] = 'Project ID is required.';
}

$conn->close();
header('Content-Type: application/json');
echo json_encode($response);
?>