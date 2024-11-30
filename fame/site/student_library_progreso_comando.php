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
  
  # Recibe parametros
  $fl_video_contenido_sp=$_GET['fl_video_contenido_sp'];
  $fl_programa_sp=$_GET['fl_programa_sp'];
  $no_orden=$_GET['no_orden'];


  $Query="SELECT ds_ruta_video FROM k_video_contenido_sp WHERE fl_video_contenido_sp=$fl_video_contenido_sp ";
  $row=RecuperaValor($Query);
  $nb_archivo=$row[0];

  $url_file=ObtenConfiguracion(116)."/fame/site/uploads/".$fl_instituto."/attachments/student_library/video_".$fl_video_contenido_sp."/video_".$no_orden."/output.txt";
  $url_video_original=ObtenConfiguracion(116)."/fame/site/uploads/".$fl_instituto."/attachments/student_library/video_".$fl_video_contenido_sp."/video_".$no_orden."/$nb_archivo";

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
    $Queryf="UPDATE k_video_contenido_sp SET ds_progreso='$progress' WHERE fl_video_contenido_sp=$fl_video_contenido_sp  ";		  
	#Eliminamos el archivo
	if($progress>=94){
		unlink($url_video_original);	
	}
    EjecutaQuery($Queryf);

    #Actualizamos la duracion del video
    $ruta_thumbnail_video="";
    $duration_video="";
    if($progress==100){
        $duration_video = intval($DurationDB[0]).":".$DurationDB[1];
        EjecutaQuery("UPDATE k_video_contenido_sp SET  ds_duracion='".$duration_video."' WHERE fl_video_contenido_sp=$fl_video_contenido_sp ");
    }



    $ruta_thumbnail_video=ObtenConfiguracion(116)."/fame/site/uploads/".$fl_instituto."/attachments/student_library/video_".$fl_video_contenido_sp."/video_".$no_orden."/img_1.png?".rand(5,10);

    $variables = array(
      'time_duration'=>$duration_video,
      'progress' => $progress,
      'update' => $Queryf,
      'delete_file_1sd'=>$url_video_original,
      'ruta_thumbnail_video'=>$ruta_thumbnail_video,
      'url_outpout' => $url_file
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
