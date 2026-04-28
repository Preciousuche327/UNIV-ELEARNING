<?php
// app/views/student/courses.php
include 'app/views/partials/header.php';
include 'app/views/partials/sidebar.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold">Course Catalog</h2>
        <p class="text-muted">Explore our wide range of academic and professional courses.</p>
    </div>
    <div class="input-group style='max-width: 300px;'">
        <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
        <input type="text" class="form-control border-start-0" placeholder="Search courses...">
    </div>
</div>

<div class="row g-4">
    <?php if (empty($courses)): ?>
        <div class="col-12 text-center py-5">
            <img src="https://illustrations.popsy.co/white/abstract-art-6.svg" alt="Empty" class="img-fluid mb-3" style="max-height: 200px;">
            <h4>No courses found</h4>
            <p class="text-muted">Stay tuned! Our instructors are preparing new content.</p>
        </div>
    <?php else: ?>
        <?php foreach ($courses as $course): ?>
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 course-card">
                    <img src="https://images.unsplash.com/photo-1516321318423-f06f85e504b3?q=80&w=400&h=200&auto=format&fit=crop" class="card-img-top" alt="Course Thumbnail" style="height: 180px; object-fit: cover;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <span class="badge bg-soft-primary px-2 py-1">Best Seller</span>
                            <h5 class="fw-bold text-primary mb-0">$<?php echo number_format($course['Price'], 2); ?></h5>
                        </div>
                        <h5 class="card-title"><?php echo htmlspecialchars($course['CourseName']); ?></h5>
                        <p class="card-text text-muted small mb-4">
                            <?php echo htmlspecialchars(substr($course['Description'], 0, 100)) . '...'; ?>
                        </p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="small text-muted"><i class="bi bi-person me-1"></i> Admin</span>
                            <a href="?page=course-details&id=<?php echo $course['CourseID']; ?>" class="btn btn-sm btn-primary">View Details</a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php include 'app/views/partials/footer.php'; ?>
