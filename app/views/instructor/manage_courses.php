<?php
// app/views/instructor/manage_courses.php
include 'app/views/partials/header.php';
include 'app/views/partials/sidebar.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold">Manage Courses</h2>
        <p class="text-muted">Create, edit, and organize your course curriculum.</p>
    </div>
    <a href="?page=create-course" class="btn btn-primary">+ Create New Course</a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Course Name</th>
                        <th>Price</th>
                        <th>Created Date</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($courses)): ?>
                        <tr>
                            <td colspan="4" class="text-center py-5">
                                <p class="text-muted mb-0">No courses available. Start by creating one!</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($courses as $course): ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold"><?php echo htmlspecialchars($course['CourseName']); ?></div>
                                    <div class="small text-muted"><?php echo substr(htmlspecialchars($course['Description']), 0, 80); ?>...</div>
                                </td>
                                <td>$<?php echo number_format($course['Price'], 2); ?></td>
                                <td><?php echo date('M d, Y', strtotime($course['CreatedAt'])); ?></td>
                                <td class="text-end pe-4">
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-light border" type="button" data-bs-toggle="dropdown">
                                            <i class="bi bi-three-dots"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                                            <li><a class="dropdown-item" href="?page=course-details&id=<?php echo $course['CourseID']; ?>"><i class="bi bi-eye me-2"></i> View</a></li>
                                            <li><a class="dropdown-item" href="#"><i class="bi bi-pencil me-2"></i> Edit Course</a></li>
                                            <li><a class="dropdown-item text-primary" href="#"><i class="bi bi-plus-circle me-2"></i> Add Content</a></li>
                                            <li><a class="dropdown-item text-info" href="#"><i class="bi bi-patch-question me-2"></i> Add Quiz</a></li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li><a class="dropdown-item text-danger" href="#"><i class="bi bi-trash me-2"></i> Delete</a></li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'app/views/partials/footer.php'; ?>
