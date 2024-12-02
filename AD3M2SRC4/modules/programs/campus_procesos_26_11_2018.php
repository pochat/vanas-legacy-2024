<?php
# Libreria de funciones
  require("../../lib/general.inc.php");
  
  # Recibe parametros
  $archivo = $_GET['archivo'];
  $clave = $_GET['clave'];
  $type = $_GET['type'];
  $name_video = array_shift(explode('.',$archivo));
  
  # Formamos la url
  if($type=="VL"){
    $url_file = VID_CAM_LEC."/video_".$clave."/video_".$clave."_vl_sd/output".$clave."_sd.php";
    $campo = "ds_progress_video_vl";
  }
  else{
    $url_file = VID_CAM_BREF."/video_".$clave."/video_".$clave."_vb_sd/output".$clave."_sd.php";
    $campo = "ds_progress_video_vf";
  }
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
    EjecutaQuery("UPDATE c_leccion SET ".$campo."='$progress' WHERE fl_leccion=$clave");
    
    if(empty($progress)){
      $row = RecuperaValor("SELECT ".$campo." FROM c_leccion WHERE fl_leccion=".$clave);
      $progress = $row[0];
    }
    
    $variables = array(
      'duration' => $duration,
      'curren_time' => $time,
      'progress' => $progress,
      'update' => "UPDATE c_leccion SET ".$campo."='$progress' WHERE fl_leccion=$clave",
      'url' => $url_file
    );   
  }
  else{
    if(empty($progress)){
      $row = RecuperaValor("SELECT ".$campo." FROM c_leccion WHERE fl_leccion=".$clave);
      $progress = $row[0];
    }
    else
      $progress = "0";
    $variables = array(
    'duration' => 0,
    'curren_time' => 0,
    'progress' => $progress,
    'error' => 1,
    'url' => $url_file
    );
  }
  echo json_encode((Object) $variables);
?>