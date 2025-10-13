<?php

namespace App\Controllers;

use App\Models\Account;

class AccountController
{
    private $accountModel;
    
    public function __construct()
    {
        $this->accountModel = new Account();
    }
    
    public function index(): void
    {
        $this->requireAuth();
        
        $accounts = $this->accountModel->getByUserId($_SESSION['user_id']);
        $totalBalance = $this->accountModel->getTotalBalance($_SESSION['user_id']);
        
        include __DIR__ . '/../../templates/accounts/index.php';
    }
    
    public function create(): void
    {
        $this->requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->processCreate();
        } else {
            $this->showCreateForm();
        }
    }
    
    public function edit(int $id): void
    {
        $this->requireAuth();
        
        $account = $this->accountModel->findById($id);
        
        if (!$account || $account['user_id'] != $_SESSION['user_id']) {
            $_SESSION['error'] = 'Account not found.';
            header('Location: /accounts.php');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->processEdit($id);
        } else {
            $this->showEditForm($account);
        }
    }
    
    public function delete(int $id): void
    {
        $this->requireAuth();
        
        $account = $this->accountModel->findById($id);
        
        if ($account && $account['user_id'] == $_SESSION['user_id']) {
            if ($this->accountModel->delete($id)) {
                $_SESSION['success'] = 'Account deleted successfully.';
            } else {
                $_SESSION['error'] = 'Failed to delete account.';
            }
        } else {
            $_SESSION['error'] = 'Account not found.';
        }
        
        header('Location: /accounts.php');
        exit;
    }
    
    private function processCreate(): void
    {
        $data = [
            'user_id' => $_SESSION['user_id'],
            'name' => trim($_POST['name'] ?? ''),
            'type' => $_POST['type'] ?? '',
            'balance' => floatval($_POST['balance'] ?? 0)
        ];
        
        $errors = $this->validateAccount($data);
        
        if (empty($errors)) {
            if ($this->accountModel->create($data)) {
                $_SESSION['success'] = 'Account created successfully!';
                header('Location: /accounts.php');
                exit;
            } else {
                $errors[] = 'Failed to create account.';
            }
        }
        
        $this->showCreateForm($errors, $data);
    }
    
    private function processEdit(int $id): void
    {
        $data = [
            'name' => trim($_POST['name'] ?? ''),
            'type' => $_POST['type'] ?? ''
        ];
        
        $errors = $this->validateAccount($data, false);
        
        if (empty($errors)) {
            if ($this->accountModel->update($id, $data)) {
                $_SESSION['success'] = 'Account updated successfully!';
                header('Location: /accounts.php');
                exit;
            } else {
                $errors[] = 'Failed to update account.';
            }
        }
        
        $account = $this->accountModel->findById($id);
        $account = array_merge($account, $data);
        $this->showEditForm($account, $errors);
    }
    
    private function validateAccount(array $data, bool $requireBalance = true): array
    {
        $errors = [];
        
        if (empty($data['name'])) {
            $errors[] = 'Account name is required.';
        }
        
        $validTypes = ['checking', 'savings', 'credit_card', 'investment'];
        if (empty($data['type']) || !in_array($data['type'], $validTypes)) {
            $errors[] = 'Invalid account type.';
        }
        
        if ($requireBalance && isset($data['balance']) && !is_numeric($data['balance'])) {
            $errors[] = 'Balance must be a valid number.';
        }
        
        return $errors;
    }
    
    private function showCreateForm(array $errors = [], array $data = []): void
    {
        include __DIR__ . '/../../templates/accounts/create.php';
    }
    
    private function showEditForm(array $account, array $errors = []): void
    {
        include __DIR__ . '/../../templates/accounts/edit.php';
    }
    
    private function requireAuth(): void
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login.php');
            exit;
        }
    }
}