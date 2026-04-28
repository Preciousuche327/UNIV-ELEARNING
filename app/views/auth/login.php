<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?php echo APP_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; display: flex; align-items: center; height: 100vh; }
        .login-card { max-width: 400px; width: 100%; margin: auto; border: none; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        .btn-primary { background: #6366f1; border: none; }
        .btn-primary:hover { background: #4f46e5; }
    </style>
</head>
<body>
    <div class="card login-card p-4">
        <h3 class="text-center mb-4"><?php echo APP_NAME; ?></h3>
        <p class="text-muted text-center">Login to your account</p>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" class="form-control" required placeholder="admin@univ.edu">
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required placeholder="admin123">
            </div>
            <button type="submit" class="btn btn-primary w-100 py-2">Sign In</button>
        </form>

        <div class="mt-4 text-center">
            <p>Don't have an account? <a href="?page=register" class="text-decoration-none">Register here</a></p>
        </div>
    </div>
</body>
</html>
