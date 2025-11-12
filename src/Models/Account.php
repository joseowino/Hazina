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
    
    public function getByName(string $accountName): array
    {
        $sql = "SELECT * FROM accounts WHERE account_name = :account_name AND is_active = 1 ORDER BY account_name";
        $stmt = $this->db->query($sql, [':account_name' => $accountName]);
        return $stmt->fetchAll();
    }
    
    public function findById(int $id)
    {
        $sql = "SELECT * FROM accounts WHERE id = :id";
        $stmt = $this->db->query($sql, [':id' => $id]);
        
        $account = $stmt->fetch();
        return $account ?: null;
    }
    
    public function update(int $id, array $data): bool
    {
        $sql = "UPDATE accounts SET account_name = :account_name, account_type = :account_type, memo = :memo,
                updated_at = CURRENT_TIMESTAMP WHERE id = :id";
                
        $params = [
            ':id' => $id,
            ':account_name' => $data['account_name'],
            ':account_type' => $data['account_type'],
            ':memo' => $data['memo'] ?? null
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
    
    public function getTotalBalance(): float
    {
        $sql = "SELECT COUNT(*) as total FROM accounts WHERE is_active = 1";
        $stmt = $this->db->query($sql);
        $result = $stmt->fetch();
        
        return (float)($result['total'] ?? 0);
    }
}