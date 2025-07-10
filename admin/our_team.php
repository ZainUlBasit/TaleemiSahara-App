<?php
session_start();
require_once '../config/database.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Handle delete request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_member'])) {
    $member_id = intval($_POST['delete_member']);
    try {
        // Fetch image path before deleting
        $stmt = $conn->prepare("SELECT image FROM team_members WHERE id = ?");
        $stmt->bind_param("i", $member_id);
        $stmt->execute();
        $stmt->bind_result($image_path);
        $stmt->fetch();
        $stmt->close();

        // Delete member
        $stmt = $conn->prepare("DELETE FROM team_members WHERE id = ?");
        $stmt->bind_param("i", $member_id);
        if ($stmt->execute()) {
            // Delete image file if it exists
            if (!empty($image_path)) {
                $image_server_path = '.' . substr($image_path, 1);
                if (file_exists($image_server_path)) {
                    unlink($image_server_path);
                }
            }
            $_SESSION['success_message'] = "Team member deleted successfully.";
        } else {
            $_SESSION['error_message'] = "Error deleting team member: " . $stmt->error;
        }
        $stmt->close();
    } catch (mysqli_sql_exception $e) {
        $_SESSION['error_message'] = "Database error: " . $e->getMessage();
    }
    header("Location: our_team.php");
    exit();
}

// Get flash messages
$success_message = $_SESSION['success_message'] ?? null;
$error_message = $_SESSION['error_message'] ?? null;
unset($_SESSION['success_message'], $_SESSION['error_message']);

// Fetch all team members
try {
    $result = $conn->query("SELECT * FROM team_members ORDER BY created_at DESC");
    $team_members = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
} catch (mysqli_sql_exception $e) {
    $team_members = [];
    $error_message = "The 'team_members' table might not exist. Please run the setup script. Error: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Team</title>
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
        }

        .add-btn:hover {
            background: #218838;
            text-decoration: none;
            color: white;
        }

        .members-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
        }

        .member-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            text-align: center;
        }

        .member-card img {
            width: 100%;
            height: 250px;
            object-fit: cover;
        }

        .member-info {
            padding: 15px;
        }

        .member-name {
            font-size: 1.2em;
            font-weight: 600;
            margin: 0;
        }

        .member-role {
            color: #666;
            margin: 5px 0 15px;
        }

        .actions {
            display: flex;
            justify-content: center;
            gap: 10px;
        }

        .action-btn {
            padding: 5px 15px;
            border-radius: 4px;
            text-decoration: none;
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
            <h1>Manage Our Team</h1>
            <a href="add_team_member.php" class="add-btn">Add New Member</a>
        </div>

        <?php if ($success_message): ?><div class="message success"><?php echo htmlspecialchars($success_message); ?></div><?php endif; ?>
        <?php if ($error_message): ?><div class="message error"><?php echo htmlspecialchars($error_message); ?></div><?php endif; ?>

        <div class="members-grid">
            <?php if (empty($team_members) && !$error_message): ?>
                <p>No team members found. <a href="add_team_member.php">Add the first one!</a></p>
            <?php else: ?>
                <?php foreach ($team_members as $member): ?>
                    <div class="member-card">
                        <img src="<?php echo htmlspecialchars($member['image']); ?>" alt="<?php echo htmlspecialchars($member['name']); ?>">
                        <div class="member-info">
                            <h3 class="member-name"><?php echo htmlspecialchars($member['name']); ?></h3>
                            <p class="member-role"><?php echo htmlspecialchars($member['role']); ?></p>
                            <div class="actions">
                                <a href="edit_team_member.php?id=<?php echo $member['id']; ?>" class="action-btn edit-btn">Edit</a>
                                <form method="POST" onsubmit="return confirm('Are you sure you want to delete this team member?');" style="display:inline;">
                                    <input type="hidden" name="delete_member" value="<?php echo $member['id']; ?>">
                                    <button type="submit" class="action-btn delete-btn">Delete</button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>