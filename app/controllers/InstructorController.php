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

        // Get instructor's courses
        $stmt = $this->pdo->prepare("SELECT c.*, COUNT(DISTINCT e.EnrollmentID) as StudentCount, COUNT(DISTINCT q.QuizID) as QuizCount 
                                     FROM courses c 
                                     LEFT JOIN instructor_courses ic ON c.CourseID = ic.CourseID 
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
        ];

        require '../app/views/instructor/dashboard.php';
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
            $price = $_POST['price'] ?? 0;

            if (!empty($course_name)) {
                $stmt = $this->pdo->prepare("INSERT INTO courses (CourseName, Description, Price) VALUES (?, ?, ?)");
                $stmt->execute([$course_name, $description, $price]);

                $course_id = $this->pdo->lastInsertId();

                // Assign course to instructor
                $stmt = $this->pdo->prepare("INSERT INTO instructor_courses (InstructorID, CourseID) VALUES (?, ?)");
                $stmt->execute([$_SESSION['user_id'], $course_id]);

                header("Location: index.php?page=manage-courses");
                exit;
            }
        }

        require '../app/views/instructor/create_course.php';
    }

    // Manage courses
    public function manageCourses() {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'Instructor') {
            header("Location: index.php?page=login");
            exit;
        }

        $instructor_id = $_SESSION['user_id'];

        $stmt = $this->pdo->prepare("SELECT c.*, COUNT(e.EnrollmentID) as StudentCount 
                                     FROM courses c 
                                     LEFT JOIN instructor_courses ic ON c.CourseID = ic.CourseID 
                                     LEFT JOIN enrollments e ON c.CourseID = e.CourseID 
                                     WHERE ic.InstructorID = ? 
                                     GROUP BY c.CourseID");
        $stmt->execute([$instructor_id]);
        $courses = $stmt->fetchAll();

        require '../app/views/instructor/manage_courses.php';
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
            $price = $_POST['price'] ?? 0;

            $stmt = $this->pdo->prepare("UPDATE courses SET CourseName = ?, Description = ?, Price = ? WHERE CourseID = ?");
            $stmt->execute([$course_name, $description, $price, $course_id]);

            header("Location: index.php?page=manage-courses");
            exit;
        }

        require '../app/views/instructor/edit_course.php';
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

        // Get instructor's courses
        $stmt = $this->pdo->prepare("SELECT c.* FROM courses c 
                                     JOIN instructor_courses ic ON c.CourseID = ic.CourseID 
                                     WHERE ic.InstructorID = ? 
                                     ORDER BY c.CourseName");
        $stmt->execute([$instructor_id]);
        $courses = $stmt->fetchAll();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $quiz_name = $_POST['quiz_name'] ?? '';
            $course_id = $_POST['course_id'] ?? null;
            $description = $_POST['description'] ?? '';
            $total_marks = $_POST['total_marks'] ?? 100;

            $stmt = $this->pdo->prepare("INSERT INTO quizzes (QuizName, CourseID, Description, TotalMarks) VALUES (?, ?, ?, ?)");
            $stmt->execute([$quiz_name, $course_id, $description, $total_marks]);

            $quiz_id = $this->pdo->lastInsertId();

            header("Location: index.php?page=manage-quiz&id=$quiz_id");
            exit;
        }

        require '../app/views/instructor/create_quiz.php';
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

        require '../app/views/instructor/manage_quiz.php';
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

    // View course results
    public function courseResults() {
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
            header("Location: index.php?page=dashboard");
            exit;
        }

        // Get results for this course
        $stmt = $this->pdo->prepare("SELECT r.*, u.Username, q.QuizName FROM results r 
                                     JOIN users u ON r.UserID = u.UserID 
                                     JOIN quizzes q ON r.QuizID = q.QuizID 
                                     WHERE r.CourseID = ? 
                                     ORDER BY r.SubmittedAt DESC");
        $stmt->execute([$course_id]);
        $results = $stmt->fetchAll();

        require '../app/views/instructor/course_results.php';
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
}
