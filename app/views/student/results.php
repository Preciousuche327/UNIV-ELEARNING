<?php
// app/views/student/results.php
include __DIR__ . '/../partials/header.php';
include __DIR__ . '/../partials/sidebar_v2.php';
?>

<div class="container-fluid p-4">
    <div class="mb-4">
        <h2 class="fw-bold">My Quiz Results</h2>
        <p class="text-muted">Track your academic performance across all courses.</p>
    </div>

    <div class="row mb-4 g-3">
        <div class="col-md-3">
            <div class="card bg-light border-0">
                <div class="card-body text-center p-3">
                    <h6 class="text-muted mb-1 small">Quizzes</h6>
                    <h4 class="mb-0 fw-bold text-warning"><?php echo $stats['Quiz']; ?></h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-light border-0">
                <div class="card-body text-center p-3">
                    <h6 class="text-muted mb-1 small">Midterms</h6>
                    <h4 class="mb-0 fw-bold text-info"><?php echo $stats['Midterm']; ?></h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-light border-0">
                <div class="card-body text-center p-3">
                    <h6 class="text-muted mb-1 small">Finals</h6>
                    <h4 class="mb-0 fw-bold text-dark"><?php echo $stats['Final']; ?></h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-light border-0">
                <div class="card-body text-center p-3">
                    <h6 class="text-muted mb-1 small">Assignments</h6>
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
                            <th class="ps-4">Assessment</th>
                            <th>Type</th>
                            <th>Course</th>
                            <th>Score</th>
                            <th>Percentage</th>
                            <th>Status</th>
                            <th class="pe-4">Submitted Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($results)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <p class="text-muted mb-0">No results found. Start by taking a quiz!</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($results as $result): ?>
                                <?php 
                                    $percentage = ($result['TotalMarks'] > 0) ? round(($result['Score'] / $result['TotalMarks']) * 100, 1) : 0;
                                    $is_pass = $percentage >= 70;
                                ?>
                                <tr>
                                    <td class="ps-4 fw-bold"><?php echo htmlspecialchars($result['QuizName']); ?></td>
                                    <td>
                                        <?php 
                                        $type_class = 'bg-secondary';
                                        switch($result['QuizType']) {
                                            case 'Midterm': $type_class = 'bg-info'; break;
                                            case 'Final': $type_class = 'bg-dark'; break;
                                            case 'Assignment': $type_class = 'bg-primary'; break;
                                            case 'Quiz': $type_class = 'bg-warning text-dark'; break;
                                        }
                                        ?>
                                        <span class="badge <?php echo $type_class; ?>"><?php echo $result['QuizType']; ?></span>
                                    </td>
                                    <td><?php echo htmlspecialchars($result['CourseName']); ?></td>
                                    <td>
                                        <span class="fw-bold <?php echo $is_pass ? 'text-success' : 'text-danger'; ?>">
                                            <?php echo $result['Score']; ?>/<?php echo $result['TotalMarks']; ?>
                                        </span>
                                    </td>
                                    <td><?php echo $percentage; ?>%</td>
                                    <td>
                                        <?php if ($is_pass): ?>
                                            <span class="badge bg-success">Pass</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Fail</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="pe-4 text-muted small"><?php echo date('M d, Y, h:i A', strtotime($result['SubmittedAt'])); ?></td>
                                    <td>
                                        <a href="?page=quiz-detail&id=<?php echo $result['ResultID']; ?>" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye"></i> View
                                        </a>
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

