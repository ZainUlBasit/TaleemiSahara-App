<?php
session_start();
require_once 'config/database.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['step']) && $_POST['step'] == 'email') {
        // Step 1: Email verification
        $email = sanitize($_POST['email']);

        if (empty($email)) {
            $error = "Email is required";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Invalid email format";
        } else {
            // Check if email exists in database
            $stmt = $conn->prepare("SELECT id, name, email FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows == 1) {
                $user = $result->fetch_assoc();

                // Generate reset token
                $reset_token = bin2hex(random_bytes(32));
                $reset_expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

                // Store reset token in database
                $update_stmt = $conn->prepare("UPDATE users SET reset_token = ?, reset_expires = ? WHERE email = ?");
                $update_stmt->bind_param("sss", $reset_token, $reset_expires, $email);
                $update_stmt->execute();

                // Send request to node backend in JSON format
                $data = json_encode(array('to' => $email, 'subject' => 'Password Reset', 'message' => $reset_token));
                $options = array(
                    'http' => array(
                        'header'  => "Content-type: application/json\r\n",
                        'method'  => 'POST',
                        'content' => $data
                    )
                );
                $context  = stream_context_create($options);
                $result = file_get_contents('https://golden-plus-app-backend.vercel.app/api/auth/send-token-php', false, $context);

                if ($update_stmt->execute()) {
                    // In a real application, you would send an email here
                    // For demo purposes, we'll show the token
                    $_SESSION['reset_email'] = $email;
                    $_SESSION['reset_token'] = $reset_token;
                    $success = "Reset instructions have been sent to your email. For demo purposes, your reset token is: " . $reset_token;
                } else {
                    $error = "Failed to process request. Please try again.";
                }
                $update_stmt->close();
            } else {
                $error = "Email not found in our system";
            }
            $stmt->close();
        }
    } elseif (isset($_POST['step']) && $_POST['step'] == 'reset') {
        // Step 2: Password reset
        $token = sanitize($_POST['token']);
        $new_password = sanitize($_POST['new_password']);
        $confirm_password = sanitize($_POST['confirm_password']);

        if (empty($token) || empty($new_password) || empty($confirm_password)) {
            $error = "All fields are required";
        } elseif ($new_password !== $confirm_password) {
            $error = "Passwords do not match";
        } elseif (strlen($new_password) < 6) {
            $error = "Password must be at least 6 characters long";
        } else {
            // Verify token and check if it's not expired
            $stmt = $conn->prepare("SELECT id, email FROM users WHERE reset_token = ?");
            $stmt->bind_param("s", $token);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows == 1) {
                $user = $result->fetch_assoc();

                // Hash new password
                $hashed_password = hashPassword($new_password);

                // Update password and clear reset token
                $update_stmt = $conn->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_expires = NULL WHERE id = ?");
                $update_stmt->bind_param("si", $hashed_password, $user['id']);

                if ($update_stmt->execute()) {
                    $success = "Password has been reset successfully! You can now login with your new password.";
                    // Clear session
                    unset($_SESSION['reset_email']);
                    unset($_SESSION['reset_token']);
                } else {
                    $error = "Failed to reset password. Please try again.";
                }
                $update_stmt->close();
            } else {
                $error = "Invalid or expired reset token";
            }
            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Taleemi Sahara</title>
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

        .forgot-container {
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
            font-weight: 600;
        }

        .form-group input {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }

        .form-group input:focus {
            outline: none;
            border-color: #5a4ae3;
            box-shadow: 0 0 0 2px rgba(90, 74, 227, 0.2);
        }

        .error-message {
            color: #dc3545;
            margin-bottom: 1rem;
            padding: 0.75rem;
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            border-radius: 5px;
        }

        .success-message {
            color: #155724;
            margin-bottom: 1rem;
            padding: 0.75rem;
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            border-radius: 5px;
        }

        .reset-btn {
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

        .reset-btn:hover {
            background-color: white;
            color: #5a4ae3;
        }

        .back-to-login {
            text-align: center;
            margin-top: 1rem;
        }

        .back-to-login a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
        }

        .back-to-login a:hover {
            text-decoration: underline;
        }

        .step-indicator {
            display: flex;
            justify-content: center;
            margin-bottom: 2rem;
        }

        .step {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background-color: #ddd;
            color: #666;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            margin: 0 10px;
            transition: all 0.3s ease;
        }

        .step.active {
            background-color: #5a4ae3;
            color: white;
        }

        .step.completed {
            background-color: #28a745;
            color: white;
        }

        .token-display {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 1rem;
            margin-bottom: 1rem;
            font-family: monospace;
            word-break: break-all;
            font-size: 0.9rem;
        }

        .password-requirements {
            font-size: 0.85rem;
            color: #666;
            margin-top: 0.5rem;
        }

        .password-requirements ul {
            margin: 0.5rem 0;
            padding-left: 1.5rem;
        }

        .password-requirements li {
            margin-bottom: 0.25rem;
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
        <div class="forgot-container" style="position: relative; z-index: 2">
            <h2 style="text-align: center; margin-bottom: 2rem; color: #333;">Reset Your Password</h2>

            <div class="step-indicator">
                <div class="step <?php echo (!isset($_SESSION['reset_email']) ? 'active' : 'completed'); ?>">1</div>
                <div class="step <?php echo (isset($_SESSION['reset_email']) ? 'active' : ''); ?>">2</div>
            </div>

            <?php if ($error): ?>
                <div class="error-message"><?php echo $error; ?></div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="success-message"><?php echo $success; ?></div>
            <?php endif; ?>

            <?php if (!isset($_SESSION['reset_email'])): ?>
                <!-- Step 1: Email Verification -->
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                    <input type="hidden" name="step" value="email">
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" required placeholder="Enter your registered email...">
                    </div>
                    <button type="submit" class="reset-btn">Send Reset Link</button>
                </form>
            <?php else: ?>
                <!-- Step 2: Password Reset -->
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                    <input type="hidden" name="step" value="reset">
                    <div class="form-group">
                        <label for="token">Reset Token</label>
                        <input type="text" id="token" name="token" required placeholder="Enter the reset token...">
                        <div class="password-requirements">
                            <strong>Note:</strong> In a real application, this token would be sent to your email. For demo purposes, it's displayed above.
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="new_password">New Password</label>
                        <input type="password" id="new_password" name="new_password" required placeholder="Enter new password...">
                        <div class="password-requirements">
                            <strong>Password requirements:</strong>
                            <ul>
                                <li>At least 6 characters long</li>
                                <li>Should be different from your current password</li>
                            </ul>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="confirm_password">Confirm New Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" required placeholder="Confirm new password...">
                    </div>
                    <button type="submit" class="reset-btn">Reset Password</button>
                </form>
            <?php endif; ?>

            <div class="back-to-login">
                <a href="login.php"><i class="fas fa-arrow-left"></i> Back to Login</a>
            </div>
        </div>
    </section>

    <script src="js/main.js"></script>
    <script>
        // Password strength validation
        document.addEventListener('DOMContentLoaded', function() {
            const newPassword = document.getElementById('new_password');
            const confirmPassword = document.getElementById('confirm_password');

            if (newPassword && confirmPassword) {
                confirmPassword.addEventListener('input', function() {
                    if (newPassword.value !== confirmPassword.value) {
                        confirmPassword.setCustomValidity('Passwords do not match');
                    } else {
                        confirmPassword.setCustomValidity('');
                    }
                });

                newPassword.addEventListener('input', function() {
                    if (this.value.length < 6) {
                        this.setCustomValidity('Password must be at least 6 characters long');
                    } else {
                        this.setCustomValidity('');
                    }

                    // Trigger confirm password validation
                    if (confirmPassword.value) {
                        confirmPassword.dispatchEvent(new Event('input'));
                    }
                });
            }
        });
    </script>
</body>

</html>