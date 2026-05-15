<?php
/* -----------------------------
   DATABASE CONFIGURATION
------------------------------*/
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_NAME', getenv('DB_NAME') ?: 'univ_elearning');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');

/* -----------------------------
   APP CONFIGURATION
------------------------------*/
define('APP_NAME', 'Univ E-Learning');
define('BASE_URL', getenv('BASE_URL') ?: 'http://localhost/univ_elearning/');

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
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
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