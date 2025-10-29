<?php
include 'db_connect.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('HTTP/1.1 401 Unauthorized');
    echo json_encode(['success' => false, 'error' => 'User not logged in.']);
    exit();
}

if (isset($_FILES['avatar'])) {
    $uploadDir = '../uploads/';
    // Create the directory if it doesn't exist
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $file = $_FILES['avatar'];
    $error = $file['error'];

    if ($error !== UPLOAD_ERR_OK) {
        header('HTTP/1.1 500 Internal Server Error');
        echo json_encode(['success' => false, 'error' => 'File upload error: ' . $error]);
        exit();
    }

    // Sanitize the filename and create a unique name
    $fileName = basename($file['name']);
    $fileInfo = pathinfo($fileName);
    $fileExtension = strtolower($fileInfo['extension']);
    $uniqueName = uniqid('avatar_', true) . '.' . $fileExtension;
    $uploadPath = $uploadDir . $uniqueName;

    // Validate file type (allow common image formats)
    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
    if (!in_array($fileExtension, $allowedTypes)) {
        header('HTTP/1.1 400 Bad Request');
        echo json_encode(['success' => false, 'error' => 'Invalid file type. Only JPG, PNG, and GIF are allowed.']);
        exit();
    }

    // Move the uploaded file
    if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
        // Return the path relative to the web root
        echo json_encode(['success' => true, 'filePath' => 'uploads/' . $uniqueName]);
    } else {
        header('HTTP/1.1 500 Internal Server Error');
        echo json_encode(['success' => false, 'error' => 'Failed to move uploaded file.']);
    }
} else {
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['success' => false, 'error' => 'No file uploaded.']);
}
?>