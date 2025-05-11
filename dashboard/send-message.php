<?php
session_start();
require_once '../config/database.php';

// Check if user is logged in and is a student
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'student') {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['receiver_id']) && isset($_POST['content'])) {
    $sender_id = $_SESSION['user_id'];
    $receiver_id = $_POST['receiver_id'];
    $content = trim($_POST['content']);
    
    if (empty($content)) {
        $_SESSION['error'] = "Message content cannot be empty.";
        header("Location: student-messages.php?mentor_id=" . $receiver_id);
        exit();
    }
    
    // Verify there is an active connection with the mentor
    $stmt = $conn->prepare("SELECT * FROM mentor_connections 
                           WHERE student_id = ? AND mentor_id = ? 
                           AND status = 'active'");
    $stmt->bind_param("ii", $sender_id, $receiver_id);
    $stmt->execute();
    $connection = $stmt->get_result()->fetch_assoc();
    
    if ($connection) {
        // Send the message
        $stmt = $conn->prepare("INSERT INTO messages 
                              (sender_id, receiver_id, content, sent_date, read_status) 
                              VALUES (?, ?, ?, NOW(), 0)");
        $stmt->bind_param("iis", $sender_id, $receiver_id, $content);
        
        if ($stmt->execute()) {
            // Create notification for mentor
            $stmt = $conn->prepare("INSERT INTO notifications 
                                  (user_id, type, content, related_id, created_at) 
                                  VALUES (?, 'new_message', 'You have a new message from your student.', ?, NOW())");
            $stmt->bind_param("ii", $receiver_id, $sender_id);
            $stmt->execute();
            
            $_SESSION['success'] = "Message sent successfully!";
        } else {
            $_SESSION['error'] = "Failed to send message. Please try again.";
        }
    } else {
        $_SESSION['error'] = "You can only send messages to your connected mentors.";
    }
    
    header("Location: student-messages.php?mentor_id=" . $receiver_id);
    exit();
}

// Redirect back to messages page if accessed directly
header("Location: student-messages.php");
exit();