<?php
session_start();
require_once '../config/database.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

// Fetch all donors
$stmt = $conn->prepare("SELECT u.id, u.name, u.email, u.created_at, dp.organization, dp.phone, dp.address FROM users u LEFT JOIN donor_profiles dp ON u.id = dp.user_id WHERE u.user_type = 'donor' ORDER BY u.created_at DESC");
$stmt->execute();
$donors = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Set headers for Excel download
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="donors_export_' . date('Y-m-d_H-i-s') . '.xls"');
header('Cache-Control: max-age=0');

// Start output buffering
ob_start();
?>
<table border="1">
    <thead>
        <tr>
            <th style="background-color: #465462; color: white; padding: 10px;">ID</th>
            <th style="background-color: #465462; color: white; padding: 10px;">Name</th>
            <th style="background-color: #465462; color: white; padding: 10px;">Email</th>
            <th style="background-color: #465462; color: white; padding: 10px;">Organization</th>
            <th style="background-color: #465462; color: white; padding: 10px;">Phone</th>
            <th style="background-color: #465462; color: white; padding: 10px;">Address</th>
            <th style="background-color: #465462; color: white; padding: 10px;">Profile Status</th>
            <th style="background-color: #465462; color: white; padding: 10px;">Joined Date</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($donors as $donor): ?>
            <tr>
                <td style="padding: 8px;"><?php echo $donor['id']; ?></td>
                <td style="padding: 8px;"><?php echo htmlspecialchars($donor['name']); ?></td>
                <td style="padding: 8px;"><?php echo htmlspecialchars($donor['email']); ?></td>
                <td style="padding: 8px;"><?php echo htmlspecialchars($donor['organization'] ?? 'N/A'); ?></td>
                <td style="padding: 8px;"><?php echo htmlspecialchars($donor['phone'] ?? 'N/A'); ?></td>
                <td style="padding: 8px;"><?php echo htmlspecialchars($donor['address'] ?? 'N/A'); ?></td>
                <td style="padding: 8px;"><?php echo !empty($donor['organization']) ? 'Complete' : 'Incomplete'; ?></td>
                <td style="padding: 8px;"><?php echo date('M j, Y', strtotime($donor['created_at'])); ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php
$content = ob_get_clean();
echo $content;
exit();
?>