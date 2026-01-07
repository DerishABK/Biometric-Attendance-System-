<?php
include 'db_connect.php';
// session_start() is already handled in db_connect.php -> session_start.php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = mysqli_real_escape_string($conn, $_POST['user_id']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    $sql = "SELECT id, role, full_name, password FROM users WHERE user_id = '$user_id'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        // For security, password_verify() should be used with hashed passwords.
        // But since the schema insert used plain text for demo, we'll check directly.
        if ($password === $row['password']) {
            $_SESSION['user_id'] = $user_id;
            $_SESSION['role'] = $row['role'];
            $_SESSION['full_name'] = $row['full_name'];

            switch($row['role']) {
                case 'admin': $redirect = "dashboard-admin.php"; break;
                case 'warden': $redirect = "dashboard-warden.php"; break;
                case 'guard': $redirect = "dashboard-guard.php"; break;
                default: $redirect = "index.php"; break;
            }
            
            echo json_encode(["status" => "success", "redirect" => $redirect]);
        } else {
            echo json_encode(["status" => "error", "message" => "Invalid password"]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "User not found"]);
    }
}
?>
