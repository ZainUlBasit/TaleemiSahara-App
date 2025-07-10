<?php
session_start();
require_once '../config/database.php';
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('Invalid donor ID.');
}
$donor_id = intval($_GET['id']);
$stmt = $conn->prepare("SELECT u.id, u.name, u.email, u.created_at, dp.organization, dp.phone, dp.address FROM users u LEFT JOIN donor_profiles dp ON u.id = dp.user_id WHERE u.id = ? AND u.user_type = 'donor'");
$stmt->bind_param('i', $donor_id);
$stmt->execute();
$donor = $stmt->get_result()->fetch_assoc();
$stmt->close();
if (!$donor) die('Donor not found.');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>View Donor</title>
    <link rel="stylesheet" href="../css/general.css">
    <style>
        .view-container {
            width: 100%;
            margin: 100px auto;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 10px #0001;
            padding: 2rem;
            max-width: 1400px;
            margin: 0 auto;
            margin-top: 100px;
            padding: 20px 20px 20px;
        }

        body {
            font-family: 'Raleway', sans-serif;
            background-color: #f4f7f6;
            display: flex;
            flex-direction: column;
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
        <div class="view-title">Donor Details</div>
        <div class="view-row"><span class="label">ID:</span> <?php echo $donor['id']; ?></div>
        <div class="view-row"><span class="label">Name:</span> <?php echo htmlspecialchars($donor['name']); ?></div>
        <div class="view-row"><span class="label">Email:</span> <?php echo htmlspecialchars($donor['email']); ?></div>
        <div class="view-row"><span class="label">Organization:</span> <?php echo htmlspecialchars($donor['organization'] ?? 'N/A'); ?></div>
        <div class="view-row"><span class="label">Phone:</span> <?php echo htmlspecialchars($donor['phone'] ?? 'N/A'); ?></div>
        <div class="view-row"><span class="label">Address:</span> <?php echo htmlspecialchars($donor['address'] ?? 'N/A'); ?></div>
        <div class="view-row"><span class="label">Profile Status:</span> <?php echo !empty($donor['organization']) ? 'Complete' : 'Incomplete'; ?></div>
        <div class="view-row"><span class="label">Joined Date:</span> <?php echo date('M j, Y', strtotime($donor['created_at'])); ?></div>
        <a href="donors.php" class="back-btn">&larr; Back to Donors</a>
    </div>
</body>

</html>