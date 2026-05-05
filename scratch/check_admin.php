<?php
require_once __DIR__ . '/../config/config.php';

try {
    // Check if an admin exists
    $stmt = $pdo->prepare("SELECT * FROM users WHERE UserType = 'Admin' LIMIT 1");
    $stmt->execute();
    $admin = $stmt->fetch();

    if ($admin) {
        echo "Admin user found: " . $admin['Username'] . " (" . $admin['Email'] . ")\n";
    } else {
        // Create a default admin user
        $username = 'admin';
        $email = 'admin@univ.edu';
        $password = 'admin123';
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $pdo->prepare("INSERT INTO users (Username, Email, Password, UserType, Status) VALUES (?, ?, ?, 'Admin', 'Approved')");
        $stmt->execute([$username, $email, $hashed_password]);
        
        echo "Default admin user created:\n";
        echo "Username: $username\n";
        echo "Email: $email\n";
        echo "Password: $password\n";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
