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
$stmt = $conn->prepare("SELECT u.*, sp.* FROM users u 
                       LEFT JOIN student_profiles sp ON u.id = sp.user_id 
                       WHERE u.id = ? AND u.user_type = 'student'");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();

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
    <title>Student Profile - EduConnect</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .profile-container {
            max-width: 800px;
            margin: 80px auto 2rem;
            padding: 2rem;
            background-color: var(--white);
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .profile-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .profile-section {
            margin-bottom: 2rem;
        }

        .profile-section h3 {
            color: var(--primary-color);
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid var(--light-gray);
        }

        .profile-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }

        .info-item {
            margin-bottom: 1rem;
        }

        .info-item label {
            font-weight: 600;
            color: var(--dark-gray);
            display: block;
            margin-bottom: 0.5rem;
        }

        .info-item p {
            color: var(--text-color);
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

        .achievements-list {
            list-style: none;
            padding: 0;
        }

        .achievements-list li {
            margin-bottom: 1rem;
            padding: 1rem;
            background-color: var(--light-gray);
            border-radius: 5px;
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
                <li><a href="student-profile.php" class="active">Profile</a></li>
                <li><a href="student-scholarships.php">Scholarships</a></li>
                <li><a href="student-mentors.php">Mentors</a></li>
                <li><a href="../logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>

    <div class="profile-container">
        <div class="profile-header">
            <h2>Student Profile</h2>
            <a href="edit-profile.php" class="action-btn btn-edit">Edit Profile</a>
        </div>

        <div class="profile-section">
            <h3>Personal Information</h3>
            <div class="profile-info">
                <div class="info-item">
                    <label>Full Name</label>
                    <p><?php echo htmlspecialchars($student['name']); ?></p>
                </div>
                <div class="info-item">
                    <label>Email</label>
                    <p><?php echo htmlspecialchars($student['email']); ?></p>
                </div>
                <div class="info-item">
                    <label>Member Since</label>
                    <p><?php echo date('F Y', strtotime($student['created_at'])); ?></p>
                </div>
            </div>
        </div>

        <div class="profile-section">
            <h3>Academic Information</h3>
            <div class="profile-info">
                <div class="info-item">
                    <label>Education Level</label>
                    <p><?php echo htmlspecialchars($student['education_level'] ?? 'Not specified'); ?></p>
                </div>
                <div class="info-item">
                    <label>Field of Study</label>
                    <p><?php echo htmlspecialchars($student['field_of_study'] ?? 'Not specified'); ?></p>
                </div>
                <div class="info-item">
                    <label>Institution</label>
                    <p><?php echo htmlspecialchars($student['institution'] ?? 'Not specified'); ?></p>
                </div>
                <div class="info-item">
                    <label>GPA</label>
                    <p><?php echo $student['gpa'] ? number_format($student['gpa'], 2) : 'Not specified'; ?></p>
                </div>
            </div>
        </div>

        <div class="profile-section">
            <h3>Bio</h3>
            <p><?php echo htmlspecialchars($student['bio'] ?? 'No bio available'); ?></p>
        </div>

        <div class="profile-section">
            <h3>Achievements</h3>
            <?php if (!empty($student['achievements'])): ?>
                <ul class="achievements-list">
                    <?php foreach (json_decode($student['achievements'], true) ?? [] as $achievement): ?>
                        <li><?php echo htmlspecialchars($achievement); ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>No achievements listed yet</p>
            <?php endif; ?>
        </div>
    </div>

    <script src="../js/main.js"></script>
</body>

</html>