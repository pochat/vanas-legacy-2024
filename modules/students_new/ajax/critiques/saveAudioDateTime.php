<?php

include('../../../lib/adodb/adodb.inc.php');

  // TODO Usar las constantes de conexion a BD que ya existen
  $user = "vanas_usvanas";
  $pwd  = "pwdvanas100373k";
  $server = "localhost";
  $db     = "vanas_vanas";

  $fl_entrega_semanal = $_GET["fl_entrega_semanal"];
  $startDateTime = $_GET["startDateTime"];
  $finishDateTime = $_GET["finishDateTime"];
  $dateStartPlay = $_GET["dateStartPlay"];
  $dateStopPlay = $_GET["dateStopPlay"];
  $nombreArchivo = $_GET["nombreArchivo"];

  $esInsert = $_GET["esInsert"];
  $folio = $_GET["folio"];

  $lastID = "";


  $DB = NewADOConnection("mysql://$user:$pwd@$server/$db?persist");


  $rows = $DB->Execute("select 1 from k_record_critique_session where fe_fin is null and fl_entrega_semanal = " . $fl_entrega_semanal );

  $query = "";
/*
  if ($rows) {
    if($rows->fields[0]) {
        if ($esInsert == 1) {
          $query = "insert into k_record_critique_audio values(null," . $fl_entrega_semanal . ",'" .  $startDateTime . "',null,'" . $dateStartPlay . "',null,'" . $nombreArchivo . "')";
          $ok = $DB->Execute($query);
          $lastID = mysql_insert_id();
        }
        else {
          $query = "update k_record_critique_audio set no_stop_play = '" . $finishDateTime . "', fe_fin = '" . $dateStopPlay . "' where fl_rc_audio_session = " . $folio;
          $ok = $DB->Execute($query);
        }
    }
  }*/



  //  Pruebas sin tener sesión activa
  if ($esInsert == 1) {
    $ok = $DB->Execute("insert into k_record_critique_audio values(null," . $fl_entrega_semanal . ",'" .  $startDateTime . "',null,'" . $dateStartPlay . "',null,'" . $nombreArchivo . "')"  );
    $lastID = mysql_insert_id();
  }
  else {
    $ok = $DB->Execute("update k_record_critique_audio set no_stop_play = '" . $finishDateTime . "', fe_fin = '" . $dateStopPlay . "' where fl_rc_audio_session = " . $folio  );
  }



  echo $lastID;
?>