<?php
// app/views/instructor/manage_courses.php
include __DIR__ . '/../partials/header.php';
include __DIR__ . '/../partials/sidebar_v2.php';
?>

<div class="container-fluid p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">My Courses</h2>
            <p class="text-muted">Create quizzes, midterms, and finals for assigned courses</p>
        </div>
        <a href="?page=create-quiz" class="btn btn-primary">
            <i class="bi bi-plus-circle me-2"></i> New Assessment
        </a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Course Name</th>
                            <th>Students</th>
                            <th>Quizzes</th>

                            <th>Status</th>
                            <th class="pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($courses)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <p class="text-muted mb-0">No courses have been assigned to you yet.</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($courses as $course): ?>
                                <tr>
                                    <td class="ps-4 fw-bold"><?php echo htmlspecialchars($course['CourseName']); ?></td>
                                    <td>
                                        <span class="badge bg-soft-primary"><?php echo $course['StudentCount']; ?></span>
                                    </td>
                                    <td>
                                        <span class="badge bg-soft-success"><?php echo isset($course['QuizCount']) ? $course['QuizCount'] : 0; ?></span>
                                    </td>

                                    <td>
                                        <span class="badge bg-success">Active</span>
                                    </td>
                                    <td class="pe-4">
                                        <div class="btn-group btn-group-sm">
                                            <a href="?page=create-quiz&course_id=<?php echo $course['CourseID']; ?>" class="btn btn-outline-success">
                                                <i class="bi bi-plus"></i> Assessment
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>

