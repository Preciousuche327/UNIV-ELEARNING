<?php
require_once 'config/config.php';

try {
    // Assign all courses to all instructors just to be safe for testing
    $stmt = $pdo->query("SELECT UserID FROM users WHERE UserType = 'Instructor'");
    $instructors = $stmt->fetchAll(PDO::FETCH_COLUMN);

    $stmt = $pdo->query("SELECT CourseID FROM courses");
    $courses = $stmt->fetchAll(PDO::FETCH_COLUMN);

    foreach ($instructors as $inst_id) {
        foreach ($courses as $course_id) {
            $stmt = $pdo->prepare("INSERT IGNORE INTO instructor_courses (InstructorID, CourseID) VALUES (?, ?)");
            $stmt->execute([$inst_id, $course_id]);
        }
    }
    echo "Successfully assigned all courses to all instructors for testing purposes.\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
