<?php
	
	# Initialize variables
  $fl_usuario = ObtenUsuario(False);
  $diferencia = RecuperaDiferenciaGMT( );
	$fl_grupos = array();
	$fl_terms = array();
	$result["event"] = array();

  # Find all groups that are active
  $Query = "SELECT fl_grupo, fl_term, nb_grupo FROM c_grupo WHERE fl_maestro=$fl_usuario";
	$rs = EjecutaQuery($Query);
	for($i=0; $row=RecuperaRegistro($rs); $i++){
		$fl_grupo = $row[0];
		$fl_term = $row[1];
		$nb_grupo = $row[2];

		# Check if group is active
		$Query  = "SELECT COUNT(1) ";
		$Query .= "FROM k_alumno_grupo a ";
		$Query .= "LEFT JOIN c_usuario b ON b.fl_usuario=a.fl_alumno ";
		$Query .= "WHERE a.fl_grupo=$fl_grupo AND b.fg_activo='1' ";
		$active = RecuperaValor($Query);
		$fg_active = $active[0];

		if(!empty($fg_active)){
			array_push($fl_grupos, $fl_grupo);
			array_push($fl_terms, $fl_term);
		}
	}
	
	# Lesson start date, submit deadline, grading deadline events for each group
	for($i=0; $i<count($fl_terms); $i++){

		$Query  = "SELECT b.ds_titulo, a.fe_publicacion, a.fe_entrega, a.fe_calificacion ";
		$Query .= "FROM k_semana a ";
		$Query .= "LEFT JOIN c_leccion b ON b.fl_leccion=a.fl_leccion ";
		$Query .= "WHERE a.fl_term=$fl_terms[$i] ";
		$rs = EjecutaQuery($Query);
    while($row=RecuperaRegistro($rs)){
			$ds_titulo = str_uso_normal($row[0]);
			$fe_publicacion = $row[1];
			$fe_entrega = $row[2];
			$fe_calificacion = $row[3];

			$event1 = array(
	    	"title" => $ds_titulo,
	  		"start" => $fe_publicacion,
	  		"description" => "Start Date",
	  		"backgroundColor" => "#66CCCC"
	  		//"className" => array("event", "#66CCCC")
	    );
	    $event2 = array(
	    	"title" => $ds_titulo,
	  		"start" => $fe_entrega,
	  		"description" => "Submission Deadline",
	  		"backgroundColor" => "#50A6C2"
	  		//"className" => array("event", "#50A6C2")
	    );
	    $event3 = array(
	    	"title" => $ds_titulo,
	  		"start" => $fe_calificacion,
	  		"description" => "Grading Deadline",
	  		"backgroundColor" => "#33A1C9"
	  		//"className" => array("event", "#33A1C9")
	    );
	    array_push($result["event"], $event1, $event2, $event3);
		}
	}

  # End Lesson Events

  # Live Session Events

  for($i=0; $i<count($fl_grupos); $i++){
    // Mandatory Sessions
  	$Query  = "SELECT c.ds_titulo, DATE_FORMAT(a.fe_clase, '%Y-%m-%d'),  DATE_FORMAT(DATE_ADD(a.fe_clase, INTERVAL $diferencia HOUR), '%l:%i %p') ";
    $Query .= ", a.fg_obligatorio, a.fg_adicional ";
  	$Query .= "FROM k_clase a ";
  	$Query .= "LEFT JOIN k_semana b ON b.fl_semana=a.fl_semana ";
  	$Query .= "LEFT JOIN c_leccion c ON c.fl_leccion=b.fl_leccion ";
  	$Query .= "WHERE a.fl_grupo=$fl_grupos[$i] ";
  	// $Query .= "AND (a.fg_obligatorio='1' AND a.fg_adicional='0') ";
    // $Query .= "OR ((a.fg_adicional='1' OR a.fg_adicional='0') AND fg_obligatorio='1')";
  	$rs = EjecutaQuery($Query);
  	for($j=0; $row=RecuperaRegistro($rs); $j++){
  		$ds_titulo = str_uso_normal($row[0]);
  		$fe_clase = $row[1];
  		$hr_clase = $row[2];
      $fg_obligatorio = $row[3];
      $fg_adicional = $row[4];
      if($fg_obligatorio=='1' ){
        if($fg_adicional=='0')
          $text = "Live session at ";
        if($fg_adicional=='1')
          $text = "Live session additional at ";
      }

      $event = array(
          "title" => $ds_titulo,
          "start" => $fe_clase,
          "description" => $text.$hr_clase,
          "backgroundColor" => "#708090"
        );
	  	array_push($result["event"], $event);  		
  	}

    // Additional Sessions
    /*$Query  = "SELECT c.ds_titulo, DATE_FORMAT(a.fe_clase, '%Y-%m-%d'),  DATE_FORMAT(DATE_ADD(a.fe_clase, INTERVAL $diferencia HOUR), '%l:%i %p') ";
  	$Query .= "FROM k_clase a ";
  	$Query .= "LEFT JOIN k_semana b ON b.fl_semana=a.fl_semana ";
  	$Query .= "LEFT JOIN c_leccion c ON c.fl_leccion=b.fl_leccion ";
  	$Query .= "WHERE a.fl_grupo=$fl_grupos[$i] ";
  	$Query .= "AND (a.fg_adicional='1' OR a.fg_adicional='0') AND a.fg_obligatorio='1' ";
  	$rs = EjecutaQuery($Query);
  	for($j=0; $row=RecuperaRegistro($rs); $j++){
  		$ds_titulo = $row[0];
  		$fe_clase = $row[1];
  		$hr_clase = $row[2];

			$event = array(
	    	"title" => $ds_titulo,
	  		"start" => $fe_clase,
	  		"description" => "Live Session at ".$hr_clase,
	  		"backgroundColor" => "#708090"
	  	);
	  	array_push($result["event"], $event);  		
  	}*/
  }
  
  # Group Sessions
    $Query = "SELECT ds_titulo, fe_clase, DATE_FORMAT(DATE_ADD(fe_clase, INTERVAL $diferencia HOUR), '%l:%i %p') FROM groups_schedules WHERE fl_maestro=$fl_usuario ";
    $rs = EjecutaQuery($Query);
    for($j=0; $row=RecuperaRegistro($rs); $j++){
      $ds_titulo = str_uso_normal($row[0]);
      $fe_clase = $row[1];
      $hr_clase = $row[2];

      $event = array(
        "title" => $ds_titulo,
        "start" => $fe_clase,
        "description" => "Group Session at ".$hr_clase,
        "backgroundColor" => "#708090"
      );
      array_push($result["event"], $event);     
    }
  
  # End Live Session Events

  # School year breaks
  $Query  = "SELECT ds_break, fe_ini, fe_fin, no_days ";
  $Query .= "FROM c_break ";
  $rs = EjecutaQuery($Query);

  while($row = RecuperaRegistro($rs)){
  	$ds_break = $row[0];
  	$fe_ini = $row[1];
  	$fe_fin = $row[2];
  	$no_days = $row[3];

  	$event = array(
  		"title" => $ds_break,
  		"start" => $fe_ini,
  		"end" => $fe_fin,
  		"backgroundColor" => "#004F9F"
  	);
  	array_push($result["event"], $event);
  }

  # End School year breeaks
  
  # Global Class  
  $Query  = "SELECT ds_titulo, DATE_FORMAT(a.fe_clase, '%Y-%m-%d'),  DATE_FORMAT(DATE_ADD(a.fe_clase, INTERVAL $diferencia HOUR), '%l:%i %p'), cg.ds_clase ";
  $Query .= "FROM k_clase_cg a, c_clase_global cg ";
  $Query .= "WHERE  a.fl_clase_global = cg.fl_clase_global AND a.fl_maestro=$fl_usuario ";
  $rs = EjecutaQuery($Query);
  for($j=0; $row=RecuperaRegistro($rs); $j++){
    $ds_titulo = str_uso_normal($row[0]);
    $fe_clase = $row[1];
    $hr_clase = $row[2];
    $ds_clase_global = $row[3];
    $event = array(
      "title" => $ds_clase_global,
      "start" => $fe_clase,
      "description" => $ds_titulo." ".$hr_clase,
      "backgroundColor" => "#71843f"
    );
    array_push($result["event"], $event);  		
  }
  # End Global Class
  #Colocamos las clases grupales.
  $Queryg="SELECT DISTINCT c.fl_maestro, a.nb_grupo,c.nb_clase,DATE_FORMAT(c.fe_clase, '%Y-%m-%d'),    DATE_FORMAT(DATE_ADD(c.fe_clase, INTERVAL $diferencia HOUR), '%l:%i %p')
		  ,DATE_FORMAT(c.fe_clase, '%H:%i'),time_format(ADDTIME  (c.fe_clase ,'01:00:00'), '%H:%i') AS hr_final
        ,c.fl_clase_grupo 
        FROM c_grupo a
        JOIN k_clase_grupo c ON c.fl_grupo=a.fl_grupo 
        JOIN k_alumno_grupo g ON g.fl_grupo=a.fl_grupo WHERE c.fl_maestro=$fl_usuario ";
  $rsg = EjecutaQuery($Queryg);
  for($jg=0; $rowg=RecuperaRegistro($rsg); $jg++){
      $ds_titulo= str_uso_normal($rowg[1]);
      $ds_clase_global=$rowg[2];
      $fe_clase=$rowg[3];
      $hr_clase=$rowg[4];
      
      $event = array(
      "title" => $ds_clase_global,
      "start" => $fe_clase,
      "description" => $ds_titulo." ".$hr_clase,
      "backgroundColor" => "#71843f"
    );

     array_push($result["event"], $event);  
  }




  echo json_encode((Object) $result);
?>
