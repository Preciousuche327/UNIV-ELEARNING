<?php
// app/views/admin/dashboard.php
include 'app/views/partials/header.php';
include 'app/views/partials/sidebar.php';

// Mock stats for Admin
$totalUsers = 150;
$totalCourses = 15;
$totalEnrollments = 450;
$totalRevenue = "$12,400"; // Note: User asked for no payments in Result/Payment table, but Course has a Price field.
?>

<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card p-3 stat-card" style="border-left-color: #6366f1;">
            <div class="d-flex align-items-center">
                <div class="stat-icon bg-soft-primary me-3">
                    <i class="bi bi-people"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-0">Total Users</h6>
                    <h3 class="mb-0 fw-bold"><?php echo $totalUsers; ?></h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-3 stat-card" style="border-left-color: #10b981;">
            <div class="d-flex align-items-center">
                <div class="stat-icon bg-soft-success me-3">
                    <i class="bi bi-book"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-0">Courses</h6>
                    <h3 class="mb-0 fw-bold"><?php echo $totalCourses; ?></h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-3 stat-card" style="border-left-color: #f59e0b;">
            <div class="d-flex align-items-center">
                <div class="stat-icon bg-soft-warning me-3">
                    <i class="bi bi-person-check"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-0">Enrollments</h6>
                    <h3 class="mb-0 fw-bold"><?php echo $totalEnrollments; ?></h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-3 stat-card" style="border-left-color: #ef4444;">
            <div class="d-flex align-items-center">
                <div class="stat-icon bg-soft-danger me-3">
                    <i class="bi bi-cash-stack"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-0">Course Value</h6>
                    <h3 class="mb-0 fw-bold"><?php echo $totalRevenue; ?></h3>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="card-title mb-0">System Activity Log</h5>
                    <button class="btn btn-sm btn-outline-secondary">Download Report</button>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>Timestamp</th>
                                <th>User</th>
                                <th>Action</th>
                                <th>Module</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="small text-muted">2026-04-16 10:45</td>
                                <td>Alice Cooper (Student)</td>
                                <td>Enrolled in Course</td>
                                <td>PHP Basics</td>
                                <td><span class="badge bg-success rounded-pill px-3">Success</span></td>
                            </tr>
                            <tr>
                                <td class="small text-muted">2026-04-16 10:30</td>
                                <td>Bob Smith (Instructor)</td>
                                <td>Added Quiz</td>
                                <td>MySQL Intro</td>
                                <td><span class="badge bg-success rounded-pill px-3">Success</span></td>
                            </tr>
                            <tr>
                                <td class="small text-muted">2026-04-16 09:15</td>
                                <td>Jane Doe (Admin)</td>
                                <td>Deleted User #102</td>
                                <td>User Management</td>
                                <td><span class="badge bg-warning rounded-pill px-3">Warning</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'app/views/partials/footer.php'; ?>
