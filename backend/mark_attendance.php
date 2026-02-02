<?php
include 'db_connect.php';

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (isset($data['prisoner_id'])) {
        $prisoner_id = mysqli_real_escape_string($conn, $data['prisoner_id']);
        $shift_name = isset($data['shift_name']) ? mysqli_real_escape_string($conn, $data['shift_name']) : 'General';
        $movement_type = isset($data['movement_type']) ? mysqli_real_escape_string($conn, $data['movement_type']) : 'Entry';
        
        $attendance_date = isset($data['attendance_date']) ? mysqli_real_escape_string($conn, $data['attendance_date']) : date("Y-m-d");
        $time_in = isset($data['time_in']) ? mysqli_real_escape_string($conn, $data['time_in']) : date("H:i:s");

        // Check if already marked for this specific shift and movement today
        try {
            $check_sql = "SELECT id FROM attendance 
                         WHERE prisoner_id = '$prisoner_id' 
                         AND attendance_date = '$attendance_date' 
                         AND shift_name = '$shift_name' 
                         AND movement_type = '$movement_type' 
                         LIMIT 1";
            $check_result = $conn->query($check_sql);

            if (!$check_result) {
                if (strpos($conn->error, 'Unknown column') !== false) {
                    echo json_encode(["status" => "error", "message" => "DATABASE ERROR: Missing 'shift_name' or 'movement_type' columns. Please run the SQL migration."]);
                } else {
                    echo json_encode(["status" => "error", "message" => "Database Query Error: " . $conn->error]);
                }
                exit;
            }

            if ($check_result->num_rows > 0) {
                echo json_encode(["status" => "info", "message" => "Attendance already marked for $shift_name ($movement_type)"]);
            } else {
                $sql = "INSERT INTO attendance (prisoner_id, attendance_date, time_in, status, shift_name, movement_type) 
                        VALUES ('$prisoner_id', '$attendance_date', '$time_in', 'Present', '$shift_name', '$movement_type')";
                
                if ($conn->query($sql) === TRUE) {
                    echo json_encode(["status" => "success", "message" => "Attendance marked for $shift_name ($movement_type) successfully!"]);
                } else {
                    echo json_encode(["status" => "error", "message" => "Database Insert Error: " . $conn->error]);
                }
            }
        } catch (Exception $e) {
            echo json_encode(["status" => "error", "message" => "System Error: " . $e->getMessage()]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Missing prisoner_id"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request method"]);
}

$conn->close();
?>
