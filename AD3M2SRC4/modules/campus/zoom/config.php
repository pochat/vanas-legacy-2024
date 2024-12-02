<?php

require_once '../../../../vendor/autoload.php';
require_once "class-db.php";
 
#Testing cuenta mike
//define('CLIENT_ID', 'tYdKelToG7iucmVKP5w');
//define('CLIENT_SECRET', 'orDt0VMWXj05IzN9X8OvGwjSyJAoA5hi');
//define('REDIRECT_URI', 'https://dev.vanas.ca/AD3M2SRC4/modules/campus/zoom/callback.php');
//host id= 
echo"<script>alert('entro');</script>";

$licencia=4;

if($licencia==1){

#1.mario@vanas.ca
define('CLIENT_ID', 'tHTALDdtTbaU1r8unxMRQ');
define('CLIENT_SECRET', 'XfxS4Ic6AKQXhAG87Z284hbcQD79xGLB');
define('REDIRECT_URI', 'https://campus.vanas.ca/AD3M2SRC4/modules/campus/zoom/callback.php');
define('HOST','mario@vanas.ca');

#Para test.
define('HOST','mario@vanas.ca');

}
if($licencia==2){
#2.info@vanas.ca
define('CLIENT_ID', 'kLNMzM_GTJy5zVZyqiikcw');
define('CLIENT_SECRET', '4VnxHqRD67Bz1428yt60MtWKB8u9RUq1');
define('REDIRECT_URI', 'https://campus.vanas.ca/AD3M2SRC4/modules/campus/zoom/callback.php');
define('HOST','info@vanas.ca');
}
if($licencia==3){
#admin@vanas.ca
define('CLIENT_ID', 'kGsV4_VYS8KxJGTS5monhA');
define('CLIENT_SECRET', 'oR3PjhE0P65b4GQJIYuYmv5H0csf6jVH');
define('REDIRECT_URI', 'https://campus.vanas.ca/AD3M2SRC4/modules/campus/zoom/callback.php');
define('HOST','admin@vanas.ca');
}

if($licencia==4){
#class01@vanas.ca 	host-id:h512Oq5gQ5GaNDM9M5KMeA  
define('CLIENT_ID', 'Qq06npmkT0uNMWsT9JPaOQ');
define('CLIENT_SECRET', '8kiH3xejsA4a1Ds92xNBgZhrp0HGZxrN');
define('REDIRECT_URI', 'https://campus.vanas.ca/AD3M2SRC4/modules/campus/zoom/callback.php');
define('HOST','class01@vanas.ca');
}

 
?>