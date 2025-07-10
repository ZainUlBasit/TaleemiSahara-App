<?php
session_start();
require_once '../config/database.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

// Handle video deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_video'])) {
    $video_id = intval($_POST['delete_video']);
    $stmt = $conn->prepare("DELETE FROM videos WHERE id = ?");
    $stmt->bind_param("i", $video_id);
    if ($stmt->execute()) {
        $success_message = "Video deleted successfully!";
    } else {
        $error_message = "Failed to delete video: " . $stmt->error;
    }
    $stmt->close();

    // Redirect to avoid form resubmission
    header("Location: manage_videos.php");
    exit();
}

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['video_id'], $_POST['status'])) {
    $video_id = intval($_POST['video_id']);
    $status = $_POST['status'];
    $allowed_statuses = ['active', 'inactive'];
    if (in_array($status, $allowed_statuses)) {
        $stmt = $conn->prepare("UPDATE videos SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $video_id);
        if ($stmt->execute()) {
            $success_message = "Video status updated successfully!";
        } else {
            $error_message = "Failed to update video status: " . $stmt->error;
        }
        $stmt->close();
    }

    // Redirect to avoid form resubmission
    header("Location: manage_videos.php");
    exit();
}

// Fetch all videos
$sql = "SELECT * FROM videos ORDER BY created_at DESC";
try {
    $result = $conn->query($sql);
    if ($result === false) {
        throw new Exception("Database query failed: " . $conn->error);
    }
    $videos = $result->fetch_all(MYSQLI_ASSOC);
} catch (Exception $e) {
    $videos = [];
    $error_message = $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/font.css">
    <link rel="stylesheet" href="../css/general.css">
    <title>Manage Videos</title>
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
            max-width: 1400px;
            width: 100%;
            padding: 20px;
            margin-top: 100px;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .page-header h1 {
            color: #333;
            margin: 0;
        }

        .add-video-btn {
            background: #28a745;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 500;
            transition: background-color 0.3s;
        }

        .add-video-btn:hover {
            background: #218838;
            color: white;
            text-decoration: none;
        }

        .videos-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
            gap: 25px;
        }

        .video-card {
            background: white;
            border: 1px solid #ddd;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .video-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }

        .video-embed {
            position: relative;
            width: 100%;
            height: 0;
            padding-bottom: 56.25%;
            /* 16:9 aspect ratio */
            background: #000;
        }

        .video-embed iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }

        .video-info {
            padding: 20px;
        }

        .video-title {
            font-size: 1.3em;
            color: #333;
            margin: 0 0 10px 0;
            font-weight: 600;
        }

        .video-description {
            color: #666;
            margin-bottom: 15px;
            line-height: 1.5;
        }

        .video-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            font-size: 0.9em;
        }

        .video-category {
            background: #e3f2fd;
            color: #1976d2;
            padding: 4px 8px;
            border-radius: 12px;
            font-weight: 500;
        }

        .video-date {
            color: #666;
        }

        .video-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .action-btn {
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.8em;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .status-btn {
            background: #007bff;
            color: white;
        }

        .status-btn:hover {
            background: #0056b3;
        }

        .status-btn.inactive {
            background: #6c757d;
        }

        .status-btn.inactive:hover {
            background: #545b62;
        }

        .delete-btn {
            background: #dc3545;
            color: white;
        }

        .delete-btn:hover {
            background: #c82333;
        }

        .edit-btn {
            background: #ffc107;
            color: #212529;
        }

        .edit-btn:hover {
            background: #e0a800;
        }

        .no-videos {
            text-align: center;
            padding: 60px 20px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .no-videos h2 {
            color: #666;
            margin-bottom: 15px;
        }

        .no-videos p {
            color: #888;
            margin-bottom: 25px;
        }

        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid #f5c6cb;
        }

        .success-message {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid #c3e6cb;
        }

        .stats-section {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .stat-number {
            font-size: 2em;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .stat-label {
            color: #666;
            font-size: 0.9em;
        }

        .stat-total .stat-number {
            color: #007bff;
        }

        .stat-active .stat-number {
            color: #28a745;
        }

        .stat-inactive .stat-number {
            color: #6c757d;
        }
    </style>
</head>

<body>
    <?php include '../components/admin-navbar.php'; ?>

    <div class="main">
        <div class="page-header">
            <h1>Manage Videos</h1>
            <a href="add_video.php" class="add-video-btn">Add New Video</a>
        </div>

        <?php if (isset($error_message)): ?>
            <div class="error-message">
                <strong>Error:</strong> <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($success_message)): ?>
            <div class="success-message">
                <strong>Success:</strong> <?php echo htmlspecialchars($success_message); ?>
            </div>
        <?php endif; ?>

        <?php if (empty($videos)): ?>
            <div class="no-videos">
                <h2>No Videos Yet</h2>
                <p>You haven't added any videos yet. Start by adding your first video!</p>
                <a href="add_video.php" class="add-video-btn">Add First Video</a>
            </div>
        <?php else: ?>
            <!-- Statistics Section -->
            <div class="stats-section">
                <?php
                $total_videos = count($videos);
                $active_videos = 0;
                $inactive_videos = 0;

                foreach ($videos as $video) {
                    if ($video['status'] === 'active') {
                        $active_videos++;
                    } else {
                        $inactive_videos++;
                    }
                }
                ?>
                <div class="stat-card stat-total">
                    <div class="stat-number"><?php echo $total_videos; ?></div>
                    <div class="stat-label">Total Videos</div>
                </div>
                <div class="stat-card stat-active">
                    <div class="stat-number"><?php echo $active_videos; ?></div>
                    <div class="stat-label">Active Videos</div>
                </div>
                <div class="stat-card stat-inactive">
                    <div class="stat-number"><?php echo $inactive_videos; ?></div>
                    <div class="stat-label">Inactive Videos</div>
                </div>
            </div>

            <!-- Videos Grid -->
            <div class="videos-grid">
                <?php foreach ($videos as $video): ?>
                    <div class="video-card">
                        <div class="video-embed">
                            <iframe src="<?php echo htmlspecialchars($video['embed_url']); ?>"
                                frameborder="0"
                                allowfullscreen>
                            </iframe>
                        </div>
                        <div class="video-info">
                            <h3 class="video-title"><?php echo htmlspecialchars($video['title']); ?></h3>
                            <?php if (!empty($video['description'])): ?>
                                <p class="video-description"><?php echo htmlspecialchars($video['description']); ?></p>
                            <?php endif; ?>

                            <div class="video-meta">
                                <?php if (!empty($video['category'])): ?>
                                    <span class="video-category"><?php echo ucfirst(htmlspecialchars($video['category'])); ?></span>
                                <?php endif; ?>
                                <span class="video-date"><?php echo date('M j, Y', strtotime($video['created_at'])); ?></span>
                            </div>

                            <div class="video-actions">
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="video_id" value="<?php echo $video['id']; ?>">
                                    <input type="hidden" name="status" value="<?php echo $video['status'] === 'active' ? 'inactive' : 'active'; ?>">
                                    <button type="submit" class="action-btn status-btn <?php echo $video['status'] === 'inactive' ? 'inactive' : ''; ?>">
                                        <?php echo $video['status'] === 'active' ? 'Deactivate' : 'Activate'; ?>
                                    </button>
                                </form>

                                <a href="edit_video.php?id=<?php echo $video['id']; ?>" class="action-btn edit-btn">
                                    Edit
                                </a>

                                <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this video?');">
                                    <input type="hidden" name="delete_video" value="<?php echo $video['id']; ?>">
                                    <button type="submit" class="action-btn delete-btn">Delete</button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <script>
        // No longer needed as we are using an anchor tag now
        // function editVideo(videoId) {
        //     alert('Edit functionality will be implemented in the next version. Video ID: ' + videoId);
        // }
    </script>
</body>

</html>