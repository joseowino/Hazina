<?php

namespace App\Controllers;

use App\Models\Transaction;
use App\Models\Account;
use App\Models\Category;

class TransactionController
{
    private $transactionModel;
    private $accountModel;
    private $categoryModel;
    
    public function __construct()
    {
        $this->transactionModel = new Transaction();
        $this->accountModel = new Account();
        $this->categoryModel = new Category();
    }
    
    public function index(): void
    {
        $this->requireAuth();
        
        // Get filters from query string
        $filters = [
            'account_id' => $_GET['account_id'] ?? null,
            'category_id' => $_GET['category_id'] ?? null,
            'type' => $_GET['type'] ?? null,
            'start_date' => $_GET['start_date'] ?? null,
            'end_date' => $_GET['end_date'] ?? null,
            'limit' => 50,
            'offset' => (int)($_GET['page'] ?? 0) * 50
        ];
        
        $transactions = $this->transactionModel->getByUserId($_SESSION['user_id'], $filters);
        $accounts = $this->accountModel->getByUserId($_SESSION['user_id']);
        $categories = $this->categoryModel->getByUserId($_SESSION['user_id']);
        
        include __DIR__ . '/../../templates/transactions/index.php';
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
        
        $transaction = $this->transactionModel->findById($id);
        
        if (!$transaction || $transaction['user_id'] != $_SESSION['user_id']) {
            $_SESSION['error'] = 'Transaction not found.';
            header('Location: /transactions.php');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->processEdit($id);
        } else {
            $this->showEditForm($transaction);
        }
    }
    
    public function delete(int $id): void
    {
        $this->requireAuth();
        
        $transaction = $this->transactionModel->findById($id);
        
        if ($transaction && $transaction['user_id'] == $_SESSION['user_id']) {
            if ($this->transactionModel->delete($id)) {
                $_SESSION['success'] = 'Transaction deleted successfully.';
            } else {
                $_SESSION['error'] = 'Failed to delete transaction.';
            }
        } else {
            $_SESSION['error'] = 'Transaction not found.';
        }
        
        header('Location: /transactions.php');
        exit;
    }
    
    private function processCreate(): void
    {
        $data = [
            'account_id' => (int)$_POST['account_id'],
            'category_id' => (int)$_POST['category_id'],
            'amount' => floatval($_POST['amount']),
            'description' => trim($_POST['description'] ?? ''),
            'transaction_date' => $_POST['transaction_date']
        ];
        
        // Get category to determine if amount should be negative
        $category = $this->categoryModel->findById($data['category_id']);
        if ($category && $category['type'] === 'expense') {
            $data['amount'] = -abs($data['amount']);
        } else {
            $data['amount'] = abs($data['amount']);
        }
        
        $errors = $this->validateTransaction($data);
        
        if (empty($errors)) {
            if ($this->transactionModel->create($data)) {
                $_SESSION['success'] = 'Transaction added successfully!';
                header('Location: /transactions.php');
                exit;
            } else {
                $errors[] = 'Failed to add transaction.';
            }
        }
        
        $this->showCreateForm($errors, $data);
    }
    
    private function processEdit(int $id): void
    {
        $data = [
            'category_id' => (int)$_POST['category_id'],
            'amount' => floatval($_POST['amount']),
            'description' => trim($_POST['description'] ?? ''),
            'transaction_date' => $_POST['transaction_date']
        ];
        
        // Get category to determine if amount should be negative
        $category = $this->categoryModel->findById($data['category_id']);
        if ($category && $category['type'] === 'expense') {
            $data['amount'] = -abs($data['amount']);
        } else {
            $data['amount'] = abs($data['amount']);
        }
        
        $errors = $this->validateTransaction($data, false);
        
        if (empty($errors)) {
            if ($this->transactionModel->update($id, $data)) {
                $_SESSION['success'] = 'Transaction updated successfully!';
                header('Location: /transactions.php');
                exit;
            } else {
                $errors[] = 'Failed to update transaction.';
            }
        }
        
        $transaction = $this->transactionModel->findById($id);
        $transaction = array_merge($transaction, $data);
        $this->showEditForm($transaction, $errors);
    }
    
    private function validateTransaction(array $data, bool $requireAccount = true): array
    {
        $errors = [];
        
        if ($requireAccount && empty($data['account_id'])) {
            $errors[] = 'Please select an account.';
        }
        
        if (empty($data['category_id'])) {
            $errors[] = 'Please select a category.';
        }
        
        if (empty($data['amount']) || !is_numeric($data['amount'])) {
            $errors[] = 'Amount must be a valid number.';
        }
        
        if (empty($data['transaction_date'])) {
            $errors[] = 'Transaction date is required.';
        }
        
        return $errors;
    }
    
    private function showCreateForm(array $errors = [], array $data = []): void
    {
        $accounts = $this->accountModel->getByUserId($_SESSION['user_id']);
        $categories = $this->categoryModel->getByUserId($_SESSION['user_id']);
        
        include __DIR__ . '/../../templates/transactions/create.php';
    }
    
    private function showEditForm(array $transaction, array $errors = []): void
    {
        $categories = $this->categoryModel->getByUserId($_SESSION['user_id']);
        
        include __DIR__ . '/../../templates/transactions/edit.php';
    }
    
    private function requireAuth(): void
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login.php');
            exit;
        }
    }
}