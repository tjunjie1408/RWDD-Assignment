<?php
// Sets the content type to JSON.
header('Content-Type: application/json');
// Includes the database connection script.
include 'db_connect.php';

// Security check: Ensures the user is logged in.
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    echo json_encode([]);
    exit;
}

// Gets the current user's ID from the session.
$user_id = $_SESSION['id'];

// Prepares a SQL statement to fetch the goals for the logged-in user.
// The column names are aliased ('as id', 'as title', etc.) to match the format expected by the FullCalendar library.
$sql = "SELECT Goal_ID as id, Title as title, Goal_Start_Time as start, Goal_End_Time as end FROM goals WHERE User_ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$events = [];
// Iterates through the results and builds an array of event objects.
while ($row = $result->fetch_assoc()) {
    $events[] = $row;
}

// Encodes the events array into JSON format, which will be consumed by the FullCalendar instance on the frontend.
echo json_encode($events);

// Closes the statement and the database connection.
$stmt->close();
$conn->close();
?>