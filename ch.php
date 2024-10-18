<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Auto Chmod Tools</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            color: #333;
        }
        label {
            display: block;
            margin-top: 10px;
            color: #555;
        }
        input[type="text"] {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #45a049;
        }
        .message {
            margin-top: 20px;
            padding: 10px;
            border-radius: 5px;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Auto Chmod Tools</h1>
        <form method="post">
            <label for="path">Path:</label>
            <input type="text" id="path" name="path" required>
            <label for="dirPermissions">Directory Permissions:</label>
            <input type="text" id="dirPermissions" name="dirPermissions" value="0755" required>
            <label for="filePermissions">File Permissions:</label>
            <input type="text" id="filePermissions" name="filePermissions" value="0644" required>
            <input type="submit" value="Change Permissions">
        </form>

        <?php
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $path = $_POST['path'];
            $dirPermissions = octdec($_POST['dirPermissions']);
            $filePermissions = octdec($_POST['filePermissions']);

            function autoChmod($path, $dirPermissions, $filePermissions) {
                // Pastikan path yang diberikan adalah direktori
                if (!is_dir($path)) {
                    echo "<div class='message error'>$path is not a valid directory</div>";
                    return;
                }

                // Fungsi untuk mengubah permission
                $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path), RecursiveIteratorIterator::SELF_FIRST);
                foreach ($iterator as $item) {
                    if ($item->isDir()) {
                        chmod($item, $dirPermissions);
                    } else {
                        chmod($item, $filePermissions);
                    }
                }
                echo "<div class='message success'>Permissions changed successfully!</div>";
            }

            // Memanggil fungsi dengan path yang diinginkan
            autoChmod($path, $dirPermissions, $filePermissions);
        }
        ?>
    </div>
</body>
</html>
