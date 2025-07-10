<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once '../config/database.php';

// Check if user is logged in and is a donor
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'donor') {
    header("Location: ../index.php");
    exit();
}

$donor_id = $_SESSION['user_id'];
$error = '';

// Initialize variables
$profile = null;
$donation_stats = ['total_donations' => 0, 'total_amount' => 0];
$recent_donations = [];
$upcoming_donations = [];
$yearly_stats = [];

try {
    // Check if donor_profiles table exists and fetch profile
    $stmt = $conn->prepare("SHOW TABLES LIKE 'donor_profiles'");
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        $stmt = $conn->prepare("SELECT * FROM donor_profiles WHERE user_id = ?");
        $stmt->bind_param("i", $donor_id);
        $stmt->execute();
        $profile = $stmt->get_result()->fetch_assoc();
        $stmt->close();
    }

    // Check if donations table exists and fetch statistics
    $stmt = $conn->prepare("SHOW TABLES LIKE 'donations'");
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        // Fetch total donations made
        $stmt = $conn->prepare("SELECT COUNT(*) as total_donations, COALESCE(SUM(amount), 0) as total_amount FROM donations WHERE donor_id = ? AND status = 'approved'");
        $stmt->bind_param("i", $donor_id);
        $stmt->execute();
        $donation_stats = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        // Fetch recent donations (last 5)
        $stmt = $conn->prepare("SELECT d.*, COALESCE(s.title, 'General Donation') as scholarship_title FROM donations d LEFT JOIN scholarships s ON d.scholarship_id = s.id WHERE d.donor_id = ? AND d.status = 'approved' ORDER BY d.donation_date DESC LIMIT 5");
        $stmt->bind_param("i", $donor_id);
        $stmt->execute();
        $recent_donations = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        // Fetch upcoming donations (scheduled for this year)
        $current_year = date('Y');
        $stmt = $conn->prepare("SELECT d.*, COALESCE(s.title, 'Scheduled Donation') as scholarship_title FROM donations d LEFT JOIN scholarships s ON d.scholarship_id = s.id WHERE d.donor_id = ? AND YEAR(d.donation_date) = ? AND d.donation_date >= CURDATE() AND d.status = 'approved' ORDER BY d.donation_date ASC");
        $stmt->bind_param("is", $donor_id, $current_year);
        $stmt->execute();
        $upcoming_donations = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        // Fetch donation history by year
        $stmt = $conn->prepare("SELECT YEAR(donation_date) as year, COUNT(*) as count, SUM(amount) as total FROM donations WHERE donor_id = ? AND status = 'approved' GROUP BY YEAR(donation_date) ORDER BY year DESC");
        $stmt->bind_param("i", $donor_id);
        $stmt->execute();
        $yearly_stats = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
    }
} catch (Exception $e) {
    $error = "Database error: " . $e->getMessage();
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
    <link rel="stylesheet" href="../css/font.css">
    <link rel="stylesheet" href="../css/general.css">
    <title>Donor Dashboard</title>
    <style>
        .dashboard-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 100px 20px 20px;
        }

        .welcome-section {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            color: white;
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 30px;
        }

        .welcome-title {
            font-size: 2.5em;
            margin: 0 0 10px 0;
        }

        .welcome-subtitle {
            font-size: 1.2em;
            opacity: 0.9;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            border: 1px solid #ddd;
            border-radius: 12px;
            padding: 25px;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-number {
            font-size: 2.5em;
            font-weight: bold;
            color: #e74c3c;
            margin-bottom: 10px;
        }

        .stat-label {
            color: #666;
            font-size: 1.1em;
        }

        .content-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 30px;
        }

        @media (max-width: 768px) {
            .content-grid {
                grid-template-columns: 1fr;
            }
        }

        .section-card {
            background: white;
            border: 1px solid #ddd;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .section-title {
            font-size: 1.5em;
            color: #333;
            margin: 0 0 20px 0;
            padding-bottom: 10px;
            border-bottom: 2px solid #e74c3c;
        }

        .donation-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid #eee;
        }

        .donation-item:last-child {
            border-bottom: none;
        }

        .donation-info {
            flex: 1;
        }

        .donation-title {
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
        }

        .donation-date {
            font-size: 0.9em;
            color: #666;
        }

        .donation-amount {
            font-weight: bold;
            color: #e74c3c;
            font-size: 1.1em;
        }

        .yearly-stats {
            display: grid;
            gap: 10px;
        }

        .year-stat {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 8px;
        }

        .year-label {
            font-weight: 500;
            color: #333;
        }

        .year-data {
            text-align: right;
        }

        .year-count {
            font-weight: bold;
            color: #e74c3c;
        }

        .year-total {
            font-size: 0.9em;
            color: #666;
        }

        .no-data {
            text-align: center;
            color: #666;
            padding: 40px;
            font-style: italic;
        }

        .status-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.8em;
            font-weight: 500;
        }

        .status-completed {
            background: #d4edda;
            color: #155724;
        }

        .status-upcoming {
            background: #fff3cd;
            color: #856404;
        }

        .profile-complete {
            background: #d4edda;
            color: #155724;
        }

        .profile-incomplete {
            background: #f8d7da;
            color: #721c24;
        }

        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>

<body>
    <?php include '../components/donor-navbar.php'; ?>
    <div class="dashboard-container animate-fade-in">
        <?php if ($error): ?>
            <div class="error-message"><?php echo $error; ?></div>
        <?php endif; ?>

        <!-- Welcome Section -->
        <div class="welcome-section">
            <h1 class="welcome-title">
                Welcome back,
                <?php
                echo htmlspecialchars(isset($_SESSION['name']) && $_SESSION['name'] ? $_SESSION['name'] : 'Donor');
                ?>!
            </h1>
            <p class="welcome-subtitle">
                <?php if ($profile): ?>
                    <?php echo htmlspecialchars($profile['organization']); ?>
                <?php else: ?>
                    Complete your profile to get started
                <?php endif; ?>
            </p>
        </div>

        <!-- Statistics -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?php echo $donation_stats['total_donations'] ?? 0; ?></div>
                <div class="stat-label">Total Donations</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">Rs. <?php echo number_format($donation_stats['total_amount'] ?? 0); ?></div>
                <div class="stat-label">Total Amount Donated</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo count($upcoming_donations); ?></div>
                <div class="stat-label">Upcoming Donations</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">
                    <?php if ($profile): ?>
                        <span class="status-badge profile-complete">Complete</span>
                    <?php else: ?>
                        <span class="status-badge profile-incomplete">Incomplete</span>
                    <?php endif; ?>
                </div>
                <div class="stat-label">Profile Status</div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="content-grid">
            <!-- Recent Donations -->
            <div class="section-card">
                <h2 class="section-title">Recent Donations</h2>
                <?php if (empty($recent_donations)): ?>
                    <div class="no-data">No donations made yet</div>
                <?php else: ?>
                    <?php foreach ($recent_donations as $donation): ?>
                        <div class="donation-item">
                            <div class="donation-info">
                                <div class="donation-title">
                                    <?php echo htmlspecialchars($donation['scholarship_title']); ?>
                                </div>
                                <div class="donation-date">
                                    <?php echo date('M j, Y', strtotime($donation['donation_date'])); ?>
                                    <?php if ($donation['status'] === 'approved'): ?>
                                        <span class="status-badge status-completed">Verified</span>
                                    <?php elseif ($donation['status'] === 'pending'): ?>
                                        <span class="status-badge status-upcoming">Pending</span>
                                    <?php else: ?>
                                        <span class="status-badge profile-incomplete">Unverified</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="donation-amount">
                                Rs. <?php echo number_format($donation['amount']); ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Sidebar -->
            <div style="display: flex; flex-direction: column; gap: 20px;">
                <!-- Upcoming Donations -->
                <div class="section-card">
                    <h2 class="section-title">Upcoming Donations (<?php echo date('Y'); ?>)</h2>
                    <?php if (empty($upcoming_donations)): ?>
                        <div class="no-data">No upcoming donations</div>
                    <?php else: ?>
                        <?php foreach ($upcoming_donations as $donation): ?>
                            <div class="donation-item">
                                <div class="donation-info">
                                    <div class="donation-title">
                                        <?php echo htmlspecialchars($donation['scholarship_title']); ?>
                                    </div>
                                    <div class="donation-date">
                                        <?php echo date('M j, Y', strtotime($donation['donation_date'])); ?>
                                        <?php if ($donation['status'] === 'approved'): ?>
                                            <span class="status-badge status-completed">Verified</span>
                                        <?php elseif ($donation['status'] === 'pending'): ?>
                                            <span class="status-badge status-upcoming">Pending</span>
                                        <?php else: ?>
                                            <span class="status-badge profile-incomplete">Unverified</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="donation-amount">
                                    Rs. <?php echo number_format($donation['amount']); ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- Yearly Statistics -->
                <div class="section-card">
                    <h2 class="section-title">Donation History by Year</h2>
                    <?php if (empty($yearly_stats)): ?>
                        <div class="no-data">No donation history</div>
                    <?php else: ?>
                        <div class="yearly-stats">
                            <?php foreach ($yearly_stats as $year_stat): ?>
                                <div class="year-stat">
                                    <div class="year-label"><?php echo $year_stat['year']; ?></div>
                                    <div class="year-data">
                                        <div class="year-count"><?php echo $year_stat['count']; ?> donations</div>
                                        <div class="year-total">Rs. <?php echo number_format($year_stat['total']); ?></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</body>

</html>