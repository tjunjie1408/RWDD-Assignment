<?php
include 'db_connect.php';

$file_id = isset($_GET['file_id']) ? filter_var($_GET['file_id'], FILTER_VALIDATE_INT) : null;
$user_id = $_SESSION['id'];

if (!$file_id) {
    die("Invalid file ID.");
}

// Get file info
$stmt = $conn->prepare("SELECT f.File_Name, f.File_URL, t.Project_ID FROM files f JOIN tasks t ON f.Task_ID = t.Task_ID WHERE f.File_ID = ?");
$stmt->bind_param("i", $file_id);
$stmt->execute();
$result = $stmt->get_result();
$file = $result->fetch_assoc();
$stmt->close();

if (!$file) {
    die("File not found.");
}

// Security check: Ensure user is a member of the project, unless they are an admin.
$is_admin = isset($_SESSION['role']) && $_SESSION['role'] === 2;
if (!$is_admin) {
    $project_id = $file['Project_ID'];
    $check_stmt = $conn->prepare("SELECT Member_ID FROM project_members WHERE Project_ID = ? AND User_ID = ?");
    $check_stmt->bind_param("ii", $project_id, $user_id);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows === 0) {
        die("You do not have permission to access this file.");
    }
    $check_stmt->close();
}

// Serve the file for download
$file_path = $file['File_URL'];
if (file_exists($file_path)) {
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . basename($file['File_Name']) . '"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($file_path));
    readfile($file_path);
    exit;
} else {
    die("File not found on server.");
}
?>