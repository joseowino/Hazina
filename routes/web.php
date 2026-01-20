<?php

use App\Controllers\AuthController;

return [
    'GET' => [
        '' => [AuthController::class, 'login'],
        'login' => [AuthController::class, 'login'],
        'register' => [AuthController::class, 'register'],
        'logout' => [AuthController::class, 'logout']
    ],
    'POST' => [
        'login' => [AuthController::class, 'login'],
        'register' => [AuthController::class, 'register']
    ]
];
