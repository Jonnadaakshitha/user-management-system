<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$message = "";
$messageClass = "";

// 1. Fetch current user data
$query = "SELECT username, email, profile_pic FROM users WHERE id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

// 2. Handle Update Logic
if (isset($_POST['update_profile'])) {
    $new_username = $_POST['username'];
    $profile_pic = $user['profile_pic']; // Keep old one by default

    // Handle File Upload
    if (!empty($_FILES['image']['name'])) {
        $file_name = $_FILES['image']['name'];
        $file_size = $_FILES['image']['size'];
        $file_tmp = $_FILES['image']['tmp_name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        
        $allowed = array("jpeg", "jpg", "png");
        
        if (in_array($file_ext, $allowed) === false) {
            $message = "Extension not allowed, please choose a JPEG or PNG file.";
            $messageClass = "alert-danger";
        } elseif ($file_size > 2097152) {
            $message = "File size must be exactly or less than 2MB";
            $messageClass = "alert-danger";
        } else {
            // Success: Rename and move
            $new_file_name = time() . "_" . $user_id . "." . $file_ext;
            move_uploaded_file($file_tmp, "uploads/" . $new_file_name);
            $profile_pic = $new_file_name;
        }
    }

    if ($messageClass !== "alert-danger") {
        $update_query = "UPDATE users SET username = ?, profile_pic = ? WHERE id = ?";
        $update_stmt = mysqli_prepare($conn, $update_query);
        mysqli_stmt_bind_param($update_stmt, "ssi", $new_username, $profile_pic, $user_id);
        
        if (mysqli_stmt_execute($update_stmt)) {
            $_SESSION['username'] = $new_username; // Update session name
            header("Location: profile.php?msg=updated");
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="card mx-auto shadow" style="max-width: 500px;">
        <div class="card-body p-4">
            <h3 class="text-center mb-4">Edit Your Profile</h3>
            
            <?php if ($message): ?>
                <div class="alert <?php echo $messageClass; ?>"><?php echo $message; ?></div>
            <?php endif; ?>

            <form action="edit_profile.php" method="POST" enctype="multipart/form-data">
                <div class="text-center mb-4">
                    <img src="uploads/<?php echo $user['profile_pic'] ?: 'default.png'; ?>" class="rounded-circle border" width="120" height="120" style="object-fit: cover;">
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Email (Locked)</label>
                    <input type="text" class="form-control" value="<?php echo $user['email']; ?>" disabled>
                </div>

                <div class="mb-4">
                    <label class="form-label">Update Profile Picture</label>
                    <input type="file" name="image" class="form-control">
                    <small class="text-muted">Max 2MB (JPG, PNG)</small>
                </div>

                <button type="submit" name="update_profile" class="btn btn-primary w-100">Save Changes</button>
                <a href="profile.php" class="btn btn-link w-100 mt-2 text-decoration-none">Cancel</a>
            </form>
        </div>
    </div>
</div>
</body>
</html>