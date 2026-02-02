<?php
include 'db_connect.php';

header('Content-Type: application/json');

$sql = "SELECT a.time_in, a.prisoner_id, p.full_name, p.cell_number, a.status, p.photo_path, a.shift_name, a.movement_type 
        FROM attendance a 
        JOIN prisoners p ON a.prisoner_id = p.prisoner_id 
        WHERE a.attendance_date = CURDATE() 
        ORDER BY a.time_in DESC 
        LIMIT 10";

$result = $conn->query($sql);
$attendance = [];

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        // Convert paths for frontend if needed
        $row['photo_path'] = str_replace('../', '', $row['photo_path']);
        $attendance[] = $row;
    }
}

echo json_encode($attendance);
$conn->close();
?>
