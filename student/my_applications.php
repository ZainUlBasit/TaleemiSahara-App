<?php
session_start();
require_once '../config/database.php';

// Check if user is logged in and is a student
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'student') {
    header("Location: ../index.php");
    exit();
}

// Fetch student's applied scholarships with details
$sql = "SELECT sa.*, sch.title as scholarship_title, sch.amount as scholarship_amount,
               sch.description as scholarship_description, sch.requirements as scholarship_requirements,
               sch.deadline as scholarship_deadline, sch.available_slots as scholarship_slots
        FROM scholarship_applications sa
        JOIN scholarships sch ON sa.scholarship_id = sch.id
        WHERE sa.student_id = ?
        ORDER BY sa.application_date DESC";

try {
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $applications = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
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
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300..700&family=Raleway:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@400..700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="../css/font.css">
    <title>My Applications</title>
    <style>
        body {
            font-family: 'Raleway', sans-serif;
            background-color: #f8f9fa;
        }

        .applications-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 100px 20px 20px;
        }

        .page-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .page-header h1 {
            color: #333;
            font-size: 2.5em;
            margin-bottom: 10px;
        }

        .page-header p {
            color: #666;
            font-size: 1.1em;
        }

        .application-card {
            background: white;
            border: 1px solid #ddd;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .application-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }

        .application-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .scholarship-info {
            flex: 1;
            min-width: 300px;
        }

        .scholarship-title {
            font-size: 1.8em;
            color: #333;
            margin: 0 0 10px 0;
            font-weight: 600;
        }

        .scholarship-amount {
            font-size: 1.4em;
            color: #28a745;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .status-section {
            text-align: right;
            min-width: 150px;
        }

        .status-badge {
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 0.9em;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
            border: 2px solid #ffeaa7;
        }

        .status-approved {
            background: #d4edda;
            color: #155724;
            border: 2px solid #c3e6cb;
        }

        .status-rejected {
            background: #f8d7da;
            color: #721c24;
            border: 2px solid #f5c6cb;
        }

        .application-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }

        .detail-group {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #007bff;
        }

        .detail-group h4 {
            margin: 0 0 10px 0;
            color: #333;
            font-size: 1.1em;
            font-weight: 600;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            padding-bottom: 8px;
            border-bottom: 1px solid #e9ecef;
        }

        .detail-row:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }

        .detail-label {
            font-weight: 500;
            color: #555;
        }

        .detail-value {
            font-weight: 600;
            color: #333;
        }

        .detail-value.amount {
            color: #28a745;
        }

        .detail-value.date {
            color: #6c757d;
        }

        .application-notes {
            background: #e3f2fd;
            border: 1px solid #bbdefb;
            border-radius: 8px;
            padding: 15px;
            margin-top: 15px;
        }

        .notes-label {
            font-weight: 600;
            color: #1976d2;
            margin-bottom: 8px;
        }

        .notes-content {
            color: #333;
            line-height: 1.5;
        }

        .no-applications {
            text-align: center;
            padding: 60px 20px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .no-applications h2 {
            color: #666;
            margin-bottom: 15px;
        }

        .no-applications p {
            color: #888;
            margin-bottom: 25px;
        }

        .apply-now-btn {
            background: #007bff;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 500;
            transition: background-color 0.3s;
            display: inline-block;
        }

        .apply-now-btn:hover {
            background: #0056b3;
            color: white;
            text-decoration: none;
        }

        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid #f5c6cb;
        }

        .stats-section {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .stat-number {
            font-size: 2em;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .stat-label {
            color: #666;
            font-size: 0.9em;
        }

        .stat-pending .stat-number {
            color: #ffc107;
        }

        .stat-approved .stat-number {
            color: #28a745;
        }

        .stat-rejected .stat-number {
            color: #dc3545;
        }

        .stat-total .stat-number {
            color: #007bff;
        }
    </style>
</head>

<body>
    <?php include '../components/student-navbar.php'; ?>

    <div class="applications-container">
        <div class="page-header">
            <h1>My Scholarship Applications</h1>
            <p>Track the status of your scholarship applications</p>
        </div>

        <?php if (isset($error_message)): ?>
            <div class="error-message">
                <strong>Error:</strong> <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>

        <?php if (empty($applications)): ?>
            <div class="no-applications">
                <h2>No Applications Yet</h2>
                <p>You haven't applied for any scholarships yet. Start exploring available opportunities!</p>
                <a href="scholarships.php" class="apply-now-btn">Browse Scholarships</a>
            </div>
        <?php else: ?>
            <!-- Statistics Section -->
            <div class="stats-section">
                <?php
                $total_applications = count($applications);
                $pending_count = 0;
                $approved_count = 0;
                $rejected_count = 0;

                foreach ($applications as $app) {
                    switch ($app['status']) {
                        case 'pending':
                            $pending_count++;
                            break;
                        case 'approved':
                            $approved_count++;
                            break;
                        case 'rejected':
                            $rejected_count++;
                            break;
                    }
                }
                ?>
                <div class="stat-card stat-total">
                    <div class="stat-number"><?php echo $total_applications; ?></div>
                    <div class="stat-label">Total Applications</div>
                </div>
                <div class="stat-card stat-pending">
                    <div class="stat-number"><?php echo $pending_count; ?></div>
                    <div class="stat-label">Pending Review</div>
                </div>
                <div class="stat-card stat-approved">
                    <div class="stat-number"><?php echo $approved_count; ?></div>
                    <div class="stat-label">Approved</div>
                </div>
                <div class="stat-card stat-rejected">
                    <div class="stat-number"><?php echo $rejected_count; ?></div>
                    <div class="stat-label">Rejected</div>
                </div>
            </div>

            <!-- Applications List -->
            <?php foreach ($applications as $application): ?>
                <div class="application-card">
                    <div class="application-header">
                        <div class="scholarship-info">
                            <h2 class="scholarship-title"><?php echo htmlspecialchars($application['scholarship_title']); ?></h2>
                            <div class="scholarship-amount">Rs. <?php echo number_format($application['scholarship_amount']); ?></div>
                        </div>
                        <div class="status-section">
                            <span class="status-badge status-<?php echo $application['status']; ?>">
                                <?php echo ucfirst($application['status']); ?>
                            </span>
                        </div>
                    </div>

                    <div class="application-details">
                        <div class="detail-group">
                            <h4>Application Details</h4>
                            <div class="detail-row">
                                <span class="detail-label">Application ID:</span>
                                <span class="detail-value">#<?php echo $application['id']; ?></span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Applied Date:</span>
                                <span class="detail-value date"><?php echo date('F j, Y', strtotime($application['application_date'])); ?></span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Status:</span>
                                <span class="detail-value"><?php echo ucfirst($application['status']); ?></span>
                            </div>
                        </div>

                        <div class="detail-group">
                            <h4>Scholarship Details</h4>
                            <div class="detail-row">
                                <span class="detail-label">Deadline:</span>
                                <span class="detail-value date"><?php echo date('F j, Y', strtotime($application['scholarship_deadline'])); ?></span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Available Slots:</span>
                                <span class="detail-value"><?php echo $application['scholarship_slots']; ?></span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Requirements:</span>
                                <span class="detail-value"><?php echo htmlspecialchars($application['scholarship_requirements'] ?: 'No specific requirements'); ?></span>
                            </div>
                        </div>
                    </div>

                    <?php if (!empty($application['scholarship_description'])): ?>
                        <div class="detail-group">
                            <h4>Description</h4>
                            <p style="margin: 0; line-height: 1.6; color: #555;">
                                <?php echo htmlspecialchars($application['scholarship_description']); ?>
                            </p>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($application['notes'])): ?>
                        <div class="application-notes">
                            <div class="notes-label">Admin Notes:</div>
                            <div class="notes-content"><?php echo htmlspecialchars($application['notes']); ?></div>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>

</html>