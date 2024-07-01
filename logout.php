<?php
session_start();
include '../config/config.php'; // Adjust the path as necessary

// kalo/jika session tidak ada, tolong redirect ke login
if (!isset($_SESSION['name'])) {
    header("location:/library/login.php?error-access-failed");
    exit;
}
session_destroy();
header("location:/library/login.php");
