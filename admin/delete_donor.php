<?php
session_start();
require_once '../config/database.php';
header('Content-Type: application/json');
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['donor_id']) || !is_numeric($_POST['donor_id'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit();
}
$donor_id = intval($_POST['donor_id']);
$conn->begin_transaction();
try {
    $stmt1 = $conn->prepare('DELETE FROM donor_profiles WHERE user_id = ?');
    $stmt1->bind_param('i', $donor_id);
    $stmt1->execute();
    $stmt1->close();
    $stmt2 = $conn->prepare('DELETE FROM users WHERE id = ? AND user_type = "donor"');
    $stmt2->bind_param('i', $donor_id);
    $stmt2->execute();
    $stmt2->close();
    $conn->commit();
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => 'Failed to delete donor.']);
}
