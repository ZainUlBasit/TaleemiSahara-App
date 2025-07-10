<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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
        case 'examination':
            header("Location: dashboard/examination.php");
            exit();
        default:
            header("Location: index.php");
            exit();
    }
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = sanitize($_POST['name']);
    $email = sanitize($_POST['email']);
    $password = sanitize($_POST['password']);
    $confirm_password = sanitize($_POST['confirm_password']);
    $user_type = sanitize($_POST['user_type']);

    // Validate input
    if (empty($name) || empty($email) || empty($password) || empty($user_type)) {
        $error = "All fields are required";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format";
    } else {
        // Check if email already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $error = "Email already exists";
        } else {
            // Insert new user
            $hashed_password = hashPassword($password);
            $stmt = $conn->prepare("INSERT INTO users (name, email, password, user_type) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $name, $email, $hashed_password, $user_type);

            if ($stmt->execute()) {
                $success = "Registration successful! Please login.";
                header("refresh:2;url=index.php");
            } else {
                $error = "Registration failed. Please try again.";
            }
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
    <title>Register - EduConnect</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300..700&family=Raleway:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@400..700&display=swap" rel="stylesheet" />


    <link rel="stylesheet" href="./css/font.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        .main {
            padding: 150px 0px;
            font-family: "Raleway", sans-serif !important;
        }

        .register-container {
            max-width: 500px;
            /* margin-top: 100px; */
            margin: 0px auto;
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

        .success-message {
            color: #28a745;
            margin-bottom: 1rem;
        }

        .register-btn {
            width: 100%;
            padding: 10px 0rem;
            background-color: #5a4ae3;
            color: white;
            border: 2px solid #5a4ae3;
            border-radius: 5px;
            font-size: 1rem;
            font-weight: bolder;
            cursor: pointer;
            transition: all 1s ease;
        }

        .register-btn:hover {
            background-color: white;
            color: #5a4ae3;
        }


        .login-link {
            text-align: center;
            margin-top: 1rem;
        }
    </style>
</head>

<body>
    <?php include 'components/navbar.php'; ?>
    <div class="main">
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

        <div class="register-container" style="position: relative; z-index: 2">
            <h2>Create an Account</h2>
            <?php if ($error): ?>
                <div class="error-message"><?php echo $error; ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="success-message"><?php echo $success; ?></div>
            <?php endif; ?>

            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                <div class="form-group">
                    <label for="user_type">I am a</label>
                    <select id="user_type" name="user_type" required>
                        <option value="">Select your role</option>
                        <option value="admin" <?php echo isset($_GET['type']) && $_GET['type'] == 'admin' ? 'selected' : ''; ?>>
                            Admin
                        </option>
                        <option value="student" <?php echo isset($_GET['type']) && $_GET['type'] == 'student' ? 'selected' : ''; ?>>
                            Student
                        </option>
                        <option value="donor" <?php echo isset($_GET['type']) && $_GET['type'] == 'donor' ? 'selected' : ''; ?>>
                            Donor
                        </option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="name">Full Name</label>
                    <input type="text" id="name" name="name" required>
                </div>

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" required>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>



                <button type="submit" class="register-btn">Create Account</button>
            </form>

            <!-- <div class="login-link">
            Already have an account? <a href="login.php">Login here</a>
        </div> -->
        </div>
    </div>

    <?php include 'components/footer.php'; ?>

    <script src="js/main.js"></script>
</body>

</html>