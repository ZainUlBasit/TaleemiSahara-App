<?php
session_start();
require_once '../config/database.php';

// Check if user is logged in and is a student
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'student') {
    header("Location: ../login.php");
    exit();
}

// Get student details
$student_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ? AND user_type = 'student'");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();

// Get student's mentors for the sidebar
$stmt = $conn->prepare("SELECT u.*, mp.expertise, 
                              (SELECT COUNT(*) FROM messages 
                               WHERE sender_id = u.id 
                               AND receiver_id = ? 
                               AND read_status = 0) as unread_count 
                       FROM mentor_connections mc 
                       JOIN users u ON mc.mentor_id = u.id 
                       JOIN mentor_profiles mp ON u.id = mp.user_id 
                       WHERE mc.student_id = ? 
                       AND mc.status = 'active'");
$stmt->bind_param("ii", $student_id, $student_id);
$stmt->execute();
$mentors = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Get selected mentor's messages if mentor_id is provided
$selected_mentor = null;
$messages = [];
if (isset($_GET['mentor_id'])) {
    $mentor_id = $_GET['mentor_id'];
    
    // Get mentor details
    $stmt = $conn->prepare("SELECT u.*, mp.expertise 
                           FROM users u 
                           JOIN mentor_profiles mp ON u.id = mp.user_id 
                           WHERE u.id = ?");
    $stmt->bind_param("i", $mentor_id);
    $stmt->execute();
    $selected_mentor = $stmt->get_result()->fetch_assoc();
    
    // Get messages
    if ($selected_mentor) {
        $stmt = $conn->prepare("SELECT m.*, 
                                      CASE 
                                          WHEN m.sender_id = ? THEN 'sent' 
                                          ELSE 'received' 
                                      END as message_type 
                               FROM messages m 
                               WHERE (sender_id = ? AND receiver_id = ?) 
                               OR (sender_id = ? AND receiver_id = ?) 
                               ORDER BY sent_date ASC");
        $stmt->bind_param("iiiii", $student_id, $student_id, $mentor_id, $mentor_id, $student_id);
        $stmt->execute();
        $messages = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        
        // Mark messages as read
        $stmt = $conn->prepare("UPDATE messages 
                              SET read_status = 1 
                              WHERE sender_id = ? 
                              AND receiver_id = ? 
                              AND read_status = 0");
        $stmt->bind_param("ii", $mentor_id, $student_id);
        $stmt->execute();
    }
}

// Get total unread messages count
$stmt = $conn->prepare("SELECT COUNT(*) as unread FROM messages WHERE receiver_id = ? AND read_status = 0");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$unread_messages = $stmt->get_result()->fetch_assoc()['unread'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages - EduConnect</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .messages-container {
            max-width: 1200px;
            margin: 80px auto 2rem;
            padding: 0 2rem;
            display: grid;
            grid-template-columns: 300px 1fr;
            gap: 2rem;
            min-height: calc(100vh - 180px);
        }

        .mentors-sidebar {
            background-color: var(--white);
            border-radius: 10px;
            padding: 1rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            height: fit-content;
        }

        .mentor-item {
            display: flex;
            align-items: center;
            padding: 1rem;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            text-decoration: none;
            color: inherit;
        }

        .mentor-item:hover {
            background-color: var(--light-gray);
        }

        .mentor-item.active {
            background-color: var(--primary-color);
            color: var(--white);
        }

        .mentor-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 1rem;
            background-color: var(--secondary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--white);
            font-size: 1.2rem;
        }

        .mentor-info h3 {
            margin: 0;
            font-size: 1rem;
        }

        .mentor-expertise {
            font-size: 0.8rem;
            color: var(--dark-gray);
        }

        .mentor-item.active .mentor-expertise {
            color: var(--light-gray);
        }

        .unread-badge {
            background-color: var(--secondary-color);
            color: var(--white);
            border-radius: 50%;
            padding: 0.25rem 0.5rem;
            font-size: 0.8rem;
            margin-left: auto;
        }

        .messages-content {
            background-color: var(--white);
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
        }

        .messages-header {
            padding: 1rem;
            border-bottom: 1px solid var(--light-gray);
            display: flex;
            align-items: center;
        }

        .messages-list {
            flex-grow: 1;
            padding: 1rem;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .message {
            max-width: 70%;
            padding: 1rem;
            border-radius: 10px;
            position: relative;
        }

        .message.sent {
            background-color: var(--primary-color);
            color: var(--white);
            align-self: flex-end;
        }

        .message.received {
            background-color: var(--light-gray);
            align-self: flex-start;
        }

        .message-time {
            font-size: 0.8rem;
            margin-top: 0.5rem;
            opacity: 0.8;
        }

        .message-form {
            padding: 1rem;
            border-top: 1px solid var(--light-gray);
            display: flex;
            gap: 1rem;
        }

        .message-input {
            flex-grow: 1;
            padding: 0.5rem;
            border: 1px solid var(--light-gray);
            border-radius: 5px;
            resize: none;
        }

        .send-btn {
            background-color: var(--secondary-color);
            color: var(--white);
            border: none;
            border-radius: 5px;
            padding: 0.5rem 1rem;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .send-btn:hover {
            background-color: var(--primary-color);
        }

        .no-messages {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100%;
            color: var(--dark-gray);
            text-align: center;
            padding: 2rem;
        }

        .no-messages i {
            font-size: 3rem;
            margin-bottom: 1rem;
            color: var(--light-gray);
        }
    </style>
</head>
<body>
    <header>
        <nav class="navbar">
            <div class="logo">
                <h1><a href="../index.html" style="text-decoration: none; color: inherit;">EduConnect</a></h1>
            </div>
            <ul class="nav-links">
                <li><a href="student.php">Dashboard</a></li>
                <li><a href="student-profile.php">Profile</a></li>
                <li><a href="student-scholarships.php">Scholarships</a></li>
                <li><a href="student-mentors.php">Mentors</a></li>
                <li><a href="student-messages.php" class="active notification-badge" data-count="<?php echo $unread_messages; ?>">Messages</a></li>
                <li><a href="../logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>

    <div class="messages-container">
        <div class="mentors-sidebar">
            <h2>Mentors</h2>
            <?php if (empty($mentors)): ?>
                <p>No mentors connected yet.</p>
            <?php else: ?>
                <?php foreach ($mentors as $mentor): ?>
                    <a href="?mentor_id=<?php echo $mentor['id']; ?>" 
                       class="mentor-item <?php echo (isset($_GET['mentor_id']) && $_GET['mentor_id'] == $mentor['id']) ? 'active' : ''; ?>">
                        <div class="mentor-avatar">
                            <?php echo strtoupper(substr($mentor['name'], 0, 1)); ?>
                        </div>
                        <div class="mentor-info">
                            <h3><?php echo htmlspecialchars($mentor['name']); ?></h3>
                            <div class="mentor-expertise"><?php echo htmlspecialchars($mentor['expertise']); ?></div>
                        </div>
                        <?php if ($mentor['unread_count'] > 0): ?>
                            <span class="unread-badge"><?php echo $mentor['unread_count']; ?></span>
                        <?php endif; ?>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div class="messages-content">
            <?php if ($selected_mentor): ?>
                <div class="messages-header">
                    <div class="mentor-avatar">
                        <?php echo strtoupper(substr($selected_mentor['name'], 0, 1)); ?>
                    </div>
                    <div class="mentor-info">
                        <h3><?php echo htmlspecialchars($selected_mentor['name']); ?></h3>
                        <div class="mentor-expertise"><?php echo htmlspecialchars($selected_mentor['expertise']); ?></div>
                    </div>
                </div>
                <div class="messages-list">
                    <?php foreach ($messages as $message): ?>
                        <div class="message <?php echo $message['message_type']; ?>">
                            <?php echo htmlspecialchars($message['content']); ?>
                            <div class="message-time">
                                <?php echo date('M d, Y h:i A', strtotime($message['sent_date'])); ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <form class="message-form" action="send-message.php" method="post">
                    <input type="hidden" name="receiver_id" value="<?php echo $selected_mentor['id']; ?>">
                    <textarea name="content" class="message-input" placeholder="Type your message..." required></textarea>
                    <button type="submit" class="send-btn">Send</button>
                </form>
            <?php else: ?>
                <div class="no-messages">
                    <i class="fas fa-comments"></i>
                    <h2>Select a mentor to start messaging</h2>
                    <p>Choose a mentor from the sidebar to view your conversation history and send messages.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="../js/main.js"></script>
</body>
</html> 