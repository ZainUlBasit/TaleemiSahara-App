<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Document</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300..700&family=Raleway:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@400..700&display=swap" rel="stylesheet" />


  <link rel="stylesheet" href="./css/font.css">
  <link rel="stylesheet" href="./css/style.css">
  <style>
    * {
      box-sizing: border-box;
      padding: 0;
      margin: 0;
      font-family: "Raleway", sans-serif;
    }

    ul {
      list-style-type: none;
      padding: 0;
      margin: 0;
      display: flex;
      align-items: center;
      gap: 20px;
    }

    .navbar {
      display: flex;
      justify-content: space-between;
      width: 100%;
      max-width: 1400px;
      padding: 10px 20px;
      margin: 0px auto;
    }

    .navbar-wrapper {
      width: 100%;
      position: fixed;
      z-index: 1000;
      /* background-color: white; */
      background-color: #96ADC5;
      border-bottom: 4px solid #465462;
    }

    .navbar a {
      text-decoration: none;
      color: inherit;
      font-weight: 600;
      transition: text-decoration 1s ease-in-out;
    }

    .navbar a:hover {
      text-decoration: underline;
    }

    /* From Uiverse.io by satyamchaudharydev */
    .button {
      position: relative;
      transition: all 0.3s ease-in-out;
      box-shadow: 0px 10px 20px rgba(0, 0, 0, 0.2);
      padding-block: 0.5rem;
      padding-inline: 1.25rem;
      background-color: rgb(0 107 179);
      border-radius: 9999px;
      display: flex;
      align-items: center;
      justify-content: center;
      color: #ffff;
      gap: 10px;
      font-weight: bold;
      border: 3px solid #ffffff4d;
      outline: none;
      overflow: hidden;
      font-size: 15px;
      cursor: pointer !important;
    }

    .icon {
      width: 24px;
      height: 24px;
      transition: all 0.3s ease-in-out;
    }

    .button:hover {
      transform: scale(1.05);
      border-color: #fff9;
    }

    .button:hover .icon {
      transform: translate(4px);
    }

    .button:hover::before {
      animation: shine 1.5s ease-out infinite;
    }

    .button::before {
      content: "";
      position: absolute;
      width: 100px;
      height: 100%;
      background-image: linear-gradient(120deg,
          rgba(255, 255, 255, 0) 30%,
          rgba(255, 255, 255, 0.8),
          rgba(255, 255, 255, 0) 70%);
      top: 0;
      left: -100px;
      opacity: 0.6;
    }

    @keyframes shine {
      0% {
        left: -100px;
      }

      60% {
        left: 100%;
      }

      to {
        left: 100%;
      }
    }

    /* From Uiverse.io by cssbuttons-io */
    .c-button {
      color: #000;
      font-weight: 700;
      font-size: 16px;
      text-decoration: none;
      padding: 0.9em 1.6em;
      cursor: pointer;
      display: inline-block;
      vertical-align: middle;
      position: relative;
      z-index: 1;
    }

    .c-button--gooey {
      color: #465462;
      text-transform: uppercase;
      letter-spacing: 2px;
      border: 4px solid #465462;
      border-radius: 0;
      position: relative;
      transition: all 700ms ease;
    }

    .c-button--gooey .c-button__blobs {
      height: 100%;
      filter: url(#goo);
      overflow: hidden;
      position: absolute;
      top: 0;
      left: 0;
      bottom: -3px;
      right: -1px;
      z-index: -1;
    }

    .c-button--gooey .c-button__blobs div {
      background-color: #465462;
      width: 34%;
      height: 100%;
      border-radius: 100%;
      position: absolute;
      transform: scale(1.4) translateY(125%) translateZ(0);
      transition: all 700ms ease;
    }

    .c-button--gooey .c-button__blobs div:nth-child(1) {
      left: -5%;
    }

    .c-button--gooey .c-button__blobs div:nth-child(2) {
      left: 30%;
      transition-delay: 60ms;
    }

    .c-button--gooey .c-button__blobs div:nth-child(3) {
      left: 66%;
      transition-delay: 25ms;
    }

    .c-button--gooey:hover {
      color: #fff;
    }

    .c-button--gooey:hover .c-button__blobs div {
      transform: scale(1.4) translateY(0) translateZ(0);
    }

    .impact-section {
      padding: 5rem 0px;
      background-color: white;
    }

    .impact-section h2 {
      text-align: center;
      font-size: 2.5rem;
      margin-bottom: 3rem;
    }

    .impact-stats {
      display: flex;
      gap: 2rem;
      max-width: 1000px;
      margin: 0 auto;
    }

    .custom-container {
      width: 100%;
      min-width: 300px;
      height: 100%;
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      place-items: center;
      place-content: center;
      margin: 0px auto;
      gap: 100px;
      padding: 50px 50px;
      color: white;
      font-family: "Spartan", sans-serif;
      animation: fade-in 0.5s ease-in-out;
      max-width: 1500px;
    }



    @media (max-width: 1250px) {
      .custom-container {
        padding: 50px 50px;
      }
    }

    @media (max-width: 1150px) {
      .custom-container {
        padding: 50px 10px;
      }

      .impact-stats {
        flex-wrap: wrap;
        justify-content: center;
      }
    }

    @media (max-width: 1000px) {
      .custom-container {
        padding: 20px 10px;
        grid-template-columns: repeat(1, 1fr);
        gap: 10px;
      }
    }

    @media (min-width: 768px) {
      .custom-container {
        width: 100%;
      }
    }

    .inner-container {
      width: 100% !important;
      padding-left: 1rem;
      padding-right: 1rem;
    }

    .heading-img {
      width: 9rem;
      /* 36 x 0.25rem = 9rem */
      height: 9rem;
      min-width: 5rem;
      /* 20 x 0.25rem = 5rem */
      margin-top: 2rem;
    }

    .text-wrapper {
      padding-top: 3rem;
      width: 100%;
    }

    .title {
      font-size: 2.25rem;
      /* text-4xl */
      font-weight: 600;
      /* font-semibold */
      color: #465462;
    }

    .subtitle {
      font-size: 1.3rem;
      text-align: justify;
      font-weight: 400;
      padding-top: 0.75rem;
      width: 100%;
      color: #465462
    }

    /* Simple fade-in animation */
    @keyframes fade-in {
      from {
        opacity: 0;
        transform: translateY(10px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    /* From Uiverse.io by lenfear23 */
    .auth-btn {
      border: 3px solid #465462;
      background-color: transparent;
      font-size: 1rem;
      padding: 5px 20px;
      border-radius: 5px;
      font-family: "Quicksand";
      transition: all 1s ease-in-out;
      cursor: pointer;
      color: black;
    }

    .auth-btn:hover {
      background-color: black;
      color: white;
    }

    /* Login Modal Styles */
    .modal-overlay {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.5);
      z-index: 1001;
      justify-content: center;
      align-items: center;
    }

    .modal-content {
      background-color: white;
      padding: 2rem;
      border-radius: 10px;
      width: 90%;
      max-width: 400px;
      position: relative;
      animation: modalSlideIn 0.3s ease-out;
    }

    @keyframes modalSlideIn {
      from {
        transform: translateY(-50px);
        opacity: 0;
      }

      to {
        transform: translateY(0);
        opacity: 1;
      }
    }

    .modal-close {
      position: absolute;
      top: 10px;
      right: 10px;
      font-size: 1.5rem;
      cursor: pointer;
      color: #666;
      transition: color 0.3s;
    }

    .modal-close:hover {
      color: #333;
    }

    .login-form,
    .register-form {
      display: flex;
      flex-direction: column;
      gap: 1rem;
    }

    .form-group {
      display: flex;
      flex-direction: column;
      gap: 0.5rem;
    }

    .form-group label {
      font-weight: 600;
      color: #333;
    }

    .form-group input,
    .form-group select {
      padding: 0.75rem;
      border: 2px solid #ddd;
      border-radius: 5px;
      font-size: 1rem;
      transition: border-color 0.3s;
    }

    .form-group input:focus,
    .form-group select:focus {
      outline: none;
      border-color: #96ADC5;
    }

    .role-specific-fields {
      display: none;
      animation: fadeIn 0.3s ease-in-out;
    }

    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: translateY(-10px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .role-specific-fields.active {
      display: block;
    }

    .login-submit,
    .register-submit {
      background-color: #465462;
      color: white;
      padding: 0.75rem;
      border: none;
      border-radius: 5px;
      font-size: 1rem;
      font-weight: 600;
      cursor: pointer;
      transition: background-color 0.3s;
    }

    .login-submit:hover,
    .register-submit:hover {
      background-color: #96ADC5;
    }

    .forgot-password {
      text-align: right;
      color: #666;
      text-decoration: none;
      font-size: 0.9rem;
    }

    .forgot-password:hover {
      text-decoration: underline;
    }

    .switch-form {
      text-align: center;
      margin-top: 1rem;
      color: #666;
    }

    .switch-form a {
      color: #465462;
      text-decoration: none;
      font-weight: 600;
    }

    .switch-form a:hover {
      text-decoration: underline;
    }
  </style>
</head>

<body>
  <?php include 'components/navbar.php'; ?>

  <div class="main">
    <?php include 'components/carousel.php'; ?>
    <div class="" id="our-mission" style="width: 100%; padding:100px;border-top: 10px solid #465462;  background-color: aliceblue;">
      <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 1.5rem; padding-top: 2rem; padding-bottom: 1rem; margin-top: 0rem;white-space: nowrap;">
        <div style="font-size: 6rem; font-weight: bold; font-family: 'Quicksand', serif; padding: 0 2rem; text-align: center; max-width: 450px; font-size: 3rem;">
          Our
          <span
            style="font-family: 'Dancing Script', cursive; font-size: 7rem; max-width: 450px; font-size: 5rem; color: #96ADC5; text-shadow: 1px 1px 2px rgba(0, 0, 0, 1)">
            Mission
          </span>
          !
        </div>
      </div>
      <div class="custom-container" style="width: 100%;">
        <div class="text-wrapper">
          <h1 class="title">Taleemi Sahara Vision</h1>
          <p class="subtitle">
            Our vision is to build a brighter future by supporting underprivileged students through education. We aim to become a leading organization known for providing scholarships, financial assistance, and academic support to deserving individuals. Through our commitment to social impact, we envision a society where no child is deprived of education due to financial hardship. Taleemi Sahara strives to bridge the gap between potential and opportunity, empowering students to reach their full potential and become positive contributors to their communities.
          </p>
        </div>
        <div class="text-wrapper">
          <h1 class="title">Taleemi Sahara Mission</h1>
          <p class="subtitle">
            Our mission is to support deserving and economically challenged students by providing scholarships, educational resources, and mentorship. We are committed to enabling equal access to quality education, especially for those who face financial and social barriers. By partnering with generous donors, institutions, and compassionate individuals, we aim to fund the education of students who show promise and dedication. At the heart of our mission lies the belief that education is a right, not a privilege, and our goal is to transform lives through learning, one student at a time.
          </p>
        </div>
      </div>
    </div>

    <section id="our-impacts" class="impact-section" style="border-top: 5px solid #96ADC5;">
      <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 1.5rem; padding-top: 2rem; padding-bottom: 1rem; margin-top: 0rem; white-space: nowrap;">
        <div style="font-size: 6rem; font-weight: bold; font-family: 'Quicksand', serif; padding: 0 2rem; text-align: center; font-size: 3rem;">
          Our
          <span
            style="font-family: 'Dancing Script', cursive; font-size: 7rem; max-width: 450px; font-size: 5rem; color: #96ADC5; text-shadow: 1px 1px 2px rgba(0, 0, 0, 1)">
            Impact
          </span>
          !
        </div>
      </div>
      <div class="impact-stats">

        <div style="text-align: center; color: white; background: #465462; border: 5px solid #96ADC5; padding: 1.5rem 2.5rem; border-radius: 20px; width: 80%;">
          <div style="font-size: 2rem; font-weight: bold; font-family: \'Quicksand\', serif;">
            1000 +
          </div>
          <div style="font-size: 1rem; font-weight: 300; white-space: nowrap;">
            Student Supported!
          </div>
        </div>
        <div style="text-align: center; color: white; background: #465462; border: 5px solid #96ADC5; padding: 1.5rem 2.5rem; border-radius: 20px; width: 80%;">
          <div style="font-size: 2rem; font-weight: bold; font-family: \'Quicksand\', serif;">
            500 +
          </div>
          <div style="font-size: 1rem; font-weight: 300; white-space: nowrap;">
            Active Donors!
          </div>
        </div>
        <div style="text-align: center; color: white; background: #465462; border: 5px solid #96ADC5; padding: 1.5rem 2.5rem; border-radius: 20px; width: 80%;">
          <div style="font-size: 2rem; font-weight: bold; font-family: \'Quicksand\', serif;">
            200 +
          </div>
          <div style="font-size: 1rem; font-weight: 300; white-space: nowrap;">
            Dedicated Mentors
          </div>
        </div>
      </div>
    </section>
    <div class="" id="our-team" style="width: 100%;padding:100px;border-top: 5px solid #96ADC5; background-color: aliceblue;">
      <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 1.5rem; padding-top: 2rem; padding-bottom: 1rem; background-color: aliceblue; margin-top: 0rem; white-space: nowrap;">
        <div style="font-size: 6rem; font-weight: bold; font-family: 'Quicksand', serif; padding: 0 2rem; text-align: center; max-width: 450px; font-size: 3rem;">
          Meet Our
          <span
            style="font-family: 'Dancing Script', cursive; font-size: 7rem; max-width: 450px; font-size: 5rem; color: #96ADC5; text-shadow: 1px 1px 2px rgba(0, 0, 0, 1)">
            Team
          </span>
          !
        </div>
      </div>
      <div style="display: flex; gap:20px; justify-content: center;  align-items: center; background-color: aliceblue; padding: 20px; flex-wrap: wrap;">
        <?php
        $teamMembers = [
          [
            'name' => 'Hina 1',
            'role' => 'Founder / CEO',
            'image' => './images/5.jpeg',
          ],
          [
            'name' => 'Hina 2',
            'role' => 'Co-Founder',
            'image' => './images/5.jpeg',
          ],
          [
            'name' => 'Hina',
            'role' => 'Director',
            'image' => './images/5.jpeg',
          ],
        ];

        foreach ($teamMembers as $member) {
          echo '<div style="width: 400px; height: 400px; position: relative;">';
          echo '<img src="' . $member['image'] . '" alt="" style="border-radius: 100%; width: 400px; height: 400px; object-fit: cover; border: 10px solid #96ADC5">';
          echo '<div style="width: 100%; position: absolute; bottom: -5px; left: 0; height: fit-content; display: flex; justify-content: center;">';
          echo '<div style="text-align: center; color: white; background: #465462; border: 5px solid #96ADC5; padding: 1.5rem 2.5rem; border-radius: 20px; width: 80%;">';
          echo '<div style="font-size: 1.2rem; font-weight: bold; font-family: \'Quicksand\', serif;">';
          echo $member['name'];
          echo '</div>';
          echo '<div style="font-size: 1rem; font-weight: 300; white-space: nowrap;">';
          echo $member['role'];
          echo '</div>';
          echo '</div>';
          echo '</div>';
          echo '</div>';
        }
        ?>
      </div>
      <!-- <div style="background-color: aliceblue; display: flex; justify-content: center; padding: 20px;">
        <button class="c-button c-button--gooey"> Show More
          <div class="c-button__blobs">
            <div></div>
            <div></div>
            <div></div>
          </div>
        </button>
        <svg xmlns="http://www.w3.org/2000/svg" version="1.1" style="display: block; height: 0; width: 0;">
          <defs>
            <filter id="goo">
              <feGaussianBlur in="SourceGraphic" stdDeviation="10" result="blur"></feGaussianBlur>
              <feColorMatrix in="blur" mode="matrix" values="1 0 0 0 0  0 1 0 0 0  0 0 1 0 0  0 0 0 18 -7" result="goo"></feColorMatrix>
              <feBlend in="SourceGraphic" in2="goo"></feBlend>
            </filter>
          </defs>
        </svg>
      </div> -->


    </div>
    <?php include 'components/news-carousel.php'; ?>

    <?php include 'components/footer.php'; ?>

    <!-- Add this right after the opening body tag -->
    <div class="modal-overlay" id="loginModal">
      <div class="modal-content">
        <span class="modal-close" onclick="closeLoginModal()">&times;</span>
        <h2 style="margin-bottom: 1.5rem; color: #465462;">Login</h2>
        <form class="login-form">
          <div class="form-group">
            <label for="login-role">Role</label>
            <select id="login-role" name="role" required onchange="showLoginRoleFields(this.value)">
              <option value="">Select Role</option>
              <option value="student">Student</option>
              <option value="mentor">Mentor</option>
              <option value="examination">Examination</option>
              <option value="donor">Donor</option>
            </select>
          </div>

          <!-- Student Fields -->
          <div id="login-student-fields" class="role-specific-fields">
            <div class="form-group">
              <label for="student-id">Student ID</label>
              <input type="text" id="student-id" name="student_id">
            </div>
          </div>

          <!-- Mentor Fields -->
          <div id="login-mentor-fields" class="role-specific-fields">
            <div class="form-group">
              <label for="mentor-id">Mentor ID</label>
              <input type="text" id="mentor-id" name="mentor_id">
            </div>
          </div>

          <!-- Examination Fields -->
          <div id="login-examination-fields" class="role-specific-fields">
            <div class="form-group">
              <label for="exam-center">Exam Center</label>
              <input type="text" id="exam-center" name="exam_center">
            </div>
          </div>

          <!-- Donor Fields -->
          <div id="login-donor-fields" class="role-specific-fields">
            <div class="form-group">
              <label for="donor-id">Donor ID</label>
              <input type="text" id="donor-id" name="donor_id">
            </div>
          </div>

          <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required>
          </div>
          <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
          </div>
          <a href="#" class="forgot-password">Forgot Password?</a>
          <button type="submit" class="login-submit">Login</button>
          <div class="switch-form">
            Don't have an account? <a href="#" onclick="switchToRegister()">Register here</a>
          </div>
        </form>
      </div>
    </div>

    <div class="modal-overlay" id="registerModal">
      <div class="modal-content">
        <span class="modal-close" onclick="closeRegisterModal()">&times;</span>
        <h2 style="margin-bottom: 1.5rem; color: #465462;">Register</h2>
        <form class="register-form">
          <div class="form-group">
            <label for="register-role">Role</label>
            <select id="register-role" name="role" required onchange="showRegisterRoleFields(this.value)">
              <option value="">Select Role</option>
              <option value="student">Student</option>
              <option value="mentor">Mentor</option>
              <option value="examination">Examination</option>
              <option value="donor">Donor</option>
            </select>
          </div>

          <!-- Student Fields -->
          <div id="register-student-fields" class="role-specific-fields">
            <div class="form-group">
              <label for="register-student-id">Student ID</label>
              <input type="text" id="register-student-id" name="student_id">
            </div>
            <div class="form-group">
              <label for="register-grade">Grade/Class</label>
              <input type="text" id="register-grade" name="grade">
            </div>
            <div class="form-group">
              <label for="register-school">School/College</label>
              <input type="text" id="register-school" name="school">
            </div>
          </div>

          <!-- Mentor Fields -->
          <div id="register-mentor-fields" class="role-specific-fields">
            <div class="form-group">
              <label for="register-mentor-id">Mentor ID</label>
              <input type="text" id="register-mentor-id" name="mentor_id">
            </div>
            <div class="form-group">
              <label for="register-specialization">Specialization</label>
              <input type="text" id="register-specialization" name="specialization">
            </div>
            <div class="form-group">
              <label for="register-experience">Years of Experience</label>
              <input type="number" id="register-experience" name="experience" min="0">
            </div>
          </div>

          <!-- Examination Fields -->
          <div id="register-examination-fields" class="role-specific-fields">
            <div class="form-group">
              <label for="register-exam-center">Exam Center Name</label>
              <input type="text" id="register-exam-center" name="exam_center">
            </div>
            <div class="form-group">
              <label for="register-center-address">Center Address</label>
              <input type="text" id="register-center-address" name="center_address">
            </div>
            <div class="form-group">
              <label for="register-center-capacity">Center Capacity</label>
              <input type="number" id="register-center-capacity" name="center_capacity" min="1">
            </div>
          </div>

          <!-- Donor Fields -->
          <div id="register-donor-fields" class="role-specific-fields">
            <div class="form-group">
              <label for="register-donor-id">Donor ID</label>
              <input type="text" id="register-donor-id" name="donor_id">
            </div>
            <div class="form-group">
              <label for="register-organization">Organization (if any)</label>
              <input type="text" id="register-organization" name="organization">
            </div>
            <div class="form-group">
              <label for="register-donation-type">Preferred Donation Type</label>
              <select id="register-donation-type" name="donation_type">
                <option value="scholarship">Scholarship</option>
                <option value="equipment">Equipment</option>
                <option value="infrastructure">Infrastructure</option>
                <option value="other">Other</option>
              </select>
            </div>
          </div>

          <div class="form-group">
            <label for="fullname">Full Name</label>
            <input type="text" id="fullname" name="fullname" required>
          </div>
          <div class="form-group">
            <label for="register-email">Email</label>
            <input type="email" id="register-email" name="email" required>
          </div>
          <div class="form-group">
            <label for="register-password">Password</label>
            <input type="password" id="register-password" name="password" required>
          </div>
          <div class="form-group">
            <label for="confirm-password">Confirm Password</label>
            <input type="password" id="confirm-password" name="confirm-password" required>
          </div>
          <button type="submit" class="register-submit">Register</button>
          <div class="switch-form">
            Already have an account? <a href="#" onclick="switchToLogin()">Login here</a>
          </div>
        </form>
      </div>
    </div>

    <script>
      // Modal Functions
      function openLoginModal() {
        document.getElementById('loginModal').style.display = 'flex';
        document.body.style.overflow = 'hidden';
      }

      function closeLoginModal() {
        document.getElementById('loginModal').style.display = 'none';
        document.body.style.overflow = 'auto';
      }

      function openRegisterModal() {
        document.getElementById('registerModal').style.display = 'flex';
        document.body.style.overflow = 'hidden';
      }

      function closeRegisterModal() {
        document.getElementById('registerModal').style.display = 'none';
        document.body.style.overflow = 'auto';
      }

      // Show role-specific fields for login
      function showLoginRoleFields(role) {
        // Hide all role-specific fields first
        document.querySelectorAll('#loginModal .role-specific-fields').forEach(field => {
          field.classList.remove('active');
        });

        // Show selected role fields
        if (role) {
          document.getElementById(`login-${role}-fields`).classList.add('active');
        }
      }

      // Show role-specific fields for registration
      function showRegisterRoleFields(role) {
        // Hide all role-specific fields first
        document.querySelectorAll('#registerModal .role-specific-fields').forEach(field => {
          field.classList.remove('active');
        });

        // Show selected role fields
        if (role) {
          document.getElementById(`register-${role}-fields`).classList.add('active');
        }
      }

      // Switch between modals
      function switchToRegister() {
        closeLoginModal();
        openRegisterModal();
        document.getElementById('register-role').value = '';
        showRegisterRoleFields('');
      }

      function switchToLogin() {
        closeRegisterModal();
        openLoginModal();
        document.getElementById('login-role').value = '';
        showLoginRoleFields('');
      }

      // Close modals when clicking outside
      document.getElementById('loginModal').addEventListener('click', function(e) {
        if (e.target === this) {
          closeLoginModal();
        }
      });

      document.getElementById('registerModal').addEventListener('click', function(e) {
        if (e.target === this) {
          closeRegisterModal();
        }
      });

      // Close modals with Escape key
      document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
          closeLoginModal();
          closeRegisterModal();
        }
      });

      // Add click events to buttons
      document.querySelectorAll('.auth-btn').forEach(button => {
        button.addEventListener('click', function() {
          if (this.textContent.trim() === 'Login') {
            openLoginModal();
          } else if (this.textContent.trim() === 'Register') {
            openRegisterModal();
          }
        });
      });
    </script>
</body>

</html>