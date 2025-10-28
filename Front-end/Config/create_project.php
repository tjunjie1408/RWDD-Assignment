<?php
// Includes the database connection script.
include 'db_connect.php';

// Security check: Ensures the user is logged in and has admin privileges.
// Redirects to the admin project page with an error if the check fails.
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['role'] !== 2) {
    header("Location: ../admin_project.php?error=auth");
    exit();
}

// Processes the form submission if the request method is POST.
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieves and sanitizes project data from the POST request.
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $start_date = trim($_POST['startDate'] ?? '');
    $end_date = trim($_POST['endDate'] ?? '');
    $admin_id = $_SESSION['id']; // The admin creating the project.
    $status = 'Not Started'; // Default status for new projects.

    // Server-side validation for required fields.
    if (empty($title) || empty($start_date) || empty($end_date)) {
        header("Location: ../admin_project.php?error=validation");
        exit();
    }

    // Prepares an SQL statement to insert the new project into the 'projects' table.
    $stmt = $conn->prepare("INSERT INTO projects (Title, Description, Project_Start_Date, Project_End_Date, Project_Status, User_ID) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssi", $title, $description, $start_date, $end_date, $status, $admin_id);

    // Executes the statement to create the project.
    if ($stmt->execute()) {
        // Retrieves the ID of the newly created project.
        $project_id = $stmt->insert_id;

        // Automatically adds the admin who created the project as a member.
        $member_stmt = $conn->prepare("INSERT INTO project_members (Project_ID, User_ID) VALUES (?, ?)");
        $member_stmt->bind_param("ii", $project_id, $admin_id);
        $member_stmt->execute();
        $member_stmt->close();

        // If members were selected in the form, add them to the 'project_members' table.
        if (isset($_POST['members']) && is_array($_POST['members'])) {
            $member_insert_stmt = $conn->prepare("INSERT INTO project_members (Project_ID, User_ID) VALUES (?, ?)");
            foreach ($_POST['members'] as $member_id) {
                $member_insert_stmt->bind_param("ii", $project_id, $member_id);
                $member_insert_stmt->execute();
            }
            $member_insert_stmt->close();
        }

        // Redirects to the admin project page with a success message.
        header("Location: ../admin_project.php?success=created");
        exit();
    } else {
        // If project creation fails, logs the error and redirects with a database error message.
        error_log("Create Project Error: " . $stmt->error);
        header("Location: ../admin_project.php?error=db");
        exit();
    }
    $stmt->close();
} else {
    // If the request method is not POST, redirects with an invalid request error.
    header("Location: ../admin_project.php?error=invalid_request");
    exit();
}

// Closes the database connection.
$conn->close();
?>