<?php
require_once '../config.php';
redirectIfNotLoggedIn();
if (isAdmin()) {
    header("Location: ../admin/");
    exit();
}

$pageTitle = "Messages - Writing Platform";

// Handle message sending
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message'])) {
    $message = sanitize($_POST['message']);
    
    if (!empty($message)) {
        // Find admin user
        $stmt = $pdo->prepare("SELECT id FROM users WHERE role = 'admin' LIMIT 1");
        $stmt->execute();
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($admin) {
            $stmt = $pdo->prepare("INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)");
            $stmt->execute([$_SESSION['user_id'], $admin['id'], $message]);
        }
    }
}

// Mark messages as read
$stmt = $pdo->prepare("UPDATE messages SET is_read = 1 WHERE sender_id IN (SELECT id FROM users WHERE role = 'admin') AND receiver_id = ? AND is_read = 0");
$stmt->execute([$_SESSION['user_id']]);

// Fetch messages with admin
$stmt = $pdo->prepare("
    SELECT m.*, u.username, u.full_name 
    FROM messages m 
    JOIN users u ON m.sender_id = u.id 
    WHERE (m.sender_id = ? AND m.receiver_id IN (SELECT id FROM users WHERE role = 'admin')) 
       OR (m.sender_id IN (SELECT id FROM users WHERE role = 'admin') AND m.receiver_id = ?) 
    ORDER BY m.created_at ASC
");
$stmt->execute([$_SESSION['user_id'], $_SESSION['user_id']]);
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Check for unread messages
$stmt = $pdo->prepare("SELECT COUNT(*) as count FROM messages WHERE sender_id IN (SELECT id FROM users WHERE role = 'admin') AND receiver_id = ? AND is_read = 0");
$stmt->execute([$_SESSION['user_id']]);
$unreadCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
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
        .chat-container {
            height: 70vh;
            border: 1px solid #dee2e6;
            border-radius: 5px;
        }
        .messages-container {
            height: 70vh;
            display: flex;
            flex-direction: column;
        }
        .messages-list {
            flex: 1;
            overflow-y: auto;
            padding: 15px;
        }
        .message-input {
            border-top: 1px solid #dee2e6;
            padding: 15px;
        }
        .message {
            margin-bottom: 15px;
            padding: 10px;
            border-radius: 10px;
            max-width: 70%;
        }
        .message.sent {
            background-color: #007bff;
            color: white;
            margin-left: auto;
        }
        .message.received {
            background-color: #f8f9fa;
            color: #333;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>
    
    <div class="container-fluid">
        <div class="row">
            <?php include 'sidebar.php'; ?>
            
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Messages with Admin</h1>
                    <?php if ($unreadCount > 0): ?>
                        <span class="badge bg-danger"><?php echo $unreadCount; ?> unread</span>
                    <?php endif; ?>
                </div>
                
                <div class="chat-container">
                    <div class="messages-container">
                        <div class="p-3 border-bottom bg-light">
                            <h5 class="mb-0">Conversation with Platform Administrator</h5>
                        </div>
                        
                        <div class="messages-list">
                            <?php if (count($messages) > 0): ?>
                                <?php foreach ($messages as $message): ?>
                                    <div class="message <?php echo $message['sender_id'] == $_SESSION['user_id'] ? 'sent' : 'received'; ?>">
                                        <div class="message-content"><?php echo $message['message']; ?></div>
                                        <small class="message-time"><?php echo date('M j, g:i A', strtotime($message['created_at'])); ?></small>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="text-center text-muted mt-5">
                                    <i class="fas fa-comments fa-3x mb-3"></i>
                                    <p>No messages yet. Start a conversation with the admin!</p>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="message-input">
                            <form method="POST" class="d-flex">
                                <input type="text" name="message" class="form-control me-2" placeholder="Type your message to admin..." required>
                                <button type="submit" class="btn btn-primary">Send</button>
                            </form>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <?php include 'footer.php'; ?>
    
    <script>
        // Auto-scroll to bottom of messages
        function scrollToBottom() {
            const messagesList = document.querySelector('.messages-list');
            if (messagesList) {
                messagesList.scrollTop = messagesList.scrollHeight;
            }
        }
        
        // Scroll to bottom when page loads
        document.addEventListener('DOMContentLoaded', scrollToBottom);
        
        // Auto-refresh messages every 5 seconds
        setInterval(function() {
            location.reload();
        }, 5000);
    </script>
</body>
</html>