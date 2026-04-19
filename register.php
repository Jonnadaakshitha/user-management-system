
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'includes/db.php';

if (isset($_POST['register'])) {
    $user = $_POST['username'];
    $email = $_POST['email'];
    // SECURITY: Hash the password before saving
    $pass = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $role = $_POST['role']; // User or Admin

    // SECURITY: Prepared Statement to prevent SQL Injection
    $stmt = mysqli_prepare($conn, "INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "ssss", $user, $email, $pass, $role);
    
    if (mysqli_stmt_execute($stmt)) {
        header("Location: login.php?msg=success");
    }
}
?>