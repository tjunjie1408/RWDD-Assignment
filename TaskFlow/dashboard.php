<?php
    // Includes the database connection and session start.
    include 'Config/db_connect.php';

    // --- Authentication ---
    // Checks if a user is logged in. If not, they are redirected to the signup/login page.
    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
        header("location: signup.php");
        exit;
    }

    // --- Data Fetching ---
    $user_id = $_SESSION['id'];
    
    // Fetches the 5 most recent projects that the currently logged-in user is a member of.
    $projects_result = $conn->query("SELECT p.* FROM projects p JOIN project_members pm ON p.Project_ID = pm.Project_ID WHERE pm.User_ID = {$user_id} ORDER BY p.Project_Start_Date DESC LIMIT 5");

    // Fetches upcoming tasks due in the next 7 days.
    $tasks_result = $conn->query("SELECT t.Title as Task_Name, t.Task_End_Time FROM tasks t WHERE t.User_ID = {$user_id} AND t.Status != 'Done' ORDER BY t.Task_End_Time ASC LIMIT 7");

    // Fetches active goals.
    $goals_result = $conn->query("SELECT Title, Description, Goal_End_Time FROM goals WHERE User_ID = {$user_id} AND Status != 'Completed' LIMIT 5");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <!-- Linking Google Fonts for Icons -->
    <link rel="icon" href="Pictures/icon.png">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0"/>
    <link rel="stylesheet" href="CSS/dashboard.css">
    <link rel="stylesheet" href="CSS/project.css"> <!-- Added for project card styles -->
</head>
<body>
    <!-- Mobile Sidebar Menu Button -->
    <button class="sidebar-menu-button">
        <span class="material-symbols-rounded">menu</span>
    </button>

    <aside class="sidebar">
        <!-- Sidebar Header -->
        <nav class="sidebar-header">
            <a href="dashboard.php" class="header-logo">
                <img src="Pictures/logo.png" alt="TaskFlow">
            </a>
            <button class="sidebar-toggler">
                <span class="material-symbols-rounded">chevron_left</span>
            </button>
        </nav>

        <nav class="sidebar-nav">
            <!-- Primary Top Nav -->
            <ul class="nav-list primary-nav">
                <li class="nav-item">
                    <a href="dashboard.php" class="nav-link active">
                        <span class="material-symbols-rounded">dashboard</span>
                        <span class="nav-label">Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="project.php" class="nav-link">
                        <span class="material-symbols-rounded">task</span>
                        <span class="nav-label">Project</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="member.php" class="nav-link">
                        <span class="material-symbols-rounded">group</span>
                        <span class="nav-label">Member</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="analysis.php" class="nav-link">
                        <span class="material-symbols-rounded">bar_chart_4_bars</span>
                        <span class="nav-label">Report Analysis</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="goal.php" class="nav-link">
                        <span class="material-symbols-rounded">task_alt</span>
                        <span class="nav-label">Goal</span>
                    </a>
                </li>
            </ul>

            <!-- Secondary Bottom Nav -->
            <ul class="nav-list secondary-nav">
                <li class="nav-item">
                    <a href="faq.php" class="nav-link">
                        <span class="material-symbols-rounded">help</span>
                        <span class="nav-label">Support</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="Config/logout.php" class="nav-link">
                        <span class="material-symbols-rounded">logout</span>
                        <span class="nav-label">Sign Out</span>
                    </a>
                </li>
            </ul>
        </nav>
    </aside>

    <header>
        <div class="header-left">
          <h1>Welcome</h1>
        </div>
        <div class="header-right">
          <div class="username" id="username">
            <p class="hello">
                Hello, <?php echo htmlspecialchars($_SESSION['username']); ?>
            </p>
          </div>
          <a href="profile.php">
            <img src="https://via.placeholder.com/150"  class="user-avatar" id="userAvatar">
          </a>
        </div>
    </header>

    <main class="dashboard-content">
        <div class="real-time-clock" id="realTimeClock">000000</div>

        <div class="dashboard-grid">
            <div class="dashboard-column-left">
                <!-- Upcoming Tasks Card -->
                <section class="card">
                    <div class="section-header">
                        <h2>My Upcoming Tasks</h2>
                        <a href="tasks.php" class="view-all">View All Tasks</a>
                    </div>
                    <div class="card-content">
                        <?php if ($tasks_result && $tasks_result->num_rows > 0): ?>
                            <ul>
                                <?php while($task = $tasks_result->fetch_assoc()): ?>
                                    <li>
                                        <strong><?php echo htmlspecialchars($task['Task_Name']); ?></strong>
                                        <br>
                                        <small>Due: <?php echo htmlspecialchars(date('Y-m-d', strtotime($task['Task_End_Time']))); ?></small>
                                    </li>
                                <?php endwhile; ?>
                            </ul>
                        <?php else: ?>
                            <p>No upcoming tasks in the next 7 days.</p>
                        <?php endif; ?>
                    </div>
                </section>

                <!-- Quote of the Day Card -->
                <section class="card">
                    <div class="section-header">
                        <h2>Quote of the Day</h2>
                        <button id="quote-refresh" class="view-all" style="border:none; background:transparent; cursor:pointer;">🔄</button>
                    </div>
                    <div class="card-content">
                        <blockquote id="quote-text"></blockquote>
                        <cite id="quote-author"></cite>
                    </div>
                </section>
            </div>

            <div class="dashboard-column-right">
                <!-- Activity Feed Card -->
                <section class="card">
                    <div class="section-header">
                        <h2>Activity Feed</h2>
                        <div>
                            <a href="project.php" class="view-all">Projects</a> |
                            <a href="goal.php" class="view-all">Goals</a>
                        </div>
                    </div>
                    <div class="card-content">
                        <h3>Recent Projects</h3>
                        <div id="projectList">
                            <?php
                                if ($projects_result && $projects_result->num_rows > 0) {
                                    while($row = $projects_result->fetch_assoc()) {
                                        $progress = $row['Project_Status'] === 'Completed' ? 100 : ($row['Progress_Percent'] ?? 0);
                                        echo '<div class="task-card-summary">';
                                        echo '    <h4>' . htmlspecialchars($row['Title']) . '</h4>';
                                        echo '    <div class="progress-bar"><div class="progress-fill" style="width: ' . $progress . '%;"></div></div>';
                                        echo '    <span class="progress-text">' . $progress . '%</span>';
                                        echo '</div>';
                                    }
                                } else {
                                    echo '<p>You have not joined any projects yet. <a href="project.php">Find a project to join!</a></p>';
                                }
                            ?>
                        </div>

                        <h3 style="margin-top: 20px;">My Active Goals</h3>
                        <div>
                            <?php if ($goals_result && $goals_result->num_rows > 0): ?>
                                <ul>
                                    <?php while($goal = $goals_result->fetch_assoc()): ?>
                                        <li>
                                            <strong><?php echo htmlspecialchars($goal['Title']); ?></strong>
                                            <br>
                                            <small>
                                                <?php if (!empty($goal['Description'])): ?>
                                                    <?php echo htmlspecialchars(substr($goal['Description'], 0, 50)); ?>...
                                                <?php endif; ?>
                                                | Due: <?php echo htmlspecialchars(date('Y-m-d', strtotime($goal['Goal_End_Time']))); ?>
                                            </small>
                                        </li>
                                    <?php endwhile; ?>
                                </ul>
                            <?php else: ?>
                                <p>No active goals.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </main>

    <script src="JS/RTclock_Calendar.js"></script>
    <script src="JS/sidebar.js"></script>
    <script src="JS/user_avatar.js"></script>
    <script src="JS/notification_button.js"></script>
    <script src="JS/dashboard_quotes.js"></script>
</body>
</html>