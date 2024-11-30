<?php 
	# Libreria de funciones
	require("../../common/lib/cam_general.inc.php");

	# Verifica que exista una sesion valida en el cookie y la resetea
  $fl_usuario = ValidaSesion(False);
 
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermisoCampus(FUNC_MAESTROS)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Retrieve the group's progression state
  function GetGroupProgress($fl_usuario, $fl_grupo, $fl_term, $fl_programa, $no_grado,$fg_grupo_global=""){

  	# Current week
  	$fe_actual = ObtenFechaActual( );
    $Query  = "SELECT MAX(a.fl_semana) FROM k_semana a ";
    $Query .= "WHERE fl_term=$fl_term AND TO_DAYS(a.fe_publicacion) <= TO_DAYS('$fe_actual') ";
	  $row_semana = RecuperaValor($Query);
	  $fl_semana = !empty($row_semana[0])?$row_semana[0]:NULL;
	  $row_semana1 = RecuperaValor("SELECT no_semana, a.fl_leccion FROM c_leccion a, k_semana b WHERE a.fl_leccion = b.fl_leccion AND b.fl_semana=$fl_semana");
    $no_semana = !empty($row_semana1[0])?$row_semana1[0]:NULL;
	  $fl_leccion = !empty($row_semana1[1])?$row_semana1[1]:NULL;
	 	// Find title corresponding to the max week in progress
	  $Query = "SELECT ds_titulo FROM c_leccion WHERE fl_leccion=$fl_leccion ";
	  $row = RecuperaValor($Query);
	  $ds_titulo = !empty($row[0])?$row[0]:NULL;
	  if(empty($no_semana)){
	    $no_semana = 0;
	    $ds_titulo = "";
	  }
	 	
	# Live session
    $liveSession = ObtainFechaGroupSessionTeacher($fl_usuario, $fl_grupo);
    $folioClase = ObtainFolioGroupSessionTeacher($fl_usuario, $fl_grupo);
    $grupo = ObtenGrupoTeacher($fl_usuario);

    // Default live session variables
    $liveSessionExists = false;
    $liveSessionTime = $liveSession["actual"];
    $liveSessionReadable = $liveSession["readable"];
    $liveSessionLink = "";
    $liveSessionStart = "";
    $liveSessionClose = "";

    // If class is available
    if(!empty($liveSession["actual"])){
    	$liveSessionExists = true;
    	$liveSessionLink = "href='../liveclass/LiveSession.php?folio=$folioClase'";
    	$liveSessionStart = $liveSession["early"];
    	$liveSessionClose = $liveSession["after"];
    }

	 	# Assignment deadline
    $Query  = "SELECT DATE_FORMAT(fe_entrega, '%W, %M %e, %Y') ";
		$Query .= "FROM k_semana ";
		$Query .= "WHERE fl_term=$fl_term AND fl_leccion=$fl_leccion";
		$row = RecuperaValor($Query);
		$fe_entrega = !empty($row[0])?$row[0]:NULL;
		if(empty($fe_entrega)){
			$fe_entrega = "N/A";
		}

	 	# Next class
	  $Query  = "SELECT MAX(no_semana) ";
	  $Query .= "FROM c_leccion ";
	  $Query .= "WHERE fl_programa=$fl_programa ";
	  $Query .= "AND no_grado=$no_grado";
	  $row = RecuperaValor($Query);
	  $max_semana = $row[0];
	  if(!empty($max_semana)) {
	  	$next_semana = $no_semana + 1;
	  	if($next_semana <= $max_semana){
	  		# Retrieve the info next class
			  $Query  = "SELECT ds_titulo, DATE_FORMAT(fe_publicacion, '%W, %M %e, %Y') ";
			  $Query .= "FROM c_leccion a ";
			  $Query .= "LEFT JOIN k_semana b ON (a.fl_leccion=b.fl_leccion AND fl_term=$fl_term) ";
			  $Query .= "WHERE fl_programa=$fl_programa ";
			  $Query .= "AND no_grado=$no_grado ";
			  $Query .= "AND no_semana=$next_semana";
			  $row = RecuperaValor($Query);
			  $ds_titulo_next = !empty($row[0])?$row[0]:NULL;
			  $fe_publicacion_next = !empty($row[1])?$row[1]:NULL;
	  	} else {
	  		$next_semana = 0;
  			$ds_titulo_next = "";
  			$fe_publicacion_next = "";
  		}
	  } 


      if($fg_grupo_global==1){
          
          #Recuperamos la semana

          $Query  = " SELECT MAX(a.fl_semana_grupo),no_semana FROM k_semana_grupo a ";
          $Query .= "WHERE fl_grupo=$fl_grupo AND TO_DAYS(a.fe_publicacion) <= TO_DAYS('$fe_actual') ";
          $row_semana = RecuperaValor($Query);
          $fl_semana = $row_semana[0];
          $no_semana=$row_semana['no_semana'];
          $no_semana = ObtenSemanaActualAlumno($fl_usuario,$fg_grupo_global,$fl_grupo);
          #Recuperamos el nombre de la clase.
          $Query="SELECT nb_clase FROM k_clase_grupo WHERE fl_grupo=$fl_grupo AND fl_semana_grupo=$fl_semana ";
          $row=RecuperaValor($Query);
          $ds_titulo=$row['nb_clase'];

          if(empty($no_semana)){
              $no_semana = 0;
              $ds_titulo = "";
          }

          $next_semana=$no_semana+1;

          

          #Recupermaos Next class.
          $Query="SELECT fl_semana_grupo FROM k_semana_grupo WHERE fl_grupo=$fl_grupo AND no_semana=$next_semana ";
          $rop=RecuperaValor($Query);
          $fl_nex_semana=$rop['fl_semana_grupo'];

          #Recuperamos el nombre de la clase.
          $Query="SELECT nb_clase,DATE_FORMAT(fe_clase, '%W, %M %e, %Y') as fe_clase FROM k_clase_grupo WHERE fl_grupo=$fl_grupo AND fl_semana_grupo=$fl_nex_semana ";
          $row=RecuperaValor($Query);
          $ds_titulo_next=$row['nb_clase'];
          $fe_publicacion_next=$row['fe_clase'];

          
          $liveSession = ObtainFechaGroupSessionTeacherGG($fl_usuario, $fl_grupo);
          $folioClase = ObtainFolioGroupSessionTeacherGG($fl_usuario, $fl_grupo);

          // Default live session variables
          $liveSessionExists = false;
          $liveSessionTime = $liveSession["actual"];
          $liveSessionReadable = $liveSession["readable"];
          $liveSessionLink = "";
          $liveSessionStart = "";
          $liveSessionClose = "";

          // If class is available
          if(!empty($liveSession["actual"])){
              $liveSessionExists = true;
              $liveSessionLink = "href='../liveclass/LiveSession_gg.php?folio=$folioClase'";
              $liveSessionStart = $liveSession["early"];
              $liveSessionClose = $liveSession["after"];
          }




      }



  	return array(
  		"current_week" => $no_semana,
  		"title" => $ds_titulo,
  		"live_session_exists" => $liveSessionExists,
    	"live_session_time" => $liveSessionTime,
    	"live_session_readable" => $liveSessionReadable,
    	"live_session_link" => $liveSessionLink,
    	"live_session_start" => array(
  			"seconds" => (int)$liveSessionStart,
  			"milliseconds" => (int)$liveSessionStart * 1000
    	),
    	"live_session_close" => array(
  			"seconds" => (int)$liveSessionClose,
  			"milliseconds" => (int)$liveSessionClose * 1000
    	),
    	"assignment_deadline" => $fe_entrega,
    	"next_week" => $next_semana,
    	"next_title" => $ds_titulo_next,
    	"next_date" => $fe_publicacion_next
  	);
  }

  # Prepares a list of students for each group
	function GetTeacherGroups($fl_usuario){
		$result = array();
		$result["size"] = array();
		

        $fe_actual = ObtenFechaActual( );

		# There could be multiple groups that a teacher is teaching in one term
        $Query ="(";
		$Query .= "SELECT fl_grupo, fl_term, nb_grupo,''fg_grupo_global FROM c_grupo WHERE fl_maestro=$fl_usuario AND fg_grupo_global<>'1'   ";
        $Query.=")UNION (";
        $Query.="

                select DISTINCT a.fl_grupo,''fl_term, nb_grupo,a.fg_grupo_global from c_grupo a
                 JOIN k_clase_grupo b ON a.fl_grupo=b.fl_grupo
                WHERE b.fl_maestro=$fl_usuario  AND a.fg_grupo_global='1'  AND  DATE_FORMAT(b.fe_clase,'%Y-%m-%d')>=DATE_FORMAT('$fe_actual','%Y-%m-%d')
 
        ";
        $Query.=") ";
		$rs2 = EjecutaQuery($Query);

		$total_groups = 0;
		$total_students = 0;
		# For each group of students
		for($i=0; $row2=RecuperaRegistro($rs2); $i++){
			$fl_grupo = $row2[0];
			$fl_term = $row2[1];
			$nb_grupo = $row2[2];
            $fg_grupo_global=$row2['fg_grupo_global'];
			$rs = StudentQuery("", "", "", $fl_grupo,$fg_grupo_global);

            #Recuperamos los terms que hay.
            if($fg_grupo_global==1){
                $Query="SELECT fl_term FROM  k_grupo_term WHERE fl_grupo=$fl_grupo ";
                $rsm=EjecutaQuery($Query);
                $total_terms=CuentaRegistros($rsm);
                $fl_terms = '';
                for($im=1;$im<$rowm=RecuperaRegistro($rsm);$im++){
                    $fl_terms.=$rowm['fl_term'];

                    if($im<=($total_terms-1)){
                        $fl_terms.= ",";
                       
                    }else{
                        $fl_terms.= "";
                       
                    }





                }




            }


			# Check if group is active
			$Query = "SELECT COUNT(1) FROM k_alumno_grupo a LEFT JOIN c_usuario b ON b.fl_usuario=a.fl_alumno WHERE fl_grupo=$fl_grupo AND b.fg_activo='1'";
			$active = RecuperaValor($Query);
			$fg_active = $active[0];

			// Add active group of students to result
			if(!empty($fg_active)){
				$Query  = "SELECT  a.fl_programa, b.nb_programa, a.no_grado ";
		  	$Query .= "FROM k_term a ";
		  	$Query .= "LEFT JOIN c_programa b ON b.fl_programa=a.fl_programa ";
              if($fg_grupo_global==1){
                  
                  $Query.="WHERE fl_term IN($fl_terms)";
                  $rsp=EjecutaQuery($Query);
                  $to_reg=CuentaRegistros($rsp);
                  $nb_programa="";
                  $no_grado="";
                  for($ip=1;$ip<$rop=RecuperaRegistro($rsp);$ip++){
                      $nb_programa_ =$rop['nb_programa'];
                      $no_grado =$rop['no_grado'];
                      $nb_programa.=$nb_programa_.", Term: ".$no_grado;

                      if($ip<=($to_reg-1)){
                          $nb_programa.= " | ";
                          
                      }else{
                          $nb_programa.= ", ";
                         
                      }

                  }
                 

              }else{
                  $nb_programa="";
                  $Query .= "WHERE fl_term=$fl_term";
                  $row = RecuperaValor($Query);
                  $fl_programa = $row[0];
                  $nb_programa = $row[1];
                  $no_grado = $row[2];


              }
              
              
              

				$result += array("group".$total_groups => array("program" => $nb_programa));
				$result["group".$total_groups] += array("group_name" => $nb_grupo,"term_actual" => $no_grado);

				// Get the group's progress
				$progress = GetGroupProgress($fl_usuario, $fl_grupo, $fl_term, $fl_programa, $no_grado,$fg_grupo_global);
				$result["group".$total_groups] += array("progress" => $progress);
				
				# Extract each student's info
				for($j=0; $row=RecuperaRegistro($rs); $j++){
					// General student info
					$fl_alumno = $row[0];
                    $fg_grupo_global=$row[9];
                    $fl_grupo=$row[10];
                    
                    

                        
                        $no_semana = ObtenSemanaActualAlumno($fl_alumno,$fg_grupo_global,$fl_grupo);
                        $fl_semana = ObtenFolioSemanaAlumno($fl_alumno, $no_semana,$fg_grupo_global,$fl_grupo);

                        

                   if($fg_grupo_global==1){
                       
                       $fe_actual = ObtenFechaActual( );
                       #Recuepramos la semana.
                       $Query="SELECT MAX(a.no_semana),a.fl_semana_grupo FROM k_semana_grupo a
                                WHERE TO_DAYS(a.fe_publicacion) <= TO_DAYS('$fe_actual')
                               AND  fl_grupo=$fl_grupo ";
                       $rop=RecuperaValor($Query);
                      $fl_semana=$rop['fl_semana_grupo'];

                   }



					if(!empty($row[1]))
						$ds_ruta_avatar = "<img src='".PATH_ALU_IMAGES."/avatars/".$row[1]."'>";
					else 
						$ds_ruta_avatar =  "<img src='".SP_IMAGES."/".IMG_S_AVATAR_DEF."'>";
					$ds_nombre = str_uso_normal($row[2]);
					$nb_programa = str_uso_normal($row[3]);
					$ds_pais = str_uso_normal($row[4]);
					
					$fg_genero=str_uso_normal($row[7]);
          $no_age=str_uso_normal($row[8]);
          
          if($fg_genero=='F'){
              $icono="female";
              $text="Female";
          }else{
              $icono="male";
              $text="Male";
          }
                    
          #Verificamos la discapacidad
          $Queryd="SELECT fg_health_condition,U.fg_nuevo FROM k_app_contrato A JOIN c_usuario U ON U.cl_sesion=A.cl_sesion  WHERE U.fl_usuario=$fl_alumno ";
          $rowd=RecuperaValor($Queryd);
          $fg_disability=$rowd['fg_health_condition'];
          $fg_nuevo_alumno=$rowd['fg_nuevo'];

          if($fg_disability)
          $disability=" Yes";
          else
          $disability=" No";

					# THe Query was adjusteed to get results using "$fl_alumno" instead of "$cl_session" (this don't exists)
          #Verificamos el disability.
          $Querydis="SELECT fg_disability, ds_disability FROM k_ses_app_frm_1 sesapp JOIN c_usuario usr ON(usr.cl_sesion=sesapp.cl_sesion) WHERE fl_usuario='$fl_alumno' ";

          $rowdis=RecuperaValor($Querydis);
          $fg_disabilityies=!empty($rowdis['fg_disability'])?$rowdis['fg_disability']:NULL;
          $ds_disabilities=!empty($rowdis['ds_disability'])?str_texto($rowdis['ds_disability']):NULL;
          
         if($fg_disabilityies){
             $ds_disabilities=$ds_disabilities;
             $disability=" Yes";
         }else{
             $ds_disabilities=""; 
             $disability=" No";
         }
					
				
					#Solo presenta Boton Para nuvos alumnos
					if($fg_nuevo_alumno==1){
					
					     $boton_rubric='<a href="javascript:void(0);" onclick="PresentaRubricEvaluation('.$fl_alumno.','.$fl_programa.');" class="btn btn-default btn-sm"><i class="fa fa-table"></i>&nbsp;'.ObtenEtiqueta(1790).'</a> ';
					
					
					}else{
					
					    $boton_rubric=" ";
					}
					
					
					// Assignment Submission Time
					$Query  = "SELECT DATE_FORMAT(fe_entregado, '%W, %M %e, %Y @ %h:%i %p'), DATE_FORMAT(fe_entregado, '%M %e, %Y') ";
					$Query .= "FROM k_entrega_semanal ";
					$Query .= "WHERE fl_alumno=$fl_alumno AND fl_grupo=$fl_grupo AND fl_semana=$fl_semana ";
					$row3 = RecuperaValor($Query);
					if(empty($row3[0])){
						$fe_entregado = "Not Submitted";
					} else {
						$fe_entregado = $row3[0];
						$submit_deadline = strtotime($progress["assignment_deadline"]);
						$submit_date = strtotime($row3[1]);

						// Check if the submitted assignment is late
						if($submit_date > $submit_deadline){
							$fe_entregado = "<span class='bg-danger'>$row3[0]<span>";
						}
					}
                    
					// Previous Grade
					if($no_semana > 0){
						$prev_no_semana = $no_semana - 1;
                        $prev_fl_semana = ObtenFolioSemanaAlumno($fl_alumno, $prev_no_semana);
                        
						#Verificamos si existe una califacion por medio de rubrics.
                        $QueryRub="SELECT CONCAT( TRUNCATE(no_calificacion, 0), '%')AS no_calificacion FROM k_calificacion_teacher_campus where fl_programa=$fl_programa AND fl_alumno=$fl_alumno AND fl_semana=$prev_fl_semana AND fl_grupo=$fl_grupo ";
                        $row3=RecuperaValor($QueryRub);
                        $no_calificacion_rubric=!empty($row3[0])?$row3[0]:NULL;
                        //$rubtic="es rubric";
                        if(empty($no_calificacion_rubric)){                        
                            $Query  = "SELECT CONCAT( TRUNCATE(b.no_equivalencia, 0), '%') ";
						    $Query .= "FROM k_entrega_semanal a ";
						    $Query .= "LEFT JOIN c_calificacion b ON b.fl_calificacion=a.fl_promedio_semana ";
						    $Query .= "WHERE fl_alumno=$fl_alumno AND fl_grupo=$fl_grupo AND fl_semana=$prev_fl_semana ";
						    $row3 = RecuperaValor($Query);//$rubtic="";
                        }
						if(!empty($row3[0])){
							$prev_no_equivalencia = $row3[0];	
						} else {
							$prev_no_equivalencia = "Not Assigned";
						}
					} else {
						$prev_no_equivalencia = "Not Available";
                        $prev_no_semana = NULL;
					}

					// Grade for Current Week
					$Query  = "SELECT CONCAT( TRUNCATE(b.no_equivalencia, 0), '%') ";
					$Query .= "FROM k_entrega_semanal a ";
					$Query .= "LEFT JOIN c_calificacion b ON b.fl_calificacion=a.fl_promedio_semana ";
					$Query .= "WHERE fl_alumno=$fl_alumno AND fl_grupo=$fl_grupo AND fl_semana=$fl_semana ";
					$row3 = RecuperaValor($Query);
					if(!empty($row3[0])){
						$no_equivalencia = $row3[0];	
					} else {
						$no_equivalencia = "Not Assigned";
					}

					// Calculate Current GPA
					$Query  = "SELECT SUM(b.no_equivalencia), COUNT(a.fl_promedio_semana) ";
					$Query .= "FROM k_entrega_semanal a ";
					$Query .= "LEFT JOIN c_calificacion b ON b.fl_calificacion=a.fl_promedio_semana ";
					$Query .= "WHERE fl_alumno=$fl_alumno AND fl_grupo=$fl_grupo ";
					$row3 = RecuperaValor($Query);
					$sum_grades = $row3[0];
					$total_grades = $row3[1];
                    if((empty($sum_grades)) OR ($total_grades == 0)){
                        $GPA = 0;
					}else{
                        $GPA = ($sum_grades / $total_grades);
                    }

					// Attendance from last week
					if($prev_no_semana > 0){
						$Query  = "SELECT a.fl_clase, b.fl_live_session, a.fe_clase, fg_obligatorio, fg_adicional,zoom_url ";
						$Query .= "FROM k_clase a ";
						$Query .= "LEFT JOIN k_live_session b ON b.fl_clase=a.fl_clase ";
						$Query .= "WHERE fl_grupo=$fl_grupo AND fl_semana=$prev_fl_semana ";
						$row3 = RecuperaValor($Query);
						$fl_clase = !empty($row3[0])?$row3[0]:NULL;
						$fl_live_session = !empty($row3[1])?$row3[1]:NULL;
						$fe_clase = !empty($row3[2])?$row3[2]:NULL;
                        $zoom_url=$row3['zoom_url'];

						$Query  = "SELECT a.fl_live_session, b.nb_estatus ";
						$Query .= "FROM k_live_session_asistencia a ";
						$Query .= "LEFT JOIN c_estatus_asistencia b ON (b.cl_estatus_asistencia=a.cl_estatus_asistencia) ";
						$Query .= "WHERE fl_live_session=$fl_live_session AND fl_usuario=$fl_alumno";
						$row3 = RecuperaValor($Query);
						if(!empty($row3[0])){
							$nb_estatus = $row3[1];
						} else {
	            $diferencia_fechas = strtotime($fe_clase) + 1200 - time();
	            if($diferencia_fechas <= 0){
	              $row3 = RecuperaValor("SELECT nb_estatus FROM c_estatus_asistencia d WHERE cl_estatus_asistencia=1");
	              $nb_estatus = $row3[0];
	            }
						}
					} else {
						// Class has not started yet
						$nb_estatus = "N/A";
					}
                    
                
                    
              if($fg_grupo_global==1){
                  




              }


                    

					$student = array(
                        "fl_alumno"=>$fl_alumno,
						"name" => $ds_nombre,
						"submission" => $fe_entregado,
						"previous_grade" => $prev_no_equivalencia,
						"current_grade" => $no_equivalencia,
                        "disability" => '<i class="fa fa-heart-o"></i>'.$disability,
						"ds_disability"=>$ds_disabilities,
						"GPA" => round($GPA).'%',
						"previous_attendance" => $nb_estatus,
						"country" => $ds_pais,
						"gender"=>'<i class="fa fa-'.$icono.'"></i> '.$text,
                        "age"=> '<i class="fa fa-birthday-cake"></i>&nbsp;'.$no_age.' years',
                        "etiqueta_btn1"=> ObtenEtiqueta(1776),
                        "etiqueta_btn2"=> ObtenEtiqueta(1777),
						"btn_rubric"=> $boton_rubric
                       
						
						
						
					);
					$result["group".$total_groups] += array("student".$j => $student);
					$total_students++;
				}
				$result["group".$total_groups] += array("group_size" => array("total_students" => $j));
				$total_groups++;
			}		
		}
		$result["size"] += array("total_groups" => $total_groups);
		$result["size"] += array("total_students" => $total_students);
		echo json_encode((Object) $result);
	}

?>
<style>
    .table-hover > tbody > tr:hover > td, .table-hover > tbody > tr:hover > th {
        background-color:transparent !important;
    }

</style>



<?php 
$access_early = ObtenConfiguracion(34);
$access_after = ObtenConfiguracion(35);
$tolerancia_link = ObtenConfiguracion(36);	
$diferencia = RecuperaDiferenciaGMT( );

$Query="SELECT DISTINCT a.fl_clase_global,a.ds_titulo 
        FROM k_clase_cg a 
        JOIN c_clase_global cg ON cg.fl_clase_global=a.fl_clase_global
        WHERE a.fl_maestro=$fl_usuario  
        AND TIMESTAMPDIFF(SECOND, '".ObtenFechaActual( )."', DATE_ADD(a.fe_clase, INTERVAL $tolerancia_link MINUTE)) >= 0 LIMIT 1 ";

$rsg = EjecutaQuery($Query);
for($jg=0; $rowg=RecuperaRegistro($rsg); $jg++){

    $fl_clase_global=$rowg[0];
    $ds_titulo=$rowg[1];
    $contador_global_class=1;
   

    #Obtenemos fecha actual :
    $Query = "Select CURDATE() ";
    $row = RecuperaValor($Query);
    $fe_actual = str_texto($row[0]);
    $fe_actual=strtotime('+0 day',strtotime($fe_actual));
    $fe_actual= date('Y-m-d',$fe_actual);

    #Recuperamos clase proxima.
    $Query2="SELECT  a.fl_clase_global,a.ds_titulo,DATE_FORMAT(DATE_ADD(a.fe_clase, INTERVAL $diferencia HOUR), '%W, %M %e, %Y') fe_clase,DATE_FORMAT(DATE_ADD(a.fe_clase, INTERVAL $diferencia HOUR) ,'%h:%i %p') hr_clase,
            UNIX_TIMESTAMP( DATE_SUB(fe_clase, INTERVAL $access_early MINUTE) ) fe_early,   
	        UNIX_TIMESTAMP( DATE_ADD(fe_clase, INTERVAL $access_after MINUTE) ) fe_after,a.fl_clase_cg, DATE_FORMAT(DATE_ADD(a.fe_clase, INTERVAL $diferencia HOUR), '%Y-%m-%d') fe_hoy_clase, DATE_FORMAT(DATE_ADD(a.fe_clase, INTERVAL $diferencia HOUR), '%m-%d-%Y') fe_time   
            ,DATE_FORMAT( DATE_ADD(a.fe_clase, INTERVAL 105 MINUTE) ,'%h:%i %p') hr_clase_script
            FROM k_clase_cg a 
            JOIN c_clase_global cg ON cg.fl_clase_global=a.fl_clase_global
            WHERE a.fl_maestro= $fl_usuario 
            AND TIMESTAMPDIFF(SECOND, '".ObtenFechaActual( )."', DATE_ADD(a.fe_clase, INTERVAL $tolerancia_link MINUTE)) >= 0  
                AND a.fl_clase_global=$fl_clase_global   
            LIMIT 1 ";
    $row=RecuperaValor($Query2);
    $fe_clase=$row['fe_clase'];
    $fe_early=$row[4];
    $fe_after=$row[5];
    $hr_clase=$row[3];
    $fl_clase_cg=$row['fl_clase_cg'];
    $fe_hoy_clase=$row['fe_hoy_clase'];
    $disable="disabled";
    $fe_time=$row['fe_time'];
    $hr_clase_script=$row['hr_clase_script']
   

?>
<div class="panel panel-primary">
    <div class="panel-heading">Global Class <?php echo $ds_titulo; ?></div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-4">             
                    <div  data-group="0" class="well ht-150 padding-10">
                        <h1 class="no-margin "><strong>Next Live Class:</strong>
                            <br>
                            <?php 
                                 echo $fe_clase." at ".$hr_clase."<strong> in </strong>";

                                

                            ?>
                           <!-- Contador regresivo -->
    <section id="hours_session" class="">
        <p>
            <span id="days"></span> days <span id="hours"></span>:<span id="minutes"></span>:<span id="seconds"></span>
        </p>
    </section>
                            
                            <a href="../liveclass/LiveSession_gc.php?folio=<?php echo $fl_clase_cg; ?>" target="_blank" role="button" class="btn btn-sm btn-default pull-right disabled" id="btn_join_disabled" style="position: absolute; right: 10px; bottom: 10px;letter-spacing: normal;">Join Class</a>
                            <a href="../liveclass/LiveSession_gc.php?folio=<?php echo $fl_clase_cg; ?>" target="_blank" role="button" class="btn btn-sm btn-primary  pull-right hidden" id="btn_join" style="position: absolute; right: 10px; bottom: 10px;letter-spacing: normal;">Join Class</a>
                            </div>
                </div>        
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => { 

        //===
        // VARIABLES
        //===
        const DATE_TARGET = new Date('<?php echo $fe_time." ".$hr_clase_script;?>');
    // DOM for render
        const SPAN_DAYS = document.querySelector('span#days');
        const SPAN_HOURS = document.querySelector('span#hours');
        const SPAN_MINUTES = document.querySelector('span#minutes');
        const SPAN_SECONDS = document.querySelector('span#seconds');
    // Milliseconds for the calculations
        const MILLISECONDS_OF_A_SECOND = 1000;
        const MILLISECONDS_OF_A_MINUTE = MILLISECONDS_OF_A_SECOND * 60;
        const MILLISECONDS_OF_A_HOUR = MILLISECONDS_OF_A_MINUTE * 60;
        const MILLISECONDS_OF_A_DAY = MILLISECONDS_OF_A_HOUR * 24

    //===
    // FUNCTIONS
    //===

    /**
    * Method that updates the countdown and the sample
    */
    function updateCountdown() {
        // Calcs
        const NOW = new Date()
        const DURATION = DATE_TARGET - NOW;
        const REMAINING_DAYS = Math.floor(DURATION / MILLISECONDS_OF_A_DAY);
        const REMAINING_HOURS = Math.floor((DURATION % MILLISECONDS_OF_A_DAY) / MILLISECONDS_OF_A_HOUR);
        const REMAINING_MINUTES = Math.floor((DURATION % MILLISECONDS_OF_A_HOUR) / MILLISECONDS_OF_A_MINUTE);
        const REMAINING_SECONDS = Math.trunc((DURATION % MILLISECONDS_OF_A_MINUTE) / MILLISECONDS_OF_A_SECOND);

        
        $("#days").empty();
        $("#hours").empty();
        $("#minutes").empty();
        $("#seconds").empty();

       
        // Render
       // SPAN_DAYS.textContent = REMAINING_DAYS;
      //  SPAN_HOURS.textContent = REMAINING_HOURS;
      //  SPAN_MINUTES.textContent = REMAINING_MINUTES;
      //  SPAN_SECONDS.textContent = REMAINING_SECONDS
        $("#days").append(REMAINING_DAYS);
        $("#hours").append(REMAINING_HOURS);
        

        if(REMAINING_DAYS < 0 ){

            $("#hours_session").addClass("hidden");
        }else{
 

            //if(REMAINING_MINUTES < 10){

            //    $("#btn_join_disabled").addClass('disabled');
            //    $("#btn_join").removeClass('hidden');
            //}



            if(REMAINING_MINUTES < 10){
                $("#minutes").append("0"+(REMAINING_MINUTES));
            }else{
            
                $("#minutes").append(REMAINING_MINUTES);
            
            }
       
            if(REMAINING_SECONDS < 10){
                $("#seconds").append("0"+(REMAINING_SECONDS));
            }else{
            
                $("#seconds").append(REMAINING_SECONDS);
            
            }

        }

    }

    //===
    // INIT
    //===
    updateCountdown();
    // Refresh every second
    setInterval(updateCountdown, MILLISECONDS_OF_A_SECOND);
    });
    </script>



<?php 
}
?>


<div name="presenta_modal" id="presenta_modal"></div>

<script type="text/javascript">
	var groups, total_groups, live_session;
	groups = <?php GetTeacherGroups($fl_usuario); ?>;
	total_groups = groups.size.total_groups;  
	if(total_groups > 0){
		for(var i=0; i<total_groups; i++){
			var group, groupPanel1, groupPanel2, groupPanel3, groupPanel4, panelContainer, panelHeader, panelBody, table, thead, tbody, tr;
			group = groups["group"+i];

			// Current Week
			groupPanel1 =
				"<h1 class='no-margin'>"+
					"<strong>You are on Week "+group.progress.current_week+":</strong>"+
					"<br>\""+group.progress.title+"\""+
				"</h1>";
			// Live session, by default it is set to unavailable
			groupPanel2 =
				"<h1 class='no-margin'><strong>Live Session Is Unavailable</strong></h1>";
			// Assignment Due
			groupPanel3 =
				"<h1 class='no-margin'>"+
					"<strong>Assignment(s) due:</strong>"+
					"<br>"+group.progress.assignment_deadline+
				"</h1>";
			// Next Week, by default it is set to unavailable
			groupPanel4 =
				"<h1 class='no-margin'><strong>Next Topic Is Unavailable</strong></h1>";
			if(group.progress.next_week > 0){
				groupPanel4 =
					"<h1 class='no-margin'>"+
						"<strong>Next Topic is Week "+group.progress.next_week+":</strong>"+
						"<br>\""+group.progress.next_title+"\"<strong> on </strong>"+
						"<br>"+group.progress.next_date+
					"</h1>";
			}
			
			// Construct table section
		  tr = "";
		  for(var j=0; j<group.group_size.total_students; j++){
		  	var student = group["student"+j];
		  	tr +=
			  	"<tr data-toggle='collapse' data-target='#demo"+i+"_"+j+"'>"+
				"<td class='text-center'><a href='javascript:void(0);'><span class='fa fa-plus-circle' style='color: #739e73 !important; font-size: 17px;'></span></button>"+ 
		        "<td>"+student.name+"</td>"+
		        "<td>"+student.submission+"</td>"+
		        "<td>"+student.previous_grade+"</td>"+
		        "<td>"+student.current_grade+"</td>"+
		        "<td>"+student.GPA+"</td>"+
		        "<td>"+student.previous_attendance+"</td>"+
		        "<td>"+student.country+"</td>"+
		      "</tr>"+
			   "<tr>"+
                  "<td class='hiddenRow' colspan='8' style='padding-top:0px !important;padding-bottom: 0px;'>"+
                      "<div class='accordian-body collapse' id='demo"+i+"_"+j+"'>"+                
                        "<table width='100%'><tbody>"+
                        "<tr style='border-bottom: solid 1px #DDDDDD;'>"+
                        "<td width='15%' style='padding:12px;'>Gender:</td>"+
                        "<td width='10%'>"+student.gender+"</td>"+
                        "<td ><a href='javascript:void(0);' onclick='PresentaModal("+student.fl_alumno+")' class='btn btn-default btn-sm'><i class='fa fa-file-text'></i> "+student.etiqueta_btn1+"</a> &nbsp; "+student.btn_rubric+"   </td>"+
                        
                        "</tr>"+
                        "<tr style='border-bottom: solid 1px #DDDDDD;'>"+
                        "<td width='15%'  style='padding:12px; margin:5px !important;'>Age:</td>"+
                        "<td>"+student.age+"</td>"+
                         "<td ><a href='javascript:void(0);' onclick='PresentaModal2("+student.fl_alumno+")' class='btn btn-default btn-sm'><i class='fa fa-folder-open'></i> "+student.etiqueta_btn2+"</a> </td>"+
                        "</tr>"+
                        "<tr>"+
                        "<td width='15%' style='padding:12px;'>Special needs:</td>"+
                        "<td>"+student.disability+"</td>"+
						"<td>"+student.ds_disability+"</td>"+
												
                        "</tr>"+
                        "</tbody></table>"+
                    "</div>"+
                "</td>"+
                "</tr>";


		  }
		  tbody =
				"<tbody>"+
					tr+
				"</tbody>";
			thead =
				"<thead>"+
			    "<tr>"+
				"<th>&nbsp;</th>"+
			      "<th>Name</th>"+
			      "<th>Assignment Submission</th>"+
			      "<th>Previous Grade</th>"+
			      "<th>Current Grade</th>"+
			      "<th>GPA To Date</th>"+
			      "<th>Previous Attendance</th>"+
			      "<th>Country</th>"+
			    "</tr>"+
		    "</thead>";
		  table =
				"<div class='table-responsive'>"+
					"<table class='table table-bordered table-hover table-striped'>"+
						thead+
						tbody+
					"</table>"+
				"</div>";
		
			// Construct dashboard panels, piecing everything together
			panelHeader = 
				"<div class='panel-heading'>"+group.program+" Group: "+group.group_name+"</div>";
			panelBody = 
				"<div class='panel-body'>"+
			    "<div class='row'>"+
						"<div class='col-sm-6 col-md-3 col-lg-3'>"+
							"<div class='well ht-150 padding-10'>"+groupPanel1+"</div>"+
						"</div>"+
						"<div class='col-sm-6 col-md-3 col-lg-3'>"+
							"<div data-countdown data-group='"+i+"' class='well ht-150 padding-10'>"+groupPanel2+"</div>"+
						"</div>"+
						"<div class='col-sm-6 col-md-3 col-lg-3'>"+
							"<div class='well ht-150 padding-10'>"+groupPanel3+"</div>"+
						"</div>"+
						"<div class='col-sm-6 col-md-3 col-lg-3'>"+
							"<div class='well ht-150 padding-10'>"+groupPanel4+"</div>"+
						"</div>"+
					"</div>"+
			  "</div>";
			panelContainer = 
				"<div class='panel panel-primary'>"+
					panelHeader+
					panelBody+
					table+
				"</div>";
			$("#content").append(panelContainer);
		}
	} else {
        <?php if(empty($contador_global_class)){ ?>
		$("#content").prepend(
			'<div class="jumbotron text-center" style="background-color:#FDFDFD; border:1px solid #E3E3E3">'+
			  '<h1>You have no groups!</h1>'+
			'</div>'
		);
        <?php } ?>
	}
	

    //Presenta Modal para Form Aplication
	function PresentaModal(fl_alumno){
	    $.ajax({
	        type: 'POST',
	        url: 'ajax/presenta_modal.php',
	        data: 'fl_alumno='+fl_alumno,                                 
	        async: false,
	        success: function (html) {
	            $('#presenta_modal').html(html);

	        }
	    });
	
	}
    //PresentaModal para Califiaciones
	function PresentaModal2(fl_alumno){
	    $.ajax({
	        type: 'POST',
	        url: 'ajax/presenta_modal.php',
	        data: 'fl_alumno='+fl_alumno+
                  '&fg_calificacion=1',
                  
	        async: false,
	        success: function (html) {
	            $('#presenta_modal').html(html);

	        }
	    });
	
	}
   //PresentaModal para Califiaciones
	function PresentaRubricEvaluation(fl_alumno,fl_programa){
	    $.ajax({
	        type: 'POST',
	        url: 'ajax/presenta_rubric_evaluacion.php',
	        data: 'fl_alumno='+fl_alumno+
			      '&fl_programa='+fl_programa+
                  '&fg_calificado=1',
                  
	        async: false,
	        success: function (html) {
	            $('#presenta_modal').html(html);

	        }
	    });
	
	}


	$(document).ready(function(){

	   


		// Initiate all live session panels
		$('[data-countdown]').each(function() {
			var groupNum, progress, liveSessionPanel, liveSessionExists, liveSessionLink, liveSessionStart, liveSessionClose, liveSessionReadable;
		  liveSessionPanel = $(this);
		  groupNum = liveSessionPanel.data('group');
		  progress = groups["group"+groupNum].progress;

			liveSessionExists = progress.live_session_exists;
			liveSessionLink = progress.live_session_link;
			liveSessionStart = progress.live_session_start.milliseconds;
			liveSessionClose = progress.live_session_close.milliseconds;
			liveSessionReadable = progress.live_session_readable;

		  // If there is a live class
		  if(liveSessionExists){
		  	// Setup panel
				liveSessionPanel.html(
					'<h1 class="no-margin"></h1>'+
					'<a role="button" class="btn btn-sm btn-default pull-right disabled" style="position: absolute; right: 10px; bottom: 10px;">Join Class</a>'
				);

				var defaultOptions, startOptions, closeOptions;
				defaultOptions = {
					labels: ['years', 'months', 'weeks', 'days', 'gours', 'minutes', 'seconds'], 
		    	labels1: ['year', 'month', 'week', 'day', 'hour', 'minute', 'second'],
		    	format: 'DHMS',
		    	alwaysExpire: true,
		    	serverSync: function() {
						$.ajax({
							url: 'ajax/sync_server_time.php'
						}).done(function(result){
							result = JSON.parse(result);
							return new Date(result.timestamp.milliseconds) || new Date();
						}).fail(function(){
							return new Date();
						});
					}
				};
				startOptions = $.extend({}, defaultOptions, {
					until: new Date(liveSessionStart),
					//until: new Date('2015-01-13 14:56:00'),
					layout: '<strong>Next Live Class:</strong><br>'+
									liveSessionReadable+'<br>'+
									'<strong>in</strong> {dn} {dl} {hn}{sep}{mnn}{sep}{snn}',
					alwaysExpire: true,
					onExpiry: function(){
						$(this).countdown('destroy');
						liveSessionPanel.html(
							'<h1 class="no-margin">'+
								'<strong>You have a Live Class Right Now!</strong>'+
							'</h1>'+
							'<a role="button" class="btn btn-sm btn-primary pull-right" '+liveSessionLink+' style="position: absolute; right: 10px; bottom: 10px;" target="_blank">Join Class</a>'+
							'<h4></h4>'
						);
						// Start another countdown before closing the session
						liveSessionPanel.find('h4').countdown(closeOptions);
					}
				});
				closeOptions = $.extend({}, defaultOptions, {
					until: new Date(liveSessionClose),
					//until: new Date('2015-01-13 14:57:00'),
					layout: 'Class link closes in {hn}{sep}{mnn}{sep}{snn}',
					alwaysExpire: true,
					onExpiry: function(){
						$(this).countdown('destroy');
						liveSessionPanel.html(
							"<h1 class='no-margin'>"+
		  					"<strong>Live Class link is Closed!</strong><br>"+
		  					"<h4>Please update the page by pressing F5 or <a style='font-weight:500;' onclick='window.location.reload();'>here</a></h4>"+
		  				"</h1>"+
		  				"<a role='button' class='btn btn-sm btn-default pull-right disabled' style='position: absolute; right: 10px; bottom: 10px;'>Join Class</a>"
						);
					}
				});

				// Start the countdown
				liveSessionPanel.find('h1').countdown(startOptions);
		  }
		});
	});


	
</script>



