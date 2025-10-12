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
    :root {
        --primary-color: #3498db;
        --secondary-color: #2c3e50;
        --accent-color: #e74c3c;
        --light-bg: #f8f9fa;
        --dark-text: #2c3e50;
        --light-text: #7f8c8d;
    }

    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif, 'kalpurush', 'SolaimanLipi';
        color: var(--dark-text);
        background-color: #f5f7fa;
    }

    .navbar {
        background-color: white;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        padding: 15px 0;
    }

    .navbar-brand {
        font-weight: 700;
        color: var(--primary-color);
        font-size: 1.5rem;
    }

    .nav-link {
        font-weight: 500;
        color: var(--dark-text);
        margin: 0 10px;
    }

    .nav-link:hover {
        color: var(--primary-color);
    }

    .hero-section {
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        color: white;
        padding: 60px 0;
        margin-bottom: 40px;
        border-radius: 0 0 20px 20px;
    }

    .hero-title {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 20px;
    }

    .hero-subtitle {
        font-size: 1.2rem;
        margin-bottom: 30px;
        opacity: 0.9;
    }

    .btn-hero {
        background-color: white;
        color: var(--primary-color);
        font-weight: 600;
        padding: 12px 30px;
        border-radius: 30px;
        border: none;
        transition: all 0.3s;
    }

    .btn-hero:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    .section-title {
        font-weight: 700;
        color: var(--secondary-color);
        margin-bottom: 25px;
        padding-bottom: 10px;
        border-bottom: 3px solid var(--primary-color);
        display: inline-block;
    }

    .featured-post {
        background-color: white;
        /* border-radius: 10px; */
        overflow: hidden;
        /* box-shadow: 0 5px 15px rgba(0,0,0,0.05); */
        margin-bottom: 30px;
        transition: transform 0.3s;
    }

    .featured-post:hover {
        transform: translateY(-5px);
    }

    .featured-image {
        height: 350px;
        /* object-fit: cover; */
        width: 100%;
    }

    .featured-content {
        padding: 25px;
    }

    .featured-title {
        font-size: 1.8rem;
        font-weight: 700;
        margin-bottom: 15px;
        color: var(--secondary-color);
    }

    .featured-meta {
        color: var(--light-text);
        font-size: 0.9rem;
        margin-bottom: 15px;
    }

    .featured-excerpt {
        color: var(--light-text);
        line-height: 1.6;
        margin-bottom: 20px;
    }

    .btn-read {
        background-color: var(--primary-color);
        color: white;
        font-weight: 600;
        padding: 8px 20px;
        border-radius: 20px;
        border: none;
        transition: all 0.3s;
    }

    .btn-read:hover {
        background-color: var(--secondary-color);
        color: white;
    }

    .sidebar-posts {
        background-color: white;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        margin-bottom: 30px;
    }

    .sidebar-title {
        font-weight: 700;
        color: var(--secondary-color);
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 2px solid var(--primary-color);
    }

    .sidebar-post {
        display: flex;
        margin-bottom: 20px;
        padding-bottom: 20px;
        border-bottom: 1px solid #eee;
    }

    .sidebar-post:last-child {
        margin-bottom: 0;
        padding-bottom: 0;
        border-bottom: none;
    }

    .sidebar-image {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: 5px;
        margin-right: 15px;
    }

    .sidebar-content {
        flex: 1;
    }

    .sidebar-post-title {
        font-weight: 600;
        font-size: 1rem;
        margin-bottom: 5px;
        color: var(--secondary-color);
    }

    .sidebar-post-meta {
        color: var(--light-text);
        font-size: 0.8rem;
    }

    .recent-posts {
        background-color: white;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
    }

    .recent-post {
        display: flex;
        margin-bottom: 15px;
        padding-bottom: 15px;
        border-bottom: 1px solid #eee;
    }

    .recent-post:last-child {
        margin-bottom: 0;
        padding-bottom: 0;
        border-bottom: none;
    }

    .recent-image {
        width: 60px;
        height: 60px;
        object-fit: cover;
        border-radius: 5px;
        margin-right: 15px;
    }

    .recent-content {
        flex: 1;
    }

    .recent-post-title {
        font-weight: 600;
        font-size: 0.9rem;
        margin-bottom: 5px;
        color: var(--secondary-color);
    }

    .recent-post-meta {
        color: var(--light-text);
        font-size: 0.75rem;
    }

    .footer {
        background-color: var(--secondary-color);
        color: white;
        padding: 40px 0;
        margin-top: 50px;
    }

    .footer h5 {
        font-weight: 600;
        margin-bottom: 20px;
    }

    .footer-links a {
        color: #bdc3c7;
        display: block;
        margin-bottom: 10px;
        text-decoration: none;
        transition: color 0.3s;
    }

    .footer-links a:hover {
        color: white;
    }

    .copyright {
        background-color: #1a252f;
        color: #bdc3c7;
        padding: 15px 0;
        text-align: center;
        font-size: 0.9rem;
    }

    .category-badge {
        background-color: var(--primary-color);
        color: white;
        font-size: 0.7rem;
        padding: 3px 10px;
        border-radius: 20px;
        display: inline-block;
        margin-bottom: 10px;
    }

    .container {
        max-width: 1300px;
    }

    @media (max-width: 768px) {
        .hero-title {
            font-size: 2rem;
        }

        .featured-image {
            height: 250px;
        }

        .featured-title {
            font-size: 1.5rem;
        }
    }

    .space {
        margin-top: 3px;
    }
    </style>
</head>

<body>
    <!-- Navigation -->
    <!-- <nav class="navbar navbar-expand-lg navbar-light sticky-top">
        <div class="container">
            <a class="navbar-brand" href="#"><i class="fas fa-pen-fancy me-2"></i>WriteSpace</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="#">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Explore</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Categories</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Writers</a>
                    </li>
                </ul>
                <div class="d-flex">
                    <a href="#" class="btn btn-outline-primary me-2">Login</a>
                    <a href="#" class="btn btn-primary">Sign Up</a>
                </div>
            </div>
        </div>
    </nav> -->

    <?php include "navbar.php"; ?>
    <!-- Hero Section -->
    <!-- <div class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="hero-title">Share Your Stories With The World</h1>
                    <p class="hero-subtitle">Join our community of writers and readers. Discover amazing content or share your own creations.</p>
                    <a href="create-account.php" class="btn btn-hero">Start Writing Today</a>
                </div>
                <div class="col-lg-6 text-center">
                    <i class="fas fa-feather-alt" style="font-size: 10rem; opacity: 0.7;"></i>
                </div>
            </div>
        </div>
    </div> -->

    <div class="mt-5"></div>

    <!-- Main Content -->
    <div class="container">
        <div class="row">

            <!-- Featured Post and Sidebar Posts -->
            <div class="col-lg-5">
                <!-- <h2 class="section-title">Featured Story</h2> -->

                <?php
                // Fetch featured post (first post from latest)
                $stmt = $pdo->prepare("
                    SELECT p.*, u.full_name as author_name, g.name as group_name 
                    FROM posts p 
                    JOIN users u ON p.author_id = u.id 
                    JOIN post_groups g ON p.group_id = g.id 
                    WHERE p.is_published = 1 
                    ORDER BY p.created_at DESC 
                    LIMIT 1
                ");
                $stmt->execute();
                $featuredPost = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($featuredPost) {
                    echo '<a href="view.php?id=' . $featuredPost['id'] . '">';
                    echo '<div class="featured-post">';
                    if ($featuredPost['thumbnail']) {
                        echo '<img src="' . $featuredPost['thumbnail'] . '" alt="Featured Post" class="featured-image">';
                    }
                    echo '<div class="featured-content">';
                    echo '<span class="category-badge">' . $featuredPost['group_name'] . '</span>';
                    echo '<h3 class="featured-title">' . $featuredPost['title'] . '</h3>';
                    echo '<div class="featured-meta">';
                    echo '<span><i class="far fa-user me-1"></i> ' . $featuredPost['author_name'] . '</span>';
                    echo '<span class="ms-3"><i class="far fa-calendar me-1"></i> ' . date('F j, Y', strtotime($featuredPost['created_at'])) . '</span>';
                    // Estimate reading time (assuming 200 words per minute)
                    $wordCount = str_word_count(strip_tags($featuredPost['content']));
                    $readingTime = ceil($wordCount / 200);
                    echo '<span class="ms-3"><i class="far fa-clock me-1"></i> ' . $readingTime . ' min read</span>';
                    echo '</div>';
                    echo '<p class="featured-excerpt">' . substr(strip_tags($featuredPost['content']), 0, 250) . '...</p>';
                     
                    echo '</div>';
                    echo '</div>';
                    echo '</a>';
                } else {
                    echo '<div class="alert alert-info">No featured story available.</div>';
                }
                ?>

                <!-- Latest Writings Section -->
                <!-- <h2 class="section-title mt-5">Latest Writings</h2> -->

                <style>
                a {
                    text-decoration: none;
                }
                </style>

                <?php
                // Fetch latest posts (excluding the featured one)
                // $stmt = $pdo->prepare("
                //     SELECT p.*, u.full_name as author_name, g.name as group_name 
                //     FROM posts p 
                //     JOIN users u ON p.author_id = u.id 
                //     JOIN post_groups g ON p.group_id = g.id 
                //     WHERE p.is_published = 1 
                //     ORDER BY p.created_at DESC 
                //     LIMIT 1, 4
                // ");
                // $stmt->execute();
                // $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // if (count($posts) > 0) {
                //     foreach ($posts as $post) {
                //         echo '<a href="view.php?id=' . $post['id'] . '">';
                //         echo '<div class="sidebar-post">';
                //         if ($post['thumbnail']) {
                //             echo '<img src="' . $post['thumbnail'] . '" alt="' . $post['title'] . '" class="sidebar-image">';
                //         }
                //         echo '<div class="sidebar-content">';
                //         echo '<h4 class="sidebar-post-title">' . $post['title'] . '</h4>';
                //         echo '<div class="sidebar-post-meta">';
                //         echo '<span>' . $post['author_name'] . ' • ' . date('F j, Y', strtotime($post['created_at'])) . '</span>';
                //         echo '</div>';
                //         echo '<a href="view.php?id=' . $post['id'] . '" class="btn btn-sm btn-outline-primary mt-2">Read More</a>';
                //         echo '</div>';
                //         echo '</div>';
                //         echo '</a>';
                //     }
                // } else {
                //     echo '<div class="alert alert-info">No writings available yet.</div>';
                // }
                // ?>
            </div>

            <div class="col-lg-4">
                <div class="sidebar-posts">
                    <!-- <h3 class="sidebar-title">Popular Stories</h3> -->
                    <?php
                    // Fetch popular posts (based on view count or other metric)
                    $stmt = $pdo->prepare("
                        SELECT p.*, u.full_name as author_name 
                        FROM posts p 
                        JOIN users u ON p.author_id = u.id 
                        WHERE p.is_published = 1 
                        ORDER BY p.created_at DESC 
                        LIMIT 3
                    ");
                    $stmt->execute();
                    $popularPosts = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    if (count($popularPosts) > 0) {
                        foreach ($popularPosts as $post) {
                            echo '<a href="view.php?id=' . $post['id'] . '">';
                            echo '<div class="sidebar-post">';
                            if ($post['thumbnail']) {
                                echo '<img src="' . $post['thumbnail'] . '" alt="' . $post['title'] . '" class="sidebar-image">';
                            }
                            echo '<div class="sidebar-content">';
                            echo '<h4 class="sidebar-post-title">' . $post['title'] . '</h4>';
                            echo '<div class="sidebar-post-meta">';
                            echo '<p class="featured-excerpt text-primary">' . substr(strip_tags($post['content']), 0, 700) . '...</p>';
                            // echo '<span>' . $post['author_name'] . ' • ' . date('M j, Y', strtotime($post['created_at'])) . '</span>';
                            echo '</div>';
                            echo '</div>';
                            echo '</div>';
                            echo '</a>';
                            echo '<hr>';
                        }
                    } else {
                        echo '<p>No popular posts.</p>';
                    }
                    ?>
                </div>
            </div>





            <!-- Sidebar -->
            <div class="col-lg-3">
                <!-- Popular Stories -->
                <div class="sidebar-posts">
                    <h3 class="sidebar-title">Popular Stories</h3>
                    <?php
                    // Fetch popular posts (based on view count or other metric)
                    $stmt = $pdo->prepare("
                        SELECT p.*, u.full_name as author_name 
                        FROM posts p 
                        JOIN users u ON p.author_id = u.id 
                        WHERE p.is_published = 1 
                        ORDER BY p.created_at DESC 
                        LIMIT 3
                    ");
                    $stmt->execute();
                    $popularPosts = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    if (count($popularPosts) > 0) {
                        foreach ($popularPosts as $post) {
                            echo '<div class="sidebar-post">';
                            if ($post['thumbnail']) {
                                echo '<img src="' . $post['thumbnail'] . '" alt="' . $post['title'] . '" class="sidebar-image">';
                            } else {
                                echo '<img src="https://images.unsplash.com/photo-1517077304055-6e89abbf09b0?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=60" alt="' . $post['title'] . '" class="sidebar-image">';
                            }
                            echo '<div class="sidebar-content">';
                            echo '<h4 class="sidebar-post-title">' . $post['title'] . '</h4>';
                            echo '<div class="sidebar-post-meta">';
                            echo '<span>' . $post['author_name'] . ' • ' . date('M j, Y', strtotime($post['created_at'])) . '</span>';
                            echo '</div>';
                            echo '</div>';
                            echo '</div>';
                        }
                    } else {
                        echo '<p>No popular posts.</p>';
                    }
                    ?>
                </div>



                <!-- Join Community Card -->
                <!-- <div class="card mt-4">
                    <div class="card-body">
                        <h5 class="card-title">Join Our Community</h5>
                        <p class="card-text">Share your writings with the world and connect with other writers.</p>
                        <a href="create-account.php" class="btn btn-success">Create Account</a>
                    </div>
                </div> -->
            </div>

        </div>

        <!-- All Categories Section -->
        <div class="container text-center mt-5">
            <h2 class="section-title ">All Categories</h2>

            <?php
    // Fetch all categories
    $stmt = $pdo->prepare("SELECT * FROM post_groups");
    $stmt->execute();
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($categories as $category) {
        echo '<div class="category-section mb-5">';
        echo '<h3 class="mb-4" style="color: var(--secondary-color); font-weight: 600;">' . $category['name'] . '</h3>';
        
        // Fetch 4 posts from this category
        $stmt = $pdo->prepare("
            SELECT p.*, u.full_name as author_name 
            FROM posts p 
            JOIN users u ON p.author_id = u.id 
            WHERE p.group_id = ? AND p.is_published = 1 
            ORDER BY p.created_at DESC 
            LIMIT 4
        ");
        $stmt->execute([$category['id']]);
        $categoryPosts = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (count($categoryPosts) > 0) {
            echo '<div class="row">';
            foreach ($categoryPosts as $post) {
                echo '<div class="col-lg-3 col-md-6 mb-4">';
                echo '<a href="view.php?id=' . $post['id'] . '">';
                echo '<div class="card h-100 post-card">';
                
                if ($post['thumbnail']) {
                    echo '<img src="' . $post['thumbnail'] . '" class="card-img-top" alt="' . $post['title'] . '" style="height: 200px; object-fit: cover;">';
                } else {
                    echo '<img src="https://images.unsplash.com/photo-1517077304055-6e89abbf09b0?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=60" class="card-img-top" alt="' . $post['title'] . '" style="height: 200px; object-fit: cover;">';
                }
                
                echo '<div class="card-body d-flex flex-column">';
                echo '<h5 class="card-title">' . $post['title'] . '</h5>';
                echo '<p class="card-text flex-grow-1">' . substr(strip_tags($post['content']), 0, 100) . '...</p>';
                echo '<div class="mt-auto">';
                echo '<div class="d-flex justify-content-between align-items-center">';
                echo '<small class="text-muted">' . $post['author_name'] . '</small>';
                echo '<small class="text-muted">' . date('M j, Y', strtotime($post['created_at'])) . '</small>';
                echo '</div>';
                echo '</div>';
                echo '</div>';
                echo '</div>';
                echo '</a>';
                echo '</div>';
            }
            echo '</div>';
            
            // View All button for the category
            echo '<div class="text-center mt-3">';
            echo '<a href="category.php?id=' . $category['id'] . '" class="btn btn-outline-primary">View All ' . $category['name'] . ' Stories</a>';
            echo '</div>';
        } else {
            echo '<div class="alert alert-info">No posts available in this category yet.</div>';
        }
        
        echo '</div>';
        echo '<hr class="my-5">';
    }
    ?>
        </div>

        <style>
        .post-card {
            transition: transform 0.3s, box-shadow 0.3s;
            border: none;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }

        .post-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .card-title {
            font-weight: 600;
            color: var(--secondary-color);
            font-size: 1.1rem;
        }

        .card-text {
            color: var(--light-text);
            font-size: 0.9rem;
        }
        </style>
    </div>

    <!-- Recent Posts -->


    <!-- <div class="recent-posts"> -->
    <!-- <h3 class="sidebar-title">Recent Posts</h3> -->
    <?php
                    // Fetch recent posts for sidebar
                    // $stmt = $pdo->prepare("
                    //     SELECT p.id, p.title, p.thumbnail, p.created_at, u.full_name as author_name 
                    //     FROM posts p 
                    //     JOIN users u ON p.author_id = u.id 
                    //     WHERE p.is_published = 1 
                    //     ORDER BY p.created_at DESC 
                    //     LIMIT 5
                    // ");
                    // $stmt->execute();
                    // $recentPosts = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    // if (count($recentPosts) > 0) {
                    //     foreach ($recentPosts as $recent) {
                    //         echo '<div class="recent-post">';
                    //         if ($recent['thumbnail']) {
                    //             echo '<img src="' . $recent['thumbnail'] . '" alt="' . $recent['title'] . '" class="recent-image">';
                    //         } else {
                    //             echo '<img src="https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=60" alt="' . $recent['title'] . '" class="recent-image">';
                    //         }
                    //         echo '<div class="recent-content">';
                    //         echo '<h5 class="recent-post-title">' . $recent['title'] . '</h5>';
                    //         echo '<div class="recent-post-meta">';
                    //         echo '<span>' . $recent['author_name'] . ' • ' . date('M j', strtotime($recent['created_at'])) . '</span>';
                    //         echo '</div>';
                    //         echo '</div>';
                    //         echo '</div>';
                    //     }
                    // } else {
                    //     echo '<p>No recent posts.</p>';
                    // }
                    // ?>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4 mb-lg-0">
                    <h5>WriteSpace</h5>
                    <p>A platform for writers to share their stories and connect with readers worldwide. Join our
                        community today!</p>
                    <div class="mt-3">
                        <a href="#" class="text-light me-3"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="text-light me-3"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-light me-3"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-light"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6 mb-4 mb-lg-0">
                    <h5>Quick Links</h5>
                    <div class="footer-links">
                        <a href="#">Home</a>
                        <a href="#">About</a>
                        <a href="#">Categories</a>
                        <a href="#">Writers</a>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6 mb-4 mb-lg-0">
                    <h5>Resources</h5>
                    <div class="footer-links">
                        <a href="#">Blog</a>
                        <a href="#">Help Center</a>
                        <a href="#">Community</a>
                        <a href="#">Events</a>
                    </div>
                </div>
                <div class="col-lg-4">
                    <h5>Newsletter</h5>
                    <p>Subscribe to get updates on new stories and writing tips.</p>
                    <div class="input-group mb-3">
                        <input type="email" class="form-control" placeholder="Your email address">
                        <button class="btn btn-primary" type="button">Subscribe</button>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <div class="copyright">
        <div class="container">
            <p class="mb-0">© 2023 WriteSpace. All rights reserved.</p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>