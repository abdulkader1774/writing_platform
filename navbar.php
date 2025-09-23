<?php
// This file should be included in pages that need the navbar
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="index.php">Writing Platform</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">Home</a>
                </li>
                <?php
                // Fetch all groups for the navbar
                if (isset($pdo)) {
                    $stmt = $pdo->prepare("SELECT * FROM post_groups ORDER BY name");
                    $stmt->execute();
                    $groups = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    foreach ($groups as $group) {
                        echo '<li class="nav-item">
                                <a class="nav-link" href="group.php?id=' . $group['id'] . '">' . $group['name'] . '</a>
                              </li>';
                    }
                }
                ?>
                <li class="nav-item">
                    <a class="nav-link" href="search.php">Search</a>
                </li>
            </ul>
            <ul class="navbar-nav">
                <?php if (isLoggedIn()): ?>
                    <?php if (isAdmin()): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="admin/">Admin Panel</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="user/">Dashboard</a>
                        </li>
                    <?php endif; ?>
                    <li class="nav-item">
                        <a class="nav-link" href="user/logout.php">Logout</a>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="create-account.php">Create Account</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="user-login.php">Login</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>