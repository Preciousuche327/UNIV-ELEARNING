<?php
// app/views/instructor/manage_courses.php
include __DIR__ . '/../partials/header.php';
include __DIR__ . '/../partials/sidebar.php';
?>

<div class="container-fluid p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">My Courses</h2>
            <p class="text-muted">Manage and monitor your courses</p>
        </div>
        <a href="?page=create-course" class="btn btn-primary">
            <i class="bi bi-plus-circle me-2"></i> New Course
        </a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Course Name</th>
                            <th>Students</th>
                            <th>Quizzes</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th class="pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($courses)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <p class="text-muted mb-0">No courses created yet. Create your first course to get started!</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($courses as $course): ?>
                                <tr>
                                    <td class="ps-4 fw-bold"><?php echo htmlspecialchars($course['CourseName']); ?></td>
                                    <td>
                                        <span class="badge bg-soft-primary"><?php echo $course['StudentCount']; ?></span>
                                    </td>
                                    <td>
                                        <span class="badge bg-soft-success"><?php echo isset($course['QuizCount']) ? $course['QuizCount'] : 0; ?></span>
                                    </td>
                                    <td>$<?php echo number_format($course['Price'], 2); ?></td>
                                    <td>
                                        <span class="badge bg-success">Active</span>
                                    </td>
                                    <td class="pe-4">
                                        <div class="btn-group btn-group-sm">
                                            <a href="?page=edit-course&id=<?php echo $course['CourseID']; ?>" class="btn btn-outline-primary">
                                                <i class="bi bi-pencil"></i> Edit
                                            </a>
                                            <a href="?page=create-quiz&course=<?php echo $course['CourseID']; ?>" class="btn btn-outline-success">
                                                <i class="bi bi-plus"></i> Quiz
                                            </a>
                                            <form method="POST" action="?page=delete-course" style="display: inline;">
                                                <input type="hidden" name="course_id" value="<?php echo $course['CourseID']; ?>">
                                                <button type="submit" class="btn btn-outline-danger" onclick="return confirm('Are you sure?')">
                                                    <i class="bi bi-trash"></i> Delete
                                                </button>
                                            </form>
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
</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>
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

<?php include __DIR__ . '/../partials/footer.php'; ?>

