<?php
require_once __DIR__ . '/../models/User.php';

class AuthController {

    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';

            // Try to find user by email first, then by username
            $stmt = $this->pdo->prepare("SELECT * FROM users WHERE Email = ? OR Username = ?");
            $stmt->execute([$email, $email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['Password'])) {
                $_SESSION['user_id'] = $user['UserID'];
                $_SESSION['username'] = $user['Username'];
                $_SESSION['user_type'] = $user['UserType'];
                header("Location: index.php?page=dashboard");
                exit;
            } else {
                $error = "Invalid email/username or password";
            }
        }

        require '../app/views/auth/login.php';
    }

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';
            $user_type = $_POST['user_type'] ?? 'Student';

            $errors = [];

            // Validation
            if (empty($username)) {
                $errors[] = "Username is required";
            }
            if (empty($email)) {
                $errors[] = "Email is required";
            }
            if (empty($password)) {
                $errors[] = "Password is required";
            }
            if ($password !== $confirm_password) {
                $errors[] = "Passwords do not match";
            }
            if (strlen($password) < 6) {
                $errors[] = "Password must be at least 6 characters";
            }

            if (empty($errors)) {
                try {
                    // Check if username exists
                    $stmt = $this->pdo->prepare("SELECT * FROM users WHERE Username = ?");
                    $stmt->execute([$username]);
                    if ($stmt->fetch()) {
                        $errors[] = "Username already exists";
                    }

                    // Check if email exists
                    $stmt = $this->pdo->prepare("SELECT * FROM users WHERE Email = ?");
                    $stmt->execute([$email]);
                    if ($stmt->fetch()) {
                        $errors[] = "Email already exists";
                    }

                    if (empty($errors)) {
                        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                        $stmt = $this->pdo->prepare("INSERT INTO users (Username, Email, Password, UserType) VALUES (?, ?, ?, ?)");
                        $stmt->execute([$username, $email, $hashed_password, $user_type]);

                        header("Location: index.php?page=login");
                        exit;
                    }
                } catch (Exception $e) {
                    $errors[] = "Registration failed: " . $e->getMessage();
                }
            }
        }

        require '../app/views/auth/register.php';
    }

    public function logout() {
        session_destroy();
        header("Location: index.php?page=login");
        exit;
    }
}