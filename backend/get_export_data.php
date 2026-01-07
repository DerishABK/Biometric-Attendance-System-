<?php
require_once 'session_start.php';
include 'db_connect.php';

$type = isset($_GET['type']) ? $_GET['type'] : '';

if ($type === 'staff') {
    $sql = "SELECT user_id, full_name, role, designation, status, assigned_wing, shift_type, contact_ext FROM users 
            ORDER BY FIELD(designation, 'Superintendent of Police', 'Sub Inspector', 'Assistant Sub Inspector', 'Constable'), full_name";
    $result = $conn->query($sql);
    $data = [];
    while($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    header('Content-Type: application/json');
    echo json_encode($data);
} elseif ($type === 'prisoners') {
    $sql = "SELECT prisoner_id, full_name, block_wing, cell_number, crime, admission_date, expected_release FROM prisoners 
            ORDER BY block_wing, full_name";
    $result = $conn->query($sql);
    $data = [];
    while($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    header('Content-Type: application/json');
    echo json_encode($data);
} else {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid type']);
}
$conn->close();
?>
