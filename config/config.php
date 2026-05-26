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

    // Ensure instructor_courses table exists for instructor support
    $pdo->exec("CREATE TABLE IF NOT EXISTS instructor_courses (
        InstructorCourseID INT AUTO_INCREMENT PRIMARY KEY,
        InstructorID INT NOT NULL,
        CourseID INT NOT NULL,
        AssignedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY unique_instructor_course (InstructorID, CourseID),
        FOREIGN KEY (InstructorID) REFERENCES users(UserID) ON DELETE CASCADE,
        FOREIGN KEY (CourseID) REFERENCES courses(CourseID) ON DELETE CASCADE
    ) ENGINE=InnoDB;");

    // Ensure messages table exists for realtime chat
    $pdo->exec("CREATE TABLE IF NOT EXISTS messages (
        MessageID INT AUTO_INCREMENT PRIMARY KEY,
        SenderID INT NOT NULL,
        ReceiverID INT NOT NULL,
        MessageText TEXT NOT NULL,
        IsRead TINYINT(1) DEFAULT 0,
        SentAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (SenderID) REFERENCES users(UserID) ON DELETE CASCADE,
        FOREIGN KEY (ReceiverID) REFERENCES users(UserID) ON DELETE CASCADE
    ) ENGINE=InnoDB;");

} catch (PDOException $e) {
    die("Database Connection Failed: " . $e->getMessage());
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
