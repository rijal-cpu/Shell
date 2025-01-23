<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP Searcher Tool</title>
    <style>
        body {
            background-color: #1e1e1e;
            color: #00ff00;
            font-family: Arial, sans-serif;
        }
        .container {
            width: 100%;
            max-width: 900px;
            margin: 0 auto;
            padding: 20px;
        }
        .search-box {
            margin-bottom: 20px;
        }
        .search-box input, .search-box select {
            padding: 10px;
            margin-right: 10px;
            border-radius: 5px;
            border: 1px solid #00ff00;
            background-color: #333;
            color: #00ff00;
        }
        .result-box {
            background-color: #222;
            padding: 20px;
            border-radius: 5px;
            border: 1px solid #00ff00;
        }
        .result-box pre {
            white-space: pre-wrap;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Searcher</h1>
    <div class="search-box">
        <form method="POST" action="">
            <label for="method">Method:</label>
            <select name="method" id="method">
                <option value="readable">Find All Readable Files</option>
                <option value="writable">Find All Writable Files</option>
                <option value="by_name">Find Files by Name</option>
            </select>

            <label for="dir">Directory:</label>
            <input type="text" name="dir" id="dir" placeholder="/var/www/html/" required>

            <label for="ext">Extension (optional):</label>
            <input type="text" name="ext" id="ext" placeholder="*.css">

            <button type="submit">Search</button>
        </form>
    </div>

    <div class="result-box">
        <pre>
        <?php
        // Fungsi untuk mencari file berdasarkan metode yang dipilih
        function findFiles($dir, $method, $ext = '*') {
            if (!is_dir($dir)) {
                return "Directory not found!";
            }

            $iterator = new RecursiveDirectoryIterator($dir);
            $files = [];

            foreach (new RecursiveIteratorIterator($iterator) as $file) {
                if ($ext != '*' && !fnmatch($ext, $file->getFilename())) {
                    continue;
                }

                switch ($method) {
                    case 'readable':
                        if (is_readable($file)) {
                            // Menambahkan padding agar hasil terlihat rapi
                            $files[] = str_pad($file->getPathname(), 120, ' ', STR_PAD_RIGHT);
                        }
                        break;
                    case 'writable':
                        if (is_writable($file)) {
                            // Menambahkan padding agar hasil terlihat rapi
                            $files[] = str_pad($file->getPathname(), 120, ' ', STR_PAD_RIGHT);
                        }
                        break;
                    case 'by_name':
                        // Menambahkan padding agar hasil terlihat rapi
                        $files[] = str_pad($file->getPathname(), 120, ' ', STR_PAD_RIGHT);
                        break;
                }
            }

            return $files ? implode(PHP_EOL, $files) : "No files found!";
        }

        // Jika form disubmit, jalankan proses pencarian file
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $dir = $_POST['dir'];
            $method = $_POST['method'];
            $ext = isset($_POST['ext']) && !empty($_POST['ext']) ? $_POST['ext'] : '*';

            $result = findFiles($dir, $method, $ext);
            echo htmlspecialchars($result);
        }
        ?>
        </pre>
    </div>
</div>

</body>
</html>
