<?php
session_start();
include 'includes/db.php';

$message = "";
$messageClass = "";

// Check for success message from register.php
if (isset($_GET['msg']) && $_GET['msg'] == 'success') {
    $message = "Registration successful! Please login.";
    $messageClass = "alert-success";
}

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $pass = $_POST['password'];

    // SECURITY: Prepared Statement to fetch user
    $stmt = mysqli_prepare($conn, "SELECT id, username, password, role FROM users WHERE email = ?");
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($user = mysqli_fetch_assoc($result)) {
        // VERIFY: Check hashed password
        if (password_verify($pass, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            // REDIRECT: Based on role
            if ($user['role'] == 'admin') {
                header("Location: admin/dashboard.php");
            } else {
                header("Location: profile.php");
            }
            exit();
        } else {
            $message = "Invalid password!";
            $messageClass = "alert-danger";
        }
    } else {
        $message = "No account found with that email.";
        $messageClass = "alert-danger";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | User Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            height: 100vh;
            display: flex;
            align-items: center;
        }
        .login-card {
            border: none;
            border-radius: 15px;
            width: 100%;
            max-width: 400px;
            margin: auto;
        }
        .btn-primary {
            background-color: #0d6efd;
            border: none;
            padding: 10px;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="card login-card shadow-lg">
        <div class="card-body p-5">
            <h3 class="text-center mb-4 fw-bold text-dark">Welcome Back</h3>
            
            <?php if ($message): ?>
                <div class="alert <?php echo $messageClass; ?> alert-dismissible fade show py-2" role="alert">
                    <small><?php echo $message; ?></small>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <form action="login.php" method="POST">
                <div class="mb-3">
                    <label class="form-label text-muted">Email Address</label>
                    <input type="email" name="email" class="form-control form-control-lg" placeholder="Enter your email" required>
                </div>
                <div class="mb-4">
                    <label class="form-label text-muted">Password</label>
                    <input type="password" name="password" class="form-control form-control-lg" placeholder="Enter password" required>
                </div>
                <button type="submit" name="login" class="btn btn-primary w-100 fw-bold shadow-sm">Sign In</button>
            </form>

            <div class="text-center mt-4">
                <span class="text-muted small">Don't have an account?</span>
                <a href="register.php" class="text-decoration-none small fw-bold"> Create one</a>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>