<?php

namespace App\Models;

use App\Database;

class User
{
    private $db;
    
    public function __construct()
    {
        $this->db = Database::getInstance();
    }
    
    public function create(array $data): bool
    {
        $sql = "INSERT INTO users (email, password_hash, first_name, last_name) 
                VALUES (:email, :password_hash, :first_name, :last_name)";
                
        $params = [
            ':email' => $data['email'],
            ':password_hash' => password_hash($data['password'], PASSWORD_DEFAULT),
            ':first_name' => $data['first_name'],
            ':last_name' => $data['last_name']
        ];
        
        try {
            $this->db->query($sql, $params);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
    
    public function findByEmail(string $email): ?array
    {
        $sql = "SELECT * FROM users WHERE email = :email";
        $stmt = $this->db->query($sql, [':email' => $email]);
        
        $user = $stmt->fetch();
        return $user ?: null;
    }
    
    public function findById(int $id): ?array
    {
        $sql = "SELECT * FROM users WHERE id = :id";
        $stmt = $this->db->query($sql, [':id' => $id]);
        
        $user = $stmt->fetch();
        return $user ?: null;
    }
    
    public function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }
    
    public function emailExists(string $email): bool
    {
        return $this->findByEmail($email) !== null;
    }
}