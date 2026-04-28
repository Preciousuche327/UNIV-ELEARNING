<?php include __DIR__ . '/../partials/header.php'; ?>
<?php include __DIR__ . '/../partials/sidebar.php'; ?>

<h2>Courses</h2>

<div class="row">
<?php foreach ($courses as $course): ?>
    <div class="col-md-4 mb-3">
        <div class="card p-3">
            <h5><?= $course['CourseName'] ?></h5>
            <p class="text-muted"><?= $course['Description'] ?></p>

            <?php if (!$course['IsEnrolled']): ?>
                <form method="POST" action="index.php?page=enroll">
                    <input type="hidden" name="course_id" value="<?= $course['CourseID'] ?>">
                    <button class="btn btn-primary w-100">Enroll</button>
                </form>
            <?php else: ?>
                <form method="POST" action="index.php?page=drop">
                    <input type="hidden" name="course_id" value="<?= $course['CourseID'] ?>">
                    <button class="btn btn-danger w-100">Drop</button>
                </form>
            <?php endif; ?>

        </div>
    </div>
<?php endforeach; ?>
</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>