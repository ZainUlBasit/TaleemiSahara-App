<?php
session_start();
require_once '../config/database.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $amount = floatval($_POST['amount'] ?? 0);
    $requirements = trim($_POST['requirements'] ?? '');
    $deadline = $_POST['deadline'] ?? '';
    $available_slots = intval($_POST['available_slots'] ?? 1);
    $status = $_POST['status'] ?? 'active';

    if (!$title || !$amount || !$deadline || !$available_slots) {
        $error = 'Please fill in all required fields.';
    } else {
        $stmt = $conn->prepare("INSERT INTO scholarships (title, description, amount, requirements, deadline, available_slots, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssdssis", $title, $description, $amount, $requirements, $deadline, $available_slots, $status);
        if ($stmt->execute()) {
            $success = 'Scholarship added successfully!';
        } else {
            $error = 'Failed to add scholarship: ' . $stmt->error;
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
    <title>Add Scholarship</title>
    <link rel="stylesheet" href="../css/font.css">
    <link rel="stylesheet" href="../css/general.css">
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
            min-height: 60px;
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
        }

        .success {
            color: #27ae60;
            background: #f0f9f0;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
        }

        .required {
            color: #e74c3c;
        }
    </style>
</head>

<body>
    <?php include '../components/admin-navbar.php'; ?>
    <div class="main animate-fade-in">
        <h1>Add Scholarship</h1>
        <?php if ($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="success"><?php echo $success; ?></div>
        <?php endif; ?>
        <form method="POST" action="">
            <div class="form-group">
                <label for="title">Title <span class="required">*</span>:</label>
                <input type="text" id="title" name="title" required value="<?php echo isset($_POST['title']) ? htmlspecialchars($_POST['title']) : ''; ?>">
            </div>
            <div class="form-group">
                <label for="description">Description:</label>
                <textarea id="description" name="description"><?php echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; ?></textarea>
            </div>
            <div class="form-group">
                <label for="amount">Amount (Rs.) <span class="required">*</span>:</label>
                <input type="number" id="amount" name="amount" step="0.01" min="1" required value="<?php echo isset($_POST['amount']) ? htmlspecialchars($_POST['amount']) : ''; ?>">
            </div>
            <div class="form-group">
                <label for="requirements">Requirements:</label>
                <textarea id="requirements" name="requirements"><?php echo isset($_POST['requirements']) ? htmlspecialchars($_POST['requirements']) : ''; ?></textarea>
            </div>
            <div class="form-group">
                <label for="deadline">Deadline <span class="required">*</span>:</label>
                <input type="date" id="deadline" name="deadline" required value="<?php echo isset($_POST['deadline']) ? htmlspecialchars($_POST['deadline']) : ''; ?>">
            </div>
            <div class="form-group">
                <label for="available_slots">Available Slots <span class="required">*</span>:</label>
                <input type="number" id="available_slots" name="available_slots" min="1" required value="<?php echo isset($_POST['available_slots']) ? htmlspecialchars($_POST['available_slots']) : '1'; ?>">
            </div>
            <div class="form-group">
                <label for="status">Status:</label>
                <select id="status" name="status">
                    <option value="active" <?php if (isset($_POST['status']) && $_POST['status'] == 'active') echo 'selected'; ?>>Active</option>
                    <option value="inactive" <?php if (isset($_POST['status']) && $_POST['status'] == 'inactive') echo 'selected'; ?>>Inactive</option>
                </select>
            </div>
            <button type="submit">Add Scholarship</button>
        </form>
    </div>
</body>

</html>