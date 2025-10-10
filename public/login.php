<?php
session_start();
require_once '../vendor/autoload.php';

use App\Controllers\AuthController;

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: /dashboard.php');
    exit;
}

$controller = new AuthController();
$controller->login();
