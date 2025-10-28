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
    // Fetches the 5 most recent projects that the currently logged-in user is a member of.
    $user_id = $_SESSION['id'];
    // The query joins the 'projects' and 'project_members' tables to filter projects by the user's ID.
    $projects_result = $conn->query("SELECT p.* FROM projects p JOIN project_members pm ON p.Project_ID = pm.Project_ID WHERE pm.User_ID = {$user_id} ORDER BY p.Project_Start_Date DESC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <!-- Linking Google Fonts for Icons -->
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

    <main class="project-content"> <!-- Use class from project.css for layout -->
        <div class="real-time-clock" id="realTimeClock">000000</div>

        <section class="card">
            <div class="section-header">
                <h2>My Projects</h2>
                <a href="project.php" class="view-all">View All</a>
            </div>
            <div id="projectList">
                <?php
                    if ($projects_result && $projects_result->num_rows > 0) {
                        while($row = $projects_result->fetch_assoc()) {
                            $progress = $row['Project_Status'] === 'Completed' ? 100 : ($row['Progress_Percent'] ?? 0);
                            echo '<div class="task-card">';
                            echo '    <div class="task-header">';
                            echo '        <h4>' . htmlspecialchars($row['Title']) . '</h4>';
                            echo '    </div>';
                            echo '    <p class="task-desc">' . (htmlspecialchars($row['Description']) ?: 'No description.') . '</p>';
                            echo '    <div class="task-footer">';
                            echo '        <span class="task-date">📅 ' . $row['Project_Start_Date'] . ' to ' . $row['Project_End_Date'] . '</span>';
                            echo '        <div class="progress-bar"><div class="progress-fill" style="width: ' . $progress . '%;"></div></div>';
                            echo '        <span class="progress-text">' . $progress . '%</span>';
                            echo '    </div>';
                            echo '</div>';
                        }
                    } else {
                        echo '<p>You have not joined any projects yet. <a href="project.php">Find a project to join!</a></p>';
                    }
                ?>
            </div>
        </section>
    </main>

    <script src="JS/RTclock_Calendar.js"></script>
    <script src="JS/sidebar.js"></script>
    <script src="JS/user_avatar.js"></script>
    <script src="JS/notification_button.js"></script>
</body>
</html>