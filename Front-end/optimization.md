Left Column
Card 1: "My Upcoming Tasks" (Most Important Card)

Purpose: This card answers "What do I need to do today/this week?"

Logic: It won't display all 50 of your tasks. Instead, a PHP script queries tasks across all your projects and filters only:

Assigned to you (User_ID = $_SESSION['id'])

With status "Open"

And due within the next 7 days

Display: Shows only a short list of 5 to 7 most urgent tasks. Each displays the task name, project it belongs to, and due date.

Link: A "View All Tasks" link at the bottom of the card directs to tasks.php.

Card 2: "Quote of the Day"

Purpose: Your desired motivational module.

Logic: Pure JavaScript. Click the refresh button (🔄) to cycle through quotes.

Display: Shows the quote and author.

Second Column (Right Column)
Card 3: "My Projects" (Summary Version)

Purpose: This card answers "How are my projects progressing overall?"

Logic: It doesn't display all 10 of your projects. It only shows the 5 most recently active projects.

Display: Shows only the project title and a progress bar.

Link: A "View All Projects" link at the bottom of the card directs to project.php.

Card 4: "My Active Goals"

Purpose: Reminds you of your personal long-term goals.

Logic: Queries the goals database table, retrieving only goals belonging to you that are not marked "Completed," limited to 5.

Display: A simple list of goals.

Link: A "View All Goals" link at the bottom of the card directs to goal.php.

###This is the core section of the Admin Dashboard. Below the clock, place four small statistics cards to display key performance indicators (KPIs) for the system.

Card 1: Total Projects

Backend SQL: SELECT COUNT(Project_ID) as total FROM projects;

Display: A large number (e.g., "12") and the title "Total Projects".

Card 2: Total Members

Backend SQL: SELECT COUNT(user_ID) as total FROM users WHERE Role_ID = 1; (Counts only regular users)

Display: A large number (e.g., "48") and the title "Total Members".

Card 3: Open Tasks

Backend SQL: SELECT COUNT(Task_ID) as total FROM tasks WHERE Status = 'Open';

Display: A large number (e.g., "92") and the title "Open Tasks".

Card 4: Completed Tasks

Backend SQL: SELECT COUNT(Task_ID) as total FROM tasks WHERE Status = 'Done';

Display: A large number (e.g., "315") and the title "Completed Tasks".

2. "Quick Actions"
Below the statistics cards, provide two prominent buttons as quick access points to management functions.

Button 1: "+ Create New Project" (Links to admin_project.php and automatically opens a modal, or directly links to the project creation page)

Button 2: "+ Add New Member" (Links to admin_member.php and automatically opens a modal)

3. Main Grid Layout (2 Columns)
Below Quick Actions, set up a two-column grid:

Left Card (Column 1): "Recent Projects"

Purpose: Administrators need visibility into all newly created projects in the system, not just those they personally added.

Backend SQL: SELECT * FROM projects ORDER BY Project_Start_Date DESC LIMIT 5; (This matches your current query in admin_dashboard.php; no changes needed).

Display: Show a list of these 5 projects, including progress bars and titles.

Link: Include a "Manage All Projects" link at the bottom of the card (linking to admin_project.php).

Right Card (Column 2): "Recent Members"

Purpose: Administrators need to monitor newly registered users.

Backend SQL: SELECT username, email, created_at FROM users WHERE Role_ID = 1 ORDER BY created_at DESC LIMIT 5; (Assuming created_at is your database's registration time field)

Display: A list of new members showing their username, email, and join date.

Link: A "Manage All Members" link at the bottom of the card (points to admin_member.php).