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

    <?php 
    $stats = ['Quiz' => 0, 'Midterm' => 0, 'Final' => 0, 'Assignment' => 0];
    foreach ($results as $r) {
        if (isset($stats[$r['QuizType']])) {
            $stats[$r['QuizType']]++;
        }
    }
    ?>

    <div class="row mb-4 g-3">
        <div class="col-md-3">
            <div class="card bg-light border-0">
                <div class="card-body text-center p-3">
                    <h6 class="text-muted mb-1 small">Quizzes Taken</h6>
                    <h4 class="mb-0 fw-bold text-warning"><?php echo $stats['Quiz']; ?></h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-light border-0">
                <div class="card-body text-center p-3">
                    <h6 class="text-muted mb-1 small">Midterms Taken</h6>
                    <h4 class="mb-0 fw-bold text-info"><?php echo $stats['Midterm']; ?></h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-light border-0">
                <div class="card-body text-center p-3">
                    <h6 class="text-muted mb-1 small">Finals Taken</h6>
                    <h4 class="mb-0 fw-bold text-dark"><?php echo $stats['Final']; ?></h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-light border-0">
                <div class="card-body text-center p-3">
                    <h6 class="text-muted mb-1 small">Assignments Taken</h6>
                    <h4 class="mb-0 fw-bold text-primary"><?php echo $stats['Assignment']; ?></h4>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Student</th>
                            <th>Course</th>
                            <th>Quiz</th>
                            <th>Midterm</th>
                            <th>Final</th>
                            <th>Assignment</th>
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
                                    <td>
                                        <?php if ($result['QuizTotal'] > 0): ?>
                                            <span class="fw-semibold"><?php echo $result['QuizScore']; ?>/<?php echo $result['QuizTotal']; ?></span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($result['MidtermTotal'] > 0): ?>
                                            <span class="fw-semibold"><?php echo $result['MidtermScore']; ?>/<?php echo $result['MidtermTotal']; ?></span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($result['FinalTotal'] > 0): ?>
                                            <span class="fw-semibold"><?php echo $result['FinalScore']; ?>/<?php echo $result['FinalTotal']; ?></span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($result['AssignmentTotal'] > 0): ?>
                                            <span class="fw-semibold"><?php echo $result['AssignmentScore']; ?>/<?php echo $result['AssignmentTotal']; ?></span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
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
