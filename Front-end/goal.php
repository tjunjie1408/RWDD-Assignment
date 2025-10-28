<?php
    include 'Config/db_connect.php';

    // Check if the user is logged in
    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
        header("location: signup.php");
        exit;
    }

    $user_id = $_SESSION['id'];

    // Fetch goals for the current user
    $sql = "SELECT * FROM goals WHERE User_ID = ? ORDER BY Goal_Start_Time DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $goals = [];
    while ($row = $result->fetch_assoc()) {
        $goals[] = $row;
    }
    $stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Goals</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0"/>
    <link rel="stylesheet" href="CSS/dashboard.css">
    <link rel="stylesheet" href="CSS/project.css"> <!-- Reusing project.css for similar card layout -->
</head>
<body>
    <button class="sidebar-menu-button">
        <span class="material-symbols-rounded">menu</span>
    </button>

    <aside class="sidebar">
        <nav class="sidebar-header">
            <a href="<?php echo (isset($_SESSION['role']) && $_SESSION['role'] === 2) ? 'admin_dashboard.php' : 'dashboard.php'; ?>" class="header-logo">
                <img src="Pictures/logo.png" alt="TaskFlow">
            </a>
            <button class="sidebar-toggler"><span class="material-symbols-rounded">chevron_left</span></button>
        </nav>
        <nav class="sidebar-nav">
            <ul class="nav-list primary-nav">
                 <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 2): ?>
                    <li class="nav-item"><a href="admin_dashboard.php" class="nav-link"><span class="material-symbols-rounded">dashboard</span><span class="nav-label">Dashboard</span></a></li>
                    <li class="nav-item"><a href="admin_project.php" class="nav-link"><span class="material-symbols-rounded">task</span><span class="nav-label">Project</span></a></li>
                    <li class="nav-item"><a href="admin_member.php" class="nav-link"><span class="material-symbols-rounded">group</span><span class="nav-label">Member</span></a></li>
                <?php else: ?>
                    <li class="nav-item"><a href="dashboard.php" class="nav-link"><span class="material-symbols-rounded">dashboard</span><span class="nav-label">Dashboard</span></a></li>
                    <li class="nav-item"><a href="project.php" class="nav-link"><span class="material-symbols-rounded">task</span><span class="nav-label">Project</span></a></li>
                    <li class="nav-item"><a href="member.php" class="nav-link"><span class="material-symbols-rounded">group</span><span class="nav-label">Member</span></a></li>
                <?php endif; ?>
                <li class="nav-item"><a href="analysis.php" class="nav-link"><span class="material-symbols-rounded">bar_chart_4_bars</span><span class="nav-label">Report Analysis</span></a></li>
                <li class="nav-item"><a href="goal.php" class="nav-link active"><span class="material-symbols-rounded">task_alt</span><span class="nav-label">Goal</span></a></li>
            </ul>
            <ul class="nav-list secondary-nav">
                <li class="nav-item"><a href="#" class="nav-link"><span class="material-symbols-rounded">help</span><span class="nav-label">Support</span></a></li>
                <li class="nav-item"><a href="Config/logout.php" class="nav-link"><span class="material-symbols-rounded">logout</span><span class="nav-label">Sign Out</span></a></li>
            </ul>
        </nav>
    </aside>

    <header>
        <div class="header-left"><h1>Goals</h1></div>
        <div class="header-right">
            <div class="username"><p class="hello">Hello, <?php echo htmlspecialchars($_SESSION['username']); ?></p></div>
            <a href="profile.php"><img src="https://via.placeholder.com/150" class="user-avatar" id="userAvatar"></a>
        </div>
    </header>

    <main class="project-content">
        <section class="card">
            <div class="section-header">
                <h2>My Goals</h2>
                <div class="header-actions">
                    <a href="goal_calendar.php" id="viewCalendarBtn" class="icon-btn" title="View Calendar">
                        <span class="material-symbols-rounded">calendar_month</span>
                    </a>
                    <button id="newGoalBtn" class="primary">+ New Goal</button>
                </div>
            </div>
            <div id="goalList">
                <?php if (count($goals) > 0): ?>
                    <?php foreach ($goals as $goal): ?>
                        <div class="task-card" data-goal-id="<?php echo $goal['Goal_ID']; ?>">
                            <div class="task-header">
                                <h4><?php echo htmlspecialchars($goal['Title']); ?></h4>
                                <span class="status-tag <?php echo strtolower(str_replace(' ', '-', $goal['Status'])); ?>"><?php echo htmlspecialchars($goal['Status']); ?></span>
                            </div>
                            <p class="task-desc"><?php echo htmlspecialchars($goal['Description']); ?></p>
                            <div class="task-footer">
                                <span class="task-date">📅 <?php echo $goal['Goal_Start_Time']; ?> to <?php echo $goal['Goal_End_Time']; ?></span>
                                <div class="project-card-actions">
                                    <button class="primary small-btn edit-goal-btn">Edit</button>
                                    <button class="danger small-btn delete-goal-btn">Delete</button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>You haven't set any goals yet. Click "+ New Goal" to get started!</p>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <!-- Add/Edit Goal Modal -->
    <div id="goalModal" class="modal" aria-hidden="true">
        <div class="modal-content">
            <h3 id="goalModalTitle">New Goal</h3>
            <form id="goalForm" method="POST">
                <input type="hidden" id="goalId" name="goalId">
                <label for="goalTitle">Title</label>
                <input id="goalTitle" type="text" name="title" required>

                <label for="goalDescription">Description</label>
                <textarea id="goalDescription" name="description"></textarea>

                <label for="goalStartDate">Start Date & Time</label>
                <input id="goalStartDate" type="datetime-local" name="startDate" required>

                <label for="goalEndDate">End Date & Time</label>
                <input id="goalEndDate" type="datetime-local" name="endDate" required>

                <label for="goalStatus">Status</label>
                <select id="goalStatus" name="status">
                    <option value="Not Started">Not Started</option>
                    <option value="In Progress">In Progress</option>
                    <option value="Completed">Completed</option>
                    <option value="On Hold">On Hold</option>
                </select>

                <div class="modal-actions">
                    <button type="button" id="cancelGoalBtn">Cancel</button>
                    <button type="submit" class="primary">Save Goal</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const allGoals = <?php echo json_encode($goals); ?>;
    </script>
    <script src="JS/sidebar.js"></script>
    <script src="JS/user_avatar.js"></script>
    <script src="JS/goals.js"></script>
</body>
</html>