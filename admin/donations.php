<?php
session_start();
require_once '../config/database.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['donation_id'], $_POST['status'])) {
    $donation_id = intval($_POST['donation_id']);
    $status = $_POST['status'];
    $allowed_statuses = ['pending', 'approved', 'failed', 'unverified', 'rejected'];
    if (in_array($status, $allowed_statuses)) {
        $stmt = $conn->prepare("UPDATE donations SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $donation_id);
        $stmt->execute();
        $stmt->close();
    }
    // Redirect to avoid form resubmission
    header("Location: donations.php");
    exit();
}

// Fetch all donations
$sql = "SELECT d.*, u.name AS donor_name, s.title AS scholarship_title FROM donations d
        LEFT JOIN users u ON d.donor_id = u.id
        LEFT JOIN scholarships s ON d.scholarship_id = s.id
        ORDER BY d.donation_date DESC, d.id DESC";
$result = $conn->query($sql);
$donations = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Donations</title>
    <link rel="stylesheet" href="../css/font.css">
    <link rel="stylesheet" href="../css/general.css">
    <style>
        .donations-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 100px 20px 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
        }

        th,
        td {
            padding: 10px 12px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }

        th {
            background: #f8f9fa;
            font-weight: 600;
        }

        tr:hover {
            background: #f8f9fa;
        }

        .status-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.8em;
            font-weight: 500;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
        }

        .status-approved {
            background: #d4edda;
            color: #155724;
        }

        .status-failed,
        .status-rejected {
            background: #f8d7da;
            color: #721c24;
        }

        .status-unverified {
            background: #f8d7da;
            color: #721c24;
        }

        form.inline-form {
            display: inline;
        }
    </style>
</head>

<body>
    <?php include '../components/admin-navbar.php'; ?>
    <div class="donations-container animate-fade-in">
        <h1>All Donations</h1>
        <div style="overflow-x:auto;">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Donor</th>
                        <th>Scholarship</th>
                        <th>Amount</th>
                        <th>Date</th>
                        <th>Payment Method</th>
                        <th>Transaction ID</th>
                        <th>Status</th>
                        <th>Change Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($donations)): ?>
                        <tr>
                            <td colspan="9" style="text-align:center;">No donations found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($donations as $donation): ?>
                            <tr>
                                <td><?php echo $donation['id']; ?></td>
                                <td><?php echo htmlspecialchars($donation['donor_name']); ?></td>
                                <td><?php echo htmlspecialchars($donation['scholarship_title'] ?? 'General Donation'); ?></td>
                                <td>Rs. <?php echo number_format($donation['amount']); ?></td>
                                <td><?php echo date('M j, Y', strtotime($donation['donation_date'])); ?></td>
                                <td><?php echo htmlspecialchars($donation['payment_method']); ?></td>
                                <td><?php echo htmlspecialchars($donation['transaction_id']); ?></td>
                                <td>
                                    <span class="status-badge status-<?php echo $donation['status']; ?>">
                                        <?php echo ucfirst($donation['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <form method="POST" class="inline-form">
                                        <input type="hidden" name="donation_id" value="<?php echo $donation['id']; ?>">
                                        <select name="status" onchange="this.form.submit()">
                                            <option value="pending" <?php if ($donation['status'] == 'pending') echo 'selected'; ?>>Pending</option>
                                            <option value="approved" <?php if ($donation['status'] == 'approved') echo 'selected'; ?>>Approved</option>
                                            <option value="failed" <?php if ($donation['status'] == 'failed') echo 'selected'; ?>>Failed</option>
                                            <option value="unverified" <?php if ($donation['status'] == 'unverified') echo 'selected'; ?>>Unverified</option>
                                            <option value="rejected" <?php if ($donation['status'] == 'rejected') echo 'selected'; ?>>Rejected</option>
                                        </select>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>