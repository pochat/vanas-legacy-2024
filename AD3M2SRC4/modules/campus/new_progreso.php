<?php
# Libreria de funciones
  require("../../lib/general.inc.php");
  
  # Recibe parametros
  $archivo = $_GET['archivo'];
  $clave = $_GET['clave'];
  $name_video = array_shift(explode('.',$archivo));
  
  # Formamos la url
  //$url_file = VID_CAM_NEWS."/video_".$clave."/video_".$clave."_hd/output".$clave.".php";
  $url_file = "../../../vanas_videos/campus/news/video_".$clave."/video_".$clave."_hd/output".$clave.".php";
  
  $url_file_sd = VID_CAM_NEWS."/video_".$clave."/video_".$clave."_hd/".$archivo.".mp4";
  $url_file_hd = VID_CAM_NEWS."/video_".$clave."/video_".$clave."_sd/".$archivo.".mp4";

  
  
  # Leemos el archivo que se genero con el comando
  $content = @file_get_contents($url_file);
  if($content){
    //get duration of source
    preg_match("/Duration: (.*?), start:/", $content, $matches);

    $rawDuration = $matches[1];

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
    $progress = round(($time/$duration) * 100);

    // echo "Duration: " . $duration . "<br>";
    // echo "Current Time: " . $time . "<br>";
    // echo "Progress: " . $progress . "%";
    
    # Actualzamos el estatus del video
     EjecutaQuery("UPDATE c_blog SET ds_progress_video='$progress' WHERE fl_blog=$clave");
    
	#Elimnamos el archivo .mp4 que se subio/*ya no se necesita, solo genera espacio en disco **/
	if($progress==100){
		$eliminar_archivo="Entro a eliminar_archivo";
		//$dir="/var/www/html/public_html/dev/vanas_videos/campus/news/video_428/video_428_hd/S_NEWS_S_NEWS_Recorr.mp4";
		unlink($url_file_sd);
		unlink($url_file_hd);
	}
	
	
	
    $variables = array(
      'duration' => $duration,
	  'name_video_hd'=>$archivo,
	  'eliminar_archivo'=>$eliminar_archivo,
      'curren_time' => $time,
      'progress' => $progress,
      'update' => "UPDATE c_blog SET ds_progress_video='$progress' WHERE fl_blog=$clave",
      'url' => $url_file
    );   
  }
  else{
    $variables = array(
    'duration' => 0,
    'curren_time' => 0,
    'progress' => 0,
    'error' => 1,
    'url' => $url_file
    );
  }
  
  
  
  
  
  echo json_encode((Object) $variables);
?>
