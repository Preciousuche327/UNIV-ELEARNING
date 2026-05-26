<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - <?php echo defined('APP_NAME') ? APP_NAME : 'Univ E-Learning'; ?></title>
    <link rel="icon" type="image/png" href="public/images/icons/icon-192.png">
    <link rel="apple-touch-icon" href="public/images/icons/icon-192.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body class="bg-light">
    <div class="auth-wrapper">
        <div class="col-12 d-flex align-items-center justify-content-center p-4 p-md-5 bg-white">
            <div class="w-100" style="max-width: 450px;">
                <div class="text-center mb-4">
                    <div class="d-inline-flex align-items-center justify-content-center bg-soft-primary text-primary rounded-circle mb-3" style="width: 70px; height: 70px;">
                        <i class="bi bi-shield-lock fs-1"></i>
                    </div>
                    <h2 class="fw-bold text-dark">Reset Password</h2>
                    <p class="text-muted">Enter your account email or username and choose a new password.</p>
                </div>

                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <?php foreach ($errors as $error): ?>
                            <div><?php echo htmlspecialchars($error); ?></div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <form method="POST" class="needs-validation" novalidate>
                    <div class="form-floating mb-3">
                        <input type="text" name="email" class="form-control" id="emailInput" placeholder="name@example.com" required>
                        <label for="emailInput"><i class="bi bi-envelope me-2 text-muted"></i>Email or Username</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="password" name="password" class="form-control" id="passwordInput" placeholder="New Password" required minlength="6">
                        <label for="passwordInput"><i class="bi bi-lock me-2 text-muted"></i>New Password</label>
                    </div>
                    <div class="form-floating mb-4">
                        <input type="password" name="confirm_password" class="form-control" id="confirmPasswordInput" placeholder="Confirm Password" required minlength="6">
                        <label for="confirmPasswordInput"><i class="bi bi-lock-fill me-2 text-muted"></i>Confirm Password</label>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 py-3 mb-4 fw-bold">Update Password</button>
                    <div class="text-center">
                        <a href="?page=login" class="text-primary fw-bold text-decoration-none">Back to sign in</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
