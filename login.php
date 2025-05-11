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

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = sanitize($_POST['email']);
    $password = sanitize($_POST['password']);

    if (empty($email) || empty($password)) {
        $error = "All fields are required";
    } else {
        // Get user from database
        $stmt = $conn->prepare("SELECT id, name, email, password, user_type FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();

            if (verifyPassword($password, $user['password'])) {
                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_type'] = $user['user_type'];

                // Redirect based on user type
                switch ($user['user_type']) {
                    case 'student':
                        header("Location: dashboard/student.php");
                        break;
                    case 'donor':
                        header("Location: dashboard/donor.php");
                        break;
                    case 'mentor':
                        header("Location: dashboard/mentor.php");
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
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .login-container {
            max-width: 400px;
            margin: 100px auto;
            padding: 2rem;
            background-color: var(--white);
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

        .form-group input {
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
            padding: 1rem;
            background-color: var(--primary-color);
            color: var(--white);
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .login-btn:hover {
            background-color: #357abd;
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
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" required>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
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