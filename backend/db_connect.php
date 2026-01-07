<?php
$host = "localhost";
$username = "root";
$password = ""; // Default XAMPP password is empty
$dbname = "prisoner_attendance_db";

// Create connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ensure session cookies are available across the entire project
require_once __DIR__ . '/session_start.php';
?>
