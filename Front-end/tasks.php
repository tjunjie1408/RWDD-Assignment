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

    // Security Check: Ensure the user is a member of this project.
    $user_id = $_SESSION['id'];
    $check_stmt = $conn->prepare("SELECT Member_ID FROM project_members WHERE Project_ID = ? AND User_ID = ?");
    $check_stmt->bind_param("ii", $project_id, $user_id);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows === 0) {
        // User is not a member, redirect them.
        header("location: project.php?error=not_a_member");
        exit;
    }
    $check_stmt->close();

    // Fetch project details to display on the page
    $project_stmt = $conn->prepare("SELECT Title, Description FROM projects WHERE Project_ID = ?");
    $project_stmt->bind_param("i", $project_id);
    $project_stmt->execute();
    $project_result = $project_stmt->get_result();
    $project = $project_result->fetch_assoc();
    $project_stmt->close();

    // Determine if the user is an admin
    $is_admin = isset($_SESSION['role']) && $_SESSION['role'] === 2;

    // Fetch tasks based on user role
    $sql = "SELECT t.*, f.File_ID, f.File_Name FROM tasks t LEFT JOIN files f ON t.Task_ID = f.Task_ID WHERE t.Project_ID = ?";
    if (!$is_admin) {
        $sql .= " AND t.User_ID = ?";
    }
    $sql .= " ORDER BY t.Task_Created_Date DESC";

    $task_stmt = $conn->prepare($sql);

    if ($is_admin) {
        $task_stmt->bind_param("i", $project_id);
    } else {
        $task_stmt->bind_param("ii", $project_id, $user_id);
    }

    $task_stmt->execute();
    $tasks_result = $task_stmt->get_result();
    $tasks = [];
    while ($row = $tasks_result->fetch_assoc()) {
        $tasks[] = $row;
    }
    $task_stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project Tasks</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0"/>
    <link rel="stylesheet" href="CSS/dashboard.css">
    <link rel="stylesheet" href="CSS/project.css"> 
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
        <div class="project-header-container card">
            <h2><?php echo htmlspecialchars($project['Title']); ?></h2>
            <p><?php echo htmlspecialchars($project['Description']); ?></p>
        </div>

        <div class="actions">
            <a href="project.php" class="primary">&larr; Back to All Projects</a>
            <?php if ($is_admin): ?>
                <button id="newTaskBtn" class="primary">+ New Task</button>
            <?php endif; ?>
        </div>

        <div id="task-list-container" class="list-view">
            <?php if (count($tasks) > 0): ?>
                <?php foreach ($tasks as $task): ?>
                    <div class="task-card <?php echo ($task['Status'] === 'Done') ? 'completed' : ''; ?>" data-task-id="<?php echo $task['Task_ID']; ?>" data-title="<?php echo htmlspecialchars($task['Title']); ?>" data-description="<?php echo htmlspecialchars($task['Description']); ?>" data-end-date="<?php echo $task['Task_End_Time']; ?>">
                        <div class="task-card-header">
                            <form action="Config/update_task_status.php" method="POST" class="task-status-form" onsubmit="return confirm('Are you sure you want to mark this task as complete?');">
                                <input type="hidden" name="taskId" value="<?php echo $task['Task_ID']; ?>">
                                <input type="hidden" name="projectId" value="<?php echo $project_id; ?>">
                                <input type="checkbox" name="status" class="task-checkbox" <?php echo ($task['Status'] === 'Done') ? 'checked' : ''; ?> onchange="this.form.submit()">
                            </form>
                            <h4 class="task-title"><?php echo htmlspecialchars($task['Title']); ?></h4>
                            <div>
                                <?php if ($is_admin): ?>
                                    <button class="primary small-btn edit-task-btn">Edit</button>
                                    <button class="danger small-btn delete-task-btn">Delete</button>
                                <?php endif; ?>
                                <button class="primary small-btn view-details-btn">Details</button>
                            </div>
                        </div>
                        <div class="task-card-body">
                            <p><strong>Due Date:</strong> <?php echo htmlspecialchars($task['Task_End_Time'] ?? 'No due date'); ?></p>
                        </div>
                        <div class="task-card-footer">
                            <div class="task-actions">
                                <button class="btn-upload-file" data-task-id="<?php echo $task['Task_ID']; ?>">📎 Upload File</button>
                                <?php if (!empty($task['File_ID'])): ?>
                                    <a href="Config/download_file.php?file_id=<?php echo $task['File_ID']; ?>" class="download-link">⬇ Download "<?php echo htmlspecialchars($task['File_Name']); ?>"</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No tasks have been created for this project yet.</p>
            <?php endif; ?>
        </div>

        <!-- Create/Edit Task Modal -->
        <div id="taskModal" class="modal" aria-hidden="true">
            <div class="modal-content">
                <h3 id="taskModalTitle">New Task</h3>
                <form id="taskForm" action="Config/create_task.php" method="POST">
                    <input type="hidden" id="taskId" name="taskId">
                    <input type="hidden" id="projectId" name="projectId" value="<?php echo $project_id; ?>">

                    <label for="taskTitle">Title</label>
                    <input id="taskTitle" type="text" name="title" required>
                    
                    <label for="taskDescription">Description</label>
                    <textarea id="taskDescription" name="description"></textarea>
                    
                    <label for="taskEndDate">End Date</label>
                    <input id="taskEndDate" type="date" name="endDate" required>

                    <label for="assignee">Assign To</label>
                    <select id="assignee" name="assigneeId">
                        <!-- Options will be populated by JS -->
                    </select>
                    
                    <div class="modal-actions">
                        <button type="button" id="cancelTaskBtn">Cancel</button>
                        <button type="submit" class="primary">Save Task</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- File Upload Modal -->
        <div id="fileUploadModal" class="modal" aria-hidden="true">
            <div class="modal-content">
                <h3>Upload File for Task</h3>
                <form id="fileUploadForm" action="Config/upload_file.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" id="uploadTaskId" name="taskId">
                    <input type="hidden" name="projectId" value="<?php echo $project_id; ?>">

                    <label for="fileInput">Select File</label>
                    <input type="file" id="fileInput" name="file" required>

                    <div class="modal-actions">
                        <button type="button" id="cancelUploadBtn">Cancel</button>
                        <button type="submit" class="primary">Upload</button>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <script src="JS/sidebar.js"></script>
    <script src="JS/user_avatar.js"></script>
    <script src="JS/tasks.js"></script>
</body>
</html>