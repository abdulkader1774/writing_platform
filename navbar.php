<?php
// This file should be included in pages that need the navbar
?>

<style>
     .navbar {
            background-color: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
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
</style>
<nav class="navbar navbar-expand-lg navbar-light sticky-top bg-dark">
    <div class="container">
        <a class="navbar-brand text-light" href="index.php"><i class="fas fa-pen-fancy me-2 text-light"></i>WriteSpace</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link active text-light" href="index.php">Home</a>
                </li>

                <?php
                // Fetch all groups for the navbar
                if (isset($pdo)) {
                    $stmt = $pdo->prepare("SELECT * FROM post_groups ORDER BY name");
                    $stmt->execute();
                    $groups = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    foreach ($groups as $group) {
                        echo '<li class="nav-item">
                                <a class="nav-link text-light" href="group.php?id=' . $group['id'] . '">' . $group['name'] . '</a>
                              </li>';
                    }
                }
                ?>
                <li class="nav-item">
                    <a class="nav-link text-light" href="search.php">Search</a>
                </li>
                <!-- <div class="d-flex">
                    <a href="#" class="btn btn-outline-primary me-2">Login</a>
                    <a href="#" class="btn btn-primary">Sign Up</a>
                </div> -->
            </ul>
            <ul class="navbar-nav">
                <?php if (isLoggedIn()): ?>
                <?php if (isAdmin()): ?>
                <li class="nav-item">
                    <a class="nav-link text-light" href="admin/">Admin Panel</a>
                </li>
                <?php else: ?>
                <li class="nav-item">
                    <a class="nav-link text-light" href="user/">Dashboard</a>
                </li>
                <?php endif; ?>
                <li class="nav-item">
                    <a class="nav-link text-light" href="user/logout.php">Logout</a>
                </li>
                <?php else: ?>
                <li class="nav-item">
                    <a class="nav-link text-light" href="create-account.php">Create Account</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-light" href="user-login.php">Login</a>
                </li>
                <?php endif; ?>
            </ul>

        </div>
    </div>
</nav>