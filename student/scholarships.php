<?php
session_start();
require_once '../config/database.php';

// Check if user is logged in and is a student
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'student') {
    header("Location: ../index.php");
    exit();
}

// Fetch available scholarships
$stmt = $conn->prepare("SELECT * FROM scholarships WHERE status = 'active'");
$stmt->execute();
$scholarships = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Check if student has already applied
$stmt = $conn->prepare("SELECT scholarship_id FROM scholarship_applications WHERE student_id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$applied_scholarships = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Convert to simple array of scholarship IDs
$applied_scholarship_ids = array_column($applied_scholarships, 'scholarship_id');
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
    <title>Student - Scholarships</title>
    <style>
        .scholarships-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 100px 20px;
        }

        .scholarship-card {
            background: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .scholarship-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .scholarship-title {
            font-size: 1.5em;
            color: #333;
            margin: 0;
        }

        .scholarship-amount {
            font-size: 1.2em;
            color: #2ecc71;
            font-weight: bold;
        }

        .scholarship-description {
            color: #666;
            margin-bottom: 15px;
        }

        .scholarship-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 15px;
        }

        .detail-item {
            display: flex;
            flex-direction: column;
        }

        .detail-label {
            font-size: 0.9em;
            color: #888;
        }

        .detail-value {
            font-weight: 500;
            color: #333;
        }

        .apply-button {
            background-color: #2ecc71;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 500;
            transition: background-color 0.3s;
        }

        .apply-button:hover {
            background-color: #27ae60;
        }

        .apply-button:disabled {
            background-color: #95a5a6;
            cursor: not-allowed;
        }

        .applied-badge {
            background-color: #3498db;
            color: white;
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 0.9em;
        }
    </style>
</head>

<body>
    <?php include '../components/student-navbar.php'; ?>
    <div class="scholarships-container animate-fade-in">
        <h1>Available Scholarships</h1>
        <?php if (empty($scholarships)): ?>
            <p>No scholarships are currently available.</p>
        <?php else: ?>
            <?php foreach ($scholarships as $scholarship): ?>
                <div class="scholarship-card">
                    <div class="scholarship-header">
                        <h2 class="scholarship-title"><?php echo htmlspecialchars($scholarship['title']); ?></h2>
                        <span class="scholarship-amount">Rs. <?php echo number_format($scholarship['amount']); ?></span>
                    </div>
                    <p class="scholarship-description"><?php echo htmlspecialchars($scholarship['description']); ?></p>
                    <div class="scholarship-details">
                        <div class="detail-item">
                            <span class="detail-label">Deadline</span>
                            <span class="detail-value"><?php echo date('F j, Y', strtotime($scholarship['deadline'])); ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Requirements</span>
                            <span class="detail-value"><?php echo htmlspecialchars($scholarship['requirements']); ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Available Slots</span>
                            <span class="detail-value"><?php echo $scholarship['available_slots']; ?></span>
                        </div>
                    </div>
                    <?php if (in_array($scholarship['id'], $applied_scholarship_ids)): ?>
                        <span class="applied-badge">Already Applied</span>
                    <?php else: ?>
                        <button class="apply-button" onclick="applyForScholarship(<?php echo $scholarship['id']; ?>)">
                            Apply Now
                        </button>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <script>
        function applyForScholarship(scholarshipId) {
            if (confirm('Are you sure you want to apply for this scholarship?')) {
                fetch('apply_scholarship.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: 'scholarship_id=' + scholarshipId
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Application submitted successfully!');
                            location.reload();
                        } else {
                            alert(data.message || 'Failed to submit application. Please try again.');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred. Please try again.');
                    });
            }
        }
    </script>
</body>

</html>