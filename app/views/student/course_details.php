<?php
// app/views/student/course_details.php
include __DIR__ . '/../partials/header.php';
include __DIR__ . '/../partials/sidebar.php';
?>

<div class="container-fluid p-4">
    <div class="mb-4">
        <a href="?page=courses" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Back to Courses</a>
    </div>

    <!-- Hero Section -->
    <div class="hero-banner mb-5 animate__animated animate__fadeIn">
        <div class="d-flex justify-content-between align-items-center position-relative" style="z-index: 1;">
            <div>
                <span class="badge bg-soft-primary px-3 py-2 mb-3" style="font-size: 0.9rem;">Course Profile</span>
                <h1 class="display-4 fw-bold mb-3"><?php echo htmlspecialchars($course['CourseName']); ?></h1>
                <p class="lead mb-0 text-white-50">
                    <i class="bi bi-people me-2"></i> <?php echo $course['StudentCount']; ?> Students Enrolled
                </p>
            </div>
            <div class="text-end d-none d-md-block">
                <img src="https://illustrations.popsy.co/white/surreal-hourglass.svg" alt="Course" style="height: 180px;">
            </div>
        </div>
    </div>

    <div class="row g-5">
        <div class="col-lg-8 animate__animated animate__fadeInUp">
            <div class="card p-4 mb-4">
                <h4 class="fw-bold mb-3 border-bottom pb-2">About This Course</h4>
                <p class="text-muted" style="line-height: 1.8; font-size: 1.1rem;">
                    <?php echo nl2br(htmlspecialchars($course['Description'])); ?>
                </p>
            </div>

            <?php if (!empty($contents)): ?>
                <div class="card p-4 mb-4">
                    <h4 class="fw-bold mb-3 border-bottom pb-2">Course Curriculum</h4>
                    <div class="list-group list-group-flush">
                        <?php foreach ($contents as $content): ?>
                            <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center py-3">
                                <div>
                                    <i class="bi bi-<?php 
                                        echo match($content['ContentType']) {
                                            'Video' => 'play-circle text-primary',
                                            'PDF' => 'file-pdf text-danger',
                                            'Link' => 'link-45deg text-info',
                                            default => 'file-text text-secondary'
                                        };
                                    ?> me-3 fs-5"></i>
                                    <span class="fw-medium"><?php echo htmlspecialchars($content['ContentTitle']); ?></span>
                                </div>
                                <span class="badge bg-light text-dark border"><?php echo $content['ContentType']; ?></span>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (!empty($quizzes)): ?>
                <div class="card p-4 mb-4">
                    <h4 class="fw-bold mb-3 border-bottom pb-2">Assessments</h4>
                    <div class="row g-4">
                        <?php foreach ($quizzes as $quiz): ?>
                            <div class="col-md-6">
                                <div class="card course-card h-100 p-3 bg-light border-0">
                                    <h5 class="fw-bold text-dark"><?php echo htmlspecialchars($quiz['QuizName']); ?></h5>
                                    <p class="text-muted small mb-3 flex-grow-1"><?php echo htmlspecialchars(substr($quiz['Description'], 0, 100)); ?></p>
                                    <div class="d-flex justify-content-between align-items-center mt-auto">
                                        <span class="badge bg-soft-primary">Marks: <?php echo $quiz['TotalMarks']; ?></span>
                                        <?php if ($is_enrolled): ?>
                                            <a href="?page=take-quiz&id=<?php echo $quiz['QuizID']; ?>" class="btn btn-sm btn-primary">Start Quiz</a>
                                        <?php else: ?>
                                            <span class="text-muted small"><i class="bi bi-lock-fill"></i> Locked</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <div class="col-lg-4 animate__animated animate__fadeInRight animate__delay-1s">
            <div class="card p-4 sticky-top border-0 shadow-sm" style="top: 20px;">
                <div class="text-center mb-4">
                    <span class="badge bg-success mb-3">Free Course</span>
                </div>
                
                <?php if ($is_enrolled): ?>
                    <div class="alert alert-success text-center mb-3">
                        <i class="bi bi-check-circle-fill me-2"></i> You are enrolled
                    </div>
                    <form method="POST" action="?page=drop">
                        <input type="hidden" name="course_id" value="<?php echo $course['CourseID']; ?>">
                        <button type="submit" class="btn btn-outline-danger w-100 fw-bold" onclick="confirmAction(event, 'Drop this course?', 'You will lose access to materials!')">Drop Course</button>
                    </form>
                <?php else: ?>
                    <form action="?page=enroll" method="POST">
                        <input type="hidden" name="course_id" value="<?php echo $course['CourseID']; ?>">
                        <input type="hidden" name="redirect" value="course-details">
                        <button type="submit" class="btn btn-primary w-100 py-3 mb-3 fw-bold fs-5 shadow-sm" onclick="showNotification('Enrolled!', 'Welcome to the course.', 'success')">Enroll Now</button>
                    </form>
                <?php endif; ?>
                
                <hr class="my-4">
                
                <h6 class="fw-bold mb-3 text-uppercase text-muted" style="letter-spacing: 1px;">Course Includes:</h6>
                <ul class="list-unstyled">
                    <li class="mb-3 d-flex align-items-center"><i class="bi bi-play-btn-fill text-primary me-3 fs-5"></i> <span>On-demand video</span></li>
                    <li class="mb-3 d-flex align-items-center"><i class="bi bi-file-earmark-text-fill text-danger me-3 fs-5"></i> <span>Downloadable resources</span></li>
                    <li class="mb-3 d-flex align-items-center"><i class="bi bi-patch-question-fill text-success me-3 fs-5"></i> <span>Interactive quizzes</span></li>
                    <li class="mb-3 d-flex align-items-center"><i class="bi bi-infinity text-info me-3 fs-5"></i> <span>Lifetime access</span></li>
                    <li class="mb-3 d-flex align-items-center"><i class="bi bi-phone-fill text-secondary me-3 fs-5"></i> <span>Access on mobile and TV</span></li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>
