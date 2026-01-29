<?php
require_once 'session_start.php';
require_once 'db_connect.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not authenticated']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $leave_date = mysqli_real_escape_string($conn, $_POST['leave_date']);
    $shift = mysqli_real_escape_string($conn, $_POST['shift']);
    $duration = mysqli_real_escape_string($conn, $_POST['duration']);
    $reason = mysqli_real_escape_string($conn, $_POST['reason']);
    $alt_staff_id = mysqli_real_escape_string($conn, $_POST['alt_staff_id']);

    if (empty($leave_date) || empty($shift) || empty($duration) || empty($reason) || empty($alt_staff_id)) {
        echo json_encode(['status' => 'error', 'message' => 'All fields are required']);
        exit();
    }

    $sql = "INSERT INTO leaves (user_id, leave_date, shift, duration, reason, alt_staff_id) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssss", $user_id, $leave_date, $shift, $duration, $reason, $alt_staff_id);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Leave application submitted successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $conn->error]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>
