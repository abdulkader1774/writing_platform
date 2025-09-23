<?php
session_start();

// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'writing_platform');

// Create database connection
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Check if user is admin
function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

// Redirect if not logged in
function redirectIfNotLoggedIn() {
    if (!isLoggedIn()) {
        header("Location: user-login.php");
        exit();
    }
}

// Redirect if not admin
function redirectIfNotAdmin() {
    if (!isAdmin()) {
        header("Location: index.php");
        exit();
    }
}

// Sanitize input data
function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

// Upload file function
function uploadFile($file, $target_dir = "uploads/") {
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    $target_file = $target_dir . time() . "_" . basename($file["name"]);
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    
    // Check if image file is actual image or fake image
    $check = getimagesize($file["tmp_name"]);
    if($check === false) {
        return ["success" => false, "message" => "File is not an image."];
    }
    
    // Check file size (5MB max)
    if ($file["size"] > 5000000) {
        return ["success" => false, "message" => "File is too large."];
    }
    
    // Allow certain file formats
    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
        return ["success" => false, "message" => "Only JPG, JPEG, PNG & GIF files are allowed."];
    }
    
    if (move_uploaded_file($file["tmp_name"], $target_file)) {
        return ["success" => true, "file_path" => $target_file];
    } else {
        return ["success" => false, "message" => "Sorry, there was an error uploading your file."];
    }
}
?>