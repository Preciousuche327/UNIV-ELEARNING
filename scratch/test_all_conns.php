<?php
$hosts = ['localhost', '127.0.0.1', '::1', 'localhost:3306', '127.0.0.1:3306'];
$db   = 'univ_elearning';
$user = 'root';
$pass = '';

foreach ($hosts as $host) {
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
        echo "SUCCESS: Connected via $host\n";
    } catch (PDOException $e) {
        echo "FAILURE: $host -> " . $e->getMessage() . "\n";
    }
}
?>
