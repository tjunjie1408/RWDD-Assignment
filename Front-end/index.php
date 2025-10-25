<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TaskFlow - Enhance Your Workflow</title>
    <meta name="description" content="Boost your productivity with our all-in-one platform for task management, time tracking, goal setting, and team collaboration.">
    
    <link rel="icon" href="Pictures/logo.png">
    <link rel="stylesheet" href="CSS/homepage.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div id="particles-js"></div>

    <header class="site-header">
        <div class="container header-container">
            <a href="#home" class="logo"><img src="Pictures/logo.png" alt="TaskFlow Logo" style="height: 40px;"></a>
            <ul class="nav-menu" id="navMenu">
                <li class="nav-item"><a href="#home" class="nav-link">Home</a></li>
                <li class="nav-item"><a href="#about" class="nav-link">About</a></li>
                <li class="nav-item"><a href="#objective" class="nav-link">Objective</a></li>
                <li class="nav-item"><a href="#product" class="nav-link">Product</a></li>
                <li class="nav-item"><a href="#support" class="nav-link">Support</a></li>
            </ul>
            <div class="header-actions">
                <a href="signup.php" class="login-btn">Log in</a>
                <a href="signup.php" class="btn btn-primary">Get Started</a>
            </div>
            <button class="mobile-toggle" id="mobileToggle">☰</button>
        </div>
    </header>

    <main>
        <section id="home" class="hero">
            <div class="container">
                <h1 class="animated-hero-text">Task Management Reimagined</h1>
                <p>Streamline workflows, track progress, and achieve more with our all-in-one platform designed for individuals and teams.</p>
                <a href="signup.php" class="btn btn-primary">Get Started For Free</a>
            </div>
        </section>

        <section id="about" class="content-section">
            <div class="container about-container">
                <div class="about-content">
                    <h2>About TaskFlow</h2>
                    <p>We are dedicated to creating intuitive tools that help you and your team achieve peak productivity with less effort. Our mission is to provide a seamless and integrated experience for managing tasks, tracking time, and collaborating with your team, no matter the size or complexity of your projects.</p>
                    <p>TaskFlow was born from the idea that productivity software should be powerful, yet simple to use. We believe in continuous improvement and work closely with our users to deliver a product that truly meets their needs.</p>
                </div>
                <div class="about-image">
                    <img src="Pictures/pic1.png" alt="Team working together">
                </div>
            </div>
        </section>

        <section id="objective" class="content-section alt-bg">
            <div class="container">
                <div class="section-header">
                    <h2>Our Objective</h2>
                </div>
                <ul class="objective-list">
                    <li class="animated-card"><i class="fas fa-bullseye"></i> To provide a user-friendly interface for task management.</li>
                    <li class="animated-card"><i class="fas fa-chart-line"></i> To enhance productivity through effective task organization.</li>
                    <li class="animated-card"><i class="fas fa-users"></i> To facilitate seamless collaboration among team members.</li>
                    <li class="animated-card"><i class="fas fa-bell"></i> To offer real-time updates and notifications to keep everyone in sync.</li>
                </ul>
            </div>
        </section>

        <section id="product" class="content-section">
            <div class="container">
                <div class="section-header">
                    <h2>Our Product in Action</h2>
                    <p>Discover how TaskFlow can transform your daily work.</p>
                </div>
                <div class="product-grid">
                    <div class="product-card animated-card">
                        <img src="Pictures/product1.png" alt="Product Screenshot 1" class="product-image">
                        <div class="product-content">
                            <h3>Visual Task Boards</h3>
                            <p>Organize your work visually with our drag-and-drop Kanban boards. See project progress at a glance and identify bottlenecks before they happen.</p>
                        </div>
                    </div>
                    <div class="product-card animated-card">
                        <img src="Pictures/product1.png" alt="Product Screenshot 2" class="product-image">
                        <div class="product-content">
                            <h3>Insightful Analytics</h3>
                            <p>Gain valuable insights into your team's performance with our powerful analytics dashboard. Track time, monitor progress, and make data-driven decisions.</p>
                        </div>
                    </div>
                    <div class="product-card animated-card">
                        <img src="Pictures/product1.png" alt="Product Screenshot 3" class="product-image">
                        <div class="product-content">
                            <h3>Team Collaboration</h3>
                            <p>Communicate with your team in real-time, share files, and keep everyone on the same page with integrated messaging and notifications.</p>
                        </div>
                    </div>
                    <div class="product-card animated-card">
                        <img src="Pictures/product1.png" alt="Product Screenshot 4" class="product-image">
                        <div class="product-content">
                            <h3>Goal Tracking</h3>
                            <p>Set, track, and achieve your goals with our goal management features. Align your team's efforts and celebrate your successes together.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="support" class="content-section alt-bg">
            <div class="container support-container">
                <div class="support-content">
                    <h2>We're Here to Help</h2>
                    <p>Our dedicated support team is always available to assist you with any questions or issues. Your success is our priority.</p>
                    <a href="mailto:jasonteo1408@gmail.com?subject=Home Support Requests&body=Please describe your problem or feedback below: " class="btn btn-secondary">Contact Support</a>
                </div>
                <div class="support-image">
                    <img src="Pictures/support.png" alt="Support Team">
                </div>
            </div>
        </section>

        <footer class="footer">
            <div class="container">
                <div class="footer-grid">
                    <div class="footer-column">
                        <h3>TaskFlow</h3>
                        <p style="color: var(--dark-text-alt);">Enhancing productivity for all.</p>
                    </div>
                    <div class="footer-column">
                        <h3>Navigate</h3>
                        <ul class="footer-links">
                            <li><a href="#home">Home</a></li>
                            <li><a href="#about">About</a></li>
                            <li><a href="#product">Product</a></li>
                        </ul>
                    </div>
                    <div class="footer-column">
                        <h3>Company</h3>
                        <ul class="footer-links">
                            <li><a href="#">About Us</a></li>
                            <li><a href="#support">Support</a></li>
                            <li><a href="#">Contact</a></li>
                        </ul>
                    </div>
                </div>
                <div class="footer-bottom"><p>&copy; 2025 TaskFlow. All rights reserved.</p></div>
            </div>
        </footer>

        <section class="final-reveal animated-card">
            <div class="container">
                <h2>TaskFlow</h2>
            </div>
        </section>
    </main>

    <script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
    <script src="JS/homepage.js"></script>
</body>
</html>