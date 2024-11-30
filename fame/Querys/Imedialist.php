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



  //$_REQUEST[''];
  //parse_str($_POST['extra_filters']['advanced_search'], $advanced_search);
  //$_POST += $advanced_search;
  //$fl_programa_sp=$_POST['extra_filters']['fl_programa_sp'];
  //$cl_pagina_creada=$_POST['extra_filters']['cl_pagina_creada'];
  
  # Obtenemo el instituto
  $fl_instituto = ObtenInstituto($fl_usuario);
  $fl_perfil_sp = ObtenPerfilUsuario($fl_usuario);

  # Get the lang suffix for the locale *** Please Implement on query ***
  $sufix = langSufix();

  # Consulta para el listado
   # Consulta para el listado  
  $Query  = " SELECT fl_leccion_sp, nb_programa".$sufix.", no_semana, ds_titulo".$sufix.", ds_leccion".$sufix.", ";
  $Query .= " CASE WHEN ds_vl_ruta IS NULL THEN '".ObtenEtiqueta(17)."' WHEN ds_vl_ruta='' THEN '".ObtenEtiqueta(17)."' ELSE '".ObtenEtiqueta(16)."' END 'Video Brief', ";
  $Query .= " CASE WHEN fg_animacion='1' OR fg_ref_animacion='1' OR no_sketch>'0' OR fg_ref_sketch ='1'  THEN '".ObtenEtiqueta(16)."' ";
  $Query .= " ELSE '".ObtenEtiqueta(17)."' END 'req_anim', ";
  $Query .= " CASE WHEN (SELECT COUNT(*) FROM k_quiz_pregunta WHERE fl_leccion_sp = a.fl_leccion_sp) = 0 THEN '".ObtenEtiqueta(17)."' ELSE '".ObtenEtiqueta(16)."' END 'quiz', ";
  $Query .= " c.no_semanas, a.ds_vl_duracion, no_grado, a.no_valor_quiz, ";
  $Query .= " (SELECT SUM(xas.no_valor_quiz) FROM c_leccion_sp xas WHERE xas.fl_programa_sp = a.fl_programa_sp) AS sum_tot_quiz, ";
  $Query .= " (SELECT SUM(kcp.no_valor_rubric) FROM c_leccion_sp kcp WHERE kcp.fl_programa_sp = a.fl_programa_sp) AS sum_tot_rubric,  ";
  $Query .= " CASE WHEN (SELECT COUNT(*) FROM k_criterio_programa_fame WHERE fl_programa_sp = a.fl_leccion_sp) = 0 THEN '".ObtenEtiqueta(17)."' ELSE '".ObtenEtiqueta(16)."' END 'rubric', no_valor_rubric, ds_vl_ruta, ds_progress_video, a.ds_learning".$sufix." ,b.fl_usuario_creacion ";
  $Query .= " FROM c_leccion_sp a, c_programa_sp b, k_programa_detalle_sp c ";
  $Query .= " WHERE a.fl_programa_sp = b.fl_programa_sp  AND a.fl_programa_sp = c.fl_programa_sp  
              AND a.fl_instituto=$fl_instituto ";
  //los teacher estan filtrados por sus cursos que crearon.
  if($fl_perfil_sp==PFL_MAESTRO_SELF)
  $Query .="  AND b.fl_usuario_creacion=$fl_usuario ";
  $Query .= " ORDER BY nb_programa, no_semana ";
  $rs = EjecutaQuery($Query);
  $registros = CuentaRegistros($rs);
?>
{
  "data": [
  <?php
  for($i=1;$row=RecuperaRegistro($rs);$i++){

    $row[8]==1 ?   $sesion = ObtenEtiqueta(1230) : $sesion = ObtenEtiqueta(1231);
    
    //Recuperamos quien lo creo.
    $fl_usuario_creacion=$row['fl_usuario_creacion'];
    $nb_creador_curso=ObtenNombreUsuario($fl_usuario_creacion);

    $creador_curso="".ObtenEtiqueta(2626).": $nb_creador_curso";


      if($row[5] == ObtenEtiqueta(17)) {
        $color1 = "danger";
        $etq1=ObtenEtiqueta(17);
      }else{
        $color1 ="success";
        $etq1=ObtenEtiqueta(16);
      }
      
      if($row[6] == ObtenEtiqueta(17)) {
        $color2 = "danger";
        $etq2=ObtenEtiqueta(17);
      }else{
        $color2 ="success";
        $etq2=ObtenEtiqueta(16);
      }
      
      if($row[7] == ObtenEtiqueta(17)) {
        $color3 = "danger";
        $etq3=ObtenEtiqueta(17);
      }else{
        $color3 = "success";
        $etq3 = ObtenEtiqueta(16);
      }  

      # QUIZ
      
      if($row[12] < 100) {
        $color4 = "warning";
        $etq4 = $row[11]." %";
      }elseif($row[12] == 100){
        $color4 = "success";
        $etq4 = $row[11]." %";
      }
      
      if($row[12] > 100){
        $color4 = "danger";
        $etq4 = $row[11]." %";
      }
      
      if(empty($row[11])){
        $color4 = "";
        $etq4 = "";
      }
      
      # RUBRIC
      
      # Si la sumatoria de los valores del rubric es menor a 100%, entonces la etiqueta es amarilla
      if($row[13] < 100) {
        $color5 = "warning";
        $etq5 = $row[15]." %";
      }elseif($row[13] == 100){ # Si la sumatoria de los valores del rubric es igual a 100%, entonces la etiqueta es verde
        $color5 = "success";
        $etq5 = $row[15]." %";
      }
      
      # Si la sumatoria de los valores del rubric del curso excede el 100%, entonces la etiqueta es color rojo
      if($row[13] > 100){
        $color5 = "danger";
        $etq5 = $row[15]." %";
      }
      if(empty($row[15])){
        $color5 = "";
        $etq5 = "";
      }

      if($row[14] == ObtenEtiqueta(17)) {
        $color6 = "danger";
        $etq6=ObtenEtiqueta(17);
      }else{
        $color6 = "success";
        $etq6 = ObtenEtiqueta(16);
      }

      #Recuperamos lenguajes.
	  $rs2 = EjecutaQuery("SELECT a.fl_idioma, nb_idioma, a.fg_activo FROM k_idioma_video a, c_idioma b WHERE  a.fl_idioma = b.fl_idioma AND  fl_leccion_sp=".$row[0]);
      $tot_idiomas = CuentaRegistros($rs2);
	  $lenguajes ="<ul class='list-inline'> ";
	  for($i2=1;$rowl=RecuperaRegistro($rs2);$i2++){
	      $fl_idioma_bd = $rowl[0];
	      $ds_language = str_texto($rowl[1]);
	      $fg_activo = $rowl[2];
	      $lenguajes .="<li>".$ds_language."</li>";
	  }
	  $lenguajes .="</ul >";

      # Nombre de video
      if(!empty($row[16]) && !empty($row[9]))
        $ds_vl_ruta = "<a href='/AD3M2SRC4/modules/fame/preview_flv.php?archivo=".str_texto($row[16])."&clave=".$row[0]."' style='color:#999; font-size: 11px;' target='_blank'>".str_texto($row[16])."</a>";
      else
        $ds_vl_ruta = '';
      $vid_progres = $row[17];
      if(empty($vid_progres))
        $vid_progres = 0;
      $progreso_video = "<div class='progress progress-xs' data-progressbar-value='".$vid_progres."'><div class='progress-bar'></div></div>";

    echo '
        {
          "checkbox": "<!--<div class=\'checkbox \'><label><input class=\'checkbox\' id=\'ch_'.$i.'\' value=\''.$row[0].'\' type=\'checkbox\' /><span></span><input type=\'hidden\' value=\''.$registros.'\' id=\'tot_registros\' /> </label></div>-->",
          "course": "<a href=\'index.php#site/Imedia_frm.php?c='.$row[0].'\'>'.str_texto($row[1]).'<br><small class=\'text-muted\'><i>'.ObtenEtiqueta(1215).'&nbsp;'.str_texto($row[8]).'&nbsp;'.$sesion.'</i></small></a><br><small class=\'text-muted\'>'.$creador_curso.'</small>",
          "session": "<td><a href=\'index.php#site/Imedia_frm.php?c='.$row[0].'\'>'.$row[10].'<br><small class=\'text-muted\'><i>'.ObtenEtiqueta(1250).'&nbsp;'.str_texto($row[2]).'</i></small></a></td>",           
          "lesson": "<a href=\'index.php#site/Imedia_frm.php?c='.$row[0].'\'><td>'.str_texto($row[3]).'</a> <br/><small class=\'text-muted\'>'.$lenguajes.' </small>  </td>",          
          "duration": "<td><a href=\'index.php#site/Imedia_frm.php?c='.$row[0].'\'>'.str_texto($row[9]).'</a><br><small class=\'text-muted\'><i>'.$ds_vl_ruta.'</i></small>'.$progreso_video.'</td>", 
          "video": "<td><a href=\'index.php#site/Imedia_frm.php?c='.$row[0].'\'><span class=\'label label-'.$color1.'\'>'.$etq1.'</span></a></td>",
          "assignment": "<td><a href=\'index.php#site/Imedia_frm.php?c='.$row[0].'\'><span class=\'label label-'.$color2.'\'>'.$etq2.'</span></a></td>",
          "quiz": "<td><a href=\'index.php#site/Imedia_frm.php?c='.$row[0].'\'><span class=\'label label-'.$color3.'\'>'.$etq3.'</span></a></td>",          
          "valor": "<td><a href=\'index.php#site/Imedia_frm.php?c='.$row[0].'\'><span class=\'label label-'.$color4.'\'>'.$etq4.'</span></a></td>",          
          "rubric": "<td><a href=\'index.php#site/Imedia_frm.php?c='.$row[0].'\'><span class=\'label label-'.$color6.'\'>'.$etq6.'</span></a></td>",          
          "valor_rubric": "<td><a href=\'index.php#site/Imedia_frm.php?c='.$row[0].'\'><span class=\'label label-'.$color5.'\'>'.$etq5.'</span></a></td>",          
          "action": "<a href=\'javascript:Delete('.$row[0].');\' class=\'btn btn-xs btn-default\' style=\'margin-left:5px\'><i class=\'fa  fa-trash-o\'></i></a>",
          "outcome": "'.$row[18].'",
          "title": "'.$row[3].'"
        }';

    if($i<=($registros-1))
        echo ",";
      else
        echo "";
  }
  ?>
  ]
}
