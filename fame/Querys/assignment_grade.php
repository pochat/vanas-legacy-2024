<?php   
	# Libreria de funciones	
	require("../lib/self_general.php");
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  $fl_usuario = ValidaSesion(False,0, True);

  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermisoSelf(FUNC_SELF)) {  
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Obtenemo el instituto
  $fl_instituto = ObtenInstituto($fl_usuario);
  $fl_perfil_sp = ObtenPerfilUsuario($fl_usuario);
  
  
  # Recupermaos alumnos que podria asignar calificacion
  $Query .="( ";
  $Query .= "SELECT DISTINCT c.no_semana, c.ds_titulo".$sufix.", c.fg_animacion, c.fg_ref_animacion, c.no_sketch, c.fg_ref_sketch, ";
  $Query .= "e.nb_programa".$sufix.", e.ds_tipo ds_tipo_programa, c.fl_leccion_sp, a.fl_usuario_sp, f.nb_grupo, a.fl_programa_sp, a.fl_maestro,'0'fg_increase_grade ";
  $Query .= "FROM k_usuario_programa a, k_details_usu_pro b, c_leccion_sp c, c_programa_sp e, c_alumno_sp f, k_entrega_semanal_sp d, k_entregable_sp k ";
  $Query .= "WHERE a.fl_maestro=$fl_usuario AND b.fl_usu_pro= a.fl_usu_pro AND a.fl_programa_sp=c.fl_programa_sp AND a.fl_programa_sp=e.fl_programa_sp ";
  $Query .= "AND a.fl_usuario_sp=f.fl_alumno_sp ";
  $Query .= "AND d.fl_alumno=a.fl_usuario_sp ";
  $Query .= "AND d.fl_promedio_semana IS NULL AND d.fl_leccion_sp=c.fl_leccion_sp AND k.fl_entrega_semanal_sp=d.fl_entrega_semanal_sp ";
  $Query .= "AND (c.fg_animacion='1' OR c.fg_ref_animacion='1' OR c.no_sketch > 0 OR c.fg_ref_sketch='1') AND b.fg_grade_tea='1' ";
  $Query .= "AND (SELECT fl_usuario FROM c_usuario z WHERE z.fl_usuario= a.fl_usuario_sp AND z.fg_activo='1' ) ";
  $Query .= "ORDER BY a.fl_usuario_sp  ";

  $Query .=")UNION( ";
  #Este trae todos los que ya fueron calificado pero aun siguen estando disponible para subir tareas.
  $Query .= "SELECT DISTINCT c.no_semana, c.ds_titulo".$sufix.", c.fg_animacion, c.fg_ref_animacion, c.no_sketch, c.fg_ref_sketch, ";
  $Query .= "e.nb_programa".$sufix.", e.ds_tipo ds_tipo_programa, c.fl_leccion_sp, a.fl_usuario_sp, f.nb_grupo, a.fl_programa_sp, a.fl_maestro,'1'fg_increase_grade ";
  $Query .= "FROM k_usuario_programa a, k_details_usu_pro b, c_leccion_sp c, c_programa_sp e, c_alumno_sp f, k_entrega_semanal_sp d, k_entregable_sp k,c_usuario u ";
  $Query .= "WHERE a.fl_maestro=$fl_usuario AND b.fl_usu_pro= a.fl_usu_pro AND a.fl_programa_sp=c.fl_programa_sp AND a.fl_programa_sp=e.fl_programa_sp ";
  $Query .= "AND a.fl_usuario_sp=f.fl_alumno_sp ";
  $Query .= "AND d.fl_alumno=a.fl_usuario_sp AND u.fl_usuario=f.fl_alumno_sp ";
  $Query .= "AND d.fl_promedio_semana IS NOT NULL AND d.fg_increase_grade='1'  AND d.fl_leccion_sp=c.fl_leccion_sp AND k.fl_entrega_semanal_sp=d.fl_entrega_semanal_sp ";
  $Query .= "AND (c.fg_animacion='1' OR c.fg_ref_animacion='1' OR c.no_sketch > 0 OR c.fg_ref_sketch='1') AND b.fg_grade_tea='1' AND fg_activo='1'  ";
  $Query .= "ORDER BY a.fl_usuario_sp ";
  $Query .=") ";
  $rs = EjecutaQuery($Query); 
  $registros = CuentaRegistros($rs);
 
  
?>
{
  "data": [

  <?php
  for($i=1;$row=RecuperaRegistro($rs);$i++){
      
     
      $no_semana = $row[0];
      $ds_titulo = $row[1];
      //$ds_titulo = htmlentities($row[1], ENT_QUOTES, "UTF-8");
      $fg_animacion = $row[2];
      $fg_ref_animacion = $row[3];
      $no_sketch = $row[4];
      $fg_ref_sketch = $row[5];
      $nb_programa = str_texto($row[6]);
      //$nb_programa = htmlentities($row[5], ENT_QUOTES, "UTF-8");
      $ds_tipo_programa = $row[7];
      $fl_leccion_sp = $row[8];
      $fl_alumno = $row[9];
      $nb_grupo = $row[10];
      $fl_programa_sp = $row[11];
      $fl_maestro = $row[12];
      $fg_increase_grade=$row[13];

      if($fg_increase_grade==1){
          $re_submisions="<br><span class='label label-danger'><i class='fa fa-file-text-o' aria-hidden='true'></i> ".ObtenEtiqueta(2640)."</span>";
      }else{
          $re_submisions="";
      }




      # Requerimientos de la leccion
      $ds_animacion = ObtenEtiqueta(1950);
      if($fg_animacion == '1')
          $ds_animacion = ObtenEtiqueta(1951);
      $ds_ref_animacion = ObtenEtiqueta(1952);
      if($fg_ref_animacion == '1')
          $ds_ref_animacion = ObtenEtiqueta(1953);
      if($no_sketch == '0')
          $ds_sketch = ObtenEtiqueta(1954);
      elseif($no_sketch == '1')
          $ds_sketch = ObtenEtiqueta(1955);
      else
          $ds_sketch = "$no_sketch ".ObtenEtiqueta(1956);
      $ds_ref_sketch = ObtenEtiqueta(1957);
      if($fg_ref_sketch == '1')
          $ds_ref_sketch = ObtenEtiqueta(1958);

      $concat = array('b.ds_nombres', "' '", 'b.ds_apaterno');
      $Query2  = "SELECT a.fl_entrega_semanal_sp, a.fl_alumno, a.fg_entregado, a.fl_promedio_semana, ";
      $Query2 .= ConcatenaBD($concat)." 'ds_nombre' ";
      $Query2 .= "FROM k_entrega_semanal_sp a, c_usuario b ";
      $Query2 .= "WHERE a.fl_alumno=b.fl_usuario ";
      $Query2 .= "AND a.fl_leccion_sp=".$fl_leccion_sp." AND a.fl_alumno=".$fl_alumno." ";
      if(empty($fg_increase_grade))
      $Query2 .= "AND a.fl_promedio_semana IS NULL ";
      $Query2 .= "AND EXISTS(SELECT 1 FROM k_entregable_sp c WHERE c.fl_entrega_semanal_sp=a.fl_entrega_semanal_sp) ";
      $Query2 .= "ORDER BY ds_nombres ";
      $rs2 = EjecutaQuery($Query2); 
      $registros2 = CuentaRegistros($rs2);

      #Variabe initialization to avoid error
      $contador=0;
      
      for($m=1;$row2=RecuperaRegistro($rs2);$m++){
      
          $contador++;

		  $fl_entrega_semanal_sp = $row2[0];
		  $fl_alumno = $row2[1];
		  $fg_entregado = $row2[2];
		  $fl_promedio_semana = $row2[3];
		  if(empty($fl_promedio_semana))
			  $fl_promedio_semana = 0;
		  $ds_nombre = str_uso_normal($row2[4]);
		  if($fg_entregado == '1')
			  $ds_status = '<span class=\'label label-success\'><i class=\'fa fa-thumbs-up\'></i> '.ObtenEtiqueta(1972).' </span>';
		  else
			  $ds_status = '<span class=\'label label-danger\'><i class=\'fa fa-exclamation-triangle\'></i>  '.ObtenEtiqueta(1973).'</span>';

		 $Query3="SELECT ds_ruta_avatar FROM c_alumno_sp WHERE fl_alumno_sp=$fl_alumno ";
		 $row3=RecuperaValor($Query3);
		 $ds_ruta_avatar=str_texto($row3['ds_ruta_avatar']);

		 if(!empty($ds_ruta_avatar))
			$ruta_avatar = PATH_SELF_UPLOADS."/".$fl_instituto."/".CARPETA_USER.$fl_alumno."/".$ds_ruta_avatar;
		 else
		 $ruta_avatar = SP_IMAGES.'/avatar_default.jpg';

		# Obtenemos cuanto fg_animation ha subido
		$rwe1 = RecuperaValor("SELECT COUNT(*) FROM k_entregable_sp WHERE fl_entrega_semanal_sp=$fl_entrega_semanal_sp AND fg_tipo='A'");
		$animation_tot = $rwe1[0];
		# Obtenemos cuanto fg_ref_animation ha subido
		$rwe2 = RecuperaValor("SELECT COUNT(*) FROM k_entregable_sp WHERE fl_entrega_semanal_sp=$fl_entrega_semanal_sp AND fg_tipo='AR'");
		$ref_animation_tot = $rwe2[0];
		# Obtenemos cuanto sketch ha subido
		$rwe3 = RecuperaValor("SELECT COUNT(*) FROM k_entregable_sp WHERE fl_entrega_semanal_sp=$fl_entrega_semanal_sp AND fg_tipo='S'");
		$sketch_tot = $rwe3[0];
		# Obtenemos cuanto sketch ha subido
		$rwe4 = RecuperaValor("SELECT COUNT(*) FROM k_entregable_sp WHERE fl_entrega_semanal_sp=$fl_entrega_semanal_sp AND fg_tipo='SR'");
		$ref_sketch_tot = $rwe4[0];  

	    # Recupera los entregables del alumno
		$Query  = "SELECT fl_entregable_sp, fg_tipo, no_orden, ds_comentario, DATE_FORMAT(fe_entregado, '%Y-%m-%d %H:%i:%s') ";
		$Query .= "FROM k_entregable_sp ";
		$Query .= "WHERE fl_entrega_semanal_sp=$fl_entrega_semanal_sp ";
		$Query .= "GROUP BY fg_tipo ORDER BY fg_tipo, no_orden";
		$rs3 = EjecutaQuery($Query);
		$tot_entregables = CuentaRegistros($rs3);
		$animation = "<strong class='txt-color-red'><i class='fa fa-times'></i> ".ObtenEtiqueta(1968)."  ".ObtenEtiqueta(1993)." ($animation_tot/1) <br></strong>";    
		$ref_animation = "<strong class='txt-color-red'><i class='fa fa-times'></i> ".ObtenEtiqueta(1969)." ".ObtenEtiqueta(1993)." ($ref_animation_tot/1)<br></strong>";    
		$sketchs = "<strong class='txt-color-red'><i class='fa fa-times'></i> ".ObtenEtiqueta(1970)." ".ObtenEtiqueta(1993)."  ($sketch_tot/$no_sketch)<br></strong>";    
		$ref_sketchs = "<strong class='txt-color-red'><i class='fa fa-times'></i> ".ObtenEtiqueta(1970)." ".ObtenEtiqueta(1993)." ($ref_sketch_tot/1)<br></strong>";    
		while($row3 = RecuperaRegistro($rs3)) {
                   $fl_entregable = $row3[0];
				   $fg_tipo = $row3[1];
				   $no_orden = $row3[2];
				   $ds_comentario = str_uso_normal($row3[3]);
				   $fe_entregado= time_elapsed_string($row3[4]);
				   $fe_entrega= substr($row3[4], 0, 10);
				   $fe_calificado= GeneraFormatoFecha($row3[4]);

				  //$ds_comentario="fuaaa";
                   if(!empty($ds_comentario))
				      $ds_coment='<a style=\'cursor:pointer;\'  Onclick=\'ViewComent('.$fl_entregable.')\'>  '.ObtenEtiqueta(1794).'</a> ';
					  else
					  $ds_coment='&nbsp;';
 
				   $ds_orden = "";
				   switch($fg_tipo) {
				           case 'A':  
						     $ds_tipo = ObtenEtiqueta(1968);  
						     $nb_tab = 'assignment'; 
						     $entregar = $animation_tot/1;
							 $animation = '<a class=\'txt-color-green\' href=\'index.php#site/desktop.php?student='.$fl_alumno.'&week='.$no_semana.'&tab='.$nb_tab.'&fl_programa='.$fl_programa_sp.'&t=1\'><i class=\'fa fa-check\'></i> <strong>'.$ds_tipo.''.$ds_orden.'</strong> ('.$entregar.')<br><b>'.ObtenEtiqueta(1677).':</b> '.$fe_entregado.'<br><small class=\'text-muted\'>'.$fe_calificado.'</small></a><br>'.$ds_coment.'';
							 $asignaciones="<small><b>".ObtenEtiqueta(2366)." $animation_tot/1 </b></small>";
						   break;
						   case 'AR': 
						     $ds_tipo = ObtenEtiqueta(1969);     
						     $nb_tab = 'assignment_ref';  
						     $entregar = $ref_animation_tot/1;
							 $ref_animation = '<a class=\'txt-color-green\' href=\'#site/desktop.php?student='.$fl_alumno.'&week='.$no_semana.'&tab='.$nb_tab.'&fl_programa='.$fl_programa_sp.'&t=1\'><i class=\'fa fa-check\'></i> <strong>'.$ds_tipo.''.$ds_orden.'</strong> ('.$entregar.')<br><b>'.ObtenEtiqueta(1677).':</b> '.$fe_entregado.'<br><small class=\'text-muted\'>'.$fe_calificado.'</small></a><br>'.$ds_coment.'';
							 $asignaciones="<small><b>".ObtenEtiqueta(2366)." $ref_animation_tot/1 </b></small>";
						   break;
						   case 'S':  
						   $ds_tipo = ObtenEtiqueta(1970);     
						   $nb_tab = 'sketch'; 
						   $entregar = $sketch_tot / $no_sketch;
						   $ds_orden = $no_orden;
						   if($no_sketch == $sketch_tot)
							 $sketchs = '<a class=\'txt-color-green\' href=\'#site/desktop.php?student='.$fl_alumno.'&week='.$no_semana.'&tab='.$nb_tab.'&fl_programa='.$fl_programa_sp.'&t=1\'><i class=\'fa fa-check\'></i> <strong>'.$ds_tipo.'</strong> ('.$entregar.')<br><b>'.ObtenEtiqueta(1677).':</b> '.$fe_entregado.'<br><small class=\'text-muted\'>'.$fe_calificado.'</small></a><br>'.$ds_coment.'';
						   $asignaciones= "<small><b>".ObtenEtiqueta(2366)." $sketch_tot/$no_sketch </b></small>";
						   break;
						  case 'SR': 
						   $ds_tipo = ObtenEtiqueta(1971);
						   $nb_tab = 'sketch_ref';
						   $entregar = $ref_sketch_tot/1;
						   $ref_sketchs = '<a class=\'txt-color-green\'  href=\'#site/desktop.php?student='.$fl_alumno.'&week='.$no_semana.'&tab='.$nb_tab.'&fl_programa='.$fl_programa_sp.'&t=1\'><i class=\'fa fa-check\'></i> <strong>'.$ds_tipo.''.$ds_orden.'</strong> ('.$entregar.')<br><b>'.ObtenEtiqueta(1677).':</b> '.$fe_entregado.'<br><small class=\'text-muted\'>'.$fe_calificado.'</small></a><br>'.$ds_coment.'';
						   $asignaciones= "<small><b>".ObtenEtiqueta(2366)." $ref_sketch_tot/1 </b></small>";
						 break;

				   }
			}	

					 if($fg_animacion=="1")
					   $animation=$animation;
					 else 
                       $animation="";					 
					if($fg_ref_animacion=="1")
					   $ref_animation=$ref_animation;
					 else
					   $ref_animation="";
					if($no_sketch>=1)
					   $sketchs==$sketchs;
                    else
                       $sketchs="";
					   
					if($fg_ref_sketch=="1")
					  $ref_sketchs=$ref_sketchs;
					  else
					  $ref_sketchs="";
					if($tot_entregables == 0){
                         $tot_entrega=  ObtenEtiqueta(1972);
                    }

					 if(!empty($fl_promedio_semana)) {
						  $Query  = "SELECT cl_calificacion, ds_calificacion, fg_aprobado FROM c_calificacion WHERE fl_calificacion=$fl_promedio_semana";
						  $row4 = RecuperaValor($Query);
						  $cl_calificacion = $row4[0];
						  $ds_calificacion = str_uso_normal($row4[1]);

						  #Recuperamos la califiacion asignada.
						  $Queryc="SELECT no_calificacion FROM k_calificacion_teacher WHERE fl_alumno=$fl_alumno AND fl_leccion_sp=$fl_leccion_sp ";
						  $rowc=RecuperaValor($Queryc); 
						  $no_calificacion=$rowc[0];  
 
							   if($row4[2] <> "1"){
								  $ds_aprobado = '<span class=\'label label-danger\'><i class=\'fa fa-thumbs-down\'></i> '.ObtenEtiqueta(1973).'</span>';
								  $color = 'danger';
							   }
								else{
								  $ds_aprobado = '<span class=\'label label-success\'><i class=\'fa fa-thumbs-up\'></i>  '.ObtenEtiqueta(1974).'</span>';
								  $color = 'success';
							   }
								$ds_calificaciones ='<strong>'.$cl_calificacion.' ('.$no_calificacion.'%) '.$ds_calificacion.' </strong><br>'.$ds_aprobado.' <br><br>';
								$ds_calificar = ObtenEtiqueta(1975);    
						}
            else {
							    $ds_calificaciones ='<span class=\'label label-warning\'><i class=\'fa fa-exclamation-triangle\'></i>'.ObtenEtiqueta(1977).'</span><br><br>';
							    $ds_calificar = ObtenEtiqueta(1976);
							    $color = 'warning';
						}

						#Boton de calificar
	                    $btn_calificar='<a data-toggle=\'tab\' href=\'javascript:void(0);\'  OnClick=\'AsignarCalificacion('.$fl_alumno.','.$fl_leccion_sp.','.$fl_programa_sp.','.$no_semana.','.$fl_entrega_semanal_sp.', '.$fl_maestro.');window.scrollTo(0,0) \' id=\'tab5\' name=\'tab5\' class=\'btn btn-primary\'><i class=\'fa fa-pencil\'></i> '.$ds_calificar.'  </a><br />';

						/** ESTE PROCESO PARA AYUDAR A LA BUSQUEDA AVANZADA **/    
						echo '
						{
						  
						  "id": "<div class=\'project-members\' style=\'width: 80px;\'><a href=\'#site/profile.php?profile_id='.$fl_alumno.'&otro=1\'><img src=\''.$ruta_avatar.'\' class=\'online\' alt=\''.$ds_nombre.'\' style=\'width:70px;\'></a><br/>  </div> ",
						  "name": "<a href=\'#site/desktop.php?student='.$fl_alumno.'&week='.$no_semana.'&tab='.$nb_tab.'&fl_programa='.$fl_programa_sp.'&t=1\' rel=\'tooltip\' data-placement=\'top\' data-html=\'true\' data-original-title=\'View '.$ds_nombre.' desktop\'>'.$ds_nombre.'</a><br><br>'.$ds_status.'",
						  "nada": "<span hidden>'.$fe_entrega.'</span>&nbsp;",
						  "myself": "<b>'.ObtenEtiqueta(1964).'</b> '.$nb_programa.'<br/><b>'.ObtenEtiqueta(1965).'</b> '.$nb_grupo.' <br/><b>'.ObtenEtiqueta(1966).'</b> '.$ds_titulo.' <br/><b>'.ObtenEtiqueta(1967).'</b> '.$no_semana.'",
						  "trabajos": "'.$animation.' '.$ref_animation.' '.$sketchs.' '.$ref_sketchs.' <br/>'.$asignaciones.' <br>'.$re_submisions.' ",
						  "asigment_grade": " '.$ds_calificaciones.' '.$btn_calificar.' "
						}';
           if($i<=($registros-1))
            echo ",";
          else
            echo "";			
		}
  }

  /*echo '
			{
			 
			  "id": "<div class=\'project-members\'> </div> ",
			  "name": "<a href=\'rr\' rel=\'tooltip\' data-placement=\'top\' data-html=\'true\' data-original-title=\'View rr desktop\'></a>",
			  "nada": "&nbsp;",
			  "myself": "&nbsp;",
			  "trabajos": "<div>  </div>",
			  "asigment_grade": "<div> </div>"
			}';*/
  
  
  
  ?>
  
  ]
}