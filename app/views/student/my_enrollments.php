<?php
// app/views/student/my_enrollments.php
include __DIR__ . '/../partials/header.php';
include __DIR__ . '/../partials/sidebar_v2.php';
?>

<div class="container-fluid p-4">
    <div class="mb-4">
        <h2 class="fw-bold">My Learning</h2>
        <p class="text-muted">Pick up where you left off in your enrolled courses.</p>
    </div>

    <div class="row g-4">
        <?php if (empty($enrollments)): ?>
            <div class="col-12 text-center py-5">
                <div class="p-5 bg-white rounded shadow-sm">
                    <img src="https://illustrations.popsy.co/white/abstract-art-4.svg" alt="Empty" class="img-fluid mb-4" style="max-height: 250px;">
                    <h4>You haven't enrolled in any courses yet</h4>
                    <p class="text-muted mb-4">Discover new skills by browsing our course catalog.</p>
                    <a href="?page=courses" class="btn btn-primary">Browse Catalog</a>
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($enrollments as $enrollment): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 border-0 shadow-sm overflow-hidden">
                        <div class="position-relative">
                            <img src="https://images.unsplash.com/photo-1434030216411-0b793f4b4173?q=80&w=400&h=150&auto=format&fit=crop" class="card-img-top" alt="Course" style="height: 140px; object-fit: cover;">
                            <div class="position-absolute top-0 end-0 p-2">
                                <span class="badge <?php echo ($enrollment['CompletionStatus'] == 'Completed') ? 'bg-success' : 'bg-primary'; ?> rounded-pill">
                                    <?php echo $enrollment['CompletionStatus']; ?>
                                </span>
                            </div>
                        </div>
                        <div class="card-body">
                            <h6 class="text-muted small mb-1">Enrolled on <?php echo date('M d, Y', strtotime($enrollment['EnrollmentDate'])); ?></h6>
                            <h5 class="card-title mb-3"><?php echo htmlspecialchars($enrollment['CourseName']); ?></h5>
                            
                            <div class="mb-3">
                                <div class="d-flex justify-content-between small mb-1">
                                    <span>Progress</span>
                                    <span><?php echo ($enrollment['CompletionStatus'] == 'Completed') ? '100%' : '25%'; ?></span>
                                </div>
                                <div class="progress" style="height: 6px;">
                                    <div class="progress-bar <?php echo ($enrollment['CompletionStatus'] == 'Completed') ? 'bg-success' : 'bg-primary'; ?>" 
                                         style="width: <?php echo ($enrollment['CompletionStatus'] == 'Completed') ? '100%' : '25%'; ?>"></div>
                                </div>
                            </div>

                            <div class="d-grid mt-4">
                                <a href="?page=course-details&id=<?php echo $enrollment['CourseID']; ?>" class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-arrow-right me-1"></i> Continue Learning
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>

