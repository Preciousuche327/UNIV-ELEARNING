<?php
// app/views/instructor/course_results.php
include __DIR__ . '/../partials/header.php';
include __DIR__ . '/../partials/sidebar.php';
?>

<div class="container-fluid p-4">
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h2 class="fw-bold mb-1"><?php echo $page_title; ?></h2>
            <p class="text-muted">Analyze student performance and quiz success rates.</p>
        </div>
        <a href="?page=dashboard" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Back to Dashboard
        </a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Student</th>
                            <th>Course</th>
                            <th>Quiz Name</th>
                            <th>Score</th>
                            <th>Status</th>
                            <th class="pe-4">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($results)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="bi bi-info-circle fs-2 d-block mb-2"></i>
                                        No results recorded yet for your courses.
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($results as $result): ?>
                                <?php 
                                    $total_marks = $result['TotalMarks'] ?: 100;
                                    $percentage = ($result['Score'] / $total_marks) * 100;
                                    $is_pass = $percentage >= 70;
                                ?>
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-light text-primary rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                                                <?php echo strtoupper(substr($result['Username'], 0, 1)); ?>
                                            </div>
                                            <span class="fw-bold"><?php echo htmlspecialchars($result['Username']); ?></span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-soft-primary text-primary border border-primary-subtle">
                                            <?php echo htmlspecialchars($result['CourseName']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars($result['QuizName']); ?></td>
                                    <td>
                                        <span class="fw-bold <?php echo $is_pass ? 'text-success' : 'text-danger'; ?>">
                                            <?php echo $result['Score']; ?>/<?php echo $total_marks; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($is_pass): ?>
                                            <span class="badge bg-success">Pass</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Fail</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="pe-4 text-muted small">
                                        <?php echo date('M d, Y', strtotime($result['SubmittedAt'])); ?>
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
