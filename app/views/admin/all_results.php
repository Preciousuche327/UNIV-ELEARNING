<?php
// app/views/admin/all_results.php
include __DIR__ . '/../partials/header.php';
include __DIR__ . '/../partials/sidebar.php';
?>

<div class="container-fluid p-4">
    <div class="mb-4">
        <h2 class="fw-bold mb-1">All Quiz Results</h2>
        <p class="text-muted">Monitor and evaluate student performance across the entire system.</p>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3">
            <form method="GET" class="d-flex gap-2">
                <input type="hidden" name="page" value="all-results">
                <select class="form-select" name="course" onchange="this.form.submit()">
                    <option value="">All Courses</option>
                    <?php foreach ($courses as $course): ?>
                        <option value="<?php echo $course['CourseID']; ?>" <?php echo (($_GET['course'] ?? '') == $course['CourseID']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($course['CourseName']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </form>
        </div>
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
                            <th>Percentage</th>
                            <th>Status</th>
                            <th class="pe-4">Submitted Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($results)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <p class="text-muted mb-0">No results recorded yet.</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($results as $result): ?>
                                <?php $percentage = round(($result['Score'] / 100) * 100, 1); ?>
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-bold"><?php echo htmlspecialchars($result['Username']); ?></div>
                                    </td>
                                    <td><?php echo htmlspecialchars($result['QuizName']); ?></td>
                                    <td><?php echo htmlspecialchars($result['CourseName']); ?></td>
                                    <td>
                                        <span class="fw-bold">
                                            <?php echo $result['Score']; ?>/100
                                        </span>
                                    </td>
                                    <td><?php echo $percentage; ?>%</td>
                                    <td>
                                        <?php if ($result['Score'] >= 70): ?>
                                            <span class="badge bg-success">Pass</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Fail</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="pe-4"><?php echo date('M d, Y, h:i A', strtotime($result['SubmittedAt'])); ?></td>
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

