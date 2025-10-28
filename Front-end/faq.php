<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FAQ - TaskFlow</title>
    <link rel="stylesheet" href="CSS/faq.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Frequently Asked Questions</h1>
            <p>Find answers to common questions about using TaskFlow.</p>
        </header>

        <div class="faq-container">
            <div class="faq-item">
                <button class="faq-question">
                    <span>What is TaskFlow?</span>
                    <i class="fas fa-chevron-down"></i>
                </button>
                <div class="faq-answer">
                    <p>TaskFlow is a comprehensive project management tool designed to help teams organize, track, and collaborate on tasks and projects efficiently. It provides features like project creation, team management, task assignment, and progress tracking.</p>
                </div>
            </div>

            <div class="faq-item">
                <button class="faq-question">
                    <span>What are the different user roles?</span>
                    <i class="fas fa-chevron-down"></i>
                </button>
                <div class="faq-answer">
                    <p>TaskFlow has two main roles: <strong>Admin</strong> and <strong>User</strong>.
                    <br>&#8226; <strong>Admins</strong> have full control to create, update, and delete projects, as well as manage which users are assigned to each project.
                    <br>&#8226; <strong>Users</strong> (or members) can participate in projects they are assigned to, manage their tasks, and view other members and projects in the system.</p>
                </div>
            </div>

            <div class="faq-item">
                <button class="faq-question">
                    <span>How do I manage projects as an Admin?</span>
                    <i class="fas fa-chevron-down"></i>
                </button>
                <div class="faq-answer">
                    <p>Only Admins can manage projects. From the <strong>Project Management</strong> page, you can create a new project, edit its details (like title, description, and dates), or delete it entirely. When creating a project, you can also select from a list of users to assign them as project members.</p>
                </div>
            </div>

            <div class="faq-item">
                <button class="faq-question">
                    <span>How do I view projects as a User?</span>
                    <i class="fas fa-chevron-down"></i>
                </button>
                <div class="faq-answer">
                    <p>On the <strong>Projects</strong> page, you will see all available projects. If you are a member of a project, you can click on it to go directly to its task board. If you are not a member, clicking on the project will open a pop-up window showing its details, but you will not be able to see the tasks inside.</p>
                </div>
            </div>

            <div class="faq-item">
                <button class="faq-question">
                    <span>How are tasks assigned and managed?</span>
                    <i class="fas fa-chevron-down"></i>
                </button>
                <div class="faq-answer">
                    <p>Inside a project, an <strong>Admin</strong> can create new tasks and assign them to any member of that project. As a <strong>User</strong>, you can view tasks assigned to you, update their status (e.g., from 'Open' to 'Done'), and upload relevant files.</p>
                </div>
            </div>

            <div class="faq-item">
                <button class="faq-question">
                    <span>Can I see other users in the system?</span>
                    <i class="fas fa-chevron-down"></i>
                </button>
                <div class="faq-answer">
                    <p>Yes. The <strong>Members</strong> page provides a view-only list of all users in the TaskFlow system. This allows you to see who you can potentially collaborate with, but only Admins can manage member details and project assignments.</p>
                </div>
            </div>
            
            <div class="faq-item">
                <button class="faq-question">
                    <span>Is my data secure?</span>
                    <i class="fas fa-chevron-down"></i>
                </button>
                <div class="faq-answer">
                    <p>We take data security very seriously. All data is encrypted in transit and at rest. We follow industry best practices to ensure your project information is safe and accessible only to your authorized team members.</p>
                </div>
            </div>
        </div>

        <div class="back-link">
            <a href="javascript:history.back()"><i class="fas fa-arrow-left"></i> Back to Previous Page</a>
        </div>
    </div>

    <script>
        const faqItems = document.querySelectorAll('.faq-item');

        faqItems.forEach(item => {
            const question = item.querySelector('.faq-question');
            question.addEventListener('click', () => {
                const answer = item.querySelector('.faq-answer');
                const icon = question.querySelector('i');

                // Toggle active class for the clicked item
                item.classList.toggle('active');

                if (item.classList.contains('active')) {
                    answer.style.maxHeight = answer.scrollHeight + 'px';
                    icon.style.transform = 'rotate(180deg)';
                } else {
                    answer.style.maxHeight = '0';
                    icon.style.transform = 'rotate(0deg)';
                }
            });
        });
    </script>
</body>
</html>