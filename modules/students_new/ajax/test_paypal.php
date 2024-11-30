<?php
 # Libreria de funciones
 require("../../common/lib/cam_general.inc.php");
 
  $paypal_url = "https://www.paypal.com/cgi-bin/webscr";
  // $paypal_url = "https://ipnpb.paypal.com/cgi-bin/webscr";
  $ch = curl_init($paypal_url);
  // curl_setopt($ch, CURLOPT_SSLVERSION, 6);
  curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
  echo date('[Y-m-d H:i e] '). "CURLOPT_HTTP_VERSION :".CURLOPT_HTTP_VERSION."<br>";
  
  curl_setopt($ch, CURLOPT_POST, 1);
  echo date('[Y-m-d H:i e] '). "CURLOPT_POST :".CURLOPT_POST."<br>";
  
  curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
  echo date('[Y-m-d H:i e] '). "CURLOPT_RETURNTRANSFER :".CURLOPT_RETURNTRANSFER."<br>";
  
  curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
  echo date('[Y-m-d H:i e] '). "CURLOPT_POSTFIELDS :".CURLOPT_POSTFIELDS."<br>";
  
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
  echo date('[Y-m-d H:i e] '). "CURLOPT_SSL_VERIFYPEER :".CURLOPT_SSL_VERIFYPEER."<br>";
  
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
  echo date('[Y-m-d H:i e] '). "CURLOPT_SSL_VERIFYHOST :".CURLOPT_SSL_VERIFYHOST."<br>";
  
  curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
  echo date('[Y-m-d H:i e] '). "CURLOPT_FORBID_REUSE :".CURLOPT_FORBID_REUSE."<br>";
  
  curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: Close'));
  echo date('[Y-m-d H:i e] '). "CURLOPT_HTTPHEADER :".CURLOPT_HTTPHEADER."<br>";
  
  // In wamp-like environments that do not come bundled with root authority certificates,
  // please download 'cacert.pem' from "https://curl.haxx.se/docs/caextract.html" and set
  // the directory path of the certificate as shown below:
  curl_setopt($ch, CURLOPT_CAINFO, dirname(__FILE__) . '/paypal_cacert/cacert.pem');
  echo date('[Y-m-d H:i e] '). "CURLOPT_CAINFO :".CURLOPT_CAINFO."<br>";
  // $res = curl_exec($ch);
  // $error = curl_error($ch);
  // var_dump($res)."<br>**";
  // var_dump($error);
  if ( !($res = curl_exec($ch)) ) {
    echo date('[Y-m-d H:i e] '). "when processing IPN data :".curl_error($ch)."<br>";
    
    curl_close($ch);
    exit;
  }
  $close = curl_close($ch);
  echo "ya cerro";
  /*$paypal_url = ObtenConfiguracion(61)
  $ch = curl_init($paypal_url);
  echo date('[Y-m-d H:i e] '). "CH:".$ch;
  if ($ch == FALSE) {
    echo date('[Y-m-d H:i e] '). "ERRROR CH:".$ch;
    return FALSE;
  }
  curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
  echo date('[Y-m-d H:i e] '). "ERRROR CH:".$ch;
  curl_setopt($ch, CURLOPT_POST, 1);
  echo date('[Y-m-d H:i e] '). "ERRROR CH:".$ch;
  curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
  echo date('[Y-m-d H:i e] '). "ERRROR CH:".$ch;
  curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
  echo date('[Y-m-d H:i e] '). "ERRROR CH:".$ch;
  // True verificara el certicado False no lo verificara se saltara este paso
  # On 1 Off 0 always off en local host es Off en produccion On
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, ObtenConfiguracion(79));
  echo date('[Y-m-d H:i e] '). "ERRROR CH:".$ch;
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
  echo date('[Y-m-d H:i e] '). "ERRROR CH:".$ch;
  curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
  echo date('[Y-m-d H:i e] '). "ERRROR CH:".$ch;

    curl_setopt($ch, CURLOPT_HEADER, 1);
    echo date('[Y-m-d H:i e] '). "ERRROR CH:".$ch;
    curl_setopt($ch, CURLINFO_HEADER_OUT, 1);
    echo date('[Y-m-d H:i e] '). "ERRROR CH:".$ch;
  
  // CONFIG: Optional proxy configuration
  //curl_setopt($ch, CURLOPT_PROXY, $proxy);
  //curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, 1);
  // Set TCP timeout to 30 seconds
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
  echo date('[Y-m-d H:i e] '). "ERRROR CH:".$ch;
  curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: Close'));
  echo date('[Y-m-d H:i e] '). "ERRROR CH:".$ch;
  // CONFIG: Please download 'cacert.pem' from "http://curl.haxx.se/docs/caextract.html" and set the directory path
  // of the certificate as shown below. Ensure the file is readable by the webserver.
  // This is mandatory for some environments.
  $cert = "/var/www/html/vanas/modules/students_new/ajax/certs/cacert.pem";
  curl_setopt($ch, CURLOPT_CAINFO, $cert);
  echo date('[Y-m-d H:i e] '). "ERRROR CH:".$ch;
  $res = curl_exec($ch);
  echo date('[Y-m-d H:i e] '). "ERRROR CH:".$ch;
  if (curl_errno($ch) != 0){ // cURL error{
    echo date('[Y-m-d H:i e] '). "Can't connect to PayPal to validate IPN message: " . curl_error($ch);    
    curl_close($ch);
    exit;
  } 
  else {
      // Log the entire HTTP response if debug is switched on.
     echo date('[Y-m-d H:i e] '). "HTTP request of validation request:". curl_getinfo($ch, CURLINFO_HEADER_OUT) ." for IPN payload: $req";
     echo date('[Y-m-d H:i e] '). "HTTP response of validation request: $res";
      curl_close($ch);
  }*/

?>