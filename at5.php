<?php
error_reporting(0);

function safeFileGetContents($file) {
    $data = @file_get_contents($file);
    return $data !== false ? $data : '';
}

// Fungsi pencarian manual (rekursif)
function searchFiles($dir, $keyword, &$results) {
    if (!is_readable($dir)) return;

    $files = scandir($dir);
    foreach ($files as $file) {
        if ($file === '.' || $file === '..') continue;
        $path = $dir . DIRECTORY_SEPARATOR . $file;

        if (is_dir($path)) {
            searchFiles($path, $keyword, $results); // rekursi
        } elseif (is_file($path)) {
            $content = safeFileGetContents($path);
            if (strpos($content, $keyword) !== false) {
                $results[] = $path;
            }
        }
    }
}

// Hapus satu file
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
    $file = $_POST['delete'];
    if (is_file($file)) {
        if (@unlink($file)) {
            echo "<p style='color:green;'>✅ File dihapus: $file</p>";
        } else {
            echo "<p style='color:red;'>❌ Gagal hapus: $file</p>";
        }
    }
}

// Hapus semua file
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_all'])) {
    $keyword = $_POST['search_text'];
    $dir = $_POST['directory'];
    $deleted = 0;

    $results = [];
    searchFiles($dir, $keyword, $results);

    foreach ($results as $f) {
        if (@unlink($f)) {
            $deleted++;
            echo "<p style='color:green;'>✅ Dihapus: $f</p>";
        }
    }

    echo "<p><b>Total dihapus:</b> $deleted file</p>";
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
    background-color: #f8f9fa;
    margin: 0;
}
.container {
    background: white;
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
    width: 100%;
    padding: 10px;
    font-size: 16px;
    border: 1px solid #ccc;
    border-radius: 5px;
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
    $dir = $_POST['directory'];
    $keyword = $_POST['search_text'];

    if (!is_dir($dir)) {
        echo "<p style='color:red;'>❌ Direktori tidak valid: " . htmlspecialchars($dir) . "</p>";
    } else {
        $found = [];
        searchFiles($dir, $keyword, $found);

        if ($found) {
            echo "<form method='post'>";
            echo "<input type='hidden' name='directory' value='" . htmlspecialchars($dir) . "'>";
            echo "<input type='hidden' name='search_text' value='" . htmlspecialchars($keyword) . "'>";
            echo "<table><tr><th>Nama File</th><th>Aksi</th></tr>";
            foreach ($found as $f) {
                echo "<tr><td>" . htmlspecialchars($f) . "</td>";
                echo "<td><button class='delete-btn' name='delete' value='" . htmlspecialchars($f) . "'>Hapus</button></td></tr>";
            }
            echo "</table><br>";
            echo "<input type='submit' name='delete_all' class='delete-btn' value='Hapus Semua'>";
            echo "</form>";
        } else {
            echo "<p>❗ Tidak ditemukan file yang mengandung: <b>" . htmlspecialchars($keyword) . "</b></p>";
        }
    }
}
?>
</div>
</body>
</html>
