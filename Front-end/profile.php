<?php
    // --- Database Connection and Session Start ---
    // This block manually defines the database connection and starts the session.
    // It appears to be a copy of 'Config/db_connect.php'.
    session_start();

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "rwdd";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // --- Authentication ---
    // Checks if a user is logged in. If not, they are redirected to the signup/login page.
    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
        header("location: signup.php");
        exit;
    }

    // --- Data Fetching ---
    // Fetches the profile data for the currently logged-in user to display on the page.
    $userId = $_SESSION['id'];
    $stmt = $conn->prepare("SELECT username, email, company, position FROM users WHERE user_ID = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    // --- Avatar Generation ---
    // Generates a Gravatar URL for the user's avatar.
    if ($user) {
        $email = trim(strtolower($user['email']));
        $md5_email = md5($email);
        $avatar_url = "https://www.gravatar.com/avatar/{$md5_email}?d=mp";
    } else {
        // Handles the edge case where a user session exists but the user is not in the database.
        $user = ['username' => 'Not Found', 'email' => '', 'company' => '', 'position' => ''];
        $avatar_url = 'https://via.placeholder.com/150';
    }

    $stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <!-- Linking Google Fonts for Icons -->
    <link rel="icon" href="Pictures/icon.png">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0"/>
    <link rel="stylesheet" href="CSS/dashboard.css">
    <link rel="stylesheet" href="CSS/profile.css">
</head>
<body>
    <!-- Mobile Sidebar Menu Button -->
    <button class="sidebar-menu-button">
        <span class="material-symbols-rounded">menu</span>
    </button>


    <aside class="sidebar">
        <!-- Sidebar Header -->
        <nav class="sidebar-header">
            <a href="<?php echo (isset($_SESSION['role']) && $_SESSION['role'] === 2) ? 'admin_dashboard.php' : 'dashboard.php'; ?>" class="header-logo">
                <img src="Pictures/logo.png" alt="TaskFlow">
            </a>
            <button class="sidebar-toggler">
                <span class="material-symbols-rounded">chevron_left</span>
            </button>
        </nav>

        <nav class="sidebar-nav">
            <!-- Primary Top Nav -->
            <ul class="nav-list primary-nav">
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 2): ?>
                    <li class="nav-item">
                        <a href="admin_dashboard.php" class="nav-link">
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
                <?php else: ?>
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
                <?php endif; ?>
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
      <h1>Profile</h1>
    </div>
    <div class="header-right">
      <div class="username" id="username">
        <p class="hello">
            Hello, <?php echo $_SESSION['username']; ?>
        </p>
      </div>
      <a href="profile.php">
        <img src="https://via.placeholder.com/150"  class="user-avatar" id="userAvatar">
      </a>
    </div>
  </header>

  <div class="profile-box">
    <div class="profile-header">
      <h2>User Profile</h2>
    </div>

    <!-- View Mode -->
    <div id="viewMode" class="viewMode">
        <div class="avatar-section">
            <img src="<?php echo htmlspecialchars($avatar_url); ?>" id="viewAvatar" class="avatar-preview">
        </div>
        <p><strong>Username:</strong> <span id="v-username"><?php echo htmlspecialchars($user['username']); ?></span></p>
        <p><strong>Email:</strong> <span id="v-email"><?php echo htmlspecialchars($user['email']); ?></span></p>
        <p><strong>Password:</strong> <span id="v-password">********</span></p>
        <p><strong>Company:</strong> <span id="v-company"><?php echo htmlspecialchars($user['company']); ?></span></p>
        <p><strong>Position:</strong> <span id="v-position"><?php echo htmlspecialchars($user['position']); ?></span></p>

        <div class="form-actions">
        <button class="edit-btn" onclick="switchToEdit()">Edit</button>
      </div>
    </div>

    <!-- Edit Mode -->
    <div id="editMode" class="hidden">
        <div class="form-group avatar-group">
            <img src="<?php echo htmlspecialchars($avatar_url); ?>" id="editAvatarPreview" class="avatar-preview">
        </div>
        <div class="form-group">
            <label>Username:</label>
            <input type="text" id="e-username" value="<?php echo htmlspecialchars($user['username']); ?>">
        </div>
        <div class="form-group">
            <label>Email:</label>
            <input type="email" id="e-email" value="<?php echo htmlspecialchars($user['email']); ?>">
        </div>
        <div class="form-group">
            <label>Password:</label>
            <input type="password" id="e-password" placeholder="Leave blank to keep current password">
        </div>
        <div class="form-group">
            <label>Company:</label>
            <input type="text" id="e-company" value="<?php echo htmlspecialchars($user['company']); ?>">
        </div>
        <div class="form-group">
            <label>Position:</label>
            <input type="text" id="e-position" value="<?php echo htmlspecialchars($user['position']); ?>">
        </div>

      <div class="form-actions">
        <button class="save-btn" onclick="saveChanges()">Save</button>
        <button class="cancel-btn" onclick="cancelEdit()">Cancel</button>
      </div>
    </div>
  </div>
    

    <script src="JS/sidebar.js"></script>
    <script src="JS/notification_button.js"></script>
    <script src="JS/profile.js"></script>
</body>
</html>