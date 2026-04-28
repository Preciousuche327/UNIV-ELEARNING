<?php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'univ_elearning');
define('DB_USER', 'root');
define('DB_PASS', '');

// Application Constants
define('APP_NAME', 'Univ E-Learning');
define('BASE_URL', 'http://localhost/univ-elearning/');

// Establish PDO Connection
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database Connection Failed: " . $e->getMessage());
}

// Start Session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Helper functions for CSRF or global utilities can be included here
function redirect($url) {
    header("Location: " . BASE_URL . $url);
    exit();
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function hasRole($role) {
    return isset($_SESSION['user_type']) && $_SESSION['user_type'] === $role;
}
?>
