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
        $sql = "INSERT INTO transactions 
                (bank_account, transaction_account, category, company_id, amount, memo, transaction_date)
                VALUES (:bank_account, :transaction_account, :category, :company_id, :amount, :memo, :transaction_date)";
        
        $params = [
            ':bank_account' => $data['bank_account'],
            ':transaction_account' => $data['transaction_account'],
            ':category' => $data['category'],
            ':company_id' => $data['company_id'],
            ':amount' => $data['amount'],
            ':memo' => $data['memo'] ?? null,
            ':transaction_date' => $data['transaction_date']
        ];

        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute($params);
        } catch (\Exception $e) {
            // You can log the error message here if needed
            return false;
        }
    }

    public function getAll(array $filters = []): array
    {
        $sql = "SELECT t.*,
                       ba.account_name AS bank_account_name,
                       ta.account_name AS transaction_account_name,
                       c.company_name
                FROM transactions t
                INNER JOIN accounts ba ON t.bank_account = ba.id
                INNER JOIN accounts ta ON t.transaction_account = ta.id
                INNER JOIN companies c ON t.company_id = c.id
                WHERE ba.is_active = 1 
                  AND ta.is_active = 1";

        $params = [];

        if (!empty($filters['bank_account'])) {
            $sql .= " AND t.bank_account = :bank_account";
            $params[':bank_account'] = $filters['bank_account'];
        }

        if (!empty($filters['transaction_account'])) {
            $sql .= " AND t.transaction_account = :transaction_account";
            $params[':transaction_account'] = $filters['transaction_account'];
        }

        if (!empty($filters['category'])) {
            $sql .= " AND t.category = :category";
            $params[':category'] = $filters['category'];
        }

        if (!empty($filters['start_date'])) {
            $sql .= " AND t.transaction_date >= :start_date";
            $params[':start_date'] = $filters['start_date'];
        }

        if (!empty($filters['end_date'])) {
            $sql .= " AND t.transaction_date <= :end_date";
            $params[':end_date'] = $filters['end_date'];
        }

        $sql .= " ORDER BY t.transaction_date DESC, t.id DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findById(int $id)
    {
        $sql = "SELECT t.*,
                       ba.account_name AS bank_account_name,
                       ta.account_name AS transaction_account_name,
                       c.company_name
                FROM transactions t
                INNER JOIN accounts ba ON t.bank_account = ba.id
                INNER JOIN accounts ta ON t.transaction_account = ta.id
                INNER JOIN companies c ON t.company_id = c.id
                WHERE t.id = :id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        $transaction = $stmt->fetch(PDO::FETCH_ASSOC);

        return $transaction ?: null;
    }

    public function update(int $id, array $data): bool
    {
        $sql = "UPDATE transactions
                SET bank_account = :bank_account,
                    transaction_account = :transaction_account,
                    category = :category,
                    amount = :amount,
                    memo = :memo,
                    transaction_date = :transaction_date,
                    updated_at = CURRENT_TIMESTAMP
                WHERE id = :id";

        $params = [
            ':id' => $id,
            ':bank_account' => $data['bank_account'],
            ':transaction_account' => $data['transaction_account'],
            ':category' => $data['category'],
            ':amount' => $data['amount'],
            ':memo' => $data['memo'] ?? null,
            ':transaction_date' => $data['transaction_date']
        ];

        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute($params);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function delete(int $id): bool
    {
        $sql = "DELETE FROM transactions WHERE id = :id";

        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([':id' => $id]);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getMonthlyTotal(string $category, int $month, int $year): float
    {
        $sql = "SELECT SUM(t.amount) AS total
                FROM transactions t
                WHERE t.category = :category
                AND strftime('%m', t.transaction_date) = :month
                AND strftime('%Y', t.transaction_date) = :year";

        $params = [
            ':category' => $category,
            ':month' => sprintf('%02d', $month),
            ':year' => $year
        ];

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return abs((float)($result['total'] ?? 0));
    }
}
