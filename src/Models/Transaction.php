<?php

namespace App\Models;

use App\Database;
use PDO;

class Transaction
{
    private $db;
    
    public function __construct()
    {
        $this->db = Database::getInstance();
    }
    
    public function create(array $data): bool
    {
        $conn = $this->db->getConnection();
        
        try {
            $conn->beginTransaction();
            
            // Insert transaction
            $sql = "INSERT INTO transactions (account_id, category_id, amount, description, transaction_date) 
                    VALUES (:account_id, :category_id, :amount, :description, :transaction_date)";
                    
            $params = [
                ':account_id' => $data['account_id'],
                ':category_id' => $data['category_id'],
                ':amount' => $data['amount'],
                ':description' => $data['description'],
                ':transaction_date' => $data['transaction_date']
            ];
            
            $this->db->query($sql, $params);
            
            // Update account balance
            $accountModel = new Account();
            $accountModel->updateBalance($data['account_id'], $data['amount']);
            
            $conn->commit();
            return true;
            
        } catch (\Exception $e) {
            $conn->rollBack();
            return false;
        }
    }
    
    public function getByUserId(int $userId, array $filters = []): array
    {
        $sql = "SELECT t.*, a.name as account_name, c.name as category_name, c.type as category_type
                FROM transactions t
                INNER JOIN accounts a ON t.account_id = a.id
                INNER JOIN categories c ON t.category_id = c.id
                WHERE a.user_id = :user_id";
        
        $params = [':user_id' => $userId];
        
        // Apply filters
        if (!empty($filters['account_id'])) {
            $sql .= " AND t.account_id = :account_id";
            $params[':account_id'] = $filters['account_id'];
        }
        
        if (!empty($filters['category_id'])) {
            $sql .= " AND t.category_id = :category_id";
            $params[':category_id'] = $filters['category_id'];
        }
        
        if (!empty($filters['start_date'])) {
            $sql .= " AND t.transaction_date >= :start_date";
            $params[':start_date'] = $filters['start_date'];
        }
        
        if (!empty($filters['end_date'])) {
            $sql .= " AND t.transaction_date <= :end_date";
            $params[':end_date'] = $filters['end_date'];
        }
        
        if (!empty($filters['type'])) {
            $sql .= " AND c.type = :type";
            $params[':type'] = $filters['type'];
        }
        
        $sql .= " ORDER BY t.transaction_date DESC, t.id DESC";
        
        // Pagination
        if (isset($filters['limit'])) {
            $sql .= " LIMIT :limit";
            $offset = $filters['offset'] ?? 0;
            if ($offset > 0) {
                $sql .= " OFFSET :offset";
            }
        }
        
        $stmt = $this->db->getConnection()->prepare($sql);
        
        // Bind parameters
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        
        if (isset($filters['limit'])) {
            $stmt->bindValue(':limit', (int)$filters['limit'], PDO::PARAM_INT);
            if (isset($filters['offset']) && $filters['offset'] > 0) {
                $stmt->bindValue(':offset', (int)$filters['offset'], PDO::PARAM_INT);
            }
        }
        
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    public function findById(int $id): ?array
    {
        $sql = "SELECT t.*, a.user_id, a.name as account_name, c.name as category_name
                FROM transactions t
                INNER JOIN accounts a ON t.account_id = a.id
                INNER JOIN categories c ON t.category_id = c.id
                WHERE t.id = :id";
        
        $stmt = $this->db->query($sql, [':id' => $id]);
        $transaction = $stmt->fetch();
        
        return $transaction ?: null;
    }
    
    public function update(int $id, array $data): bool
    {
        $conn = $this->db->getConnection();
        
        try {
            $conn->beginTransaction();
            
            // Get old transaction to reverse balance
            $oldTransaction = $this->findById($id);
            if (!$oldTransaction) {
                return false;
            }
            
            // Reverse old balance
            $accountModel = new Account();
            $accountModel->updateBalance($oldTransaction['account_id'], -$oldTransaction['amount']);
            
            // Update transaction
            $sql = "UPDATE transactions 
                    SET category_id = :category_id, amount = :amount, 
                        description = :description, transaction_date = :transaction_date,
                        updated_at = CURRENT_TIMESTAMP
                    WHERE id = :id";
                    
            $params = [
                ':id' => $id,
                ':category_id' => $data['category_id'],
                ':amount' => $data['amount'],
                ':description' => $data['description'],
                ':transaction_date' => $data['transaction_date']
            ];
            
            $this->db->query($sql, $params);
            
            // Apply new balance
            $accountModel->updateBalance($oldTransaction['account_id'], $data['amount']);
            
            $conn->commit();
            return true;
            
        } catch (\Exception $e) {
            $conn->rollBack();
            return false;
        }
    }
    
    public function delete(int $id): bool
    {
        $conn = $this->db->getConnection();
        
        try {
            $conn->beginTransaction();
            
            // Get transaction to reverse balance
            $transaction = $this->findById($id);
            if (!$transaction) {
                return false;
            }
            
            // Reverse balance
            $accountModel = new Account();
            $accountModel->updateBalance($transaction['account_id'], -$transaction['amount']);
            
            // Delete transaction
            $sql = "DELETE FROM transactions WHERE id = :id";
            $this->db->query($sql, [':id' => $id]);
            
            $conn->commit();
            return true;
            
        } catch (\Exception $e) {
            $conn->rollBack();
            return false;
        }
    }
    
    public function getMonthlyTotal(int $userId, string $type, int $month, int $year): float
    {
        $sql = "SELECT SUM(t.amount) as total
                FROM transactions t
                INNER JOIN accounts a ON t.account_id = a.id
                INNER JOIN categories c ON t.category_id = c.id
                WHERE a.user_id = :user_id 
                AND c.type = :type
                AND strftime('%m', t.transaction_date) = :month
                AND strftime('%Y', t.transaction_date) = :year";
        
        $params = [
            ':user_id' => $userId,
            ':type' => $type,
            ':month' => sprintf('%02d', $month),
            ':year' => $year
        ];
        
        $stmt = $this->db->query($sql, $params);
        $result = $stmt->fetch();
        
        return abs((float)($result['total'] ?? 0));
    }
}