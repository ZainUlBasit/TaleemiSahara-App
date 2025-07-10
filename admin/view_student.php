<?php
session_start();
require_once '../config/database.php';
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('Invalid student ID.');
}
$student_id = intval($_GET['id']);
$stmt = $conn->prepare("SELECT u.id, u.name, u.email, u.created_at, sp.roll_no, sp.department, sp.student_id FROM users u LEFT JOIN student_profiles sp ON u.id = sp.user_id WHERE u.id = ? AND u.user_type = 'student'");
$stmt->bind_param('i', $student_id);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();
$stmt->close();
if (!$student) die('Student not found.');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>View Student</title>
    <link rel="stylesheet" href="../css/general.css">
    <style>
        body {
            font-family: 'Raleway', sans-serif;
            background-color: #f4f7f6;
            display: flex;
            flex-direction: column;
        }

        .view-container {
            width: 100%;
            max-width: 700px;
            margin: 100px auto;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 10px #0001;
            padding: 2rem;
        }

        .view-title {
            font-size: 2rem;
            margin-bottom: 1rem;
        }

        .view-row {
            margin-bottom: 1rem;
        }

        .label {
            font-weight: bold;
            color: #465462;
        }

        .back-btn {
            display: inline-block;
            margin-top: 2rem;
            background: #465462;
            color: #fff;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
        }

        .back-btn:hover {
            background: #96ADC5;
            color: #333;
        }
    </style>
</head>

<body>
    <?php include '../components/admin-navbar.php'; ?>
    <div class="view-container">
        <div class="view-title">Student Details</div>
        <div class="view-row"><span class="label">ID:</span> <?php echo $student['id']; ?></div>
        <div class="view-row"><span class="label">Name:</span> <?php echo htmlspecialchars($student['name']); ?></div>
        <div class="view-row"><span class="label">Email:</span> <?php echo htmlspecialchars($student['email']); ?></div>
        <div class="view-row"><span class="label">Student ID:</span> <?php echo htmlspecialchars($student['student_id'] ?? 'N/A'); ?></div>
        <div class="view-row"><span class="label">Roll No:</span> <?php echo htmlspecialchars($student['roll_no'] ?? 'N/A'); ?></div>
        <div class="view-row"><span class="label">Department:</span> <?php echo htmlspecialchars($student['department'] ?? 'N/A'); ?></div>
        <div class="view-row"><span class="label">Profile Status:</span> <?php echo !empty($student['roll_no']) ? 'Complete' : 'Incomplete'; ?></div>
        <div class="view-row"><span class="label">Joined Date:</span> <?php echo date('M j, Y', strtotime($student['created_at'])); ?></div>
        <a href="students.php" class="back-btn">&larr; Back to Students</a>
    </div>
</body>

</html>