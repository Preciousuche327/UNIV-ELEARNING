<?php
require_once __DIR__ . '/../models/Course.php';

class CourseController {

    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function dashboard() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?page=login");
            exit;
        }

        $user_id = $_SESSION['user_id'];

        // Get enrollment statistics
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM enrollments WHERE UserID = ?");
        $stmt->execute([$user_id]);
        $enrolled_count = $stmt->fetchColumn();

        // Get enrolled courses
        $stmt = $this->pdo->prepare("SELECT c.*, e.CompletionStatus FROM courses c 
                                     JOIN enrollments e ON c.CourseID = e.CourseID 
                                     WHERE e.UserID = ? 
                                     ORDER BY e.EnrollmentDate DESC");
        $stmt->execute([$user_id]);
        $enrolled_courses = $stmt->fetchAll();

        // Get completed quizzes count from results table
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM results WHERE UserID = ?");
        $stmt->execute([$user_id]);
        $completed_quizzes = $stmt->fetchColumn();

        // Calculate average score from results
        $average_score = 0;
        if ($completed_quizzes > 0) {
            $stmt = $this->pdo->prepare("SELECT AVG(Score) as avg_score FROM results WHERE UserID = ?");
            $stmt->execute([$user_id]);
            $score_data = $stmt->fetch();
            $average_score = ($score_data && isset($score_data['avg_score'])) ? round($score_data['avg_score'], 2) : 0;
        }

        // Get breakdown of completed assessments
        $assessment_stats = [
            'quizzes' => $this->countCompletedByType($user_id, 'Quiz'),
            'midterms' => $this->countCompletedByType($user_id, 'Midterm'),
            'finals' => $this->countCompletedByType($user_id, 'Final'),
            'assignments' => $this->countCompletedByType($user_id, 'Assignment'),
        ];

        // Get recent results
        $stmt = $this->pdo->prepare("SELECT r.*, q.QuizName, q.QuizType, q.TotalMarks 
                                     FROM results r 
                                     JOIN quizzes q ON r.QuizID = q.QuizID 
                                     WHERE r.UserID = ? 
                                     ORDER BY r.SubmittedAt DESC LIMIT 5");
        $stmt->execute([$user_id]);
        $recent_results = $stmt->fetchAll();

        require __DIR__ . '/../views/student/dashboard.php';
    }

    public function courses() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?page=login");
            exit;
        }

        $user_id = $_SESSION['user_id'];

        // Get search and filter
        $search = $_GET['search'] ?? '';
        $sort = $_GET['sort'] ?? 'new';

        $query = "SELECT c.*, 
                  CASE WHEN e.EnrollmentID IS NOT NULL THEN 1 ELSE 0 END as IsEnrolled,
                  COUNT(DISTINCT e2.EnrollmentID) as StudentCount
                  FROM courses c 
                  LEFT JOIN enrollments e ON c.CourseID = e.CourseID AND e.UserID = ?
                  LEFT JOIN enrollments e2 ON c.CourseID = e2.CourseID
                  WHERE 1=1";
        
        $params = [$user_id];

        if (!empty($search)) {
            $query .= " AND (c.CourseName LIKE ? OR c.Description LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }

        $query .= " GROUP BY c.CourseID";

        // Add sorting
        if ($sort === 'new') {
            $query .= " ORDER BY c.CreatedAt DESC";
        } elseif ($sort === 'popular') {
            $query .= " ORDER BY StudentCount DESC";
        }

        $stmt = $this->pdo->prepare($query);
        $stmt->execute($params);
        $courses = $stmt->fetchAll();

        require __DIR__ . '/../views/student/courses.php';
    }

    public function courseDetails() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?page=login");
            exit;
        }

        $user_id = $_SESSION['user_id'];
        $course_id = $_GET['id'] ?? null;

        // Get course
        $stmt = $this->pdo->prepare("SELECT c.*, COUNT(DISTINCT e.EnrollmentID) as StudentCount 
                                     FROM courses c 
                                     LEFT JOIN enrollments e ON c.CourseID = e.CourseID 
                                     WHERE c.CourseID = ? 
                                     GROUP BY c.CourseID");
        $stmt->execute([$course_id]);
        $course = $stmt->fetch();

        if (!$course) {
            header("Location: index.php?page=courses");
            exit;
        }

        // Check if user is enrolled
        $stmt = $this->pdo->prepare("SELECT * FROM enrollments WHERE UserID = ? AND CourseID = ?");
        $stmt->execute([$user_id, $course_id]);
        $is_enrolled = $stmt->fetch() ? true : false;

        // Get course content
        $stmt = $this->pdo->prepare("SELECT * FROM course_contents WHERE CourseID = ? ORDER BY ContentID");
        $stmt->execute([$course_id]);
        $contents = $stmt->fetchAll();

        // Get quizzes
        $stmt = $this->pdo->prepare("SELECT * FROM quizzes WHERE CourseID = ? ORDER BY QuizID");
        $stmt->execute([$course_id]);
        $quizzes = $stmt->fetchAll();

        require __DIR__ . '/../views/student/course_details.php';
    }

    public function myEnrollments() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?page=login");
            exit;
        }

        $user_id = $_SESSION['user_id'];

        $stmt = $this->pdo->prepare("SELECT c.*, e.CompletionStatus, e.EnrollmentDate,
                                     (SELECT COUNT(*) FROM quizzes WHERE CourseID = c.CourseID) as TotalQuizzes,
                                     (SELECT COUNT(*) FROM results r JOIN quizzes q ON r.QuizID = q.QuizID 
                                      WHERE r.UserID = ? AND q.CourseID = c.CourseID) as CompletedQuizzes
                                     FROM courses c 
                                     JOIN enrollments e ON c.CourseID = e.CourseID 
                                     WHERE e.UserID = ? 
                                     ORDER BY e.EnrollmentDate DESC");
        $stmt->execute([$user_id, $user_id]);
        $enrollments = $stmt->fetchAll();

        // Calculate progress for each enrollment
        foreach ($enrollments as &$enrollment) {
            $total_quizzes = $enrollment['TotalQuizzes'];
            $completed_quizzes = $enrollment['CompletedQuizzes'];
            
            if ($total_quizzes > 0) {
                $enrollment['Progress'] = round(($completed_quizzes / $total_quizzes) * 100);
            } else {
                $enrollment['Progress'] = 0;
            }
            
            // If all quizzes completed, mark as completed
            if ($completed_quizzes == $total_quizzes && $total_quizzes > 0) {
                $enrollment['CompletionStatus'] = 'Completed';
                // Update database
                $update_stmt = $this->pdo->prepare("UPDATE enrollments SET CompletionStatus = 'Completed' 
                                                   WHERE UserID = ? AND CourseID = ?");
                $update_stmt->execute([$user_id, $enrollment['CourseID']]);
            }
        }

        require __DIR__ . '/../views/student/my_enrollments.php';
    }

    public function myResults() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?page=login");
            exit;
        }

        $user_id = $_SESSION['user_id'];

        $stmt = $this->pdo->prepare("SELECT r.*, c.CourseName, q.QuizName, q.QuizType, q.TotalMarks 
                                     FROM results r 
                                     JOIN courses c ON r.CourseID = c.CourseID 
                                     JOIN quizzes q ON r.QuizID = q.QuizID 
                                     WHERE r.UserID = ? 
                                     ORDER BY r.SubmittedAt DESC");
        $stmt->execute([$user_id]);
        $results = $stmt->fetchAll();

        require __DIR__ . '/../views/student/results.php';
    }

    public function enroll() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?page=login");
            exit;
        }

        $user_id = $_SESSION['user_id'];
        $course_id = $_POST['course_id'] ?? null;

        if ($course_id) {
            // Check if already enrolled
            $stmt = $this->pdo->prepare("SELECT * FROM enrollments WHERE UserID = ? AND CourseID = ?");
            $stmt->execute([$user_id, $course_id]);

            if (!$stmt->fetch()) {
                $stmt = $this->pdo->prepare("INSERT INTO enrollments (UserID, CourseID) VALUES (?, ?)");
                $stmt->execute([$user_id, $course_id]);
            }
        }

        $redirect = $_POST['redirect'] ?? 'courses';
        header("Location: index.php?page=$redirect&id=$course_id");
    }

    public function drop() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?page=login");
            exit;
        }

        $user_id = $_SESSION['user_id'];
        $course_id = $_POST['course_id'] ?? null;

        if ($course_id) {
            $stmt = $this->pdo->prepare("DELETE FROM enrollments WHERE UserID = ? AND CourseID = ?");
            $stmt->execute([$user_id, $course_id]);
        }

        header("Location: index.php?page=courses");
    }

    private function countCompletedByType($user_id, $type) {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM results r 
                                     JOIN quizzes q ON r.QuizID = q.QuizID 
                                     WHERE r.UserID = ? AND q.QuizType = ?");
        $stmt->execute([$user_id, $type]);
        return $stmt->fetchColumn();
    }
}