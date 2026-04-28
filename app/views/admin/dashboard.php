<?php
// app/views/admin/dashboard.php
include __DIR__ . '/../partials/header.php';
include __DIR__ . '/../partials/sidebar.php';

// Use stats passed from controller
$totalUsers = $stats['total_users'] ?? 0;
$totalCourses = $stats['total_courses'] ?? 0;
$totalEnrollments = $stats['total_enrollments'] ?? 0;
$totalQuizzes = $stats['total_quizzes'] ?? 0;
$totalInstructors = $stats['total_instructors'] ?? 0;
$totalStudents = $stats['total_students'] ?? 0;
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
                    <i class="bi bi-patch-question"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-0">Total Quizzes</h6>
                    <h3 class="mb-0 fw-bold"><?php echo $totalQuizzes; ?></h3>
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
                    <h5 class="card-title mb-0">System Overview</h5>
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-sm btn-outline-secondary">Day</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary active">Month</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary">Year</button>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>Metric</th>
                                <th>Count</th>
                                <th>Percentage</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="fw-bold">Total Instructors</td>
                                <td><?php echo $totalInstructors; ?></td>
                                <td><?php echo $totalUsers > 0 ? round(($totalInstructors / $totalUsers) * 100, 1) : 0; ?>%</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Total Students</td>
                                <td><?php echo $totalStudents; ?></td>
                                <td><?php echo $totalUsers > 0 ? round(($totalStudents / $totalUsers) * 100, 1) : 0; ?>%</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Average Students per Course</td>
                                <td><?php echo $totalCourses > 0 ? round($totalEnrollments / $totalCourses, 1) : 0; ?></td>
                                <td>Per Course</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Average Quizzes per Course</td>
                                <td><?php echo $totalCourses > 0 ? round($totalQuizzes / $totalCourses, 1) : 0; ?></td>
                                <td>Per Course</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
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

<?php include __DIR__ . '/../partials/footer.php'; ?>

