<?php
require_once '../config.php';
redirectIfNotLoggedIn();
redirectIfNotAdmin();

$pageTitle = "Manage Users - Writing Platform";

// Handle user actions
if (isset($_GET['action']) && isset($_GET['id'])) {
    $user_id = intval($_GET['id']);
    $action = $_GET['action'];
    
    if ($action === 'toggle') {
        // Toggle user active status
        $stmt = $pdo->prepare("SELECT is_active FROM users WHERE id = ? AND role = 'user'");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            $newStatus = $user['is_active'] ? 0 : 1;
            $updateStmt = $pdo->prepare("UPDATE users SET is_active = ? WHERE id = ?");
            $updateStmt->execute([$newStatus, $user_id]);
            $success = "User status updated successfully.";
        }
    } elseif ($action === 'delete') {
        // Delete user (and their posts)
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ? AND role = 'user'");
        if ($stmt->execute([$user_id])) {
            $success = "User deleted successfully.";
        }
    }
}

// Fetch all users
$stmt = $pdo->prepare("SELECT * FROM users WHERE role = 'user' ORDER BY created_at DESC");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
                    <h1 class="h2">Manage Users</h1>
                </div>
                
                <?php if (isset($success)): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                <?php endif; ?>
                
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Full Name</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Posts</th>
                                <th>Status</th>
                                <th>Joined</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($users) > 0): ?>
                                <?php foreach ($users as $user): ?>
                                    <tr>
                                        <td><?php echo $user['id']; ?></td>
                                        <td><?php echo $user['full_name']; ?></td>
                                        <td><?php echo $user['username']; ?></td>
                                        <td><?php echo $user['email']; ?></td>
                                        <td>
                                            <?php
                                            $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM posts WHERE author_id = ?");
                                            $stmt->execute([$user['id']]);
                                            $postCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
                                            echo $postCount;
                                            ?>
                                        </td>
                                        <td>
                                            <?php if ($user['is_active']): ?>
                                                <span class="badge bg-success">Active</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">Blocked</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo date('M j, Y', strtotime($user['created_at'])); ?></td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="chat.php?user_id=<?php echo $user['id']; ?>" class="btn btn-info" title="Message">
                                                    <i class="fas fa-envelope"></i>
                                                </a>
                                                <a href="user-list.php?action=toggle&id=<?php echo $user['id']; ?>" class="btn btn-<?php echo $user['is_active'] ? 'warning' : 'success'; ?>" title="<?php echo $user['is_active'] ? 'Block' : 'Activate'; ?>">
                                                    <i class="fas fa-<?php echo $user['is_active'] ? 'lock' : 'unlock'; ?>"></i>
                                                </a>
                                                <a href="user-list.php?action=delete&id=<?php echo $user['id']; ?>" class="btn btn-danger" title="Delete" onclick="return confirm('Are you sure you want to delete this user? All their posts will also be deleted.')">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center">No users found.</td>
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