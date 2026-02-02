<?php
include 'db_connect.php';

header('Content-Type: application/json');

$sql = "SELECT prisoner_id, full_name FROM prisoners";
$result = $conn->query($sql);
$names = [];

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $names[$row['prisoner_id']] = $row['full_name'];
    }
}

echo json_encode($names);
$conn->close();
?>
