<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once '../config/database.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$admin_id = $_SESSION['user_id'];
$admin_name = 'Admin'; // Default name

// Fetch admin's name
$stmt_admin = $conn->prepare("SELECT name FROM users WHERE id = ?");
$stmt_admin->bind_param("i", $admin_id);
if ($stmt_admin->execute()) {
    $result = $stmt_admin->get_result();
    if ($row = $result->fetch_assoc()) {
        $admin_name = htmlspecialchars(explode(' ', $row['name'])[0]);
    }
}
$stmt_admin->close();

// Fetch statistics. Wrap queries in try-catch to prevent fatal errors if tables don't exist.
try {
    $result = $conn->query("SELECT COUNT(id) AS count FROM users WHERE user_type = 'student'");
    $total_students = $result ? $result->fetch_assoc()['count'] : 0;
} catch (mysqli_sql_exception $e) {
    $total_students = 0;
}

try {
    $result = $conn->query("SELECT COUNT(id) AS count FROM users WHERE user_type = 'donor'");
    $total_donors = $result ? $result->fetch_assoc()['count'] : 0;
} catch (mysqli_sql_exception $e) {
    $total_donors = 0;
}

try {
    $result = $conn->query("SELECT SUM(amount) AS total FROM donations");
    $total_donations = $result ? ($result->fetch_assoc()['total'] ?? 0) : 0;
} catch (mysqli_sql_exception $e) {
    $total_donations = 0;
}

try {
    $result = $conn->query("SELECT COUNT(id) AS count FROM scholarships");
    $total_scholarships = $result ? $result->fetch_assoc()['count'] : 0;
} catch (mysqli_sql_exception $e) {
    $total_scholarships = 0;
}

try {
    $result = $conn->query("SELECT COUNT(id) AS count FROM scholarship_applications WHERE status = 'pending'");
    $pending_applications = $result ? $result->fetch_assoc()['count'] : 0;
} catch (mysqli_sql_exception $e) {
    $pending_applications = 0;
}

try {
    $result = $conn->query("SELECT COUNT(id) AS count FROM videos");
    $total_videos = $result ? $result->fetch_assoc()['count'] : 0;
} catch (mysqli_sql_exception $e) {
    $total_videos = 0;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/font.css">
    <link rel="stylesheet" href="../css/general.css">
    <title>Admin Dashboard</title>
    <style>
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
        .icon-students {
            background-color: #3498db;
        }

        .icon-donors {
            background-color: #2ecc71;
        }

        .icon-donations {
            background-color: #f1c40f;
        }

        .icon-scholarships {
            background-color: #9b59b6;
        }

        .icon-applications {
            background-color: #e67e22;
        }

        .icon-videos {
            background-color: #e74c3c;
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

    <?php include '../components/admin-navbar.php'; ?>
    <div class="main animate-fade-in">
        <div class="dashboard-header">
            <h1>Welcome, <?php echo $admin_name; ?>!</h1>
            <p>Here's a quick overview of your platform's status.</p>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon icon-students">&#128100;</div>
                <div class="stat-info">
                    <span class="stat-number"><?php echo $total_students; ?></span>
                    <span class="stat-label">Total Students</span>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon icon-donors">&#128176;</div>
                <div class="stat-info">
                    <span class="stat-number"><?php echo $total_donors; ?></span>
                    <span class="stat-label">Total Donors</span>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon icon-donations">&#128178;</div>
                <div class="stat-info">
                    <span class="stat-number">Rs. <?php echo number_format($total_donations); ?></span>
                    <span class="stat-label">Total Donations</span>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon icon-scholarships">&#127891;</div>
                <div class="stat-info">
                    <span class="stat-number"><?php echo $total_scholarships; ?></span>
                    <span class="stat-label">Scholarships</span>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon icon-applications">&#128203;</div>
                <div class="stat-info">
                    <span class="stat-number"><?php echo $pending_applications; ?></span>
                    <span class="stat-label">Pending Applications</span>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon icon-videos">&#127909;</div>
                <div class="stat-info">
                    <span class="stat-number"><?php echo $total_videos; ?></span>
                    <span class="stat-label">Total Videos</span>
                </div>
            </div>
        </div>

        <div class="quick-actions">
            <h2>Quick Actions</h2>
            <div class="action-buttons">
                <a href="available_scholarships.php" class="action-btn">Manage Scholarships</a>
                <a href="applications.php" class="action-btn">View Applications</a>
                <a href="manage_videos.php" class="action-btn">Manage Videos</a>
                <a href="students.php" class="action-btn">View Students</a>
                <a href="donors.php" class="action-btn">View Donors</a>
            </div>
        </div>
    </div>

</body>

</html>