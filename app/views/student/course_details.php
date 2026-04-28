<?php
// app/views/student/course_details.php
include 'app/views/partials/header.php';
include 'app/views/partials/sidebar.php';
?>

<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="?page=courses">Courses</a></li>
        <li class="breadcrumb-item active" aria-current="page">Course Details</li>
    </ol>
</nav>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card p-4 mb-4">
            <h1 class="fw-bold mb-3"><?php echo htmlspecialchars($course['CourseName']); ?></h1>
            <p class="lead text-muted mb-4"><?php echo nl2br(htmlspecialchars($course['Description'])); ?></p>
            
            <h5 class="fw-bold mb-3">What you'll learn</h5>
            <ul class="list-group list-group-flush mb-4">
                <li class="list-group-item border-0 ps-0"><i class="bi bi-check-circle-fill text-success me-2"></i> Comprehensive understanding of the subject.</li>
                <li class="list-group-item border-0 ps-0"><i class="bi bi-check-circle-fill text-success me-2"></i> Practical skills and real-world applications.</li>
                <li class="list-group-item border-0 ps-0"><i class="bi bi-check-circle-fill text-success me-2"></i> Preparation for advanced certification quizzes.</li>
            </ul>
        </div>

        <div class="card p-4">
            <h5 class="fw-bold mb-4">Course Curriculum</h5>
            <div class="accordion" id="curriculumAccordion">
                <?php if (empty($contents)): ?>
                    <p class="text-muted italic">Curriculum is being finalized by the instructor.</p>
                <?php else: ?>
                    <?php foreach ($contents as $index => $content): ?>
                        <div class="accordion-item border-0 mb-2 shadow-sm rounded">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed rounded" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?php echo $index; ?>">
                                    <i class="bi <?php echo ($content['ContentType'] == 'Video') ? 'bi-play-circle' : 'bi-file-earmark-pdf'; ?> me-3 text-primary"></i>
                                    <?php echo htmlspecialchars($content['ContentTitle']); ?>
                                </button>
                            </h2>
                            <div id="collapse<?php echo $index; ?>" class="accordion-collapse collapse" data-bs-parent="#curriculumAccordion">
                                <div class="accordion-body">
                                    <p class="small text-muted mb-3">This is a <?php echo $content['ContentType']; ?> resource.</p>
                                    <a href="<?php echo htmlspecialchars($content['ContentURL']); ?>" target="_blank" class="btn btn-sm btn-outline-primary">Access Content</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card p-4 sticky-top" style="top: 20px;">
            <div class="text-center mb-4">
                <h2 class="fw-bold text-primary mb-1">$<?php echo number_format($course['Price'], 2); ?></h2>
                <p class="text-muted small">Full Lifetime Access</p>
            </div>
            
            <form action="?page=enroll" method="POST">
                <input type="hidden" name="course_id" value="<?php echo $course['CourseID']; ?>">
                <button type="submit" class="btn btn-primary w-100 py-3 mb-3 fw-bold">Enroll Now</button>
            </form>
            
            <p class="text-center text-muted small mb-4">30-Day Money-Back Guarantee</p>
            
            <div class="border-top pt-4">
                <h6 class="fw-bold mb-3">This course includes:</h6>
                <div class="mb-2"><i class="bi bi-play-btn me-2"></i> On-demand video</div>
                <div class="mb-2"><i class="bi bi-file-earmark me-2"></i> Downloadable resources</div>
                <div class="mb-2"><i class="bi bi-patch-question me-2"></i> Interactive quizzes</div>
                <div class="mb-2"><i class="bi bi-infinity me-2"></i> Lifetime access</div>
            </div>
        </div>
    </div>
</div>

<?php include 'app/views/partials/footer.php'; ?>
