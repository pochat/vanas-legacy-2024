<?php

	# Include campus libraries 
	require '/var/www/html/vanas/lib/com_func.inc.php';
	require '/var/www/html/vanas/lib/sp_config.inc.php';
	// require '../lib/com_func.inc.php';
	// require '../lib/sp_config.inc.php';
  
  # Cron que se ejecutara una vez por semana por cuestiones de cambio de los alumos
  
  
  # Buscamos todos los grupos que tengan  clases en el mes actual
  $mes_anio_act = date("Y-m");
  $Query = "SELECT gr.fl_grupo FROM c_grupo gr, k_clase kc WHERE gr.fl_grupo = kc.fl_grupo AND DATE_FORMAT(fe_clase, '%Y-%m')='$mes_anio_act' GROUP BY fl_grupo";
  $rs = EjecutaQuery($Query);
  for($i=0;$row=RecuperaRegistro($rs);$i++){
    $fl_grupo = $row[0];
    
    # Buscamos todos los alumos actuales que estan en el grupos
    $Query1 = "SELECT COUNT(*) FROM k_alumno_grupo WHERE fl_grupo=$fl_grupo";
    $row = RecuperaValor($Query1);
    $no_alumnos = $row[0];
    
    
    # Actulizamos el numero de alumnos en c_grupo
    $Query2  = "UPDATE c_grupo SET no_alumnos=$no_alumnos WHERE fl_grupo=$fl_grupo";
    EjecutaQuery($Query2);
    
    # En caso de que no existan alumnos verificara en el historial del alumno   
    $row_1 = RecuperaValor("SELECT no_alumnos FROM c_grupo WHERE fl_grupo=$fl_grupo");
    if(empty($row_1[0])){
      $row_2 = RecuperaValor("SELECT COUNT(*) FROM k_alumno_historia where fl_grupo=$fl_grupo");
      $Query_2  = "UPDATE c_grupo SET no_alumnos=$row_2[0] WHERE fl_grupo=$fl_grupo ";
      EjecutaQuery($Query_2);
    }   
  }
  
?>
