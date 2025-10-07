<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Database;

try {
    $db = Database::getInstance();
    
    // Read and execute schema
    $schema = file_get_contents(__DIR__ . '/schema.sql');
    $db->getConnection()->exec($schema);
    
    echo "Database setup completed successfully!\n";
    
} catch (Exception $e) {
    echo "Database setup failed: " . $e->getMessage() . "\n";
}