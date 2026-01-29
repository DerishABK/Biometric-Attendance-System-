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

    // Backend Validation: Check if submitted shift matches user's shift_type
    $user_query = "SELECT shift_type FROM users WHERE user_id = ?";
    $u_stmt = $conn->prepare($user_query);
    $u_stmt->bind_param("s", $user_id);
    $u_stmt->execute();
    $u_res = $u_stmt->get_result();
    $u_data = $u_res->fetch_assoc();
    
    $user_shift_type = $u_data['shift_type'];
    $mapped_shift = 'General';
    if (stripos($user_shift_type, 'Day') !== false) $mapped_shift = 'Day Shift';
    elseif (stripos($user_shift_type, 'Night') !== false) $mapped_shift = 'Night Shift';

    if ($shift !== $mapped_shift) {
        echo json_encode(['status' => 'error', 'message' => 'You can only apply for a leave on your assigned shift: ' . $mapped_shift]);
        exit();
    }

    // Backend Validation: Check if alt_staff_id is from the same shift_type
    $alt_query = "SELECT shift_type FROM users WHERE user_id = ?";
    $a_stmt = $conn->prepare($alt_query);
    $a_stmt->bind_param("s", $alt_staff_id);
    $a_stmt->execute();
    $a_res = $a_stmt->get_result();
    $a_data = $a_res->fetch_assoc();

    if (!$a_data || $a_data['shift_type'] !== $user_shift_type) {
        echo json_encode(['status' => 'error', 'message' => 'Alternative arrangement must be with a staff member from the same shift.']);
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
