<?php
require_once '../config.php';
redirectIfNotLoggedIn();
if (isAdmin()) {
    header("Location: ../admin/");
    exit();
}

$pageTitle = "User Dashboard - Writing Platform";
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
                    <h1 class="h2">Dashboard</h1>
                </div>
                
                <div class="row">
                    <div class="col-md-4">
                        <div class="card text-white bg-primary mb-3">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h5 class="card-title">My Posts</h5>
                                        <?php
                                        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM posts WHERE author_id = ?");
                                        $stmt->execute([$_SESSION['user_id']]);
                                        $postCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
                                        ?>
                                        <h2><?php echo $postCount; ?></h2>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-file-alt fa-3x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card text-white bg-success mb-3">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h5 class="card-title">Total Views</h5>
                                        <?php
                                        $stmt = $pdo->prepare("SELECT SUM(views) as total FROM posts WHERE author_id = ?");
                                        $stmt->execute([$_SESSION['user_id']]);
                                        $viewCount = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?: 0;
                                        ?>
                                        <h2><?php echo $viewCount; ?></h2>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-eye fa-3x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card text-white bg-info mb-3">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h5 class="card-title">Messages</h5>
                                        <?php
                                        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM messages WHERE receiver_id = ? AND is_read = 0");
                                        $stmt->execute([$_SESSION['user_id']]);
                                        $messageCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
                                        ?>
                                        <h2><?php echo $messageCount; ?></h2>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-envelope fa-3x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5>Recent Posts</h5>
                            </div>
                            <div class="card-body">
                                <?php
                                $stmt = $pdo->prepare("
                                    SELECT p.*, g.name as group_name 
                                    FROM posts p 
                                    JOIN post_groups g ON p.group_id = g.id 
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
                                        echo '<a href="../view.php?id=' . $post['id'] . '">' . $post['title'] . '</a>';
                                        echo '<span class="badge bg-primary rounded-pill">' . $post['group_name'] . '</span>';
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
                    
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5>Quick Actions</h5>
                            </div>
                            <div class="card-body">
                                <div class="d-grid gap-2">
                                    <a href="create-post.php" class="btn btn-primary">Create New Post</a>
                                    <a href="post-list.php" class="btn btn-secondary">Manage Posts</a>
                                    <a href="chat.php" class="btn btn-info">Message Admin</a>
                                    <a href="profile.php" class="btn btn-success">Edit Profile</a>
                                </div>
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