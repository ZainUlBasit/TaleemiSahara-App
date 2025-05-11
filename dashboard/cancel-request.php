<?php
session_start();
require_once '../config/database.php';

// Check if user is logged in and is a student
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'student') {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['request_id'])) {
    $student_id = $_SESSION['user_id'];
    $request_id = $_POST['request_id'];
    
    // Verify the request belongs to the student
    $stmt = $conn->prepare("SELECT mentor_id FROM connection_requests 
                           WHERE id = ? AND student_id = ? 
                           AND status = 'pending'");
    $stmt->bind_param("ii", $request_id, $student_id);
    $stmt->execute();
    $request = $stmt->get_result()->fetch_assoc();
    
    if ($request) {
        // Delete the connection request
        $stmt = $conn->prepare("DELETE FROM connection_requests 
                              WHERE id = ? AND student_id = ?");
        $stmt->bind_param("ii", $request_id, $student_id);
        
        if ($stmt->execute()) {
            // Delete related notification
            $stmt = $conn->prepare("DELETE FROM notifications 
                                  WHERE user_id = ? 
                                  AND type = 'connection_request' 
                                  AND related_id = ?");
            $stmt->bind_param("ii", $request['mentor_id'], $student_id);
            $stmt->execute();
            
            $_SESSION['success'] = "Connection request canceled successfully!";
        } else {
            $_SESSION['error'] = "Failed to cancel connection request. Please try again.";
        }
    } else {
        $_SESSION['error'] = "Invalid request or permission denied.";
    }
}

// Redirect back to mentors page
header("Location: student-mentors.php");
exit(); 