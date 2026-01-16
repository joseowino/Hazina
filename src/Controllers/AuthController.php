<?php

namespace App\Controllers;

use App\Models\User;

class AuthController
{
    private $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }
    
    public function register(): void
    {
        session_start();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->processRegistration();
        } else {
            $this->showRegistrationForm();
        }
    }

    public function login(): void
    {
        session_start();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->processLogin();
        } else {
            $this->showLoginForm();
        }
    }
    
    public function logout(): void
    {
        session_start();
        session_destroy();
        header('Location: /login.php');
        exit;
    }
    
    private function processRegistration(): void
    {
        $data = [
            'first_name' => trim($_POST['first_name'] ?? ''),
            'last_name' => trim($_POST['last_name'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'password' => $_POST['password'] ?? '',
            'password_confirm' => $_POST['password_confirm'] ?? ''
        ];
        
        $errors = $this->validateRegistration($data);
        
        if (empty($errors)) {
            if ($this->userModel->create($data)) {
                $_SESSION['success'] = 'Registration successful! Please log in.';
                header('Location: /login.php');
                exit;
            } else {
                $errors[] = 'Registration failed. Please try again.';
            }
        }
        
        $this->showRegistrationForm($errors, $data);
    }
    
    private function processLogin(): void
    {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        
        $errors = [];
        
        if (empty($email) || empty($password)) {
            $errors[] = 'Email and password are required.';
        } else {
            $user = $this->userModel->getByEmail($email);

            echo($user['email']);
            
            if ($user && $this->userModel->verifyPassword($password, $user['password_hash'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];

                header('Location: /dashboard.php');
                exit;
            } else {
                $errors[] = 'Invalid email or password.';
            }
        }
        
        $this->showLoginForm($errors, ['email' => $email]);
    }
    
    private function validateRegistration(array $data): array
    {
        $errors = [];
        
        if (empty($data['first_name'])) {
            $errors[] = 'First name is required.';
        }
        
        if (empty($data['last_name'])) {
            $errors[] = 'Last name is required.';
        }
        
        if (empty($data['email'])) {
            $errors[] = 'Email is required.';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Invalid email format.';
        } elseif ($this->userModel->emailExists($data['email'])) {
            $errors[] = 'Email already exists.';
        }
        
        if (empty($data['password'])) {
            $errors[] = 'Password is required.';
        } elseif (strlen($data['password']) < 8) {
            $errors[] = 'Password must be at least 8 characters long.';
        }
        if ($data['password'] !== $data['password_confirm']) {
            $errors[] = 'Passwords do not match.';
        }
        
        return $errors;
    }
    
    private function showRegistrationForm(array $errors = [], array $data = []): void
    {
        include __DIR__ . '/../../templates/auth/register.php';
    }
    
    private function showLoginForm(array $errors = [], array $data = []): void
    {
        include __DIR__ . '/../../templates/auth/login.php';
    }
}