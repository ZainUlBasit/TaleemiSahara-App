<?php
session_start();
require_once '../config/database.php';

// Check if user is logged in and is a mentor
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'mentor') {
    header("Location: ../login.php");
    exit();
}

// Get mentor details
$mentor_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ? AND user_type = 'mentor'");
$stmt->bind_param("i", $mentor_id);
$stmt->execute();
$mentor = $stmt->get_result()->fetch_assoc();

// Get mentor's student connections
$stmt = $conn->prepare("SELECT mc.*, u.name as student_name, u.email as student_email 
                       FROM mentor_connections mc 
                       JOIN users u ON mc.student_id = u.id 
                       WHERE mc.mentor_id = ?");
$stmt->bind_param("i", $mentor_id);
$stmt->execute();
$connections = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Get pending connection requests
$stmt = $conn->prepare("SELECT cr.*, u.name as student_name, u.email as student_email 
                       FROM connection_requests cr 
                       JOIN users u ON cr.student_id = u.id 
                       WHERE cr.mentor_id = ? AND cr.status = 'pending'");
$stmt->bind_param("i", $mentor_id);
$stmt->execute();
$pending_requests = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Get upcoming sessions
$stmt = $conn->prepare("SELECT ms.*, u.name as student_name 
                       FROM mentoring_sessions ms 
                       JOIN users u ON ms.student_id = u.id 
                       WHERE ms.mentor_id = ? AND ms.session_date >= CURDATE() 
                       ORDER BY ms.session_date ASC");
$stmt->bind_param("i", $mentor_id);
$stmt->execute();
$upcoming_sessions = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mentor Dashboard - EduConnect</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .dashboard-container {
            padding: 2rem 5%;
            margin-top: 60px;
        }

        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .welcome-message {
            font-size: 1.5rem;
            color: var(--text-color);
        }

        .dashboard-actions {
            display: flex;
            gap: 1rem;
        }

        .dashboard-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background-color: var(--white);
            border-radius: 10px;
            padding: 1.5rem;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .stat-card h3 {
            color: var(--primary-color);
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
        }

        .dashboard-card {
            background-color: var(--white);
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .dashboard-card h3 {
            color: var(--primary-color);
            margin-bottom: 1rem;
            font-size: 1.2rem;
        }

        .student-list,
        .session-list,
        .request-list {
            list-style: none;
        }

        .student-list li,
        .session-list li,
        .request-list li {
            padding: 1rem;
            border-bottom: 1px solid var(--light-gray);
        }

        .student-list li:last-child,
        .session-list li:last-child,
        .request-list li:last-child {
            border-bottom: none;
        }

        .action-btn {
            padding: 0.5rem 1rem;
            border-radius: 5px;
            text-decoration: none;
            color: var(--white);
            font-weight: 500;
            transition: background-color 0.3s ease;
            display: inline-block;
            margin-top: 0.5rem;
        }

        .btn-schedule {
            background-color: var(--secondary-color);
        }

        .btn-chat {
            background-color: var(--primary-color);
        }

        .btn-accept {
            background-color: var(--secondary-color);
        }

        .btn-reject {
            background-color: #dc3545;
        }

        .session-time {
            color: var(--primary-color);
            font-weight: 500;
        }

        .notification-badge {
            position: relative;
        }

        .notification-badge::after {
            content: attr(data-count);
            position: absolute;
            top: -8px;
            right: -8px;
            background-color: #dc3545;
            color: var(--white);
            border-radius: 50%;
            padding: 0.25rem 0.5rem;
            font-size: 0.8rem;
        }
    </style>
</head>
<body>
    <header>
        <nav class="navbar">
            <div class="logo">
                <h1>EduConnect</h1>
            </div>
            <ul class="nav-links">
                <li><a href="#dashboard">Dashboard</a></li>
                <li><a href="#students">My Students</a></li>
                <li><a href="#sessions">Sessions</a></li>
                <li><a href="#messages" class="notification-badge" data-count="2">Messages</a></li>
                <li><a href="../logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>

    <div class="dashboard-container">
        <div class="dashboard-header">
            <div class="welcome-message">
                Welcome, <?php echo htmlspecialchars($mentor['name']); ?>!
            </div>
            <div class="dashboard-actions">
                <a href="update-availability.php" class="action-btn btn-schedule">Update Availability</a>
            </div>
        </div>

        <div class="dashboard-stats">
            <div class="stat-card">
                <h3><?php echo count($connections); ?></h3>
                <p>Students Mentoring</p>
            </div>
            <div class="stat-card">
                <h3><?php echo count($upcoming_sessions); ?></h3>
                <p>Upcoming Sessions</p>
            </div>
            <div class="stat-card">
                <h3><?php echo count($pending_requests); ?></h3>
                <p>Pending Requests</p>
            </div>
        </div>

        <div class="dashboard-grid">
            <!-- Current Students -->
            <div class="dashboard-card">
                <h3>My Students</h3>
                <ul class="student-list">
                    <?php if (empty($connections)): ?>
                        <li>No student connections yet</li>
                    <?php else: ?>
                        <?php foreach ($connections as $connection): ?>
                            <li>
                                <div class="student-info">
                                    <h4><?php echo htmlspecialchars($connection['student_name']); ?></h4>
                                    <p><?php echo htmlspecialchars($connection['student_email']); ?></p>
                                    <div class="student-actions">
                                        <a href="schedule-session.php?student_id=<?php echo $connection['student_id']; ?>" class="action-btn btn-schedule">Schedule Session</a>
                                        <a href="chat.php?student_id=<?php echo $connection['student_id']; ?>" class="action-btn btn-chat">Chat</a>
                                    </div>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>

            <!-- Upcoming Sessions -->
            <div class="dashboard-card">
                <h3>Upcoming Sessions</h3>
                <ul class="session-list">
                    <?php if (empty($upcoming_sessions)): ?>
                        <li>No upcoming sessions scheduled</li>
                    <?php else: ?>
                        <?php foreach ($upcoming_sessions as $session): ?>
                            <li>
                                <div class="session-info">
                                    <h4><?php echo htmlspecialchars($session['student_name']); ?></h4>
                                    <p class="session-time">
                                        <?php echo date('l, M d, Y', strtotime($session['session_date'])); ?> at 
                                        <?php echo date('g:i A', strtotime($session['session_time'])); ?>
                                    </p>
                                    <p><?php echo htmlspecialchars($session['topic']); ?></p>
                                    <a href="join-session.php?id=<?php echo $session['id']; ?>" class="action-btn btn-schedule">Join Session</a>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>

            <!-- Connection Requests -->
            <div class="dashboard-card">
                <h3>Connection Requests</h3>
                <ul class="request-list">
                    <?php if (empty($pending_requests)): ?>
                        <li>No pending connection requests</li>
                    <?php else: ?>
                        <?php foreach ($pending_requests as $request): ?>
                            <li>
                                <div class="request-info">
                                    <h4><?php echo htmlspecialchars($request['student_name']); ?></h4>
                                    <p><?php echo htmlspecialchars($request['student_email']); ?></p>
                                    <p><?php echo htmlspecialchars($request['message']); ?></p>
                                    <div class="request-actions">
                                        <a href="accept-request.php?id=<?php echo $request['id']; ?>" class="action-btn btn-accept">Accept</a>
                                        <a href="reject-request.php?id=<?php echo $request['id']; ?>" class="action-btn btn-reject">Reject</a>
                                    </div>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>

    <script src="../js/main.js"></script>
</body>
</html> 