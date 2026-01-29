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
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $contact_ext = mysqli_real_escape_string($conn, $_POST['contact_ext']);
    $new_password = $_POST['new_password'] ?? '';

    // Start building update query
    $update_fields = ["full_name = ?", "contact_ext = ?"];
    $params = [$full_name, $contact_ext];
    $types = "ss";

    if (!empty($new_password)) {
        $update_fields[] = "password = ?";
        $params[] = $new_password; // Plain text as per existing database
        $types .= "s";
    }

    $params[] = $user_id;
    $types .= "s";

    $sql = "UPDATE users SET " . implode(", ", $update_fields) . " WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);

    if ($stmt->execute()) {
        $_SESSION['full_name'] = $full_name; // Update session
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Database update failed: ' . $conn->error]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>
