<?php
	# Include campus libraries 
	require '../AD3M2SRC4/lib/general.inc.php';
	//require '../lib/sp_config.inc.php';

	


	# Tuition payment due, find terms that have tuition due today
	$Query  = "SELECT fl_leccion_sp,a.fl_programa_sp, nb_programa, no_semana, ds_titulo, ds_vl_ruta, ds_progress_video  ";
	$Query .= "FROM c_leccion_sp a, c_programa_sp b, k_programa_detalle_sp c   ";
	$Query .= "WHERE a.fl_programa_sp = b.fl_programa_sp  AND a.fl_programa_sp = c.fl_programa_sp 
	AND a.fl_programa_sp >=32  AND a.fl_programa_sp <=45    ";
	//$Query.="AND a.fl_leccion_sp=1  ";
	$Query."
	 
	ORDER BY nb_programa, no_semana ";
	$rs = EjecutaQuery($Query);

	while($row=RecuperaRegistro($rs)){
		
		$fl_leccion_sp = $row[0];
		$fl_programa_sp = $row[1];
		$nb_programa = $row[2];
		$no_semana = $row[3];
		$ds_titulo = $row[4];
		$nb_video = str_texto($row[5])."_sd.mp4";
		$ds_progress_video=$row[6];
		
		#Ruta video.
		//$ruta_video=ObtenConfiguracion(116)."/vanas_videos/fame/lessons/video_".$fl_leccion_sp."/video_".$fl_leccion_sp."_sd";
		$ruta_video=SP_HOME."/vanas_videos/fame/lessons/video_".$fl_leccion_sp."/video_".$fl_leccion_sp."_sd";
		
		
        #Ruta del video
        $file_name_mp4_hd=$ruta_video."/".$nb_video;
		$ruta_img=$ruta_video;
        
		chmod($ruta_img, 0777); 
		
		chmod($ruta_video."/".$nb_video, 0777);
		
		#Verificamos si existe el archivo.
		$existe=file_exists($file_name_mp4_hd);
		if($existe){
			
		   //echo"<script>  alert('si existe: $file_name_mp4_hd  <br>   fliel img->$ruta_img');  </script>";	
           
           #Comando para generarlos thumbails del video linea de tiempo.
           $params=ObtenConfiguracion(135);
           exec(VIDEOS_CMD_HLS." -i $file_name_mp4_hd $params $ruta_img/img%d.jpg");
           
		   echo $nb_programa.$ds_titulo." yes <br>";
		   
		}else{
			
			
			echo "no se encontro archivo:leccion:$fl_leccion_sp , ". $file_name_mp4_hd."<br/>";
			
			
		} 
           
       
		
		
		
    
		
		
	}
?>