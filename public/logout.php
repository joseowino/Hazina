<?php
session_start();
require_once '../vendor/autoload.php';

use App\Controllers\AuthController;

$controller = new AuthController();
$controller->logout();

header('Location: /login.php');
exit;
