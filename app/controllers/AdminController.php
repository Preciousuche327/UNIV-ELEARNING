<?php
require_once __DIR__ . '/../models/User.php';

class AdminController {

    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Admin Dashboard with statistics
    public function dashboard() {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'Admin') {
            header("Location: index.php?page=login");
            exit;
        }

        // Get statistics
        $stats = [
            'total_users' => $this->getTotalUsers(),
            'total_instructors' => $this->countUsersByType('Instructor'),
            'total_students' => $this->countUsersByType('Student'),
            'total_courses' => $this->getTotalCourses(),
            'total_enrollments' => $this->getTotalEnrollments(),
            'total_quizzes' => $this->getTotalQuizzes(),
        ];

        require '../app/views/admin/dashboard.php';
    }

    // View all users
    public function users() {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'Admin') {
            header("Location: index.php?page=login");
            exit;
        }

        $search = $_GET['search'] ?? '';
        $type = $_GET['type'] ?? '';

        $query = "SELECT * FROM users WHERE 1=1";
        $params = [];

        if (!empty($search)) {
            $query .= " AND (Username LIKE ? OR Email LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }

        if (!empty($type)) {
            $query .= " AND UserType = ?";
            $params[] = $type;
        }

        $query .= " ORDER BY CreatedAt DESC";

        $stmt = $this->pdo->prepare($query);
        $stmt->execute($params);
        $users = $stmt->fetchAll();

        require '../app/views/admin/users.php';
    }

    // Edit user type
    public function editUser() {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'Admin') {
            header("Location: index.php?page=login");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user_id = $_POST['user_id'];
            $user_type = $_POST['user_type'];

            $stmt = $this->pdo->prepare("UPDATE users SET UserType = ? WHERE UserID = ?");
            $stmt->execute([$user_type, $user_id]);

            header("Location: index.php?page=admin-users");
            exit;
        }

        $user_id = $_GET['id'] ?? null;
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE UserID = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch();

        require '../app/views/admin/edit_user.php';
    }

    // Delete user
    public function deleteUser() {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'Admin') {
            header("Location: index.php?page=login");
            exit;
        }

        $user_id = $_POST['user_id'] ?? null;

        if ($user_id && $user_id != $_SESSION['user_id']) {
            $stmt = $this->pdo->prepare("DELETE FROM users WHERE UserID = ?");
            $stmt->execute([$user_id]);
        }

        header("Location: index.php?page=admin-users");
        exit;
    }

    // View all courses
    public function courses() {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'Admin') {
            header("Location: index.php?page=login");
            exit;
        }

        $stmt = $this->pdo->query("SELECT c.*, COUNT(e.EnrollmentID) as EnrollmentCount FROM courses c 
                                   LEFT JOIN enrollments e ON c.CourseID = e.CourseID 
                                   GROUP BY c.CourseID ORDER BY c.CreatedAt DESC");
        $courses = $stmt->fetchAll();

        require '../app/views/admin/courses.php';
    }

    // View all results
    public function allResults() {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'Admin') {
            header("Location: index.php?page=login");
            exit;
        }

        $filter_course = $_GET['course'] ?? '';

        $query = "SELECT r.*, u.Username, c.CourseName, q.QuizName FROM results r 
                  JOIN users u ON r.UserID = u.UserID 
                  JOIN courses c ON r.CourseID = c.CourseID 
                  JOIN quizzes q ON r.QuizID = q.QuizID WHERE 1=1";
        
        $params = [];

        if (!empty($filter_course)) {
            $query .= " AND r.CourseID = ?";
            $params[] = $filter_course;
        }

        $query .= " ORDER BY r.SubmittedAt DESC";

        $stmt = $this->pdo->prepare($query);
        $stmt->execute($params);
        $results = $stmt->fetchAll();

        // Get courses for filter dropdown
        $stmt = $this->pdo->query("SELECT * FROM courses ORDER BY CourseName");
        $courses = $stmt->fetchAll();

        require '../app/views/admin/all_results.php';
    }

    // Helper methods
    private function getTotalUsers() {
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM users");
        return $stmt->fetchColumn();
    }

    private function countUsersByType($type) {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM users WHERE UserType = ?");
        $stmt->execute([$type]);
        return $stmt->fetchColumn();
    }

    private function getTotalCourses() {
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM courses");
        return $stmt->fetchColumn();
    }

    private function getTotalEnrollments() {
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM enrollments");
        return $stmt->fetchColumn();
    }

    private function getTotalQuizzes() {
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM quizzes");
        return $stmt->fetchColumn();
    }
}
