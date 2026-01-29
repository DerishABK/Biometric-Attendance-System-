<?php
require_once 'session_start.php';
require_once 'db_connect.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['status' => 'error', 'message' => 'Not authorized']);
    exit();
}

$sql = "SELECT l.*, u.full_name, u.designation, alt.full_name as alt_staff_name 
        FROM leaves l 
        JOIN users u ON l.user_id = u.user_id 
        LEFT JOIN users alt ON l.alt_staff_id = alt.user_id 
        ORDER BY l.created_at DESC";

$result = $conn->query($sql);
$applications = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $applications[] = $row;
    }
    echo json_encode(['status' => 'success', 'data' => $applications]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $conn->error]);
}
?>
