<?php
  
  # Libreria de funciones
  require("../../common/lib/cam_general.inc.php");
  
  // CONFIG: Enable debug mode. This means we'll log requests into 'ipn.log' in the same directory.
  // Especially useful if you encounter network errors or other intermittent problems with IPN (validation).
  // Set this to 0 once you go live or don't require logging.
  define("DEBUG", 1);
  define("DEBUG_LOG", ObtenConfiguracion(84)); // esta variable es para encontrar error siempre debe estar en off
define("DEBUG_LOG", 1);

  define("LOG_FILE", "./ipn.log");
  // Read POST data
  // reading posted data directly from $_POST causes serialization
  // issues with array data in POST. Reading raw POST data from input stream instead.
  $raw_post_data = file_get_contents('php://input');
  $raw_post_array = explode('&', $raw_post_data);
  $myPost = array();
  foreach ($raw_post_array as $keyval) {
    $keyval = explode ('=', $keyval);
    if (count($keyval) == 2){
      if(DEBUG_LOG == 1)
        error_log(date('[Y-m-d H:i e] '). "Valores recibidos:".$myPost[$keyval[0]] = urldecode($keyval[1]). PHP_EOL, 3, LOG_FILE);
      $myPost[$keyval[0]] = urldecode($keyval[1]);      
    }
  }
  // read the post from PayPal system and add 'cmd'
  $req = 'cmd=_notify-validate';
  if(function_exists('get_magic_quotes_gpc')) {
    $get_magic_quotes_exists = true;
  }
  foreach ($myPost as $key => $value) {
    if($get_magic_quotes_exists == true && get_magic_quotes_gpc() == 1) {
      $value = urlencode(stripslashes($value));
    } else {
      $value = urlencode($value);
    }
    $req .= "&$key=$value";
  }
  
  // Post IPN data back to PayPal to validate the IPN data is genuine
  // Without this step anyone can fake IPN data
  $paypal_url = ObtenConfiguracion(61);
  $paypal_url = "https://www.sandbox.paypal.com/cgi-bin/webscr";
  
  $ch = curl_init($paypal_url);
  if(DEBUG_LOG == 1)
    error_log(date('[Y-m-d H:i e] '). "CH:".$ch. PHP_EOL, 3, LOG_FILE);
  if ($ch == FALSE) {
    return FALSE;
  }


error_log(date('[Y-m-d H:i e] '). "Valores recibidos:".$req. PHP_EOL, 3, LOG_FILE);


  curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
  // True verificara el certicado False no lo verificara se saltara este paso
  # On 1 Off 0 always off en local host es Off en produccion On
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, ObtenConfiguracion(79));
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
  curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
  if(DEBUG == true) {
    curl_setopt($ch, CURLOPT_HEADER, 1);
    curl_setopt($ch, CURLINFO_HEADER_OUT, 1);
  }
  // CONFIG: Optional proxy configuration
  //curl_setopt($ch, CURLOPT_PROXY, $proxy);
  //curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, 1);
  // Set TCP timeout to 30 seconds
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
  curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: Close'));
  // CONFIG: Please download 'cacert.pem' from "http://curl.haxx.se/docs/caextract.html" and set the directory path
  // of the certificate as shown below. Ensure the file is readable by the webserver.
  // This is mandatory for some environments.
  //$cert = "cacert.pem";
  //curl_setopt($ch, CURLOPT_CAINFO, $cert);
  $res = curl_exec($ch);
  if (curl_errno($ch) != 0) // cURL error
    {
    if(DEBUG == true) {	
      error_log(date('[Y-m-d H:i e] '). "Can't connect to PayPal to validate IPN message: " . curl_error($ch) . PHP_EOL, 3, LOG_FILE);
    }
    curl_close($ch);
    exit;
  } else {
      // Log the entire HTTP response if debug is switched on.
      if(DEBUG == true) {
        error_log(date('[Y-m-d H:i e] '). "HTTP request of validation request:". curl_getinfo($ch, CURLINFO_HEADER_OUT) ." for IPN payload: $req" . PHP_EOL, 3, LOG_FILE);
        error_log(date('[Y-m-d H:i e] '). "HTTP response of validation request: $res" . PHP_EOL, 3, LOG_FILE);
      }
      curl_close($ch);
  }

// MDB
exit;

  
  // Inspect IPN validation result and act accordingly
  // Split response headers and payload, a better way for strcmp
  $tokens = explode("\r\n\r\n", trim($res));
  $res = trim(end($tokens));
  if(DEBUG_LOG==1)
      error_log(date('[Y-m-d H:i e] '). " TOKENS: $tokens  <<>> res: $res". PHP_EOL, 3, LOG_FILE);
  if (strcmp ($res, "VERIFIED") == 0) {
    
    if(DEBUG == true) {
      error_log(date('[Y-m-d H:i e] '). "Verified IPN: $req ". PHP_EOL, 3, LOG_FILE);
    }

    # Recibe parametros
    $mn_pagado = RecibeParametroHTML('mc_gross'); // cantidad que recibio paypal y los las regresa
    $custom = RecibeParametroHTML('custom'); // volvemos a recibir el alumno y el pago que realizo 
    # dividimos el alumno y el term 
    if(strpos($custom, "|T") !== False){
      $cm = explode("|T", $custom);
      $fl_alumno = $cm[0];
      $fl_term_pago = $cm[1];
    }
    $ds_transaccion = RecibeParametroHTML('txn_id'); // id de la trasaccion
    $payment_status = RecibeParametroHTML('payment_status'); // status del pago
    $receiver_email = RecibeParametroHTML('receiver_email'); // correo del vendedor
    $mn_tax_paypal = RecibeParametroHTML('tax');
    $payment_date = RecibeParametroHTML('payment_date');
    $payment_date = date("Y-m-d H:i:s", strtotime($payment_date));    
    // obtenermos los errores prueba gabriel si recibe los parametros
    if(DEBUG_LOG==1)
      error_log(date('[Y-m-d H:i e] '). " mn_pagado:$mn_pagado, custom:$custom, ds_transaccion:$ds_transaccion,payment_status:$payment_status, receiver_email:$receiver_email". PHP_EOL, 3, LOG_FILE);
    # Se obtiene la cantidad que debe pagar el student
    if(strpos($custom, "|T") !== False){
      # Recupera el programa y term que esta cursando el alumno
      $fl_programa = ObtenProgramaAlumno($fl_alumno);
      $fl_term = ObtenTermAlumno($fl_alumno);
      
      # Fecha actual
      $fe_actual = ObtenFechaActual();
      # Query para obtener el costo dependendo de la opcion de pago
      $Query1 .= "SELECT CASE b.fg_opcion_pago WHEN '1' THEN b.mn_a_due WHEN '2' THEN b.mn_b_due ";
      $Query1 .= "WHEN '3' THEN b.mn_c_due WHEN '4' THEN b.mn_d_due END mn_x_due, a.cl_sesion, b.fg_opcion_pago ";
      $Query1 .= "FROM c_usuario a, k_app_contrato b WHERE a.cl_sesion=b.cl_sesion AND  fl_usuario=$fl_alumno ";
      $row1 = RecuperaValor($Query1);
      $mn_x_due = $row1[0];
      
      # Obtenemos la diferencia de diaspara saber si pago a tiendo o no
      $row2 = RecuperaValor("SELECT DATEDIFF(DATE_FORMAT(fe_pago, '%Y-%m-%d'), '$fe_actual') no_dias, fe_pago FROM k_term_pago WHERE fl_term_pago=$fl_term_pago");
      if($row2[0] < 0)
        $mn_late_fee = ObtenConfiguracion(66);
      else
        $mn_late_fee = '0.00';
      # total del pago
      $mn_total = $mn_x_due + $mn_late_fee;
      
      # indicamos que es un pago de un student
      $type_payment = True;
    }
    else{
      
      # Verificamos si es un app o unpago completo app fee mas costo del programa
      if(strpos($custom, "PC|") !== False)
        $fl_alumno = substr($custom, 3);
      else
        $fl_alumno = $custom;
      
      $row3 = RecuperaValor("SELECT fl_programa FROM k_ses_app_frm_1 WHERE cl_sesion='".$fl_alumno."'");
      $Query2 = "SELECT a.mn_app_fee, a.mn_a_due, b.fg_total_programa, b.fg_tax_rate, b.fl_template ";
      $Query2 .= "FROM k_programa_costos a, c_programa b WHERE a.fl_programa=b.fl_programa AND  a.fl_programa=".$row3[0]." ";
      $row4  = RecuperaValor($Query2);
      $mn_app_fee = $row4[0];
      $mn_x_due = $row4[1];
      $fg_total_programa = $row4[2];
      $fg_tax_rate = $row4[3];
      $fl_template = $row4[4];
      
      # Validamos si paga appfee o pago completo
      if(!empty($fg_total_programa))
        $mn_total = $mn_app_fee + $mn_x_due;
      else
        $mn_total = $mn_app_fee;
      
      #Indicamos que es un pago de app fee o un pago completo
      $type_payment = False;
    }
    // check whether the payment_status is Completed
    // check that txn_id has not been previously processed
    // check that receiver_email is your PayPal email
    // check that payment_amount/payment_currency are correct
    // process payment and mark item as paid.
    // assign posted variables to local variables
    
    # Vericamos que no exita la transaccion en las tablas c_sesion k_ses_pago k_alumno_pago 
    $sesion_tr = ExisteEnTabla('c_sesion','ds_transaccion',$ds_transaccion);
    $ses_tr = ExisteEnTabla('k_ses_pago','ds_transaccion',$ds_transaccion);
    $alumno_tr = ExisteEnTabla('k_alumno_pago','ds_transaccion',$ds_transaccion);
    if(!$sesion_tr AND !$ses_tr AND !$alumno_tr)
      $existe_transaccion = False;
    else
      $existe_transaccion = True;    
    $valida_email = ($receiver_email == ObtenConfiguracion(62)) ? True : False;
    $valida_monto = ($mn_pagado == $mn_total) ? True : False;
    $existe_term_pago = ExisteEnTabla('k_alumno_pago','fl_alumno',$fl_alumno,'fl_term_pago',$fl_term_pago,True);
    if(DEBUG_LOG==1)
      error_log(date('[Y-m-d H:i e] '). "Payment_status:$payment_status Existe_transaccion:$existe_transaccion Valida_email:$Valida_email Valida_monto:$Valida_monto Existe_Term:$existe_term_pago". PHP_EOL, 3, LOG_FILE);
    # las validaciones estan correctas
    # 22 enero 2016 quitamos validacion de monto
    if($payment_status == "Completed" AND !$existe_transaccion AND $valida_email AND !$existe_term_pago){
      # Verifica que es un pago de app fee mas el costo del programa
      if(strpos($custom, "PC|") !== False OR !$type_payment)
        AppPro($fl_alumno,$payment_status,$mn_app_fee,$mn_x_due,$ds_transaccion, $fg_total_programa, $fg_tax_rate, $fl_template, $mn_tax_paypal, $payment_date);
      else // Inserta el pago del student
        PagoStudent($fl_alumno,$fl_term_pago,$mn_pagado,$mn_late_fee, $ds_transaccion, $payment_date);
    }    
  }else{
    if (strcmp ($res, "INVALID") == 0) {
      // log for manual investigation
      // Add business logic here which deals with invalid IPN messages
      if(DEBUG == true) {
        error_log(date('[Y-m-d H:i e] '). "Invalid IPN: $req" . PHP_EOL, 3, LOG_FILE);
      }
    }
  }
  
  # PC| Indetificacion para Pago Completo
  function AppPro($fl_alumno,$payment_status,$mn_app_fee,$mn_x_due,$ds_transaccion, $fg_total_programa, $fg_tax_rate=0, $fl_template=0, $mn_tax_paypal=0, $payment_date){
    # Obtenemos informacion del usuario para obtener el term a pagar y pais y app fee pagado total tuition pagado
    $QueryT = "SELECT CASE b.fl_term_ini WHEN 0 THEN (SELECT fl_term_pago FROM k_term_pago c WHERE b.fl_term=c.fl_term) ";
    $QueryT .= "WHEN b.fl_term_ini>0 THEN (SELECT fl_term_pago FROM k_term_pago d WHERE d.fl_term=b.fl_term_ini) END fl_term_pago, ";
    $QueryT .= "a.ds_add_country,a.ds_add_state, c.mn_app_fee, c.mn_tot_tuition ";
    $QueryT .= "FROM (k_ses_app_frm_1 a, k_term b) LEFT JOIN k_app_contrato  c ON(c.cl_sesion=a.cl_sesion AND c.no_contrato='1') ";
    $QueryT .= "WHERE a.fl_programa=b.fl_programa AND a.fl_periodo=b.fl_periodo AND a.cl_sesion='".$fl_alumno."' ";
    $row = RecuperaValor($QueryT);
    $fl_term_pago = $row[0];
    $ds_add_country = $row[1];
    $ds_add_state = $row[2];
    if(empty($fg_total_programa))
      $mn_app_fee = $mn_app_fee;
    else
      $mn_app_fee = $row[3];    
    $mn_tot_tuition = $row[4];
    $tax_appfee = 0;
    $tax_tuition = 0;
    
    # Para obtener el tax del app fee y tuition debe cumplir configuration
    # Obtengamos un tax de paypal y debe ser mayor a cero
    # El programa sea corto de pago completo y ademas que requiera pago de tax
    # El usuario debe ser de canada
    if($mn_tax_paypal>0 AND !empty($fg_tax_rate) AND $ds_add_country==38){
      # Obtenemos el tax de la provincia
      $rowp = RecuperaValor("SELECT mn_PST, mn_GST, mn_HST, mn_tax tax_provincia FROM k_provincias WHERE fl_provincia=$ds_add_state");
      $mn_PST = $rowp[0];
      $mn_GST = $rowp[1];
      $mn_HST = $rowp[2];
      $ds_tax_provincia = "";
      if($mn_PST>0)
        $ds_tax_provincia .= ObtenEtiqueta(822);
      if($mn_GST>0)
        $ds_tax_provincia .= ObtenEtiqueta(823);
      if($mn_HST>0)
        $ds_tax_provincia .= ObtenEtiqueta(824);
      $tax_provincia = $rowp[3];
      # Realiamps calculos para conocer el tax del app fee y tax de tuition por separado
      $tax_appfee = $mn_app_fee*($tax_provincia/100);
      $tax_tuition = $mn_tot_tuition*($tax_provincia/100);
    }
    else{
      $ds_tax_provincia = "Tax";
    }
    # Actualizamos el pago del app
    $fg_paypal = 1;
    $fg_pago = 1;
    $cl_metodo_pago = 1;
    $fe_pago=$payment_date;
    $QueryAPP  = "UPDATE c_sesion SET fg_paypal='$fg_paypal', fg_confirmado='1', fg_pago='".$fg_pago."',  ";
    $QueryAPP .= "fe_ultmod=CURRENT_TIMESTAMP, cl_metodo_pago=$cl_metodo_pago, fe_pago='$fe_pago', mn_pagado='$mn_app_fee', ds_transaccion='$ds_transaccion', ";
    $QueryAPP .= "mn_tax_paypal='$tax_appfee', ds_tax_provincia='$ds_tax_provincia' WHERE cl_sesion='$fl_alumno' ";
    EjecutaQuery($QueryAPP);    
    # Si el pago de completo insertamos un pago en k_ses_pago
    if(!empty($fg_total_programa)){
      if(empty($fl_term_pago))
        $fl_term_pago=0;
      $QueryPAGO  = "INSERT INTO k_ses_pago(cl_sesion, fl_term_pago, cl_metodo_pago, fe_pago, mn_pagado, ds_transaccion, mn_tax_paypal, ds_tax_provincia) ";
      $QueryPAGO .= "VALUES('".$fl_alumno."', $fl_term_pago, $cl_metodo_pago, '$fe_pago', '$mn_x_due', '$ds_transaccion', '$tax_tuition', '$ds_tax_provincia')";
      EjecutaQuery($QueryPAGO);
      if(DEBUG_LOG==1)
        error_log(date('[Y-m-d H:i e] '). "Inserta Primer pago cuando es un pago completo".$QueryPAGO. PHP_EOL, 3, LOG_FILE);
    }
    if(DEBUG_LOG==1)
      error_log(date('[Y-m-d H:i e] '). "APPFEE Actualiza el app fee".$QueryAPP. PHP_EOL, 3, LOG_FILE);
  }
  
  function PagoStudent($fl_alumno,$fl_term_pago,$mn_pagado,$mn_late_fee, $ds_transaccion, $payment_date){
    
    # Insertamos el pago del student
    $Query  = "INSERT INTO k_alumno_pago (fl_alumno, fl_term_pago, cl_metodo_pago, fe_pago, mn_pagado, mn_late_fee, ds_transaccion)  ";
    $Query .= "VALUES ($fl_alumno,$fl_term_pago, '1', '$payment_date', '$mn_pagado', $mn_late_fee, '$ds_transaccion')";
    EjecutaQuery($Query);
    if(DEBUG_LOG==1)
        error_log(date('[Y-m-d H:i e] '). "Pago normal de estudiante".$Query. PHP_EOL, 3, LOG_FILE);
  }
?>
