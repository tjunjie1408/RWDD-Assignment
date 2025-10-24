<?php
    include 'Config/db_connect.php';

    // Check if the user is logged in, if not then redirect to login page
    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
        header("location: signup.php");
        exit;
    }

    // Check if the logged-in user has admin role (assuming Role_ID 2 is admin)
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 2) {
        header("location: project.php"); // Redirect non-admins to the regular project page
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
                    <a href="admin_dashboard.php" class="nav-link">
                        <span class="material-symbols-rounded">dashboard</span>
                        <span class="nav-label">Dashboard</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="admin_project.php" class="nav-link active">
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
            <h1>Project Management</h1>
        </div>
        <div class="header-right">
            <div class="username" id="username">
                <p class="hello">
                    Hello, Admin <?php echo htmlspecialchars($_SESSION['username']); ?>
                </p>
            </div>
            <a href="profile.php">
                <img src="https://via.placeholder.com/150" class="user-avatar" id="userAvatar">
            </a>
        </div>
    </header>

    <main class="project-content">
        <section class="card">
            <div class="section-header">
                <h2>All Projects</h2>
                <div class="project-actions">
                    <input id="searchInput" type="text" placeholder="Search projects...">
                    <button id="newProjectBtn" class="primary">+ New Project</button>
                </div>
            </div>
            <div id="projectList">
                <!-- Project cards will be injected here by project.js -->
            </div>
        </section>
    </main>

    <!-- Create Project Modal -->
    <div id="projectModal" class="modal" aria-hidden="true">
        <div class="modal-content">
            <h3>Create New Project</h3>
            <form id="projectForm">
                <label for="projectTitle">Title</label>
                <input id="projectTitle" type="text" name="title" placeholder="e.g., Website Redesign" required>
                
                <label for="projectDescription">Description</label>
                <textarea id="projectDescription" name="description" placeholder="A short description of the project..."></textarea>
                
                <label for="projectStartDate">Start Date</label>
                <input id="projectStartDate" type="date" name="startDate" required>
                
                <label for="projectEndDate">End Date</label>
                <input id="projectEndDate" type="date" name="endDate" required>
                
                <div class="modal-actions">
                    <button type="button" id="cancelProjectBtn">Cancel</button>
                    <button type="submit" class="primary">Create Project</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Project Modal -->
    <div id="editProjectModal" class="modal" aria-hidden="true">
        <div class="modal-content">
            <h3>Edit Project</h3>
            <form id="editProjectForm">
                <input type="hidden" id="editProjectId" name="projectId">
                <label for="editProjectTitle">Title</label>
                <input id="editProjectTitle" type="text" name="title" required>
                
                <label for="editProjectDescription">Description</label>
                <textarea id="editProjectDescription" name="description"></textarea>
                
                <label for="editProjectStartDate">Start Date</label>
                <input id="editProjectStartDate" type="date" name="startDate" required>
                
                <label for="editProjectEndDate">End Date</label>
                <input id="editProjectEndDate" type="date" name="endDate" required>
                
                <label for="editProjectStatus">Status</label>
                <select id="editProjectStatus" name="status">
                    <option value="Not Started">Not Started</option>
                    <option value="In Progress">In Progress</option>
                    <option value="Completed">Completed</option>
                    <option value="On Hold">On Hold</option>
                </select>

                <div class="modal-actions">
                    <button type="button" id="cancelEditProjectBtn">Cancel</button>
                    <button type="submit" class="primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>


    <script src="/RWDD-Assignment/Front-end/JS/sidebar.js"></script>
    <script src="/RWDD-Assignment/Front-end/JS/project.js"></script>
</body>
</html>
