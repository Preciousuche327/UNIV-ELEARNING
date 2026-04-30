<?php
// app/views/student/dashboard.php
include __DIR__ . '/../partials/header.php';
include __DIR__ . '/../partials/sidebar.php';

// Calculate completed courses
$completed_count = 0;
foreach ($enrolled_courses as $course) {
    if ($course['CompletionStatus'] === 'Completed') {
        $completed_count++;
    }
}

// Calculate average score from results
$average_score = 0;
if ($completed_quizzes > 0) {
    $stmt = $pdo->prepare("SELECT AVG(Score) as avg_score FROM results WHERE UserID = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $score_data = $stmt->fetch();
    $average_score = ($score_data && isset($score_data['avg_score'])) ? round($score_data['avg_score'], 2) : 0;
}
?>

<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card p-3 stat-card">
            <div class="d-flex align-items-center">
                <div class="stat-icon bg-soft-primary me-3">
                    <i class="bi bi-journal-check"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-0">Enrolled Courses</h6>
                    <h3 class="mb-0 fw-bold"><?php echo $enrolled_count; ?></h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card p-3 stat-card" style="border-left-color: #10b981;">
            <div class="d-flex align-items-center">
                <div class="stat-icon bg-soft-success me-3">
                    <i class="bi bi-trophy"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-0">Completed</h6>
                    <h3 class="mb-0 fw-bold"><?php echo $completed_count; ?></h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card p-3 stat-card" style="border-left-color: #f59e0b;">
            <div class="d-flex align-items-center">
                <div class="stat-icon bg-soft-warning me-3">
                    <i class="bi bi-graph-up"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-0">Average Score</h6>
                    <h3 class="mb-0 fw-bold"><?php echo $average_score; ?>%</h3>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="card-title mb-0">My Recent Courses</h5>
                    <a href="?page=my-enrollments" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>Course Name</th>
                                <th>Progress</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($enrolled_courses)): ?>
                                <?php foreach (array_slice($enrolled_courses, 0, 5) as $course): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="p-2 bg-light rounded me-2">
                                                    <i class="bi bi-book text-primary"></i>
                                                </div>
                                                <span><?php echo htmlspecialchars($course['CourseName']); ?></span>
                                            </div>
                                        </td>
                                        <td style="width: 200px;">
                                            <div class="progress" style="height: 6px;">
                                                <div class="progress-bar bg-primary" style="width: 50%"></div>
                                            </div>
                                        </td>
                                        <td>
                                            <?php 
                                            $badge_class = ($course['CompletionStatus'] === 'Completed') ? 'bg-soft-success' : 'bg-soft-primary';
                                            $badge_text = ($course['CompletionStatus'] === 'Completed') ? 'Completed' : 'In Progress';
                                            ?>
                                            <span class="badge <?php echo $badge_class; ?> px-2 py-1"><?php echo $badge_text; ?></span>
                                        </td>
                                        <td>
                                            <a href="?page=course-details&id=<?php echo $course['CourseID']; ?>" class="btn btn-sm btn-light">View</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">
                                        No courses enrolled yet. <a href="?page=courses">Browse courses</a>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-body text-center py-5">
                <img src="https://illustrations.popsy.co/white/student-going-to-school.svg" alt="Learn" class="img-fluid mb-4" style="max-height: 150px;">
                <h5>Ready to learn more?</h5>
                <p class="text-muted small">Explore updated courses from our top instructors.</p>
                <a href="?page=courses" class="btn btn-primary w-100 mt-3">Browse Catalog</a>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>

