<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'includes/db.php';

$error_msg = "";

if (isset($_POST['register'])) {
    $user = $_POST['username'];
    $email = $_POST['email'];
    $pass = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $role = $_POST['role'];

    try {
        // SECURITY: Prepared Statement
        $stmt = mysqli_prepare($conn, "INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "ssss", $user, $email, $pass, $role);
        
        if (mysqli_stmt_execute($stmt)) {
            header("Location: login.php?msg=success");
            exit();
        }
    } catch (mysqli_sql_exception $e) {
        // Check for duplicate entry error code (1062)
        if ($e->getCode() == 1062) {
            $error_msg = "This email is already registered. Please login or use a different email.";
        } else {
            $error_msg = "Something went wrong. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | Task 3</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f4f7f6; height: 100vh; display: flex; align-items: center; }
        .register-card { width: 100%; max-width: 400px; margin: auto; }
    </style>
</head>
<body>

<div class="container">
    <div class="card register-card shadow-sm">
        <div class="card-body p-4">
            <h3 class="text-center mb-4">Create Account</h3>
            <form action="register.php" method="POST">
                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Role</label>
                    <select name="role" class="form-select">
                        <option value="user">User</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <button type="submit" name="register" class="btn btn-primary w-100">Register</button>
            </form>
            <p class="text-center mt-3">Already have an account? <a href="login.php">Login</a></p>
        </div>
    </div>
</div>

</body>
</html>