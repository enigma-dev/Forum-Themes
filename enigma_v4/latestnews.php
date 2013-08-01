<?php
include("/home/enigma/public_html/forums/SSI.php");
$array = ssi_boardNews(1.0, 1, null, null, 'array');
foreach ($array as $news) { echo '<a href="', $news['href'], '"><span style="color:#000000;">', $news['subject'], '</span></a>'; }
?>