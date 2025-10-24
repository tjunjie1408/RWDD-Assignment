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
- Can upload files related to their assigned tasks within a project.
- Can view and download files uploaded by other users in the same project.
- Must mark their tasks as complete using a checkbox.
- The overall project progress should automatically update when a task is marked as complete.

### 1.3. Member Management

- **Admin:**
- Can modify the details of any member.
- Can delete any member from the system.
- **User:**
- Can view a list of other members.
- Can click on a member's profile to view their details in an enlarged view.

### 1.4. Goals & Analysis

- This section is accessible to and functions identically for both **Admin** and **User** roles.

### 1.5. Dashboard

- The main dashboard should provide an overview and access to all major sections of the application.

## 2. Technical Requirements

These requirements describe the underlying technical implementation details needed to support the subjective requirements.

### 2.1. Session Management

- The system must implement robust session management to handle user authentication and authorization.
- It must differentiate between `Admin` and `User` roles upon login and enforce access control throughout the application based on the user's session role.

### 2.2. CRUD Operations

- The application will implement CRUD (Create, Read, Update, Delete) functionalities.
- Each user (including admins) should have full CRUD control over their own personal data (e.g., profile information, personal goals).
- Admins will have extended CRUD privileges for managing global resources like projects and members.

### 2.3. File Handling

- A system for secure file uploads and downloads must be implemented for project tasks.
- File access should be restricted to members of the specific project.

### MySQL

- File Table:

1. File_ID (Auto_Increment) (Primary Key)
2. File_Name
3. File_URL
4. File_Type
5. File_Upload_Time (current_timestamp)
6. Project_ID (Foreign Key)
7. User_ID (Foreign Key)

- Goal Table:

1. Goal_ID (Auto_Increment) (Primary Key)
2. Title
3. Description
4. Type
5. Status
6. Goal_Start_Time
7. Goal_End_Time
8. Goal_Created_Time (current_timestamp)
9. Goal_Completed_Time
10. User_ID (Foreign Key)
11. Project_ID (Foreign Key)
12. Task_ID (Foreign Key)

- Project Table:

1. Project_ID (Auto_Increment) (Primary Key)
2. Title
3. Description
4. Project_Start_Time
5. Project_End_Time
6. Project_Status
7. User_ID (Foreign Key)

- Task Table:

1. Task_ID (Auto_Increment) (Primary Key)
2. Title
3. Description
4. Status
5. Category
6. Priority
7. Task_Start_Time
8. Task_End_Time
9. Task_Created_Time (current_timestamp)
10. Task_Completed_Time
11. User_ID (Foreign Key)
12. Project_ID (Foreign Key)
