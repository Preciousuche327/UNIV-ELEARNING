<?php
require_once 'config/config.php';
require_once 'app/controllers/AuthController.php';
require_once 'app/controllers/CourseController.php';
require_once 'app/controllers/QuizController.php';

// Route handling
$page = $_GET['page'] ?? 'dashboard';

// Controller Initialization
$auth = new AuthController($pdo);
$courseCtrl = new CourseController($pdo);
$quizCtrl = new QuizController($pdo);

// Simple Router
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

    case 'dashboard':
        if (!isLoggedIn()) redirect('?page=login');
        
        $user_type = $_SESSION['user_type'];
        if ($user_type === 'Admin') {
            include 'app/views/admin/dashboard.php';
        } elseif ($user_type === 'Instructor') {
            include 'app/views/instructor/dashboard.php';
        } else {
            include 'app/views/student/dashboard.php';
        }
        break;

    case 'courses':
        if (!isLoggedIn()) redirect('?page=login');
        $courseCtrl->index();
        break;

    case 'course-details':
        if (!isLoggedIn()) redirect('?page=login');
        $courseId = $_GET['id'] ?? null;
        if ($courseId) $courseCtrl->view($courseId);
        break;

    case 'enroll':
        if (!isLoggedIn()) redirect('?page=login');
        $courseCtrl->enroll();
        break;

    case 'my-enrollments':
        if (!isLoggedIn()) redirect('?page=login');
        $courseCtrl->myEnrollments();
        break;

    case 'manage-courses':
        if (!isLoggedIn()) redirect('?page=login');
        $courseCtrl->manageCourses();
        break;

    case 'create-course':
        if (!isLoggedIn()) redirect('?page=login');
        $courseCtrl->create();
        break;

    case 'take-quiz':
        if (!isLoggedIn()) redirect('?page=login');
        $quizId = $_GET['id'] ?? null;
        if ($quizId) $quizCtrl->take($quizId);
        break;

    case 'my-results':
        if (!isLoggedIn()) redirect('?page=login');
        $quizCtrl->myResults();
        break;

    case 'all-results':
        if (!isLoggedIn()) redirect('?page=login');
        $quizCtrl->allResults();
        break;

    case 'student-results': // Alias for instructor view
        if (!isLoggedIn()) redirect('?page=login');
        $quizCtrl->allResults();
        break;

    default:
        // Handle 404
        echo "404 Page Not Found";
        break;
}
?>
