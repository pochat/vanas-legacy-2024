<?php

  # Libreria de funciones
  require("../../lib/general.inc.php");
  
  # Recibe parametros
  $clave = $_GET['clave'];
  $archivo = $_GET['archivo'];
  $ext = ObtenExtensionArchivo($archivo);
  $explosion=explode('.',$archivo);
  $name_video = array_shift($explosion);
  $fg_tipo = $_GET['fg_tipo']??NULL;
  $fg_creado_fame=!empty($_GET['fg_creado_fame'])?$_GET['fg_creado_fame']:NULL;//Indica que fue creado por un instituto.
  
  # Formamos la url
  if(empty($fg_tipo)){
    $url_file = PATH_SELF_VIDEOS."/video_".$clave."/video_".$clave."_sd/output".$clave."_sd.txt";
	$url_file_sd = PATH_SELF_VIDEOS."/video_".$clave."/video_".$clave."_hd/".$name_video.".".$ext;

	$url_file_l_sd=PATH_SELF_VIDEOS."/video_".$clave."/video_".$clave."_sd/".$name_video."_sd.".$ext;
	$url_file_l_hd=PATH_SELF_VIDEOS."/video_".$clave."/video_".$clave."_hd/".$name_video."_hd.".$ext;
	
  }else{
    $fl_programa = $_GET['fl_programa'];
    $fl_vid_content = $_GET['fl_vid_cont'];
	$fl_video_contenid=$_GET['fl_video_contenid'];
	
	if(!empty($fg_creado_fame))
        $url_file = VID_FAME_STU_LIB."/video_".$clave."_".$fl_programa."/video_".$fl_vid_content."/video_".$fl_vid_content."_sd/output".$fl_vid_content."_sd.txt";
    else
	    $url_file = VID_FAME_STU_LIB."/video_".$clave."_".$fl_programa."/video_".$fl_vid_content."/video_".$fl_vid_content."_sd/output".$fl_vid_content.".txt";
    	
  }
  
  # Leemos el archivo que se genero con el comando
  $content = @file_get_contents($url_file);
  if(!empty($content)){
    //get duration of source
    preg_match("/Duration: (.*?), start:/", $content, $matches);

    $rawDuration = $matches[1]??NULL;
    $DurationDB = substr($rawDuration, 3, 5);
    $DurationDB = explode(":", $DurationDB);

    //rawDuration is in 00:00:00.00 format. This converts it to seconds.
    $ar = array_reverse(explode(":", $rawDuration));
    $duration = floatval($ar[0]);
    if (!empty($ar[1])) $duration += intval($ar[1]) * 60;
    if (!empty($ar[2])) $duration += intval($ar[2]) * 60 * 60;

    //get the time in the file that is already encoded
    preg_match_all("/time=(.*?) bitrate/", $content, $matches);

    $rawTime = array_pop($matches);

    //this is needed if there is more than one match
    if (is_array($rawTime)){$rawTime = array_pop($rawTime);}

    //rawTime is in 00:00:00.00 format. This converts it to seconds.
    $ar = array_reverse(explode(":", $rawTime));
    $time = floatval($ar[0]);
    if (!empty($ar[1])) $time += intval($ar[1]) * 60;
    if (!empty($ar[2])) $time += intval($ar[2]) * 60 * 60;

    //calculate the progress
    $progress = $duration==0?0:round(($time/$duration) * 100);

    // echo "Duration: " . $duration . "<br>";
    // echo "Current Time: " . $time . "<br>";
    // echo "Progress: " . $progress . "%";
    
	if($progress>=94)
	$progress=100;
    # Actualzamos el estatus del video
    if(empty($fg_tipo)){
      $Queryf = "UPDATE c_leccion_sp SET ds_progress_video='$progress' WHERE fl_leccion_sp=$clave";
    }else{
	  if($fg_creado_fame==1){
		  if($progress>=94)
			  $progress=100;
		  
		$Queryf="UPDATE k_video_contenido_sp SET ds_progreso='$progress' WHERE fl_video_contenido_sp=$fl_video_contenid AND fl_programa_sp=$fl_programa ";		  
		}else{
      $Queryf = "UPDATE k_video_contenido_sp SET ds_progress='$progress' WHERE fl_video_contenido_sp=$fl_vid_content";
	  }
    }
	
	#Eliminamos el archivo
	if($progress>=95){
	
		unlink($url_file_sd);
		//unlink($url_file_hd);
		unlink($url_file_l_sd);
		//unlink($url_file_l_hd);
	
	}
    EjecutaQuery($Queryf);

    #Actualizamos la duracion del video
    $ruta_thumbnail_video="";
    $duration_video="";
    if($progress==100){
        $times=explode(":",$rawTime);
        $hr=!empty($times[0])?$times[0]:"00";
        $mn=!empty($times[1])?$times[1]:"00";
        $sec=!empty($times[2])?$times[2]:"00";
        $duration_video=$hr.":".$mn.":".$sec;
        $duration_video = intval($DurationDB[0]).":".$DurationDB[1];
        EjecutaQuery("UPDATE c_leccion_sp SET  ds_vl_duracion='".$duration_video."' WHERE fl_leccion_sp=$clave ");
    }
    $ruta_thumbnail_video=ObtenConfiguracion(116)."/vanas_videos/fame/lessons/video_".$clave."/video_".$clave."_sd/img_1.png?i=".rand(5,10);

    $variables = array(
      'duration' => $duration,
	  'archivo'=>$archivo,
      'curren_time' => $time,
      'time_duration'=>$duration_video,
      'progress' => $progress,
      'update' => $Queryf,
      'delete_file_sd'=>$url_file_sd,
       'delete_file_hd'=>($url_file_hd??NULL),
      'delete_file_1hd'=>($url_file_l_hd??NULL),
      'delete_file_1sd'=>$url_file_l_sd,
      'ruta_thumbnail_video'=>$ruta_thumbnail_video,
      'url' => $url_file
    );   
  }else{

    #Hay un error actulizamos la
      $Queryf = "UPDATE c_leccion_sp SET ds_progress_video='0',ds_vl_ruta='',ds_vl_duracion='' WHERE fl_leccion_sp=$clave";
    EjecutaQuery($Queryf);

    $variables = array(
    'duration' => 0,
    'curren_time' => 0,
    'progress' => 0,
    'error' => 1,
    'Query'=>$Queryf,
    'url' => $url_file
    );
  }
  echo json_encode((Object) $variables);
?>
