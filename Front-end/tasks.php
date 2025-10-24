<?php
    include 'Config/db_connect.php';

    // Check if the user is logged in, if not then redirect to login page
    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
        header("location: signup.php");
        exit;
    }

    // Get project ID from URL and validate it
    $project_id = isset($_GET['project_id']) ? filter_var($_GET['project_id'], FILTER_VALIDATE_INT) : null;

    if (!$project_id) {
        header("location: project.php"); // Redirect if no valid ID is provided
        exit;
    }

    // TODO: Add a check to ensure the user is a member of this project before showing tasks.
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project Tasks</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0"/>
    <link rel="stylesheet" href="/RWDD-Assignment/Front-end/CSS/dashboard.css">
    <link rel="stylesheet" href="/RWDD-Assignment/Front-end/CSS/project.css"> 
</head>
<body>
    <button class="sidebar-menu-button">
        <span class="material-symbols-rounded">menu</span>
    </button>

    <aside class="sidebar">
        <nav class="sidebar-header">
            <a href="dashboard.php" class="header-logo"><img src="Pictures/logo.png" alt="TaskFlow"></a>
            <button class="sidebar-toggler"><span class="material-symbols-rounded">chevron_left</span></button>
        </nav>
        <nav class="sidebar-nav">
            <ul class="nav-list primary-nav">
                <li class="nav-item"><a href="dashboard.php" class="nav-link"><span class="material-symbols-rounded">dashboard</span><span class="nav-label">Dashboard</span></a></li>
                <li class="nav-item"><a href="project.php" class="nav-link active"><span class="material-symbols-rounded">task</span><span class="nav-label">Project</span></a></li>
                <li class="nav-item"><a href="member.php" class="nav-link"><span class="material-symbols-rounded">group</span><span class="nav-label">Member</span></a></li>
                <li class="nav-item"><a href="analysis.php" class="nav-link"><span class="material-symbols-rounded">bar_chart_4_bars</span><span class="nav-label">Report Analysis</span></a></li>
                <li class="nav-item"><a href="goal.php" class="nav-link"><span class="material-symbols-rounded">task_alt</span><span class="nav-label">Goal</span></a></li>
            </ul>
            <ul class="nav-list secondary-nav">
                <li class="nav-item"><a href="#" class="nav-link"><span class="material-symbols-rounded">help</span><span class="nav-label">Support</span></a></li>
                <li class="nav-item"><a href="Config/logout.php" class="nav-link"><span class="material-symbols-rounded">logout</span><span class="nav-label">Sign Out</span></a></li>
            </ul>
        </nav>
    </aside>

    <header>
        <div class="header-left"><h1>Project Tasks</h1></div>
        <div class="header-right">
            <div class="username" id="username"><p class="hello">Hello, <?php echo htmlspecialchars($_SESSION['username']); ?></p></div>
            <a href="profile.php"><img src="https://via.placeholder.com/150" class="user-avatar" id="userAvatar"></a>
        </div>
    </header>

    <main class="project-content">
        <div class="actions">
            <a href="project.php" class="primary">&larr; Back to All Projects</a>
        </div>

        <div id="task-list-container" class="list-view">
            <!-- Tasks will be injected here by JavaScript -->
        </div>
    </main>

    <script src="/RWDD-Assignment/Front-end/JS/sidebar.js"></script>
    <script src="/RWDD-Assignment/Front-end/JS/user_avatar.js"></script>
    <script src="/RWDD-Assignment/Front-end/JS/tasks.js"></script>
</body>
</html>