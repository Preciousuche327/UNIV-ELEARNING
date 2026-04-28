<?php
// app/views/student/quiz_detail.php
include __DIR__ . '/../partials/header.php';
include __DIR__ . '/../partials/sidebar.php';

$percentage = ($result['TotalMarks'] > 0) ? round(($result['Score'] / $result['TotalMarks']) * 100, 1) : 0;
$is_pass = $percentage >= 70;
?>

<div class="container-fluid p-4">
    <div class="mb-4">
        <a href="?page=my-results" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Back to Results
        </a>
    </div>

    <div class="row mb-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header <?php echo $is_pass ? 'bg-success' : 'bg-danger'; ?> text-white p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="card-title mb-1"><?php echo htmlspecialchars($result['QuizName']); ?></h4>
                            <small><?php echo htmlspecialchars($result['CourseName']); ?></small>
                        </div>
                        <div class="text-end">
                            <h2 class="mb-0"><?php echo $percentage; ?>%</h2>
                            <small><?php echo $result['Score']; ?>/<?php echo $result['TotalMarks']; ?> points</small>
                        </div>
                    </div>
                </div>

                <div class="card-body p-4">
                    <div class="alert <?php echo $is_pass ? 'alert-success' : 'alert-danger'; ?>">
                        <i class="bi bi-<?php echo $is_pass ? 'check-circle' : 'x-circle'; ?> me-2"></i>
                        <strong><?php echo $is_pass ? 'Passed!' : 'Did not pass'; ?></strong>
                        <?php echo $is_pass ? 'Great job! You scored above 70%.' : 'Try retaking the quiz to improve your score.'; ?>
                    </div>

                    <hr>

                    <h5 class="fw-bold mb-4">Question Review</h5>

                    <?php if (!empty($questions)): ?>
                        <?php foreach ($questions as $qindex => $question): ?>
                            <?php 
                                $is_correct = $question['IsCorrect'];
                                $answered = !is_null($question['SelectedOptionID']);
                            ?>
                            <div class="mb-4 p-4 border rounded <?php echo $is_correct ? 'border-success bg-light-success' : 'border-danger bg-light-danger'; ?>">
                                <div class="d-flex align-items-start mb-3">
                                    <span class="badge <?php echo $is_correct ? 'bg-success' : 'bg-danger'; ?> me-3">
                                        <?php echo $is_correct ? '✓' : '✗'; ?>
                                    </span>
                                    <div class="flex-grow-1">
                                        <h6 class="fw-bold mb-1"><?php echo htmlspecialchars($question['QuestionText']); ?></h6>
                                        <small class="text-muted">Marks: <?php echo isset($question['Marks']) ? $question['Marks'] : '1'; ?></small>
                                    </div>
                                </div>

                                <div class="ms-5">
                                    <div class="mb-3">
                                        <p class="small fw-bold mb-2">
                                            <?php echo $answered ? 'Your Answer:' : 'Not Answered'; ?>
                                        </p>
                                        <?php if ($answered && !is_null($question['SelectedOption'])): ?>
                                            <p class="mb-2">
                                                <span class="badge <?php echo $is_correct ? 'bg-success' : 'bg-danger'; ?>">
                                                    <?php echo htmlspecialchars($question['SelectedOption']); ?>
                                                </span>
                                            </p>
                                        <?php endif; ?>
                                    </div>

                                    <?php if (!$is_correct && !empty($question['options'])): ?>
                                        <div class="mt-3 pt-3 border-top">
                                            <p class="small fw-bold mb-2">Correct Answer:</p>
                                            <?php foreach ($question['options'] as $option): ?>
                                                <?php if ($option['IsCorrect']): ?>
                                                    <p class="mb-0">
                                                        <span class="badge bg-success">
                                                            <?php echo htmlspecialchars($option['OptionText']); ?>
                                                        </span>
                                                    </p>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 sticky-top" style="top: 20px;">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">Quiz Summary</h5>

                    <div class="mb-3">
                        <label class="text-muted small d-block mb-1">Total Score</label>
                        <h4 class="mb-0"><?php echo $result['Score']; ?> / <?php echo $result['TotalMarks']; ?></h4>
                    </div>

                    <div class="mb-3">
                        <label class="text-muted small d-block mb-1">Percentage</label>
                        <h4 class="mb-0"><?php echo $percentage; ?>%</h4>
                    </div>

                    <div class="mb-3">
                        <label class="text-muted small d-block mb-1">Status</label>
                        <span class="badge <?php echo $is_pass ? 'bg-success' : 'bg-danger'; ?> p-2">
                            <?php echo $is_pass ? 'Passed' : 'Failed'; ?>
                        </span>
                    </div>

                    <div class="mb-3">
                        <label class="text-muted small d-block mb-1">Submitted</label>
                        <small><?php echo date('M d, Y', strtotime($result['SubmittedAt'])); ?></small>
                    </div>

                    <hr>

                    <a href="?page=course-details&id=<?php echo $result['CourseID']; ?>" class="btn btn-primary w-100 btn-sm">
                        <i class="bi bi-arrow-left me-1"></i> Back to Course
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>
