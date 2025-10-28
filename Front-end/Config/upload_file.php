<?php
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $taskId = $_POST['taskId'];
    $projectId = $_POST['projectId'];
    $userId = $_SESSION['id'];

    if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
        $target_dir = "../uploads/";
        $file_name = basename($_FILES["file"]["name"]);
        $file_type = $_FILES["file"]["type"];
        // Sanitize the filename to prevent security issues
        $safe_file_name = preg_replace("/[^a-zA-Z0-9\._-]/", "", $file_name);
        $unique_file_name = time() . "_" . $safe_file_name;
        $target_file = $target_dir . $unique_file_name;

        // Security: Disallow executable files
        $file_ext = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $disallowed_exts = ["exe", "php", "sh", "bat"];
        if (in_array($file_ext, $disallowed_exts)) {
            header("location: ../tasks.php?project_id=$projectId&error=invalid_file_type");
            exit;
        }

        if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
            $sql = "INSERT INTO files (File_Name, File_URL, File_Type, Task_ID, Project_ID, User_ID) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssiii", $safe_file_name, $target_file, $file_type, $taskId, $projectId, $userId);

            if ($stmt->execute()) {
                header("location: ../tasks.php?project_id=$projectId&success=file_uploaded");
            } else {
                header("location: ../tasks.php?project_id=$projectId&error=db");
            }
            $stmt->close();
        } else {
            header("location: ../tasks.php?project_id=$projectId&error=upload_failed");
        }
    } else {
        header("location: ../tasks.php?project_id=$projectId&error=no_file");
    }

    $conn->close();
}
?>