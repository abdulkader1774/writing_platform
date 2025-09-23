<?php
require_once '../config.php';
redirectIfNotLoggedIn();
if (isAdmin()) {
    header("Location: ../admin/");
    exit();
}

$pageTitle = "Edit Profile - Writing Platform";
$error = '';
$success = '';

// Fetch current user data
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = sanitize($_POST['full_name']);
    $email = sanitize($_POST['email']);
    $bio = sanitize($_POST['bio']);
    
    // Handle profile picture upload
    $profile_pic = $user['profile_pic'];
    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === 0) {
        $uploadResult = uploadFile($_FILES['profile_pic'], '../uploads/');
        if ($uploadResult['success']) {
            $profile_pic = basename($uploadResult['file_path']);
            
            // Delete old profile picture if it's not the default
            if ($user['profile_pic'] && $user['profile_pic'] !== 'default-profile.jpg') {
                $old_file_path = '../uploads/' . $user['profile_pic'];
                if (file_exists($old_file_path)) {
                    unlink($old_file_path);
                }
            }
        } else {
            $error = $uploadResult['message'];
        }
    }
    
    if (empty($full_name) || empty($email)) {
        $error = "Full name and email are required.";
    } else {
        // Check if email is already taken by another user
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $stmt->execute([$email, $_SESSION['user_id']]);
        
        if ($stmt->rowCount() > 0) {
            $error = "Email is already taken by another user.";
        } else {
            // Update user profile
            $stmt = $pdo->prepare("UPDATE users SET full_name = ?, email = ?, bio = ?, profile_pic = ? WHERE id = ?");
            
            if ($stmt->execute([$full_name, $email, $bio, $profile_pic, $_SESSION['user_id']])) {
                $success = "Profile updated successfully!";
                // Update session data
                $_SESSION['full_name'] = $full_name;
                
                // Refresh user data
                $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
                $stmt->execute([$_SESSION['user_id']]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
            } else {
                $error = "Error updating profile. Please try again.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include 'navbar.php'; ?>
    
    <div class="container-fluid">
        <div class="row">
            <?php include 'sidebar.php'; ?>
            
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flexMD-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Edit Profile</h1>
                    <a href="profile.php" class="btn btn-secondary">Back to Profile</a>
                </div>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                <?php endif; ?>
                
                <div class="row">
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body text-center">
                                <img src="../<?php echo $user['profile_pic'] ? 'uploads/' . $user['profile_pic'] : 'assets/default-profile.jpg'; ?>" 
                                     class="rounded-circle mb-3" width="200" height="200" alt="Profile Picture" style="object-fit: cover;">
                                <h4><?php echo $user['full_name']; ?></h4>
                                <p class="text-muted">@<?php echo $user['username']; ?></p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-body">
                                <form method="POST" enctype="multipart/form-data">
                                    <div class="mb-3">
                                        <label for="profile_pic" class="form-label">Profile Picture</label>
                                        <input type="file" class="form-control" id="profile_pic" name="profile_pic" accept="image/*">
                                        <div class="form-text">Recommended size: 200x200 pixels. JPG, PNG, or GIF.</div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="full_name" class="form-label">Full Name</label>
                                        <input type="text" class="form-control" id="full_name" name="full_name" value="<?php echo $user['full_name']; ?>" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="email" name="email" value="<?php echo $user['email']; ?>" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="bio" class="form-label">Bio</label>
                                        <textarea class="form-control" id="bio" name="bio" rows="4"><?php echo $user['bio']; ?></textarea>
                                        <div class="form-text">Tell us about yourself and your writing interests.</div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Username</label>
                                        <input type="text" class="form-control" value="<?php echo $user['username']; ?>" disabled>
                                        <div class="form-text">Username cannot be changed.</div>
                                    </div>
                                    
                                    <button type="submit" class="btn btn-primary">Update Profile</button>
                                    <a href="profile.php" class="btn btn-secondary">Cancel</a>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html>