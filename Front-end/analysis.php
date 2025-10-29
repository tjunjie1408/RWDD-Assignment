<?php
    // Includes the database connection and session start.
    include 'Config/db_connect.php';

    // --- Authentication and Authorization ---
    // Checks if a user is logged in.
    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
        header("location: signup.php");
        exit;
    }

    // Determines the user's ID and if they are an admin. This is used to fetch the correct data.
    $user_id = $_SESSION['id'];
    $is_admin = isset($_SESSION['role']) && $_SESSION['role'] === 2;

    // --- Data Fetching for Charts ---
    // Initializes arrays to hold the data for the charts.
    $project_completion_data = ['completed' => 0, 'in_progress' => 0, 'not_started' => 0, 'on_hold' => 0];
    $task_distribution_data = ['done' => 0, 'open' => 0];

    // --- Fetch Project Data ---
    // The base SQL query to count projects by their status.
    $project_sql = "SELECT Project_Status, COUNT(*) as count FROM projects";
    if (!$is_admin) {
        // If the user is not an admin, the query is modified to only count projects they are a member of.
        $project_sql .= " WHERE Project_ID IN (SELECT Project_ID FROM project_members WHERE User_ID = ?)";
    }
    $project_sql .= " GROUP BY Project_Status";

    $project_stmt = $conn->prepare($project_sql);
    if (!$is_admin) {
        $project_stmt->bind_param("i", $user_id);
    }
    $project_stmt->execute();
    $project_result = $project_stmt->get_result();

    // Populates the data array with the results from the database.
    while ($row = $project_result->fetch_assoc()) {
        $status = strtolower(str_replace(' ', '_', $row['Project_Status']));
        if (array_key_exists($status, $project_completion_data)) {
            $project_completion_data[$status] = $row['count'];
        }
    }
    $project_stmt->close();

    // --- Fetch Task Data ---
    // The base SQL query to count tasks by their status.
    $task_sql = "SELECT Status, COUNT(*) as count FROM tasks";
    if (!$is_admin) {
        // If the user is not an admin, the query is modified to only count tasks assigned to them.
        $task_sql .= " WHERE User_ID = ?";
    }
    $task_sql .= " GROUP BY Status";

    $task_stmt = $conn->prepare($task_sql);
    if (!$is_admin) {
        $task_stmt->bind_param("i", $user_id);
    }
    $task_stmt->execute();
    $task_result = $task_stmt->get_result();

    // Populates the data array with the results.
    while ($row = $task_result->fetch_assoc()) {
        $status = strtolower($row['Status']);
        if (array_key_exists($status, $task_distribution_data)) {
            $task_distribution_data[$status] = $row['count'];
        }
    }
    $task_stmt->close();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report Analysis</title>
    <link rel="icon" href="Pictures/icon.png">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0"/>
    <link rel="stylesheet" href="CSS/dashboard.css">
    <link rel="stylesheet" href="CSS/analysis.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <button class="sidebar-menu-button">
        <span class="material-symbols-rounded">menu</span>
    </button>

    <aside class="sidebar">
        <nav class="sidebar-header">
            <a href="<?php echo $is_admin ? 'admin_dashboard.php' : 'dashboard.php'; ?>" class="header-logo">
                <img src="Pictures/logo.png" alt="TaskFlow">
            </a>
            <button class="sidebar-toggler">
                <span class="material-symbols-rounded">chevron_left</span>
            </button>
        </nav>
        <nav class="sidebar-nav">
            <ul class="nav-list primary-nav">
                <?php if ($is_admin): ?>
                    <li class="nav-item"><a href="admin_dashboard.php" class="nav-link"><span class="material-symbols-rounded">dashboard</span><span class="nav-label">Dashboard</span></a></li>
                    <li class="nav-item"><a href="admin_project.php" class="nav-link"><span class="material-symbols-rounded">task</span><span class="nav-label">Project</span></a></li>
                    <li class="nav-item"><a href="admin_member.php" class="nav-link"><span class="material-symbols-rounded">group</span><span class="nav-label">Member</span></a></li>
                <?php else: ?>
                    <li class="nav-item"><a href="dashboard.php" class="nav-link"><span class="material-symbols-rounded">dashboard</span><span class="nav-label">Dashboard</span></a></li>
                    <li class="nav-item"><a href="project.php" class="nav-link"><span class="material-symbols-rounded">task</span><span class="nav-label">Project</span></a></li>
                    <li class="nav-item"><a href="member.php" class="nav-link"><span class="material-symbols-rounded">group</span><span class="nav-label">Member</span></a></li>
                <?php endif; ?>
                <li class="nav-item"><a href="analysis.php" class="nav-link active"><span class="material-symbols-rounded">bar_chart_4_bars</span><span class="nav-label">Report Analysis</span></a></li>
                <li class="nav-item"><a href="goal.php" class="nav-link"><span class="material-symbols-rounded">task_alt</span><span class="nav-label">Goal</span></a></li>
            </ul>
            <ul class="nav-list secondary-nav">
                <li class="nav-item"><a href="faq.php" class="nav-link"><span class="material-symbols-rounded">help</span><span class="nav-label">Support</span></a></li>
                <li class="nav-item"><a href="Config/logout.php" class="nav-link"><span class="material-symbols-rounded">logout</span><span class="nav-label">Sign Out</span></a></li>
            </ul>
        </nav>
    </aside>

    <header>
        <div class="header-left"><h1>Report Analysis</h1></div>
        <div class="header-right">
            <div class="username" id="username"><p class="hello">Hello, <?php echo htmlspecialchars($_SESSION['username']); ?></p></div>
            <a href="profile.php"><img src="https://via.placeholder.com/150" class="user-avatar" id="userAvatar"></a>
        </div>
    </header>

    <main class="analysis-container">
        <div class="analysis-box">
            <h2>Project Status Distribution</h2>
            <div class="analysis-chart">
                <canvas id="projectChart"></canvas>
            </div>
        </div>
    
        <div class="analysis-box">
            <h2>Task Status Distribution</h2>
            <div class="analysis-chart">
                <canvas id="taskChart"></canvas>
            </div>
        </div>
    </main>

    <script>
        // Pass PHP data to JavaScript
        const projectData = <?php echo json_encode(array_values($project_completion_data)); ?>;
        const projectLabels = <?php echo json_encode(array_keys($project_completion_data)); ?>;
        const taskData = <?php echo json_encode(array_values($task_distribution_data)); ?>;
        const taskLabels = <?php echo json_encode(array_keys($task_distribution_data)); ?>;
    </script>
    <script src="JS/sidebar.js"></script>
    <script src="JS/user_avatar.js"></script>
    <script src="JS/analysis.js"></script>
</body>
</html>