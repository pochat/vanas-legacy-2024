<?php
	# Default video variables
  $ds_matricula = ObtenMatriculaAlumno($fl_usuario);
  $ds_nombre_alumno=ObtenNombreUsuario($fl_usuario);
  $result["rtmp"] = "rtmp://".ObtenConfiguracion(60)."/oflaDemo";
  $result["rtmp_plugin"] = SP_FLASH."/flowplayer.rtmp-3.2.13.swf";
  $result["player"] = SP_FLASH."/flowplayer.commercial-3.2.18.swf";
  $result["player_img"] = SP_IMAGES."/PosterFrame_PlayIcon.jpg";  
  $result["st_id"] = $ds_matricula;
  // $fl_leccion=6;
  // $ds_vl_ruta = "CA_T1_week06_VID_web.m3u8";
  
  $campus_url = ObtenConfiguracion(121);
  $key_campus = ObtenConfiguracion(120);
  # Se da la prioridad al profesor de ver todos los videos de las semanas
  if($nb_tab == 'lecture') {
    $dif = $semana_act-$no_semana;
    if(!empty($ds_vl_ruta)) {
      if(VIDEOS_FLASH==true){
        $result["video_name"] = ObtenNombreArchivo($ds_vl_ruta);
        $result["key_flowplayer"] = $key_campus;
        //$result["watermark"] = PresentWatermark($ds_matricula);
		$result["watermark"] = $ds_nombre_alumno;
        $result["scripts"] = '
        <!-- Flowplayer flash -->		
        <script src="'.PATH_N_COM_JS.'/plugin/flowplayer_flash/flowplayer-3.2.13.min.js"></script>';
      }
      else{
        // $ruta_ini= ObtenConfiguracion(116)."/vanas_videos/campus/lessons/video_".$fl_leccion;
        $name_video = array_shift(explode('.',$ds_vl_ruta));
        $ruta_ini= $campus_url."/vanas_videos/campus/lessons/video_".$fl_leccion;
        $ruta_videos_mp4 = $ruta_ini."/video_".$fl_leccion."_vl_sd/".$name_video.".mp4";
        // $ruta_videos_m3u8 = $ruta_ini."/video_".$fl_leccion."/video_".$fl_leccion."_vl_sd/".$name_video.".m3u8";
        $ruta_videos_m3u8 = $ruta_ini."/video_".$fl_leccion."_vl_hd/".$name_video.".m3u8";
        $result["sources_fame_mp4"] = $ruta_videos_mp4;
        $result["sources_fame_m3u8"] = $ruta_videos_m3u8;
        $result["video_name"] = ObtenNombreArchivo($ds_vl_ruta);
        $result["key_flowplayer"] = $key_campus;
        //$result["watermark"] = $ds_matricula;
		$result["watermark"] = $ds_nombre_alumno;
        $result["scripts"] = '
        <!-- Flowplayer library -->
        <script src="'.PATH_SELF_JS.'/flowplayer/flowplayer.min.js"></script>
        <!-- Flowplayer hlsjs engine -->
        <script src="//releases.flowplayer.org/hlsjs/flowplayer.hlsjs.min.js"></script>
        <!-- Flowplayer quality selector plugin -->
        <script src="//releases.flowplayer.org/vod-quality-selector/flowplayer.vod-quality-selector.js"></script>';
      }
    } else {
      $result["message"] = "The video lecture for this lesson is not available.";
    }
  }
  else {
    if(!empty($ds_as_ruta)) {
      if(VIDEOS_FLASH==true){
        $result["video_name"] = ObtenNombreArchivo($ds_as_ruta);
        $result["scripts"] = '
        <!-- Flowplayer flash -->		
        <script src="'.PATH_N_COM_JS.'/plugin/flowplayer_flash/flowplayer-3.2.13.min.js"></script>';
      }
      else{
        $ruta_ini= $campus_url."/vanas_videos/campus/brief/video_".$fl_leccion;
        $name_video = array_shift(explode('.',$ds_as_ruta));
        $ruta_videos_mp4 = $ruta_ini."/video_".$fl_leccion."_vb_hd/".$name_video.".mp4";
        $ruta_videos_m3u8 = $ruta_ini."/video_".$fl_leccion."_vb_hd/".$name_video.".m3u8";
        $result["sources_fame_mp4"] = $ruta_videos_mp4;
        $result["sources_fame_m3u8"] = $ruta_videos_m3u8;
        $result["video_name"] = $ds_as_ruta;
        $result["key_flowplayer"] = $key_campus;
        //$result["watermark"] = $ds_matricula;
		$result["watermark"] = $ds_nombre_alumno;
        $result["scripts"] = '
        <!-- Flowplayer library -->
        <script src="'.PATH_SELF_JS.'/flowplayer/flowplayer.min.js"></script>
        <!-- Flowplayer hlsjs engine -->
        <script src="//releases.flowplayer.org/hlsjs/flowplayer.hlsjs.min.js"></script>
        <!-- Flowplayer quality selector plugin -->
        <script src="//releases.flowplayer.org/vod-quality-selector/flowplayer.vod-quality-selector.js"></script>';
      }
    } else {
      $result["message"] = "The video brief for this lesson is not available.";
    }    
  }
  $result["videos_flash"] = VIDEOS_FLASH;
  # Presenta la descripcion de la leccion, solo si es la actual y dos atras
  $lesson = array(
    "title" => $ds_titulo,
    "instructions" => $ds_leccion
  );
  $result["lesson"] = (Object) $lesson;

  echo json_encode((Object) $result);
?>