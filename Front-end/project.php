<?php
    // Includes the database connection and session start.
    include 'Config/db_connect.php';

    // --- Authentication ---
    // Checks if a user is logged in.
    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
        header("location: signup.php");
        exit;
    }

    // --- Data Fetching ---
    // Gets the current user's ID from the session.
    $current_user_id = $_SESSION['id'];

    // Fetches all projects from the database.
    // It uses a LEFT JOIN with the 'project_members' table to determine if the current user
    // is a member of each project. The result is a boolean flag called 'is_member'.
    $sql = "SELECT p.Project_ID, p.Title, p.Description, p.Project_Start_Date, p.Project_End_Date, p.Project_Status, p.Progress_Percent,
                   (pm.User_ID IS NOT NULL) AS is_member
            FROM projects p
            LEFT JOIN project_members pm ON p.Project_ID = pm.Project_ID AND pm.User_ID = ?
            ORDER BY p.Project_Start_Date DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $current_user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Stores the fetched projects in an array for display.
    $projects = [];
    while ($row = $result->fetch_assoc()) {
        $projects[] = $row;
    }
    $stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project</title>
    <!-- Linking Google Fonts for Icons -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0"/>
    <link rel="stylesheet" href="CSS/dashboard.css">
    <link rel="stylesheet" href="CSS/project.css">
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
                    <a href="dashboard.php" class="nav-link">
                        <span class="material-symbols-rounded">dashboard</span>
                        <span class="nav-label">Dashboard</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="project.php" class="nav-link active">
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
      <h1>Project</h1>
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

    <main class="project-content">
        <?php if(isset($_GET['error']) && $_GET['error'] == 'not_a_member'): ?>
            <div class="message error">You can only view tasks for projects you are a member of.</div>
        <?php endif; ?>

        <section class="card">
            <div class="section-header">
                <h2>Available Projects</h2>
            </div>
            <div id="projectList">
                <?php
                    if (count($projects) > 0) {
                        foreach ($projects as $row) {
                            $progress = $row['Project_Status'] === 'Completed' ? 100 : $row['Progress_Percent'];
                            $is_member = (bool)$row['is_member'];

                            // Define the link and actions based on membership
                            $card_link = $is_member ? 'href="tasks.php?project_id=' . $row['Project_ID'] . '"' : 'style="cursor:pointer;" class="project-details-trigger"';

                            echo '<div class="task-card" data-project-id="' . $row['Project_ID'] . '" data-title="' . htmlspecialchars($row['Title']) . '" data-description="' . htmlspecialchars($row['Description']) . '" data-start-date="' . $row['Project_Start_Date'] . '" data-end-date="' . $row['Project_End_Date'] . '">';
                            echo '    <a ' . $card_link . ' style="text-decoration: none;">';
                            echo '        <div class="task-header">';
                            echo '            <h4>' . htmlspecialchars($row['Title']) . '</h4>';
                            if ($is_member) {
                                echo '            <span class="member-badge">You are a member</span>';
                            }
                            echo '        </div>';
                            echo '        <p class="task-desc">' . (htmlspecialchars($row['Description']) ?: 'No description.') . '</p>';
                            echo '        <div class="task-footer">';
                            echo '            <span class="task-date">📅 ' . $row['Project_Start_Date'] . ' to ' . $row['Project_End_Date'] . '</span>';
                            echo '            <div class="progress-bar"><div class="progress-fill" style="width: ' . $progress . '%;"></div></div>';
                            echo '            <span class="progress-text">' . $progress . '%</span>';
                            echo '        </div>';
                            echo '    </a>';
                            echo '</div>';
                        }
                    } else {
                        echo '<p>No projects found.</p>';
                    }
                ?>
            </div>
        </section>
    </main>

    <!-- Project Details Modal -->
    <div id="projectDetailsModal" class="modal" aria-hidden="true">
        <div class="modal-content">
            <h3 id="projectDetailsTitle"></h3>
            <p><strong>Description:</strong></p>
            <p id="projectDetailsDescription"></p>
            <p><strong>Dates:</strong> <span id="projectDetailsDates"></span></p>
            <div class="modal-actions">
                <button type="button" id="closeProjectDetailsBtn">Close</button>
            </div>
        </div>
    </div>

    <script src="JS/sidebar.js"></script>
    <script src="JS/user_avatar.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const modal = document.getElementById('projectDetailsModal');
            const closeBtn = document.getElementById('closeProjectDetailsBtn');

            document.querySelectorAll('.project-details-trigger').forEach(trigger => {
                trigger.addEventListener('click', () => {
                    const card = trigger.closest('.task-card');
                    document.getElementById('projectDetailsTitle').textContent = card.dataset.title;
                    document.getElementById('projectDetailsDescription').textContent = card.dataset.description;
                    document.getElementById('projectDetailsDates').textContent = `${card.dataset.startDate} to ${card.dataset.endDate}`;
                    modal.classList.add('show');
                });
            });

            const closeModal = () => modal.classList.remove('show');
            closeBtn.addEventListener('click', closeModal);
            window.addEventListener('click', (event) => {
                if (event.target === modal) {
                    closeModal();
                }
            });
        });
    </script>
</body>
</html>