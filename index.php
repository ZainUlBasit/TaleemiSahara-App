<?php
session_start();
require_once 'config/database.php';

if (isset($_SESSION['user_id'])) {
  switch ($_SESSION['user_type']) {
    case 'student':
      header("Location: dashboard/student.php");
      exit();
    case 'donor':
      header("Location: dashboard/donor.php");
      exit();
    case 'mentor':
      header("Location: dashboard/mentor.php");
      exit();
    default:
      header("Location: index.php");
      exit();
  }
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>EduConnect - Empowering Education</title>
  <link rel="stylesheet" href="css/style.css" />
  <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
</head>

<body>
  <header>
    <nav class="navbar" style="height: 10vh">
      <div class="logo">
        <img
          src="images/logo.png"
          style="width: 200px; height: 180px"
          alt="Logo" />
      </div>
      <ul class="nav-links">
        <!-- <li><a href="#home">Home</a></li> -->
        <!-- <li><a href="#about">About</a></li>
          <li><a href="#students">Students</a></li>
          <li><a href="#donors">Donors</a></li>
          <li><a href="#mentors">Mentors</a></li> -->
        <li><a href="login.php" style="background-color: #5a4ae3; color:white; padding: 8px 10px; border-radius: 5px;">Login</a></li>
        <li><a href="register.php" style="background-color: #5a4ae3; color:white; padding: 8px 10px; border-radius: 5px;">Register</a></li>
      </ul>
    </nav>
  </header>

  <main>
    <?php include 'components/carousel.php'; ?>
    <!-- <section id="hero" class="hero-section">
      <div
        class="hero-overlay"
        style="
            background-image: url('images/bgimage.jpg');
            background-size: cover;
            background-position: center;
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 1;
            z-index: 1;
          "></div>
      <div
        class="hero-overlay"
        style="
            background-color: black;
            background-size: cover;
            background-position: center;
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0.8;
            z-index: 1;
          "></div>
      <div
        class="hero-overlay"
        style="
            background-color: white;
            background-size: cover;
            background-position: center;
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0.1;
            z-index: 1;
          "></div>
      <div class="hero-content" style="position: relative; z-index: 2">
        <h1 style="color: white">Connecting Dreams to Reality</h1>
        <p style="color: white">
          Join our platform to connect students with donors and mentors,
          making quality education accessible to all.
        </p>
        <div class="cta-buttons">
          <a href="register.php?type=student" class="btn btn-primary">I'm a Student</a>
          <a href="register.php?type=donor" class="btn btn-secondary">I'm a Donor</a>
          <a href="register.php?type=mentor" class="btn btn-tertiary">I'm a Mentor</a>
          <a href="register.php?type=mentor" class="btn btn-tertiary">Examinations</a>
        </div>
        Create a profile, showcase your achievements, and connect with
        potential donors and mentors.
        </p>
      </div>
      <div class="feature-card">
        <i class="fas fa-hand-holding-heart"></i>
        <h3>For Donors</h3>
        <p>
          Browse student profiles, make secure donations, and track the
          impact of your contributions.
        </p>
      </div>
      <div class="feature-card">
        <i class="fas fa-chalkboard-teacher"></i>
        <h3>For Mentors</h3>
        <p>
          Share your expertise, guide students, and make a lasting impact on
          their educational journey.
        </p>
      </div>
      </div>
    </section> -->

    <section id="impact" class="impact-section">
      <h2>Our Impact</h2>
      <div class="impact-stats">
        <div class="stat-card">
          <h3>1000+</h3>
          <p>Students Supported</p>
        </div>
        <div class="stat-card">
          <h3>500+</h3>
          <p>Active Donors</p>
        </div>
        <div class="stat-card">
          <h3>200+</h3>
          <p>Dedicated Mentors</p>
        </div>
      </div>
    </section>

    <?php include 'components/news-carousel.php'; ?>
  </main>

  <footer>
    <div class="footer-content">
      <div class="footer-section">
        <img
          src="images/logo-white.png"
          style="width: 200px; height: 180px"
          alt="Logo" />
        <p>Empowering education through community support and mentorship.</p>
      </div>
      <div class="footer-section">
        <h3>Quick Links</h3>
        <ul>
          <li><a href="#about">About Us</a></li>
          <li><a href="#contact">Contact</a></li>
          <li><a href="#privacy">Privacy Policy</a></li>
          <li><a href="#terms">Terms of Service</a></li>
        </ul>
      </div>
      <div class="footer-section">
        <h3>Connect With Us</h3>
        <div class="social-links">
          <a href="#"><i class="fab fa-facebook"></i></a>
          <a href="#"><i class="fab fa-twitter"></i></a>
          <a href="#"><i class="fab fa-linkedin"></i></a>
          <a href="#"><i class="fab fa-instagram"></i></a>
        </div>
      </div>
    </div>
    <div class="footer-bottom">
      <p>&copy; 2024 Taleemi Sahara. All rights reserved.</p>
    </div>
  </footer>

  <script src="js/main.js"></script>
  <script src="js/index.js"></script>
</body>

</html>