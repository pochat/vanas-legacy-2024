<?php
  # Librerias
  require '../../lib/general.inc.php';
  
  # Recibe Parametros  
  parse_str($_POST['extra_filters']['advanced_search'], $advanced_search);
  $_POST += isset($advanced_search)?$advanced_search:NULL;
  $fl_param = isset($_POST['fl_param'])?$_POST['fl_param']:NULL;
  $fe_ini = isset($_POST['fe_uno'])?$_POST['fe_uno']:NULL;
  $fe_dos = isset($_POST['fe_dos'])?$_POST['fe_dos']:NULL;
  $fl_programa = isset($_POST['fl_programa']) ? $_POST['fl_programa']:NULL;
  $fg_opcion=isset($_POST['fg_opcion']) ? $_POST['fg_opcion']:NULL;
  $fl_pais=$_POST['fl_pais'];

  if((empty($fe_ini))&&(empty($fe_dos))){
  
      #Obtenemos fecha actual :
      $Query3 = "Select CURDATE() ";
      $row = RecuperaValor($Query3);
      $fe_consulta =strtotime('-1 years',strtotime($row[0])); #restamos 1 años.
      $fecha1= date('d-m-Y',$fe_consulta);
      $fecha_dos=strtotime('0 days',strtotime($row[0]));
      $fecha2= date('d-m-Y',$fecha_dos);	
      

      #Obtenemos año actual :
      $Query3 = "Select CURDATE() ";
      $row = RecuperaValor($Query3);
      $anio_actual=strtotime('-0 years',strtotime($row[0]));
      $anio_actual= date('Y',$anio_actual);
      
      $anio_anterior =strtotime('-1 years',strtotime($row[0])); #restamos 1 años.
      $anio_anterior= date('Y',$anio_anterior);

      $fe_consulta =strtotime('-1 years',strtotime(''.$anio_actual.'-07-01')); #restamos 1 años.
      $fe_ini= date('d-m-Y',$fe_consulta);


      $fecha_dos=strtotime('0 days',strtotime(''.$anio_actual.'-06-30'));
      $fe_dos= date('d-m-Y',$fecha_dos);	

  }
  



  if(isset($fe_ini)){
  #Damos formato de fecha alos parametros recibidos.
  $fe_ini =strtotime('0 days',strtotime($fe_ini)); 
  $fecha1= date('Y-m-d',$fe_ini);
  }
  if(isset($fe_dos)){
  $fe_dos=strtotime('0 days',strtotime($fe_dos)); 
  $fecha2= date('Y-m-d',$fe_dos);
  }

  #Muestra resultados de la busqueda.
  $Query ='
    SELECT Main.*,
           Main.nb_usuario "name",
           CONCAT_WS(" ", Main.ds_add_city, Main.nb_zona_horaria) "country",
           CONCAT_WS(" ", Main.nb_programa, "Term:", Main.no_grado) "program",
           Main.status_label "status",
           
           "teachers" "teachers"           
          
         
    FROM (SELECT Usuario.fl_usuario fl_usuario,
                 Usuario.cl_sesion,
                 Usuario.ds_login ds_login,
                 CONCAT_WS(" ", IFNULL(Usuario.ds_nombres, ""),
                                IFNULL(Usuario.ds_apaterno, ""),
                                IFNULL(Usuario.ds_amaterno, "")) nb_usuario,
                 Usuario.ds_nombres ds_nombres,
                 Usuario.ds_apaterno ds_apaterno,
                 Usuario.ds_amaterno ds_amaterno,
                 Usuario.fg_genero fg_genero,
                 Usuario.fe_nacimiento,
                 Usuario.ds_email,
                 Usuario.fg_activo,
                 Usuario.fe_alta, 
                 DATE_FORMAT(Usuario.fe_alta, "%d-%m-%Y") AS fe_alta_label,
                 Usuario.fe_ultacc,
                 USesion.fe_ultmod,
                 Alumno.no_promedio_t,
                 Alumno.ds_notas,
                 CONCAT(ZH.nb_zona_horaria, " ", "GMT", " (", ZH.no_gmt, ")") nb_zona_horaria,
                 (SELECT fg_international
                  FROM k_app_contrato app
                  WHERE app.cl_sesion = Usuario.cl_sesion
                  ORDER BY no_contrato LIMIT 1) fg_international,   
                 Periodo.nb_periodo,
                 (SELECT fe_inicio
                  FROM k_term te, c_periodo i, k_alumno_term al
                  WHERE te.fl_periodo = i.fl_periodo 
                  AND te.fl_term = al.fl_term
                  AND al.fl_alumno = Usuario.fl_usuario
                  AND no_grado = 1
                  LIMIT 1) fe_start_date,    
                 Programa.nb_programa,
                 CONCAT(Profesor.ds_nombres, " ", Profesor.ds_apaterno) ds_profesor,
                 Grupo.nb_grupo,                 
                 PCTIA.fe_carta,
                 PCTIA.fe_contrato,
                 PCTIA.fe_fin,
                 PCTIA.fe_completado,
                 PCTIA.fe_emision,
                 PCTIA.fg_certificado,
                 PCTIA.fg_honores,
                 PCTIA.fe_graduacion,
                (PCTIA.fe_graduacion + INTERVAL 6 month)fe_graduacion_seis_meses,
                (PCTIA.fe_graduacion + INTERVAL 6 month + INTERVAL 7 DAY)fe_graduacion_seis_meses_one,
                (PCTIA.fe_graduacion + INTERVAL 6 month + INTERVAL 14 DAY)fe_graduacion_seis_meses_two,
                 PCTIA.fg_desercion,
                 PCTIA.fg_dismissed,
                 PCTIA.fg_job,
                 PCTIA.fg_graduacion,
                 Form1.ds_add_city,
                 Form1.ds_add_state,
                 USesion.fg_pago,
                 Pais.ds_pais,
                 YEAR(Usuario.fe_nacimiento) ye_fe_nacimiento,
                 YEAR(Usuario.fe_alta) ye_fe_alta,
                 YEAR(Usuario.fe_ultacc) ye_fe_ultacc,
                 YEAR(Form1.fe_ultmod) ye_fe_ultmod,
                 YEAR(PCTIA.fe_carta) ye_fe_carta,
                 YEAR(PCTIA.fe_contrato) ye_fe_contrato,
                 YEAR(PCTIA.fe_fin) ye_fe_fin,
                 YEAR(PCTIA.fe_completado) ye_fe_completado,
                 YEAR(PCTIA.fe_emision) ye_fe_emision,
                 YEAR(PCTIA.fe_graduacion) ye_fe_graduacion,
                 (SELECT YEAR(fe_inicio)
                  FROM k_term te, c_periodo i, k_alumno_term al
                  WHERE te.fl_periodo = i.fl_periodo
                  AND te.fl_term = al.fl_term
                  AND al.fl_alumno = Usuario.fl_usuario
                  AND no_grado = 1
                  LIMIT 1) ye_fe_start_date,
                  CASE
                  WHEN PCTIA.fg_job LIKE "1" THEN "Work placement"
                  WHEN PCTIA.fg_graduacion LIKE "1" THEN "Graduated" 
                  WHEN PCTIA.fg_dismissed LIKE "1" THEN "Student dismissed" 
	          WHEN PCTIA.fg_desercion LIKE "1" THEN "Student withdrawal"
	          WHEN Usuario.fg_activo LIKE "1" THEN "Active"
                  ELSE "Not Set"
                  END status_label,
                  CASE WHEN Grupo.fl_term >0 THEN Grupo.fl_term ELSE 0 END fl_term,
                  Form1.fl_programa,
                  CASE WHEN Term.no_grado >0 THEN Term.no_grado ELSE 0 END no_grado,
            (SELECT cl_calificacion FROM c_calificacion WHERE no_min <= Alumno.no_promedio_t AND no_max >= Alumno.no_promedio_t LIMIT 1) cl_calificacion,
            Alumno.mn_progreso, Programa.ds_duracion,Alumno.fg_absence, Alumno.fg_change_status,Usuario.ds_graduate_status,ProgCosto.cl_delivery,ProgCosto.ds_credential,Usuario.fe_graduate_status   
                  /*      
                  /*
                  OCULTO POR EFICIENCIA
                  ,IFNULL(GROUP_CONCAT(TRUNCATE(Grade.no_promedio, 0) ORDER BY Grade.no_grado ),0) grades_by_term
                  */
          FROM c_usuario Usuario
          JOIN c_sesion USesion ON(USesion.cl_sesion = Usuario.cl_sesion)
          JOIN c_alumno Alumno ON(Usuario.fl_usuario = Alumno.fl_alumno)
          JOIN c_zona_horaria ZH ON(ZH.fl_zona_horaria = Alumno.fl_zona_horaria)
          LEFT JOIN k_alumno_grupo AlumnoGrupo ON(AlumnoGrupo.fl_alumno = Usuario.fl_usuario) AND AlumnoGrupo.fg_grupo_global<>"1" 
          LEFT JOIN c_grupo Grupo ON (Grupo.fl_grupo = AlumnoGrupo.fl_grupo)
          LEFT JOIN c_usuario Profesor ON(Grupo.fl_maestro = Profesor.fl_usuario)
          LEFT JOIN k_term Term ON(Term.fl_term = Grupo.fl_term)
          JOIN k_ses_app_frm_1 Form1 ON(Usuario.cl_sesion = Form1.cl_sesion)
          JOIN c_programa Programa ON(Programa.fl_programa = Form1.fl_programa)
          JOIN c_periodo Periodo ON (Periodo.fl_periodo = Form1.fl_periodo)
          LEFT JOIN k_programa_costos ProgCosto ON ProgCosto.fl_programa=Programa.fl_programa 
          /*
          OCULTO POR EFICIENCIA
          LEFT JOIN (SELECT kat.fl_alumno, t.no_grado, kat.no_promedio, t.fl_programa
                     FROM k_alumno_term kat
                     JOIN k_term t ON(t.fl_term = kat.fl_term)
                     LEFT JOIN (SELECT kat.fl_alumno, t.fl_term, t.no_grado
                                FROM k_alumno_term kat
                                JOIN k_term t ON(t.fl_term = kat.fl_term)
		                ) t2 ON(t2.fl_alumno = kat.fl_alumno 
                                        AND t2.no_grado = t.no_grado 
                                        AND  t.fl_term < t2.fl_term )
		     WHERE t2.fl_term IS NULL
                     ORDER BY t.no_grado, t.fl_term) Grade ON(Grade.fl_alumno = Usuario.fl_usuario 
                                                              AND Grade.fl_programa = Programa.fl_programa)
          */
          JOIN c_pais Pais ON(Pais.fl_pais = Form1.ds_add_country)
          LEFT JOIN k_pctia PCTIA ON (PCTIA.fl_alumno = Usuario.fl_usuario)
          WHERE Usuario.fl_perfil = 3 ';
  if(!empty($fl_pais)){
      $Query.=' AND Form1.ds_add_country='.$fl_pais.' ';
  }
  $Query.='
          GROUP BY Usuario.fl_usuario) AS Main
    WHERE true = true ';
  if($fl_param=='Active'){
      $Query.=' AND fg_activo= "1" ';
  }
  if($fl_param=='Inactive'){
      $Query.=' AND fg_activo ="0" ';
  }
  /**2021 antes el rango se determinabana con la fecha iniciaitl del curso y fecha final del curso, ahora para que le listado este igual a l students todo se aplica en fecha de inciio*/
  if(!empty($fe_ini)){   
      $Query.=' AND fe_start_date >= STR_TO_DATE("'.$fecha1.'", "%Y-%m-%d") ';
      //$Query.=' AND fe_start_date>="'.$fecha1.'" ';
  }
  if(!empty($fe_dos)){
      $Query.=' AND fe_start_date <= STR_TO_DATE("'.$fecha2.'", "%Y-%m-%d") ';
      //$Query.=' AND fe_fin<="'.$fecha2.'" ';  //COMENTADO POR LA SITUACION DE ARRIBA SI HACE FALLA ESTO REGRESAR fe_ini>='fe_ini' and fe_fin<='fe_dos'
  }
  if($fg_opcion=='Certificate'){
      $Query.=' AND ds_credential LIKE "%Certificate%" ';
      //$Query.=' AND fl_programa=31 ';  
  }
  if($fg_opcion=='Diploma'){

      $Query.=' AND ds_credential LIKE "%Diploma%" ';   

  }
  $Query.='  
    ORDER BY Main.fe_alta DESC 
';
  
  
  $rs = EjecutaQuery($Query);
  $registros = CuentaRegistros($rs);

?>
{

    "data": [
    <?php 
    for($i=1;$row=RecuperaRegistro($rs);$i++) {
      $fl_usuario=$row['fl_usuario'];
      $ds_login=$row['ds_login'];
      $nb_usuario=$row['nb_usuario'];

      $ds_add_city=$row['ds_add_city'];
      $ds_pais=$row['ds_pais'];
      $nb_zona_horaria=$row['nb_zona_horaria'];
      $nb_programa=$row['nb_programa'];
      $ds_duracion=$row['ds_duracion'];
      $fl_programa=$row['fl_programa'];

      $cl_calificacion=$row['cl_calificacion'];
      $status_label=$row['status_label'];
      $mn_progreso=$row['mn_progreso'];
      $fe_start_date=$row['fe_start_date'];
      $fe_fin=$row['fe_fin'];
      $fe_graduacion=$row['fe_graduacion'];
      $fe_graduacion_seis_meses=$row['fe_graduacion_seis_meses']; 
      $fe_graduacion_seis_meses_one=$row['fe_graduacion_seis_meses_one'];
      $fe_graduacion_seis_meses_two=$row['fe_graduacion_seis_meses_two'];
      $ds_graduate_status=$row['ds_graduate_status'];
      $fe_graduate_status=$row['fe_graduate_status'];

      if(!empty($fe_start_date)){
          $date = date_create($fe_start_date);
          $fe_start_date = date_format($date, 'F j, Y');
      }
      if(!empty($fe_fin)){
          $date = date_create($fe_fin);
          $fe_fin = date_format($date, 'F j, Y');
      }
      if(!empty($fe_graduacion)){
          $date = date_create($fe_graduacion);
          $fe_graduacion = date_format($date, 'F j, Y');
      }
      if(!empty($fe_graduacion_seis_meses)){
          $date = date_create($fe_graduacion_seis_meses);
          $fe_graduacion_seis_meses = date_format($date, 'F j, Y');

          $date = date_create($fe_graduacion_seis_meses_one);
          $fe_graduacion_seis_meses_one = date_format($date, 'F j, Y');

          $date = date_create($fe_graduacion_seis_meses_two);
          $fe_graduacion_seis_meses_two = date_format($date, 'F j, Y');
      }

      if(!empty($fe_graduate_status)){
          $date = date_create($fe_graduate_status);
          $fe_graduate_status = date_format($date, 'F j, Y');
          $fe_graduate_status="Answer on:".$fe_graduate_status;
      }


      switch ($status_label) 
      {
          case 'Not Set':
              $statusColors="warning";
              break;
          case 'Graduated':
              $statusColors="success";
              break;
          case 'Student withdrawal':
              $statusColors="danger";
              break;
          case 'Work placement':
              $statusColors="danger";
              break;
          case 'Active':
              $statusColors="success";
              break;
      }



      # Obtenemos la clase actual
      $row0 = RecuperaValor("SELECT b.fl_term, c.no_grado FROM k_alumno_grupo a, c_grupo b, k_term c WHERE  a.fl_grupo=b.fl_grupo AND b.fl_term=c.fl_term AND a.fl_alumno =".$row['fl_usuario']."");
      $current_term = !empty($row0[0])?$row0[0]:NULL;
      $current_grado = !empty($row0[1])?$row0[1]:NULL;
      $Query1  = "SELECT MAX(b.no_semana) ";
      $Query1 .= "FROM k_semana a, c_leccion b ";
      $Query1 .= "WHERE a.fl_leccion=b.fl_leccion ";
      $Query1 .= "AND TO_DAYS(a.fe_publicacion) <= TO_DAYS('".date('Y-m-d')."') ";
      $Query1 .= "AND a.fl_term=$current_term ";
      $Query1 .= "AND b.fl_programa=".$row['fl_programa']." ";
      $Query1 .= "AND b.no_grado=$current_grado";    
      $row1 = RecuperaValor($Query1);
      $week_current = !empty($row1[0])?$row1[0]:NULL;

  
      $Query00  = "SELECT MAX(a.fl_term), a.no_promedio FROM k_alumno_term a, k_term b ";
      $Query00 .= "WHERE a.fl_term = b.fl_term AND a.fl_alumno=".$row['fl_usuario']." ORDER BY b.no_grado DESC";
      $row00 = RecuperaValor($Query00);
      $fl_term_max = !empty($row00[0])?$row00[0]:NULL;
      # Obtener el promedio
      $Query01 = "SELECT no_promedio FROM k_alumno_term WHERE fl_alumno=".$row['fl_usuario']." AND fl_term=".$fl_term_max;
      $row01 = RecuperaValor($Query01);
      $no_promedio = $row01[0];
      if(empty($no_promedio))
          $no_promedio=0;
      $row01 = RecuperaValor("SELECT cl_calificacion, fg_aprobado FROM c_calificacion WHERE no_min <= ROUND($no_promedio) AND no_max >= ROUND($no_promedio)");
      $cl_calificacion = $row01[0];
      
      if($fe_graduacion){
          $send_email='<small class=\'text-muted\' style=\'font-size:11px;\'>1st.'.$fe_graduacion_seis_meses.'</small><br><br><small class=\'text-muted\' style=\'font-size:11px;\'>2nd.'.$fe_graduacion_seis_meses_one.'</small><br><br><small class=\'text-muted\' style=\'font-size:11px;\'>3rd.'.$fe_graduacion_seis_meses_two.'</small>';
      }

      
      if($ds_graduate_status){
          
          switch ($ds_graduate_status) 
          {
              case '1':
                  $ds_graduate_status=ObtenEtiqueta(2654);
                  break;
              case '2':
                  $ds_graduate_status=ObtenEtiqueta(2655);
                  break;
              case '3':
                  $ds_graduate_status=ObtenEtiqueta(2656);
                  break;
              case '4':
                  $ds_graduate_status=ObtenEtiqueta(2657);
                  break;
              case '5':
                  $ds_graduate_status=ObtenEtiqueta(2658);
                  break;
              case '6':
                  $ds_graduate_status=ObtenEtiqueta(2659);
                  break;
          }

          $answer='<label class=\'label label-success\'>'.$ds_graduate_status.'</label>';
          
      }


  
    echo '
    {
        
        
        "name": "<a href=\'javascript:Envia(\"students_frm.php\",\"'.$fl_usuario.'\");\'><b>'.$nb_usuario.'</b><br><small class=\'text-muted\'>'.$ds_login.'</small></a>",
        "country": "<td><a href=\'javascript:Envia(\"students_frm.php\",\"'.$fl_usuario.'\");\'>'.$ds_add_city.'</a> '.$ds_pais.'<br><small class=\'text-muted\'>'.$nb_zona_horaria.'</small></td>",           
        "program": "<td><a href=\'javascript:Envia(\"students_frm.php\",\"'.$fl_usuario.'\");\'>'.$nb_programa.'</a> ('.$ds_duracion.')<br><small class=\'text-muted\'>Term: '.$no_grado.', Week '.$week_current.', GPA: '.$cl_calificacion.' ('.round(number_format($no_promedio,2)).'%) </td>",          
        "status": "<p><span class=\'label label-'.$statusColors.'\'>'.$status_label.'</span><br> <br> '.$answer.' ",
		"progress":"<div class=\'progress progress-xs\' data-progressbar-value=\''.round($mn_progreso).'\'><div class=\'progress-bar\'></div></div><span class=\'hidden\'>'.round($mn_progreso).'</span>  ",
		"gpa":"'.$cl_calificacion.'<br>('.$no_promedio.'%) ",
        "start_date":"'.$fe_start_date.'",
        "end_date": "'.$fe_fin.' ",
        "graduate_date":"'.$fe_graduacion.'<br><br>'.$fe_graduate_status.' ",
        "send_email":"'.$send_email.'"
                        
           
 
    }';
        
    
      if($i<=($registros-1))
        echo ",";
      else
        echo "";
    }
    ?>
   ]

}
