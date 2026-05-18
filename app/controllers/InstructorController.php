<?php
require_once __DIR__ . '/../models/Course.php';

class InstructorController {

    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Instructor Dashboard
    public function dashboard() {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'Instructor') {
            header("Location: index.php?page=login");
            exit;
        }

        $instructor_id = $_SESSION['user_id'];

        // Get courses assigned by admins to this instructor
        $stmt = $this->pdo->prepare("SELECT c.*, COUNT(DISTINCT e.EnrollmentID) as StudentCount, COUNT(DISTINCT q.QuizID) as QuizCount 
                                     FROM courses c 
                                     JOIN instructor_courses ic ON c.CourseID = ic.CourseID
                                     LEFT JOIN enrollments e ON c.CourseID = e.CourseID 
                                     LEFT JOIN quizzes q ON c.CourseID = q.CourseID 
                                     WHERE ic.InstructorID = ?
                                     GROUP BY c.CourseID");
        $stmt->execute([$instructor_id]);
        $courses = $stmt->fetchAll();

        $stats = [
            'total_courses' => count($courses),
            'total_students' => $this->getTotalStudents($instructor_id),
            'total_quizzes' => $this->getTotalQuizzes($instructor_id),
            'midterms' => $this->countQuizzesByType($instructor_id, 'Midterm'),
            'finals' => $this->countQuizzesByType($instructor_id, 'Final'),
            'assignments' => $this->countQuizzesByType($instructor_id, 'Assignment'),
            'standard_quizzes' => $this->countQuizzesByType($instructor_id, 'Quiz'),
        ];

        require __DIR__ . '/../views/instructor/dashboard.php';
    }

    // Create new course
    public function createCourse() {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'Instructor') {
            header("Location: index.php?page=login");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $course_name = $_POST['course_name'] ?? '';
            $description = $_POST['description'] ?? '';
            if (!empty($course_name)) {
                $stmt = $this->pdo->prepare("INSERT INTO courses (CourseName, Description) VALUES (?, ?)");
                $stmt->execute([$course_name, $description]);

                $course_id = $this->pdo->lastInsertId();

                // Assign course to instructor
                $stmt = $this->pdo->prepare("INSERT INTO instructor_courses (InstructorID, CourseID) VALUES (?, ?)");
                $stmt->execute([$_SESSION['user_id'], $course_id]);

                header("Location: index.php?page=manage-courses");
                exit;
            }
        }

        require __DIR__ . '/../views/instructor/create_course.php';
    }

    // Manage courses
    public function manageCourses() {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'Instructor') {
            header("Location: index.php?page=login");
            exit;
        }

        $instructor_id = $_SESSION['user_id'];

        $stmt = $this->pdo->prepare("SELECT c.*, COUNT(DISTINCT e.EnrollmentID) as StudentCount, COUNT(DISTINCT q.QuizID) as QuizCount 
                                     FROM courses c 
                                     JOIN instructor_courses ic ON c.CourseID = ic.CourseID
                                     LEFT JOIN enrollments e ON c.CourseID = e.CourseID 
                                     LEFT JOIN quizzes q ON c.CourseID = q.CourseID 
                                     WHERE ic.InstructorID = ? 
                                     GROUP BY c.CourseID");
        $stmt->execute([$instructor_id]);
        $courses = $stmt->fetchAll();

        require __DIR__ . '/../views/instructor/manage_courses.php';
    }

    // Edit course
    public function editCourse() {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'Instructor') {
            header("Location: index.php?page=login");
            exit;
        }

        $course_id = $_GET['id'] ?? null;
        $instructor_id = $_SESSION['user_id'];

        // Verify course belongs to instructor
        $stmt = $this->pdo->prepare("SELECT c.* FROM courses c 
                                     JOIN instructor_courses ic ON c.CourseID = ic.CourseID 
                                     WHERE c.CourseID = ? AND ic.InstructorID = ?");
        $stmt->execute([$course_id, $instructor_id]);
        $course = $stmt->fetch();

        if (!$course) {
            header("Location: index.php?page=manage-courses");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $course_name = $_POST['course_name'] ?? '';
            $description = $_POST['description'] ?? '';
            $stmt = $this->pdo->prepare("UPDATE courses SET CourseName = ?, Description = ? WHERE CourseID = ?");
            $stmt->execute([$course_name, $description, $course_id]);

            header("Location: index.php?page=manage-courses");
            exit;
        }

        require __DIR__ . '/../views/instructor/edit_course.php';
    }

    // Delete course
    public function deleteCourse() {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'Instructor') {
            header("Location: index.php?page=login");
            exit;
        }

        $course_id = $_POST['course_id'] ?? null;
        $instructor_id = $_SESSION['user_id'];

        // Verify course belongs to instructor
        $stmt = $this->pdo->prepare("SELECT * FROM instructor_courses WHERE CourseID = ? AND InstructorID = ?");
        $stmt->execute([$course_id, $instructor_id]);

        if ($stmt->fetch()) {
            $stmt = $this->pdo->prepare("DELETE FROM courses WHERE CourseID = ?");
            $stmt->execute([$course_id]);
        }

        header("Location: index.php?page=manage-courses");
        exit;
    }

    // Create quiz
    public function createQuiz() {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'Instructor') {
            header("Location: index.php?page=login");
            exit;
        }

        $instructor_id = $_SESSION['user_id'];

        // Get only courses belonging to this instructor
        $stmt = $this->pdo->prepare("SELECT c.* FROM courses c 
                                     JOIN instructor_courses ic ON c.CourseID = ic.CourseID
                                     WHERE ic.InstructorID = ?
                                     ORDER BY c.CourseName");
        $stmt->execute([$instructor_id]);
        $courses = $stmt->fetchAll();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $quiz_name = $_POST['quiz_name'] ?? '';
            $course_id = $_POST['course_id'] ?? null;
            $quiz_type = $_POST['quiz_type'] ?? 'Quiz';
            $description = $_POST['description'] ?? '';
            $total_marks = $_POST['total_marks'] ?? 100;

            if (!in_array($quiz_type, ['Quiz', 'Midterm', 'Final', 'Assignment'], true)) {
                $quiz_type = 'Quiz';
            }

            $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM instructor_courses WHERE InstructorID = ? AND CourseID = ?");
            $stmt->execute([$instructor_id, $course_id]);
            if (!$stmt->fetchColumn()) {
                header("Location: index.php?page=dashboard");
                exit;
            }

            $stmt = $this->pdo->prepare("INSERT INTO quizzes (QuizName, CourseID, QuizType, Description, TotalMarks) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$quiz_name, $course_id, $quiz_type, $description, $total_marks]);

            $quiz_id = $this->pdo->lastInsertId();

            header("Location: index.php?page=manage-quiz&id=$quiz_id");
            exit;
        }

        require __DIR__ . '/../views/instructor/create_quiz.php';
    }

    // Manage quiz questions
    public function manageQuiz() {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'Instructor') {
            header("Location: index.php?page=login");
            exit;
        }

        $quiz_id = $_GET['id'] ?? null;
        $instructor_id = $_SESSION['user_id'];

        // Verify quiz belongs to instructor's course
        $stmt = $this->pdo->prepare("SELECT q.* FROM quizzes q 
                                     JOIN courses c ON q.CourseID = c.CourseID 
                                     JOIN instructor_courses ic ON c.CourseID = ic.CourseID 
                                     WHERE q.QuizID = ? AND ic.InstructorID = ?");
        $stmt->execute([$quiz_id, $instructor_id]);
        $quiz = $stmt->fetch();

        if (!$quiz) {
            header("Location: index.php?page=dashboard");
            exit;
        }

        // Get questions
        $stmt = $this->pdo->prepare("SELECT * FROM questions WHERE QuizID = ? ORDER BY QuestionID");
        $stmt->execute([$quiz_id]);
        $questions = $stmt->fetchAll();

        // Get options for each question
        foreach ($questions as $key => $question) {
            $stmt = $this->pdo->prepare("SELECT * FROM question_options WHERE QuestionID = ?");
            $stmt->execute([$question['QuestionID']]);
            $questions[$key]['options'] = $stmt->fetchAll();
        }

        require __DIR__ . '/../views/instructor/manage_quiz.php';
    }

    // List quizzes for a specific course
    public function quizzesByCourse() {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'Instructor') {
            header("Location: index.php?page=login");
            exit;
        }

        $course_id = $_GET['course_id'] ?? null;
        $instructor_id = $_SESSION['user_id'];

        // Get course details only if assigned to this instructor
        $stmt = $this->pdo->prepare("SELECT c.* FROM courses c
                                     JOIN instructor_courses ic ON c.CourseID = ic.CourseID
                                     WHERE c.CourseID = ? AND ic.InstructorID = ?");
        $stmt->execute([$course_id, $instructor_id]);
        $course = $stmt->fetch();

        if (!$course) {
            header("Location: index.php?page=dashboard");
            exit;
        }

        // Get quizzes
        $stmt = $this->pdo->prepare("SELECT * FROM quizzes WHERE CourseID = ? ORDER BY QuizID");
        $stmt->execute([$course_id]);
        $quizzes = $stmt->fetchAll();

        require __DIR__ . '/../views/instructor/course_quizzes.php';
    }

    // Add question to quiz
    public function addQuestion() {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'Instructor') {
            header("Location: index.php?page=login");
            exit;
        }

        $quiz_id = $_POST['quiz_id'] ?? null;
        $question_text = $_POST['question_text'] ?? '';
        $question_type = $_POST['question_type'] ?? 'Multiple Choice';
        $marks = $_POST['marks'] ?? 1;

        if (!empty($question_text)) {
            $stmt = $this->pdo->prepare("INSERT INTO questions (QuizID, QuestionText, QuestionType, Marks) VALUES (?, ?, ?, ?)");
            $stmt->execute([$quiz_id, $question_text, $question_type, $marks]);

            $question_id = $this->pdo->lastInsertId();

            // Add options if multiple choice
            if ($question_type === 'Multiple Choice' && isset($_POST['options'])) {
                foreach ($_POST['options'] as $index => $option_text) {
                    if (!empty($option_text)) {
                        $is_correct = isset($_POST['correct_option']) && $_POST['correct_option'] == $index;
                        $stmt = $this->pdo->prepare("INSERT INTO question_options (QuestionID, OptionText, IsCorrect) VALUES (?, ?, ?)");
                        $stmt->execute([$question_id, $option_text, $is_correct]);
                    }
                }
            }
        }

        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit;
    }

    public function courseResults() {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'Instructor') {
            header("Location: index.php?page=login");
            exit;
        }

        $course_id = $_GET['id'] ?? null;
        $instructor_id = $_SESSION['user_id'];

        if ($course_id) {
            // Verify course belongs to instructor
            $stmt = $this->pdo->prepare("SELECT c.* FROM courses c 
                                         JOIN instructor_courses ic ON c.CourseID = ic.CourseID 
                                         WHERE c.CourseID = ? AND ic.InstructorID = ?");
            $stmt->execute([$course_id, $instructor_id]);
            $course = $stmt->fetch();

            if (!$course) {
                header("Location: index.php?page=dashboard");
                exit;
            }

            // Get stats for this course
            $stmt = $this->pdo->prepare("SELECT q.QuizType, COUNT(*) as count FROM results r 
                                         JOIN quizzes q ON r.QuizID = q.QuizID 
                                         WHERE r.CourseID = ? 
                                         GROUP BY q.QuizType");
            $stmt->execute([$course_id]);
            $stats = ['Quiz' => 0, 'Midterm' => 0, 'Final' => 0, 'Assignment' => 0];
            foreach ($stmt->fetchAll() as $row) {
                if (isset($stats[$row['QuizType']])) {
                    $stats[$row['QuizType']] = $row['count'];
                }
            }

            // Aggregate results by student for this course
            $stmt = $this->pdo->prepare("SELECT u.UserID, u.Username, c.CourseID, c.CourseName,
                                         SUM(CASE WHEN q.QuizType = 'Quiz' THEN r.Score ELSE 0 END) AS QuizScore,
                                         SUM(CASE WHEN q.QuizType = 'Quiz' THEN q.TotalMarks ELSE 0 END) AS QuizTotal,
                                         SUM(CASE WHEN q.QuizType = 'Midterm' THEN r.Score ELSE 0 END) AS MidtermScore,
                                         SUM(CASE WHEN q.QuizType = 'Midterm' THEN q.TotalMarks ELSE 0 END) AS MidtermTotal,
                                         SUM(CASE WHEN q.QuizType = 'Final' THEN r.Score ELSE 0 END) AS FinalScore,
                                         SUM(CASE WHEN q.QuizType = 'Final' THEN q.TotalMarks ELSE 0 END) AS FinalTotal,
                                         SUM(CASE WHEN q.QuizType = 'Assignment' THEN r.Score ELSE 0 END) AS AssignmentScore,
                                         SUM(CASE WHEN q.QuizType = 'Assignment' THEN q.TotalMarks ELSE 0 END) AS AssignmentTotal
                                         FROM results r
                                         JOIN users u ON r.UserID = u.UserID
                                         JOIN quizzes q ON r.QuizID = q.QuizID
                                         JOIN courses c ON r.CourseID = c.CourseID
                                         WHERE r.CourseID = ?
                                         GROUP BY u.UserID, u.Username, c.CourseID, c.CourseName
                                         ORDER BY u.Username");
            $stmt->execute([$course_id]);
            $results = $stmt->fetchAll();
            $page_title = "Results for " . $course['CourseName'];
        } else {
            // Get stats for all instructor courses
            $stmt = $this->pdo->prepare("SELECT q.QuizType, COUNT(*) as count FROM results r 
                                         JOIN quizzes q ON r.QuizID = q.QuizID 
                                         JOIN courses c ON r.CourseID = c.CourseID
                                         JOIN instructor_courses ic ON c.CourseID = ic.CourseID
                                         WHERE ic.InstructorID = ? 
                                         GROUP BY q.QuizType");
            $stmt->execute([$instructor_id]);
            $stats = ['Quiz' => 0, 'Midterm' => 0, 'Final' => 0, 'Assignment' => 0];
            foreach ($stmt->fetchAll() as $row) {
                if (isset($stats[$row['QuizType']])) {
                    $stats[$row['QuizType']] = $row['count'];
                }
            }

            // Aggregate results by student across all instructor courses
            $stmt = $this->pdo->prepare("SELECT u.UserID, u.Username, c.CourseID, c.CourseName,
                                         SUM(CASE WHEN q.QuizType = 'Quiz' THEN r.Score ELSE 0 END) AS QuizScore,
                                         SUM(CASE WHEN q.QuizType = 'Quiz' THEN q.TotalMarks ELSE 0 END) AS QuizTotal,
                                         SUM(CASE WHEN q.QuizType = 'Midterm' THEN r.Score ELSE 0 END) AS MidtermScore,
                                         SUM(CASE WHEN q.QuizType = 'Midterm' THEN q.TotalMarks ELSE 0 END) AS MidtermTotal,
                                         SUM(CASE WHEN q.QuizType = 'Final' THEN r.Score ELSE 0 END) AS FinalScore,
                                         SUM(CASE WHEN q.QuizType = 'Final' THEN q.TotalMarks ELSE 0 END) AS FinalTotal,
                                         SUM(CASE WHEN q.QuizType = 'Assignment' THEN r.Score ELSE 0 END) AS AssignmentScore,
                                         SUM(CASE WHEN q.QuizType = 'Assignment' THEN q.TotalMarks ELSE 0 END) AS AssignmentTotal
                                         FROM results r
                                         JOIN users u ON r.UserID = u.UserID
                                         JOIN quizzes q ON r.QuizID = q.QuizID
                                         JOIN courses c ON r.CourseID = c.CourseID
                                         JOIN instructor_courses ic ON c.CourseID = ic.CourseID
                                         WHERE ic.InstructorID = ?
                                         GROUP BY u.UserID, u.Username, c.CourseID, c.CourseName
                                         ORDER BY u.Username, c.CourseName");
            $stmt->execute([$instructor_id]);
            $results = $stmt->fetchAll();
            $page_title = "All Course Performance";
        }

        require __DIR__ . '/../views/instructor/course_results.php';
    }

    // Upload content
    public function uploadContent() {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'Instructor') {
            header("Location: index.php?page=login");
            exit;
        }

        $instructor_id = $_SESSION['user_id'];

        // Load only courses belonging to this instructor for the upload dropdown
        $stmt = $this->pdo->prepare("SELECT c.* FROM courses c 
                                     JOIN instructor_courses ic ON c.CourseID = ic.CourseID
                                     WHERE ic.InstructorID = ?
                                     ORDER BY c.CourseName");
        $stmt->execute([$instructor_id]);
        $courses = $stmt->fetchAll();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $course_id = $_POST['course_id'] ?? null;
            $title = $_POST['content_title'] ?? '';
            $type = $_POST['content_type'] ?? 'Text';
            $url = $_POST['content_url'] ?? '';

            if ($course_id && !empty($title)) {
                $stmt = $this->pdo->prepare("INSERT INTO course_contents (CourseID, ContentType, ContentTitle, ContentURL, CreatedBy) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$course_id, $type, $title, $url, $instructor_id]);

                header("Location: index.php?page=manage-content");
                exit;
            }
        }

        require __DIR__ . '/../views/instructor/upload_content.php';
    }

    // Manage course content
    public function manageContent() {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'Instructor') {
            header("Location: index.php?page=login");
            exit;
        }

        $instructor_id = $_SESSION['user_id'];

        // Get all content for courses belonging to this instructor
        $stmt = $this->pdo->prepare("SELECT cc.*, c.CourseName 
                                     FROM course_contents cc
                                     JOIN courses c ON cc.CourseID = c.CourseID
                                     JOIN instructor_courses ic ON c.CourseID = ic.CourseID
                                     WHERE ic.InstructorID = ?
                                     ORDER BY cc.CreatedAt DESC");
        $stmt->execute([$instructor_id]);
        $contents = $stmt->fetchAll();

        require __DIR__ . '/../views/instructor/manage_content.php';
    }

    // Edit content
    public function editContent() {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'Instructor') {
            header("Location: index.php?page=login");
            exit;
        }

        $instructor_id = $_SESSION['user_id'];
        $content_id = $_GET['id'] ?? ($_POST['content_id'] ?? null);

        // Verify content belongs to instructor's course
        $stmt = $this->pdo->prepare("SELECT cc.* FROM course_contents cc
                                     JOIN courses c ON cc.CourseID = c.CourseID
                                     JOIN instructor_courses ic ON c.CourseID = ic.CourseID
                                     WHERE cc.ContentID = ? AND ic.InstructorID = ?");
        $stmt->execute([$content_id, $instructor_id]);
        $content = $stmt->fetch();

        if (!$content) {
            header("Location: index.php?page=manage-content");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = $_POST['content_title'] ?? '';
            $type = $_POST['content_type'] ?? 'Text';
            $url = $_POST['content_url'] ?? '';

            if (!empty($title)) {
                $stmt = $this->pdo->prepare("UPDATE course_contents SET ContentTitle = ?, ContentType = ?, ContentURL = ? WHERE ContentID = ?");
                $stmt->execute([$title, $type, $url, $content_id]);

                header("Location: index.php?page=manage-content");
                exit;
            }
        }

        require __DIR__ . '/../views/instructor/edit_content.php';
    }

    // Delete content
    public function deleteContent() {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'Instructor') {
            header("Location: index.php?page=login");
            exit;
        }

        $instructor_id = $_SESSION['user_id'];
        $content_id = $_POST['content_id'] ?? null;

        if ($content_id) {
            // Verify content belongs to instructor's course
            $stmt = $this->pdo->prepare("SELECT cc.* FROM course_contents cc
                                         JOIN courses c ON cc.CourseID = c.CourseID
                                         JOIN instructor_courses ic ON c.CourseID = ic.CourseID
                                         WHERE cc.ContentID = ? AND ic.InstructorID = ?");
            $stmt->execute([$content_id, $instructor_id]);

            if ($stmt->fetch()) {
                $stmt = $this->pdo->prepare("DELETE FROM course_contents WHERE ContentID = ?");
                $stmt->execute([$content_id]);
            }
        }

        header("Location: index.php?page=manage-content");
        exit;
    }

    // Helper methods
    private function getTotalStudents($instructor_id) {
        $stmt = $this->pdo->prepare("SELECT COUNT(DISTINCT e.UserID) FROM enrollments e 
                                     JOIN courses c ON e.CourseID = c.CourseID 
                                     JOIN instructor_courses ic ON c.CourseID = ic.CourseID 
                                     WHERE ic.InstructorID = ?");
        $stmt->execute([$instructor_id]);
        return $stmt->fetchColumn();
    }

    private function getTotalQuizzes($instructor_id) {
        $stmt = $this->pdo->prepare("SELECT COUNT(q.QuizID) FROM quizzes q 
                                     JOIN courses c ON q.CourseID = c.CourseID 
                                     JOIN instructor_courses ic ON c.CourseID = ic.CourseID 
                                     WHERE ic.InstructorID = ?");
        $stmt->execute([$instructor_id]);
        return $stmt->fetchColumn();
    }

    private function countQuizzesByType($instructor_id, $type) {
        $stmt = $this->pdo->prepare("SELECT COUNT(q.QuizID) FROM quizzes q 
                                     JOIN courses c ON q.CourseID = c.CourseID 
                                     JOIN instructor_courses ic ON c.CourseID = ic.CourseID 
                                     WHERE ic.InstructorID = ? AND q.QuizType = ?");
        $stmt->execute([$instructor_id, $type]);
        return $stmt->fetchColumn();
    }
}
