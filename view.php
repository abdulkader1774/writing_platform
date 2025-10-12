<?php
require_once 'config.php';

$post_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch post details
$stmt = $pdo->prepare("
    SELECT p.*, u.full_name as author_name, g.name as group_name 
    FROM posts p 
    JOIN users u ON p.author_id = u.id 
    JOIN post_groups g ON p.group_id = g.id 
    WHERE p.id = ? AND p.is_published = 1
");
$stmt->execute([$post_id]);
$post = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$post) {
    header("Location: index.php");
    exit();
}

// Increment view count
$stmt = $pdo->prepare("UPDATE posts SET views = views + 1 WHERE id = ?");
$stmt->execute([$post_id]);

// Fetch next 3 posts in the same group with thumbnails
$stmt = $pdo->prepare("
    SELECT p.id, p.title, p.thumbnail, p.created_at
    FROM posts p 
    WHERE p.group_id = ? AND p.is_published = 1 AND p.id != ? 
    ORDER BY p.created_at DESC 
    LIMIT 4
");
$stmt->execute([$post['group_id'], $post_id]);
$nextPosts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle comment submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment']) && isLoggedIn()) {
    $comment = sanitize($_POST['comment']);
    
    if (!empty($comment)) {
        $stmt = $pdo->prepare("INSERT INTO comments (post_id, user_id, content) VALUES (?, ?, ?)");
        $stmt->execute([$post_id, $_SESSION['user_id'], $comment]);
        
        // Refresh to show new comment
        header("Location: view.php?id=" . $post_id);
        exit();
    }
}

// Fetch comments for this post
$stmt = $pdo->prepare("
    SELECT c.*, u.username, u.full_name 
    FROM comments c 
    JOIN users u ON c.user_id = u.id 
    WHERE c.post_id = ? 
    ORDER BY c.created_at DESC
");
$stmt->execute([$post_id]);
$comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pageTitle = $post['title'] . " - Writing Platform";
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
    .post-header {
        border-bottom: 1px solid #e9ecef;
        padding-bottom: 2rem;
        margin-bottom: 2rem;
    }

    .post-title {
        font-size: 2.5rem;
        font-weight: 700;
        line-height: 1.2;
        margin-bottom: 1rem;
        color: #2c3e50;
    }

    .post-meta {
        color: #6c757d;
        font-size: 0.95rem;
        margin-bottom: 1.5rem;
    }

    .post-meta .author {
        font-weight: 600;
        color: #495057;
    }

    .post-thumbnail {
        border-radius: 8px;
        margin-bottom: 2rem;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .post-content {
        line-height: 1.7;
        font-size: 1.1rem;
        color: #4a5568;
        margin-bottom: 3rem;
    }

    .post-content img {
        max-width: 100%;
        height: auto;
        border-radius: 6px;
        margin: 1.5rem 0;
    }

    .post-content table {
        width: 100%;
        border-collapse: collapse;
        margin: 1.5rem 0;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .post-content table,
    .post-content th,
    .post-content td {
        border: 1px solid #e2e8f0;
        padding: 12px 15px;
    }

    .post-content th {
        background-color: #f8f9fa;
        font-weight: 600;
    }

    .post-content blockquote {
        border-left: 4px solid #4f46e5;
        padding-left: 1.5rem;
        margin: 1.5rem 0;
        color: #6b7280;
        font-style: italic;
    }

    .related-posts {
        background-color: #f8f9fa;
        border-radius: 8px;
        padding: 2rem;
        margin: 3rem 0;
    }

    .related-title {
        font-size: 1.5rem;
        font-weight: 600;
        margin-bottom: 1.5rem;
        color: #2c3e50;
    }

    .post-card {
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        height: 100%;
        border: 1px solid #e9ecef;
    }

    .post-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }

    .post-card-img {
        height: 160px;
        object-fit: cover;
        width: 100%;
        border-radius: 8px 8px 0 0;
    }

    .post-card-body {
        padding: 1.25rem;
    }

    .post-card-title {
        font-size: 1.1rem;
        font-weight: 600;
        margin-bottom: 1rem;
        color: #2c3e50;
        line-height: 1.4;
    }

    .comment-section {
        background-color: white;
        border-radius: 8px;
        padding: 2rem;
        border: 1px solid #e9ecef;
    }

    .comment-title {
        font-size: 1.5rem;
        font-weight: 600;
        margin-bottom: 1.5rem;
        color: #2c3e50;
    }

    .comment-form {
        background-color: #f8f9fa;
        border-radius: 8px;
        padding: 1.5rem;
        margin-bottom: 2rem;
    }

    .comment {
        padding: 1.25rem 0;
        border-bottom: 1px solid #e9ecef;
    }

    .comment:last-child {
        border-bottom: none;
    }

    .comment-header {
        display: flex;
        justify-content: between;
        align-items: center;
        margin-bottom: 0.75rem;
    }

    .comment-author {
        font-weight: 600;
        color: #2c3e50;
    }

    .comment-date {
        color: #6c757d;
        font-size: 0.875rem;
    }

    .comment-content {
        color: #4a5568;
        line-height: 1.6;
        margin: 0;
    }

    .views-count {
        background-color: #e3f2fd;
        color: #1976d2;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-weight: 500;
        font-size: 0.9rem;
    }

    @media (max-width: 768px) {
        .post-title {
            font-size: 2rem;
        }

        .post-content {
            font-size: 1rem;
        }

        .related-posts {
            padding: 1.5rem;
        }

        .comment-section {
            padding: 1.5rem;
        }
    }
    </style>
</head>

<body>
    <?php include 'navbar.php'; ?>

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-lg-12">
                <nav aria-label="breadcrumb" class="mb-4">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none">Home</a></li>
                        <li class="breadcrumb-item"><a href="group.php?id=<?php echo $post['group_id']; ?>"
                                class="text-decoration-none"><?php echo $post['group_name']; ?></a></li>
                        <li class="breadcrumb-item active"><?php echo $post['title']; ?></li>
                    </ol>
                </nav>

                <article class="container col-sm-10 card " >
                    <header class="post-header">
                        <h1 class="post-title"><?php echo $post['title']; ?></h1>

                        <div class="post-meta">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <span class="author">By <?php echo $post['author_name']; ?></span>
                                    •
                                    <span><?php echo date('F j, Y', strtotime($post['created_at'])); ?></span>
                                </div>
                                <div class="col-md-4 text-md-end">
                                    <span class="views-count">
                                        <i class="fas fa-eye me-1"></i><?php echo $post['views'] + 1; ?> views
                                    </span>
                                </div>
                            </div>
                        </div>

                        <?php if ($post['thumbnail']): ?>
                        <img src="<?php echo $post['thumbnail']; ?>" class="img-fluid post-thumbnail rounded mx-auto d-block"
                            alt="<?php echo $post['title']; ?> Responsive image">
                        <?php endif; ?>
                    </header>

                    <div class="post-content">
                        <?php echo $post['content']; ?>
                    </div>
                </article>

                
            </div>
        </div>
        
    </div>

    <div class="container-fluid">
        <!-- Related Posts Section -->
                <?php if (count($nextPosts) > 0): ?>
                <section class="related-posts">
                    <h3 class="related-title">More from <?php echo $post['group_name']; ?></h3>
                    <div class="row">
                        <?php foreach ($nextPosts as $nextPost): ?>
                        <div class="col-md-3 mb-3">
                            <div class="post-card">
                                <?php if ($nextPost['thumbnail']): ?>
                                <img src="<?php echo $nextPost['thumbnail']; ?>" class="post-card-img"
                                    alt="<?php echo $nextPost['title']; ?>">
                                <?php else: ?>
                                <div class="post-card-img bg-light d-flex align-items-center justify-content-center">
                                    <i class="fas fa-image fa-2x text-muted"></i>
                                </div>
                                <?php endif; ?>
                                <div class="post-card-body">
                                    <h5 class="post-card-title"><?php echo $nextPost['title']; ?></h5>
                                    <a href="view.php?id=<?php echo $nextPost['id']; ?>"
                                        class="btn btn-primary btn-sm">Read More</a>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </section>
                <?php endif; ?>

                <!-- Comments Section -->
                <section class="comment-section">
                    <h3 class="comment-title">Comments (<?php echo count($comments); ?>)</h3>

                    <?php if (isLoggedIn()): ?>
                    <form method="POST" class="comment-form">
                        <div class="mb-3">
                            <label for="comment" class="form-label">Add your comment</label>
                            <textarea class="form-control" id="comment" name="comment" rows="4"
                                placeholder="Share your thoughts..." required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane me-2"></i>Post Comment
                        </button>
                    </form>
                    <?php else: ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Please <a href="user-login.php" class="alert-link">login</a> to post a comment.
                    </div>
                    <?php endif; ?>

                    <div class="comments-list">
                        <?php if (count($comments) > 0): ?>
                        <?php foreach ($comments as $comment): ?>
                        <div class="comment">
                            <div class="comment-header">
                                <span class="comment-author"><?php echo $comment['full_name']; ?></span>
                                <small
                                    class="comment-date"><?php echo date('M j, Y g:i A', strtotime($comment['created_at'])); ?></small>
                            </div>
                            <p class="comment-content"><?php echo $comment['content']; ?></p>
                        </div>
                        <?php endforeach; ?>
                        <?php else: ?>
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-comments fa-2x mb-3"></i>
                            <p>No comments yet. Be the first to comment!</p>
                        </div>
                        <?php endif; ?>
                    </div>
                </section>
    </div>

    <?php include 'footer.php'; ?>
</body>

</html>