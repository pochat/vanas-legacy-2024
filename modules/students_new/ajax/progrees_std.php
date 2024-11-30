<?php 
  # Se agregaron dos campos nuevos al c_alumno actualizar cada vez que entra el estudiante
  # Este archivo actualiza el proceso por programa del estudiante y su semaana actual
	# Libreria de funciones
	require("../../common/lib/cam_general.inc.php");
  
  $Query = "SELECT calu.fl_alumno, CONCAT(cstd.ds_nombres,' ', cstd.ds_apaterno), cstd.cl_sesion  FROM c_alumno calu, c_usuario cstd WHERE  calu.fl_alumno=cstd.fl_usuario ";
  $rs = EjecutaQuery($Query);
  for($i=0;$row=RecuperaRegistro($rs);$i++){
    $fl_alumno = $row[0];
    $ds_nombres = $row[1];
    $cl_sesion = $row[2];
    $fl_programa = ObtenProgramaAlumno($fl_alumno);
    if(empty($fl_programa)){
      $rowp = RecuperaValor("SELECT fl_programa FROM k_ses_app_frm_1 WHERE cl_sesion='$cl_sesion'");
      $fl_programa = $rowp[0];
    }
		$current_no_grado = ObtenGradoAlumno($fl_alumno);
    # Si ya se graduo o no tiene un grado
    if(empty($current_no_grado)){
      $Queryt  = "SELECT MAX(no_grado) FROM k_alumno_term a, k_term b ";
      $Queryt .= "WHERE a.fl_term=b.fl_term AND fl_alumno=$fl_alumno ";
      $rowt = RecuperaValor($Queryt);
      $current_no_grado = $rowt[0];
    }
		$current_no_semana = ObtenSemanaActualAlumno($fl_alumno);
    if(empty($current_no_semana)){
      $Querys = "SELECT MAX(no_semana) FROM k_entrega_semanal a, k_semana b, c_leccion c ";
      $Querys .= "WHERE a.fl_semana=b.fl_semana AND b.fl_leccion = c.fl_leccion AND a.fl_alumno=$fl_alumno ";
      $rows= RecuperaValor($Querys);
      $current_no_semana = $rows[0];
    }
		$current_max_semana = ObtenSemanaMaximaAlumno($fl_alumno);
    if(empty($current_max_semana)){
      $Querym  = "SELECT MAX(no_semana) ";
      $Querym .= "FROM c_leccion ";
      $Querym .= "WHERE fl_programa=$fl_programa ";
      $Querym .= "AND no_grado=$current_no_grado";
      $rowm = RecuperaValor($Querym);
      $current_max_semana = $rowm[0];
    }
		$fl_grupo = ObtenGrupoAlumno($fl_alumno);
    if(empty($fl_grupo)){
      $rowg = RecuperaValor("SELECT MAX(fl_grupo) FROM k_alumno_historia WHERE fl_alumno=$fl_alumno");
      $fl_grupo = $rowg[0];
    }
    
    // Find max weeks for each term of this program
		$Query2  = "SELECT count(fl_leccion), no_grado ";
		$Query2 .= "FROM c_leccion ";
		$Query2 .= "WHERE fl_programa=$fl_programa ";
		$Query2 .= "GROUP BY no_grado ";
		$Query2 .= "ORDER BY no_grado ";
		$rs2 = EjecutaQuery($Query2);
    
    // Find the total number of weeks of the program and total number of weeks the student has completed
		$total_weeks = 0;
		$total_weeks_done = 0;
		for($j=0; $row2=RecuperaRegistro($rs2); $j++){
			$max_weeks = $row2[0];
			$no_grado = $row2[1];

			// Total weeks for the full duration of the program
			$total_weeks += $max_weeks; 

			// Add up to the number of weeks the student has completed until this term
			if($no_grado < $current_no_grado){
				$total_weeks_done += $max_weeks;
			}
		}
		// Also include the weeks done in the current term
		$total_weeks_done += $current_no_semana;

		// Program Pie Chart
    if(!empty($total_weeks_done) AND !empty($total_weeks))
      $percent = $total_weeks_done / $total_weeks *100;
    else
      $percent = 0;
    //echo $ds_nombres."--".$fl_programa."--".$current_no_grado."--".$current_no_semana."--".$current_max_semana."--".$fl_grupo."<br/>";
    //echo $ds_nombres."--".$percent."<br/>";
    # Actualizara sus datos
    //echo $ds_nombres."-->".$update = "UPDATE c_alumno SET mn_progreso=".round($percent,2).", no_week_current=$current_no_semana WHERE fl_alumno=$fl_alumno <br>";
    $update = "UPDATE c_alumno SET mn_progreso='".round($percent,2)."', no_week_current=$current_no_semana WHERE fl_alumno=$fl_alumno ";
    EjecutaQuery($update);
    
  }

?>