<?php
session_start();

// Redirect to dashboard if logged in
if (isset($_SESSION['user_id'])) {
    header('Location: /dashboard.php');
    exit;
}

// Redirect to login page
header('Location: /login.php');
exit;