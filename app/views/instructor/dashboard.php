<?php
// app/views/instructor/dashboard.php
include 'app/views/partials/header.php';
include 'app/views/partials/sidebar.php';

// Mock stats for Instructor
$totalCourses = 5;
$totalStudents = 120;
$pendingQuizzes = 2;
?>

<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card p-3 stat-card" style="border-left-color: #6366f1;">
            <div class="d-flex align-items-center">
                <div class="stat-icon bg-soft-primary me-3">
                    <i class="bi bi-journal-text"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-0">Total Courses</h6>
                    <h3 class="mb-0 fw-bold"><?php echo $totalCourses; ?></h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card p-3 stat-card" style="border-left-color: #ec4899;">
            <div class="d-flex align-items-center">
                <div class="stat-icon bg-light text-pink me-3" style="color: #ec4899; background-color: #fdf2f8;">
                    <i class="bi bi-people"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-0">Total Students</h6>
                    <h3 class="mb-0 fw-bold"><?php echo $totalStudents; ?></h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card p-3 stat-card" style="border-left-color: #06b6d4;">
            <div class="d-flex align-items-center">
                <div class="stat-icon bg-soft-info me-3" style="background-color: #ecfeff; color: #0891b2;">
                    <i class="bi bi-patch-question"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-0">Quizzes Created</h6>
                    <h3 class="mb-0 fw-bold"><?php echo $pendingQuizzes; ?></h3>
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
                    <h5 class="card-title mb-0">My Published Courses</h5>
                    <a href="?page=create-course" class="btn btn-sm btn-primary">+ Create New Course</a>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>Course Name</th>
                                <th>Enrollments</th>
                                <th>Average Score</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Mock Data -->
                            <tr>
                                <td>Advanced Web Development</td>
                                <td>45 Students</td>
                                <td>92%</td>
                                <td>
                                    <button class="btn btn-sm btn-light border"><i class="bi bi-pencil"></i></button>
                                    <button class="btn btn-sm btn-light border text-primary"><i class="bi bi-plus-circle"></i> Content</button>
                                </td>
                            </tr>
                            <tr>
                                <td>Database Management Systems</td>
                                <td>75 Students</td>
                                <td>88%</td>
                                <td>
                                    <button class="btn btn-sm btn-light border"><i class="bi bi-pencil"></i></button>
                                    <button class="btn btn-sm btn-light border text-primary"><i class="bi bi-plus-circle"></i> Content</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title mb-3">Quick Actions</h5>
                <div class="d-grid gap-2">
                    <a href="?page=add-quiz" class="btn btn-outline-dark text-start">
                        <i class="bi bi-plus-square me-2"></i> Create a Quiz
                    </a>
                    <a href="?page=upload-content" class="btn btn-outline-dark text-start">
                        <i class="bi bi-cloud-upload me-2"></i> Upload New Content
                    </a>
                    <a href="?page=view-analytics" class="btn btn-outline-dark text-start">
                        <i class="bi bi-bar-chart me-2"></i> View Performance
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'app/views/partials/footer.php'; ?>
