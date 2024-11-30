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
  # Query que obtiene los usuarios dependiedo de la intitucion
  # Adm Muestra teacher y students
  # Teacher Muestra los students
 
  
  

  # Consulta para el listado
  $Query  = "SELECT a.fl_programa_sp, a.nb_programa '".ObtenEtiqueta(360)."', a.ds_duracion '".ObtenEtiqueta(361)."', ";
  $Query .= "a.ds_tipo '".ObtenEtiqueta(362)."', a.no_grados '".ObtenEtiqueta(365)."|right', ";
  $Query .= "a.no_orden '".ObtenEtiqueta(48)."|right', a.no_creditos, ";
  $Query .= "CASE WHEN a.fg_fulltime=1 THEN 'Full Time' ELSE 'Part Time' END schedule, ";
  $Query .= "(SELECT SUM(b.no_valor_quiz) FROM c_leccion_sp b WHERE b.fl_programa_sp = a.fl_programa_sp ) as valor_tot_quiz, ";
  $Query .= "(SELECT c.no_workload FROM k_programa_detalle_sp c WHERE c.fl_programa_sp = a.fl_programa_sp) AS workload, ";
  $Query .= "(SELECT COUNT(1) FROM c_leccion_sp z WHERE z.fl_programa_sp = a.fl_programa_sp) AS cont_lecciones, ";
  $Query .= "CASE WHEN (SELECT COUNT(1) FROM c_leccion_sp z WHERE z.fl_programa_sp = a.fl_programa_sp) = 0 
	THEN '".ObtenEtiqueta(1354)."' 
	ELSE CONCAT((SELECT COUNT(1) FROM c_leccion_sp z WHERE z.fl_programa_sp = a.fl_programa_sp), ' ".ObtenEtiqueta(1355)."')
	END 'cont_lecciones_2',fg_publico,a.fg_compartir_curso,a.fl_usuario_creacion ";
  $Query .= "FROM c_programa_sp a WHERE a.fl_instituto=$fl_instituto ";
  //los teacher estan filtrados por sus cursos que crearon.
  if($fl_perfil_sp==PFL_MAESTRO_SELF)
  $Query .="AND a.fl_usuario_creacion=$fl_usuario ";
  $Query .= "ORDER BY a.fl_programa_sp DESC ";
  // echo $Query;

  $rs = EjecutaQuery($Query);
  $registros = CuentaRegistros($rs);
?>
{
  "data": [
  <?php
  for($i=1;$row=RecuperaRegistro($rs);$i++){
   

      $fl_programa=$row[0];
      $fg_publico=$row['fg_publico'];
      $Query  = "SELECT no_horas, no_semanas ";
      $Query .= "FROM k_programa_detalle_sp ";
      $Query .= "WHERE fl_programa_sp = $row[0] ";
      $row1 = RecuperaValor($Query);
      
      $row1[0]==1 ?   $hora = ObtenEtiqueta(1232) : $hora = ObtenEtiqueta(1233);
      $row1[1]==1 ? $sesion = ObtenEtiqueta(1230) : $sesion = ObtenEtiqueta(1231);
      
      $fg_global_compartir_curso=$row['fg_compartir_curso'];

      //Recuperamos quien lo creo.
      $fl_usuario_creacion=$row['fl_usuario_creacion'];
      $nb_creador_curso=ObtenNombreUsuario($fl_usuario_creacion);

      $creador_curso="".ObtenEtiqueta(2626).": $nb_creador_curso";



      #Variable initial
      $etq_global_compartir_curso="";

      if($row[8] < 100) {
          $color = "warning";
          $etq = $row[8]." %";
      }else{
          $color = "success";
          $etq = $row[8]." %";
      }
      
      if(empty($row[8])){
          $color = "";
          $etq = "";
      }        
      
      $Query_t = "SELECT ds_titulo, ds_vl_duracion, ds_tiempo_tarea, no_valor_quiz FROM c_leccion_sp WHERE fl_programa_sp = $row[0]";
      $rs_t = EjecutaQuery($Query_t);
      $arma = "";
      for($i_t=1;$row_t=RecuperaRegistro($rs_t);$i_t++) {
          $arma .= "<tr><td width='5%'></td><td width='5%'>$i_t</td><td width='50%'>$row_t[0]</td><td width='10%'>$row_t[1]</td><td width='15%'>$row_t[2]</td><td width='15%'>$row_t[3] %</td width='5%'><td></td></tr>";
      }
      
      if(empty($row[10])){
          $color_10 = "danger";
          $etq_10 = $row[11];
      }else{
          $color_10 = "success";
          $etq_10 = $row[11];
      }
      
      if($fg_publico==1){
          $publish="<span class='label label-success'>Yes</span> ";

          if($fg_global_compartir_curso==1){
              $etq_global_compartir_curso="<small class='text-muted'>".ObtenEtiqueta(2638)."</small>";
          }else{
              $etq_global_compartir_curso="<small class='text-muted'>".ObtenEtiqueta(2639)."</small>";

          }

      }else{
          $publish="<span class='label label-danger'>No</span>";
      }
      
      



   
 
 

	//"checkbox": "<label class=\'checkbox no-padding no-margin\'><input class=\'checkbox\' type=\'checkbox\' id=\'ch_'.$i.'\' value=\''.$fl_usuario.'\'><span></span></label><input type=\'hidden\' id=\'use_lic'.$i.'\' name=\'use_lic'.$i.'\' value=\'1\'>",
      
    /** ESTE PROCESO PARA AYUDAR A LA BUSQUEDA AVANZADA **/    
    echo '
    {
      "nb_programa": "<div class=\'project-members\'><a href=\'index.php#site/mycourses_details.php?clave='.$fl_programa.'\' >'.str_texto(isset($row[1])?$row[1]:NULL).'<br><small class=\'text-muted\'><i>'.str_texto(isset($row[3])?$row[3]:NULL).'&nbsp;'.str_texto(isset($row1[2])?$row1[2]:NULL).'</i></small><br><small class=\'text-muted\'>'.$creador_curso.' </small></div> ",
      "published": "'.$publish.' <br>'.$etq_global_compartir_curso.' ",
	  "credits": "<a href=\'index.php#site/mycourses_details.php?clave='.$fl_programa.'\'>'.str_texto($row[6]).'</a>",
      "gender": "'.$arma.'",
	  "duration": "<a href=\'index.php#site/mycourses_details.php?clave='.$fl_programa.'\'>'.$row1[0].' '.$hora.'<br><small class=\'text-muted\'><i>'.str_texto($row1[1]).' '.$sesion.'</i></small></a>",
      "lesson": "<td><span class=\'label label-'.$color_10.'\'>'.$etq_10.'</span> ",
      "assignment_workload": "<a href=\'index.php#site/mycourses_details.php?clave='.$fl_programa.'\'>'.($row[9]).'</a> ",
      "quiz_percentage": "<td><span class=\'label label-'.$color.'\'>'.$etq.'</span></td>",
      "delete": "<a href=\'javascript:Delete('.$fl_programa.');\' class=\'btn btn-xs btn-default\' style=\'margin-left:5px\'><i class=\'fa  fa-trash-o\'></i></a> "
      
    }';
    if($i<=($registros-1))
        echo ",";
      else
        echo "";
  }
  ?>
  ]
}