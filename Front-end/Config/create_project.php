<?php
include 'db_connect.php';

// Check if the user is logged in and is an admin
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['role'] !== 2) {
    header("Location: ../admin_project.php?error=auth");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $start_date = trim($_POST['startDate'] ?? '');
    $end_date = trim($_POST['endDate'] ?? '');
    $admin_id = $_SESSION['id'];
    $status = 'Not Started';

    if (empty($title) || empty($start_date) || empty($end_date)) {
        header("Location: ../admin_project.php?error=validation");
        exit();
    }

    $stmt = $conn->prepare("INSERT INTO projects (Title, Description, Project_Start_Date, Project_End_Date, Project_Status, User_ID) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssi", $title, $description, $start_date, $end_date, $status, $admin_id);

    if ($stmt->execute()) {
        $project_id = $stmt->insert_id;

        // Automatically add the admin as a member of the new project
        $member_stmt = $conn->prepare("INSERT INTO project_members (Project_ID, User_ID) VALUES (?, ?)");
        $member_stmt->bind_param("ii", $project_id, $admin_id);
        $member_stmt->execute();
        $member_stmt->close();

        header("Location: ../admin_project.php?success=created");
        exit();
    } else {
        error_log("Create Project Error: " . $stmt->error);
        header("Location: ../admin_project.php?error=db");
        exit();
    }
    $stmt->close();
} else {
    header("Location: ../admin_project.php?error=invalid_request");
    exit();
}

$conn->close();
?>