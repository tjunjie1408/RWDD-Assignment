<?php
// Starts a new session or resumes an existing one. This is necessary for session variables like user ID and role.
session_start();

// Defines the database connection parameters.
$servername = "localhost"; // The server where the database is hosted.
$username = "root";        // The database username.
$password = "";            // The database password.
$dbname = "rwdd";          // The name of the database.

// Creates a new MySQLi object to establish a connection to the database.
$conn = new mysqli($servername, $username, $password, $dbname);

// Checks if the connection to the database was successful.
// If the connection fails, it terminates the script and displays an error message.
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>