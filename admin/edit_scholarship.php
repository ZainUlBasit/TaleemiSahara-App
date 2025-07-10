<?php
session_start();
require_once '../config/database.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

// Check if an ID is provided and is valid
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: available_scholarships.php");
    exit();
}
$scholarship_id = intval($_GET['id']);
$error = '';
$success = '';

// Fetch scholarship details
$stmt = $conn->prepare("SELECT * FROM scholarships WHERE id = ?");
$stmt->bind_param("i", $scholarship_id);
$stmt->execute();
$result = $stmt->get_result();
$scholarship = $result->fetch_assoc();
$stmt->close();

// If no scholarship is found, redirect
if (!$scholarship) {
    header("Location: available_scholarships.php");
    exit();
}

// Handle form submission for update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $amount = floatval($_POST['amount'] ?? 0);
    $requirements = trim($_POST['requirements'] ?? '');
    $deadline = $_POST['deadline'] ?? '';
    $available_slots = intval($_POST['available_slots'] ?? 0);
    $status = $_POST['status'] ?? 'inactive';

    if (empty($title) || $amount <= 0 || empty($deadline) || $available_slots < 0) {
        $error = 'Please fill in all required fields with valid values.';
    } else {
        $stmt = $conn->prepare("UPDATE scholarships SET title = ?, description = ?, amount = ?, requirements = ?, deadline = ?, available_slots = ?, status = ? WHERE id = ?");
        $stmt->bind_param("ssdssisi", $title, $description, $amount, $requirements, $deadline, $available_slots, $status, $scholarship_id);

        if ($stmt->execute()) {
            $success = 'Scholarship updated successfully! Redirecting...';
            // Refresh scholarship data to show in the form
            $scholarship = [
                'title' => $title,
                'description' => $description,
                'amount' => $amount,
                'requirements' => $requirements,
                'deadline' => $deadline,
                'available_slots' => $available_slots,
                'status' => $status
            ];
            header("Refresh:2; url=available_scholarships.php");
        } else {
            $error = 'Failed to update scholarship: ' . $stmt->error;
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Scholarship</title>
    <link rel="stylesheet" href="../css/font.css">
    <link rel="stylesheet" href="../css/general.css">
    <style>
        /* Reusing styles from add_scholarship.php - can be centralized */
        body {
            font-family: 'Raleway', sans-serif;
            display: flex;
            flex-direction: column;
            gap: 100px;
            width: 100vw;
        }

        .main {
            margin: 0px auto;
            max-width: 600px;
            width: 100%;
            padding: 20px;
            border: 2px solid #465462;
            margin-top: 100px;
            border-radius: 10px;
            background: #fff;
        }

        h1 {
            color: #465462;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
            color: #333;
        }

        input[type="text"],
        input[type="number"],
        input[type="date"],
        select,
        textarea {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
            font-size: 1rem;
            border: 1.5px solid #465462;
            border-radius: 3px;
        }

        textarea {
            resize: vertical;
            min-height: 80px;
        }

        button {
            padding: 10px 15px;
            background-color: #465462;
            color: white;
            border: none;
            cursor: pointer;
            font-weight: 500;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #2c3e50;
        }

        .error {
            color: #e74c3c;
            background: #fdf2f2;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            border: 1px solid #f5c6cb;
        }

        .success {
            color: #27ae60;
            background: #f0f9f0;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            border: 1px solid #c3e6cb;
        }

        .required {
            color: #e74c3c;
        }

        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: #007bff;
            text-decoration: none;
        }

        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <?php include '../components/admin-navbar.php'; ?>
    <div class="main animate-fade-in">
        <a href="available_scholarships.php" class="back-link">&larr; Back to Available Scholarships</a>
        <h1>Edit Scholarship</h1>

        <?php if ($error): ?><div class="error"><?php echo $error; ?></div><?php endif; ?>
        <?php if ($success): ?><div class="success"><?php echo $success; ?></div><?php endif; ?>

        <form method="POST" action="edit_scholarship.php?id=<?php echo $scholarship_id; ?>">
            <div class="form-group">
                <label for="title">Title <span class="required">*</span>:</label>
                <input type="text" id="title" name="title" required value="<?php echo htmlspecialchars($scholarship['title']); ?>">
            </div>
            <div class="form-group">
                <label for="description">Description:</label>
                <textarea id="description" name="description"><?php echo htmlspecialchars($scholarship['description']); ?></textarea>
            </div>
            <div class="form-group">
                <label for="amount">Amount (Rs.) <span class="required">*</span>:</label>
                <input type="number" id="amount" name="amount" step="1" min="1" required value="<?php echo htmlspecialchars($scholarship['amount']); ?>">
            </div>
            <div class="form-group">
                <label for="requirements">Requirements:</label>
                <textarea id="requirements" name="requirements"><?php echo htmlspecialchars($scholarship['requirements']); ?></textarea>
            </div>
            <div class="form-group">
                <label for="deadline">Deadline <span class="required">*</span>:</label>
                <input type="date" id="deadline" name="deadline" required value="<?php echo htmlspecialchars($scholarship['deadline']); ?>">
            </div>
            <div class="form-group">
                <label for="available_slots">Available Slots <span class="required">*</span>:</label>
                <input type="number" id="available_slots" name="available_slots" min="0" required value="<?php echo htmlspecialchars($scholarship['available_slots']); ?>">
            </div>
            <div class="form-group">
                <label for="status">Status:</label>
                <select id="status" name="status">
                    <option value="active" <?php if ($scholarship['status'] == 'active') echo 'selected'; ?>>Active</option>
                    <option value="inactive" <?php if ($scholarship['status'] == 'inactive') echo 'selected'; ?>>Inactive</option>
                </select>
            </div>
            <button type="submit">Update Scholarship</button>
        </form>
    </div>
</body>

</html>