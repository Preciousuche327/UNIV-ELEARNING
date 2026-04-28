CREATE DATABASE IF NOT EXISTS univ_elearning;
USE univ_elearning;

-- Table 1: User
CREATE TABLE IF NOT EXISTS users (
    UserID INT AUTO_INCREMENT PRIMARY KEY,
    Username VARCHAR(50) NOT NULL UNIQUE,
    Email VARCHAR(100) NOT NULL UNIQUE,
    Password VARCHAR(255) NOT NULL,
    UserType ENUM('Admin', 'Instructor', 'Student') NOT NULL DEFAULT 'Student',
    CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Table 2: Course
CREATE TABLE IF NOT EXISTS courses (
    CourseID INT AUTO_INCREMENT PRIMARY KEY,
    CourseName VARCHAR(100) NOT NULL,
    Description TEXT,
    Price DECIMAL(10, 2) DEFAULT 0.00,
    CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Table 3: Enrollment
CREATE TABLE IF NOT EXISTS enrollments (
    EnrollmentID INT AUTO_INCREMENT PRIMARY KEY,
    UserID INT NOT NULL,
    CourseID INT NOT NULL,
    EnrollmentDate TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CompletionStatus ENUM('In Progress', 'Completed') DEFAULT 'In Progress',
    FOREIGN KEY (UserID) REFERENCES users(UserID) ON DELETE CASCADE,
    FOREIGN KEY (CourseID) REFERENCES courses(CourseID) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Table 4: CourseContent
CREATE TABLE IF NOT EXISTS course_contents (
    ContentID INT AUTO_INCREMENT PRIMARY KEY,
    CourseID INT NOT NULL,
    ContentType ENUM('Video', 'PDF', 'Link', 'Text') NOT NULL,
    ContentTitle VARCHAR(255) NOT NULL,
    ContentURL VARCHAR(255),
    FOREIGN KEY (CourseID) REFERENCES courses(CourseID) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Table 5: Quiz
CREATE TABLE IF NOT EXISTS quizzes (
    QuizID INT AUTO_INCREMENT PRIMARY KEY,
    QuizName VARCHAR(100) NOT NULL,
    CourseID INT NOT NULL,
    Description TEXT,
    TotalMarks INT DEFAULT 100,
    FOREIGN KEY (CourseID) REFERENCES courses(CourseID) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Table 6: Result
CREATE TABLE IF NOT EXISTS results (
    ResultID INT AUTO_INCREMENT PRIMARY KEY,
    UserID INT NOT NULL,
    CourseID INT NOT NULL,
    QuizID INT NOT NULL,
    Score INT NOT NULL,
    SubmittedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (UserID) REFERENCES users(UserID) ON DELETE CASCADE,
    FOREIGN KEY (CourseID) REFERENCES courses(CourseID) ON DELETE CASCADE,
    FOREIGN KEY (QuizID) REFERENCES quizzes(QuizID) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Seed Admin User (password: admin123)
-- Hash generated using password_hash('admin123', PASSWORD_DEFAULT)
INSERT IGNORE INTO users (Username, Email, Password, UserType) 
VALUES ('admin', 'admin@univ.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin');
