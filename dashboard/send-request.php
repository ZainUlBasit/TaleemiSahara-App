<?php
session_start();
require_once '../config/database.php';

// Check if user is logged in and is a student
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'student') {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mentor_id'])) {
    $student_id = $_SESSION['user_id'];
    $mentor_id = $_POST['mentor_id'];
    
    // Check if a connection or request already exists
    $stmt = $conn->prepare("SELECT * FROM mentor_connections 
                           WHERE student_id = ? AND mentor_id = ?");
    $stmt->bind_param("ii", $student_id, $mentor_id);
    $stmt->execute();
    $existing_connection = $stmt->get_result()->fetch_assoc();
    
    $stmt = $conn->prepare("SELECT * FROM connection_requests 
                           WHERE student_id = ? AND mentor_id = ? 
                           AND status = 'pending'");
    $stmt->bind_param("ii", $student_id, $mentor_id);
    $stmt->execute();
    $existing_request = $stmt->get_result()->fetch_assoc();
    
    if ($existing_connection || $existing_request) {
        $_SESSION['error'] = "A connection or request already exists with this mentor.";
    } else {
        // Create new connection request
        $stmt = $conn->prepare("INSERT INTO connection_requests 
                              (student_id, mentor_id, status, request_date) 
                              VALUES (?, ?, 'pending', NOW())");
        $stmt->bind_param("ii", $student_id, $mentor_id);
        
        if ($stmt->execute()) {
            // Create notification for mentor
            $stmt = $conn->prepare("INSERT INTO notifications 
                                  (user_id, type, content, related_id, created_at) 
                                  VALUES (?, 'connection_request', 'You have a new connection request from a student.', ?, NOW())");
            $stmt->bind_param("ii", $mentor_id, $student_id);
            $stmt->execute();
            
            $_SESSION['success'] = "Connection request sent successfully!";
        } else {
            $_SESSION['error'] = "Failed to send connection request. Please try again.";
        }
    }
}

// Redirect back to mentors page
header("Location: student-mentors.php");
exit(); 