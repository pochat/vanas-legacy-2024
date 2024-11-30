<?php
echo "1 \n\r";
$ch = curl_init('https://www.howsmyssl.com/a/check');
echo "2 \n\r";
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
echo "3 \n\r";
$data = curl_exec($ch);
echo "4 \n\r";
curl_close($ch);
echo "5 \n\r";
$json = json_decode($data);
echo $json->tls_version;

echo "\n";


$curl_info = curl_version();
echo $curl_info['ssl_version'];

?>

