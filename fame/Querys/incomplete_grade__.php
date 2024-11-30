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
  $Query  = "SELECT c.no_semana, c.ds_titulo".$sufix.", c.fg_animacion, c.fg_ref_animacion, c.no_sketch, c.fg_ref_sketch, ";
  $Query .= "e.nb_programa".$sufix.", e.ds_tipo ds_tipo_programa, c.fl_leccion_sp, a.fl_usuario_sp, f.nb_grupo, a.fl_programa_sp ";
  $concat = array('u.ds_nombres', "' '", 'u.ds_apaterno');
  $Query .=", ".ConcatenaBD($concat)." 'ds_nombre',g.fl_entrega_semanal, a.fl_maestro ";
  $Query .= "FROM k_usuario_programa a 
			 JOIN k_details_usu_pro b ON b.fl_usu_pro=a.fl_usu_pro
			 JOIN c_leccion_sp c ON a.fl_programa_sp = c.fl_programa_sp 
			 JOIN c_programa_sp e ON a.fl_programa_sp = e.fl_programa_sp 
			 JOIN c_alumno_sp f ON a.fl_usuario_sp = f.fl_alumno_sp 
             JOIN c_usuario u ON u.fl_usuario =f.fl_alumno_sp 
			 LEFT JOIN ( SELECT MIN(m.fl_entrega_semanal_sp)fl_entrega_semanal ,m.fl_leccion_sp, m.fl_alumno 
                         FROM   k_entrega_semanal_sp m
					     JOIN k_entregable_sp n ON  m.fl_entrega_semanal_sp = n.fl_entrega_semanal_sp 
                       ) g ON g.fl_leccion_sp = c.fl_leccion_sp AND  g.fl_alumno=a.fl_usuario_sp
			 ";
  $Query .= "WHERE a.fl_maestro=$fl_usuario ";
  $Query .= "AND (c.fg_animacion='1' OR c.fg_ref_animacion='1' OR c.no_sketch > 0 OR c.fg_ref_sketch='1') AND b.fg_grade_tea='1' ";
  $Query .= "ORDER BY a.fl_usuario_sp ";

  $rs = EjecutaQuery($Query); 
  $registros = CuentaRegistros($rs);
 
  
?>
{
  "data": [

  <?php
  for($i=1;$row=RecuperaRegistro($rs);$i++){
      
      
      $no_semana = $row[0];
      $ds_titulo = htmlentities($row[1], ENT_QUOTES, "UTF-8");
      $fg_animacion = $row[2];
      $fg_ref_animacion = $row[3];
      $no_sketch = $row[4];
      $fg_ref_sketch = $row[5];
      //$nb_programa = str_texto($row[6]);
      $nb_programa = htmlentities($row[6], ENT_QUOTES, "UTF-8");
      $ds_tipo_programa = $row[7];
      $fl_leccion_sp = $row[8];
      $fl_alumno = $row[9];
      $nb_grupo = $row[10];
      $fl_programa_sp = $row[11];
      $ds_nombre = str_uso_normal($row[12]);
      $fl_entrega_semanal =$row[13];
      if(empty($fl_entrega_semanal_sp))
        $fl_entrega_semanal_sp=0;
      $fl_maestro =$row[14];
      
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
      
 
		  $fg_entregado = '0';
		  $ds_status = '<span class=\'label label-danger\'><i class=\'fa fa-times\'></i> '.ObtenEtiqueta(1963).'</span>';
	
		 $Query3="SELECT ds_ruta_avatar FROM c_alumno_sp WHERE fl_alumno_sp=$fl_alumno ";
		 $row3=RecuperaValor($Query3);
		 $ds_ruta_avatar=str_texto($row3['ds_ruta_avatar']);

		 if(!empty($ds_ruta_avatar))
			$ruta_avatar = PATH_SELF_UPLOADS."/".$fl_instituto."/".CARPETA_USER.$fl_alumno."/".$ds_ruta_avatar;
		 else
		    $ruta_avatar = SP_IMAGES.'/avatar_default.jpg';
      
         
		 
		  
		 
		 
	   # Recupera los entregables del alumno
		$Query  = "SELECT fl_entregable_sp, fg_tipo, no_orden, ds_comentario,fe_entregado ";
		$Query .= "FROM k_entregable_sp ";
		$Query .= "WHERE fl_entrega_semanal_sp=$fl_entrega_semanal ";
		$Query .= "ORDER BY fg_tipo, no_orden";
		$rs3 = EjecutaQuery($Query);
		$tot_entregables = CuentaRegistros($rs3);
		while($row3 = RecuperaRegistro($rs3)) {
			$fl_entregable = $row3[0];
			$fg_tipo = $row3[1];
			$no_orden = $row3[2];
			$ds_comentario = str_uso_normal($row3[3]);
			$fe_entregado= time_elapsed_string($row3[4], false);
    
					//$ds_comentario="fuaaa";
			if($ds_comentario)
				$ds_coment='<a style=\'cursor:pointer;\'  Onclick=\'ViewComent('.$fl_entregable.')\'>  '.ObtenEtiqueta(1794).'</a> ';
			else
				$ds_coment='&nbsp;';
                     
			$ds_orden = "";
			switch($fg_tipo) {
				case 'A':  
					$ds_tipo = ObtenEtiqueta(1968);  
					$nb_tab = 'assignment'; 
					break;

				case 'AR': 
					$ds_tipo = ObtenEtiqueta(1969);     
					$nb_tab = 'assignment_ref';  
					break;

				case 'S':  
					$ds_tipo = ObtenEtiqueta(1970);     
					$nb_tab = 'sketch'; 
					$ds_orden =$no_orden;
					break;

				case 'SR': 
					$ds_tipo = ObtenEtiqueta(1971);
					$nb_tab = 'sketch_ref';
					break;

				default:
					break;
			   
			}
		}	
	
	
					
					if($tot_entregables == 0){
                         $tot_entrega=  '<span class=\'label label-danger\'><i class=\'fa fa-times\'></i> '.ObtenEtiqueta(1972).'</span>';
                    }
					
	
	
	
					 if(!empty($fl_promedio_semana)) {
						  $Query  = "SELECT cl_calificacion, ds_calificacion, fg_aprobado FROM c_calificacion WHERE fl_calificacion=$fl_promedio_semana";
						  $row4 = RecuperaValor($Query);
						  $cl_calificacion = $row4[0];
						  $ds_calificacion = str_uso_normal($row4[1]);


							   if($row4[2] <> "1"){
								  $ds_aprobado = '<span class=\'text_unread\'>'.ObtenEtiqueta(1973).'</span>';
								  						   }
								else{
								  $ds_aprobado = ''.ObtenEtiqueta(1974).'';
								  
							   }
								$ds_calificaciones ='<strong>'.$cl_calificacion.'  '.$ds_calificacion.' </strong><br>'.$ds_aprobado.' <br><br>';
								$ds_calificar = ObtenEtiqueta(1975);    
						}else {
							    $ds_calificaciones ='<span class=\'label label-danger\'><i class=\'fa fa-times\'></i>  '.ObtenEtiqueta(1977).'</span><br><br>';
							    $ds_calificar = ObtenEtiqueta(1976);
							  
						}
	
	
	
						#Boton de calificar
	                    $btn_calificar='<a data-toggle=\'tab\' href=\'javascript:void(0);\'  OnClick=\'AsignarCalificacion('.$fl_alumno.','.$fl_leccion_sp.','.$fl_programa_sp.','.$no_semana.','.$fl_entrega_semanal_sp.', '.$fl_maestro.' );window.scrollTo(0,0) \' id=\'tab5\' name=\'tab5\' class=\'btn btn-primary\'><i class=\'fa fa-pencil\'></i> '.$ds_calificar.'  </a><br />';
						
	
    
						/** ESTE PROCESO PARA AYUDAR A LA BUSQUEDA AVANZADA **/    
						echo '
						{
						  
						  "id": "<div class=\'project-members\'><a href=\'#site/profile.php?profile_id='.$fl_alumno.'&otro=1\'  ><img src=\''.$ruta_avatar.'\' class=\'online\' alt=\''.$ds_ruta_avatar.'\' style=\'width:70px;\'></a><br/>  </div> ",
						  "name": "<a href=\'#site/desktop.php?student='.$fl_alumno.'&week='.$no_semana.'&tab='.$nb_tab.'&fl_programa='.$fl_programa_sp.'&t=1\' rel=\'tooltip\' data-placement=\'top\' data-html=\'true\' data-original-title=\'View '.$ds_nombre.' desktop\'>'.$ds_nombre.'</a><br><br>'.$ds_status.'",
						  "nada": "&nbsp;",
						  "myself": "<b>'.ObtenEtiqueta(1964).'</b> '.$nb_programa.'<br/><b>'.ObtenEtiqueta(1965).'</b> '.$nb_grupo.' <br/><b>'.ObtenEtiqueta(1966).'</b> '.$ds_titulo.' <br/><b>'.ObtenEtiqueta(1967).'</b> '.$no_semana.'",
						  "trabajos": " '.$tot_entrega.'  <a href=\'#site/desktop.php?student='.$fl_alumno.'&week='.$no_semana.'&tab='.$nb_tab.'&fl_programa='.$fl_programa_sp.'&t=1\'>'.$ds_tipo.''.$ds_orden.'</a>&nbsp;&nbsp;'.$ds_coment.' <br>  ",
						  "asigment_grade": " '.$ds_calificaciones.' '.$btn_calificar.' "
						}';
						echo ",";
						
                       	
						
						
           
 
		}
  
	

  
 
  echo '
			{
			 
			  "id": "<div class=\'project-members\'> </div> ",
			  "name": "<a href=\'rr\' rel=\'tooltip\' data-placement=\'top\' data-html=\'true\' data-original-title=\'View rr desktop\'></a>",
			  "nada": "&nbsp;",
			  "myself": "&nbsp;",
			  "trabajos": "<div>  </div>",
			  "asigment_grade": "<div> </div>"
			}';
  
  
  
  ?>
  
  
  
  
  ]
}