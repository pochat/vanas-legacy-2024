<?php
	# Libreria de funciones
	require("../../common/lib/cam_general.inc.php");

	$Query  = "SELECT CURRENT_TIMESTAMP, UNIX_TIMESTAMP() ";
  $row = RecuperaValor($Query);
  $fe_actual = $row[0];
  $fe_timestamp = $row[1];

  echo json_encode((Object)array(
  	"time" => $fe_actual,
  	"timestamp" => array(
  		"seconds" => (int)$fe_timestamp,
  		"milliseconds" => (int)$fe_timestamp * 1000
  	)
  ));
?>