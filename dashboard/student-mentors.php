<?php
session_start();
require_once '../config/database.php';

// Check if user is logged in and is a student
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'student') {
    header("Location: ../login.php");
    exit();
}

// Get student details
$student_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ? AND user_type = 'student'");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();

// Get student's current mentors
$stmt = $conn->prepare("SELECT u.*, mp.expertise, mp.bio, mc.status 
                       FROM mentor_connections mc 
                       JOIN users u ON mc.mentor_id = u.id 
                       JOIN mentor_profiles mp ON u.id = mp.user_id 
                       WHERE mc.student_id = ? 
                       AND mc.status = 'active'");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$my_mentors = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Get pending connection requests
$stmt = $conn->prepare("SELECT u.*, mp.expertise, mp.bio, cr.status, cr.id as request_id 
                       FROM connection_requests cr 
                       JOIN users u ON cr.mentor_id = u.id 
                       JOIN mentor_profiles mp ON u.id = mp.user_id 
                       WHERE cr.student_id = ? 
                       AND cr.status = 'pending'");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$pending_requests = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Get available mentors (not connected and no pending requests)
$stmt = $conn->prepare("SELECT u.*, mp.expertise, mp.bio 
                       FROM users u 
                       JOIN mentor_profiles mp ON u.id = mp.user_id 
                       WHERE u.user_type = 'mentor' 
                       AND u.id NOT IN (
                           SELECT mentor_id FROM mentor_connections 
                           WHERE student_id = ? AND status = 'active'
                       ) 
                       AND u.id NOT IN (
                           SELECT mentor_id FROM connection_requests 
                           WHERE student_id = ? AND status = 'pending'
                       )");
$stmt->bind_param("ii", $student_id, $student_id);
$stmt->execute();
$available_mentors = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Get unread messages count
$stmt = $conn->prepare("SELECT COUNT(*) as unread FROM messages WHERE receiver_id = ? AND read_status = 0");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$unread_messages = $stmt->get_result()->fetch_assoc()['unread'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mentors - EduConnect</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .mentors-container {
            max-width: 1200px;
            margin: 80px auto 2rem;
            padding: 0 2rem;
        }

        .section-header {
            margin-bottom: 2rem;
        }

        .mentors-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }

        .mentor-card {
            background-color: var(--white);
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .mentor-header {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
        }

        .mentor-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            margin-right: 1rem;
            background-color: var(--primary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--white);
            font-size: 1.5rem;
        }

        .mentor-info h3 {
            color: var(--primary-color);
            margin-bottom: 0.5rem;
        }

        .mentor-expertise {
            color: var(--secondary-color);
            font-weight: 500;
            margin-bottom: 1rem;
        }

        .mentor-bio {
            color: var(--dark-gray);
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
            line-height: 1.5;
        }

        .action-btn {
            display: inline-block;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            text-decoration: none;
            color: var(--white);
            font-weight: 500;
            transition: background-color 0.3s ease;
            cursor: pointer;
            border: none;
            font-size: 0.9rem;
        }

        .btn-connect {
            background-color: var(--secondary-color);
        }

        .btn-message {
            background-color: var(--primary-color);
            margin-left: 0.5rem;
        }

        .btn-cancel {
            background-color: #dc3545;
        }

        .status-badge {
            display: inline-block;
            padding: 0.25rem 0.5rem;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 500;
            margin-bottom: 1rem;
        }

        .status-pending {
            background-color: #ffd700;
            color: #000;
        }

        .status-active {
            background-color: var(--secondary-color);
            color: var(--white);
        }
    </style>
</head>

<body>
    <header>
        <nav class="navbar">
            <div class="logo">
                <h1><a href="../index.html" style="text-decoration: none; color: inherit;">EduConnect</a></h1>
            </div>
            <ul class="nav-links">
                <li><a href="student.php">Dashboard</a></li>
                <li><a href="student-profile.php">Profile</a></li>
                <li><a href="student-scholarships.php">Scholarships</a></li>
                <li><a href="student-mentors.php" class="active">Mentors</a></li>
                <li><a href="../logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>

    <div class="mentors-container">
        <div class="section-header">
            <h2>My Mentors</h2>
        </div>
        <div class="mentors-grid">
            <?php if (empty($my_mentors)): ?>
                <div class="mentor-card">
                    <p>You don't have any active mentors yet.</p>
                </div>
            <?php else: ?>
                <?php foreach ($my_mentors as $mentor): ?>
                    <div class="mentor-card">
                        <div class="mentor-header">
                            <div class="mentor-avatar">
                                <?php echo strtoupper(substr($mentor['name'], 0, 1)); ?>
                            </div>
                            <div class="mentor-info">
                                <h3><?php echo htmlspecialchars($mentor['name']); ?></h3>
                                <div class="mentor-expertise"><?php echo htmlspecialchars($mentor['expertise']); ?></div>
                            </div>
                        </div>
                        <div class="mentor-bio">
                            <?php echo htmlspecialchars($mentor['bio']); ?>
                        </div>
                        <a href="student-messages.php?mentor_id=<?php echo $mentor['id']; ?>" class="action-btn btn-message">Message</a>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <?php if (!empty($pending_requests)): ?>
            <div class="section-header">
                <h2>Pending Requests</h2>
            </div>
            <div class="mentors-grid">
                <?php foreach ($pending_requests as $request): ?>
                    <div class="mentor-card">
                        <div class="mentor-header">
                            <div class="mentor-avatar">
                                <?php echo strtoupper(substr($request['name'], 0, 1)); ?>
                            </div>
                            <div class="mentor-info">
                                <h3><?php echo htmlspecialchars($request['name']); ?></h3>
                                <div class="mentor-expertise"><?php echo htmlspecialchars($request['expertise']); ?></div>
                            </div>
                        </div>
                        <div class="mentor-bio">
                            <?php echo htmlspecialchars($request['bio']); ?>
                        </div>
                        <span class="status-badge status-pending">Request Pending</span>
                        <form action="cancel-request.php" method="post" style="display: inline;">
                            <input type="hidden" name="request_id" value="<?php echo $request['request_id']; ?>">
                            <button type="submit" class="action-btn btn-cancel">Cancel Request</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="section-header">
            <h2>Available Mentors</h2>
        </div>
        <div class="mentors-grid">
            <?php if (empty($available_mentors)): ?>
                <div class="mentor-card">
                    <p>No available mentors at the moment.</p>
                </div>
            <?php else: ?>
                <?php foreach ($available_mentors as $mentor): ?>
                    <div class="mentor-card">
                        <div class="mentor-header">
                            <div class="mentor-avatar">
                                <?php echo strtoupper(substr($mentor['name'], 0, 1)); ?>
                            </div>
                            <div class="mentor-info">
                                <h3><?php echo htmlspecialchars($mentor['name']); ?></h3>
                                <div class="mentor-expertise"><?php echo htmlspecialchars($mentor['expertise']); ?></div>
                            </div>
                        </div>
                        <div class="mentor-bio">
                            <?php echo htmlspecialchars($mentor['bio']); ?>
                        </div>
                        <form action="send-request.php" method="post" style="display: inline;">
                            <input type="hidden" name="mentor_id" value="<?php echo $mentor['id']; ?>">
                            <button type="submit" class="action-btn btn-connect">Connect</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <script src="../js/main.js"></script>
</body>

</html>