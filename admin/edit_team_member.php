<?php
session_start();
require_once '../config/database.php';

// Authenticate and authorize admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Validate ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: our_team.php");
    exit();
}
$member_id = intval($_GET['id']);
$error = '';

// Fetch member details
try {
    $stmt = $conn->prepare("SELECT * FROM team_members WHERE id = ?");
    $stmt->bind_param("i", $member_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $member = $result->fetch_assoc();
    $stmt->close();
    if (!$member) {
        $_SESSION['error_message'] = "Team member not found.";
        header("Location: our_team.php");
        exit();
    }
} catch (mysqli_sql_exception $e) {
    $_SESSION['error_message'] = "Database error: " . $e->getMessage();
    header("Location: our_team.php");
    exit();
}

// Handle form submission for update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $role = trim($_POST['role'] ?? '');
    $status = trim($_POST['status'] ?? 'active');
    $current_image_path = trim($_POST['current_image'] ?? '');

    if (empty($name) || empty($role)) {
        $error = 'Name and role are required fields.';
    } else {
        $image_db_path = $current_image_path; // Assume we are keeping the old image

        // Check if a new file is uploaded
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $image = $_FILES['image'];
            $upload_dir = './uploads/team/';
            $image_name = uniqid() . '_' . basename($image['name']);
            $target_path = $upload_dir . $image_name;
            $new_image_db_path = './uploads/team/' . $image_name;

            // Validate file
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            if (!in_array($image['type'], $allowed_types)) {
                $error = 'Invalid file type. Only JPG, PNG, and GIF are allowed.';
            } elseif ($image['size'] > 2097152) { // 2MB
                $error = 'File size exceeds the 2MB limit.';
            } else {
                // Create upload directory if it doesn't exist
                if (!is_dir($upload_dir)) {
                    if (!mkdir($upload_dir, 0755, true)) {
                        $error = 'Failed to create upload directory. Please check permissions.';
                    }
                }

                // Check if directory is writable
                if (empty($error) && !is_writable($upload_dir)) {
                    $error = 'Upload directory is not writable. Please check permissions.';
                }

                if (empty($error)) {
                    if (move_uploaded_file($image['tmp_name'], $target_path)) {
                        $image_db_path = $new_image_db_path; // Set new path for DB update
                    } else {
                        $error = 'Failed to upload new image. Error code: ' . $image['error'] . '. Please check file permissions.';
                    }
                }
            }
        }

        // Proceed with DB update if no upload error occurred
        if (empty($error)) {
            try {
                $stmt = $conn->prepare("UPDATE team_members SET name = ?, role = ?, image = ?, status = ? WHERE id = ?");
                $stmt->bind_param("ssssi", $name, $role, $image_db_path, $status, $member_id);
                if ($stmt->execute()) {
                    // If image was changed and DB update was successful, delete old image
                    if ($image_db_path !== $current_image_path && !empty($current_image_path)) {
                        $old_image_server_path = '.' . substr($current_image_path, 1);
                        if (file_exists($old_image_server_path)) {
                            unlink($old_image_server_path);
                        }
                    }
                    $_SESSION['success_message'] = 'Team member updated successfully!';
                    header("Location: our_team.php");
                    exit();
                } else {
                    $error = 'Failed to update team member: ' . $stmt->error;
                }
                $stmt->close();
            } catch (mysqli_sql_exception $e) {
                $error = "Database error: " . $e->getMessage();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Team Member</title>
    <link rel="stylesheet" href="../css/font.css">
    <link rel="stylesheet" href="../css/general.css">
    <style>
        body {
            font-family: 'Raleway', sans-serif;
            background-color: #f4f7f6;
            display: flex;
            flex-direction: column;
        }

        .main {
            max-width: 700px;
            width: 100%;
            margin: 100px auto 20px;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #2c3e50;
            text-align: center;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }

        input[type="text"],
        input[type="file"],
        select {
            width: 100%;
            padding: 12px;
            box-sizing: border-box;
            font-size: 1rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            transition: border-color 0.3s;
        }

        input:focus,
        select:focus {
            outline: none;
            border-color: #3498db;
        }

        button {
            display: block;
            width: 100%;
            padding: 12px;
            background-color: #34495e;
            color: white;
            border: none;
            cursor: pointer;
            font-weight: 600;
            border-radius: 5px;
            transition: background-color 0.3s;
            font-size: 1rem;
        }

        button:hover {
            background-color: #2c3e50;
        }

        .error {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            color: #e74c3c;
            background: #fdf2f2;
            border: 1px solid #f5c6cb;
        }

        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: #3498db;
            text-decoration: none;
        }

        .back-link:hover {
            text-decoration: underline;
        }

        .current-image {
            display: block;
            max-width: 150px;
            margin-top: 10px;
            border-radius: 5px;
        }
    </style>
</head>

<body>
    <?php include '../components/admin-navbar.php'; ?>
    <div class="main animate-fade-in">
        <a href="our_team.php" class="back-link">&larr; Back to Manage Team</a>
        <h1>Edit Team Member</h1>
        <?php if ($error): ?><div class="error"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="current_image" value="<?php echo htmlspecialchars($member['image']); ?>">
            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" required value="<?php echo htmlspecialchars($member['name']); ?>">
            </div>
            <div class="form-group">
                <label for="role">Role / Position</label>
                <input type="text" id="role" name="role" required value="<?php echo htmlspecialchars($member['role']); ?>">
            </div>
            <div class="form-group">
                <label for="image">New Image (optional)</label>
                <input type="file" id="image" name="image" accept="image/png, image/jpeg, image/gif">
                <p>Current Image:</p>
                <img src="<?php echo htmlspecialchars($member['image']); ?>" alt="Current Image" class="current-image">
            </div>
            <div class="form-group">
                <label for="status">Status</label>
                <select id="status" name="status">
                    <option value="active" <?php if ($member['status'] == 'active') echo 'selected'; ?>>Active</option>
                    <option value="inactive" <?php if ($member['status'] == 'inactive') echo 'selected'; ?>>Inactive</option>
                </select>
            </div>
            <button type="submit">Update Member</button>
        </form>
    </div>
</body>

</html>