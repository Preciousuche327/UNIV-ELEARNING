<?php
require_once __DIR__ . '/../config/config.php';

try {
    // Delete existing admin if any
    $pdo->prepare("DELETE FROM users WHERE Username = 'admin' OR Email = 'admin@univ.edu'")->execute();
    
    // Create fresh admin
    $username = 'admin';
    $email = 'admin@univ.edu';
    $password = 'admin123';
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    $stmt = $pdo->prepare("INSERT INTO users (Username, Email, Password, UserType, Status) VALUES (?, ?, ?, 'Admin', 'Approved')");
    $stmt->execute([$username, $email, $hashed_password]);
    
    echo "CLEAN RESET SUCCESSFUL!\n";
    echo "======================\n";
    echo "Login: admin\n";
    echo "Password: admin123\n";
    echo "======================\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
