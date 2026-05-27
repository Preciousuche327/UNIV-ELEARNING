<?php
require_once 'config/config.php';

echo "<h1>Deployment Setup Utility</h1>";

// 1. Check Database Connection
try {
    $pdo->query("SELECT 1");
    echo "<p style='color: green;'>✅ Database Connection Successful!</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Database Connection Failed: " . $e->getMessage() . "</p>";
    echo "<p>Please ensure you have set the DB_HOST, DB_NAME, DB_USER, and DB_PASS environment variables in your dashboard.</p>";
    exit;
}

// 2. Check if tables exist
$tables = ['users', 'courses', 'enrollments'];
$missing = false;
foreach ($tables as $table) {
    $stmt = $pdo->prepare("SHOW TABLES LIKE ?");
    $stmt->execute([$table]);
    if ($stmt->rowCount() == 0) {
        $missing = true;
        break;
    }
}

if ($missing) {
    echo "<p>⚠️ Tables are missing. Attempting to import schema.sql...</p>";
    $sql = file_get_contents('database/schema.sql');
    
    // Remove 'CREATE DATABASE' and 'USE' statements as they might fail on managed DBs
    $sql = preg_replace('/CREATE DATABASE IF NOT EXISTS.*;/i', '', $sql);
    $sql = preg_replace('/USE .*;/i', '', $sql);
    
    try {
        $pdo->exec($sql);
        echo "<p style='color: green;'>✅ schema.sql imported successfully!</p>";
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Error importing schema: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p style='color: green;'>✅ Tables already exist. No import needed.</p>";
}

// 2b. Apply small schema updates needed by the current code.
try {
    $stmt = $pdo->query("SHOW COLUMNS FROM users LIKE 'Status'");
    if ($stmt->rowCount() === 0) {
        $pdo->exec("ALTER TABLE users ADD COLUMN Status ENUM('Pending', 'Approved', 'Rejected') NOT NULL DEFAULT 'Approved' AFTER UserType");
        echo "<p style='color: green;'>Added users.Status column.</p>";
    }

    $pdo->exec("UPDATE users SET Status = 'Approved' WHERE Status IS NULL OR Status = ''");

    $pdo->exec("CREATE TABLE IF NOT EXISTS password_reset_tokens (
        TokenID INT AUTO_INCREMENT PRIMARY KEY,
        UserID INT NOT NULL,
        TokenHash CHAR(64) NOT NULL UNIQUE,
        ExpiresAt DATETIME NOT NULL,
        UsedAt DATETIME NULL DEFAULT NULL,
        CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_password_reset_user (UserID),
        INDEX idx_password_reset_expires (ExpiresAt),
        FOREIGN KEY (UserID) REFERENCES users(UserID) ON DELETE CASCADE
    ) ENGINE=InnoDB;");
    echo "<p style='color: green;'>Password reset token table is ready.</p>";

    $stmt = $pdo->query("SHOW COLUMNS FROM users LIKE 'EmailVerifiedAt'");
    if ($stmt->rowCount() === 0) {
        $pdo->exec("ALTER TABLE users ADD COLUMN EmailVerifiedAt DATETIME NULL DEFAULT NULL AFTER Status");
        $pdo->exec("UPDATE users SET EmailVerifiedAt = NOW() WHERE EmailVerifiedAt IS NULL");
        echo "<p style='color: green;'>Added users.EmailVerifiedAt column.</p>";
    }

    $pdo->exec("CREATE TABLE IF NOT EXISTS email_verification_tokens (
        TokenID INT AUTO_INCREMENT PRIMARY KEY,
        UserID INT NOT NULL,
        TokenHash CHAR(64) NOT NULL UNIQUE,
        ExpiresAt DATETIME NOT NULL,
        UsedAt DATETIME NULL DEFAULT NULL,
        CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_email_verification_user (UserID),
        INDEX idx_email_verification_expires (ExpiresAt),
        FOREIGN KEY (UserID) REFERENCES users(UserID) ON DELETE CASCADE
    ) ENGINE=InnoDB;");
    echo "<p style='color: green;'>Email verification token table is ready.</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>Error applying schema updates: " . $e->getMessage() . "</p>";
}

// 3. Check Seed Data
$stmt = $pdo->query("SELECT COUNT(*) FROM users");
if ($stmt->fetchColumn() <= 1) { // Only admin or empty
    echo "<p>⚠️ No seed data found. Attempting to import seed.sql...</p>";
    $seedSql = file_get_contents('database/seed.sql');
    try {
        $pdo->exec($seedSql);
        echo "<p style='color: green;'>✅ seed.sql imported successfully!</p>";
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Error importing seed data: " . $e->getMessage() . "</p>";
    }
}

echo "<h2>Environment Status</h2>";
echo "<ul>";
echo "<li><strong>APP_NAME:</strong> " . APP_NAME . "</li>";
echo "<li><strong>BASE_URL:</strong> " . BASE_URL . "</li>";
echo "<li><strong>DB_HOST:</strong> " . DB_HOST . "</li>";
echo "<li><strong>DB_NAME:</strong> " . DB_NAME . "</li>";
echo "</ul>";

echo "<p><a href='index.php'>Go to Homepage</a></p>";
?>
