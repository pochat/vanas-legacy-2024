<?php

  # Libreria de funciones
  require '../../lib/general.inc.php';

  # Verifica que exista una sesion valida en el cookie y la resetea
  ValidaSesion( );

  # Recibe la clave
  $clave = RecibeParametroNumerico('clave');
  $cl_dia=RecibeParametroNumerico('cl_dia_combined');
  $timepicker_combined1=RecibeParametroHTML('timepicker_combined1');
  $timepicker_combined2=RecibeParametroHTML('timepicker_combined2');

  $time=explode(" ",$timepicker_combined1);
  $no_hora1=$time[0];
  $tiempo1=$time[1];

  $time2=explode(" ",$timepicker_combined2);
  $no_hora2=$time2[0];
  $tiempo2=$time2[1];

  $Query="UPDATE c_periodo SET cl_dia=$cl_dia, no_hora1='$no_hora1',no_tiempo1='$tiempo1',no_hora2='$no_hora2',no_tiempo2='$tiempo2'  where fl_periodo=$clave  ";
  EjecutaQuery($Query);

  # Redirige al listado
  header("Location: ".ObtenProgramaBase( ));

?>