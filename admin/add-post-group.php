<?php
require_once '../config.php';
redirectIfNotLoggedIn();
redirectIfNotAdmin();

$pageTitle = "Add Category - Writing Platform";
$error = '';
$success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name']);
    $description = sanitize($_POST['description']);
    
    if (empty($name)) {
        $error = "Category name is required.";
    } else {
        // Check if category already exists
        $stmt = $pdo->prepare("SELECT id FROM post_groups WHERE name = ?");
        $stmt->execute([$name]);
        
        if ($stmt->rowCount() > 0) {
            $error = "Category already exists.";
        } else {
            // Insert new category
            $stmt = $pdo->prepare("INSERT INTO post_groups (name, description, created_by) VALUES (?, ?, ?)");
            if ($stmt->execute([$name, $description, $_SESSION['user_id']])) {
                $success = "Category added successfully!";
                // Reset form
                $name = $description = '';
            } else {
                $error = "Error adding category. Please try again.";
            }
        }
    }
}

// Fetch existing categories
$stmt = $pdo->prepare("SELECT * FROM post_groups ORDER BY name");
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
                    <h1 class="h2">Add Category</h1>
                </div>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                <?php endif; ?>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5>Add New Category</h5>
                            </div>
                            <div class="card-body">
                                <form method="POST">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Category Name</label>
                                        <input type="text" class="form-control" id="name" name="name" value="<?php echo isset($name) ? $name : ''; ?>" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="description" class="form-label">Description</label>
                                        <textarea class="form-control" id="description" name="description" rows="3"><?php echo isset($description) ? $description : ''; ?></textarea>
                                    </div>
                                    
                                    <button type="submit" class="btn btn-primary">Add Category</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5>Existing Categories</h5>
                            </div>
                            <div class="card-body">
                                <?php if (count($categories) > 0): ?>
                                    <div class="list-group">
                                        <?php foreach ($categories as $category): ?>
                                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h6 class="mb-1"><?php echo $category['name']; ?></h6>
                                                    <?php if ($category['description']): ?>
                                                        <p class="mb-1 text-muted"><?php echo $category['description']; ?></p>
                                                    <?php endif; ?>
                                                </div>
                                                <span class="badge bg-primary rounded-pill">
                                                    <?php
                                                    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM posts WHERE group_id = ?");
                                                    $stmt->execute([$category['id']]);
                                                    $postCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
                                                    echo $postCount . ' posts';
                                                    ?>
                                                </span>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else: ?>
                                    <p>No categories found.</p>
                                <?php endif; ?>
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