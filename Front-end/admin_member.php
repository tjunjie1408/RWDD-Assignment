<?php
    // Includes the database connection and session start.
    include 'Config/db_connect.php';

    // --- Authentication and Authorization ---
    // Checks if a user is logged in. If not, they are redirected to the signup/login page.
    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
        header("location: signup.php");
        exit;
    }

    // Checks if the logged-in user has an admin role (Role_ID 2).
    // If they are not an admin, they are redirected to the regular user dashboard, as this page is for admins only.
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 2) {
        header("location: dashboard.php");
        exit;
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Member</title>
    <!-- Linking Google Fonts for Icons -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0"/>
    <link rel="stylesheet" href="CSS/member.css"> 
    <link rel="stylesheet" href="CSS/dashboard.css">
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
                    <a href="admin_project.php" class="nav-link">
                        <span class="material-symbols-rounded">task</span>
                        <span class="nav-label">Project</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="admin_member.php" class="nav-link active">
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
      <h1>Member Management</h1>
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

    <main class="membercontent">
    <section class="team-section">
      <div class="team-header">
        <div class="team-controls">
          <input type="text" id="searchMember" placeholder="Search team or member">
          <button id="addMemberBtn">+ Add Member</button>
        </div>
      </div>

      <h3>People you work with</h3>
      <div id="memberList" class="member-list">
        <!-- 成员卡片由 JS 渲染 -->
      </div>
    </section>
  </main>

  <!-- Add/Edit Member Modal -->
  <div id="memberFormModal" class="modal">
    <div class="member-modal-content">
      <h2 id="memberFormModalTitle">Add Member</h2>
      
      <button class="member-close">&times;</button>
      <form id="memberForm">
        <input type="hidden" id="memberId" name="memberId">
        <label for="memberName">Username</label>
        <input type="text" id="memberName" name="username" required>
        
        <label for="memberEmail">Email</label>
        <input type="email" id="memberEmail" name="email" required>
        
        <label for="memberPassword">Password (leave blank to keep current)</label>
        <input type="password" id="memberPassword" name="password">
        
        <label for="memberCompany">Company</label>
        <input type="text" id="memberCompany" name="company" required>
        
        <label for="memberPosition">Position</label>
        <input type="text" id="memberPosition" name="position" required>
        
        <button type="submit" id="memberFormSubmitBtn">Save</button>
      </form>
    </div>
  </div>

  <!-- View Member Details Modal -->
  <div id="viewMemberModal" class="member modal">
    <div class="member-modal-content">
      <span class="member-close">&times;</span>
      <h2>Member Details</h2>
      <div id="memberDetails"></div>
      <div class="modal-actions">
        <button id="editMemberBtn" class="action-button">Edit Member</button>
        <button id="deleteMemberBtn" class="action-button delete-button">Delete Member</button>
      </div>
    </div>
  </div>

    <!-- Confirmation Modal for Deletion -->
    <div id="confirmDeleteModal" class="modal">
        <div class="modal-content">
            <h2>Confirm Deletion</h2>
            <p>Are you sure you want to delete this member?</p>
            <button id="confirmDeleteYes">Yes</button>
            <button id="confirmDeleteNo">No</button>
        </div>
    </div>

    <script src="JS/admin_member.js"></script>
    <script src="JS/sidebar.js"></script>
    <script src="JS/user_avatar.js"></script>
    <script src="JS/notification_button.js"></script>
</body>
</html>