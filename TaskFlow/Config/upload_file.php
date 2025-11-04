<?php
// Includes the database connection script.
include 'db_connect.php';

// Processes the request only if it's a POST method.
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $target_dir = "../uploads/";
    // Permission check: Ensures the server has write permissions for the upload directory.
    if (!is_writable($target_dir)) {
        header("location: ../tasks.php?project_id=" . $_POST['projectId'] . "&error=permissions");
        exit;
    }

    // Retrieves IDs from the POST request.
    $taskId = $_POST['taskId'];
    $projectId = $_POST['projectId'];
    $userId = $_SESSION['id'];

    // Checks if a file was uploaded successfully.
    if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
        $target_dir = "../uploads/";
        $file_name = basename($_FILES["file"]["name"]);
        $file_type = $_FILES["file"]["type"];
        
        // --- File Processing and Security ---
        // Sanitizes the filename by removing characters that could be used for directory traversal or other attacks.
        $safe_file_name = preg_replace("/[^a-zA-Z0-9\._-] /", "", $file_name);
        // Creates a unique filename using the current timestamp to prevent overwriting existing files.
        $unique_file_name = time() . "_" . $safe_file_name;
        $target_file = $target_dir . $unique_file_name;

        // Security: Disallows the upload of potentially executable or harmful file types.
        $file_ext = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $disallowed_exts = ["exe", "php", "sh", "bat"];
        if (in_array($file_ext, $disallowed_exts)) {
            header("location: ../tasks.php?project_id=$projectId&error=invalid_file_type");
            exit;
        }

        // Moves the uploaded file to the final destination.
        if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
            // If the file move is successful, inserts a record into the 'files' table.
            $sql = "INSERT INTO files (File_Name, File_URL, File_Type, Task_ID, Project_ID, User_ID) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssiii", $safe_file_name, $target_file, $file_type, $taskId, $projectId, $userId);

            if ($stmt->execute()) {
                // On success, redirects with a success message.
                header("location: ../tasks.php?project_id=$projectId&success=file_uploaded");
            } else {
                // On database insertion failure, redirects with an error.
                header("location: ../tasks.php?project_id=$projectId&error=db");
            }
            $stmt->close();
        } else {
            // If moving the file fails, redirects with an error.
            header("location: ../tasks.php?project_id=$projectId&error=upload_failed");
        }
    } else {
        // If no file was uploaded or an error occurred, redirects with an error.
        header("location: ../tasks.php?project_id=$projectId&error=no_file");
    }

    $conn->close();
}
?>