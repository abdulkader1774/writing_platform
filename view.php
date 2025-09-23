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

// Fetch next 3 posts in the same group
$stmt = $pdo->prepare("
    SELECT p.id, p.title 
    FROM posts p 
    WHERE p.group_id = ? AND p.is_published = 1 AND p.id != ? 
    ORDER BY p.created_at DESC 
    LIMIT 3
");
$stmt->execute([$post['group_id'], $post_id]);
$nextPosts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle comment submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment']) && isLoggedIn()) {
    $comment = sanitize($_POST['comment']);
    
    if (!empty($comment)) {
        $stmt = $pdo->prepare("INSERT INTO comments (post_id, user_id, content) VALUES (?, ?, ?)");
        $stmt->execute([$post_id, $_SESSION['user_id'], $comment]);
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
        .post-content {
            line-height: 1.8;
            font-size: 1.1rem;
        }
        .post-content img {
            max-width: 100%;
            height: auto;
        }
        .post-content table {
            width: 100%;
            border-collapse: collapse;
        }
        .post-content table, .post-content th, .post-content td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        .comment-section {
            background-color: #f8f9fa;
            border-radius: 5px;
            padding: 20px;
        }
        .comment {
            border-bottom: 1px solid #dee2e6;
            padding: 15px 0;
        }
        .comment:last-child {
            border-bottom: none;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container mt-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item"><a href="group.php?id=<?php echo $post['group_id']; ?>"><?php echo $post['group_name']; ?></a></li>
                <li class="breadcrumb-item active"><?php echo $post['title']; ?></li>
            </ol>
        </nav>
        
        <article>
            <header class="mb-4">
                <h1 class="display-4"><?php echo $post['title']; ?></h1>
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="lead">By <?php echo $post['author_name']; ?></p>
                        <p class="text-muted">Published on <?php echo date('F j, Y', strtotime($post['created_at'])); ?></p>
                    </div>
                    <div class="text-muted">
                        <i class="fas fa-eye"></i> <?php echo $post['views'] + 1; ?> views
                    </div>
                </div>
                <?php if ($post['thumbnail']): ?>
                    <img src="<?php echo $post['thumbnail']; ?>" class="img-fluid rounded mb-4" alt="<?php echo $post['title']; ?>">
                <?php endif; ?>
            </header>
            
            <div class="post-content">
                <?php echo $post['content']; ?>
            </div>
        </article>
        
        <hr class="my-5">
        
        <!-- Next Posts Section -->
        <?php if (count($nextPosts) > 0): ?>
            <section class="mb-5">
                <h3>More from <?php echo $post['group_name']; ?></h3>
                <div class="row">
                    <?php foreach ($nextPosts as $nextPost): ?>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo $nextPost['title']; ?></h5>
                                    <a href="view.php?id=<?php echo $nextPost['id']; ?>" class="btn btn-primary">Read More</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>
        
        <!-- Comments Section -->
        <section class="comment-section">
            <h3>Comments (<?php echo count($comments); ?>)</h3>
            
            <?php if (isLoggedIn()): ?>
                <form method="POST" class="mb-4">
                    <div class="mb-3">
                        <label for="comment" class="form-label">Add a comment</label>
                        <textarea class="form-control" id="comment" name="comment" rows="3" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Post Comment</button>
                </form>
            <?php else: ?>
                <div class="alert alert-info">
                    Please <a href="user-login.php">login</a> to post a comment.
                </div>
            <?php endif; ?>
            
            <div class="comments-list">
                <?php if (count($comments) > 0): ?>
                    <?php foreach ($comments as $comment): ?>
                        <div class="comment">
                            <div class="d-flex justify-content-between">
                                <strong><?php echo $comment['full_name']; ?></strong>
                                <small class="text-muted"><?php echo date('M j, Y g:i A', strtotime($comment['created_at'])); ?></small>
                            </div>
                            <p class="mb-0"><?php echo $comment['content']; ?></p>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No comments yet. Be the first to comment!</p>
                <?php endif; ?>
            </div>
        </section>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html>