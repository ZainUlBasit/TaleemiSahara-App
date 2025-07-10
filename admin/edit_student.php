<?php
session_start();
require_once '../config/database.php';
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('Invalid student ID.');
}
$student_id = intval($_GET['id']);
$error = $success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $student_id_val = trim($_POST['student_id']);
    $roll_no = trim($_POST['roll_no']);
    $department = trim($_POST['department']);
    if (!$name || !$email) {
        $error = 'Name and Email are required.';
    } else {
        $conn->begin_transaction();
        try {
            $stmt = $conn->prepare("UPDATE users SET name=?, email=? WHERE id=? AND user_type='student'");
            $stmt->bind_param('ssi', $name, $email, $student_id);
            $stmt->execute();
            $stmt->close();
            $stmt2 = $conn->prepare("UPDATE student_profiles SET student_id=?, roll_no=?, department=? WHERE user_id=?");
            $stmt2->bind_param('sssi', $student_id_val, $roll_no, $department, $student_id);
            $stmt2->execute();
            $stmt2->close();
            $conn->commit();
            header("Location: view_student.php?id=$student_id");
            exit();
        } catch (Exception $e) {
            $conn->rollback();
            $error = 'Failed to update student.';
        }
    }
}
$stmt = $conn->prepare("SELECT u.id, u.name, u.email, sp.student_id, sp.roll_no, sp.department FROM users u LEFT JOIN student_profiles sp ON u.id = sp.user_id WHERE u.id = ? AND u.user_type = 'student'");
$stmt->bind_param('i', $student_id);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();
$stmt->close();
if (!$student) die('Student not found.');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Edit Student</title>
    <link rel="stylesheet" href="../css/general.css">
    <style>
        body {
            font-family: 'Raleway', sans-serif;
            background-color: #f4f7f6;
            display: flex;
            flex-direction: column;
        }

        .edit-container {
            width: 100%;
            max-width: 700px;
            margin: 100px auto;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 10px #0001;
            padding: 2rem;
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
    <div class="edit-container">
        <div class="edit-title">Edit Student</div>
        <?php if ($error): ?><div class="error"><?php echo $error; ?></div><?php endif; ?>
        <form method="POST">
            <div class="form-group">
                <label class="label">Name</label>
                <input class="input" type="text" name="name" value="<?php echo htmlspecialchars($student['name']); ?>" required>
            </div>
            <div class="form-group">
                <label class="label">Email</label>
                <input class="input" type="email" name="email" value="<?php echo htmlspecialchars($student['email']); ?>" required>
            </div>
            <div class="form-group">
                <label class="label">Student ID</label>
                <input class="input" type="text" name="student_id" value="<?php echo htmlspecialchars($student['student_id'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label class="label">Roll No</label>
                <input class="input" type="text" name="roll_no" value="<?php echo htmlspecialchars($student['roll_no'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label class="label">Department</label>
                <input class="input" type="text" name="department" value="<?php echo htmlspecialchars($student['department'] ?? ''); ?>">
            </div>
            <button class="btn" type="submit">Update Student</button>
        </form>
    </div>
</body>

</html>