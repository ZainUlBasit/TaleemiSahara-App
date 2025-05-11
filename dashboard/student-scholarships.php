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

// Get all available scholarships
$stmt = $conn->prepare("SELECT s.*, u.name as donor_name 
                       FROM scholarships s 
                       JOIN users u ON s.donor_id = u.id 
                       WHERE s.application_deadline >= CURDATE() 
                       AND s.status = 'active'
                       ORDER BY s.application_deadline ASC");
$stmt->execute();
$available_scholarships = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Get student's scholarship applications
$stmt = $conn->prepare("SELECT sa.*, s.name as scholarship_name, s.amount, u.name as donor_name 
                       FROM scholarship_applications sa 
                       JOIN scholarships s ON sa.scholarship_id = s.id 
                       JOIN users u ON s.donor_id = u.id 
                       WHERE sa.student_id = ? 
                       ORDER BY sa.applied_date DESC");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$my_applications = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

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
    <title>Scholarships - EduConnect</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .scholarships-container {
            max-width: 1200px;
            margin: 80px auto 2rem;
            padding: 0 2rem;
        }

        .section-header {
            margin-bottom: 2rem;
        }

        .scholarships-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }

        .scholarship-card {
            background-color: var(--white);
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .scholarship-card h3 {
            color: var(--primary-color);
            margin-bottom: 1rem;
            font-size: 1.2rem;
        }

        .scholarship-info {
            margin-bottom: 1rem;
        }

        .scholarship-info p {
            margin-bottom: 0.5rem;
            color: var(--dark-gray);
        }

        .scholarship-amount {
            font-size: 1.5rem;
            color: var(--secondary-color);
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .deadline {
            color: #dc3545;
            font-weight: 500;
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

        .status-approved {
            background-color: var(--secondary-color);
            color: var(--white);
        }

        .status-rejected {
            background-color: #dc3545;
            color: var(--white);
        }

        .action-btn {
            display: inline-block;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            text-decoration: none;
            color: var(--white);
            font-weight: 500;
            transition: background-color 0.3s ease;
        }

        .btn-apply {
            background-color: var(--secondary-color);
        }

        .btn-view {
            background-color: var(--primary-color);
        }

        .requirements-list {
            list-style: none;
            margin-bottom: 1rem;
        }

        .requirements-list li {
            margin-bottom: 0.5rem;
            padding-left: 1.5rem;
            position: relative;
        }

        .requirements-list li::before {
            content: "•";
            position: absolute;
            left: 0;
            color: var(--primary-color);
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
                <li><a href="student-scholarships.php" class="active">Scholarships</a></li>
                <li><a href="student-mentors.php">Mentors</a></li>
                <li><a href="student-messages.php" class="notification-badge" data-count="<?php echo $unread_messages; ?>">Messages</a></li>
                <li><a href="../logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>

    <div class="scholarships-container">
        <div class="section-header">
            <h2>My Applications</h2>
        </div>
        <div class="scholarships-grid">
            <?php if (empty($my_applications)): ?>
                <div class="scholarship-card">
                    <p>You haven't applied for any scholarships yet.</p>
                </div>
            <?php else: ?>
                <?php foreach ($my_applications as $application): ?>
                    <div class="scholarship-card">
                        <h3><?php echo htmlspecialchars($application['scholarship_name']); ?></h3>
                        <div class="scholarship-info">
                            <div class="scholarship-amount">$<?php echo number_format($application['amount']); ?></div>
                            <p><strong>Donor:</strong> <?php echo htmlspecialchars($application['donor_name']); ?></p>
                            <p><strong>Applied:</strong> <?php echo date('M d, Y', strtotime($application['applied_date'])); ?></p>
                        </div>
                        <span class="status-badge status-<?php echo strtolower($application['status']); ?>">
                            <?php echo ucfirst($application['status']); ?>
                        </span>
                        <a href="view-application.php?id=<?php echo $application['id']; ?>" class="action-btn btn-view">View Details</a>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div class="section-header">
            <h2>Available Scholarships</h2>
        </div>
        <div class="scholarships-grid">
            <?php if (empty($available_scholarships)): ?>
                <div class="scholarship-card">
                    <p>No scholarships available at the moment.</p>
                </div>
            <?php else: ?>
                <?php foreach ($available_scholarships as $scholarship): ?>
                    <div class="scholarship-card">
                        <h3><?php echo htmlspecialchars($scholarship['name']); ?></h3>
                        <div class="scholarship-info">
                            <div class="scholarship-amount">$<?php echo number_format($scholarship['amount']); ?></div>
                            <p><strong>Donor:</strong> <?php echo htmlspecialchars($scholarship['donor_name']); ?></p>
                            <p class="deadline"><strong>Deadline:</strong> <?php echo date('M d, Y', strtotime($scholarship['application_deadline'])); ?></p>
                        </div>
                        <?php if (!empty($scholarship['requirements'])): ?>
                            <h4>Requirements:</h4>
                            <ul class="requirements-list">
                                <?php foreach (json_decode($scholarship['requirements'], true) as $requirement): ?>
                                    <li><?php echo htmlspecialchars($requirement); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                        <a href="apply-scholarship.php?id=<?php echo $scholarship['id']; ?>" class="action-btn btn-apply">Apply Now</a>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <script src="../js/main.js"></script>
</body>
</html> 