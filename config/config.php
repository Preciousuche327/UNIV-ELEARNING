<?php
/* -----------------------------
   DATABASE CONFIGURATION
------------------------------*/
$localConfigPath = __DIR__ . '/hosting.local.php';
$localConfig = [];

if (is_file($localConfigPath)) {
    $loadedLocalConfig = require $localConfigPath;

    if (is_array($loadedLocalConfig)) {
        $localConfig = $loadedLocalConfig;
    }
}

$databaseUrl = getenv('DATABASE_URL') ?: getenv('MYSQL_URL') ?: '';
$databaseConfig = [];

if ($databaseUrl) {
    $parsedDatabaseUrl = parse_url($databaseUrl);

    if ($parsedDatabaseUrl !== false) {
        $databaseConfig = [
            'host' => $parsedDatabaseUrl['host'] ?? null,
            'port' => $parsedDatabaseUrl['port'] ?? null,
            'name' => isset($parsedDatabaseUrl['path']) ? ltrim($parsedDatabaseUrl['path'], '/') : null,
            'user' => isset($parsedDatabaseUrl['user']) ? rawurldecode($parsedDatabaseUrl['user']) : null,
            'pass' => isset($parsedDatabaseUrl['pass']) ? rawurldecode($parsedDatabaseUrl['pass']) : null,
        ];
    }
}

define('DB_HOST', $localConfig['db_host'] ?? $databaseConfig['host'] ?? getenv('MYSQLHOST') ?: getenv('DB_HOST') ?: '127.0.0.1');
define('DB_PORT', $localConfig['db_port'] ?? $databaseConfig['port'] ?? getenv('MYSQLPORT') ?: getenv('DB_PORT') ?: '3306');
define('DB_NAME', $localConfig['db_name'] ?? $databaseConfig['name'] ?? getenv('MYSQLDATABASE') ?: getenv('DB_NAME') ?: 'univ_elearning');
define('DB_USER', $localConfig['db_user'] ?? $databaseConfig['user'] ?? getenv('MYSQLUSER') ?: getenv('DB_USER') ?: 'root');
define('DB_PASS', $localConfig['db_pass'] ?? $databaseConfig['pass'] ?? getenv('MYSQLPASSWORD') ?: getenv('DB_PASS') ?: '');

/* -----------------------------
   APP CONFIGURATION
------------------------------*/
define('APP_NAME', 'Univ E-Learning');
define('BASE_URL', $localConfig['base_url'] ?? getenv('BASE_URL') ?: 'http://localhost/univ_elearning/');

$mailFrom = $localConfig['mail_from'] ?? '';
if ($mailFrom === '') $mailFrom = getenv('MAIL_FROM') ?: 'no-reply@univ-elearning.local';

$mailFromName = $localConfig['mail_from_name'] ?? '';
if ($mailFromName === '') $mailFromName = getenv('MAIL_FROM_NAME') ?: APP_NAME;

$smtpHost = $localConfig['smtp_host'] ?? '';
if ($smtpHost === '') $smtpHost = getenv('SMTP_HOST') ?: '';

$smtpPort = $localConfig['smtp_port'] ?? '';
if ($smtpPort === '') $smtpPort = getenv('SMTP_PORT') ?: 587;

$smtpUsername = $localConfig['smtp_username'] ?? '';
if ($smtpUsername === '') $smtpUsername = getenv('SMTP_USERNAME') ?: '';

$smtpPassword = $localConfig['smtp_password'] ?? '';
if ($smtpPassword === '') $smtpPassword = getenv('SMTP_PASSWORD') ?: '';

$smtpSecure = $localConfig['smtp_secure'] ?? '';
if ($smtpSecure === '') $smtpSecure = getenv('SMTP_SECURE') ?: 'tls';

define('MAIL_FROM', $mailFrom);
define('MAIL_FROM_NAME', $mailFromName);
define('SMTP_HOST', $smtpHost);
define('SMTP_PORT', (int) $smtpPort);
define('SMTP_USERNAME', $smtpUsername);
define('SMTP_PASSWORD', $smtpPassword);
define('SMTP_SECURE', strtolower($smtpSecure));

/* -----------------------------
   START SESSION (SAFE)
------------------------------*/
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* -----------------------------
   PDO DATABASE CONNECTION
------------------------------*/
try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS
    );

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Database Connection Failed: " . $e->getMessage());
}

$schemaUpdates = [
    "CREATE TABLE IF NOT EXISTS instructor_courses (
        InstructorCourseID INT AUTO_INCREMENT PRIMARY KEY,
        InstructorID INT NOT NULL,
        CourseID INT NOT NULL,
        AssignedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY unique_instructor_course (InstructorID, CourseID),
        FOREIGN KEY (InstructorID) REFERENCES users(UserID) ON DELETE CASCADE,
        FOREIGN KEY (CourseID) REFERENCES courses(CourseID) ON DELETE CASCADE
    ) ENGINE=InnoDB;",
    "CREATE TABLE IF NOT EXISTS password_reset_tokens (
        TokenID INT AUTO_INCREMENT PRIMARY KEY,
        UserID INT NOT NULL,
        TokenHash CHAR(64) NOT NULL UNIQUE,
        ExpiresAt DATETIME NOT NULL,
        UsedAt DATETIME NULL DEFAULT NULL,
        CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_password_reset_user (UserID),
        INDEX idx_password_reset_expires (ExpiresAt),
        FOREIGN KEY (UserID) REFERENCES users(UserID) ON DELETE CASCADE
    ) ENGINE=InnoDB;",
    "CREATE TABLE IF NOT EXISTS email_verification_tokens (
        TokenID INT AUTO_INCREMENT PRIMARY KEY,
        UserID INT NOT NULL,
        TokenHash CHAR(64) NOT NULL UNIQUE,
        ExpiresAt DATETIME NOT NULL,
        UsedAt DATETIME NULL DEFAULT NULL,
        CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_email_verification_user (UserID),
        INDEX idx_email_verification_expires (ExpiresAt),
        FOREIGN KEY (UserID) REFERENCES users(UserID) ON DELETE CASCADE
    ) ENGINE=InnoDB;",
];

foreach ($schemaUpdates as $schemaUpdate) {
    try {
        $pdo->exec($schemaUpdate);
    } catch (Exception $e) {
        error_log("Schema maintenance warning: " . $e->getMessage());
    }
}

try {
    $stmt = $pdo->query("SHOW COLUMNS FROM users LIKE 'EmailVerifiedAt'");
    if ($stmt->rowCount() === 0) {
        $pdo->exec("ALTER TABLE users ADD COLUMN EmailVerifiedAt DATETIME NULL DEFAULT NULL AFTER Status");
        $pdo->exec("UPDATE users SET EmailVerifiedAt = NOW() WHERE EmailVerifiedAt IS NULL");
    }
} catch (Exception $e) {
    error_log("Email verification column warning: " . $e->getMessage());
}

/* -----------------------------
   HELPER FUNCTIONS
------------------------------*/

function redirect($url) {
    // Allow both relative and absolute URLs
    if (strpos($url, 'http') === 0) {
        header("Location: " . $url);
    } else {
        header("Location: " . BASE_URL . $url);
    }
    exit();
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function hasRole($role) {
    return isset($_SESSION['user_type']) && $_SESSION['user_type'] === $role;
}

function requireLogin() {
    if (!isLoggedIn()) {
        redirect('?page=login');
    }
}

function requireRole($role) {
    requireLogin();

    if (!hasRole($role)) {
        http_response_code(403);
        die("403 Unauthorized Access");
    }
}
