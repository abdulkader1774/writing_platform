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
            $thumbnail = substr($thumbnail, 3);


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

    .editor-content table,
    .editor-content th,
    .editor-content td {
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
                <div
                    class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
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
                        <input type="text" class="form-control" id="title" name="title"
                            value="<?php echo isset($title) ? $title : ''; ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="group_id" class="form-label">Category</label>
                        <select class="form-control" id="group_id" name="group_id" required>
                            <option value="">Select a category</option>
                            <?php foreach ($groups as $group): ?>
                            <option value="<?php echo $group['id']; ?>"
                                <?php echo (isset($group_id) && $group_id == $group['id']) ? 'selected' : ''; ?>>
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

                    <!-- <div class="mb-3">
                        <label for="content" class="form-label">Content</label>
                        <div class="editor-container">
                            <div class="editor-toolbar">
                                <button type="button" onclick="formatText('bold')" title="Bold"><i
                                        class="fas fa-bold"></i></button>
                                <button type="button" onclick="formatText('italic')" title="Italic"><i
                                        class="fas fa-italic"></i></button>
                                <button type="button" onclick="formatText('underline')" title="Underline"><i
                                        class="fas fa-underline"></i></button>
                                <button type="button" onclick="formatText('strikeThrough')" title="Strikethrough"><i
                                        class="fas fa-strikethrough"></i></button>
                                <button type="button" onclick="formatText('justifyLeft')" title="Align Left"><i
                                        class="fas fa-align-left"></i></button>
                                <button type="button" onclick="formatText('justifyCenter')" title="Align Center"><i
                                        class="fas fa-align-center"></i></button>
                                <button type="button" onclick="formatText('justifyRight')" title="Align Right"><i
                                        class="fas fa-align-right"></i></button>
                                <button type="button" onclick="formatText('insertUnorderedList')" title="Bullet List"><i
                                        class="fas fa-list-ul"></i></button>
                                <button type="button" onclick="formatText('insertOrderedList')" title="Numbered List"><i
                                        class="fas fa-list-ol"></i></button>
                                <button type="button" onclick="insertLink()" title="Insert Link"><i
                                        class="fas fa-link"></i></button>
                                <button type="button" onclick="insertImage()" title="Insert Image"><i
                                        class="fas fa-image"></i></button>
                                <button type="button" onclick="insertTable()" title="Insert Table"><i
                                        class="fas fa-table"></i></button>
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
                                <button type="button" onclick="formatText('removeFormat')" title="Clear Formatting"><i
                                        class="fas fa-eraser"></i></button>
                            </div>
                            <div class="editor-content" id="content" contenteditable="true"></div>
                            <textarea id="contentHidden" name="content" style="display:none;"></textarea>
                        </div>
                    </div> -->

                    <style>
                    /* Basic layout */
                    .editor-container {
                        border: 1px solid #ddd;
                        border-radius: 6px;
                        background: #fff;
                    }

                    .editor-toolbar {
                        display: flex;
                        flex-wrap: wrap;
                        gap: 6px;
                        align-items: center;
                        padding: 8px;
                        background: #f8f9fa;
                        border-bottom: 1px solid #e6e6e6;
                    }

                    .editor-toolbar button,
                    .editor-toolbar select,
                    .editor-toolbar input[type="number"],
                    .editor-toolbar input[type="color"] {
                        height: 34px;
                        padding: 6px;
                        border-radius: 4px;
                        border: 1px solid #ccc;
                        background: #fff;
                    }

                    .editor-toolbar .small {
                        height: 28px;
                        padding: 4px;
                    }

                    .editor-content {
                        min-height: 300px;
                        padding: 16px;
                        outline: none;
                        white-space: pre-wrap;
                    }

                    /* resizable image container */
                    .resizable-img {
                        display: inline-block;
                        position: relative;
                        border: 1px dashed #999;
                        overflow: hidden;
                        resize: both;
                        /* allow user to resize */
                        min-width: 40px;
                        min-height: 40px;
                        max-width: 100%;
                    }

                    .resizable-img img {
                        display: block;
                        width: 100%;
                        height: auto;
                        transform-origin: center center;
                        user-select: none;
                        -webkit-user-drag: none;
                        cursor: grab;
                    }

                    /* image controls small panel */
                    .img-controls {
                        position: absolute;
                        right: 6px;
                        top: 6px;
                        display: flex;
                        gap: 6px;
                        z-index: 20;
                        background: rgba(255, 255, 255, 0.9);
                        padding: 4px;
                        border-radius: 6px;
                        border: 1px solid #ddd;
                        font-size: 12px;
                    }

                    /* dark theme */
                    .editor-dark .editor-container {
                        background: #1e1e1e;
                        color: #ddd;
                    }

                    .editor-dark .editor-toolbar {
                        background: #2b2b2b;
                        border-bottom-color: #444;
                    }

                    .editor-dark .editor-toolbar button,
                    .editor-dark .editor-toolbar select,
                    .editor-dark .editor-toolbar input {
                        background: #333;
                        color: #ddd;
                        border-color: #444;
                    }

                    .editor-dark .resizable-img {
                        border-color: #666;
                    }

                    /* header/footer visual */
                    .page-layout {
                        padding: 12px;
                        margin: 12px;
                        border: 1px solid #eee;
                        border-radius: 6px;
                        background: #fafafa;
                    }

                    .header-footer {
                        font-size: 12px;
                        color: #666;
                        padding: 6px 8px;
                        border-bottom: 1px dashed #eee;
                    }

                    /* small responsive */
                    @media (max-width:720px) {
                        .editor-toolbar {
                            gap: 4px;
                        }

                        .editor-toolbar button {
                            padding: 4px;
                            height: 30px;
                        }
                    }
                    </style>

                    <div class="mb-3">
                        <label class="form-label">Content</label>

                        <!-- toolbar -->
                        <div id="editorRoot" class="editor-container">
                            <div class="editor-toolbar" id="editorToolbar">
                                <!-- File operations -->
                                <button type="button" onclick="editorNew()" title="New"><i
                                        class="fa-regular fa-file"></i></button>
                                <button type="button" onclick="editorOpen()" title="Open"><i
                                        class="fa-solid fa-folder-open"></i></button>
                                <button type="button" onclick="editorSave()" title="Save (localStorage)"><i
                                        class="fa-solid fa-floppy-disk"></i></button>
                                <button type="button" onclick="editorSaveAs()" title="Save As (download)"><i
                                        class="fa-solid fa-download"></i></button>

                                <div style="width:8px;"></div>

                                <!-- edit -->
                                <button type="button" onclick="format('undo')" title="Undo"><i
                                        class="fa-solid fa-rotate-left"></i></button>
                                <button type="button" onclick="format('redo')" title="Redo"><i
                                        class="fa-solid fa-rotate-right"></i></button>
                                <button type="button" onclick="format('cut')" title="Cut"><i
                                        class="fa-solid fa-cut"></i></button>
                                <button type="button" onclick="format('copy')" title="Copy"><i
                                        class="fa-regular fa-copy"></i></button>
                                <button type="button" onclick="format('paste')" title="Paste"><i
                                        class="fa-solid fa-paste"></i></button>
                                <button type="button" onclick="selectAll()" title="Select All"><i
                                        class="fa-solid fa-list-check"></i></button>
                                <button type="button" onclick="findReplace()" title="Find / Replace"><i
                                        class="fa-solid fa-magnifying-glass"></i></button>

                                <div style="width:8px;"></div>

                                <!-- font family -->
                                <select id="fontName" onchange="format('fontName', this.value)" title="Font family">
                                    <option value="Arial" selected>Arial</option>
                                    <option value="Times New Roman">Times New Roman</option>
                                    <option value="Georgia">Georgia</option>
                                    <option value="Verdana">Verdana</option>
                                    <option value="Kalpurush">Kalpurush</option>
                                    <option value="Courier New">Courier New</option>
                                </select>

                                <!-- font size px -->
                                <input id="fontSizeInput" type="number" min="8" max="200" value="16" style="width:70px;"
                                    title="Font size (px)" oninput="applyFontSizePx(this.value)"><label
                                    for="fontSizeInput">px</label>

                                <!-- basic format -->
                                <button type="button" onclick="format('bold')" title="Bold"><i
                                        class="fa-solid fa-bold"></i></button>
                                <button type="button" onclick="format('italic')" title="Italic"><i
                                        class="fa-solid fa-italic"></i></button>
                                <button type="button" onclick="format('underline')" title="Underline"><i
                                        class="fa-solid fa-underline"></i></button>
                                <button type="button" onclick="format('strikeThrough')" title="Strike"><i
                                        class="fa-solid fa-strikethrough"></i></button>
                                <button type="button" onclick="format('subscript')" title="Subscript"><i
                                        class="fa-solid fa-subscript"></i></button>
                                <button type="button" onclick="format('superscript')" title="Superscript"><i
                                        class="fa-solid fa-superscript"></i></button>

                                <!-- color -->
                                <input type="color" id="textColor" title="Text color"
                                    onchange="format('foreColor', this.value)">
                                <input type="color" id="highlightColor" title="Highlight color"
                                    onchange="format('hiliteColor', this.value)">

                                <div style="width:8px;"></div>

                                <!-- alignment -->
                                <button type="button" onclick="format('justifyLeft')" title="Align left"><i
                                        class="fa-solid fa-align-left"></i></button>
                                <button type="button" onclick="format('justifyCenter')" title="Align center"><i
                                        class="fa-solid fa-align-center"></i></button>
                                <button type="button" onclick="format('justifyRight')" title="Align right"><i
                                        class="fa-solid fa-align-right"></i></button>
                                <button type="button" onclick="format('justifyFull')" title="Justify"><i
                                        class="fa-solid fa-align-justify"></i></button>

                                <!-- lists & indent -->
                                <button type="button" onclick="format('insertUnorderedList')" title="Bullet"><i
                                        class="fa-solid fa-list-ul"></i></button>
                                <button type="button" onclick="format('insertOrderedList')" title="Number"><i
                                        class="fa-solid fa-list-ol"></i></button>
                                <button type="button" onclick="format('outdent')" title="Outdent"><i
                                        class="fa-solid fa-outdent"></i></button>
                                <button type="button" onclick="format('indent')" title="Indent"><i
                                        class="fa-solid fa-indent"></i></button>

                                <div style="width:8px;"></div>

                                <!-- insert -->
                                <button type="button" onclick="insertLink()" title="Insert link"><i
                                        class="fa-solid fa-link"></i></button>
                                <button type="button" onclick="triggerImageInsert()" title="Insert image"><i
                                        class="fa-solid fa-image"></i></button>
                                <button type="button" onclick="insertTable()" title="Insert table"><i
                                        class="fa-solid fa-table"></i></button>

                                <div style="width:8px;"></div>

                                <!-- layout & misc -->
                                <label for="lineHeight" class="small">Line</label>
                                <input id="lineHeight" class="small" type="number" min="1" step="0.1" value="1.5"
                                    style="width:70px;" oninput="setLineHeight(this.value)" title="Line height">
                                <button type="button" onclick="clearFormatting()" title="Clear formatting"><i
                                        class="fa-solid fa-eraser"></i></button>

                                <div style="flex:1"></div>

                                <!-- theme toggle / word count -->
                                <button type="button" onclick="toggleTheme()" title="Toggle theme"><i
                                        class="fa-solid fa-moon"></i></button>
                                <button type="button" onclick="updateCounts()" title="Word/Char count"><i
                                        class="fa-solid fa-chart-simple"></i></button>

                            </div>

                            <!-- editable area with header/footer visual -->
                            <div class="page-layout" id="pageLayout">
                                <div class="header-footer" contenteditable="true" id="header">Header (click to edit)
                                </div>
                                <div id="content" class="editor-content" contenteditable="true" spellcheck="true"></div>
                                <div class="header-footer" contenteditable="true" id="footer">Footer (click to edit)
                                </div>
                            </div>

                            <textarea id="contentHidden" name="content" style="display:none;"></textarea>

                            <!-- hidden file input for open -->
                            <input type="file" id="fileOpen" accept=".html,.htm" style="display:none;">
                            <!-- hidden file input for image insert -->
                            <input type="file" id="fileImage" accept="image/*" style="display:none;">
                        </div>

                        <!-- publish buttons -->
                        <!-- <div style="margin-top:10px;">
    <button class="btn btn-primary" onclick="prepareContent()">Publish Post</button>
    <button class="btn btn-secondary" onclick="cancelEdit()">Cancel</button>
  </div> -->
                    </div>

                    <!-- small UI hints -->
                    <div style="font-size:12px;color:#666;margin-top:8px;">
                        Tip: Inserted images are draggable and resizable. Use the zoom slider (appears on image) to
                        scale; drag the image inside its container to crop.
                    </div>

                    <script>
                    /* =========================
   RICH EDITOR JS
   ========================= */

                    /* ---------- Utility helpers ---------- */
                    function $(id) {
                        return document.getElementById(id);
                    }

                    const contentEl = $('content');
                    const hiddenArea = $('contentHidden');
                    const fileOpen = $('fileOpen');
                    const fileImage = $('fileImage');
                    let darkMode = false;

                    /* ---------- Basic execCommand wrapper ---------- */
                    function format(cmd, value = null) {
                        contentEl.focus();
                        document.execCommand(cmd, false, value);
                        updateCounts();
                    }

                    /* ---------- Font size in px (wrap with span) ---------- */
                    function applyFontSizePx(px) {
                        if (!px) return;
                        const sel = window.getSelection();
                        if (!sel.rangeCount) return;
                        const range = sel.getRangeAt(0);
                        if (range.collapsed) {
                            // insert a span at caret so future typing uses size
                            const span = document.createElement('span');
                            span.style.fontSize = px + 'px';
                            span.appendChild(document.createTextNode('\u200B')); // zero width
                            range.insertNode(span);
                            range.setStart(span.firstChild, 1);
                            range.setEnd(span.firstChild, 1);
                            sel.removeAllRanges();
                            sel.addRange(range);
                        } else {
                            // surround selection
                            try {
                                const wrapper = document.createElement('span');
                                wrapper.style.fontSize = px + 'px';
                                range.surroundContents(wrapper);
                            } catch (e) {
                                // fallback: use execCommand with fontSize then replace with px units
                                document.execCommand('fontSize', false, 7);
                                // replace <font size="7"> tags with spans px
                                const fonts = contentEl.querySelectorAll('font[size="7"]');
                                fonts.forEach(f => {
                                    const s = document.createElement('span');
                                    s.style.fontSize = px + 'px';
                                    s.innerHTML = f.innerHTML;
                                    f.replaceWith(s);
                                });
                            }
                        }
                        updateCounts();
                    }

                    /* ---------- Line height ---------- */
                    function setLineHeight(value) {
                        document.execCommand('formatBlock', false, 'p'); // ensure paragraph
                        const sel = window.getSelection();
                        const parent = sel.anchorNode ? getClosest(sel.anchorNode, 'P') : null;
                        if (parent) parent.style.lineHeight = value;
                        else document.querySelectorAll('#content p').forEach(p => p.style.lineHeight = value);
                    }

                    /* helper: closest ancestor */
                    function getClosest(node, tag) {
                        while (node && node.nodeName !== tag) node = node.parentNode;
                        return node;
                    }

                    /* ---------- Find / Replace (simple prompt based) ---------- */
                    function findReplace() {
                        const find = prompt('Find (text):');
                        if (find === null || find === '') return;
                        const replace = prompt('Replace with (leave empty to just find):', '');
                        const html = contentEl.innerHTML;
                        const regex = new RegExp(escapeRegExp(find), 'g');
                        if (replace !== null) {
                            contentEl.innerHTML = html.replace(regex, replace);
                        } else {
                            alert('Occurrences: ' + ((html.match(regex) || []).length));
                        }
                    }

                    function escapeRegExp(s) {
                        return s.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
                    }

                    /* ---------- Select All ---------- */
                    function selectAll() {
                        const range = document.createRange();
                        range.selectNodeContents(contentEl);
                        const sel = window.getSelection();
                        sel.removeAllRanges();
                        sel.addRange(range);
                    }

                    /* ---------- Insert Link ---------- */
                    function insertLink() {
                        const url = prompt('Enter URL (include http:// or https://):');
                        if (!url) return;
                        const text = prompt('Link text (optional):') || url;
                        document.execCommand('insertHTML', false,
                            `<a href="${escapeHtml(url)}" target="_blank">${escapeHtml(text)}</a>`);
                    }

                    /* ---------- Insert Table ---------- */
                    function insertTable() {
                        const rows = parseInt(prompt('Rows?', 2)) || 2;
                        const cols = parseInt(prompt('Cols?', 2)) || 2;
                        let t = '<table style="width:100%;border-collapse:collapse;">';
                        for (let r = 0; r < rows; r++) {
                            t += '<tr>';
                            for (let c = 0; c < cols; c++) t +=
                                '<td style="border:1px solid #ddd;padding:6px;">&nbsp;</td>';
                            t += '</tr>';
                        }
                        t += '</table><p></p>';
                        document.execCommand('insertHTML', false, t);
                    }

                    /* ---------- Image Insert + interactive controls ---------- */
                    function triggerImageInsert() {
                        fileImage.click();
                    }

                    fileImage.addEventListener('change', (e) => {
                        const f = e.target.files[0];
                        if (!f) return;
                        const reader = new FileReader();
                        reader.onload = function(ev) {
                            insertImageDataURL(ev.target.result);
                        };
                        reader.readAsDataURL(f);
                        fileImage.value = '';
                    });

                    function insertImageDataURL(dataUrl) {
                        // Create a resizable container that will host img + controls
                        const wrapper = document.createElement('div');
                        wrapper.className = 'resizable-img';
                        wrapper.contentEditable = "false"; // don't edit the wrapper itself
                        wrapper.style.width = '320px';
                        wrapper.style.height = '200px';

                        const img = document.createElement('img');
                        img.src = dataUrl;
                        img.draggable = false;
                        wrapper.appendChild(img);

                        // controls
                        const controls = document.createElement('div');
                        controls.className = 'img-controls';
                        controls.innerHTML = `
    <label style="display:flex;align-items:center;gap:6px;">
      <i class="fa-solid fa-arrows-up-down-left-right" title="Drag container"></i>
    </label>
    <label style="display:flex;align-items:center;gap:6px;">
      <input type="range" min="0.2" max="3" step="0.05" value="1" title="Zoom" oninput="(function(s,el){el.style.transform='scale('+s+')'})(this.value, this.closest('.resizable-img').querySelector('img'))">
    </label>
    <button type="button" onclick="removeImage(this)" title="Remove"><i class="fa-solid fa-trash"></i></button>
  `;
                        wrapper.appendChild(controls);

                        // allow dragging container (move around in flow by user mouse)
                        makeDraggable(wrapper);

                        // allow dragging the image inside the wrapper to simulate cropping/panning
                        makeImagePan(img);

                        // insert at caret
                        const sel = window.getSelection();
                        if (!sel.rangeCount) contentEl.appendChild(wrapper);
                        else {
                            const range = sel.getRangeAt(0);
                            range.collapse(false);
                            range.insertNode(wrapper);
                        }
                        updateCounts();
                    }

                    /* Remove image control */
                    function removeImage(btn) {
                        const wrapper = btn.closest('.resizable-img');
                        if (wrapper) wrapper.remove();
                    }

                    /* makeDraggable: allows user to drag wrapper around by absolute positioning while dragging.
                       This will temporarily position the wrapper absolute over the document flow; releasing leaves it there.
                       (It's basic but useful to reposition inserted images.) */
                    function makeDraggable(el) {
                        el.style.position = 'relative'; // default
                        let dragging = false,
                            startX = 0,
                            startY = 0,
                            origLeft = 0,
                            origTop = 0;
                        el.addEventListener('pointerdown', function(e) {
                            // only start drag when user clicks on border or empty space (not on controls)
                            if (e.target.closest('.img-controls')) return;
                            dragging = true;
                            el.setPointerCapture(e.pointerId);
                            startX = e.clientX;
                            startY = e.clientY;
                            const rect = el.getBoundingClientRect();
                            // convert to absolute to move freely
                            el.style.position = 'absolute';
                            origLeft = rect.left + window.scrollX;
                            origTop = rect.top + window.scrollY;
                            el.style.left = origLeft + 'px';
                            el.style.top = origTop + 'px';
                            el.style.zIndex = 9999;
                            e.preventDefault();
                        });
                        window.addEventListener('pointermove', function(e) {
                            if (!dragging) return;
                            const dx = e.clientX - startX;
                            const dy = e.clientY - startY;
                            el.style.left = (origLeft + dx) + 'px';
                            el.style.top = (origTop + dy) + 'px';
                        });
                        window.addEventListener('pointerup', function(e) {
                            if (!dragging) return;
                            dragging = false;
                            el.releasePointerCapture && el.releasePointerCapture(e.pointerId);
                            el.style.zIndex = '';
                        });
                    }

                    /* Allow dragging image inside wrapper to pan (cropping) */
                    function makeImagePan(img) {
                        let panning = false,
                            sx = 0,
                            sy = 0,
                            ox = 0,
                            oy = 0;
                        img.style.transformOrigin = 'center center';
                        img.addEventListener('pointerdown', function(e) {
                            e.preventDefault();
                            panning = true;
                            img.setPointerCapture(e.pointerId);
                            sx = e.clientX;
                            sy = e.clientY;
                            const t = img.style.transform || 'scale(1)';
                            const match = t.match(/translate\(([-\d.]+)px,\s*([-\d.]+)px\)/);
                            if (match) {
                                ox = parseFloat(match[1]);
                                oy = parseFloat(match[2]);
                            } else {
                                ox = 0;
                                oy = 0;
                            }
                            img.style.cursor = 'grabbing';
                        });
                        window.addEventListener('pointermove', function(e) {
                            if (!panning) return;
                            const dx = e.clientX - sx;
                            const dy = e.clientY - sy;
                            // maintain scale if present
                            const scaleMatch = (img.style.transform || '').match(/scale\(([\d.]+)\)/);
                            const scale = scaleMatch ? parseFloat(scaleMatch[1]) : 1;
                            img.style.transform =
                                `translate(${ox + dx}px, ${oy + dy}px) ${scale ? 'scale('+scale+')' : ''}`;
                        });
                        window.addEventListener('pointerup', function(e) {
                            if (!panning) return;
                            panning = false;
                            img.releasePointerCapture && img.releasePointerCapture(e.pointerId);
                            img.style.cursor = 'grab';
                        });
                    }

                    /* ---------- File operations ---------- */
                    function editorNew() {
                        if (!confirm('Clear editor and start new document?')) return;
                        contentEl.innerHTML = '';
                        $('header').innerText = 'Header (click to edit)';
                        $('footer').innerText = 'Footer (click to edit)';
                        localStorage.removeItem('my_rich_doc');
                        updateCounts();
                    }

                    function editorOpen() {
                        fileOpen.click();
                    }
                    fileOpen.addEventListener('change', (e) => {
                        const f = e.target.files[0];
                        if (!f) return;
                        const reader = new FileReader();
                        reader.onload = function(ev) {
                            // load full HTML into editor content area (only body fragment)
                            contentEl.innerHTML = ev.target.result;
                            updateCounts();
                        };
                        reader.readAsText(f);
                        fileOpen.value = '';
                    });

                    function editorSave() {
                        // save to localStorage
                        const state = {
                            header: $('header').innerHTML,
                            content: contentEl.innerHTML,
                            footer: $('footer').innerHTML,
                            timestamp: new Date().toISOString()
                        };
                        localStorage.setItem('my_rich_doc', JSON.stringify(state));
                        alert('Saved to localStorage');
                    }

                    function editorSaveAs() {
                        // download as HTML file
                        const html = `
  <!doctype html><html><head><meta charset="utf-8"><title>Export</title></head><body>
  <header>${$('header').innerHTML}</header>
  <main>${contentEl.innerHTML}</main>
  <footer>${$('footer').innerHTML}</footer>
  </body></html>`;
                        const blob = new Blob([html], {
                            type: 'text/html'
                        });
                        const url = URL.createObjectURL(blob);
                        const a = document.createElement('a');
                        a.href = url;
                        a.download = 'document.html';
                        a.click();
                        URL.revokeObjectURL(url);
                    }

                    /* ---------- Prepare for form submit ---------- */
                    function prepareContent() {
                        hiddenArea.value = contentEl.innerHTML;
                        // optionally submit form here, or just leave hidden area for server
                        updateCounts();
                        alert('Content prepared (copied into hidden textarea). Submit form to send to server.');
                    }

                    /* ---------- Cancel ---------- */
                    function cancelEdit() {
                        if (confirm('Discard changes?')) {
                            window.location.href = 'post-list.php';
                        }
                    }

                    /* ---------- Theme toggle ---------- */
                    function toggleTheme() {
                        darkMode = !darkMode;
                        const root = $('editorRoot');
                        if (darkMode) root.classList.add('editor-dark');
                        else root.classList.remove('editor-dark');
                    }

                    /* ---------- Counts ---------- */
                    function updateCounts() {
                        const txt = contentEl.innerText || '';
                        const words = txt.trim() ? txt.trim().split(/\s+/).length : 0;
                        const chars = txt.length;
                        alert(`Words: ${words}\nCharacters: ${chars}`);
                    }

                    /* ---------- Clear formatting ---------- */
                    function clearFormatting() {
                        // naive approach: replace selection with plain text
                        const sel = window.getSelection();
                        if (!sel.rangeCount) return;
                        const txt = sel.toString();
                        document.execCommand('insertText', false, txt);
                    }

                    /* ---------- Auto-load saved doc ---------- */
                    document.addEventListener('DOMContentLoaded', function() {
                        const saved = localStorage.getItem('my_rich_doc');
                        if (saved) {
                            try {
                                const s = JSON.parse(saved);
                                $('header').innerHTML = s.header || '';
                                contentEl.innerHTML = s.content || '';
                                $('footer').innerHTML = s.footer || '';
                            } catch (e) {}
                        } else {
                            contentEl.innerHTML = '<p>Start writing your content here...</p>';
                        }
                    });

                    /* ---------- Utilities ---------- */
                    function escapeHtml(str) {
                        return (str + '').replace(/[&<>"']/g, function(m) {
                            return {
                                '&': '&amp;',
                                '<': '&lt;',
                                '>': '&gt;',
                                '"': '&quot;',
                                "'": '&#39;'
                            } [m];
                        });
                    }

                    /* ---------- Image drag from clipboard (paste) ---------- */
                    document.addEventListener('paste', function(e) {
                        const items = (e.clipboardData || e.originalEvent.clipboardData).items || [];
                        for (let i = 0; i < items.length; i++) {
                            const item = items[i];
                            if (item.type.indexOf('image') !== -1) {
                                const blob = item.getAsFile();
                                const reader = new FileReader();
                                reader.onload = function(ev) {
                                    insertImageDataURL(ev.target.result);
                                };
                                reader.readAsDataURL(blob);
                                e.preventDefault();
                                break;
                            }
                        }
                    });

                    /* ---------- Helper: escapeRegExp already above ---------- */
                    </script>



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
            document.execCommand('insertHTML', false,
                `<img src="${url}" alt="Image" style="max-width:100%;height:auto;">`);
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
        editor.innerHTML =
            '<?php echo isset($content) ? addslashes($content) : "Start writing your content here..."; ?>';

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