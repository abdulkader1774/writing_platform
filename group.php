<?php
require_once 'config.php';

$group_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch group details
$stmt = $pdo->prepare("SELECT * FROM post_groups WHERE id = ?");
$stmt->execute([$group_id]);
$group = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$group) {
    header("Location: index.php");
    exit();
}

// Fetch posts for this group
$stmt = $pdo->prepare("
    SELECT p.*, u.full_name as author_name 
    FROM posts p 
    JOIN users u ON p.author_id = u.id 
    WHERE p.group_id = ? AND p.is_published = 1 
    ORDER BY p.created_at DESC
");
$stmt->execute([$group_id]);
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pageTitle = $group['name'] . " - Writing Platform";
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
        .post-list-item {
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 20px;
            transition: box-shadow 0.3s;
        }
        .post-list-item:hover {
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .post-thumbnail {
            width: 150px;
            height: 100px;
            object-fit: cover;
            border-radius: 5px;
        }
        .recent-posts {
            background-color: #f8f9fa;
            border-radius: 5px;
            padding: 15px;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container mt-4">
        <div class="row">
            <div class="col-lg-8">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                        <li class="breadcrumb-item active"><?php echo $group['name']; ?></li>
                    </ol>
                </nav>
                
                <h1 class="mb-4"><?php echo $group['name']; ?></h1>
                <?php if ($group['description']): ?>
                    <p class="lead"><?php echo $group['description']; ?></p>
                <?php endif; ?>
                
                <div class="posts-list">
                    <?php if (count($posts) > 0): ?>
                        <?php foreach ($posts as $post): ?>
                            <div class="post-list-item">
                                <div class="row">
                                    <?php if ($post['thumbnail']): ?>
                                        <div class="col-md-3">
                                            <img src="<?php echo $post['thumbnail']; ?>" alt="<?php echo $post['title']; ?>" class="post-thumbnail img-fluid">
                                        </div>
                                        <div class="col-md-9">
                                    <?php else: ?>
                                        <div class="col-12">
                                    <?php endif; ?>
                                        <h3><?php echo $post['title']; ?></h3>
                                        <p class="text-muted">By <?php echo $post['author_name']; ?> on <?php echo date('M j, Y', strtotime($post['created_at'])); ?></p>
                                        <p><?php echo substr(strip_tags($post['content']), 0, 200); ?>...</p>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <a href="view.php?id=<?php echo $post['id']; ?>" class="btn btn-primary">Read More</a>
                                            <small class="text-muted"><?php echo $post['views']; ?> views</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="alert alert-info">
                            No writings available in this category yet.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="recent-posts mb-4">
                    <h4>Recently Published</h4>
                    <?php
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
                            echo '<div class="recent-post-item mb-2">';
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
                        <h5 class="card-title">Categories</h5>
                        <?php
                        $stmt = $pdo->prepare("SELECT * FROM post_groups ORDER BY name");
                        $stmt->execute();
                        $allGroups = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        
                        echo '<ul class="list-unstyled">';
                        foreach ($allGroups as $cat) {
                            $activeClass = ($cat['id'] == $group_id) ? 'fw-bold' : '';
                            echo '<li><a href="group.php?id=' . $cat['id'] . '" class="text-decoration-none ' . $activeClass . '">' . $cat['name'] . '</a></li>';
                        }
                        echo '</ul>';
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html>