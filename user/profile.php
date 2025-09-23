<?php
require_once '../config.php';
redirectIfNotLoggedIn();
if (isAdmin()) {
    header("Location: ../admin/");
    exit();
}

// Fetch user details
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch user's post count
$stmt = $pdo->prepare("SELECT COUNT(*) as count FROM posts WHERE author_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$postCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

// Fetch user's total views
$stmt = $pdo->prepare("SELECT SUM(views) as total FROM posts WHERE author_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$totalViews = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?: 0;

$pageTitle = "Profile - Writing Platform";
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
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">My Profile</h1>
                    <a href="edit-profile.php" class="btn btn-primary">Edit Profile</a>
                </div>
                
                <div class="row">
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body text-center">
                                <img src="../<?php echo $user['profile_pic'] ? 'uploads/' . $user['profile_pic'] : 'assets/default-profile.jpg'; ?>" 
                                     class="rounded-circle mb-3" width="150" height="150" alt="Profile Picture">
                                <h4><?php echo $user['full_name']; ?></h4>
                                <p class="text-muted">@<?php echo $user['username']; ?></p>
                                <div class="d-flex justify-content-around mt-4">
                                    <div>
                                        <h5><?php echo $postCount; ?></h5>
                                        <p class="text-muted">Posts</p>
                                    </div>
                                    <div>
                                        <h5><?php echo $totalViews; ?></h5>
                                        <p class="text-muted">Views</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h5>Profile Information</h5>
                            </div>
                            <div class="card-body">
                                <table class="table table-borderless">
                                    <tr>
                                        <th width="30%">Full Name:</th>
                                        <td><?php echo $user['full_name']; ?></td>
                                    </tr>
                                    <tr>
                                        <th>Username:</th>
                                        <td><?php echo $user['username']; ?></td>
                                    </tr>
                                    <tr>
                                        <th>Email:</th>
                                        <td><?php echo $user['email']; ?></td>
                                    </tr>
                                    <tr>
                                        <th>Member Since:</th>
                                        <td><?php echo date('F j, Y', strtotime($user['created_at'])); ?></td>
                                    </tr>
                                    <?php if ($user['bio']): ?>
                                    <tr>
                                        <th>Bio:</th>
                                        <td><?php echo $user['bio']; ?></td>
                                    </tr>
                                    <?php endif; ?>
                                </table>
                            </div>
                        </div>
                        
                        <div class="card mt-4">
                            <div class="card-header">
                                <h5>Recent Activity</h5>
                            </div>
                            <div class="card-body">
                                <?php
                                $stmt = $pdo->prepare("
                                    SELECT p.title, p.created_at, p.views 
                                    FROM posts p 
                                    WHERE p.author_id = ? 
                                    ORDER BY p.created_at DESC 
                                    LIMIT 5
                                ");
                                $stmt->execute([$_SESSION['user_id']]);
                                $recentPosts = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                
                                if (count($recentPosts) > 0) {
                                    echo '<ul class="list-group">';
                                    foreach ($recentPosts as $post) {
                                        echo '<li class="list-group-item d-flex justify-content-between align-items-center">';
                                        echo '<div>';
                                        echo '<h6 class="mb-1">' . $post['title'] . '</h6>';
                                        echo '<small class="text-muted">Published on ' . date('M j, Y', strtotime($post['created_at'])) . '</small>';
                                        echo '</div>';
                                        echo '<span class="badge bg-primary rounded-pill">' . $post['views'] . ' views</span>';
                                        echo '</li>';
                                    }
                                    echo '</ul>';
                                } else {
                                    echo '<p>No posts yet. <a href="create-post.php">Create your first post</a></p>';
                                }
                                ?>
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