<?php
require_once __DIR__ . '/../config/config.php';

try {
    // Add Status column to users table if it doesn't exist
    $pdo->exec("ALTER TABLE users ADD COLUMN IF NOT EXISTS Status ENUM('Pending', 'Approved', 'Rejected') DEFAULT 'Approved' AFTER UserType");
    
    // Set existing instructors to Approved just in case
    $pdo->exec("UPDATE users SET Status = 'Approved' WHERE Status IS NULL OR Status = ''");
    
    echo "Database updated successfully.\n";
} catch (PDOException $e) {
    echo "Error updating database: " . $e->getMessage() . "\n";
}
