<?php
require_once __DIR__ . '/../models/User.php';

class AdminController {

    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Admin Dashboard with statistics
    public function dashboard() {
        requireRole('Admin');

        // Get statistics
        $stats = [
            'total_users' => $this->getTotalUsers(),
            'total_instructors' => $this->countUsersByType('Instructor'),
            'pending_instructors' => $this->countUsersByStatus('Instructor', 'Pending'),
            'total_students' => $this->countUsersByType('Student'),
            'total_courses' => $this->getTotalCourses(),
            'total_enrollments' => $this->getTotalEnrollments(),
            'total_quizzes' => $this->getTotalQuizzes(),
        ];

        require __DIR__ . '/../views/admin/dashboard.php';
    }

    // View all users
    public function users() {
        requireRole('Admin');

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

        require __DIR__ . '/../views/admin/users.php';
    }

    // Edit user type
    public function editUser() {
        requireRole('Admin');

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

        require __DIR__ . '/../views/admin/edit_user.php';
    }

    // Delete user
    public function deleteUser() {
        requireRole('Admin');

        $user_id = $_POST['user_id'] ?? null;

        if ($user_id && $user_id != $_SESSION['user_id']) {
            try {
                $this->pdo->beginTransaction();

                $stmt = $this->pdo->prepare("DELETE FROM instructor_courses WHERE InstructorID = ?");
                $stmt->execute([$user_id]);

                $stmt = $this->pdo->prepare("UPDATE course_contents SET CreatedBy = NULL WHERE CreatedBy = ?");
                $stmt->execute([$user_id]);

                $stmt = $this->pdo->prepare("DELETE FROM users WHERE UserID = ?");
                $stmt->execute([$user_id]);

                $this->pdo->commit();
                $_SESSION['success'] = "Account deleted successfully.";
            } catch (Exception $e) {
                if ($this->pdo->inTransaction()) {
                    $this->pdo->rollBack();
                }
                $_SESSION['error'] = "Could not delete that account. Please remove related records and try again.";
            }
        }

        header("Location: index.php?page=admin-users");
        exit;
    }

    // View all courses
    public function courses() {
        requireRole('Admin');

        $stmt = $this->pdo->query("SELECT c.*, 
                                   COUNT(DISTINCT e.EnrollmentID) as EnrollmentCount,
                                   COUNT(DISTINCT q.QuizID) as QuizCount,
                                   GROUP_CONCAT(DISTINCT u.Username ORDER BY u.Username SEPARATOR ', ') as InstructorNames
                                   FROM courses c 
                                   LEFT JOIN enrollments e ON c.CourseID = e.CourseID 
                                   LEFT JOIN quizzes q ON c.CourseID = q.CourseID
                                   LEFT JOIN instructor_courses ic ON c.CourseID = ic.CourseID
                                   LEFT JOIN users u ON ic.InstructorID = u.UserID
                                   GROUP BY c.CourseID 
                                   ORDER BY c.CreatedAt DESC");
        $courses = $stmt->fetchAll();

        require __DIR__ . '/../views/admin/courses.php';
    }

    // Create a course and optionally assign it to an approved instructor
    public function createCourse() {
        requireRole('Admin');

        $instructors = $this->getApprovedInstructors();
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $course_name = trim($_POST['course_name'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $instructor_id = $_POST['instructor_id'] ?? '';

            if ($course_name === '') {
                $errors[] = "Course name is required.";
            }

            if (empty($errors)) {
                $stmt = $this->pdo->prepare("INSERT INTO courses (CourseName, Description) VALUES (?, ?)");
                $stmt->execute([$course_name, $description]);
                $course_id = $this->pdo->lastInsertId();

                if ($instructor_id !== '') {
                    $stmt = $this->pdo->prepare("INSERT IGNORE INTO instructor_courses (InstructorID, CourseID) VALUES (?, ?)");
                    $stmt->execute([$instructor_id, $course_id]);
                }

                header("Location: index.php?page=admin-courses");
                exit;
            }
        }

        require __DIR__ . '/../views/admin/create_course.php';
    }

    // Edit a course and its instructor assignment
    public function editCourse() {
        requireRole('Admin');

        $course_id = $_GET['id'] ?? ($_POST['course_id'] ?? null);
        $instructors = $this->getApprovedInstructors();
        $errors = [];

        $stmt = $this->pdo->prepare("SELECT c.*, ic.InstructorID FROM courses c
                                     LEFT JOIN instructor_courses ic ON c.CourseID = ic.CourseID
                                     WHERE c.CourseID = ?
                                     LIMIT 1");
        $stmt->execute([$course_id]);
        $course = $stmt->fetch();

        if (!$course) {
            header("Location: index.php?page=admin-courses");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $course_name = trim($_POST['course_name'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $instructor_id = $_POST['instructor_id'] ?? '';

            if ($course_name === '') {
                $errors[] = "Course name is required.";
            }

            if (empty($errors)) {
                $stmt = $this->pdo->prepare("UPDATE courses SET CourseName = ?, Description = ? WHERE CourseID = ?");
                $stmt->execute([$course_name, $description, $course_id]);

                $stmt = $this->pdo->prepare("DELETE FROM instructor_courses WHERE CourseID = ?");
                $stmt->execute([$course_id]);

                if ($instructor_id !== '') {
                    $stmt = $this->pdo->prepare("INSERT INTO instructor_courses (InstructorID, CourseID) VALUES (?, ?)");
                    $stmt->execute([$instructor_id, $course_id]);
                }

                header("Location: index.php?page=admin-courses");
                exit;
            }
        }

        require __DIR__ . '/../views/admin/edit_course.php';
    }

    // Delete a course
    public function deleteCourse() {
        requireRole('Admin');

        $course_id = $_POST['course_id'] ?? null;

        if ($course_id) {
            $stmt = $this->pdo->prepare("DELETE FROM courses WHERE CourseID = ?");
            $stmt->execute([$course_id]);
        }

        header("Location: index.php?page=admin-courses");
        exit;
    }

    // View all results
    public function allResults() {
        requireRole('Admin');

        $filter_course = $_GET['course'] ?? '';

        $query = "SELECT r.*, u.Username, c.CourseName, q.QuizName, q.QuizType, q.TotalMarks FROM results r
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

        require __DIR__ . '/../views/admin/all_results.php';
    }

    // Manage instructors (Approval flow)
    public function manageInstructors() {
        requireRole('Admin');

        $status_filter = $_GET['status'] ?? 'Pending';
        
        $query = "SELECT * FROM users WHERE UserType = 'Instructor'";
        $params = [];

        if (!empty($status_filter)) {
            $query .= " AND Status = ?";
            $params[] = $status_filter;
        }

        $query .= " ORDER BY CreatedAt DESC";

        $stmt = $this->pdo->prepare($query);
        $stmt->execute($params);
        $instructors = $stmt->fetchAll();

        require __DIR__ . '/../views/admin/manage_instructors.php';
    }

    // Approve instructor
    public function approveInstructor() {
        requireRole('Admin');

        $user_id = $_GET['id'] ?? null;
        if ($user_id) {
            $userModel = new User($this->pdo);
            $userModel->updateStatus($user_id, 'Approved');
            $_SESSION['success'] = "Instructor approved successfully!";
        }

        header("Location: index.php?page=manage-instructors");
        exit;
    }

    // Reject instructor
    public function rejectInstructor() {
        requireRole('Admin');

        $user_id = $_GET['id'] ?? null;
        if ($user_id) {
            $userModel = new User($this->pdo);
            $userModel->updateStatus($user_id, 'Rejected');
            $_SESSION['error'] = "Instructor registration rejected.";
        }

        header("Location: index.php?page=manage-instructors");
        exit;
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

    private function countUsersByStatus($type, $status) {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM users WHERE UserType = ? AND Status = ?");
        $stmt->execute([$type, $status]);
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

    private function getApprovedInstructors() {
        $stmt = $this->pdo->query("SELECT UserID, Username FROM users 
                                   WHERE UserType = 'Instructor' AND Status = 'Approved' 
                                   ORDER BY Username");
        return $stmt->fetchAll();
    }
}
