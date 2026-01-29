<?php
require_once 'session_start.php';
require_once 'db_connect.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not authenticated']);
    exit();
}

$user_id = $_SESSION['user_id'];
$sql = "SELECT id, message, is_read, created_at FROM notifications WHERE user_id = ? AND is_read = 0 ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$notifications = [];
while ($row = $result->fetch_assoc()) {
    $notifications[] = $row;
}

echo json_encode(['status' => 'success', 'data' => $notifications]);
?>
