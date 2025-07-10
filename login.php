<?php
session_start();
require_once 'config/database.php';

if (isset($_SESSION['user_id'])) {
    switch ($_SESSION['user_type']) {
        case 'student':
            header("Location: student/dashboard.php");
            exit();
        case 'admin':
            header("Location: admin/dashboard.php");
            exit();
        case 'donor':
            header("Location: donor/dashboard.php");
            exit();
        case 'mentor':
            header("Location: mentor/dashboard.php");
            exit();
        case 'examination':
            header("Location: examination/dashboard.php");
            exit();
        default:
            header("Location: index.php");
            exit();
    }
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = sanitize($_POST['email']);
    $password = sanitize($_POST['password']);
    $role = sanitize($_POST['role']);

    if (empty($email) || empty($password) || empty($role)) {
        $error = "All fields are required";
    } else {
        // Get user from database
        $stmt = $conn->prepare("SELECT id, name, email, password, user_type FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();

            if ($user['user_type'] !== $role) {
                $error = "Invalid role selected for this account.";
            } elseif (verifyPassword($password, $user['password'])) {
                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_type'] = $user['user_type'];

                // Redirect based on user type
                switch ($user['user_type']) {
                    case 'student':
                        header("Location: student/dashboard.php");
                        break;
                    case 'donor':
                        header("Location: donor/dashboard.php");
                        break;
                    case 'mentor':
                        header("Location: mentor/dashboard.php");
                        break;
                    case 'admin':
                        header("Location: admin/dashboard.php");
                        break;
                    case 'examination':
                        header("Location: examination/dashboard.php");
                        break;
                    default:
                        header("Location: index.php");
                }
                exit();
            } else {
                $error = "Invalid email or password";
            }
        } else {
            $error = "Invalid email or password";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - EduConnect</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300..700&family=Raleway:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@400..700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="./css/font.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        #hero {
            padding: 150px 0px;
            font-family: "Raleway", sans-serif !important;
        }

        .login-container {
            max-width: 400px;
            margin: 100px auto;
            padding: 2rem;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--text-color);
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
        }

        .error-message {
            color: #dc3545;
            margin-bottom: 1rem;
        }

        .login-btn {
            width: 100%;
            padding: 10px 0rem;
            background-color: #5a4ae3;
            color: white;
            border: 2px solid #5a4ae3;
            border-radius: 5px;
            font-size: 1rem;
            font-weight: bolder;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .login-btn:hover {
            background-color: white;
            color: #5a4ae3;
        }

        .register-link {
            text-align: center;
            margin-top: 1rem;
        }

        .forgot-password {
            text-align: right;
            margin-top: -1rem;
            margin-bottom: 1rem;
        }

        .forgot-password a {
            color: var(--primary-color);
            text-decoration: none;
            font-size: 0.9rem;
        }
    </style>
</head>

<body>
    <?php include 'components/navbar.php'; ?>
    <section id="hero" class="hero-section">
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
        <div class="login-container" style="position: relative; z-index: 2">
            <h2>Welcome Back</h2>
            <?php if ($error): ?>
                <div class="error-message"><?php echo $error; ?></div>
            <?php endif; ?>

            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                <div class="form-group">
                    <label for="role">Role</label>
                    <select id="role" name="role" required>
                        <option value="">Select Role</option>
                        <option value="admin">Admin</option>
                        <option value="student">Student</option>
                        <option value="mentor">Mentor</option>
                        <option value="examination">Examination</option>
                        <option value="donor">Donor</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" required placeholder="Enter Your Email...">
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="****************" required>
                </div>

                <div class="forgot-password">
                    <a href="forgot-password.php">Forgot Password?</a>
                </div>

                <button type="submit" class="login-btn">Login</button>
            </form>

            <div class="register-link">
                Don't have an account? <a href="register.php">Register here</a>
            </div>
        </div>
    </section>

    <script src="js/main.js"></script>
</body>

</html>