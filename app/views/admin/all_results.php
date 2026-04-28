<?php
// app/views/admin/all_results.php
include 'app/views/partials/header.php';
include 'app/views/partials/sidebar.php';
?>

<div class="mb-4">
    <h2 class="fw-bold">Global Enrollment Results</h2>
    <p class="text-muted">Monitor and evaluate student performance across the entire system.</p>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Student</th>
                        <th>Quiz Name</th>
                        <th>Course</th>
                        <th>Score</th>
                        <th>Status</th>
                        <th class="pe-4">Date Submited</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($results)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <p class="text-muted mb-0">No results recorded in the system yet.</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($results as $result): ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold"><?php echo htmlspecialchars($result['Username']); ?></div>
                                    <div class="small text-muted">ID: #ST-<?php echo $result['UserID']; ?></div>
                                </td>
                                <td><?php echo htmlspecialchars($result['QuizName']); ?></td>
                                <td><?php echo htmlspecialchars($result['CourseName']); ?></td>
                                <td>
                                    <span class="fw-bold <?php echo ($result['Score'] >= 70) ? 'text-success' : 'text-danger'; ?>">
                                        <?php echo $result['Score']; ?>/100
                                    </span>
                                </td>
                                <td>
                                    <?php if ($result['Score'] >= 70): ?>
                                        <span class="badge bg-soft-success px-3 py-1">Pass</span>
                                    <?php else: ?>
                                        <span class="badge bg-soft-danger px-3 py-1">Fail</span>
                                    <?php endif; ?>
                                </td>
                                <td class="pe-4 text-muted small"><?php echo date('M d, Y', strtotime($result['SubmittedAt'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'app/views/partials/footer.php'; ?>
