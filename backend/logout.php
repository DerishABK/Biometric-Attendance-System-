<?php
require_once 'session_start.php';
session_unset();
session_destroy();
header("Location: ../frontend/index.php");
exit();
?>
