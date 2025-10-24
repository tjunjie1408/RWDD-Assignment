# Project Specification

This document outlines the requirements for the project, categorized into subjective (user-focused) and technical requirements.

## 1. Subjective Requirements

These requirements describe the user-facing features and functionality of the application.

### 1.1. User Roles

- **Admin (Manager):** Has full control over the system's core components like projects and members.
- **User (Employee):** Has limited access, primarily focused on participating in projects and managing their own tasks.

### 1.2. Project Management

- **Admin:**
    - Can create new projects.
    - Can modify existing project details.
    - Can delete projects.
- **User:**
    - Can join an existing project.
    - Interacts with tasks within a project (see Task Management below).

#### 1.2.1. Task Management (User View)

This is the primary interface for users to manage their work.

- **Task List:** Users will see a list of tasks assigned specifically to them.
- **Task Card:** Each task will display the project name, task title, who assigned it, and the due date.
- **Status & Filtering:**
    - Tasks will have a status of "Open" or "Done".
    - Users can filter their tasks by project or status.
- **Task Completion:**
    - Each task must have a **checkbox**.
    - Clicking the checkbox marks the task as "Done" and immediately notifies the backend.
    - The task's appearance will change (e.g., greyed out with a strikethrough).
- **File Operations:**
    - Each task card will have an icon (e.g., 📎) to upload files. This will open a modal for file selection.
    - If a file is already associated with a task, a download icon (⬇) will be displayed to allow any project member to download it.

### 1.3. Member Management

- **Admin:**
    - Can modify the details of any member.
    - Can delete any member from the system.
- **User:**
    - Can view a list of other members.
    - Can click on a member's profile to view their details.

### 1.4. Goals & Analysis

- This section is accessible to and functions identically for both **Admin** and **User** roles.

### 1.5. Dashboard

- The main dashboard should provide an overview and access to all major sections of the application.

## 2. Technical Requirements

These requirements describe the underlying technical implementation details.

### 2.1. Session Management

- The system must implement robust session management to handle user authentication and authorization.
- It must differentiate between `Admin` and `User` roles upon login and enforce access control throughout the application based on the user's session role.

### 2.2. CRUD Operations

- The application will implement CRUD (Create, Read, Update, Delete) functionalities.
- **User-specific CRUD:** Each user has full CRUD control over their own personal data (e.g., profile, goals).
- **Admin-level CRUD:** Admins have extended CRUD privileges for global resources like projects and members.
- **Task Status Update:**
    - A dedicated backend script (e.g., `update_task_status.php`) will handle status changes triggered by the user's checkbox click.
    - **Authorization:** This script MUST verify that the task being updated is assigned to the currently logged-in user.
    - **Project Progress Update:** Upon successfully marking a task as 'Done', the system must automatically recalculate and update the `Progress_Percent` of the parent project.

### 2.3. File Handling

- **Upload:**
    - A dedicated script (e.g., `upload_file.php`) will manage file uploads for tasks.
    - **Security:** It must validate file types (no executables) and size. Files must be stored in a secure location (outside the webroot if possible) with a unique, non-guessable filename.
    - The file's path and original name will be stored in the database, linked to the task.
- **Download:**
    - A dedicated script (e.g., `download_file.php`) will manage file downloads.
    - **Authorization:** This script MUST verify that the logged-in user is a member of the project to which the task's file belongs before serving the file. This prevents unauthorized access to files from other projects.

### 2.4. MySQL Database Schema

- **File Table:**
    1.  `File_ID` (INT, PK, AI)
    2.  `File_Name` (VARCHAR) - Original name of the file.
    3.  `File_Path` (VARCHAR) - Secure path on the server.
    4.  `File_Type` (VARCHAR)
    5.  `File_Upload_Time` (TIMESTAMP, DEFAULT CURRENT_TIMESTAMP)
    6.  `Task_ID` (INT, FK)
    7.  `User_ID` (INT, FK) - The user who uploaded the file.

- **Goal Table:**
    1.  `Goal_ID` (INT, PK, AI)
    2.  `Title` (VARCHAR)
    3.  `Description` (TEXT)
    4.  `Status` (VARCHAR)
    5.  `Goal_Start_Time` (DATE)
    6.  `Goal_End_Time` (DATE)
    7.  `User_ID` (INT, FK)

- **Project Table:**
    1.  `Project_ID` (INT, PK, AI)
    2.  `Title` (VARCHAR)
    3.  `Description` (TEXT)
    4.  `Project_Start_Time` (DATE)
    5.  `Project_End_Time` (DATE)
    6.  `Project_Status` (VARCHAR)
    7.  `Progress_Percent` (INT, DEFAULT 0)
    8.  `User_ID` (INT, FK) - The admin who created the project.

- **Task Table:**
    1.  `Task_ID` (INT, PK, AI)
    2.  `Title` (VARCHAR)
    3.  `Description` (TEXT)
    4.  `Status` (VARCHAR, e.g., 'Open', 'Done')
    5.  `Category` (VARCHAR)
    6.  `Priority` (VARCHAR)
    7.  `Task_End_Time` (DATE)
    8.  `Task_Created_Time` (TIMESTAMP, DEFAULT CURRENT_TIMESTAMP)
    9.  `Assigner_ID` (INT, FK to users) - Who assigned the task.
    10. `Assigned_User_ID` (INT, FK to users) - Who the task is for.
    11. `Project_ID` (INT, FK)