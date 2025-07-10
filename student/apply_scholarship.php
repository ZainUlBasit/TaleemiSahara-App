<?php
session_start();
require_once '../config/database.php';

// Check if user is logged in and is a student
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'student') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

// Check if scholarship_id is provided
if (!isset($_POST['scholarship_id'])) {
    echo json_encode(['success' => false, 'message' => 'Scholarship ID is required']);
    exit();
}

$scholarship_id = $_POST['scholarship_id'];
$student_id = $_SESSION['user_id'];

// Check if scholarship exists and is active
$stmt = $conn->prepare("SELECT * FROM scholarships WHERE id = ? AND status = 'active'");
$stmt->bind_param("i", $scholarship_id);
$stmt->execute();
$scholarship = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$scholarship) {
    echo json_encode(['success' => false, 'message' => 'Scholarship not found or not active']);
    exit();
}

// Check if student has already applied
$stmt = $conn->prepare("SELECT id FROM scholarship_applications WHERE student_id = ? AND scholarship_id = ?");
$stmt->bind_param("ii", $student_id, $scholarship_id);
$stmt->execute();
if ($stmt->get_result()->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'You have already applied for this scholarship']);
    exit();
}
$stmt->close();

// Check if there are available slots
if ($scholarship['available_slots'] <= 0) {
    echo json_encode(['success' => false, 'message' => 'No slots available for this scholarship']);
    exit();
}

// Insert application
$stmt = $conn->prepare("INSERT INTO scholarship_applications (student_id, scholarship_id, status, application_date) VALUES (?, ?, 'pending', NOW())");
$stmt->bind_param("ii", $student_id, $scholarship_id);

if ($stmt->execute()) {
    // Update available slots
    $new_slots = $scholarship['available_slots'] - 1;
    $update_stmt = $conn->prepare("UPDATE scholarships SET available_slots = ? WHERE id = ?");
    $update_stmt->bind_param("ii", $new_slots, $scholarship_id);
    $update_stmt->execute();
    $update_stmt->close();

    echo json_encode(['success' => true, 'message' => 'Application submitted successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to submit application']);
}

$stmt->close();
