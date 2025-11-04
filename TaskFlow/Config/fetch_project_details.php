<?php
// Includes the database connection script.
include 'db_connect.php';

// Security check: Ensures the user is logged in before they can fetch project details.
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('HTTP/1.1 401 Unauthorized');
    echo json_encode(['success' => false, 'error' => 'User not logged in.']);
    exit();
}

// Initializes the response array.
$response = ['success' => false, 'project' => null];

// Checks if a project ID is provided in the GET request.
if (isset($_GET['id']) && !empty($_GET['id'])) {
    // Validates the project ID to ensure it's an integer.
    $project_id = filter_var($_GET['id'], FILTER_VALIDATE_INT);

    // If the ID is invalid, returns a 400 Bad Request error.
    if ($project_id === false) {
        header('HTTP/1.1 400 Bad Request');
        $response['error'] = 'Invalid Project ID.';
    } else {
        // Prepares a SQL statement to fetch details for a specific project.
        $stmt = $conn->prepare("SELECT Project_ID, Title, Description, Project_Start_Time, Project_End_Time, Project_Status FROM projects WHERE Project_ID = ?");
        $stmt->bind_param("i", $project_id);

        // Executes the statement.
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            // Fetches the project data.
            if ($project = $result->fetch_assoc()) {
                // If the project is found, sets success to true and includes the project data in the response.
                $response['success'] = true;
                $response['project'] = $project;
            } else {
                // If no project is found, sets an error message.
                $response['error'] = 'Project not found.';
            }
        } else {
            // If the query fails, logs the error and sets an error message.
            error_log("Fetch Project Details Error: " . $stmt->error);
            $response['error'] = 'Failed to fetch project details.';
        }
        $stmt->close();
    }
} else {
    // If no project ID is provided, returns a 400 Bad Request error.
    header('HTTP/1.1 400 Bad Request');
    $response['error'] = 'Project ID is required.';
}

// Closes the database connection.
$conn->close();
// Sets the content type to JSON.
header('Content-Type: application/json');
// Sends the JSON response.
echo json_encode($response);
?>