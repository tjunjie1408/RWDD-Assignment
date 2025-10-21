<?php
    include 'Config/db_connect.php';

    // Check if the user is logged in, if not then redirect to login page
    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
        header("location: signup.php");
        exit;
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project</title>
    <!-- Linking Google Fonts for Icons -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0"/>
    <link rel="stylesheet" href="/RWDD-Assignment/Front-end/CSS/dashboard.css">
    <link rel="stylesheet" href="/RWDD-Assignment/Front-end/CSS/project.css">
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
                    <a href="#" class="nav-link">
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
    <div class="actions">
        <div class="search">
            <input id="searchInput" type="text" placeholder="Search tasks...">
        </div>
        <button id="newTaskBtn" class="primary">+ New Task</button>
    </div>

        <!-- Filters and Sorting -->
    <div class="filter-group">
        <label>Filter:</label>
        <select id="statusFilter">
            <option value="all">All Tasks</option>
            <option value="completed">Completed</option>
            <option value="overdue">Overdue</option>
            <option value="progress">Progress</option>
        </select>
    </div>

        <!-- Kanban View -->
        <div id="kanbanView" class="kanban-view">
            <div class="kanban-columns">
                <div class="kanban-column">
                    <div class="kanban-header">
                        <span class="kanban-title">In Progress</span>
                        <span id="inProgressCount" class="kanban-count">0</span>
                    </div>
                </div>
                <div class="kanban-column">
                    <div class="kanban-header">
                        <span class="kanban-title">Completed</span>
                        <span id="completedCount" class="kanban-count">0</span>
                </div>
                </div>
                <div class="kanban-column">
                    <div class="kanban-header">
                        <span class="kanban-title">Overdue</span>
                        <span id="overdueCount" class="kanban-count">0</span>
                    </div>
                </div>
            </div>
        </div>
    </main>

        <!-- List View -->
    <div id="listView" class="list-view">
        <section class="card">
            <div class="section-header">
                <h2>All Tasks</h2>
                <a class="view-all" href="#">View All</a>
            </div>
            <div id="taskGroups">
                <!-- Task groups will be injected here -->
            </div>
        </section>
    </div>

    <div id="modal" class="modal" aria-hidden="true">
        <div class="modal-content">
            <h3>Create Task</h3>
            <label>Title
                <input id="taskTitle" type="text" placeholder="e.g. Proposal for new project">
            </label>
            <label>Category
                <select id="taskCategory">
                    <option>Development</option>
                    <option>Design</option>
                    <option>Meetings</option>
                    <option>Research</option>
                </select>
            </label>
            <label>Due Date
                <input id="taskDate" type="date">
            </label>
            <label>Progress (%)
                <input id="taskProgress" type="number" min="0" max="100" value="0" placeholder="0">
            </label>
            <label>Description
                <textarea id="taskDescription" placeholder="Task description..."></textarea>
            </label>
            <div class="modal-actions">
                <button id="cancelBtn">Cancel</button>
                <button id="createBtn" class="primary">Create</button>
            </div>
        </div>
    </div>

    <!-- Task Detail Modal -->
    <div id="taskDetailModal" class="modal" aria-hidden="true">
    <div class="modal-content">
        <h3 id="detailTitle">Task Title</h3>
        <p><strong>Category:</strong> <span id="detailCategory"></span></p>
        <p><strong>Due Date:</strong> <span id="detailDate"></span></p>
        <p><strong>Progress:</strong> <span id="detailProgress"></span>%</p>
        <p><strong>Description:</strong></p>
        <p id="detailDescription"></p>
        <p><strong>Members:</strong></p>
        <ul id="detailMembers"></ul>

        <!-- 提交工作内容 -->
        <div class="submission-section">
        <h4>Submit Your Work</h4>
        <form id="submissionForm">
            <label for="submissionDescription">Description:</label>
            <textarea id="submissionDescription" rows="4" placeholder="Describe your work..." required></textarea>

            <label for="submissionFile">Upload File:</label>
            <input type="file" id="submissionFile" required>

            <button type="submit">Submit</button>
        </form>
        </div>

        <div class="modal-actions">
        <button id="closeDetailBtn">Close</button>
        </div>
    </div>
    </div>

</main>

    <script src="/RWDD-Assignment/Front-end/JS/dashboard.js"></script>
    <script src="/RWDD-Assignment/Front-end/JS/sidebar.js"></script>
    <script src="/RWDD-Assignment/Front-end/JS/notification_button.js"></script>
    <script src="/RWDD-Assignment/Front-end/JS/user_avatar.js"></script>
    <script src="/RWDD-Assignment/Front-end/JS/project.js"></script>
</body>
</html>