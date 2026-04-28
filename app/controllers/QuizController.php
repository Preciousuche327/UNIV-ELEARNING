<?php
require_once 'app/models/Quiz.php';

class QuizController {
    private $quizModel;

    public function __construct($pdo) {
        $this->quizModel = new Quiz($pdo);
    }

    public function take($quizId) {
        if (!isLoggedIn()) redirect('?page=login');
        
        $quiz = $this->quizModel->getQuizById($quizId);
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $score = rand(60, 100); // For now, simulate score logic
            $userId = $_SESSION['user_id'];
            $courseId = $quiz['CourseID'];
            
            if ($this->quizModel->saveResult($userId, $courseId, $quizId, $score)) {
                redirect('?page=my-results');
            }
        }
        include 'app/views/student/take_quiz.php';
    }

    public function myResults() {
        if (!isLoggedIn()) redirect('?page=login');
        $userId = $_SESSION['user_id'];
        $results = $this->quizModel->getUserResults($userId);
        include 'app/views/student/results.php';
    }

    public function allResults() {
        if (!hasRole('Admin') && !hasRole('Instructor')) redirect('?page=dashboard');
        $results = $this->quizModel->getAllResults();
        include 'app/views/admin/all_results.php';
    }
}
?>
