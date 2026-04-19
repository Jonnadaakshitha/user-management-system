<?php
session_start();
include '../includes/db.php';

if ($_SESSION['role'] !== 'admin') { header("Location: ../login.php"); exit(); }

$id = $_GET['id'];
if (isset($_POST['update'])) {
    $username = $_POST['username'];
    $role = $_POST['role'];

    // SECURITY: Prepared Statement for Update
    $stmt = mysqli_prepare($conn, "UPDATE users SET username = ?, role = ? WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "ssi", $username, $role, $id);
    
    if (mysqli_stmt_execute($stmt)) {
        header("Location: dashboard.php?msg=updated");
    }
}

$res = mysqli_query($conn, "SELECT * FROM users WHERE id = $id");
$user = mysqli_fetch_assoc($res);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-5">
    <div class="container" style="max-width: 500px;">
        <h3>Edit User: <?php echo $user['username']; ?></h3>
        <form method="POST">
            <div class="mb-3">
                <label>Username</label>
                <input type="text" name="username" class="form-control" value="<?php echo $user['username']; ?>">
            </div>
            <div class="mb-3">
                <label>Role</label>
                <select name="role" class="form-control">
                    <option value="user" <?php if($user['role'] == 'user') echo 'selected'; ?>>User</option>
                    <option value="admin" <?php if($user['role'] == 'admin') echo 'selected'; ?>>Admin</option>
                </select>
            </div>
            <button type="submit" name="update" class="btn btn-success">Update User</button>
            <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</body>
</html>