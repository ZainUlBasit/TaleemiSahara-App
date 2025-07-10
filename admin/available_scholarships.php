<?php
session_start();
require_once '../config/database.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

// Handle POST requests for actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle scholarship deletion
    if (isset($_POST['delete_scholarship'])) {
        $scholarship_id = intval($_POST['delete_scholarship']);

        // It's good practice to check for dependencies before deleting
        $check_stmt = $conn->prepare("SELECT id FROM scholarship_applications WHERE scholarship_id = ? LIMIT 1");
        $check_stmt->bind_param("i", $scholarship_id);
        $check_stmt->execute();
        $result = $check_stmt->get_result();
        $check_stmt->close();

        if ($result->num_rows > 0) {
            $_SESSION['error_message'] = "Cannot delete scholarship as it has existing applications. Consider setting it to 'inactive' instead.";
        } else {
            $delete_stmt = $conn->prepare("DELETE FROM scholarships WHERE id = ?");
            $delete_stmt->bind_param("i", $scholarship_id);
            if ($delete_stmt->execute()) {
                $_SESSION['success_message'] = "Scholarship deleted successfully.";
            } else {
                $_SESSION['error_message'] = "Error deleting scholarship: " . $delete_stmt->error;
            }
            $delete_stmt->close();
        }
    }

    // Handle status update
    if (isset($_POST['scholarship_id'], $_POST['status'])) {
        $scholarship_id = intval($_POST['scholarship_id']);
        $status = $_POST['status'];
        $allowed_statuses = ['active', 'inactive'];
        if (in_array($status, $allowed_statuses)) {
            $stmt = $conn->prepare("UPDATE scholarships SET status = ? WHERE id = ?");
            $stmt->bind_param("si", $status, $scholarship_id);
            if ($stmt->execute()) {
                $_SESSION['success_message'] = "Scholarship status updated successfully.";
            } else {
                $_SESSION['error_message'] = "Error updating status: " . $stmt->error;
            }
            $stmt->close();
        }
    }

    // Redirect to prevent form resubmission
    header("Location: available_scholarships.php");
    exit();
}

// Get flash messages from session
$error_message = $_SESSION['error_message'] ?? null;
$success_message = $_SESSION['success_message'] ?? null;
unset($_SESSION['error_message'], $_SESSION['success_message']);

// Fetch all scholarships
$sql = "SELECT * FROM scholarships ORDER BY created_at DESC";
$result = $conn->query($sql);
$scholarships = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Available Scholarships</title>
    <link rel="stylesheet" href="../css/font.css">
    <link rel="stylesheet" href="../css/general.css">
    <style>
        .scholarships-container {
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
            padding: 12px 15px;
            border-bottom: 1px solid #ddd;
            text-align: left;
            vertical-align: middle;
        }

        th {
            background: #f8f9fa;
            font-weight: 600;
        }

        tr:hover {
            background: #f8f9fa;
        }

        .status-badge {
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 0.85em;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-active {
            background: #d4edda;
            color: #155724;
        }

        .status-inactive {
            background: #f8d7da;
            color: #721c24;
        }

        .actions {
            display: flex;
            gap: 10px;
        }

        .action-btn {
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.9em;
            font-weight: 500;
            text-decoration: none;
            color: white;
            display: inline-block;
            transition: all 0.3s ease;
        }

        .edit-btn {
            background: #ffc107;
            color: #212529;
        }

        .edit-btn:hover {
            background: #e0a800;
        }

        .delete-btn {
            background: #dc3545;
            color: white;
        }

        .delete-btn:hover {
            background: #c82333;
        }

        .status-btn {
            background: #17a2b8;
            color: white;
        }

        .status-btn:hover {
            background: #138496;
        }

        .message {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border: 1px solid transparent;
        }

        .success {
            background: #d4edda;
            color: #155724;
            border-color: #c3e6cb;
        }

        .error {
            background: #f8d7da;
            color: #721c24;
            border-color: #f5c6cb;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .header-actions {
            display: flex;
            gap: 15px;
        }

        .page-header h1 {
            margin: 0;
        }

        .action-btn.add-btn {
            background: #28a745;
        }

        .action-btn.add-btn:hover {
            background: #218838;
        }

        .action-btn.view-apps-btn {
            background: #007bff;
        }

        .action-btn.view-apps-btn:hover {
            background: #0056b3;
        }
    </style>
</head>

<body>
    <?php include '../components/admin-navbar.php'; ?>
    <div class="scholarships-container animate-fade-in">
        <div class="page-header">
            <h1>Available Scholarships</h1>
            <div class="header-actions">
                <a href="add_scholarship.php" class="action-btn add-btn">Add New Scholarship</a>
                <a href="applications.php" class="action-btn view-apps-btn">View Applications</a>
            </div>
        </div>

        <?php if ($success_message): ?><div class="message success"><?php echo htmlspecialchars($success_message); ?></div><?php endif; ?>
        <?php if ($error_message): ?><div class="message error"><?php echo htmlspecialchars($error_message); ?></div><?php endif; ?>

        <div style="overflow-x:auto;">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Amount</th>
                        <th>Deadline</th>
                        <th>Slots</th>
                        <th>Status</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($scholarships)): ?>
                        <tr>
                            <td colspan="8" style="text-align:center;">No scholarships found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($scholarships as $scholarship): ?>
                            <tr>
                                <td><?php echo $scholarship['id']; ?></td>
                                <td><?php echo htmlspecialchars($scholarship['title']); ?></td>
                                <td>Rs. <?php echo number_format($scholarship['amount']); ?></td>
                                <td><?php echo date('M j, Y', strtotime($scholarship['deadline'])); ?></td>
                                <td><?php echo $scholarship['available_slots']; ?></td>
                                <td>
                                    <span class="status-badge status-<?php echo $scholarship['status']; ?>">
                                        <?php echo ucfirst($scholarship['status']); ?>
                                    </span>
                                </td>
                                <td><?php echo date('M j, Y', strtotime($scholarship['created_at'])); ?></td>
                                <td class="actions">
                                    <a href="edit_scholarship.php?id=<?php echo $scholarship['id']; ?>" class="action-btn edit-btn">Edit</a>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="scholarship_id" value="<?php echo $scholarship['id']; ?>">
                                        <input type="hidden" name="status" value="<?php echo $scholarship['status'] === 'active' ? 'inactive' : 'active'; ?>">
                                        <button type="submit" class="action-btn status-btn">
                                            <?php echo $scholarship['status'] === 'active' ? 'Deactivate' : 'Activate'; ?>
                                        </button>
                                    </form>
                                    <form method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this scholarship? This action cannot be undone.');">
                                        <input type="hidden" name="delete_scholarship" value="<?php echo $scholarship['id']; ?>">
                                        <button type="submit" class="action-btn delete-btn">Delete</button>
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