<?php

include('../../../lib/adodb/adodb.inc.php');

  // TODO Usar las constantes de conexion a BD que ya existen
  $user = "vanas_usvanas";
  $pwd  = "pwdvanas100373k";
  $server = "localhost";
  $db     = "vanas_vanas";
   
  $fl_entrega_semanal = $_GET["fl_entrega_semanal"];
  $finishDateTime = $_GET["finishDateTime"];
  
  
  $DB = NewADOConnection("mysql://$user:$pwd@$server/$db?persist");

  $ok = $DB->Execute("update k_record_critique_session set cl_estatus = 3, fe_fin = '" . $finishDateTime . "' where fl_entrega_semanal = " . $fl_entrega_semanal );
?>