<?php
    // Includes the database connection and session start.
    include 'Config/db_connect.php';

    // --- Authentication and Authorization ---
    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
        header("location: signup.php");
        exit;
    }
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 2) {
        header("location: dashboard.php"); 
        exit;
    }

    // --- Data Fetching ---
    // KPI Stats
    $total_projects = $conn->query("SELECT COUNT(Project_ID) as total FROM projects")->fetch_assoc()['total'];
    $total_members = $conn->query("SELECT COUNT(User_ID) as total FROM users WHERE Role_ID = 1")->fetch_assoc()['total'];
    $open_tasks = $conn->query("SELECT COUNT(Task_ID) as total FROM tasks")->fetch_assoc()['total'];
    $completed_tasks = $conn->query("SELECT COUNT(Task_ID) as total FROM tasks WHERE Status = 'Done'")->fetch_assoc()['total'];

    // Recent Projects
    $projects_result = $conn->query("SELECT * FROM projects ORDER BY Project_Start_Date DESC LIMIT 5");

    // Recent Members
    $members_result = $conn->query("SELECT Username, Email, account_creation_date FROM users WHERE Role_ID = 1 ORDER BY account_creation_date DESC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
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
            <a href="admin_dashboard.php" class="header-logo">
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
                    <a href="admin_dashboard.php" class="nav-link active">
                        <span class="material-symbols-rounded">dashboard</span>
                        <span class="nav-label">Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="admin_project.php" class="nav-link">
                        <span class="material-symbols-rounded">task</span>
                        <span class="nav-label">Project</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="admin_member.php" class="nav-link">
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
          <h1>Admin Dashboard</h1>
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
        <div class="top-dashboard-row">
            <div class="real-time-clock" id="realTimeClock">000000</div>
            <!-- Quick Actions (moved to top right) -->
            <div class="quick-actions-top-right">
                <a href="admin_project.php" class="action-button">+ Create New Project</a>
                <a href="admin_member.php" class="action-button">+ Add New Member</a>
            </div>
        </div>

        <!-- KPI Stats -->
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total Projects</h3>
                <p><?php echo $total_projects; ?></p>
            </div>
            <div class="stat-card">
                <h3>Total Members</h3>
                <p><?php echo $total_members; ?></p>
            </div>
            <div class="stat-card">
                <h3>Open Tasks</h3>
                <p><?php echo $open_tasks; ?></p>
            </div>
            <div class="stat-card">
                <h3>Completed Tasks</h3>
                <p><?php echo $completed_tasks; ?></p>
            </div>
        </div>

        <!-- Main Grid -->
        <div class="dashboard-grid">
            <div class="dashboard-column-left">
                <!-- Recent Projects Card -->
                <section class="card">
                    <div class="section-header">
                        <h2>Recent Projects</h2>
                        <a href="admin_project.php" class="view-all">Manage All Projects</a>
                    </div>
                    <div id="projectList" class="card-content">
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
                                echo '<p>No projects found.</p>';
                            }
                        ?>
                    </div>
                </section>
            </div>
            <div class="dashboard-column-right">
                <!-- Recent Members Card -->
                <section class="card">
                    <div class="section-header">
                        <h2>Recent Members</h2>
                        <a href="admin_member.php" class="view-all">Manage All Members</a>
                    </div>
                    <div class="card-content">
                        <?php if ($members_result && $members_result->num_rows > 0): ?>
                            <ul>
                                <?php while($member = $members_result->fetch_assoc()): ?>
                                    <li>
                                        <strong><?php echo htmlspecialchars($member['Username']); ?></strong>
                                        <br>
                                        <small>Email: <?php echo htmlspecialchars($member['Email']); ?> | Joined: <?php echo htmlspecialchars(date('Y-m-d', strtotime($member['account_creation_date']))); ?></small>
                                    </li>
                                <?php endwhile; ?>
                            </ul>
                        <?php else: ?>
                            <p>No new members found.</p>
                        <?php endif; ?>
                    </div>
                </section>
            </div>
        </div>
    </main>

    <script src="JS/RTclock_Calendar.js"></script>
    <script src="JS/sidebar.js"></script>
    <script src="JS/user_avatar.js"></script>
    <script src="JS/notification_button.js"></script>
</body>
</html>