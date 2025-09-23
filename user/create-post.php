<?php
require_once '../config.php';
redirectIfNotLoggedIn();
if (isAdmin()) {
    header("Location: ../admin/");
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = sanitize($_POST['title']);
    $content = $_POST['content']; // Don't sanitize to preserve HTML from editor
    $group_id = intval($_POST['group_id']);
    
    // Handle thumbnail upload
    $thumbnail = '';
    if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] === 0) {
        $uploadResult = uploadFile($_FILES['thumbnail'], '../uploads/');
        if ($uploadResult['success']) {
            $thumbnail = $uploadResult['file_path'];
        } else {
            $error = $uploadResult['message'];
        }
    }
    
    if (empty($title) || empty($content) || empty($group_id)) {
        $error = "Title, content, and group are required.";
    } else {
        // Insert new post
        $stmt = $pdo->prepare("INSERT INTO posts (title, content, author_id, group_id, thumbnail) VALUES (?, ?, ?, ?, ?)");
        
        if ($stmt->execute([$title, $content, $_SESSION['user_id'], $group_id, $thumbnail])) {
            $success = "Post created successfully!";
            // Reset form
            $title = $content = '';
            $group_id = 0;
        } else {
            $error = "Error creating post. Please try again.";
        }
    }
}

// Fetch groups for dropdown
$stmt = $pdo->prepare("SELECT * FROM post_groups ORDER BY name");
$stmt->execute();
$groups = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pageTitle = "Create Post - Writing Platform";
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
        .editor-toolbar {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-bottom: none;
            padding: 5px;
            border-radius: 5px 5px 0 0;
        }
        .editor-toolbar button {
            background: none;
            border: 1px solid #dee2e6;
            padding: 5px 10px;
            margin: 2px;
            border-radius: 3px;
            cursor: pointer;
        }
        .editor-toolbar button:hover {
            background-color: #e9ecef;
        }
        .editor-content {
            border: 1px solid #dee2e6;
            border-radius: 0 0 5px 5px;
            padding: 15px;
            min-height: 300px;
            outline: none;
        }
        .editor-content:focus {
            border-color: #86b7fe;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }
        .editor-content img {
            max-width: 100%;
            height: auto;
        }
        .editor-content table {
            width: 100%;
            border-collapse: collapse;
        }
        .editor-content table, .editor-content th, .editor-content td {
            border: 1px solid #ddd;
            padding: 8px;
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
                    <h1 class="h2">Create New Post</h1>
                </div>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                <?php endif; ?>
                
                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="title" class="form-label">Post Title</label>
                        <input type="text" class="form-control" id="title" name="title" value="<?php echo isset($title) ? $title : ''; ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="group_id" class="form-label">Category</label>
                        <select class="form-control" id="group_id" name="group_id" required>
                            <option value="">Select a category</option>
                            <?php foreach ($groups as $group): ?>
                                <option value="<?php echo $group['id']; ?>" <?php echo (isset($group_id) && $group_id == $group['id']) ? 'selected' : ''; ?>>
                                    <?php echo $group['name']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="thumbnail" class="form-label">Thumbnail Image</label>
                        <input type="file" class="form-control" id="thumbnail" name="thumbnail" accept="image/*">
                        <div class="form-text">Optional. Recommended size: 800x400 pixels.</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="content" class="form-label">Content</label>
                        <div class="editor-container">
                            <div class="editor-toolbar">
                                <button type="button" onclick="formatText('bold')" title="Bold"><i class="fas fa-bold"></i></button>
                                <button type="button" onclick="formatText('italic')" title="Italic"><i class="fas fa-italic"></i></button>
                                <button type="button" onclick="formatText('underline')" title="Underline"><i class="fas fa-underline"></i></button>
                                <button type="button" onclick="formatText('strikeThrough')" title="Strikethrough"><i class="fas fa-strikethrough"></i></button>
                                <button type="button" onclick="formatText('justifyLeft')" title="Align Left"><i class="fas fa-align-left"></i></button>
                                <button type="button" onclick="formatText('justifyCenter')" title="Align Center"><i class="fas fa-align-center"></i></button>
                                <button type="button" onclick="formatText('justifyRight')" title="Align Right"><i class="fas fa-align-right"></i></button>
                                <button type="button" onclick="formatText('insertUnorderedList')" title="Bullet List"><i class="fas fa-list-ul"></i></button>
                                <button type="button" onclick="formatText('insertOrderedList')" title="Numbered List"><i class="fas fa-list-ol"></i></button>
                                <button type="button" onclick="insertLink()" title="Insert Link"><i class="fas fa-link"></i></button>
                                <button type="button" onclick="insertImage()" title="Insert Image"><i class="fas fa-image"></i></button>
                                <button type="button" onclick="insertTable()" title="Insert Table"><i class="fas fa-table"></i></button>
                                <select onchange="formatText('formatBlock', this.value)" title="Paragraph Format">
                                    <option value="p">Paragraph</option>
                                    <option value="h1">Heading 1</option>
                                    <option value="h2">Heading 2</option>
                                    <option value="h3">Heading 3</option>
                                    <option value="h4">Heading 4</option>
                                    <option value="h5">Heading 5</option>
                                    <option value="h6">Heading 6</option>
                                </select>
                                <select onchange="formatText('fontSize', this.value)" title="Font Size">
                                    <option value="1">Small</option>
                                    <option value="3" selected>Normal</option>
                                    <option value="5">Large</option>
                                    <option value="7">Huge</option>
                                </select>
                                <input type="color" onchange="formatText('foreColor', this.value)" title="Text Color">
                                <button type="button" onclick="formatText('removeFormat')" title="Clear Formatting"><i class="fas fa-eraser"></i></button>
                            </div>
                            <div class="editor-content" id="content" contenteditable="true"></div>
                            <textarea id="contentHidden" name="content" style="display:none;"></textarea>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary" onclick="prepareContent()">Publish Post</button>
                    <a href="post-list.php" class="btn btn-secondary">Cancel</a>
                </form>
            </main>
        </div>
    </div>

    <?php include 'footer.php'; ?>
    
    <script>
        // Text formatting functions
        function formatText(command, value = null) {
            document.getElementById('content').focus();
            if (value) {
                document.execCommand(command, false, value);
            } else {
                document.execCommand(command, false, null);
            }
        }

        // Insert link
        function insertLink() {
            const url = prompt('Enter URL:');
            if (url) {
                const text = prompt('Enter link text (optional):') || url;
                document.execCommand('insertHTML', false, `<a href="${url}" target="_blank">${text}</a>`);
            }
        }

        // Insert image
        function insertImage() {
            const url = prompt('Enter image URL:');
            if (url) {
                document.execCommand('insertHTML', false, `<img src="${url}" alt="Image" style="max-width:100%;height:auto;">`);
            }
        }

        // Insert table
        function insertTable() {
            const rows = parseInt(prompt('Number of rows:') || 3);
            const cols = parseInt(prompt('Number of columns:') || 3);
            
            if (rows > 0 && cols > 0) {
                let tableHTML = '<table border="1" style="width:100%;border-collapse:collapse;">';
                for (let i = 0; i < rows; i++) {
                    tableHTML += '<tr>';
                    for (let j = 0; j < cols; j++) {
                        tableHTML += '<td style="padding:8px;border:1px solid #ddd;">&nbsp;</td>';
                    }
                    tableHTML += '</tr>';
                }
                tableHTML += '</table>';
                document.execCommand('insertHTML', false, tableHTML);
            }
        }

        // Prepare content before form submission
        function prepareContent() {
            document.getElementById('contentHidden').value = document.getElementById('content').innerHTML;
        }

        // Initialize editor with sample content if needed
        document.addEventListener('DOMContentLoaded', function() {
            const editor = document.getElementById('content');
            editor.innerHTML = '<?php echo isset($content) ? addslashes($content) : "Start writing your content here..."; ?>';
            
            // Clear placeholder text on focus
            editor.addEventListener('focus', function() {
                if (this.innerHTML === 'Start writing your content here...') {
                    this.innerHTML = '';
                }
            });
        });
    </script>
</body>
</html>