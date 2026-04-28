<?php
require_once 'app/models/User.php';

class AuthController {
    private $userModel;

    public function __construct($pdo) {
        $this->userModel = new User($pdo);
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';

            $user = $this->userModel->login($email, $password);
            if ($user) {
                $_SESSION['user_id'] = $user['UserID'];
                $_SESSION['username'] = $user['Username'];
                $_SESSION['user_type'] = $user['UserType'];
                
                redirect('?page=dashboard');
            } else {
                return "Invalid email or password.";
            }
        }
        include 'app/views/auth/login.php';
    }

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            $user_type = $_POST['user_type'] ?? 'Student';

            if ($this->userModel->register($username, $email, $password, $user_type)) {
                redirect('?page=login');
            } else {
                return "Registration failed. Registration might already exist.";
            }
        }
        include 'app/views/auth/register.php';
    }

    public function logout() {
        session_destroy();
        redirect('?page=login');
    }
}
?>
