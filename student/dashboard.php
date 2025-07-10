<?php
session_start();
require_once '../config/database.php';

// Check if user is logged in and is a student
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'student') {
    header("Location: ../login.php");
    exit();
}

$student_id = $_SESSION['user_id'];
$student_name = 'Student'; // Default

// Fetch student's name
try {
    $stmt_student = $conn->prepare("SELECT name FROM users WHERE id = ?");
    $stmt_student->bind_param("i", $student_id);
    if ($stmt_student->execute()) {
        $result = $stmt_student->get_result();
        if ($row = $result->fetch_assoc()) {
            $student_name = htmlspecialchars(explode(' ', $row['name'])[0]);
        }
    }
    $stmt_student->close();
} catch (mysqli_sql_exception $e) {
    // Keep default name if query fails
}

// Fetch statistics for the student
try {
    $result = $conn->query("SELECT COUNT(id) AS count FROM scholarships WHERE status = 'active'");
    $available_scholarships = $result ? $result->fetch_assoc()['count'] : 0;
} catch (mysqli_sql_exception $e) {
    $available_scholarships = 0;
}

try {
    $stmt = $conn->prepare("SELECT status, COUNT(id) AS count FROM scholarship_applications WHERE student_id = ? GROUP BY status");
    $stmt->bind_param("i", $student_id);
    $applications_by_status = ['total' => 0, 'approved' => 0, 'pending' => 0, 'rejected' => 0];
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            if (isset($applications_by_status[$row['status']])) {
                $applications_by_status[$row['status']] = $row['count'];
                $applications_by_status['total'] += $row['count'];
            }
        }
    }
    $stmt->close();
} catch (mysqli_sql_exception $e) {
    $applications_by_status = ['total' => 0, 'approved' => 0, 'pending' => 0, 'rejected' => 0];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300..700&family=Raleway:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@400..700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="./css/font.css">
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="../css/general.css">
    <style>
        /* Reusing admin dashboard styles for consistency */
        body {
            display: flex;
            flex-direction: column;
            font-family: 'Raleway', sans-serif;
            background-color: #f4f7f6;
            color: #333;
        }

        .main {
            margin: 100px auto 20px;
            max-width: 1200px;
            width: 100%;
            padding: 20px;
        }

        .dashboard-header h1 {
            font-size: 2.5em;
            color: #2c3e50;
            margin-bottom: 10px;
        }

        .dashboard-header p {
            font-size: 1.1em;
            color: #7f8c8d;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin: 30px 0;
        }

        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            display: flex;
            align-items: center;
            gap: 20px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }

        .stat-icon {
            font-size: 2.5em;
            padding: 15px;
            border-radius: 50%;
            color: white;
        }

        .stat-info .stat-number {
            font-size: 2em;
            font-weight: 700;
            display: block;
            color: #2c3e50;
        }

        .stat-info .stat-label {
            font-size: 1em;
            color: #7f8c8d;
        }

        /* Icon Colors */
        .icon-scholarships {
            background-color: #9b59b6;
        }

        .icon-total-apps {
            background-color: #3498db;
        }

        .icon-approved {
            background-color: #2ecc71;
        }

        .icon-pending {
            background-color: #f1c40f;
        }

        .quick-actions {
            margin-top: 30px;
        }

        .quick-actions h2 {
            margin-bottom: 20px;
            color: #2c3e50;
        }

        .action-buttons {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }

        .action-btn {
            background-color: #34495e;
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 600;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .action-btn:hover {
            background-color: #2c3e50;
            transform: translateY(-2px);
            color: white;
            text-decoration: none;
        }
    </style>
</head>

<body>
    <?php include '../components/student-navbar.php'; ?>
    <div class="main animate-fade-in">
        <div class="dashboard-header">
            <h1>Welcome, <?php echo $student_name; ?>!</h1>
            <p>Here's your personal dashboard. Keep track of your scholarship applications and discover new opportunities.</p>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon icon-scholarships">&#127891;</div>
                <div class="stat-info">
                    <span class="stat-number"><?php echo $available_scholarships; ?></span>
                    <span class="stat-label">Available Scholarships</span>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon icon-total-apps">&#128203;</div>
                <div class="stat-info">
                    <span class="stat-number"><?php echo $applications_by_status['total']; ?></span>
                    <span class="stat-label">My Applications</span>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon icon-approved">&#10004;</div>
                <div class="stat-info">
                    <span class="stat-number"><?php echo $applications_by_status['approved']; ?></span>
                    <span class="stat-label">Approved</span>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon icon-pending">&#8987;</div>
                <div class="stat-info">
                    <span class="stat-number"><?php echo $applications_by_status['pending']; ?></span>
                    <span class="stat-label">Pending</span>
                </div>
            </div>
        </div>

        <div class="quick-actions">
            <h2>Quick Actions</h2>
            <div class="action-buttons">
                <a href="scholarships.php" class="action-btn">Browse Scholarships</a>
                <a href="my_applications.php" class="action-btn">View My Applications</a>
                <a href="student_profile.php" class="action-btn">Update My Profile</a>
            </div>
        </div>
    </div>
</body>

</html>