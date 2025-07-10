<?php
session_start();
require_once '../config/database.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $date = trim($_POST['date'] ?? '');
    $status = trim($_POST['status'] ?? 'active');

    if (empty($title) || empty($description) || empty($date) || !isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        $error = 'Please fill in all fields and upload an image.';
    } else {
        // Handle file upload
        $image = $_FILES['image'];
        $upload_dir = './uploads/news/';
        $image_name = uniqid() . '_' . basename($image['name']);
        $target_path = $upload_dir . $image_name;
        $image_db_path = './admin/uploads/news/' . $image_name; // Path to store in DB

        // Validate file type and size
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($image['type'], $allowed_types)) {
            $error = 'Invalid file type. Only JPG, PNG, and GIF are allowed.';
        } elseif ($image['size'] > 2097152) { // 2MB limit
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
                    // File uploaded successfully, now insert into DB
                    try {
                        $stmt = $conn->prepare("INSERT INTO news (title, description, image, date, status) VALUES (?, ?, ?, ?, ?)");
                        $stmt->bind_param("sssss", $title, $description, $image_db_path, $date, $status);
                        if ($stmt->execute()) {
                            $_SESSION['success_message'] = 'News article added successfully!';
                            header("Location: our_news.php");
                            exit();
                        } else {
                            $error = 'Failed to add news article: ' . $stmt->error;
                            unlink($target_path); // Delete uploaded file on DB error
                        }
                        $stmt->close();
                    } catch (mysqli_sql_exception $e) {
                        $error = "Database error: " . $e->getMessage();
                        unlink($target_path); // Delete uploaded file on DB error
                    }
                } else {
                    $error = 'Failed to upload image. Error code: ' . $image['error'] . '. Please check file permissions.';
                }
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
    <title>Add News Article</title>
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
        input[type="date"],
        textarea,
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
        textarea:focus,
        select:focus {
            outline: none;
            border-color: #3498db;
        }

        textarea {
            resize: vertical;
            min-height: 120px;
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

        .error,
        .success {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }

        .error {
            color: #e74c3c;
            background: #fdf2f2;
            border: 1px solid #f5c6cb;
        }

        .success {
            color: #27ae60;
            background: #f0f9f0;
            border: 1px solid #c3e6cb;
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
    </style>
</head>

<body>
    <?php include '../components/admin-navbar.php'; ?>
    <div class="main animate-fade-in">
        <a href="our_news.php" class="back-link">&larr; Back to Manage News</a>
        <h1>Add New News Article</h1>
        <?php if ($error): ?><div class="error"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="title">Title</label>
                <input type="text" id="title" name="title" required>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" required></textarea>
            </div>
            <div class="form-group">
                <label for="image">Image</label>
                <input type="file" id="image" name="image" accept=".jpg,.png,.gif" required>
            </div>
            <div class="form-group">
                <label for="date">Date</label>
                <input type="date" id="date" name="date" required>
            </div>
            <div class="form-group">
                <label for="status">Status</label>
                <select id="status" name="status">
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
            <button type="submit">Add Article</button>
        </form>
    </div>
</body>

</html>