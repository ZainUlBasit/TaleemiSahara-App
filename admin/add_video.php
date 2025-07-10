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
    $video_url = trim($_POST['video_url'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $status = $_POST['status'] ?? 'active';

    if (!$title || !$video_url) {
        $error = 'Please fill in all required fields.';
    } else {
        // Validate and process video URL
        $embed_url = processVideoUrl($video_url);
        if (!$embed_url) {
            $error = 'Please provide a valid YouTube, Vimeo, or other supported video URL.';
        } else {
            $stmt = $conn->prepare("INSERT INTO videos (title, description, video_url, embed_url, category, status) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssss", $title, $description, $video_url, $embed_url, $category, $status);
            if ($stmt->execute()) {
                $success = 'Video added successfully!';
                // Clear form data after successful submission
                $_POST = array();
            } else {
                $error = 'Failed to add video: ' . $stmt->error;
            }
            $stmt->close();
        }
    }
}

// Function to process video URLs and convert to embed URLs
function processVideoUrl($url)
{
    // YouTube
    if (preg_match('/youtube\.com\/watch\?v=([a-zA-Z0-9_-]+)/', $url, $matches)) {
        return 'https://www.youtube.com/embed/' . $matches[1];
    }
    if (preg_match('/youtu\.be\/([a-zA-Z0-9_-]+)/', $url, $matches)) {
        return 'https://www.youtube.com/embed/' . $matches[1];
    }

    // Vimeo
    if (preg_match('/vimeo\.com\/([0-9]+)/', $url, $matches)) {
        return 'https://player.vimeo.com/video/' . $matches[1];
    }

    // Dailymotion
    if (preg_match('/dailymotion\.com\/video\/([a-zA-Z0-9]+)/', $url, $matches)) {
        return 'https://www.dailymotion.com/embed/video/' . $matches[1];
    }

    // If it's already an embed URL, return as is
    if (strpos($url, 'embed') !== false) {
        return $url;
    }

    return false;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/font.css">
    <link rel="stylesheet" href="../css/general.css">
    <title>Add Video</title>
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
            max-width: 800px;
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
            text-align: center;
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
        input[type="url"],
        select,
        textarea {
            width: 100%;
            padding: 12px;
            box-sizing: border-box;
            font-size: 1rem;
            border: 2px solid #ddd;
            border-radius: 6px;
            transition: border-color 0.3s;
        }

        input[type="text"]:focus,
        input[type="url"]:focus,
        select:focus,
        textarea:focus {
            outline: none;
            border-color: #465462;
        }

        textarea {
            resize: vertical;
            min-height: 100px;
        }

        .url-help {
            font-size: 0.9em;
            color: #666;
            margin-top: 5px;
        }

        .supported-platforms {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 6px;
            padding: 15px;
            margin-bottom: 20px;
        }

        .supported-platforms h3 {
            margin: 0 0 10px 0;
            color: #333;
            font-size: 1.1em;
        }

        .platform-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .platform-list li {
            padding: 5px 0;
            color: #666;
        }

        .platform-list li:before {
            content: "✓ ";
            color: #28a745;
            font-weight: bold;
        }

        button {
            padding: 12px 24px;
            background-color: #465462;
            color: white;
            border: none;
            cursor: pointer;
            font-weight: 600;
            border-radius: 6px;
            transition: background-color 0.3s;
            font-size: 1rem;
        }

        button:hover {
            background-color: #2c3e50;
        }

        .error {
            color: #e74c3c;
            background: #fdf2f2;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            border: 1px solid #f5c6cb;
        }

        .success {
            color: #27ae60;
            background: #f0f9f0;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            border: 1px solid #c3e6cb;
        }

        .required {
            color: #e74c3c;
        }

        .preview-section {
            margin-top: 20px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 6px;
            border: 1px solid #e9ecef;
        }

        .preview-section h3 {
            margin: 0 0 15px 0;
            color: #333;
        }

        .video-preview {
            position: relative;
            width: 100%;
            height: 0;
            padding-bottom: 56.25%;
            /* 16:9 aspect ratio */
            background: #000;
            border-radius: 6px;
            overflow: hidden;
        }

        .video-preview iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }

        .no-preview {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: #666;
            text-align: center;
        }
    </style>
</head>

<body>
    <?php include '../components/admin-navbar.php'; ?>
    <div class="main animate-fade-in">
        <h1>Add New Video</h1>

        <?php if ($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="success"><?php echo $success; ?></div>
        <?php endif; ?>

        <div class="supported-platforms">
            <h3>Supported Video Platforms</h3>
            <ul class="platform-list">
                <li>YouTube (youtube.com, youtu.be)</li>
                <li>Vimeo (vimeo.com)</li>
                <li>Dailymotion (dailymotion.com)</li>
                <li>Direct embed URLs</li>
            </ul>
        </div>

        <form method="POST" action="" id="videoForm">
            <div class="form-group">
                <label for="title">Video Title <span class="required">*</span>:</label>
                <input type="text" id="title" name="title" required
                    value="<?php echo isset($_POST['title']) ? htmlspecialchars($_POST['title']) : ''; ?>"
                    placeholder="Enter video title">
            </div>

            <div class="form-group">
                <label for="description">Description:</label>
                <textarea id="description" name="description"
                    placeholder="Enter video description (optional)"><?php echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; ?></textarea>
            </div>

            <div class="form-group">
                <label for="video_url">Video URL <span class="required">*</span>:</label>
                <input type="url" id="video_url" name="video_url" required
                    value="<?php echo isset($_POST['video_url']) ? htmlspecialchars($_POST['video_url']) : ''; ?>"
                    placeholder="https://www.youtube.com/watch?v=... or https://vimeo.com/...">
                <div class="url-help">Paste the video URL from YouTube, Vimeo, or other supported platforms</div>
            </div>

            <div class="form-group">
                <label for="category">Category:</label>
                <select id="category" name="category">
                    <option value="">Select Category</option>
                    <option value="educational" <?php if (isset($_POST['category']) && $_POST['category'] == 'educational') echo 'selected'; ?>>Educational</option>
                    <option value="tutorial" <?php if (isset($_POST['category']) && $_POST['category'] == 'tutorial') echo 'selected'; ?>>Tutorial</option>
                    <option value="promotional" <?php if (isset($_POST['category']) && $_POST['category'] == 'promotional') echo 'selected'; ?>>Promotional</option>
                    <option value="news" <?php if (isset($_POST['category']) && $_POST['category'] == 'news') echo 'selected'; ?>>News</option>
                    <option value="other" <?php if (isset($_POST['category']) && $_POST['category'] == 'other') echo 'selected'; ?>>Other</option>
                </select>
            </div>

            <div class="form-group">
                <label for="status">Status:</label>
                <select id="status" name="status">
                    <option value="active" <?php if (isset($_POST['status']) && $_POST['status'] == 'active') echo 'selected'; ?>>Active</option>
                    <option value="inactive" <?php if (isset($_POST['status']) && $_POST['status'] == 'inactive') echo 'selected'; ?>>Inactive</option>
                </select>
            </div>

            <div class="preview-section" id="previewSection" style="display: none;">
                <h3>Video Preview</h3>
                <div class="video-preview" id="videoPreview">
                    <div class="no-preview">Enter a valid video URL to see preview</div>
                </div>
            </div>

            <button type="submit">Add Video</button>
        </form>
    </div>

    <script>
        document.getElementById('video_url').addEventListener('input', function() {
            const url = this.value.trim();
            const previewSection = document.getElementById('previewSection');
            const videoPreview = document.getElementById('videoPreview');

            if (url) {
                // Process URL to get embed URL
                let embedUrl = '';

                // YouTube
                if (url.includes('youtube.com/watch?v=')) {
                    const videoId = url.split('v=')[1].split('&')[0];
                    embedUrl = `https://www.youtube.com/embed/${videoId}`;
                } else if (url.includes('youtu.be/')) {
                    const videoId = url.split('youtu.be/')[1].split('?')[0];
                    embedUrl = `https://www.youtube.com/embed/${videoId}`;
                }
                // Vimeo
                else if (url.includes('vimeo.com/')) {
                    const videoId = url.split('vimeo.com/')[1].split('/')[0];
                    embedUrl = `https://player.vimeo.com/video/${videoId}`;
                }
                // Dailymotion
                else if (url.includes('dailymotion.com/video/')) {
                    const videoId = url.split('dailymotion.com/video/')[1].split('/')[0];
                    embedUrl = `https://www.dailymotion.com/embed/video/${videoId}`;
                }
                // Direct embed URL
                else if (url.includes('embed')) {
                    embedUrl = url;
                }

                if (embedUrl) {
                    videoPreview.innerHTML = `<iframe src="${embedUrl}" frameborder="0" allowfullscreen></iframe>`;
                    previewSection.style.display = 'block';
                } else {
                    videoPreview.innerHTML = '<div class="no-preview">Invalid video URL</div>';
                    previewSection.style.display = 'block';
                }
            } else {
                previewSection.style.display = 'none';
            }
        });
    </script>
</body>

</html>