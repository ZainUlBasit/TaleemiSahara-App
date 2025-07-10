<?php
session_start();
require_once '../config/database.php';
header('Content-Type: application/json');
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['student_id']) || !is_numeric($_POST['student_id'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit();
}
$student_id = intval($_POST['student_id']);
$conn->begin_transaction();
try {
    $stmt1 = $conn->prepare('DELETE FROM student_profiles WHERE user_id = ?');
    $stmt1->bind_param('i', $student_id);
    $stmt1->execute();
    $stmt1->close();
    $stmt2 = $conn->prepare('DELETE FROM users WHERE id = ? AND user_type = "student"');
    $stmt2->bind_param('i', $student_id);
    $stmt2->execute();
    $stmt2->close();
    $conn->commit();
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => 'Failed to delete student.']);
}
