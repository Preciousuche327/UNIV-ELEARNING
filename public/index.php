<?php
session_start();

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../app/controllers/AuthController.php';
require_once __DIR__ . '/../app/controllers/CourseController.php';
require_once __DIR__ . '/../app/controllers/QuizController.php';
require_once __DIR__ . '/../app/controllers/AdminController.php';
require_once __DIR__ . '/../app/controllers/InstructorController.php';

$page = $_GET['page'] ?? 'dashboard';

$auth = new AuthController($pdo);
$course = new CourseController($pdo);
$quiz = new QuizController($pdo);
$admin = new AdminController($pdo);
$instructor = new InstructorController($pdo);

switch ($page) {

    case 'login':
        $auth->login();
        break;

    case 'register':
        $auth->register();
        break;

    case 'logout':
        $auth->logout();
        break;

    case 'courses':
        $course->courses();
        break;

    case 'course-details':
        $course->courseDetails();
        break;

    case 'my-enrollments':
        $course->myEnrollments();
        break;

    case 'results':
        $course->myResults();
        break;

    case 'enroll':
        $course->enroll();
        break;

    case 'drop':
        $course->drop();
        break;

    case 'take-quiz':
        $quiz->takeQuiz();
        break;

    case 'quiz-detail':
        $quiz->quizDetail();
        break;

    // Admin routes
    case 'admin-dashboard':
        $admin->dashboard();
        break;

    case 'admin-users':
        $admin->users();
        break;

    case 'admin-edit-user':
        $admin->editUser();
        break;

    case 'admin-delete-user':
        $admin->deleteUser();
        break;

    case 'admin-courses':
        $admin->courses();
        break;

    case 'admin-results':
        $admin->allResults();
        break;

    // Instructor routes
    case 'instructor-dashboard':
        $instructor->dashboard();
        break;

    case 'create-course':
        $instructor->createCourse();
        break;

    case 'manage-courses':
        $instructor->manageCourses();
        break;

    case 'edit-course':
        $instructor->editCourse();
        break;

    case 'delete-course':
        $instructor->deleteCourse();
        break;

    case 'create-quiz':
        $instructor->createQuiz();
        break;

    case 'manage-quiz':
        $instructor->manageQuiz();
        break;

    case 'add-question':
        $instructor->addQuestion();
        break;

    case 'upload-content':
        $instructor->uploadContent();
        break;

    case 'course-results':
        $instructor->courseResults();
        break;

    default:
        // Route based on user type
        if (isset($_SESSION['user_type'])) {
            if ($_SESSION['user_type'] === 'Admin') {
                $admin->dashboard();
            } elseif ($_SESSION['user_type'] === 'Instructor') {
                $instructor->dashboard();
            } else {
                $course->dashboard();
            }
        } else {
            $course->dashboard();
        }
}