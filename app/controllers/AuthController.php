<?php
require_once __DIR__ . '/../models/User.php';

class AuthController {

    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' && isLoggedIn()) {
            header("Location: index.php?page=dashboard");
            exit;
        }

        $remembered_email = $_COOKIE['remembered_login'] ?? '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $password = trim($_POST['password'] ?? '');

            // Try to find user by email first, then by username
            $stmt = $this->pdo->prepare("SELECT * FROM users WHERE Email = ? OR Username = ?");
            $stmt->execute([$email, $email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['Password'])) {
                if ($user['Status'] === 'Pending') {
                    $error = "Your account is currently waiting for admin approval. Please check back later.";
                } elseif ($user['Status'] === 'Rejected') {
                    $error = "Your account registration has been rejected. Please contact support.";
                } else {
                    $_SESSION['user_id'] = $user['UserID'];
                    $_SESSION['username'] = $user['Username'];
                    $_SESSION['user_type'] = $user['UserType'];

                    if (!empty($_POST['remember_me'])) {
                        setcookie('remembered_login', $email, time() + (30 * 24 * 60 * 60), '', '', false, true);
                    } else {
                        setcookie('remembered_login', '', time() - 3600, '', '', false, true);
                    }

                    header("Location: index.php?page=dashboard");
                    exit;
                }
            } else {
                $error = "Invalid email/username or password";
            }
        }

        require __DIR__ . '/../views/auth/login.php';
    }

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' && isLoggedIn()) {
            header("Location: index.php?page=dashboard");
            exit;
        }

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
            if ($user_type === 'Admin') {
                $errors[] = "Administrator accounts cannot be created via public registration.";
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
                        $status = ($user_type === 'Instructor') ? 'Pending' : 'Approved';

                        $stmt = $this->pdo->prepare("INSERT INTO users (Username, Email, Password, UserType, Status) VALUES (?, ?, ?, ?, ?)");
                        $stmt->execute([$username, $email, $hashed_password, $user_type, $status]);

                        if ($status === 'Pending') {
                            $success_message = "Your account has been created successfully! However, instructor accounts require admin approval. You will be able to log in once your account is approved.";
                            require __DIR__ . '/../views/auth/login.php';
                            exit;
                        }

                        header("Location: index.php?page=login");
                        exit;
                    }
                } catch (Exception $e) {
                    $errors[] = "Registration failed: " . $e->getMessage();
                }
            }
        }

        require __DIR__ . '/../views/auth/register.php';
    }

    public function logout() {
        session_destroy();
        header("Location: index.php?page=login");
        exit;
    }
}
