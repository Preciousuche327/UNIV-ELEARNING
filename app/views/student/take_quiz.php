<?php
// app/views/student/take_quiz.php
include 'app/views/partials/header.php';
include 'app/views/partials/sidebar.php';
?>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm p-5">
            <div class="text-center mb-5">
                <span class="badge bg-soft-primary px-3 py-2 mb-3">Quiz Mode</span>
                <h2 class="fw-bold"><?php echo htmlspecialchars($quiz['QuizName']); ?></h2>
                <p class="text-muted"><?php echo htmlspecialchars($quiz['Description']); ?></p>
            </div>

            <form method="POST">
                <div class="mb-5">
                    <h5 class="fw-bold mb-3">1. Which of the following is the correct syntax to start a PHP session?</h5>
                    <div class="form-check p-3 border rounded mb-2">
                        <input class="form-check-input ms-0 me-3" type="radio" name="q1" id="q1a" value="a">
                        <label class="form-check-label" for="q1a">session_begin();</label>
                    </div>
                    <div class="form-check p-3 border rounded mb-2 bg-soft-primary border-primary">
                        <input class="form-check-input ms-0 me-3" type="radio" name="q1" id="q1b" value="b" checked>
                        <label class="form-check-label" for="q1b">session_start();</label>
                    </div>
                    <div class="form-check p-3 border rounded mb-2">
                        <input class="form-check-input ms-0 me-3" type="radio" name="q1" id="q1c" value="c">
                        <label class="form-check-label" for="q1c">start_session();</label>
                    </div>
                </div>

                <div class="mb-5">
                    <h5 class="fw-bold mb-3">2. How do you select all columns from a table named "Users" in MySQL?</h5>
                    <div class="form-check p-3 border rounded mb-2">
                        <input class="form-check-input ms-0 me-3" type="radio" name="q2" id="q2a" value="a">
                        <label class="form-check-label" for="q2a">SELECT Users;</label>
                    </div>
                    <div class="form-check p-3 border rounded mb-2">
                        <input class="form-check-input ms-0 me-3" type="radio" name="q2" id="q2b" value="b">
                        <label class="form-check-label" for="q2b">SELECT all FROM Users;</label>
                    </div>
                    <div class="form-check p-3 border rounded mb-2 bg-soft-primary border-primary">
                        <input class="form-check-input ms-0 me-3" type="radio" name="q2" id="q2c" value="c" checked>
                        <label class="form-check-label" for="q2c">SELECT * FROM Users;</label>
                    </div>
                </div>

                <div class="alert alert-info py-3 mb-4">
                    <i class="bi bi-info-circle me-2"></i> This is a simulated quiz interface. Clicking submit will generate a representative score for your performance.
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-primary py-3 fw-bold">Submit My Answers</button>
                    <a href="?page=dashboard" class="btn btn-link text-muted mt-2">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'app/views/partials/footer.php'; ?>
