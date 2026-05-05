<?php
require_once __DIR__ . '/../config/config.php';

try {
    $username = 'admin';
    $password = 'admin123';
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    // Check if admin exists
    $stmt = $pdo->prepare("SELECT * FROM users WHERE Username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user) {
        // Update existing admin
        $stmt = $pdo->prepare("UPDATE users SET Password = ?, Status = 'Approved', UserType = 'Admin' WHERE Username = ?");
        $stmt->execute([$hashed_password, $username]);
        echo "Admin password reset successfully for user: $username\n";
    } else {
        // Create new admin
        $email = 'admin@univ.edu';
        $stmt = $pdo->prepare("INSERT INTO users (Username, Email, Password, UserType, Status) VALUES (?, ?, ?, 'Admin', 'Approved')");
        $stmt->execute([$username, $email, $hashed_password]);
        echo "Admin user created successfully with password: $password\n";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
