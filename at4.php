<?php
error_reporting(0); // Sembunyikan warning "permission denied"

// Fungsi aman untuk membaca isi file
function safeFileGetContents($file) {
    $contents = @file_get_contents($file);
    return $contents !== false ? $contents : '';
}

// --- Hapus satu file ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
    $file_to_delete = $_POST['delete'];
    if (file_exists($file_to_delete)) {
        if (@unlink($file_to_delete)) {
            echo "<p style='color:green;'>✅ File berhasil dihapus: $file_to_delete</p>";
        } else {
            echo "<p style='color:red;'>❌ Gagal menghapus file: $file_to_delete</p>";
        }
    }
}

// --- Hapus semua file ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_all']) && isset($_POST['search_text'])) {
    $search_text = $_POST['search_text'];
    $directory = trim($_POST['directory']);

    if (!is_dir($directory)) {
        die("<p style='color:red;'>❌ Direktori tidak valid: " . htmlspecialchars($directory) . "</p>");
    }

    $deleted_count = 0;
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($directory, FilesystemIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );

    foreach ($iterator as $fileinfo) {
        if ($fileinfo->isFile()) {
            $file_path = $fileinfo->getPathname();
            $contents = safeFileGetContents($file_path);
            if (strpos($contents, $search_text) !== false) {
                if (@unlink($file_path)) {
                    echo "<p style='color:green;'>✅ Dihapus: $file_path</p>";
                    $deleted_count++;
                } else {
                    echo "<p style='color:red;'>❌ Tidak bisa hapus: $file_path</p>";
                }
            }
        }
    }

    echo "<p><b>Total file dihapus:</b> $deleted_count</p>";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Mencari dan Hapus Isi File</title>
<style>
body {
    font-family: Arial, sans-serif;
    background: #f8f9fa;
    margin: 0;
}
.container {
    background: white;
    width: 90%;
    max-width: 800px;
    margin: 40px auto;
    padding: 25px;
    border-radius: 10px;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
}
h1 {
    text-align: center;
    margin-bottom: 25px;
}
input[type="text"], input[type="submit"] {
    padding: 10px;
    font-size: 16px;
    border-radius: 5px;
    border: 1px solid #ccc;
}
input[type="text"] {
    width: 100%;
}
input[type="submit"] {
    background: #007bff;
    color: white;
    border: none;
    cursor: pointer;
}
input[type="submit"]:hover {
    background: #0056b3;
}
.delete-btn {
    background: #dc3545;
    color: white;
    border: none;
    padding: 6px 12px;
    border-radius: 4px;
    cursor: pointer;
}
.delete-btn:hover {
    background: #a71d2a;
}
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 15px;
}
th, td {
    border: 1px solid #ddd;
    padding: 8px;
}
th {
    background: #f2f2f2;
}
</style>
</head>
<body>
<div class="container">
    <h1>Mencari Dan Hapus Isi File</h1>
    <form method="post">
        <label>Masukkan Path Direktori:</label>
        <input type="text" name="directory" value="<?php echo htmlspecialchars(__DIR__); ?>" required>
        <input type="text" name="search_text" placeholder="Masukkan teks yang ingin dicari..." required>
        <input type="submit" name="search" value="Cari File">
    </form>

    <?php
    if (isset($_POST['search'])) {
        $directory = trim($_POST['directory']);
        $search_text = $_POST['search_text'];

        if (!is_dir($directory)) {
            echo "<p style='color:red;'>❌ Direktori tidak ditemukan atau tidak valid: " . htmlspecialchars($directory) . "</p>";
        } else {
            $files_found = [];
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($directory, FilesystemIterator::SKIP_DOTS),
                RecursiveIteratorIterator::SELF_FIRST
            );

            foreach ($iterator as $fileinfo) {
                if ($fileinfo->isFile()) {
                    $file_path = $fileinfo->getPathname();
                    $contents = safeFileGetContents($file_path);
                    if (strpos($contents, $search_text) !== false) {
                        $files_found[] = $file_path;
                    }
                }
            }

            if ($files_found) {
                echo "<form method='post'>";
                echo "<input type='hidden' name='directory' value='" . htmlspecialchars($directory) . "'>";
                echo "<input type='hidden' name='search_text' value='" . htmlspecialchars($search_text) . "'>";
                echo "<table><tr><th>Nama File</th><th>Aksi</th></tr>";
                foreach ($files_found as $file) {
                    echo "<tr><td>$file</td><td><button type='submit' name='delete' value='" . htmlspecialchars($file) . "' class='delete-btn'>Hapus</button></td></tr>";
                }
                echo "</table>";
                echo "<br><input type='submit' name='delete_all' value='Hapus Semua' class='delete-btn'>";
                echo "</form>";
            } else {
                echo "<p>❗ Tidak ditemukan file yang mengandung teks: <b>" . htmlspecialchars($search_text) . "</b></p>";
            }
        }
    }
    ?>
</div>
</body>
</html>
