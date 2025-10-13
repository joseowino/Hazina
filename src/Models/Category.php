<?php

namespace App\Models;

use App\Database;

class Category
{
    private $db;
    
    public function __construct()
    {
        $this->db = Database::getInstance();
    }
    
    public function create(array $data): bool
    {
        $sql = "INSERT INTO categories (user_id, name, type, color) 
                VALUES (:user_id, :name, :type, :color)";
                
        $params = [
            ':user_id' => $data['user_id'],
            ':name' => $data['name'],
            ':type' => $data['type'],
            ':color' => $data['color'] ?? '#007bff'
        ];
        
        try {
            $this->db->query($sql, $params);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
    
    public function getByUserId(int $userId, ?string $type = null): array
    {
        if ($type) {
            $sql = "SELECT * FROM categories WHERE user_id = :user_id AND type = :type ORDER BY name";
            $params = [':user_id' => $userId, ':type' => $type];
        } else {
            $sql = "SELECT * FROM categories WHERE user_id = :user_id ORDER BY type, name";
            $params = [':user_id' => $userId];
        }
        
        $stmt = $this->db->query($sql, $params);
        return $stmt->fetchAll();
    }
    
    public function findById(int $id): ?array
    {
        $sql = "SELECT * FROM categories WHERE id = :id";
        $stmt = $this->db->query($sql, [':id' => $id]);
        
        $category = $stmt->fetch();
        return $category ?: null;
    }
    
    public function seedDefaultCategories(int $userId): void
    {
        $defaultCategories = [
            // Income categories
            ['name' => 'Salary', 'type' => 'income', 'color' => '#28a745'],
            ['name' => 'Freelance', 'type' => 'income', 'color' => '#20c997'],
            ['name' => 'Investment', 'type' => 'income', 'color' => '#17a2b8'],
            
            // Expense categories
            ['name' => 'Food & Dining', 'type' => 'expense', 'color' => '#dc3545'],
            ['name' => 'Transportation', 'type' => 'expense', 'color' => '#fd7e14'],
            ['name' => 'Housing', 'type' => 'expense', 'color' => '#ffc107'],
            ['name' => 'Utilities', 'type' => 'expense', 'color' => '#6610f2'],
            ['name' => 'Healthcare', 'type' => 'expense', 'color' => '#e83e8c'],
            ['name' => 'Entertainment', 'type' => 'expense', 'color' => '#6f42c1'],
            ['name' => 'Shopping', 'type' => 'expense', 'color' => '#fd7e14'],
            ['name' => 'Education', 'type' => 'expense', 'color' => '#007bff'],
            ['name' => 'Other', 'type' => 'expense', 'color' => '#6c757d']
        ];
        
        foreach ($defaultCategories as $category) {
            $category['user_id'] = $userId;
            $this->create($category);
        }
    }
}