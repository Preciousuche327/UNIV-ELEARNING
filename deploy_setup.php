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
