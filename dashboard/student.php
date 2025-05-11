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

// Get student's scholarship applications
$stmt = $conn->prepare("SELECT * FROM scholarship_applications WHERE student_id = ?");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$applications = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Get available scholarships
$stmt = $conn->prepare("SELECT * FROM scholarships WHERE application_deadline >= CURDATE() AND status = 'active'");
$stmt->execute();
$available_scholarships = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Get student's mentor connections
$stmt = $conn->prepare("SELECT m.*, u.name as mentor_name, u.email as mentor_email 
                       FROM mentor_connections m 
                       JOIN users u ON m.mentor_id = u.id 
                       WHERE m.student_id = ?");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$mentor_connections = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

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
    <title>Student Dashboard - EduConnect</title>
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

        .profile-info {
            margin-bottom: 1rem;
        }

        .profile-info p {
            margin-bottom: 0.5rem;
        }

        .scholarship-list,
        .mentor-list {
            list-style: none;
        }

        .scholarship-list li,
        .mentor-list li {
            padding: 1rem;
            border-bottom: 1px solid var(--light-gray);
        }

        .scholarship-list li:last-child,
        .mentor-list li:last-child {
            border-bottom: none;
        }

        .status-badge {
            display: inline-block;
            padding: 0.25rem 0.5rem;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .status-pending {
            background-color: #ffd700;
            color: #000;
        }

        .status-approved {
            background-color: var(--secondary-color);
            color: var(--white);
        }

        .status-rejected {
            background-color: #dc3545;
            color: var(--white);
        }

        .action-btn {
            padding: 0.5rem 1rem;
            border-radius: 5px;
            text-decoration: none;
            color: var(--white);
            font-weight: 500;
            transition: background-color 0.3s ease;
        }

        .btn-edit {
            background-color: var(--primary-color);
        }

        .btn-apply {
            background-color: var(--secondary-color);
        }

        .btn-connect {
            background-color: var(--tertiary-color);
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
                <h1><a href="../index.html" style="text-decoration: none; color: inherit;">EduConnect</a></h1>
            </div>
            <ul class="nav-links">
                <li><a href="student-profile.php">Profile</a></li>
                <li><a href="student-scholarships.php">Scholarships</a></li>
                <li><a href="student-mentors.php">Mentors</a></li>
                <li><a href="student-messages.php" class="notification-badge" data-count="<?php echo $unread_messages; ?>">Messages</a></li>
                <li><a href="../logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>

    <div class="dashboard-container">
        <div class="dashboard-header">
            <div class="welcome-message">
                Welcome, <?php echo htmlspecialchars($student['name']); ?>!
            </div>
            <div class="dashboard-actions">
                <a href="edit-profile.php" class="action-btn btn-edit">Edit Profile</a>
            </div>
        </div>

        <div class="dashboard-grid">
            <!-- Profile Card -->
            <div class="dashboard-card">
                <h3>My Profile</h3>
                <div class="profile-info">
                    <p><strong>Name:</strong> <?php echo htmlspecialchars($student['name']); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($student['email']); ?></p>
                    <p><strong>Member Since:</strong> <?php echo date('F Y', strtotime($student['created_at'])); ?></p>
                </div>
            </div>

            <!-- Scholarship Applications -->
            <div class="dashboard-card">
                <h3>My Scholarship Applications</h3>
                <ul class="scholarship-list">
                    <?php if (empty($applications)): ?>
                        <li>No applications yet</li>
                    <?php else: ?>
                        <?php foreach ($applications as $application): ?>
                            <li>
                                <div class="scholarship-info">
                                    <h4><?php echo htmlspecialchars($application['scholarship_name']); ?></h4>
                                    <p>Applied: <?php echo date('M d, Y', strtotime($application['applied_date'])); ?></p>
                                    <span class="status-badge status-<?php echo strtolower($application['status']); ?>">
                                        <?php echo ucfirst($application['status']); ?>
                                    </span>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>

            <!-- Available Scholarships -->
            <div class="dashboard-card">
                <h3>Available Scholarships</h3>
                <ul class="scholarship-list">
                    <?php if (empty($available_scholarships)): ?>
                        <li>No scholarships available at the moment</li>
                    <?php else: ?>
                        <?php foreach ($available_scholarships as $scholarship): ?>
                            <li>
                                <div class="scholarship-info">
                                    <h4><?php echo htmlspecialchars($scholarship['name']); ?></h4>
                                    <p>Amount: $<?php echo number_format($scholarship['amount']); ?></p>
                                    <p>Deadline: <?php echo date('M d, Y', strtotime($scholarship['application_deadline'])); ?></p>
                                    <a href="apply-scholarship.php?id=<?php echo $scholarship['id']; ?>" class="action-btn btn-apply">Apply Now</a>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>

            <!-- Mentor Connections -->
            <div class="dashboard-card">
                <h3>My Mentors</h3>
                <ul class="mentor-list">
                    <?php if (empty($mentor_connections)): ?>
                        <li>No mentor connections yet</li>
                    <?php else: ?>
                        <?php foreach ($mentor_connections as $connection): ?>
                            <li>
                                <div class="mentor-info">
                                    <h4><?php echo htmlspecialchars($connection['mentor_name']); ?></h4>
                                    <p><?php echo htmlspecialchars($connection['mentor_email']); ?></p>
                                    <a href="chat.php?mentor_id=<?php echo $connection['mentor_id']; ?>" class="action-btn btn-connect">Chat</a>
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
