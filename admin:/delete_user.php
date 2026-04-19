<?php
session_start();
include '../includes/db.php';

if ($_SESSION['role'] == 'admin' && isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // SECURITY: Prepared Statement
    $stmt = mysqli_prepare($conn, "DELETE FROM users WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    
    if (mysqli_stmt_execute($stmt)) {
        header("Location: dashboard.php?msg=deleted");
    }
}
?>