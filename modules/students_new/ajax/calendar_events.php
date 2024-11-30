<?php

	# Initial variables
	$fl_usuario = ObtenUsuario(False);
  $fe_actual = "STR_TO_DATE('".ObtenFechaActual( )."', '%Y-%m-%d %H:%i:%s')";
	$result["event"] = array();

	# Reminder Events

	# Tabla de Reminders
  $opt = 0;
  $no_reg = 0;
  $reminder = array( );
  $diferencia = RecuperaDiferenciaGMT( );
/*
	// Reminders para alumnos
	#Query para traer fechas de Q&A Live Sessions
	$res = RecuperaValor("SELECT fl_grupo FROM k_alumno_grupo WHERE fl_alumno=$fl_usuario");
	$fl_grupo = $res[0];
	$Query1  = "SELECT (DATE_ADD(fe_clase, INTERVAL $diferencia HOUR)), 'Q&A Live Session ' ";
	$Query1 .= "FROM k_clase ";
	$Query1 .= "WHERE fe_clase >= $fe_actual ";
	$Query1 .= "AND fl_grupo = $fl_grupo ";
	$Query1 .= "ORDER BY fe_clase ";
	$rs1 = EjecutaQuery($Query1);

	#Arma arreglo con datos de query
	$no_reg = $no_reg + CuentaRegistros($rs1);
	while($row1 = RecuperaRegistro($rs1))
	{
	  $reminder[$opt][0] = $row1[0];
	  $reminder[$opt][1] = $row1[1];
	  $reminder[$opt][2] = '';
	  $opt++;
	}
	*/
	#Query para traer fechas lImite de pago de colegiatura para alumnos
	$Query3  = "SELECT (DATE_ADD(fe_pago, INTERVAL $diferencia HOUR)), 'Payment 1 due date ' ";
	$Query3 .= "FROM k_alumno_term a, k_term b, c_periodo c ";
	$Query3 .= "WHERE a.fl_term=b.fl_term ";
	$Query3 .= "AND b.fl_periodo=c.fl_periodo ";
	$Query3 .= "AND a.fl_alumno=$fl_usuario ";
	$Query3 .= "AND fe_pago >= $fe_actual ";
	$rs3 = EjecutaQuery($Query3);

	#Arma arreglo con datos de query
	$no_reg = $no_reg + CuentaRegistros($rs3);
	while($row1 = RecuperaRegistro($rs3))
	{
	  $reminder[$opt][0] = $row1[0];
	  $reminder[$opt][1] = $row1[1];
	  $reminder[$opt][2] = '';
	  $opt++;
	}

	#Query para traer fechas lImite de pago de colegiatura para alumnos
	$Query3  = "SELECT (DATE_ADD(fe_pago2, INTERVAL $diferencia HOUR)), 'Payment 2 due date ' ";
	$Query3 .= "FROM k_alumno_term a, k_term b, c_periodo c ";
	$Query3 .= "WHERE a.fl_term=b.fl_term ";
	$Query3 .= "AND b.fl_periodo=c.fl_periodo ";
	$Query3 .= "AND a.fl_alumno=$fl_usuario ";
	$Query3 .= "AND fe_pago2 >= $fe_actual ";
	$rs3 = EjecutaQuery($Query3);

	#Arma arreglo con datos de query
	$no_reg = $no_reg + CuentaRegistros($rs3);
	while($row1 = RecuperaRegistro($rs3))
	{
	  $reminder[$opt][0] = $row1[0];
	  $reminder[$opt][1] = $row1[1];
	  $reminder[$opt][2] = '';
	  $opt++;
	}

	#Query para traer fechas lImite de pago de colegiatura para alumnos
	$Query3  = "SELECT (DATE_ADD(fe_pago3, INTERVAL $diferencia HOUR)), 'Payment 3 due date ' ";
	$Query3 .= "FROM k_alumno_term a, k_term b, c_periodo c ";
	$Query3 .= "WHERE a.fl_term=b.fl_term ";
	$Query3 .= "AND b.fl_periodo=c.fl_periodo ";
	$Query3 .= "AND a.fl_alumno=$fl_usuario ";
	$Query3 .= "AND fe_pago3 >= $fe_actual ";
	$rs3 = EjecutaQuery($Query3);

	#Arma arreglo con datos de query
	$no_reg = $no_reg + CuentaRegistros($rs3);
	while($row1 = RecuperaRegistro($rs3))
	{
	  $reminder[$opt][0] = $row1[0];
	  $reminder[$opt][1] = $row1[1];
	  $reminder[$opt][2] = '';
	  $opt++;
	}

	#Query para traer fechas lImite de entrega de trabajos para alumnos
	$res = RecuperaValor("SELECT  MAX(fl_term) FROM k_alumno_term WHERE fl_alumno = $fl_usuario");
	$Query4  = "SELECT (DATE_ADD(fe_entrega, INTERVAL $diferencia HOUR)), 'Submission due date ' ";
	$Query4 .= "FROM k_semana ";
	$Query4 .= "WHERE fe_entrega >= $fe_actual ";
	$Query4 .= "AND fl_term = $res[0] ";
	$rs4 = EjecutaQuery($Query4);

	#Arma arreglo con datos de query
	$no_reg = $no_reg + CuentaRegistros($rs4);
/*	while($row1 = RecuperaRegistro($rs4))
	{
	  $reminder[$opt][0] = $row1[0];
	  $reminder[$opt][1] = $row1[1];
	  $reminder[$opt][2] = '';
	  $opt++;
	}
	*/
	#Query para traer fechas de cumpleanios de classmates
	$Query5  = "SELECT MAKEDATE(CASE WHEN dayofyear(fe_nacimiento) < dayofyear($fe_actual) THEN year($fe_actual)+1 ELSE year($fe_actual) END, ";
	$Query5 .= "        CASE WHEN (dayofyear(fe_nacimiento)>59 ";
	$Query5 .= "              AND ((year(fe_nacimiento)%4=0 AND year(fe_nacimiento)%100>0) OR year(fe_nacimiento)%400=0) ";
	$Query5 .= "                AND ((year($fe_actual)%4>0 OR year($fe_actual)%100=0) ";
	$Query5 .= "                  AND year($fe_actual)%400>0)) ";
	$Query5 .= "            THEN dayofyear(fe_nacimiento)-1 ";
	$Query5 .= "            WHEN (dayofyear(fe_nacimiento)>59 ";
	$Query5 .= "              AND ((year(fe_nacimiento)%4>0 OR year(fe_nacimiento)%100=0) AND year(fe_nacimiento)%400>0) ";
	$Query5 .= "                AND ((year($fe_actual)%4=0 AND year($fe_actual)%100>0) ";
	$Query5 .= "                  OR year($fe_actual)%400=0)) ";
	$Query5 .= "            THEN dayofyear(fe_nacimiento)+1 ";
	$Query5 .= "            ELSE dayofyear(fe_nacimiento) ";
	$Query5 .= "            END) fe_cumple, ";
	$Query5 .= "a.ds_nombres, a.ds_apaterno, ' birthday! ' ";
	$Query5 .= "FROM c_usuario a, k_alumno_grupo b ";
	$Query5 .= "WHERE a.fl_usuario = b.fl_alumno ";
	$Query5 .= "AND MAKEDATE(CASE WHEN dayofyear(fe_nacimiento) < dayofyear($fe_actual) THEN year($fe_actual)+1 ELSE year($fe_actual) END, ";
	$Query5 .= "        CASE WHEN (dayofyear(fe_nacimiento)>59 ";
	$Query5 .= "              AND ((year(fe_nacimiento)%4=0 AND year(fe_nacimiento)%100>0) OR year(fe_nacimiento)%400=0) ";
	$Query5 .= "                AND ((year($fe_actual)%4>0 OR year($fe_actual)%100=0) ";
	$Query5 .= "                  AND year($fe_actual)%400>0)) ";
	$Query5 .= "            THEN dayofyear(fe_nacimiento)-1 ";
	$Query5 .= "            WHEN (dayofyear(fe_nacimiento)>59 ";
	$Query5 .= "              AND ((year(fe_nacimiento)%4>0 OR year(fe_nacimiento)%100=0) AND year(fe_nacimiento)%400>0) ";
	$Query5 .= "                AND ((year($fe_actual)%4=0 AND year($fe_actual)%100>0) ";
	$Query5 .= "                  OR year($fe_actual)%400=0)) ";
	$Query5 .= "            THEN dayofyear(fe_nacimiento)+1 ";
	$Query5 .= "            ELSE dayofyear(fe_nacimiento) ";
	$Query5 .= "            END) >= $fe_actual ";
	$Query5 .= "AND b.fl_grupo = $fl_grupo";

	$rs5 = EjecutaQuery($Query5);

	#Arma arreglo con datos de query
	$no_reg = $no_reg + CuentaRegistros($rs5);
	while($row1 = RecuperaRegistro($rs5))
	{
	  $reminder[$opt][0] = $row1[0];
	  $reminder[$opt][1] = $row1[1].' '.$row1[2].' '.$row1[3];
	  $reminder[$opt][2] = '';
	  $opt++;
	}

	# Presenta reminders
  if($no_reg < 5)
    $n = $no_reg;
  else
    $n = 5;
  if($n > 0) {
    $rem = sort($reminder);
    for($i=0; $i<$n; $i++) {
      $var = $reminder[$i][0];
      $mes = substr($var, 5, 2);
      $dia = substr($var, 8, 2);
      $anio = substr($var, 0, 4);
      $hora = substr($var, 11, 5);
      $fecha = ObtenNombreMes($mes)." ".$dia.", ".$anio." ".$hora;

      $event = array(
      	"title" => $reminder[$i][1].$reminder[$i][2],
    		"start" => $fecha,
    		"className" => array("event", "bg-color-red")
    	);

      array_push($result["event"], $event);
    }
  }
  # End Reminder Events

  # Initialize variables
  $fl_term = ObtenTermAlumno($fl_alumno);
  $fl_grupo = ObtenGrupoAlumno($fl_alumno);

  # Lesson start date, submit deadline, grading deadline events
	$Query  = "SELECT b.ds_titulo, a.fe_publicacion, a.fe_entrega, a.fe_calificacion ";
	$Query .= "FROM k_semana a ";
	$Query .= "LEFT JOIN c_leccion b ON b.fl_leccion=a.fl_leccion ";
	$Query .= "WHERE a.fl_term=$fl_term ";
	$rs = EjecutaQuery($Query);
	while($row=RecuperaRegistro($rs)){
		$ds_titulo = $row[0];
		$fe_publicacion = $row[1];
		$fe_entrega = $row[2];
		$fe_calificacion = $row[3];

    $event1 = array();
		/*$event1 = array(
    	"title" => "Start Date Week",
  		"start" => $fe_publicacion,
  		"description" => $ds_titulo,
  		"backgroundColor" => "#66CCCC"
  		//"className" => array("event", "#66CCCC")
    );*/
    $event2 = array(
    	"title" => "Deadline Submission Assignment",
  		"start" => $fe_entrega,
  		"description" => "",
  		"backgroundColor" => "#a30227e0"
  		//"className" => array("event", "#50A6C2")
    );
    $event3 = array(
    	"title" => "Deadline Grading",
  		"start" => $fe_calificacion,
  		"description" => "",
  		"backgroundColor" => "#e58c08"
  		//"className" => array("event", "#33A1C9")
    );
    array_push($result["event"], $event1, $event2, $event3);
	}

  # End Lesson Events

  # Live Session Events

	// Mandatory Sessions
	$Query  = "SELECT c.ds_titulo, DATE_FORMAT(DATE_ADD(a.fe_clase, INTERVAL $diferencia HOUR), '%Y-%m-%d'),  DATE_FORMAT(DATE_ADD(a.fe_clase, INTERVAL $diferencia HOUR), '%l:%i %p') ";
  $Query .= ", a.fg_obligatorio, a.fg_adicional ";
	$Query .= "FROM k_clase a ";
	$Query .= "LEFT JOIN k_semana b ON b.fl_semana=a.fl_semana ";
	$Query .= "LEFT JOIN c_leccion c ON c.fl_leccion=b.fl_leccion ";
	$Query .= "WHERE a.fl_grupo=$fl_grupo ";
	// $Query .= "AND fg_obligatorio='1' ";
	$rs = EjecutaQuery($Query);
	while($row=RecuperaRegistro($rs)){
		$ds_titulo = $row[0];
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
    	"title" => "Lecture -". $ds_titulo,
  		"start" => $fe_clase,
  		"description" => $text.$hr_clase,
  		"backgroundColor" => "#007bff"
  	);
    array_push($result["event"], $event);
	}

  /*// Additional Sessions
  $Query  = "SELECT c.ds_titulo, DATE_FORMAT(DATE_ADD(a.fe_clase, INTERVAL $diferencia HOUR), '%Y-%m-%d'),  DATE_FORMAT(DATE_ADD(a.fe_clase, INTERVAL $diferencia HOUR), '%l:%i %p') ";
	$Query .= "FROM k_clase a ";
	$Query .= "LEFT JOIN k_semana b ON b.fl_semana=a.fl_semana ";
	$Query .= "LEFT JOIN c_leccion c ON c.fl_leccion=b.fl_leccion ";
	$Query .= "WHERE a.fl_grupo=$fl_grupo ";
	$Query .= "AND a.fg_adicional='1' ";
	$rs = EjecutaQuery($Query);
	while($row=RecuperaRegistro($rs)){
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
  $Query  = "SELECT kcg.ds_titulo, DATE_FORMAT(DATE_ADD(kcg.fe_clase, INTERVAL $diferencia HOUR), '%Y-%m-%d'),  DATE_FORMAT(DATE_ADD(kcg.fe_clase, INTERVAL $diferencia HOUR), '%l:%i %p'), ds_clase ";
  $Query .= "FROM c_usuario cus, k_ses_app_frm_1 frm ";
  $Query .= "JOIN k_curso_cg  kcc ON(kcc.fl_programa=frm.fl_programa) ";
  $Query .= "JOIN c_clase_global cc ON(cc.fl_clase_global = kcc.fl_clase_global) ";
  $Query .= "LEFT JOIN k_clase_cg kcg ON ( kcg.fl_clase_global = cc.fl_clase_global ) ";
  $Query .= "WHERE cus.cl_sesion = frm.cl_sesion AND fg_activo='1'  AND cus.fl_usuario = $fl_usuario ";
  $rs = EjecutaQuery($Query);
	while($row=RecuperaRegistro($rs)){
		$ds_titulo = $row[0];
		$fe_clase = $row[1];
		$hr_clase = $row[2];
    $ds_clase_global = $row[3];

    $back_grou = ($ds_titulo=="Student Networking")?"#535e6a":"#44678c";

		$event = array(
    	"title" => "GC-".$ds_clase_global,
  		"start" => $fe_clase,
  		"description" => $ds_titulo ." ".$hr_clase,
  		// "description" => "Live Global Class at ".$hr_clase,
  		"backgroundColor" => $back_grou
  	);
  	array_push($result["event"], $event);
	}
  # End Global Class


    #Query para traer las claes grupales.

    $Queryg="
        SELECT  a.nb_grupo,c.nb_clase, DATE_FORMAT(DATE_ADD(c.fe_clase, INTERVAL $diferencia HOUR), '%Y-%m-%d'),DATE_FORMAT(DATE_ADD(c.fe_clase, INTERVAL $diferencia HOUR), '%l:%i %p')
        FROM c_grupo a
        JOIN k_clase_grupo c ON c.fl_grupo=a.fl_grupo
        JOIN k_alumno_grupo g ON g.fl_grupo=a.fl_grupo
        WHERE g.fl_alumno=$fl_usuario
    ";
    $rsg = EjecutaQuery($Queryg);
	while($rowg=RecuperaRegistro($rsg)){

        $nb_grupo = $rowg[0];
        $nb_clase=$rowg[1];
		$fe_clase = $rowg[2];
		$hr_clase = $rowg[3];
        $ds_clase_global =$nb_grupo." - ".$nb_clase;

        //$programas=ObtenEtiqueta(2521);


        $event = array(
    	"title" => $ds_clase_global,
  		"start" => $fe_clase,
  		"description" => "Groups ".$hr_clase,
  		"backgroundColor" => "#007bff"
  	    );
        array_push($result["event"], $event);
    }



  echo json_encode((Object) $result);
?>