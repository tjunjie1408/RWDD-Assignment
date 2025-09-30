1. PREVIOUS VERSION HOMEPAGE

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>homepage</title>
  <link rel="stylesheet" href="/RWDD-Assignment/Front-end/CSS/style.css">
</head>
<body>
  <div class="container">

    <!-- Header -->
    <header>
      <img class="logo" src="Pictures/logo.png" alt="TaskFlow">
      
      <!-- Hamburger Menu -->
      <div class="menu-toggle" onclick="toggleMenu()">
        <span></span>
        <span></span>
        <span></span>
      </div>

      <div class="nav">
        <a href="#home">Home</a>
        <a href="#about">About</a>
        <a href="#objective">Objective</a>
        <a href="#product">Product</a>
        <a href="#support">Support</a>
      </div>
    </header>

    <!-- Hero Section -->
    <nav id="home">
      <div class="tittle">
        <h1>
          <span class="task">“Task</span>
          <span class="flow">Flow”</span>
        </h1>
        <p>Task Management Website</p>
      </div>

      <div class="btn-container">
        <a href="signup.html" class="btn">Get Start</a>
      </div>

      <img class="pic1" src="Pictures/pic1.png" alt="pic1">
    </nav>

    <!-- About Section -->
    <section id="about" class="about_section">
      <p class="about">About</p>
      <p class="about_details">Lorem ipsum dolor sit amet consectetur adipisicing elit. Id ducimus culpa voluptate, voluptatum dicta delectus provident officiis beatae minus similique hic, inventore numquam quos iste sequi aperiam quidem quibusdam! Numquam.</p>
    </section>

    <!-- Objective Section -->
    <section id="objective" class="objective_section">
      <h1 class="objective">Objective</h1>
      <ul class="objective_details">
        <li>To provide a user-friendly interface for task management.</li>
        <li>To enhance productivity through effective task organization.</li>
        <li>To facilitate collaboration among team members.</li>
        <li>To offer real-time updates and notifications.</li>
      </ul>
    </section>

    <!-- Product Section -->
    <section id="product" class="product_section">
      <h1>Product</h1>

      <div class="product_item left">
        <img src="Pictures/product1.png" alt="product1">
        <p class="product_details">Lorem ipsum dolor sit amet consectetur adipisicing elit. Id ducimus culpa voluptate...</p>
      </div>

      <div class="product_item right">
        <img src="Pictures/product1.png" alt="product2">
        <p class="product_details">Lorem ipsum dolor sit amet consectetur adipisicing elit. Id ducimus culpa voluptate...</p>
      </div>

      <div class="product_item left">
        <img src="Pictures/product1.png" alt="product3">
        <p class="product_details">Lorem ipsum dolor sit amet consectetur adipisicing elit. Id ducimus culpa voluptate...</p>
      </div>

      <div class="product_item right">
        <img src="Pictures/product1.png" alt="product4">
        <p class="product_details">Lorem ipsum dolor sit amet consectetur adipisicing elit. Id ducimus culpa voluptate...</p>
      </div>
    </section>

    <!-- Support Section -->
    <section id="support" class="support_section">
      <h1>Support</h1>
      <div class="support_container">
        <img class="support" src="Pictures/support.png" alt="support">
        <p class="support_details">Lorem ipsum dolor sit amet, consectetur adipisicing elit. A repellendus modi totam vitae nulla illum unde repudiandae?</p>
      </div>
    </section>

    <hr>

    <!-- Footer -->
    <footer class="footer">
      <div class="footer_top">
        <div class="footer_logo">
          <img src="Pictures/logo.png" alt="TaskFlow Logo">
        </div>
        <nav class="footer_nav">
          <p class="footer_title">About</p>
          <ul>
            <li><a href="#home">Home</a></li>
            <li><a href="#about">About</a></li>
            <li><a href="#objective">Objective</a></li>
            <li><a href="#product">Product</a></li>
            <li><a href="#support">Support</a></li>
          </ul>
        </nav>
      </div>

      <hr>

      <div class="footer_bottom">
        <p>©2025 TaskFlow</p>
        <div>
          <p>Privacy & Policy &emsp; Terms & Condition</p>
        </div>
      </div>
    </footer>
  </div>

  <!-- JS for hamburger menu -->
  <script src="/RWDD-Assignment/Front-end/JS/script.js"></script>
</body>
</html>