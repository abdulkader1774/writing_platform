<?php
require_once 'config.php';

$search_query = isset($_GET['q']) ? sanitize($_GET['q']) : '';
$results = [];

if (!empty($search_query)) {
    $stmt = $pdo->prepare("
        SELECT p.*, u.full_name as author_name, g.name as group_name 
        FROM posts p 
        JOIN users u ON p.author_id = u.id 
        JOIN post_groups g ON p.group_id = g.id 
        WHERE (p.title LIKE ? OR p.content LIKE ? OR u.full_name LIKE ?) 
        AND p.is_published = 1 
        ORDER BY p.created_at DESC
    ");
    $search_term = "%$search_query%";
    $stmt->execute([$search_term, $search_term, $search_term]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$pageTitle = "Search - Writing Platform";
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

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <h1 class="text-center mb-4">Search Writings</h1>
                
                <form method="GET" class="mb-4">
                    <div class="input-group">
                        <input type="text" class="form-control" name="q" value="<?php echo $search_query; ?>" placeholder="Search by title, content, or author..." required>
                        <button class="btn btn-primary" type="submit">Search</button>
                    </div>
                </form>
                
                <?php if (!empty($search_query)): ?>
                    <div class="mb-3">
                        <p>Found <?php echo count($results); ?> result(s) for "<?php echo $search_query; ?>"</p>
                    </div>
                    
                    <?php if (count($results) > 0): ?>
                        <div class="search-results">
                            <?php foreach ($results as $post): ?>
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <h5 class="card-title">
                                            <a href="view.php?id=<?php echo $post['id']; ?>" class="text-decoration-none">
                                                <?php echo $post['title']; ?>
                                            </a>
                                        </h5>
                                        <p class="card-text">
                                            <small class="text-muted">
                                                By <?php echo $post['author_name']; ?> in <?php echo $post['group_name']; ?> 
                                                on <?php echo date('M j, Y', strtotime($post['created_at'])); ?>
                                            </small>
                                        </p>
                                        <p class="card-text">
                                            <?php 
                                            $content = strip_tags($post['content']);
                                            $position = stripos($content, $search_query);
                                            if ($position !== false) {
                                                $start = max(0, $position - 50);
                                                $excerpt = substr($content, $start, 200);
                                                if ($start > 0) $excerpt = '...' . $excerpt;
                                                if (strlen($content) > $start + 200) $excerpt .= '...';
                                                echo preg_replace("/$search_query/i", "<mark>$0</mark>", $excerpt);
                                            } else {
                                                echo substr($content, 0, 200) . '...';
                                            }
                                            ?>
                                        </p>
                                        <a href="view.php?id=<?php echo $post['id']; ?>" class="btn btn-primary btn-sm">Read More</a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info text-center">
                            No results found for your search. Try different keywords.
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html>