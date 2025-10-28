<?php
header('Content-Type: application/json');
include 'db_connect.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    echo json_encode(['success' => false, 'error' => 'Authentication required.']);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $goal_id = filter_var($_POST['goalId'] ?? '', FILTER_VALIDATE_INT);
    $user_id = $_SESSION['id'];

    if ($goal_id === false) {
        echo json_encode(['success' => false, 'error' => 'Invalid Goal ID.']);
        exit();
    }

    // Security check: ensure the goal belongs to the user
    $check_sql = "SELECT User_ID FROM goals WHERE Goal_ID = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("i", $goal_id);
    $check_stmt->execute();
    $check_stmt->bind_result($owner_id);
    $check_stmt->fetch();
    $check_stmt->close();

    if ($owner_id != $user_id) {
        echo json_encode(['success' => false, 'error' => 'You do not have permission to delete this goal.']);
        exit();
    }

    $sql = "DELETE FROM goals WHERE Goal_ID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $goal_id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Database error: Could not delete the goal.']);
    }
    $stmt->close();
    $conn->close();
    exit();
}
?>