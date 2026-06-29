-- ============================================================
--  UNIV E-LEARNING  —  PostgreSQL Schema (Supabase)
--  Run this entire file in the Supabase SQL Editor.
--  Converted from MySQL schema:
--    - AUTO_INCREMENT  →  SERIAL
--    - ENGINE=InnoDB   →  removed
--    - ENUM(...)       →  VARCHAR + CHECK constraint
--    - BOOLEAN         →  BOOLEAN (native in pg)
--    - ON UPDATE CURRENT_TIMESTAMP  →  trigger function
-- ============================================================

-- --------------------------------------------------------
-- Helper: auto-update UpdatedAt via trigger
-- --------------------------------------------------------
CREATE OR REPLACE FUNCTION update_updated_at_column()
RETURNS TRIGGER AS $$
BEGIN
    NEW."UpdatedAt" = NOW();
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;


-- --------------------------------------------------------
-- Table 1: users
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS users (
    "UserID"           SERIAL PRIMARY KEY,
    "Username"         VARCHAR(50)  NOT NULL UNIQUE,
    "Email"            VARCHAR(100) NOT NULL UNIQUE,
    "Password"         VARCHAR(255) NOT NULL,
    "UserType"         VARCHAR(20)  NOT NULL DEFAULT 'Student'
                           CHECK ("UserType" IN ('Admin','Instructor','Student')),
    "Status"           VARCHAR(20)  NOT NULL DEFAULT 'Approved'
                           CHECK ("Status" IN ('Pending','Approved','Rejected')),
    "EmailVerifiedAt"  TIMESTAMP    NULL DEFAULT NULL,
    "CreatedAt"        TIMESTAMP    NOT NULL DEFAULT NOW(),
    "LastActiveAt"     TIMESTAMP    NULL DEFAULT NULL
);

-- --------------------------------------------------------
-- Table 2: password_reset_tokens
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS password_reset_tokens (
    "TokenID"    SERIAL PRIMARY KEY,
    "UserID"     INT NOT NULL REFERENCES users("UserID") ON DELETE CASCADE,
    "TokenHash"  CHAR(64) NOT NULL UNIQUE,
    "ExpiresAt"  TIMESTAMP NOT NULL,
    "UsedAt"     TIMESTAMP NULL DEFAULT NULL,
    "CreatedAt"  TIMESTAMP NOT NULL DEFAULT NOW()
);
CREATE INDEX IF NOT EXISTS idx_password_reset_user    ON password_reset_tokens ("UserID");
CREATE INDEX IF NOT EXISTS idx_password_reset_expires ON password_reset_tokens ("ExpiresAt");

-- --------------------------------------------------------
-- Table 3: email_verification_tokens
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS email_verification_tokens (
    "TokenID"    SERIAL PRIMARY KEY,
    "UserID"     INT NOT NULL REFERENCES users("UserID") ON DELETE CASCADE,
    "TokenHash"  CHAR(64) NOT NULL UNIQUE,
    "ExpiresAt"  TIMESTAMP NOT NULL,
    "UsedAt"     TIMESTAMP NULL DEFAULT NULL,
    "CreatedAt"  TIMESTAMP NOT NULL DEFAULT NOW()
);
CREATE INDEX IF NOT EXISTS idx_email_verification_user    ON email_verification_tokens ("UserID");
CREATE INDEX IF NOT EXISTS idx_email_verification_expires ON email_verification_tokens ("ExpiresAt");

-- --------------------------------------------------------
-- Table 4: courses
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS courses (
    "CourseID"    SERIAL PRIMARY KEY,
    "CourseName"  VARCHAR(100) NOT NULL,
    "Description" TEXT,
    "CreatedAt"   TIMESTAMP NOT NULL DEFAULT NOW()
);

-- --------------------------------------------------------
-- Table 5: enrollments
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS enrollments (
    "EnrollmentID"       SERIAL PRIMARY KEY,
    "UserID"             INT NOT NULL REFERENCES users("UserID") ON DELETE CASCADE,
    "CourseID"           INT NOT NULL REFERENCES courses("CourseID") ON DELETE CASCADE,
    "EnrollmentDate"     TIMESTAMP NOT NULL DEFAULT NOW(),
    "CompletionStatus"   VARCHAR(20) NOT NULL DEFAULT 'In Progress'
                             CHECK ("CompletionStatus" IN ('In Progress','Completed')),
    UNIQUE ("UserID", "CourseID")
);

-- --------------------------------------------------------
-- Table 6: course_contents
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS course_contents (
    "ContentID"    SERIAL PRIMARY KEY,
    "CourseID"     INT NOT NULL REFERENCES courses("CourseID") ON DELETE CASCADE,
    "ContentType"  VARCHAR(20) NOT NULL
                       CHECK ("ContentType" IN ('Video','PDF','Link','Text')),
    "ContentTitle" VARCHAR(255) NOT NULL,
    "ContentURL"   VARCHAR(255),
    "CreatedBy"    INT REFERENCES users("UserID") ON DELETE SET NULL,
    "CreatedAt"    TIMESTAMP NOT NULL DEFAULT NOW(),
    "UpdatedAt"    TIMESTAMP NOT NULL DEFAULT NOW()
);

-- Auto-update UpdatedAt on course_contents
DROP TRIGGER IF EXISTS set_updated_at_course_contents ON course_contents;
CREATE TRIGGER set_updated_at_course_contents
    BEFORE UPDATE ON course_contents
    FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();

-- --------------------------------------------------------
-- Table 7: quizzes
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS quizzes (
    "QuizID"      SERIAL PRIMARY KEY,
    "QuizName"    VARCHAR(100) NOT NULL,
    "CourseID"    INT NOT NULL REFERENCES courses("CourseID") ON DELETE CASCADE,
    "QuizType"    VARCHAR(20) NOT NULL DEFAULT 'Quiz'
                      CHECK ("QuizType" IN ('Quiz','Midterm','Final','Assignment')),
    "Description" TEXT,
    "TotalMarks"  INT DEFAULT 100
);

-- --------------------------------------------------------
-- Table 8: results
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS results (
    "ResultID"    SERIAL PRIMARY KEY,
    "UserID"      INT NOT NULL REFERENCES users("UserID") ON DELETE CASCADE,
    "CourseID"    INT NOT NULL REFERENCES courses("CourseID") ON DELETE CASCADE,
    "QuizID"      INT NOT NULL REFERENCES quizzes("QuizID") ON DELETE CASCADE,
    "Score"       INT NOT NULL,
    "SubmittedAt" TIMESTAMP NOT NULL DEFAULT NOW()
);

-- --------------------------------------------------------
-- Table 9: questions
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS questions (
    "QuestionID"   SERIAL PRIMARY KEY,
    "QuizID"       INT NOT NULL REFERENCES quizzes("QuizID") ON DELETE CASCADE,
    "QuestionText" TEXT NOT NULL,
    "QuestionType" VARCHAR(30) NOT NULL DEFAULT 'Multiple Choice'
                       CHECK ("QuestionType" IN ('Multiple Choice','True/False','Short Answer')),
    "Marks"        INT DEFAULT 1,
    "CreatedAt"    TIMESTAMP NOT NULL DEFAULT NOW()
);

-- --------------------------------------------------------
-- Table 10: question_options
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS question_options (
    "OptionID"   SERIAL PRIMARY KEY,
    "QuestionID" INT NOT NULL REFERENCES questions("QuestionID") ON DELETE CASCADE,
    "OptionText" TEXT NOT NULL,
    "IsCorrect"  BOOLEAN NOT NULL DEFAULT FALSE
);

-- --------------------------------------------------------
-- Table 11: user_answers
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS user_answers (
    "AnswerID"         SERIAL PRIMARY KEY,
    "UserID"           INT NOT NULL REFERENCES users("UserID") ON DELETE CASCADE,
    "QuestionID"       INT NOT NULL REFERENCES questions("QuestionID") ON DELETE CASCADE,
    "SelectedOptionID" INT REFERENCES question_options("OptionID") ON DELETE SET NULL,
    "AnswerText"       TEXT,
    "IsCorrect"        BOOLEAN,
    "SubmittedAt"      TIMESTAMP NOT NULL DEFAULT NOW(),
    UNIQUE ("UserID", "QuestionID")
);

-- --------------------------------------------------------
-- Table 12: quiz_attempts
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS quiz_attempts (
    "AttemptID"   SERIAL PRIMARY KEY,
    "UserID"      INT NOT NULL REFERENCES users("UserID") ON DELETE CASCADE,
    "QuizID"      INT NOT NULL REFERENCES quizzes("QuizID") ON DELETE CASCADE,
    "StartedAt"   TIMESTAMP NOT NULL DEFAULT NOW(),
    "SubmittedAt" TIMESTAMP NULL DEFAULT NULL,
    "Score"       INT,
    "Status"      VARCHAR(20) NOT NULL DEFAULT 'In Progress'
                      CHECK ("Status" IN ('In Progress','Submitted','Graded'))
);

-- --------------------------------------------------------
-- Table 13: instructor_courses
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS instructor_courses (
    "InstructorCourseID" SERIAL PRIMARY KEY,
    "InstructorID"       INT NOT NULL REFERENCES users("UserID") ON DELETE CASCADE,
    "CourseID"           INT NOT NULL REFERENCES courses("CourseID") ON DELETE CASCADE,
    "AssignedAt"         TIMESTAMP NOT NULL DEFAULT NOW(),
    UNIQUE ("InstructorID", "CourseID")
);

-- --------------------------------------------------------
-- Table 14: course_progress
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS course_progress (
    "ProgressID"        SERIAL PRIMARY KEY,
    "UserID"            INT NOT NULL REFERENCES users("UserID") ON DELETE CASCADE,
    "CourseID"          INT NOT NULL REFERENCES courses("CourseID") ON DELETE CASCADE,
    "CompletedLessons"  INT DEFAULT 0,
    "TotalLessons"      INT DEFAULT 0,
    "LastAccessedAt"    TIMESTAMP NOT NULL DEFAULT NOW()
);

-- --------------------------------------------------------
-- Table 15: messages (for in-app chat)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS messages (
    "MessageID"  SERIAL PRIMARY KEY,
    "SenderID"   INT NOT NULL REFERENCES users("UserID") ON DELETE CASCADE,
    "ReceiverID" INT NOT NULL REFERENCES users("UserID") ON DELETE CASCADE,
    "Content"    TEXT NOT NULL,
    "SentAt"     TIMESTAMP NOT NULL DEFAULT NOW(),
    "IsRead"     BOOLEAN NOT NULL DEFAULT FALSE
);


-- ============================================================
--  SEED DATA — Schema + Admin User Only
-- ============================================================

-- Admin user  (password: admin123)
-- Hash: password_hash('admin123', PASSWORD_DEFAULT) from PHP
INSERT INTO users ("Username","Email","Password","UserType","Status","EmailVerifiedAt","CreatedAt")
VALUES (
    'admin',
    'admin@univ.edu',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'Admin',
    'Approved',
    NOW(),
    NOW()
)
ON CONFLICT ("Username") DO NOTHING;
