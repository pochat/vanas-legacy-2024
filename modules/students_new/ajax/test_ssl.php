<?php

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://tlstest.paypal.com');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FAILONERROR, true);
curl_setopt($ch, CURLOPT_SSLVERSION, 6); // CURL_SSLVERSION_TLSv1_2
$result = curl_exec($ch);
echo 'result = '.$result.'<br>';
echo 'errno = '.curl_errno($ch).'<br>';
echo 'error = '.curl_error($ch).'<br>';
curl_close($ch);



echo "\n";

//var_dump(curl_version());

echo "\n";


?>

