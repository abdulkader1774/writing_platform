<?php
require_once 'config.php';
$pageTitle = "Home - Writing Platform";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .post-card {
            transition: transform 0.3s;
            margin-bottom: 20px;
        }
        .post-card:hover {
            transform: translateY(-5px);
        }
        .thumbnail {
            height: 200px;
            object-fit: cover;
        }
        .recent-posts {
            background-color: #f8f9fa;
            border-radius: 5px;
            padding: 15px;
        }
        .recent-post-item {
            border-bottom: 1px solid #eee;
            padding: 10px 0;
        }
        .recent-post-item:last-child {
            border-bottom: none;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container mt-4">
        <div class="row">
            <div class="col-lg-8">
                <h2 class="mb-4">Latest Writings</h2>
                
                <?php
                // Fetch latest posts
                $stmt = $pdo->prepare("
                    SELECT p.*, u.full_name as author_name, g.name as group_name 
                    FROM posts p 
                    JOIN users u ON p.author_id = u.id 
                    JOIN post_groups g ON p.group_id = g.id 
                    WHERE p.is_published = 1 
                    ORDER BY p.created_at DESC 
                    LIMIT 10
                ");
                $stmt->execute();
                $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                if (count($posts) > 0) {
                    foreach ($posts as $post) {
                        echo '<div class="card post-card mb-4">';
                        echo '<div class="row g-0">';
                        if ($post['thumbnail']) {
                            echo '<div class="col-md-4">';
                            echo '<img src="' . $post['thumbnail'] . '" class="img-fluid rounded-start thumbnail" alt="' . $post['title'] . '">';
                            echo '</div>';
                            echo '<div class="col-md-8">';
                        } else {
                            echo '<div class="col-md-12">';
                        }
                        echo '<div class="card-body">';
                        echo '<h5 class="card-title">' . $post['title'] . '</h5>';
                        echo '<p class="card-text"><small class="text-muted">By ' . $post['author_name'] . ' in ' . $post['group_name'] . '</small></p>';
                        echo '<p class="card-text">' . substr(strip_tags($post['content']), 0, 200) . '...</p>';
                        echo '<a href="view.php?id=' . $post['id'] . '" class="btn btn-primary">Read More</a>';
                        echo '</div>';
                        echo '</div>';
                        echo '</div>';
                        echo '</div>';
                    }
                } else {
                    echo '<div class="alert alert-info">No writings available yet.</div>';
                }
                ?>
            </div>
            
            <div class="col-lg-4">
                <div class="recent-posts mb-4">
                    <h4>Recently Published</h4>
                    <?php
                    // Fetch recent posts for sidebar
                    $stmt = $pdo->prepare("
                        SELECT p.id, p.title 
                        FROM posts p 
                        WHERE p.is_published = 1 
                        ORDER BY p.created_at DESC 
                        LIMIT 5
                    ");
                    $stmt->execute();
                    $recentPosts = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    if (count($recentPosts) > 0) {
                        foreach ($recentPosts as $recent) {
                            echo '<div class="recent-post-item">';
                            echo '<a href="view.php?id=' . $recent['id'] . '">' . $recent['title'] . '</a>';
                            echo '</div>';
                        }
                    } else {
                        echo '<p>No recent posts.</p>';
                    }
                    ?>
                </div>
                
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Join Our Community</h5>
                        <p class="card-text">Share your writings with the world and connect with other writers.</p>
                        <a href="create-account.php" class="btn btn-success">Create Account</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html>