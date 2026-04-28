<?php

class Quiz {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getQuizzesByCourse($courseId) {
        $stmt = $this->pdo->prepare("SELECT * FROM quizzes WHERE CourseID = ?");
        $stmt->execute([$courseId]);
        return $stmt->fetchAll();
    }

    public function getQuizById($quizId) {
        $stmt = $this->pdo->prepare("SELECT * FROM quizzes WHERE QuizID = ?");
        $stmt->execute([$quizId]);
        return $stmt->fetch();
    }

    public function saveResult($userId, $courseId, $quizId, $score) {
        $stmt = $this->pdo->prepare("INSERT INTO results (UserID, CourseID, QuizID, Score) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$userId, $courseId, $quizId, $score]);
    }

    public function getUserResults($userId) {
        $stmt = $this->pdo->prepare("
            SELECT r.*, q.QuizName, c.CourseName 
            FROM results r 
            JOIN quizzes q ON r.QuizID = q.QuizID 
            JOIN courses c ON r.CourseID = c.CourseID 
            WHERE r.UserID = ?
            ORDER BY r.SubmittedAt DESC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function getAllResults() {
        $stmt = $this->pdo->query("
            SELECT r.*, u.Username, q.QuizName, c.CourseName 
            FROM results r 
            JOIN users u ON r.UserID = u.UserID 
            JOIN quizzes q ON r.QuizID = q.QuizID 
            JOIN courses c ON r.CourseID = c.CourseID 
            ORDER BY r.SubmittedAt DESC
        ");
        return $stmt->fetchAll();
    }
}
?>
