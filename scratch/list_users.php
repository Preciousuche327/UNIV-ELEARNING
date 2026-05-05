<?php
require_once __DIR__ . '/../config/config.php';

$stmt = $pdo->query("SELECT UserID, Username, Email, UserType, Status FROM users");
$users = $stmt->fetchAll();
print_r($users);
