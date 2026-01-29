<?php
require_once 'session_start.php';
require_once 'db_connect.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['status' => 'error', 'message' => 'Not authorized']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $leave_id = (int)$_POST['leave_id'];
    $action = $_POST['action']; // 'Approved' or 'Rejected'

    if (!in_array($action, ['Approved', 'Rejected'])) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
        exit();
    }

    $conn->begin_transaction();

    try {
        // Update leave status
        $sql = "UPDATE leaves SET status = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $action, $leave_id);
        $stmt->execute();

        if ($action === 'Approved') {
            // Get user_id for this leave
            $user_sql = "SELECT user_id FROM leaves WHERE id = ?";
            $user_stmt = $conn->prepare($user_sql);
            $user_stmt->bind_param("i", $leave_id);
            $user_stmt->execute();
            $user_res = $user_stmt->get_result();
            $leave_info = $user_res->fetch_assoc();

            if ($leave_info) {
                // Update user status to 'On Leave'
                $update_user_sql = "UPDATE users SET status = 'On Leave' WHERE user_id = ?";
                $update_user_stmt = $conn->prepare($update_user_sql);
                $update_user_stmt->bind_param("s", $leave_info['user_id']);
                $update_user_stmt->execute();
            }
        }

        $conn->commit();
        echo json_encode(['status' => 'success', 'message' => 'Leave application ' . strtolower($action) . ' successfully']);
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>
