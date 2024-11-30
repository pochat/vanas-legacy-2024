<?php
  
  # Solo se permite ver la leccion actual y dos hacia atras
  if($nb_tab == 'lecture') {
    $dif = $semana_act-$no_semana;
    if(!empty($ds_vl_ruta)) {
      $explosion = explode('.',$ds_vl_ruta);
      $name_video = array_shift($explosion);
      $result["video_name"] = $name_video;
      # si coiparon algun video de otra leccion
      if(!empty($fl_leccion_copy) AND !empty($ds_vl_ruta_copy)){
        $fl_leccion_sp = $fl_leccion_copy;
        $name_video = $ds_vl_ruta_copy;
      }
      $ruta_ini= ObtenConfiguracion(116)."/vanas_videos/fame/lessons/video_".$fl_leccion_sp;
      // $ruta_videos_m3u8_hd = $ruta_ini."/video_".$fl_leccion_sp."_sd/".$name_video."_sd.mp4";
      $ruta_videos_m3u8 = $ruta_ini."/video_".$fl_leccion_sp."_sd/".$name_video."_sd.m3u8";
      // $result["sources_fame_m3u8_hd"] = $ruta_videos_m3u8_hd;
      $result["sources_fame_m3u8"] = $ruta_videos_m3u8;
	  
	  $ruta_img_thumbs=$ruta_ini."/video_".$fl_leccion_sp."_sd/";
	  
      # Subtitles
      # Obtenemos los idiomas del video solo muestra los activos
      $Query  = "SELECT a.fl_idioma, ds_language, nb_archivo, b.nb_idioma, ds_code FROM k_idioma_video a, c_idioma b ";     
      $Query .= "WHERE a.fl_idioma = b.fl_idioma AND  fl_leccion_sp=".$fl_leccion_sp." AND a.fg_activo='1' ";
      $rs = EjecutaQuery($Query);
      $tot_subtitles = CuentaRegistros($rs);
      $subtitles = "";
      $result['track'] = array();
      for($i=1;$rowl=RecuperaRegistro($rs);$i++){
        $fl_idioma = $rowl[0];
        $ds_language = str_texto($rowl[1]);
        $nb_archivo = $rowl[2];
        $nb_idioma = str_texto($rowl[3]);
        $idioma = $rowl[4];
        # Valores del idioma
        $result['track'] += array(
        "srclang".$i => $idioma,
        "label".$i => $nb_idioma,
        "vtt".$i => $ruta_ini.'/'.$nb_archivo
        );
      }
      $result['tot_subtitles'] = $tot_subtitles;
      $result['fl_usuario_fame'] = $fl_usuario;
    } 
    else {
      $result["message"] = "The video lecture for this lesson is not available";
    }
  }

  # Default video variables
  $ds_matricula = "<div>".ObtenNombreUsuario($fl_usuario)."</div>".ObtenMatriculaAlumno($fl_usuario);
  $result["rtmp"] = "rtmp://".ObtenConfiguracion(116)."/oflaDemo";
  $result["rtmp_plugin"] = SP_FLASH_FAME."/flowplayer.swf";
  // $result["player"] = SP_FLASH_FAME."/flowplayer.commercial-3.2.18.swf";
  $result["player_img"] = SP_IMAGES."/PosterFrame_PlayIcon.jpg"; 
  $result["watermark"] = $ds_matricula;
  $result["st_id"] = $ds_matricula;
  $result["ruta_img_thumbs"]=$ruta_img_thumbs;
  
  # Presenta la descripcion de la leccion, solo si es la actual y dos atras
  $lesson = array(
    "title" => $ds_titulo,
    "instructions" => html_entity_decode($ds_leccion)
  );
  $result["lesson"] = (Object) $lesson;
  
  
  # Presenta el boton para marcar como completado la session
  if($preview==0)
    $result['btn_complete'] = btn_complete_desktop($fl_usuario, $fl_leccion_sp);
  else
    $result['btn_complete'] = "";
  
  # key to flowplayer
  $result['key_flowplayer'] = ObtenConfiguracion(110);

  echo json_encode((Object) $result);
?>