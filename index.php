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
        .hero-section {
            background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('images/hero-bg.jpg') center/cover no-repeat;
            color: white;
            padding: 100px 0;
            text-align: center;
            margin-bottom: 40px;
        }
        .hero-section h1 {
            font-size: 3rem;
            margin-bottom: 20px;
            font-weight: 700;
        }
        .hero-section p {
            font-size: 1.2rem;
            max-width: 600px;
            margin: 0 auto 30px;
        }
        .post-card {
            transition: transform 0.3s;
            margin-bottom: 20px;
            border: none;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .post-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0,0,0,0.1);
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
        .featured-post {
            border-left: 4px solid #0d6efd;
        }
        .section-title {
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <!-- Hero Section with Featured Image -->
    <!-- <div class="hero-section">
        <div class="container">
            <h1>Share Your Stories With The World</h1>
            <p>Join our community of writers and readers. Discover amazing content or share your own creations.</p>
            <a href="create-account.php" class="btn btn-primary btn-lg me-2">Get Started</a>
            <a href="#latest-writings" class="btn btn-outline-light btn-lg">Explore Writings</a>
        </div>
    </div> -->

    <div class="container mt-4">
        <div class="row">
            <div class="col-lg-8">
                <h2 class="section-title" id="latest-writings">Latest Writings</h2>
                
                <?php
                // Fetch latest posts
                $stmt = $pdo->prepare("
                    SELECT p.*, u.full_name as author_name, g.name as group_name 
                    FROM posts p 
                    JOIN users u ON p.author_id = u.id 
                    JOIN post_groups g ON p.group_id = g.id 
                    WHERE p.is_published = 1 
                    ORDER BY p.created_at DESC 
                    LIMIT 5
                ");
                $stmt->execute();
                $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if (count($posts) > 0) {
                    foreach ($posts as $post) {
                        echo '<div class="card post-card">';
                        echo '<div class="row g-0">';
                        if ($post['thumbnail']) {
                            echo '<div class="col-md-4">';
                            echo '<img src="' . $post['thumbnail'] . '" class="img-fluid rounded-start thumbnail w-100" alt="' . $post['title'] . '">';
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

    <div class="" style="width:400px">
    <img class="card-img-top" src="uploads/1758541743_aaa.png" alt="Card image" style="width:100%">
    <div class="card-body">
      <h4 class="card-title">John Doe</h4>
      <p class="card-text">Some example text some example text. John Doe is an architect and engineer</p>
      <a href="#" class="btn btn-primary">See Profile</a>
    </div>
  </div>

    <?php include 'footer.php'; ?>
</body>
</html>