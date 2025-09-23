<?php
require_once '../config.php';
redirectIfNotLoggedIn();
redirectIfNotAdmin();

$pageTitle = "Manage Posts - Writing Platform";

// Handle post actions
if (isset($_GET['action']) && isset($_GET['id'])) {
    $post_id = intval($_GET['id']);
    $action = $_GET['action'];
    
    if ($action === 'toggle') {
        // Toggle post published status
        $stmt = $pdo->prepare("SELECT is_published FROM posts WHERE id = ?");
        $stmt->execute([$post_id]);
        $post = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($post) {
            $newStatus = $post['is_published'] ? 0 : 1;
            $updateStmt = $pdo->prepare("UPDATE posts SET is_published = ? WHERE id = ?");
            $updateStmt->execute([$newStatus, $post_id]);
            $success = "Post status updated successfully.";
        }
    } elseif ($action === 'delete') {
        // Delete post
        $stmt = $pdo->prepare("DELETE FROM posts WHERE id = ?");
        if ($stmt->execute([$post_id])) {
            $success = "Post deleted successfully.";
        }
    }
}

// Fetch all posts with user and group info
$stmt = $pdo->prepare("
    SELECT p.*, u.username, u.full_name, g.name as group_name 
    FROM posts p 
    JOIN users u ON p.author_id = u.id 
    JOIN post_groups g ON p.group_id = g.id 
    ORDER BY p.created_at DESC
");
$stmt->execute();
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
                    <h1 class="h2">Manage Posts</h1>
                </div>
                
                <?php if (isset($success)): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                <?php endif; ?>
                
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Title</th>
                                <th>Author</th>
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
                                        <td><?php echo $post['id']; ?></td>
                                        <td>
                                            <a href="../view.php?id=<?php echo $post['id']; ?>" class="text-decoration-none">
                                                <?php echo substr($post['title'], 0, 50); ?><?php echo strlen($post['title']) > 50 ? '...' : ''; ?>
                                            </a>
                                        </td>
                                        <td><?php echo $post['full_name']; ?></td>
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
                                                <a href="post-list.php?action=toggle&id=<?php echo $post['id']; ?>" class="btn btn-<?php echo $post['is_published'] ? 'warning' : 'success'; ?>" title="<?php echo $post['is_published'] ? 'Unpublish' : 'Publish'; ?>">
                                                    <i class="fas fa-<?php echo $post['is_published'] ? 'eye-slash' : 'eye'; ?>"></i>
                                                </a>
                                                <a href="post-list.php?action=delete&id=<?php echo $post['id']; ?>" class="btn btn-danger" title="Delete" onclick="return confirm('Are you sure you want to delete this post?')">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center">No posts found.</td>
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