<?php
session_start();
require_once '../config/database.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Handle POST requests for actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete_news'])) {
        $news_id = intval($_POST['delete_news']);

        // Fetch image path before deleting
        $stmt = $conn->prepare("SELECT image FROM news WHERE id = ?");
        $stmt->bind_param("i", $news_id);
        $stmt->execute();
        $stmt->bind_result($image_path);
        $stmt->fetch();
        $stmt->close();

        // Delete news article
        $stmt = $conn->prepare("DELETE FROM news WHERE id = ?");
        $stmt->bind_param("i", $news_id);
        if ($stmt->execute()) {
            // Delete image file if it exists
            if (!empty($image_path)) {
                $image_server_path = '.' . substr($image_path, 1);
                if (file_exists($image_server_path)) {
                    unlink($image_server_path);
                }
            }
            $_SESSION['success_message'] = "News article deleted successfully.";
        } else {
            $_SESSION['error_message'] = "Error deleting news article: " . $stmt->error;
        }
        $stmt->close();
    }
    header("Location: our_news.php");
    exit();
}

// Get flash messages
$success_message = $_SESSION['success_message'] ?? null;
$error_message = $_SESSION['error_message'] ?? null;
unset($_SESSION['success_message'], $_SESSION['error_message']);

// Fetch all news items
try {
    $result = $conn->query("SELECT * FROM news ORDER BY date DESC");
    $news_items = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
} catch (mysqli_sql_exception $e) {
    $news_items = [];
    $error_message = "The 'news' table does not exist. Please run the setup script. " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage News</title>
    <link rel="stylesheet" href="../css/font.css">
    <link rel="stylesheet" href="../css/general.css">
    <style>
        body {
            display: flex;
            flex-direction: column;
            font-family: 'Raleway', sans-serif;
            background-color: #f4f7f6;
            color: #333;
        }

        .main {
            margin: 100px auto 20px;
            max-width: 1200px;
            width: 100%;
            padding: 20px;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .page-header h1 {
            margin: 0;
        }

        .add-btn {
            background: #28a745;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 500;
            transition: background-color 0.3s;
        }

        .add-btn:hover {
            background: #218838;
            color: white;
            text-decoration: none;
        }

        .news-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .news-table th,
        .news-table td {
            padding: 12px 15px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }

        .news-table th {
            background-color: #f8f9fa;
            font-weight: 600;
        }

        .news-table tr:hover {
            background-color: #f1f1f1;
        }

        .actions {
            display: flex;
            gap: 10px;
        }

        .action-btn {
            padding: 5px 10px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 0.9em;
            color: white;
            border: none;
            cursor: pointer;
        }

        .edit-btn {
            background-color: #ffc107;
            color: #212529;
        }

        .delete-btn {
            background-color: #dc3545;
        }

        .message {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }

        .success {
            background-color: #d4edda;
            color: #155724;
        }

        .error {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
</head>

<body>
    <?php include '../components/admin-navbar.php'; ?>
    <div class="main">
        <div class="page-header">
            <h1>Manage News</h1>
            <a href="add_news.php" class="add-btn">Add New Article</a>
        </div>

        <?php if ($success_message): ?><div class="message success"><?php echo htmlspecialchars($success_message); ?></div><?php endif; ?>
        <?php if ($error_message): ?><div class="message error"><?php echo htmlspecialchars($error_message); ?></div><?php endif; ?>

        <?php if (empty($news_items) && !$error_message): ?>
            <p>No news articles found. <a href="add_news.php">Add the first one!</a></p>
        <?php elseif (!empty($news_items)): ?>
            <table class="news-table">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($news_items as $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['title']); ?></td>
                            <td><?php echo date('M j, Y', strtotime($item['date'])); ?></td>
                            <td><?php echo ucfirst($item['status']); ?></td>
                            <td class="actions">
                                <a href="edit_news.php?id=<?php echo $item['id']; ?>" class="action-btn edit-btn">Edit</a>
                                <form method="POST" onsubmit="return confirm('Are you sure you want to delete this article?');" style="display:inline;">
                                    <input type="hidden" name="delete_news" value="<?php echo $item['id']; ?>">
                                    <button type="submit" class="action-btn delete-btn">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>

</html>