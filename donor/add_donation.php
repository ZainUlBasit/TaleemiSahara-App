<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once '../config/database.php';

// Check if user is logged in and is a donor
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'donor') {
    header("Location: ../index.php");
    exit();
}

$donor_id = $_SESSION['user_id'];
$error = '';
$success = '';

// Fetch scholarships for dropdown
$scholarships = [];
$stmt = $conn->prepare("SELECT id, title FROM scholarships WHERE status = 'active'");
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $scholarships[] = $row;
}
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $scholarship_id = isset($_POST['scholarship_id']) && $_POST['scholarship_id'] !== '' ? intval($_POST['scholarship_id']) : null;
    $amount = isset($_POST['amount']) ? floatval($_POST['amount']) : 0;
    $donation_date = isset($_POST['donation_date']) ? $_POST['donation_date'] : date('Y-m-d');
    $payment_method = isset($_POST['payment_method']) ? trim($_POST['payment_method']) : '';
    $transaction_id = isset($_POST['transaction_id']) ? trim($_POST['transaction_id']) : '';
    $notes = isset($_POST['notes']) ? trim($_POST['notes']) : '';

    // Validation
    if ($amount <= 0) {
        $error = 'Please enter a valid donation amount.';
    } elseif (!$donation_date) {
        $error = 'Please select a donation date.';
    } else {
        $stmt = $conn->prepare("INSERT INTO donations (donor_id, scholarship_id, amount, donation_date, payment_method, transaction_id, notes, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')");
        if ($scholarship_id) {
            $stmt->bind_param("iisssss", $donor_id, $scholarship_id, $amount, $donation_date, $payment_method, $transaction_id, $notes);
        } else {
            // If no scholarship selected, set scholarship_id to NULL
            $null = null;
            $stmt->bind_param("iisssss", $donor_id, $null, $amount, $donation_date, $payment_method, $transaction_id, $notes);
        }
        if ($stmt->execute()) {
            $success = 'Donation added successfully!';
        } else {
            $error = 'Failed to add donation: ' . $stmt->error;
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
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300..700&family=Raleway:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@400..700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="../css/font.css">
    <link rel="stylesheet" href="../css/general.css">
    <title>Add Donation</title>
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
            border: 2px solid #e74c3c;
            margin-top: 100px;
            border-radius: 10px;
            background: #fff;
        }

        h1 {
            color: #e74c3c;
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
            border: 1.5px solid #e74c3c;
            border-radius: 3px;
        }

        textarea {
            resize: vertical;
            min-height: 60px;
        }

        button {
            padding: 10px 15px;
            background-color: #e74c3c;
            color: white;
            border: none;
            cursor: pointer;
            font-weight: 500;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #c0392b;
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
    <?php include '../components/donor-navbar.php'; ?>
    <div class="main animate-fade-in">
        <h1>Add Donation</h1>
        <?php if ($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="success"><?php echo $success; ?></div>
        <?php endif; ?>
        <form method="POST" action="">
            <div class="form-group">
                <label for="scholarship_id">Scholarship (optional):</label>
                <select id="scholarship_id" name="scholarship_id">
                    <option value="">-- General Donation --</option>
                    <?php foreach ($scholarships as $scholarship): ?>
                        <option value="<?php echo $scholarship['id']; ?>" <?php if (isset($_POST['scholarship_id']) && $_POST['scholarship_id'] == $scholarship['id']) echo 'selected'; ?>><?php echo htmlspecialchars($scholarship['title']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="amount">Amount (Rs.) <span class="required">*</span>:</label>
                <input type="number" id="amount" name="amount" step="0.01" min="1" required value="<?php echo isset($_POST['amount']) ? htmlspecialchars($_POST['amount']) : ''; ?>">
            </div>
            <div class="form-group">
                <label for="donation_date">Donation Date <span class="required">*</span>:</label>
                <input type="date" id="donation_date" name="donation_date" required value="<?php echo isset($_POST['donation_date']) ? htmlspecialchars($_POST['donation_date']) : date('Y-m-d'); ?>">
            </div>
            <div class="form-group">
                <label for="payment_method">Payment Method:</label>
                <input type="text" id="payment_method" name="payment_method" value="<?php echo isset($_POST['payment_method']) ? htmlspecialchars($_POST['payment_method']) : ''; ?>">
            </div>
            <div class="form-group">
                <label for="transaction_id">Transaction ID:</label>
                <input type="text" id="transaction_id" name="transaction_id" value="<?php echo isset($_POST['transaction_id']) ? htmlspecialchars($_POST['transaction_id']) : ''; ?>">
            </div>
            <div class="form-group">
                <label for="notes">Notes:</label>
                <textarea id="notes" name="notes"><?php echo isset($_POST['notes']) ? htmlspecialchars($_POST['notes']) : ''; ?></textarea>
            </div>
            <button type="submit">Add Donation</button>
        </form>
    </div>
</body>

</html>