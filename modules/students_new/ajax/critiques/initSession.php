<?php

include('../../../lib/adodb/adodb.inc.php');

  // TODO Usar las constantes de conexion a BD que ya existen
  $user = "vanas_usvanas";
  $pwd  = "pwdvanas100373k";
  $server = "localhost";
  $db     = "vanas_vanas";
   
  $fl_entrega_semanal = $_GET["fl_entrega_semanal"];
  $startDateTime = $_GET["startDateTime"];
  
  
  $DB = NewADOConnection("mysql://$user:$pwd@$server/$db?persist");

  $ok = $DB->Execute("delete from k_record_critique_audio where fl_entrega_semanal = " . $fl_entrega_semanal );
  $ok = $DB->Execute("delete from k_record_critique_session where fl_entrega_semanal = " . $fl_entrega_semanal );
  $ok = $DB->Execute("insert into k_record_critique_session values(null, " . $fl_entrega_semanal . ",1,'" . $startDateTime . "', null)"  ); 
  
?>