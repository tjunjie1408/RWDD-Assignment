<?php
include 'db_connect.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('HTTP/1.1 401 Unauthorized');
    echo json_encode(['success' => false, 'error' => 'You must be logged in to join a project.']);
    exit();
}

$response = ['success' => false, 'error' => ''];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $project_id = filter_var($_POST['projectId'] ?? '', FILTER_VALIDATE_INT);
    $user_id = $_SESSION['id'];

    if ($project_id === false) {
        header('HTTP/1.1 400 Bad Request');
        $response['error'] = "Invalid Project ID.";
        echo json_encode($response);
        exit();
    }

    // Check if the user is already a member
    $stmt_check = $conn->prepare("SELECT ProjectMember_ID FROM project_members WHERE Project_ID = ? AND User_ID = ?");
    $stmt_check->bind_param("ii", $project_id, $user_id);
    $stmt_check->execute();
    $stmt_check->store_result();

    if ($stmt_check->num_rows > 0) {
        header('HTTP/1.1 409 Conflict');
        $response['error'] = "You are already a member of this project.";
        $stmt_check->close();
        echo json_encode($response);
        exit();
    }
    $stmt_check->close();

    // Add user to the project
    $stmt_insert = $conn->prepare("INSERT INTO project_members (Project_ID, User_ID) VALUES (?, ?)");
    $stmt_insert->bind_param("ii", $project_id, $user_id);

    if ($stmt_insert->execute()) {
        $response['success'] = true;
    } else {
        error_log("Join Project Error: " . $stmt_insert->error);
        header('HTTP/1.1 500 Internal Server Error');
        $response['error'] = 'Failed to join project.';
    }
    $stmt_insert->close();

} else {
    header('HTTP/1.1 405 Method Not Allowed');
    $response['error'] = 'Invalid request method.';
}

$conn->close();
header('Content-Type: application/json');
echo json_encode($response);
?>