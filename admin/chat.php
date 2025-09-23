<?php
require_once '../config.php';
redirectIfNotLoggedIn();
redirectIfNotAdmin();

$pageTitle = "Messages - Writing Platform";

// Get specific user if provided
$selected_user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;

// Handle message sending
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message']) && $selected_user_id > 0) {
    $message = sanitize($_POST['message']);
    
    if (!empty($message)) {
        $stmt = $pdo->prepare("INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)");
        $stmt->execute([$_SESSION['user_id'], $selected_user_id, $message]);
    }
}

// Mark messages as read when viewing conversation
if ($selected_user_id > 0) {
    $stmt = $pdo->prepare("UPDATE messages SET is_read = 1 WHERE sender_id = ? AND receiver_id = ? AND is_read = 0");
    $stmt->execute([$selected_user_id, $_SESSION['user_id']]);
}

// Fetch all users for sidebar
$stmt = $pdo->prepare("SELECT id, username, full_name FROM users WHERE role = 'user' ORDER BY full_name");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch unread message counts
$unreadCounts = [];
foreach ($users as $user) {
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM messages WHERE sender_id = ? AND receiver_id = ? AND is_read = 0");
    $stmt->execute([$user['id'], $_SESSION['user_id']]);
    $unreadCounts[$user['id']] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
}

// Fetch messages for selected user
$messages = [];
if ($selected_user_id > 0) {
    $stmt = $pdo->prepare("
        SELECT m.*, u.username, u.full_name 
        FROM messages m 
        JOIN users u ON m.sender_id = u.id 
        WHERE (m.sender_id = ? AND m.receiver_id = ?) OR (m.sender_id = ? AND m.receiver_id = ?) 
        ORDER BY m.created_at ASC
    ");
    $stmt->execute([$selected_user_id, $_SESSION['user_id'], $_SESSION['user_id'], $selected_user_id]);
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
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
        .users-list {
            height: 70vh;
            overflow-y: auto;
            border-right: 1px solid #dee2e6;
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
        .user-item {
            cursor: pointer;
            padding: 10px;
            border-bottom: 1px solid #dee2e6;
        }
        .user-item:hover, .user-item.active {
            background-color: #f8f9fa;
        }
        .unread-count {
            background-color: #dc3545;
            color: white;
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 12px;
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
                    <h1 class="h2">Messages</h1>
                </div>
                
                <div class="chat-container">
                    <div class="row h-100">
                        <!-- Users List -->
                        <div class="col-md-4 p-0">
                            <div class="users-list bg-light">
                                <div class="p-3 border-bottom">
                                    <h5 class="mb-0">Users</h5>
                                </div>
                                <?php if (count($users) > 0): ?>
                                    <?php foreach ($users as $user): ?>
                                        <div class="user-item d-flex justify-content-between align-items-center <?php echo $selected_user_id == $user['id'] ? 'active' : ''; ?>" 
                                             onclick="location.href='chat.php?user_id=<?php echo $user['id']; ?>'">
                                            <div>
                                                <strong><?php echo $user['full_name']; ?></strong>
                                                <br>
                                                <small class="text-muted">@<?php echo $user['username']; ?></small>
                                            </div>
                                            <?php if ($unreadCounts[$user['id']] > 0): ?>
                                                <span class="unread-count"><?php echo $unreadCounts[$user['id']]; ?></span>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="p-3 text-center">
                                        <p>No users found.</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <!-- Messages Area -->
                        <div class="col-md-8 p-0">
                            <div class="messages-container">
                                <?php if ($selected_user_id > 0): ?>
                                    <?php
                                    $stmt = $pdo->prepare("SELECT full_name FROM users WHERE id = ?");
                                    $stmt->execute([$selected_user_id]);
                                    $selectedUser = $stmt->fetch(PDO::FETCH_ASSOC);
                                    ?>
                                    
                                    <div class="p-3 border-bottom">
                                        <h5 class="mb-0">Conversation with <?php echo $selectedUser['full_name']; ?></h5>
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
                                                <p>No messages yet. Start a conversation!</p>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="message-input">
                                        <form method="POST" class="d-flex">
                                            <input type="text" name="message" class="form-control me-2" placeholder="Type your message..." required>
                                            <button type="submit" class="btn btn-primary">Send</button>
                                        </form>
                                    </div>
                                <?php else: ?>
                                    <div class="d-flex justify-content-center align-items-center h-100">
                                        <div class="text-center text-muted">
                                            <i class="fas fa-comments fa-3x mb-3"></i>
                                            <p>Select a user to start chatting</p>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
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
            if (<?php echo $selected_user_id > 0 ? 'true' : 'false'; ?>) {
                location.reload();
            }
        }, 5000);
    </script>
</body>
</html>