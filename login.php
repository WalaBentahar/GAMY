<?php
// DESTROY ALL EXISTING SESSIONS FIRST
session_start();
session_unset();
session_destroy();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; }
        .login-box {
            max-width: 400px;
            margin: 100px auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-box">
            <h2 class="text-center mb-4">Admin Login</h2>
            
            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger">
                    <?php 
                    switch($_GET['error']) {
                        case '1': echo "Invalid username or password"; break;
                        case '2': echo "Database error"; break;
                        default: echo "Login required";
                    }
                    ?>
                </div>
            <?php endif; ?>

            <form action="auth.php" method="POST">
                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Login</button>
            </form>
        </div>
    </div>
</body>
</html>