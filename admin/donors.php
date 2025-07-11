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
    <title>Admin - Donors</title>
    <style>
        .donors-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 100px 20px 20px;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .page-title {
            font-size: 2em;
            color: #333;
            margin: 0;
        }

        .export-btn {
            background: #28a745;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 600;
            transition: background-color 0.3s;
        }

        .export-btn:hover {
            background: #218838;
        }

        .stats-card {
            background: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }

        .stat-item {
            text-align: center;
        }

        .stat-number {
            font-size: 2em;
            font-weight: bold;
            color: #e74c3c;
        }

        .stat-label {
            color: #666;
            font-size: 0.9em;
        }

        .donors-table {
            background: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .table-header {
            background: #f8f9fa;
            padding: 15px 20px;
            border-bottom: 1px solid #ddd;
        }

        .table-title {
            margin: 0;
            color: #333;
        }

        .table-container {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background: #f8f9fa;
            font-weight: 600;
            color: #333;
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

        .status-active {
            background: #d4edda;
            color: #155724;
        }

        .status-inactive {
            background: #f8d7da;
            color: #721c24;
        }

        .action-buttons {
            display: flex;
            gap: 8px;
        }

        .btn {
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.9em;
            text-decoration: none;
            display: inline-block;
        }

        .btn-view {
            background: #007bff;
            color: white;
        }

        .btn-edit {
            background: #28a745;
            color: white;
        }

        .btn-delete {
            background: #dc3545;
            color: white;
        }

        .search-box {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin-bottom: 20px;
            width: 300px;
        }

        .no-data {
            text-align: center;
            padding: 40px;
            color: #666;
        }

        .organization-name {
            font-weight: 500;
            color: #2c3e50;
        }

        .contact-info {
            font-size: 0.9em;
            color: #666;
        }
    </style>
</head>

<body>
    <?php include '../components/admin-navbar.php'; ?>
    <div class="donors-container animate-fade-in">
        <div class="page-header">
            <h1 class="page-title">Donors Management</h1>
            <a href="export_donors.php" class="export-btn">Export Donors</a>
        </div>

        <!-- Statistics -->
        <div class="stats-card">
            <div class="stats-grid">
                <div class="stat-item">
                    <div class="stat-number"><?php echo count($donors); ?></div>
                    <div class="stat-label">Total Donors</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number"><?php echo count(array_filter($donors, function ($d) {
                                                    return !empty($d['organization']);
                                                })); ?></div>
                    <div class="stat-label">With Profiles</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number"><?php echo count(array_filter($donors, function ($d) {
                                                    return empty($d['organization']);
                                                })); ?></div>
                    <div class="stat-label">Without Profiles</div>
                </div>
            </div>
        </div>

        <!-- Donors Table -->
        <div class="donors-table">
            <div class="table-header">
                <h2 class="table-title">All Donors</h2>
                <input type="text" class="search-box" placeholder="Search donors..." onkeyup="filterTable(this.value)">
            </div>
            <div class="table-container">
                <table id="donorsTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Organization</th>
                            <th>Phone</th>
                            <th>Address</th>
                            <th>Profile Status</th>
                            <th>Joined Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($donors)): ?>
                            <tr>
                                <td colspan="9" class="no-data">No donors found</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($donors as $donor): ?>
                                <tr>
                                    <td><?php echo $donor['id']; ?></td>
                                    <td>
                                        <div class="organization-name"><?php echo htmlspecialchars($donor['name']); ?></div>
                                    </td>
                                    <td><?php echo htmlspecialchars($donor['email']); ?></td>
                                    <td><?php echo htmlspecialchars($donor['organization'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($donor['phone'] ?? 'N/A'); ?></td>
                                    <td>
                                        <div class="contact-info">
                                            <?php echo htmlspecialchars($donor['address'] ?? 'N/A'); ?>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if (!empty($donor['organization'])): ?>
                                            <span class="status-badge status-active">Complete</span>
                                        <?php else: ?>
                                            <span class="status-badge status-inactive">Incomplete</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo date('M j, Y', strtotime($donor['created_at'])); ?></td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="view_donor.php?id=<?php echo $donor['id']; ?>" class="btn btn-view">View</a>
                                            <a href="edit_donor.php?id=<?php echo $donor['id']; ?>" class="btn btn-edit">Edit</a>
                                            <button class="btn btn-delete" onclick="deleteDonor(<?php echo $donor['id']; ?>)">Delete</button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        function filterTable(searchTerm) {
            const table = document.getElementById('donorsTable');
            const rows = table.getElementsByTagName('tr');

            for (let i = 1; i < rows.length; i++) {
                const row = rows[i];
                const cells = row.getElementsByTagName('td');
                let found = false;

                for (let j = 0; j < cells.length; j++) {
                    const cellText = cells[j].textContent.toLowerCase();
                    if (cellText.includes(searchTerm.toLowerCase())) {
                        found = true;
                        break;
                    }
                }

                row.style.display = found ? '' : 'none';
            }
        }

        function deleteDonor(donorId) {
            if (confirm('Are you sure you want to delete this donor? This action cannot be undone.')) {
                fetch('delete_donor.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: 'donor_id=' + donorId
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Donor deleted successfully!');
                            location.reload();
                        } else {
                            alert(data.message || 'Failed to delete donor.');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred while deleting the donor.');
                    });
            }
        }
    </script>
</body>

</html>