<?php
// app/views/instructor/manage_quiz.php
include __DIR__ . '/../partials/header.php';
include __DIR__ . '/../partials/sidebar_v2.php';
?>

<div class="container-fluid p-4">
    <div class="mb-4">
        <a href="?page=dashboard" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-lg-8">
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-primary text-white p-4">
                    <h4 class="card-title mb-0">Manage Quiz: <?php echo htmlspecialchars($quiz['QuizName']); ?></h4>
                </div>

                <div class="card-body p-4">
                    <div class="mb-4 p-3 bg-light rounded">
                        <div class="row">
                            <div class="col-md-3">
                                <small class="text-muted d-block mb-1">Total Questions</small>
                                <h5 class="mb-0"><?php echo count($questions); ?></h5>
                            </div>
                            <div class="col-md-3">
                                <small class="text-muted d-block mb-1">Assessment Type</small>
                                <h5 class="mb-0">
                                    <span class="badge bg-secondary"><?php echo $quiz['QuizType']; ?></span>
                                </h5>
                            </div>
                            <div class="col-md-3">
                                <small class="text-muted d-block mb-1">Total Marks</small>
                                <h5 class="mb-0"><?php echo $quiz['TotalMarks']; ?></h5>
                            </div>
                            <div class="col-md-3">
                                <small class="text-muted d-block mb-1">Description</small>
                                <p class="mb-0 text-truncate"><small><?php echo htmlspecialchars($quiz['Description']); ?></small></p>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <h5 class="fw-bold mb-3">Questions</h5>

                    <?php if (empty($questions)): ?>
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            No questions added yet. Add your first question below.
                        </div>
                    <?php else: ?>
                        <div class="mb-4">
                            <?php foreach ($questions as $qindex => $question): ?>
                                <div class="card mb-3 border-0 bg-light">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div>
                                                <h6 class="fw-bold mb-1">Q<?php echo $qindex + 1; ?>: <?php echo htmlspecialchars($question['QuestionText']); ?></h6>
                                                <small class="text-muted">Type: <?php echo $question['QuestionType']; ?> | Marks: <?php echo $question['Marks']; ?></small>
                                            </div>
                                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteQuestion(<?php echo $question['QuestionID']; ?>)">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>

                                        <?php if (!empty($question['options'])): ?>
                                            <div class="mt-2">
                                                <small class="text-muted d-block mb-1">Options:</small>
                                                <ul class="list-unstyled mb-0">
                                                    <?php foreach ($question['options'] as $option): ?>
                                                        <li class="small mb-1">
                                                            <?php echo $option['IsCorrect'] ? '<strong class="text-success">✓ ' : '○ '; ?>
                                                            <?php echo htmlspecialchars($option['OptionText']); ?>
                                                            <?php echo $option['IsCorrect'] ? '</strong>' : ''; ?>
                                                        </li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm sticky-top" style="top: 20px;">
                <div class="card-header bg-success text-white p-4">
                    <h5 class="card-title mb-0">Add New Question</h5>
                </div>

                <div class="card-body p-4">
                    <form method="POST" action="?page=add-question">
                        <input type="hidden" name="quiz_id" value="<?php echo $quiz['QuizID']; ?>">

                        <div class="mb-3">
                            <label for="question_text" class="form-label fw-bold">Question <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="question_text" name="question_text" rows="3" required placeholder="Enter the question..."></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="question_type" class="form-label fw-bold">Question Type <span class="text-danger">*</span></label>
                            <select class="form-select" id="question_type" name="question_type" onchange="toggleOptions()">
                                <option value="Multiple Choice">Multiple Choice</option>
                                <option value="True/False">True/False</option>
                                <option value="Short Answer">Short Answer</option>
                            </select>
                        </div>

                        <div id="options_section" class="mb-3">
                            <label class="form-label fw-bold">Options</label>
                            <div id="options_container">
                                <div class="input-group mb-2">
                                    <div class="form-check me-2 mt-2">
                                        <input class="form-check-input" type="radio" name="correct_option" value="0" checked>
                                    </div>
                                    <input type="text" class="form-control" name="options[]" placeholder="Option 1">
                                    <button type="button" class="btn btn-outline-secondary" onclick="removeOption(this)">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                                <div class="input-group mb-2">
                                    <div class="form-check me-2 mt-2">
                                        <input class="form-check-input" type="radio" name="correct_option" value="1">
                                    </div>
                                    <input type="text" class="form-control" name="options[]" placeholder="Option 2">
                                    <button type="button" class="btn btn-outline-secondary" onclick="removeOption(this)">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                                <div class="input-group mb-2">
                                    <div class="form-check me-2 mt-2">
                                        <input class="form-check-input" type="radio" name="correct_option" value="2">
                                    </div>
                                    <input type="text" class="form-control" name="options[]" placeholder="Option 3">
                                    <button type="button" class="btn btn-outline-secondary" onclick="removeOption(this)">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                                <div class="input-group mb-2">
                                    <div class="form-check me-2 mt-2">
                                        <input class="form-check-input" type="radio" name="correct_option" value="3">
                                    </div>
                                    <input type="text" class="form-control" name="options[]" placeholder="Option 4">
                                    <button type="button" class="btn btn-outline-secondary" onclick="removeOption(this)">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="addOption()">
                                + Add Option
                            </button>
                        </div>

                        <div class="mb-3">
                            <label for="marks" class="form-label fw-bold">Marks <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="marks" name="marks" value="1" min="1" required>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-plus-circle me-2"></i> Add Question
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function toggleOptions() {
    const type = document.getElementById('question_type').value;
    const section = document.getElementById('options_section');
    section.style.display = (type === 'Multiple Choice') ? 'block' : 'none';
}

function addOption() {
    const container = document.getElementById('options_container');
    const count = container.children.length;
    const html = `
        <div class="input-group mb-2">
            <div class="form-check me-2 mt-2">
                <input class="form-check-input" type="radio" name="correct_option" value="${count}">
            </div>
            <input type="text" class="form-control" name="options[]" placeholder="Option ${count + 1}">
            <button type="button" class="btn btn-outline-secondary" onclick="removeOption(this)">
                <i class="bi bi-trash"></i>
            </button>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', html);
}

function removeOption(button) {
    button.parentElement.remove();
}

function deleteQuestion(questionId) {
    if (confirm('Are you sure you want to delete this question?')) {
        // Will need a DELETE endpoint
        alert('Question deletion not yet implemented');
    }
}
</script>

<?php include __DIR__ . '/../partials/footer.php'; ?>
