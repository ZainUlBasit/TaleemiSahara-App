<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once '../config/database.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['application_id'], $_POST['status'])) {
    $application_id = intval($_POST['application_id']);
    $status = $_POST['status'];
    $notes = trim($_POST['notes'] ?? '');

    // Validate status
    $allowed_statuses = ['pending', 'approved', 'rejected'];
    if (in_array($status, $allowed_statuses)) {
        $stmt = $conn->prepare("UPDATE scholarship_applications SET status = ?, notes = ? WHERE id = ?");
        $stmt->bind_param("ssi", $status, $notes, $application_id);
        if ($stmt->execute()) {
            $success_message = "Application status updated successfully!";
        } else {
            $error_message = "Failed to update application status: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $error_message = "Invalid status provided.";
    }

    // Redirect to avoid form resubmission
    header("Location: applications.php");
    exit();
}

// Fetch all scholarship applications with student and scholarship details
$sql = "SELECT sa.*, u.name as student_name, u.email as student_email, 
               sch.title as scholarship_title, sch.amount as scholarship_amount,
               sch.description as scholarship_description, sch.requirements as scholarship_requirements,
               sch.deadline as scholarship_deadline, sch.available_slots as scholarship_slots
        FROM scholarship_applications sa
        JOIN users u ON sa.student_id = u.id
        JOIN scholarships sch ON sa.scholarship_id = sch.id
        ORDER BY sa.application_date DESC";

try {
    $result = $conn->query($sql);
    if ($result === false) {
        throw new Exception("Database query failed: " . $conn->error);
    }
    $applications = $result->fetch_all(MYSQLI_ASSOC);
} catch (Exception $e) {
    $applications = [];
    $error_message = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/font.css">
    <link rel="stylesheet" href="../css/general.css">
    <title>Scholarship Applications</title>
    <style>
        body {
            font-family: 'Raleway', sans-serif;
            display: flex;
            flex-direction: column;
            gap: 100px;
            width: 100vw;
        }

        .main {
            margin: 0px auto;
            max-width: 1400px;
            width: 100%;
            padding: 20px;
            margin-top: 100px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            margin-top: 20px;
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

        .status-rejected {
            background: #f8d7da;
            color: #721c24;
        }

        .amount {
            font-weight: 600;
            color: #28a745;
        }

        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border: 1px solid #f5c6cb;
        }

        .success-message {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border: 1px solid #c3e6cb;
        }

        .status-actions {
            display: flex;
            gap: 5px;
            flex-wrap: wrap;
        }

        .status-btn {
            padding: 4px 8px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.8em;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .status-btn.approve {
            background: #28a745;
            color: white;
        }

        .status-btn.approve:hover {
            background: #218838;
        }

        .status-btn.reject {
            background: #dc3545;
            color: white;
        }

        .status-btn.reject:hover {
            background: #c82333;
        }

        .status-btn.pending {
            background: #ffc107;
            color: #212529;
        }

        .status-btn.pending:hover {
            background: #e0a800;
        }

        .name {
            white-space: nowrap !important;
            display: flex;
            align-items: center;
            column-gap: 4px;
        }

        .view-btn {
            border: none;
            border-radius: 100%;
            cursor: pointer;
            font-size: 0.8em;
            font-weight: 500;
            background: #17a2b8;
            color: white;
            transition: all 0.3s ease;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .view-btn>svg {
            padding: 5px;
            width: 30px;
            height: 30px;
        }

        .view-btn:hover {
            background: #138496;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 90%;
            max-width: 600px;
            border-radius: 8px;
            max-height: 80vh;
            overflow-y: auto;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover {
            color: black;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
        }

        .form-group textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            resize: vertical;
            min-height: 80px;
        }

        .form-actions {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            margin-top: 20px;
        }

        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 500;
        }

        .btn-primary {
            background: #007bff;
            color: white;
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn:hover {
            opacity: 0.8;
        }

        .detail-section {
            margin-bottom: 20px;
        }

        .detail-section h3 {
            color: #333;
            border-bottom: 2px solid #007bff;
            padding-bottom: 5px;
            margin-bottom: 15px;
        }

        .detail-row {
            display: flex;
            margin-bottom: 10px;
            border-bottom: 1px solid #eee;
            padding-bottom: 8px;
        }

        .detail-label {
            font-weight: 600;
            min-width: 150px;
            color: #555;
        }

        .detail-value {
            flex: 1;
            color: #333;
        }

        .detail-value.amount {
            font-weight: 600;
            color: #28a745;
        }

        .detail-value.status {
            font-weight: 500;
        }

        .detail-value.status.pending {
            color: #856404;
        }

        .detail-value.status.approved {
            color: #155724;
        }

        .detail-value.status.rejected {
            color: #721c24;
        }
    </style>
</head>

<body>
    <?php include '../components/admin-navbar.php'; ?>
    <div class="main animate-fade-in">
        <h1>Scholarship Applications</h1>

        <?php
        // Check if notes column exists and show notice if it doesn't
        $check_column = $conn->query("SHOW COLUMNS FROM scholarship_applications LIKE 'notes'");
        if ($check_column->num_rows == 0):
        ?>
            <div class="error-message">
                <strong>Database Update Required:</strong> The 'notes' column is missing from the scholarship_applications table.
                To enable notes functionality, please run this SQL query in phpMyAdmin:<br><br>
                <code>ALTER TABLE scholarship_applications ADD COLUMN notes TEXT AFTER application_date;</code><br><br>
                Or use the provided <code>add_notes_column.sql</code> file in your project root.
            </div>
        <?php endif; ?>

        <?php if (isset($error_message)): ?>
            <div class="error-message">
                <strong>Error:</strong> <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($success_message)): ?>
            <div class="success-message">
                <strong>Success:</strong> <?php echo htmlspecialchars($success_message); ?>
            </div>
        <?php endif; ?>

        <div style="overflow-x:auto;">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Student Name</th>
                        <th>Student Email</th>
                        <th>Scholarship</th>
                        <th>Amount</th>
                        <th>Application Date</th>
                        <th>Status</th>
                        <th>Notes</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($applications)): ?>
                        <tr>
                            <td colspan="9" style="text-align:center;">No applications found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($applications as $application): ?>
                            <tr>
                                <td><?php echo $application['id']; ?></td>
                                <td class="name">
                                    <?php echo htmlspecialchars($application['student_name']); ?>
                                    <button class="view-btn"
                                        data-application='<?php echo htmlspecialchars(json_encode($application), ENT_QUOTES, 'UTF-8'); ?>'
                                        onclick="viewStudentDetails(this)">
                                        <svg xmlns="http://www.w3.org/2000/svg" xml:space="preserve" id="eye" width="48" height="48" x="0" y="0" fill="#fff" version="1.1" viewBox="0 0 48 48">
                                            <path d="M24 39C6 39 0 24 0 24S6 9 24 9s24 15 24 15-6 15-24 15zm0-24c-4.971 0-9 4.029-9 9s4.029 9 9 9 9-4.029 9-9-4.029-9-9-9zm0 15c-3.312 0-6-2.688-6-6h6l-4.242-4.242A5.97 5.97 0 0 1 24 18c3.312 0 6 2.688 6 6s-2.688 6-6 6z"></path>
                                        </svg>
                                    </button>
                                </td>
                                <td><?php echo htmlspecialchars($application['student_email']); ?></td>
                                <td class="name">
                                    <?php echo htmlspecialchars($application['scholarship_title']); ?>
                                    <button class="view-btn"
                                        data-application='<?php echo htmlspecialchars(json_encode($application), ENT_QUOTES, 'UTF-8'); ?>'
                                        onclick="viewScholarshipDetails(this)">
                                        <svg xmlns="http://www.w3.org/2000/svg" xml:space="preserve" id="eye" width="48" height="48" x="0" y="0" fill="#fff" version="1.1" viewBox="0 0 48 48">
                                            <path d="M24 39C6 39 0 24 0 24S6 9 24 9s24 15 24 15-6 15-24 15zm0-24c-4.971 0-9 4.029-9 9s4.029 9 9 9 9-4.029 9-9-4.029-9-9-9zm0 15c-3.312 0-6-2.688-6-6h6l-4.242-4.242A5.97 5.97 0 0 1 24 18c3.312 0 6 2.688 6 6s-2.688 6-6 6z"></path>
                                        </svg>
                                    </button>
                                </td>
                                <td class="amount">Rs. <?php echo number_format($application['scholarship_amount']); ?></td>
                                <td><?php echo date('M j, Y', strtotime($application['application_date'])); ?></td>
                                <td>
                                    <span class="status-badge status-<?php echo $application['status']; ?>">
                                        <?php echo ucfirst($application['status']); ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars($application['notes'] ?? ''); ?></td>
                                <td>
                                    <div class="status-actions">
                                        <?php if ($application['status'] !== 'approved'): ?>
                                            <button class="status-btn approve" onclick="updateStatus(<?php echo $application['id']; ?>, 'approved')">
                                                Approve
                                            </button>
                                        <?php endif; ?>
                                        <?php if ($application['status'] !== 'rejected'): ?>
                                            <button class="status-btn reject" onclick="updateStatus(<?php echo $application['id']; ?>, 'rejected')">
                                                Reject
                                            </button>
                                        <?php endif; ?>
                                        <?php if ($application['status'] !== 'pending'): ?>
                                            <button class="status-btn pending" onclick="updateStatus(<?php echo $application['id']; ?>, 'pending')">
                                                Mark Pending
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Status Update Modal -->
    <div id="statusModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Update Application Status</h2>
            <form id="statusForm" method="POST">
                <input type="hidden" id="applicationId" name="application_id">
                <input type="hidden" id="statusValue" name="status">

                <div class="form-group">
                    <label for="notes">Notes (optional):</label>
                    <textarea id="notes" name="notes" placeholder="Add any notes about this application..."></textarea>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Status</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Student Details Modal -->
    <div id="studentModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Student Details</h2>
            <div id="studentDetails">
                <!-- Student details will be populated here -->
            </div>
        </div>
    </div>

    <!-- Scholarship Details Modal -->
    <div id="scholarshipModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Scholarship Details</h2>
            <div id="scholarshipDetails">
                <!-- Scholarship details will be populated here -->
            </div>
        </div>
    </div>

    <script>
        function updateStatus(applicationId, status) {
            document.getElementById('applicationId').value = applicationId;
            document.getElementById('statusValue').value = status;
            document.getElementById('statusModal').style.display = 'block';
        }

        function closeModal() {
            document.getElementById('statusModal').style.display = 'none';
            document.getElementById('notes').value = '';
        }

        function viewStudentDetails(button) {
            try {
                const application = JSON.parse(button.getAttribute('data-application'));
                const modal = document.getElementById('studentModal');
                const detailsDiv = document.getElementById('studentDetails');

                detailsDiv.innerHTML = `
                    <div class="detail-section">
                        <h3>Student Information</h3>
                        <div class="detail-row">
                            <div class="detail-label">Name:</div>
                            <div class="detail-value">${application.student_name || 'N/A'}</div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Email:</div>
                            <div class="detail-value">${application.student_email || 'N/A'}</div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Student ID:</div>
                            <div class="detail-value">${application.student_id || 'N/A'}</div>
                        </div>
                    </div>
                    
                    <div class="detail-section">
                        <h3>Application Information</h3>
                        <div class="detail-row">
                            <div class="detail-label">Application ID:</div>
                            <div class="detail-value">${application.id || 'N/A'}</div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Application Date:</div>
                            <div class="detail-value">${application.application_date ? new Date(application.application_date).toLocaleDateString() : 'N/A'}</div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Status:</div>
                            <div class="detail-value status ${application.status || 'pending'}">${(application.status || 'pending').charAt(0).toUpperCase() + (application.status || 'pending').slice(1)}</div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Notes:</div>
                            <div class="detail-value">${application.notes || 'No notes available'}</div>
                        </div>
                    </div>
                `;

                modal.style.display = 'block';
            } catch (error) {
                console.error('Error viewing student details:', error);
                alert('Error loading student details. Please try again.');
            }
        }

        function viewScholarshipDetails(button) {
            try {
                const application = JSON.parse(button.getAttribute('data-application'));
                const modal = document.getElementById('scholarshipModal');
                const detailsDiv = document.getElementById('scholarshipDetails');

                detailsDiv.innerHTML = `
                    <div class="detail-section">
                        <h3>Scholarship Information</h3>
                        <div class="detail-row">
                            <div class="detail-label">Title:</div>
                            <div class="detail-value">${application.scholarship_title || 'N/A'}</div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Amount:</div>
                            <div class="detail-value amount">Rs. ${application.scholarship_amount ? parseInt(application.scholarship_amount).toLocaleString() : 'N/A'}</div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Description:</div>
                            <div class="detail-value">${application.scholarship_description || 'No description available'}</div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Requirements:</div>
                            <div class="detail-value">${application.scholarship_requirements || 'No requirements specified'}</div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Deadline:</div>
                            <div class="detail-value">${application.scholarship_deadline ? new Date(application.scholarship_deadline).toLocaleDateString() : 'N/A'}</div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Available Slots:</div>
                            <div class="detail-value">${application.scholarship_slots || 'N/A'}</div>
                        </div>
                    </div>
                    
                    <div class="detail-section">
                        <h3>Application Details</h3>
                        <div class="detail-row">
                            <div class="detail-label">Application ID:</div>
                            <div class="detail-value">${application.id || 'N/A'}</div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Student:</div>
                            <div class="detail-value">${application.student_name || 'N/A'} (${application.student_email || 'N/A'})</div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Application Date:</div>
                            <div class="detail-value">${application.application_date ? new Date(application.application_date).toLocaleDateString() : 'N/A'}</div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Status:</div>
                            <div class="detail-value status ${application.status || 'pending'}">${(application.status || 'pending').charAt(0).toUpperCase() + (application.status || 'pending').slice(1)}</div>
                        </div>
                    </div>
                `;

                modal.style.display = 'block';
            } catch (error) {
                console.error('Error viewing scholarship details:', error);
                alert('Error loading scholarship details. Please try again.');
            }
        }

        // Close modals when clicking on X
        document.querySelectorAll('.close').forEach(function(closeBtn) {
            closeBtn.onclick = function() {
                this.closest('.modal').style.display = 'none';
            }
        });

        // Close modals when clicking outside of them
        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                event.target.style.display = 'none';
            }
        }
    </script>
</body>

</html>