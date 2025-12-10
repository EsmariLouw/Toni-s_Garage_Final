<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>About Us - Toni's Garage</title>
  <!-- bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <!-- icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" />
  <link rel="stylesheet" href="styles.css" />
</head>

<body>
  <!-- Header -->
  <header>
    <div class="navbar">
      <div class="logo" onclick="window.location.href='index.php'" style="cursor:pointer;">Toni's garage</div>
      <nav class="nav-links">
        <a href="index.php">Home</a>
        <a href="inventory.php">Inventory</a>
        <a href="about.php">About</a>
        <a href="index.php#contact">Contact</a>
      </nav>
      <div class="right-buttons">
          <?php
          
          if (!empty($_SESSION['logged_in']) && $_SESSION['logged_in'] === true): ?>
              <button class="login-btn" onclick="window.location.href='logout.php'">
                  Log out
              </button>
          <?php else: ?>
              <button class="logout-btn" onclick="window.location.href='login.php'">
                  Log in
              </button>
          <?php endif; ?>
      </div>
    </div>
  </header>

  <!-- Hero -->
  <section class="about-hero">
    <h1>About Toni's Garage</h1>
    <p>A student project showcasing modern web development and database management</p>
  </section>

  <!-- Main about -->
  <section class="about-content">
    <div class="about-section">
      <h2>Our Story</h2>
      <p>
        Toni's Garage is a comprehensive car dealership platform developed as a final project for our web development course
        at RIT Croatia. This project demonstrates the practical application of full-stack web development principles,
        combining modern frontend technologies with robust backend systems and database management.
      </p>
      <p>
        Our platform offers a complete solution for vehicle sales, featuring user authentication, dynamic inventory management,
        advanced filtering systems, and a seamless user experience. Built with attention to detail and modern design principles,
        Toni's Garage showcases what dedicated students can achieve with the right guidance and collaboration.
      </p>
    </div>
  </section>

  <!-- Team section -->
  <section class="team-section">
    <h2>Our Development Team</h2>
    <p class="team-subtitle">
      A collaborative effort by dedicated students at RIT Croatia, working together to create a professional-grade
      web application that demonstrates our skills in full-stack development.
    </p>

    <div class="about-section">
      <h3 style="text-align: center; margin-bottom: 30px;">Technologies Used</h3>
      <div class="tech-stack">
        <div class="tech-item">
          <i class="bi bi-filetype-php"></i>
          <p>PHP</p>
        </div>
        <div class="tech-item">
          <i class="bi bi-database"></i>
          <p>MySQL</p>
        </div>
        <div class="tech-item">
          <i class="bi bi-filetype-html"></i>
          <p>HTML5</p>
        </div>
        <div class="tech-item">
          <i class="bi bi-filetype-css"></i>
          <p>CSS3</p>
        </div>
        <div class="tech-item">
          <i class="bi bi-filetype-js"></i>
          <p>JavaScript</p>
        </div>
        <div class="tech-item">
          <i class="bi bi-bootstrap"></i>
          <p>Bootstrap 5</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Goals with members -->
  <section class="project-goals-section">
    <div class="about-content">
      <div class="about-section">
        <h2>Meet the team</h2>
        <p>
          Our primary objective was to create a fully functional car dealership platform that demonstrates Teamwork, Quality and solving all Objectives.
        </p>
      </div>

      <!-- Members carousel -->
      <div class="team-carousel-section">
        <h2 style="text-align: center; margin-bottom: 50px; color: #111;">Thank you!</h2>
        <div class="carousel-container">
          <div class="carousel-track">

            <!-- members -->
            <div class="team-member">
              <div class="member-avatar" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <span>H</span>
              </div>
              <div class="member-info">
                <p class="member-name">Hana</p>
                <p class="member-role">Backend</p>
              </div>
            </div>

            <div class="team-member">
              <div class="member-avatar" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                <span>I</span>
              </div>
              <div class="member-info">
                <p class="member-name">Ivo</p>
                <p class="member-role">Backend</p>
              </div>
            </div>

            <div class="team-member">
              <div class="member-avatar" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                <span>N</span>
              </div>
              <div class="member-info">
                <p class="member-name">Nikola</p>
                <p class="member-role">Backend</p>
              </div>
            </div>

            <div class="team-member">
              <div class="member-avatar" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
                <span>B</span>
              </div>
              <div class="member-info">
                <p class="member-name">Borna</p>
                <p class="member-role">Frontend</p>
              </div>
            </div>

            <div class="team-member">
              <div class="member-avatar" style="background: linear-gradient(135deg, #30cfd0 0%, #330867 100%);">
                <span>D</span>
              </div>
              <div class="member-info">
                <p class="member-name">Dusan</p>
                <p class="member-role">Frontend</p>
              </div>
            </div>

            <div class="team-member">
              <div class="member-avatar" style="background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);">
                <span>A</span>
              </div>
              <div class="member-info">
                <p class="member-name">Adrian</p>
                <p class="member-role">Frontend</p>
              </div>
            </div>

            <div class="team-member">
              <div class="member-avatar" style="background: linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%);">
                <span>A</span>
              </div>
              <div class="member-info">
                <p class="member-name">Alex</p>
                <p class="member-role">Frontend</p>
              </div>
            </div>

            <div class="team-member">
              <div class="member-avatar" style="background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%);">
                <span>B</span>
              </div>
              <div class="member-info">
                <p class="member-name">Bruno</p>
                <p class="member-role">Frontend</p>
              </div>
            </div>

            <div class="team-member">
              <div class="member-avatar" style="background: linear-gradient(135deg, #ff6e7f 0%, #bfe9ff 100%);">
                <span>D</span>
              </div>
              <div class="member-info">
                <p class="member-name">Danilo</p>
                <p class="member-role">Backend</p>
              </div>
            </div>

            <div class="team-member">
              <div class="member-avatar" style="background: linear-gradient(135deg, #e0c3fc 0%, #8ec5fc 100%);">
                <span>D</span>
              </div>
              <div class="member-info">
                <p class="member-name">Dora</p>
                <p class="member-role">Backend</p>
              </div>
            </div>

            <div class="team-member">
              <div class="member-avatar" style="background: linear-gradient(135deg, #f77062 0%, #fe5196 100%);">
                <span>D</span>
              </div>
              <div class="member-info">
                <p class="member-name">Dorde</p>
                <p class="member-role">Backend</p>
              </div>
            </div>

            <div class="team-member">
              <div class="member-avatar" style="background: linear-gradient(135deg, #c471f5 0%, #fa71cd 100%);">
                <span>E</span>
              </div>
              <div class="member-info">
                <p class="member-name">Egor</p>
                <p class="member-role">Backend</p>
              </div>
            </div>

            <div class="team-member">
              <div class="member-avatar" style="background: linear-gradient(135deg, #48c6ef 0%, #6f86d6 100%);">
                <span>E</span>
              </div>
              <div class="member-info">
                <p class="member-name">Esmari</p>
                <p class="member-role">Frontend</p>
              </div>
            </div>

            <div class="team-member">
              <div class="member-avatar" style="background: linear-gradient(135deg, #96fbc4 0%, #f9f586 100%);">
                <span>I</span>
              </div>
              <div class="member-info">
                <p class="member-name">Igor</p>
                <p class="member-role">Frontend</p>
              </div>
            </div>

            <div class="team-member">
              <div class="member-avatar" style="background: linear-gradient(135deg, #fbc2eb 0%, #a6c1ee 100%);">
                <span>I</span>
              </div>
              <div class="member-info">
                <p class="member-name">Ivan Begic</p>
                <p class="member-role">Backend</p>
              </div>
            </div>

            <div class="team-member">
              <div class="member-avatar" style="background: linear-gradient(135deg, #fdcbf1 0%, #e6dee9 100%);">
                <span>I</span>
              </div>
              <div class="member-info">
                <p class="member-name">Ivan Miskic</p>
                <p class="member-role">Frontend</p>
              </div>
            </div>

            <div class="team-member">
              <div class="member-avatar" style="background: linear-gradient(135deg, #a1c4fd 0%, #c2e9fb 100%);">
                <span>N</span>
              </div>
              <div class="member-info">
                <p class="member-name">nadjaa</p>
                <p class="member-role">Frontend</p>
              </div>
            </div>

            <div class="team-member">
              <div class="member-avatar" style="background: linear-gradient(135deg, #d299c2 0%, #fef9d7 100%);">
                <span>S</span>
              </div>
              <div class="member-info">
                <p class="member-name">Sarah</p>
                <p class="member-role">Backend</p>
              </div>
            </div>

            <div class="team-member">
              <div class="member-avatar" style="background: linear-gradient(135deg, #89f7fe 0%, #66a6ff 100%);">
                <span>Y</span>
              </div>
              <div class="member-info">
                <p class="member-name">Yifei Zhu</p>
                <p class="member-role">Frontend</p>
              </div>
            </div>

            <!-- duplicate for seamless loop -->
            <div class="team-member">
              <div class="member-avatar" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <span>H</span>
              </div>
              <div class="member-info">
                <p class="member-name">Hana</p>
                <p class="member-role">Backend</p>
              </div>
            </div>

            <div class="team-member">
              <div class="member-avatar" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                <span>I</span>
              </div>
              <div class="member-info">
                <p class="member-name">Ivo</p>
                <p class="member-role">Backend</p>
              </div>
            </div>

            <div class="team-member">
              <div class="member-avatar" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                <span>N</span>
              </div>
              <div class="member-info">
                <p class="member-name">Nikola</p>
                <p class="member-role">Backend</p>
              </div>
            </div>

            <div class="team-member">
              <div class="member-avatar" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
                <span>B</span>
              </div>
              <div class="member-info">
                <p class="member-name">Borna</p>
                <p class="member-role">Frontend</p>
              </div>
            </div>

            <div class="team-member">
              <div class="member-avatar" style="background: linear-gradient(135deg, #30cfd0 0%, #330867 100%);">
                <span>D</span>
              </div>
              <div class="member-info">
                <p class="member-name">Dusan</p>
                <p class="member-role">Frontend</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer id="contact">
    <div class="footer-content">
      <div class="footer-section">
        <h3>Toni's Garage</h3>
        <p>Your trusted partner in finding the perfect vehicle.</p>
      </div>
      <div class="footer-section">
        <h4>Quick Links</h4>
        <ul>
          <li><a href="index.php">Home</a></li>
          <li><a href="inventory.php">Inventory</a></li>
          <li><a href="about.php">About Us</a></li>
        </ul>
      </div>
      <div class="footer-section">
        <h4>Contact</h4>
        <ul>
          <li>https://discord.gg/yc3B4swt</li>
          <li>RIT Croatia, Dubrovnik</li>
        </ul>
      </div>
      <div class="footer-section">
        <h4>Hours</h4>
        <ul>
          <li>Mon-Fri: 9:00 AM - 8:00 PM</li>
          <li>Saturday/Sunday: Vacation</li>
        </ul>
      </div>
    </div>
    <div class="footer-bottom">
      <p>© 2025 Car Dealership. Built for course project under Professor Toni's guidance.</p>
    </div>
  </footer>

  <!-- bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="app.js"></script>
</body>

</html>