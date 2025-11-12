<?php

namespace App\Models;

use App\Database;

class Account
{
    private $db;
    
    public function __construct()
    {
        $this->db = Database::getInstance();
    }
    
    public function create(array $data): bool
    {
        $sql = "INSERT INTO accounts (account_name, account_type, memo) 
                VALUES (:account_name, :account_type, :memo)";
                
        $params = [
            ':account_name' => $data['account_name'],
            ':account_type' => $data['account_type'],
            ':memo' => $data['memo'] 
        ];
        
        try {
            $this->db->query($sql, $params);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
    
    public function getByUserId(int $userId): array
    {
        $sql = "SELECT * FROM accounts WHERE user_id = :user_id AND is_active = 1 ORDER BY name";
        $stmt = $this->db->query($sql, [':user_id' => $userId]);
        return $stmt->fetchAll();
    }
    
    public function findById(int $id): ?array
    {
        $sql = "SELECT * FROM accounts WHERE id = :id";
        $stmt = $this->db->query($sql, [':id' => $id]);
        
        $account = $stmt->fetch();
        return $account ?: null;
    }
    
    public function update(int $id, array $data): bool
    {
        $sql = "UPDATE accounts SET name = :name, type = :type, 
                updated_at = CURRENT_TIMESTAMP WHERE id = :id";
                
        $params = [
            ':id' => $id,
            ':name' => $data['name'],
            ':type' => $data['type']
        ];
        
        try {
            $this->db->query($sql, $params);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
    
    public function delete(int $id): bool
    {
        $sql = "UPDATE accounts SET is_active = 0 WHERE id = :id";
        
        try {
            $this->db->query($sql, [':id' => $id]);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
    
    public function updateBalance(int $accountId, float $amount): bool
    {
        $sql = "UPDATE accounts SET balance = balance + :amount WHERE id = :id";
        
        try {
            $this->db->query($sql, [
                ':amount' => $amount,
                ':id' => $accountId
            ]);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
    
    public function getTotalBalance(int $userId): float
    {
        $sql = "SELECT SUM(balance) as total FROM accounts 
                WHERE user_id = :user_id AND is_active = 1";
        $stmt = $this->db->query($sql, [':user_id' => $userId]);
        $result = $stmt->fetch();
        
        return (float)($result['total'] ?? 0);
    }
}