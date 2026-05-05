<?php
require_once 'config/config.php';

echo "--- Users and Course Ownership ---\n";
$stmt = $pdo->query("SELECT u.UserID, u.Username, u.UserType, COUNT(ic.CourseID) as CourseCount 
                     FROM users u 
                     LEFT JOIN instructor_courses ic ON u.UserID = ic.InstructorID 
                     GROUP BY u.UserID");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "ID: {$row['UserID']} | User: {$row['Username']} | Type: {$row['UserType']} | Courses: {$row['CourseCount']}\n";
}

echo "\n--- All Courses ---\n";
$stmt = $pdo->query("SELECT CourseID, CourseName FROM courses");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "ID: {$row['CourseID']} | Name: {$row['CourseName']}\n";
}
