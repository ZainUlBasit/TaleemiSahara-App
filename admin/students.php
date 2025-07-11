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
    <title>Admin - Students</title>
    <style>
        .students-container {
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
            color: #2ecc71;
        }

        .stat-label {
            color: #666;
            font-size: 0.9em;
        }

        .students-table {
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
    </style>
</head>

<body>
    <?php include '../components/admin-navbar.php'; ?>
    <div class="students-container animate-fade-in">
        <div class="page-header">
            <h1 class="page-title">Students Management</h1>
            <a href="export_students.php" class="export-btn">Export Students</a>
        </div>

        <!-- Statistics -->
        <div class="stats-card">
            <div class="stats-grid">
                <div class="stat-item">
                    <div class="stat-number"><?php echo count($students); ?></div>
                    <div class="stat-label">Total Students</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number"><?php echo count(array_filter($students, function ($s) {
                                                    return !empty($s['roll_no']);
                                                })); ?></div>
                    <div class="stat-label">With Profiles</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number"><?php echo count(array_filter($students, function ($s) {
                                                    return empty($s['roll_no']);
                                                })); ?></div>
                    <div class="stat-label">Without Profiles</div>
                </div>
            </div>
        </div>

        <!-- Students Table -->
        <div class="students-table">
            <div class="table-header">
                <h2 class="table-title">All Students</h2>
                <input type="text" class="search-box" placeholder="Search students..." onkeyup="filterTable(this.value)">
            </div>
            <div class="table-container">
                <table id="studentsTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Student ID</th>
                            <th>Roll No</th>
                            <th>Department</th>
                            <th>Profile Status</th>
                            <th>Joined Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($students)): ?>
                            <tr>
                                <td colspan="9" class="no-data">No students found</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($students as $student): ?>
                                <tr>
                                    <td><?php echo $student['id']; ?></td>
                                    <td><?php echo htmlspecialchars($student['name']); ?></td>
                                    <td><?php echo htmlspecialchars($student['email']); ?></td>
                                    <td><?php echo htmlspecialchars($student['student_id'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($student['roll_no'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($student['department'] ?? 'N/A'); ?></td>
                                    <td>
                                        <?php if (!empty($student['roll_no'])): ?>
                                            <span class="status-badge status-active">Complete</span>
                                        <?php else: ?>
                                            <span class="status-badge status-inactive">Incomplete</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo date('M j, Y', strtotime($student['created_at'])); ?></td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="view_student.php?id=<?php echo $student['id']; ?>" class="btn btn-view">View</a>
                                            <a href="edit_student.php?id=<?php echo $student['id']; ?>" class="btn btn-edit">Edit</a>
                                            <button class="btn btn-delete" onclick="deleteStudent(<?php echo $student['id']; ?>)">Delete</button>
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
            const table = document.getElementById('studentsTable');
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

        function deleteStudent(studentId) {
            if (confirm('Are you sure you want to delete this student? This action cannot be undone.')) {
                fetch('delete_student.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: 'student_id=' + studentId
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Student deleted successfully!');
                            location.reload();
                        } else {
                            alert(data.message || 'Failed to delete student.');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred while deleting the student.');
                    });
            }
        }
    </script>
</body>

</html>