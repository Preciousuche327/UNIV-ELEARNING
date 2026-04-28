<?php
// app/views/student/dashboard.php
include 'app/views/partials/header.php';
include 'app/views/partials/sidebar.php';

// Fetch some stats (dummy for now, real queries later)
$enrolledCourses = 3;
$completedCourses = 1;
$averageScore = "85%";
?>

<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card p-3 stat-card">
            <div class="d-flex align-items-center">
                <div class="stat-icon bg-soft-primary me-3">
                    <i class="bi bi-journal-check"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-0">Enrolled Courses</h6>
                    <h3 class="mb-0 fw-bold"><?php echo $enrolledCourses; ?></h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card p-3 stat-card" style="border-left-color: #10b981;">
            <div class="d-flex align-items-center">
                <div class="stat-icon bg-soft-success me-3">
                    <i class="bi bi-trophy"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-0">Completed</h6>
                    <h3 class="mb-0 fw-bold"><?php echo $completedCourses; ?></h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card p-3 stat-card" style="border-left-color: #f59e0b;">
            <div class="d-flex align-items-center">
                <div class="stat-icon bg-soft-warning me-3">
                    <i class="bi bi-graph-up"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-0">Average Score</h6>
                    <h3 class="mb-0 fw-bold"><?php echo $averageScore; ?></h3>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="card-title mb-0">My Recent Courses</h5>
                    <a href="?page=my-enrollments" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>Course Name</th>
                                <th>Progress</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Mock Data -->
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="p-2 bg-light rounded me-2">
                                            <i class="bi bi-code-slash text-primary"></i>
                                        </div>
                                        <span>Introduction to PHP</span>
                                    </div>
                                </td>
                                <td style="width: 200px;">
                                    <div class="progress" style="height: 6px;">
                                        <div class="progress-bar bg-primary" style="width: 75%"></div>
                                    </div>
                                </td>
                                <td><span class="badge bg-soft-primary px-2 py-1">In Progress</span></td>
                                <td><a href="#" class="btn btn-sm btn-light">Continue</a></td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="p-2 bg-light rounded me-2">
                                            <i class="bi bi-database text-success"></i>
                                        </div>
                                        <span>MySQL Fundamentals</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="progress" style="height: 6px;">
                                        <div class="progress-bar bg-success" style="width: 100%"></div>
                                    </div>
                                </td>
                                <td><span class="badge bg-soft-success px-2 py-1">Completed</span></td>
                                <td><a href="#" class="btn btn-sm btn-light">Review</a></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-body text-center py-5">
                <img src="https://illustrations.popsy.co/white/student-going-to-school.svg" alt="Learn" class="img-fluid mb-4" style="max-height: 150px;">
                <h5>Ready to learn more?</h5>
                <p class="text-muted small">Explore updated courses from our top instructors.</p>
                <a href="?page=courses" class="btn btn-primary w-100 mt-3">Browse Catalog</a>
            </div>
        </div>
    </div>
</div>

<?php include 'app/views/partials/footer.php'; ?>
