<?php
// Includes the database connection script.
include 'db_connect.php';

// Retrieves and validates the file ID from the GET request.
$file_id = isset($_GET['file_id']) ? filter_var($_GET['file_id'], FILTER_VALIDATE_INT) : null;
$user_id = $_SESSION['id']; // Gets the current user's ID from the session.

// If the file ID is invalid, the script terminates.
if (!$file_id) {
    die("Invalid file ID.");
}

// Fetches file information from the database, including the project ID for security checks.
$stmt = $conn->prepare("SELECT f.File_Name, f.File_URL, t.Project_ID FROM files f JOIN tasks t ON f.Task_ID = t.Task_ID WHERE f.File_ID = ?");
$stmt->bind_param("i", $file_id);
$stmt->execute();
$result = $stmt->get_result();
$file = $result->fetch_assoc();
$stmt->close();

// If no file is found with the given ID, the script terminates.
if (!$file) {
    die("File not found.");
}

// Security check: Ensures the user is a member of the project associated with the file.
// Admins are exempt from this check and can download any file.
$is_admin = isset($_SESSION['role']) && $_SESSION['role'] === 2;
if (!$is_admin) {
    $project_id = $file['Project_ID'];
    $check_stmt = $conn->prepare("SELECT Member_ID FROM project_members WHERE Project_ID = ? AND User_ID = ?");
    $check_stmt->bind_param("ii", $project_id, $user_id);
    $check_stmt->execute();
    $check_stmt->store_result();

    // If the user is not a member of the project, access is denied.
    if ($check_stmt->num_rows === 0) {
        die("You do not have permission to access this file.");
    }
    $check_stmt->close();
}

// Serves the file for download if all checks pass.
$file_path = $file['File_URL'];
if (file_exists($file_path)) {
    // Sets appropriate headers to trigger a file download in the browser.
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream'); // Generic MIME type for binary files.
    header('Content-Disposition: attachment; filename="' . basename($file['File_Name']) . '"'); // Suggests the original filename.
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($file_path));
    // Reads the file and outputs its contents to the browser.
    readfile($file_path);
    exit;
} else {
    // If the file is not found on the server, the script terminates.
    die("File not found on server.");
}
?>