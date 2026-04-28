<?php
require_once 'app/models/Course.php';

class CourseController {
    private $courseModel;

    public function __construct($pdo) {
        $this->courseModel = new Course($pdo);
    }

    public function index() {
        $courses = $this->courseModel->getAllCourses();
        include 'app/views/student/courses.php';
    }

    public function view($courseId) {
        $course = $this->courseModel->getCourseById($courseId);
        $contents = $this->courseModel->getCourseContent($courseId);
        include 'app/views/student/course_details.php';
    }

    public function enroll() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $courseId = $_POST['course_id'];
            $userId = $_SESSION['user_id'];
            if ($this->courseModel->enrollUser($userId, $courseId)) {
                redirect('?page=my-enrollments');
            }
        }
    }

    public function myEnrollments() {
        $userId = $_SESSION['user_id'];
        $enrollments = $this->courseModel->getEnrolledCourses($userId);
        include 'app/views/student/my_enrollments.php';
    }

    // Instructor Methods
    public function manageCourses() {
        if (!hasRole('Instructor') && !hasRole('Admin')) redirect('?page=dashboard');
        $courses = $this->courseModel->getAllCourses(); // In a real app, maybe filter by instructor
        include 'app/views/instructor/manage_courses.php';
    }

    public function create() {
        if (!hasRole('Instructor') && !hasRole('Admin')) redirect('?page=dashboard');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'];
            $description = $_POST['description'];
            $price = $_POST['price'];
            if ($this->courseModel->createCourse($name, $description, $price)) {
                redirect('?page=manage-courses');
            }
        }
        include 'app/views/instructor/create_course.php';
    }
}
?>
