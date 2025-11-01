<?php
// Includes the database connection script.
include 'db_connect.php';

// Security check: Ensures the user is logged in.
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('HTTP/1.1 401 Unauthorized');
    echo json_encode(['success' => false, 'error' => 'User not logged in.']);
    exit();
}

// Checks if a file has been uploaded.
if (isset($_FILES['avatar'])) {
    $uploadDir = '../uploads/';
    // Creates the upload directory if it doesn't exist.
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $file = $_FILES['avatar'];
    $error = $file['error'];

    // Checks for any upload errors.
    if ($error !== UPLOAD_ERR_OK) {
        header('HTTP/1.1 500 Internal Server Error');
        echo json_encode(['success' => false, 'error' => 'File upload error: ' . $error]);
        exit();
    }

    // --- File Processing and Security ---
    // Sanitizes the filename and creates a unique name to prevent overwrites and security issues.
    $fileName = basename($file['name']);
    $fileInfo = pathinfo($fileName);
    $fileExtension = strtolower($fileInfo['extension']);
    $uniqueName = uniqid('avatar_', true) . '.' . $fileExtension;
    $uploadPath = $uploadDir . $uniqueName;

    // Validates the file type to ensure only allowed image formats are uploaded.
    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
    if (!in_array($fileExtension, $allowedTypes)) {
        header('HTTP/1.1 400 Bad Request');
        echo json_encode(['success' => false, 'error' => 'Invalid file type. Only JPG, PNG, and GIF are allowed.']);
        exit();
    }

    // Moves the uploaded file from the temporary directory to the final destination.
    if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
        // On success, returns the relative path to the new file so the frontend can display it.
        echo json_encode(['success' => true, 'filePath' => 'uploads/' . $uniqueName]);
    } else {
        // If moving the file fails, returns a server error.
        header('HTTP/1.1 500 Internal Server Error');
        echo json_encode(['success' => false, 'error' => 'Failed to move uploaded file.']);
    }
} else {
    // If no file was sent in the request, returns a bad request error.
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['success' => false, 'error' => 'No file uploaded.']);
}
?>