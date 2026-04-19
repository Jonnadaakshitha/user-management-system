<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'includes/db.php';

$message = "";
$messageClass = "";

if (isset($_POST['register'])) {
    $user = $_POST['username'];
    $email = $_POST['email'];
    // SECURITY: Hash the password before saving
    $pass = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $role = $_POST['role']; 

    try {
        // SECURITY: Prepared Statement to prevent SQL Injection
        $stmt = mysqli_prepare($conn, "INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "ssss", $user, $email, $pass, $role);
        
        if (mysqli_stmt_execute($stmt)) {
            header("Location: login.php?msg=success");
            exit();
        }
    } catch (mysqli_sql_exception $e) {
        // Handle Duplicate Email Error (Error Code 1062)
        if ($e->getCode() == 1062) {
            $message = "Error: This email is already registered!";
            $messageClass = "alert-danger";
        } else {
            $message = "Something went wrong. Please try again.";
            $messageClass = "alert-warning";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | User Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; height: 100vh; display: flex; align-items: center; }
        .register-card { width: 100%; max-width: 450px; margin: auto; border: none; border-radius: 15px; }
    </style>
</head>
<body>

<div class="container">
    <div class="card register-card shadow-lg">
        <div class="card-body p-5">
            <h3 class="text-center mb-4 fw-bold">Create Account</h3>

            <?php if ($message): ?>
                <div class="alert <?php echo $messageClass; ?> alert-dismissible fade show" role="alert">
                    <?php echo $message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <form action="register.php" method="POST">
                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" placeholder="Choose a username" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email Address</label>
                    <input type="email" name="email" class="form-control" placeholder="name@example.com" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" placeholder="Create a strong password" required>
                </div>
                <div class="mb-4">
                    <label class="form-label">Assign Role</label>
                    <select name="role" class="form-select">
                        <option value="user" selected>Standard User</option>
                        <option value="admin">Administrator</option>
                    </select>
                </div>
                <button type="submit" name="register" class="btn btn-primary w-100 py-2 fw-bold">Register Now</button>
            </form>
            
            <p class="text-center mt-4 mb-0 text-muted">
                Already have an account? <a href="login.php" class="text-decoration-none">Login here</a>
            </p>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>