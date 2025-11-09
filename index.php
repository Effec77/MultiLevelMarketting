<?php
session_start();
require_once 'config.php';

// Redirect to login if not logged in
if (!isset($_SESSION['member_code'])) {
    header('Location: login.php');
    exit();
}

// Redirect to dashboard
header('Location: dashboard.php');
exit();
?>
