<?php 
ob_start ("ob_gzhandler");
header("Content-type: text/css; charset= UTF-8");
header("Cache-Control: must-revalidate");
$expires_time = 1440;
$offset = 60 * $expires_time ;
$ExpStr = "Expires: " . 
gmdate("D, d M Y H:i:s",
time() + $offset) . " GMT";
header($ExpStr);
                ?>

/*** style.css ***/

.countdown {position: relative; text-align: center !important; width: 100%; padding: 15px 0px 15px 0px;border: 1px solid #ccc;}.countdown_title {display: block; font-weight: bold;}.countdown_displaydate {display: block; color: #ccc;}.countdown_daycount {display: block; color:#2B7CBE;font-size:50px; font-weight:bold; margin:0px; padding:0px; line-height:normal;}.countdown_dney {display: block; color: #ccc;}.countdown_link {display: block; }.countdown_hourcount{display: block; color:#2B7CBE;font-size:20px; font-weight:bold; margin:0px; padding:0px; line-height:normal;}