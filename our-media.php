<?php
require_once 'config/database.php';

// Fetch all active videos from the database
$videos = [];
$error_message = '';
try {
    $sql = "SELECT * FROM videos WHERE status = 'active' ORDER BY created_at DESC";
    $result = $conn->query($sql);
    if ($result) {
        $videos = $result->fetch_all(MYSQLI_ASSOC);
    } else {
        // This will help in debugging if the query fails but doesn't throw an exception
        $error_message = 'Error fetching videos: ' . $conn->error;
    }
} catch (Exception $e) {
    $error_message = 'Database error: ' . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300..700&family=Raleway:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@400..700&display=swap" rel="stylesheet" />
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        body {
            margin: 0;
            font-family: 'Spartan', sans-serif;
        }

        .vision-section {
            position: relative;
            background-color: black;
            width: 100%;
            padding: 4rem 0;
            overflow: hidden;
        }

        .vision-bg {
            width: 100%;
            height: 22rem;
            object-fit: cover;
            opacity: 0.2;
        }

        @media (min-width: 640px) {
            .vision-bg {
                height: 20rem;
            }
        }

        @media (min-width: 768px) {
            .vision-bg {
                height: 24rem;
            }
        }

        @media (min-width: 1024px) {
            .vision-bg {
                height: 28rem;
            }
        }

        .vision-content {
            position: absolute;
            top: 0;
            bottom: 0;
            width: 100%;
            height: 100%;
            background: transparent;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: white;
            text-align: center;
        }

        .vision-title {
            font-size: 5rem !important;
            font-weight: bold;
            padding: 1rem;
        }

        @media (min-width: 480px) {
            .vision-title {
                font-size: 1.25rem;
            }
        }

        @media (min-width: 640px) {
            .vision-title {
                font-size: 1.5rem;
            }
        }

        @media (min-width: 768px) {
            .vision-title {
                font-size: 2.25rem;
            }
        }

        @media (min-width: 1024px) {
            .vision-title {
                font-size: 3rem;
            }
        }

        .vision-text {
            width: 80%;
            font-size: 0.75rem;
            font-weight: 300;
        }

        @media (min-width: 480px) {
            .vision-text {
                font-size: 0.875rem;
            }
        }

        @media (min-width: 640px) {
            .vision-text {
                font-size: 1rem;
            }
        }

        @media (min-width: 768px) {
            .vision-text {
                font-size: 1.1rem;
            }
        }

        @media (min-width: 1024px) {
            .vision-text {
                font-size: 1.2rem;
            }
        }


        .frame-container {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            place-items: start;
            gap: 50px;
            padding: 50px;
            max-width: 1400px;
            margin: 0px auto;
        }

        @media (max-width: 1250px) {
            .frame-container {
                grid-template-columns: repeat(1, 1fr);
                width: 100%;
                padding: 20px;
            }
        }

        .video-card {
            background: white;
            border: 1px solid #ddd;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            width: 100%;
        }

        .video-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
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
            font-size: 1.4em;
            color: #333;
            margin: 0 0 10px 0;
            font-weight: 600;
        }

        .video-description {
            color: #666;
            margin-bottom: 15px;
            line-height: 1.6;
            font-size: 0.95em;
        }

        .video-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.9em;
            border-top: 1px solid #f0f0f0;
            padding-top: 15px;
            margin-top: 15px;
        }

        .video-category {
            background: #e3f2fd;
            color: #1976d2;
            padding: 5px 12px;
            border-radius: 15px;
            font-weight: 500;
        }

        .video-date {
            color: #6c757d;
        }

        .no-videos-message {
            grid-column: 1 / -1;
            /* Span across all columns */
            text-align: center;
            padding: 50px;
            background: #f8f9fa;
            border-radius: 12px;
        }
    </style>
</head>

<body>
    <?php include 'components/navbar.php'; ?>
    <div class="vision-section">
        <img src="./images/About3.png" alt="Vision Background" class="vision-bg" />
        <div class="vision-content">
            <h1 class="vision-title">Taleemi Sahara Media Team</h1>
            <p class="vision-text">
                At Taleemi Sahara, our Media Team plays a vital role in spreading awareness about our mission to support underprivileged students through donations and scholarships. We are dedicated to highlighting the stories, impact, and ongoing efforts of our foundation through engaging visuals, inspiring campaigns, and authentic storytelling. Our goal is to connect hearts and build a strong community that believes in equal access to education for all. Through the power of media, we aim to inspire action, encourage donations, and give a voice to those who need it most.
            </p>

        </div>
    </div>

    <div class="frame-container">
        <?php if (!empty($videos)): ?>
            <?php foreach ($videos as $video): ?>
                <div class="video-card">
                    <div class="video-embed">
                        <iframe src="<?php echo htmlspecialchars($video['embed_url']); ?>"
                            title="<?php echo htmlspecialchars($video['title']); ?>"
                            frameborder="0"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                            referrerpolicy="strict-origin-when-cross-origin"
                            allowfullscreen>
                        </iframe>
                    </div>
                    <div class="video-info">
                        <h3 class="video-title"><?php echo htmlspecialchars($video['title']); ?></h3>
                        <?php if (!empty($video['description'])): ?>
                            <p class="video-description"><?php echo nl2br(htmlspecialchars($video['description'])); ?></p>
                        <?php endif; ?>
                        <div class="video-meta">
                            <?php if (!empty($video['category'])): ?>
                                <span class="video-category"><?php echo ucfirst(htmlspecialchars($video['category'])); ?></span>
                            <?php endif; ?>
                            <span class="video-date">
                                <?php echo date('F j, Y', strtotime($video['created_at'])); ?>
                            </span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php elseif ($error_message): ?>
            <div class="no-videos-message">
                <h2>Error Loading Videos</h2>
                <p><?php echo htmlspecialchars($error_message); ?></p>
            </div>
        <?php else: ?>
            <div class="no-videos-message">
                <h2>No Videos Available</h2>
                <p>There are no videos to display at the moment. Please check back later!</p>
            </div>
        <?php endif; ?>
    </div>
    <?php include 'components/footer.php'; ?>


</body>

</html>