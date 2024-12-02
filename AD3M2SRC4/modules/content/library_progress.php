<?php
# Libreria de funciones
  require("../../lib/general.inc.php");
  
  # Recibe parametros
  $clave = $_GET['clave'];
  $fl_programa = $_GET['fl_programa'];
  $no_grado = $_GET['no_grado'];
  $fl_vid_cont = $_GET['fl_vid_cont'];
  $fg_fame = $_GET['fg_fame'];
  
  # Formamos la url
  if(empty($fg_fame)){
    // $url_file = VID_CAM_STU_LIB."/video_".$clave."_".$fl_programa."_".$no_grado."/video_".$fl_vid_cont."/video_".$fl_vid_cont."_hd/output".$fl_vid_cont.".php";
    $url_file = VID_CAM_STU_LIB."/video_".$fl_vid_cont."/video_".$fl_vid_cont."_hd/output".$fl_vid_cont.".php";
  }
  else{
    $url_file = VID_FAME_STU_LIB."/video_".$clave."_".$fl_programa."/video_".$fl_vid_cont."/video_".$fl_vid_cont."_hd/output".$fl_vid_cont.".php";
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
    if($progress>=95){
        $progress=100;
    }

    # Actualzamos el estatus del video
    if(empty($fg_fame))
      $Query_upt = "UPDATE k_video_contenido SET ds_progreso='".$progress."'  WHERE fl_video_contenido=".$fl_vid_cont;      
    else
      $Query_upt = "UPDATE k_video_contenido_sp SET ds_progreso='".$progress."'  WHERE fl_video_contenido_sp=".$fl_vid_cont;
    
    EjecutaQuery($Query_upt);
    
    $variables = array(
      'duration' => $duration,
      'curren_time' => $time,
      'progress' => $progress,
      'update' => $Query_upt,
      'url' => $url_file,
      'fl_vid_cont' => $fl_vid_cont,
      'error' => 0,
    );   
  }
  else{
    if(empty($progress)){
      $row = RecuperaValor("SELECT ds_progreso FROM k_video_contenido WHERE fl_video_contenido=".$fl_vid_cont);
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