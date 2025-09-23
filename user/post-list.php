<?php
require_once '../config.php';
redirectIfNotLoggedIn();
if (isAdmin()) {
    header("Location: ../admin/");
    exit();
}

$pageTitle = "My Posts - Writing Platform";

// Handle post deletion
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $post_id = intval($_GET['delete']);
    
    // Verify that the post belongs to the current user
    $stmt = $pdo->prepare("SELECT id FROM posts WHERE id = ? AND author_id = ?");
    $stmt->execute([$post_id, $_SESSION['user_id']]);
    
    if ($stmt->rowCount() > 0) {
        $deleteStmt = $pdo->prepare("DELETE FROM posts WHERE id = ?");
        $deleteStmt->execute([$post_id]);
        $success = "Post deleted successfully.";
    } else {
        $error = "You don't have permission to delete this post.";
    }
}

// Handle post status toggle
if (isset($_GET['toggle']) && is_numeric($_GET['toggle'])) {
    $post_id = intval($_GET['toggle']);
    
    // Verify that the post belongs to the current user
    $stmt = $pdo->prepare("SELECT id, is_published FROM posts WHERE id = ? AND author_id = ?");
    $stmt->execute([$post_id, $_SESSION['user_id']]);
    $post = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($post) {
        $newStatus = $post['is_published'] ? 0 : 1;
        $updateStmt = $pdo->prepare("UPDATE posts SET is_published = ? WHERE id = ?");
        $updateStmt->execute([$newStatus, $post_id]);
        $success = "Post status updated successfully.";
    } else {
        $error = "You don't have permission to modify this post.";
    }
}

// Fetch user's posts
$stmt = $pdo->prepare("
    SELECT p.*, g.name as group_name 
    FROM posts p 
    JOIN post_groups g ON p.group_id = g.id 
    WHERE p.author_id = ? 
    ORDER BY p.created_at DESC
");
$stmt->execute([$_SESSION['user_id']]);
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
                    <h1 class="h2">My Posts</h1>
                    <a href="create-post.php" class="btn btn-primary">Create New Post</a>
                </div>
                
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <?php if (isset($success)): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                <?php endif; ?>
                
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Category</th>
                                <th>Status</th>
                                <th>Views</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($posts) > 0): ?>
                                <?php foreach ($posts as $post): ?>
                                    <tr>
                                        <td>
                                            <a href="../view.php?id=<?php echo $post['id']; ?>" class="text-decoration-none">
                                                <?php echo $post['title']; ?>
                                            </a>
                                        </td>
                                        <td><?php echo $post['group_name']; ?></td>
                                        <td>
                                            <?php if ($post['is_published']): ?>
                                                <span class="badge bg-success">Published</span>
                                            <?php else: ?>
                                                <span class="badge bg-warning">Draft</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo $post['views']; ?></td>
                                        <td><?php echo date('M j, Y', strtotime($post['created_at'])); ?></td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="../view.php?id=<?php echo $post['id']; ?>" class="btn btn-info" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="create-post.php?edit=<?php echo $post['id']; ?>" class="btn btn-warning" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="post-list.php?toggle=<?php echo $post['id']; ?>" class="btn btn-secondary" title="<?php echo $post['is_published'] ? 'Unpublish' : 'Publish'; ?>">
                                                    <i class="fas fa-<?php echo $post['is_published'] ? 'eye-slash' : 'eye'; ?>"></i>
                                                </a>
                                                <a href="post-list.php?delete=<?php echo $post['id']; ?>" class="btn btn-danger" title="Delete" onclick="return confirm('Are you sure you want to delete this post?')">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center">No posts found. <a href="create-post.php">Create your first post</a></td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </main>
        </div>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html>