<?php
session_start();
require_once '../config/database.php';

// Check if user is logged in and is a donor
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'donor') {
    header("Location: ../login.php");
    exit();
}

// Get donor details
$donor_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ? AND user_type = 'donor'");
$stmt->bind_param("i", $donor_id);
$stmt->execute();
$donor = $stmt->get_result()->fetch_assoc();

// Get donor's scholarships
$stmt = $conn->prepare("SELECT * FROM scholarships WHERE donor_id = ?");
$stmt->bind_param("i", $donor_id);
$stmt->execute();
$scholarships = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Get scholarship applications for donor's scholarships
$stmt = $conn->prepare("SELECT sa.*, s.name as scholarship_name, u.name as student_name, u.email as student_email 
                       FROM scholarship_applications sa 
                       JOIN scholarships s ON sa.scholarship_id = s.id 
                       JOIN users u ON sa.student_id = u.id 
                       WHERE s.donor_id = ?");
$stmt->bind_param("i", $donor_id);
$stmt->execute();
$applications = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Calculate total impact
$total_amount = 0;
$students_supported = 0;
foreach ($scholarships as $scholarship) {
    $total_amount += $scholarship['amount'];
    if ($scholarship['status'] === 'awarded') {
        $students_supported++;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donor Dashboard - EduConnect</title>
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

        .scholarship-list,
        .application-list {
            list-style: none;
        }

        .scholarship-list li,
        .application-list li {
            padding: 1rem;
            border-bottom: 1px solid var(--light-gray);
        }

        .scholarship-list li:last-child,
        .application-list li:last-child {
            border-bottom: none;
        }

        .status-badge {
            display: inline-block;
            padding: 0.25rem 0.5rem;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .status-active {
            background-color: var(--secondary-color);
            color: var(--white);
        }

        .status-closed {
            background-color: var(--dark-gray);
            color: var(--white);
        }

        .status-pending {
            background-color: #ffd700;
            color: #000;
        }

        .action-btn {
            padding: 0.5rem 1rem;
            border-radius: 5px;
            text-decoration: none;
            color: var(--white);
            font-weight: 500;
            transition: background-color 0.3s ease;
            border: var(--secondary-color) 2px solid;
        }

        .btn-create {
            background-color: var(--secondary-color);
        }

        .action-btn:hover {
            background-color: white;
            color: var(--secondary-color) !important;
        }

        .btn-view {
            background-color: var(--primary-color);
        }

        .btn-review {
            background-color: var(--tertiary-color);
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
                <li><a href="#messages" class="action-btn btn-create" style="text-decoration: none; color: white;">Donate Now</a></li>
                <li><a href="../logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>

    <div class="dashboard-container">
        <div class="dashboard-header">
            <div class="welcome-message">
                Welcome, <?php echo htmlspecialchars($donor['name']); ?>!
            </div>
            <!-- <div class="dashboard-actions">
                <a href="create-scholarship.php" class="action-btn btn-create">Create Scholarship</a>
            </div> -->
        </div>

        <div class="dashboard-stats">
            <div class="stat-card">
                <h3><?php echo count($scholarships); ?></h3>
                <p>Total Students</p>
            </div>
            <div class="stat-card">
                <h3>$<?php echo number_format($total_amount); ?></h3>
                <p>Total Donations</p>
            </div>
            <div class="stat-card">
                <h3><?php echo $students_supported; ?></h3>
                <p>Students Supported</p>
            </div>
        </div>

        <div class="dashboard-grid">
            <!-- Active Scholarships -->
            <div class="dashboard-card">
                <h3>My Donations</h3>
                <ul class="scholarship-list">
                    <?php if (empty($scholarships)): ?>
                        <li>No donations <div class="media">
                                <a class="d-flex" href="#">
                                    <img src="" alt="">
                                </a>
                                <div class="media-body">
                                    <h5>Media heading</h5>

                                </div>
                            </div> yet</li>
                    <?php else: ?>
                        <?php foreach ($scholarships as $scholarship): ?>
                            <li>
                                <div class="scholarship-info">
                                    <h4><?php echo htmlspecialchars($scholarship['name']); ?></h4>
                                    <p>Amount: $<?php echo number_format($scholarship['amount']); ?></p>
                                    <p>Deadline: <?php echo date('M d, Y', strtotime($scholarship['application_deadline'])); ?></p>
                                    <span class="status-badge status-<?php echo strtolower($scholarship['status']); ?>">
                                        <?php echo ucfirst($scholarship['status']); ?>
                                    </span>
                                    <a href="view-scholarship.php?id=<?php echo $scholarship['id']; ?>" class="action-btn btn-view">View Details</a>
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