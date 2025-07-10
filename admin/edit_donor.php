<?php
session_start();
require_once '../config/database.php';
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('Invalid donor ID.');
}
$donor_id = intval($_GET['id']);
$error = $success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $organization = trim($_POST['organization']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    if (!$name || !$email) {
        $error = 'Name and Email are required.';
    } else {
        $conn->begin_transaction();
        try {
            $stmt = $conn->prepare("UPDATE users SET name=?, email=? WHERE id=? AND user_type='donor'");
            $stmt->bind_param('ssi', $name, $email, $donor_id);
            $stmt->execute();
            $stmt->close();
            $stmt2 = $conn->prepare("UPDATE donor_profiles SET organization=?, phone=?, address=? WHERE user_id=?");
            $stmt2->bind_param('sssi', $organization, $phone, $address, $donor_id);
            $stmt2->execute();
            $stmt2->close();
            $conn->commit();
            header("Location: view_donor.php?id=$donor_id");
            exit();
        } catch (Exception $e) {
            $conn->rollback();
            $error = 'Failed to update donor.';
        }
    }
}
$stmt = $conn->prepare("SELECT u.id, u.name, u.email, dp.organization, dp.phone, dp.address FROM users u LEFT JOIN donor_profiles dp ON u.id = dp.user_id WHERE u.id = ? AND u.user_type = 'donor'");
$stmt->bind_param('i', $donor_id);
$stmt->execute();
$donor = $stmt->get_result()->fetch_assoc();
$stmt->close();
if (!$donor) die('Donor not found.');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Edit Donor</title>
    <link rel="stylesheet" href="../css/general.css">
    <style>
        .students-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 100px 20px 20px;
        }

        .edit-title {
            font-size: 2rem;
            margin-bottom: 1rem;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .label {
            font-weight: bold;
            color: #465462;
        }

        .input {
            width: 100%;
            padding: 8px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        .btn {
            background: #465462;
            color: #fff;
            padding: 10px 20px;
            border-radius: 5px;
            border: none;
            cursor: pointer;
        }

        .btn:hover {
            background: #96ADC5;
            color: #333;
        }

        .error {
            color: #dc3545;
            margin-bottom: 1rem;
        }

        .success {
            color: #28a745;
            margin-bottom: 1rem;
        }
    </style>
</head>

<body>
    <?php include '../components/admin-navbar.php'; ?>
    <div class="edit-container students-container">
        <div class="edit-title">Edit Donor</div>
        <?php if ($error): ?><div class="error"><?php echo $error; ?></div><?php endif; ?>
        <form method="POST">
            <div class="form-group">
                <label class="label">Name</label>
                <input class="input" type="text" name="name" value="<?php echo htmlspecialchars($donor['name']); ?>" required>
            </div>
            <div class="form-group">
                <label class="label">Email</label>
                <input class="input" type="email" name="email" value="<?php echo htmlspecialchars($donor['email']); ?>" required>
            </div>
            <div class="form-group">
                <label class="label">Organization</label>
                <input class="input" type="text" name="organization" value="<?php echo htmlspecialchars($donor['organization'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label class="label">Phone</label>
                <input class="input" type="text" name="phone" value="<?php echo htmlspecialchars($donor['phone'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label class="label">Address</label>
                <input class="input" type="text" name="address" value="<?php echo htmlspecialchars($donor['address'] ?? ''); ?>">
            </div>
            <button class="btn" type="submit">Update Donor</button>
        </form>
    </div>
</body>

</html>