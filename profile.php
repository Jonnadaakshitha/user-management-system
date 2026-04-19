<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }

$user_id = $_SESSION['user_id'];

// Handle the Upload
if (isset($_POST['update_profile'])) {
    $target_dir = "uploads/";
    $file_name = time() . "_" . basename($_FILES["profile_pic"]["name"]);
    $target_file = $target_dir . $file_name;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // VALIDATION: Check size (max 2MB) and type
    if ($_FILES["profile_pic"]["size"] > 2000000) {
        echo "File too large!";
    } elseif (!in_array($imageFileType, ['jpg', 'png', 'jpeg'])) {
        echo "Only JPG, JPEG & PNG allowed!";
    } else {
        if (move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $target_file)) {
            // Update Database
            $stmt = mysqli_prepare($conn, "UPDATE users SET profile_pic = ? WHERE id = ?");
            mysqli_stmt_bind_param($stmt, "si", $file_name, $user_id);
            mysqli_stmt_execute($stmt);
            echo "Profile updated!";
        }
    }
}

// Fetch Current Info
$res = mysqli_query($conn, "SELECT * FROM users WHERE id = $user_id");
$user = mysqli_fetch_assoc($res);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>My Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light p-5">
    <div class="card mx-auto" style="max-width: 400px;">
        <div class="card-body text-center">
            <img src="uploads/<?php echo $user['profile_pic']; ?>" class="rounded-circle mb-3" width="150" height="150">
            <h3><?php echo $user['username']; ?></h3>
            
            <form action="profile.php" method="POST" enctype="multipart/form-data" class="mt-4">
                <input type="file" name="profile_pic" class="form-control mb-2" required>
                <button type="submit" name="update_profile" class="btn btn-primary w-100">Upload New Picture</button>
            </form>
            <a href="logout.php" class="btn btn-link">Logout</a>
        </div>
    </div>
</body>
</html>