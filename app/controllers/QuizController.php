<?php
require_once __DIR__ . '/../models/Quiz.php';

class QuizController {

    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function takeQuiz() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?page=login");
            exit;
        }

        $user_id = $_SESSION['user_id'];
        $quiz_id = $_GET['id'] ?? null;

        // Get quiz details
        $stmt = $this->pdo->prepare("SELECT q.* FROM quizzes q WHERE q.QuizID = ?");
        $stmt->execute([$quiz_id]);
        $quiz = $stmt->fetch();

        if (!$quiz) {
            header("Location: index.php?page=courses");
            exit;
        }

        // Check if user is enrolled in the course
        $stmt = $this->pdo->prepare("SELECT * FROM enrollments WHERE UserID = ? AND CourseID = ?");
        $stmt->execute([$user_id, $quiz['CourseID']]);
        if (!$stmt->fetch()) {
            header("Location: index.php?page=courses");
            exit;
        }

        // Handle quiz submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_quiz'])) {
            // Process all answers
            foreach ($_POST as $key => $value) {
                if (strpos($key, 'answer_') === 0 && !empty($value)) {
                    $question_id = substr($key, 7); // Remove 'answer_' prefix
                    $option_id = $value;

                    // Get the option to check if correct
                    $stmt = $this->pdo->prepare("SELECT IsCorrect FROM question_options WHERE OptionID = ?");
                    $stmt->execute([$option_id]);
                    $option = $stmt->fetch();

                    if ($option) {
                        // Save or update answer
                        $stmt = $this->pdo->prepare("INSERT INTO user_answers (UserID, QuestionID, SelectedOptionID, IsCorrect) 
                                                     VALUES (?, ?, ?, ?) 
                                                     ON DUPLICATE KEY UPDATE SelectedOptionID = ?, IsCorrect = ?");
                        $stmt->execute([
                            $user_id, $question_id, $option_id, $option['IsCorrect'],
                            $option_id, $option['IsCorrect']
                        ]);
                    }
                }
            }

            // Calculate score and submit
            $this->submitQuiz($user_id, $quiz_id);
            header("Location: index.php?page=my-results");
            exit;
        }

        // Get questions
        $stmt = $this->pdo->prepare("SELECT * FROM questions WHERE QuizID = ? ORDER BY QuestionID");
        $stmt->execute([$quiz_id]);
        $questions = $stmt->fetchAll();

        // Get options for each question and user's previous answers
        foreach ($questions as $key => $question) {
            $stmt = $this->pdo->prepare("SELECT * FROM question_options WHERE QuestionID = ?");
            $stmt->execute([$question['QuestionID']]);
            $questions[$key]['options'] = $stmt->fetchAll();

            $stmt = $this->pdo->prepare("SELECT * FROM user_answers WHERE UserID = ? AND QuestionID = ?");
            $stmt->execute([$user_id, $question['QuestionID']]);
            $questions[$key]['user_answer'] = $stmt->fetch();
        }

        require __DIR__ . '/../views/student/take_quiz.php';
    }

    private function submitQuiz($user_id, $quiz_id) {
        // Calculate score
        $stmt = $this->pdo->prepare("SELECT SUM(q.Marks) as total_marks FROM questions q WHERE q.QuizID = ?");
        $stmt->execute([$quiz_id]);
        $result = $stmt->fetch();
        $total_marks = ($result && isset($result['total_marks'])) ? $result['total_marks'] : 0;

        // Count correct answers
        $stmt = $this->pdo->prepare("SELECT SUM(q.Marks) as score FROM user_answers ua 
                                     JOIN questions q ON ua.QuestionID = q.QuestionID 
                                     WHERE ua.UserID = ? AND q.QuizID = ? AND ua.IsCorrect = 1");
        $stmt->execute([$user_id, $quiz_id]);
        $result = $stmt->fetch();
        $score = ($result && isset($result['score'])) ? $result['score'] : 0;

        // Get course ID
        $stmt = $this->pdo->prepare("SELECT CourseID FROM quizzes WHERE QuizID = ?");
        $stmt->execute([$quiz_id]);
        $quiz_data = $stmt->fetch();
        $course_id = $quiz_data ? $quiz_data['CourseID'] : null;

        // Save result
        $stmt = $this->pdo->prepare("INSERT INTO results (UserID, CourseID, QuizID, Score) VALUES (?, ?, ?, ?)");
        $stmt->execute([$user_id, $course_id, $quiz_id, $score]);
    }

    public function myResults() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?page=login");
            exit;
        }

        $user_id = $_SESSION['user_id'];

        $stmt = $this->pdo->prepare("SELECT r.*, c.CourseName, q.QuizName, q.TotalMarks 
                                     FROM results r 
                                     JOIN courses c ON r.CourseID = c.CourseID 
                                     JOIN quizzes q ON r.QuizID = q.QuizID 
                                     WHERE r.UserID = ? 
                                     ORDER BY r.SubmittedAt DESC");
        $stmt->execute([$user_id]);
        $results = $stmt->fetchAll();

        require __DIR__ . '/../views/student/results.php';
    }

    public function quizDetail() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?page=login");
            exit;
        }

        $user_id = $_SESSION['user_id'];
        $result_id = $_GET['id'] ?? null;

        $stmt = $this->pdo->prepare("SELECT r.*, c.CourseName, q.QuizName, q.TotalMarks 
                                     FROM results r 
                                     JOIN courses c ON r.CourseID = c.CourseID 
                                     JOIN quizzes q ON r.QuizID = q.QuizID 
                                     WHERE r.ResultID = ? AND r.UserID = ?");
        $stmt->execute([$result_id, $user_id]);
        $result = $stmt->fetch();

        if (!$result) {
            header("Location: index.php?page=my-results");
            exit;
        }

        // Get questions and user answers
        $stmt = $this->pdo->prepare("SELECT q.*, ua.SelectedOptionID, ua.IsCorrect, 
                                     qo.OptionText as SelectedOption 
                                     FROM questions q 
                                     LEFT JOIN user_answers ua ON q.QuestionID = ua.QuestionID AND ua.UserID = ?
                                     LEFT JOIN question_options qo ON ua.SelectedOptionID = qo.OptionID
                                     WHERE q.QuizID = ? 
                                     ORDER BY q.QuestionID");
        $stmt->execute([$user_id, $result['QuizID']]);
        $questions = $stmt->fetchAll();

        // Get options for each question
        foreach ($questions as $key => $question) {
            $stmt = $this->pdo->prepare("SELECT * FROM question_options WHERE QuestionID = ?");
            $stmt->execute([$question['QuestionID']]);
            $questions[$key]['options'] = $stmt->fetchAll();
        }

        require __DIR__ . '/../views/student/quiz_detail.php';
    }
}
