<?php
require_once 'session_start.php';
require_once 'db_connect.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['status' => 'error', 'message' => 'Not authorized']);
    exit();
}

$sql = "SELECT COUNT(*) as count FROM leaves WHERE status = 'Pending'";
$result = $conn->query($sql);

if ($result) {
    $row = $result->fetch_assoc();
    echo json_encode(['status' => 'success', 'count' => (int)$row['count']]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Database error']);
}
?>
