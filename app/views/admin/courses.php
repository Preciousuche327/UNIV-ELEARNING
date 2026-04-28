<?php
// app/views/admin/courses.php
include __DIR__ . '/../partials/header.php';
include __DIR__ . '/../partials/sidebar.php';
?>

<div class="container-fluid p-4">
    <div class="mb-4">
        <h2 class="fw-bold mb-1">Course Management</h2>
        <p class="text-muted">View and manage all courses in the system</p>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Course Name</th>
                            <th>Instructor</th>
                            <th>Students Enrolled</th>

                            <th>Quizzes</th>
                            <th class="pe-4">Created Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($courses)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <p class="text-muted mb-0">No courses found.</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($courses as $course): ?>
                                <tr>
                                    <td class="ps-4 fw-bold"><?php echo htmlspecialchars($course['CourseName']); ?></td>
                                    <td>Instructor</td>
                                    <td>
                                        <span class="badge bg-soft-primary"><?php echo $course['EnrollmentCount']; ?></span>
                                    </td>

                                    <td>
                                        <span class="badge bg-soft-info">0</span>
                                    </td>
                                    <td class="pe-4"><?php echo date('M d, Y', strtotime($course['CreatedAt'])); ?></td>
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
