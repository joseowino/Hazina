<?php

use App\Controllers\AuthController;

$autoload = __DIR__ . '/../vendor/autoload.php';
if (!file_exists($autoload)) {
    error_log('Autoload file not found: ' . $autoload);
    http_response_code(500);
    echo 'Application dependency files are missing. Run "composer install" in project root.';
    exit;
}

require_once $autoload;

session_start();

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: /dashboard.php');
    exit;
}

$controller = new AuthController();
$controller->login();
