<?php
session_start();
require_once '../config/database.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

// Fetch all students
$stmt = $conn->prepare("SELECT u.id, u.name, u.email, u.created_at, sp.roll_no, sp.department, sp.student_id FROM users u LEFT JOIN student_profiles sp ON u.id = sp.user_id WHERE u.user_type = 'student' ORDER BY u.created_at DESC");
$stmt->execute();
$students = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Set headers for Excel download
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="students_export_' . date('Y-m-d_H-i-s') . '.xls"');
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
            <th style="background-color: #465462; color: white; padding: 10px;">Student ID</th>
            <th style="background-color: #465462; color: white; padding: 10px;">Roll No</th>
            <th style="background-color: #465462; color: white; padding: 10px;">Department</th>
            <th style="background-color: #465462; color: white; padding: 10px;">Profile Status</th>
            <th style="background-color: #465462; color: white; padding: 10px;">Joined Date</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($students as $student): ?>
            <tr>
                <td style="padding: 8px;"><?php echo $student['id']; ?></td>
                <td style="padding: 8px;"><?php echo htmlspecialchars($student['name']); ?></td>
                <td style="padding: 8px;"><?php echo htmlspecialchars($student['email']); ?></td>
                <td style="padding: 8px;"><?php echo htmlspecialchars($student['student_id'] ?? 'N/A'); ?></td>
                <td style="padding: 8px;"><?php echo htmlspecialchars($student['roll_no'] ?? 'N/A'); ?></td>
                <td style="padding: 8px;"><?php echo htmlspecialchars($student['department'] ?? 'N/A'); ?></td>
                <td style="padding: 8px;"><?php echo !empty($student['roll_no']) ? 'Complete' : 'Incomplete'; ?></td>
                <td style="padding: 8px;"><?php echo date('M j, Y', strtotime($student['created_at'])); ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php
$content = ob_get_clean();
echo $content;
exit();
?>