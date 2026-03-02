<?php
$output = "";  // Menyimpan output yang akan ditampilkan di bawah form

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Mengambil path direktori dari form input
    $directory = $_POST['directory'];
    
    // Memeriksa apakah direktori yang dimasukkan valid
    if (!is_dir($directory)) {
        $output = "<p>Direktori yang dimasukkan tidak valid atau tidak ada.</p>";
    } else {
        $start_date = strtotime('2026-01-01'); // 1 Januari 2025
        $end_date = strtotime('2026-12-31 23:59:59'); // 31 Desember 2025
        
        // Membuka direktori
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($directory, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );
        
        foreach ($iterator as $fileinfo) {
            $file_path = $fileinfo->getRealPath();
            
            // Mengecek apakah file dimodifikasi dalam rentang waktu 2025
            if ($fileinfo->isFile() && filemtime($file_path) >= $start_date && filemtime($file_path) <= $end_date) {
                // Menghapus file yang dimodifikasi pada tahun 2025
                if (unlink($file_path)) {
                    $output .= "<p>File berhasil dihapus: $file_path</p>";
                } else {
                    $output .= "<p>Gagal menghapus file: $file_path</p>";
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Files Modified in 2025</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 50%;
            margin: 50px auto;
            padding: 20px;
            background-color: white;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        h1 {
            color: #333;
        }

        form {
            margin-top: 20px;
        }

        input[type="text"] {
            padding: 10px;
            width: 80%;
            margin-bottom: 10px;
        }

        button {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
            font-size: 16px;
        }

        button:hover {
            background-color: #45a049;
        }

        .output {
            margin-top: 20px;
            text-align: left;
            padding: 10px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Delete Files Modified in 2025</h1>
        <form action="" method="post">
            <label for="directory">Enter Directory Path:</label><br>
            <input type="text" id="directory" name="directory" value="<?php echo htmlspecialchars(__DIR__); ?>" required><br><br>
            <button type="submit">Delete Files Modified in 2025</button>
        </form>

        <!-- Output section for results -->
        <div class="output">
            <?php echo $output; ?>
        </div>
    </div>
</body>
</html>

