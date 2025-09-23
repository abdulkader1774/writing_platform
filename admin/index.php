<?php
require_once '../config.php';
redirectIfNotLoggedIn();
redirectIfNotAdmin();

$pageTitle = "Admin Dashboard - Writing Platform";

// Get statistics
$stmt = $pdo->prepare("SELECT COUNT(*) as count FROM users WHERE role = 'user'");
$stmt->execute();
$userCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

$stmt = $pdo->prepare("SELECT COUNT(*) as count FROM posts");
$stmt->execute();
$postCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

$stmt = $pdo->prepare("SELECT COUNT(*) as count FROM comments");
$stmt->execute();
$commentCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

$stmt = $pdo->prepare("SELECT SUM(views) as total FROM posts");
$stmt->execute();
$totalViews = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?: 0;
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
                    <h1 class="h2">Admin Dashboard</h1>
                </div>
                
                <div class="row">
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-primary shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                            Total Users</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $userCount; ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-users fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-success shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                            Total Posts</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $postCount; ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-file-alt fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-info shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                            Total Comments</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $commentCount; ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-comments fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-warning shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                            Total Views</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $totalViews; ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-eye fa-2x text-gray-300"></i>
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
                                    SELECT p.*, u.username, g.name as group_name 
                                    FROM posts p 
                                    JOIN users u ON p.author_id = u.id 
                                    JOIN post_groups g ON p.group_id = g.id 
                                    ORDER BY p.created_at DESC 
                                    LIMIT 5
                                ");
                                $stmt->execute();
                                $recentPosts = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                
                                if (count($recentPosts) > 0) {
                                    echo '<div class="list-group">';
                                    foreach ($recentPosts as $post) {
                                        $statusBadge = $post['is_published'] ? 
                                            '<span class="badge bg-success">Published</span>' : 
                                            '<span class="badge bg-warning">Draft</span>';
                                        
                                        echo '<a href="../view.php?id=' . $post['id'] . '" class="list-group-item list-group-item-action">';
                                        echo '<div class="d-flex w-100 justify-content-between">';
                                        echo '<h6 class="mb-1">' . $post['title'] . '</h6>';
                                        echo '<small>' . $statusBadge . '</small>';
                                        echo '</div>';
                                        echo '<p class="mb-1">By ' . $post['username'] . ' in ' . $post['group_name'] . '</p>';
                                        echo '<small>Views: ' . $post['views'] . '</small>';
                                        echo '</a>';
                                    }
                                    echo '</div>';
                                } else {
                                    echo '<p>No posts yet.</p>';
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
                                    <a href="post-list.php" class="btn btn-primary">Manage Posts</a>
                                    <a href="user-list.php" class="btn btn-success">Manage Users</a>
                                    <a href="add-post-group.php" class="btn btn-info">Add Category</a>
                                    <a href="chat.php" class="btn btn-warning">User Messages</a>
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