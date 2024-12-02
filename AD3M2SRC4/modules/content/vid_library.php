<?php

  # Libreria de funciones
  require '../../lib/general.inc.php';

  # Recibe parametros
  $clave = RecibeParametroNumerico('clave');
  $fl_programa = RecibeParametroNumerico('fl_programa');
  $no_grado = RecibeParametroNumerico('no_grado');  
  $fl_usuario = RecibeParametroNumerico('usuario');
  $fg_actualizar = RecibeParametroNumerico('fg_actualizar');
  $fg_fame = RecibeParametroNumerico('fg_fame');
  $title_video = RecibeParametroHTML('title_video');
  $fg_creado_teacher=RecibeParametroNumerico('fg_creado_teacher');
  $fg_video_orientation=RecibeParametroBinario('fg_video_orientation');
  if(empty($title_video))
    $title_video = "NULL";
  # Obtenemos la ultima leccion insertada
  if(empty($clave)){
    $clave = "us".$fl_usuario;    
    $fl_programa = "us".$fl_usuario;    
    $no_grado = "us".$fl_usuario;
  }

  # Recibe archivo de video
  if(!empty($_FILES)){
      # Nombre original del archivo
      $file_name_ori = $_FILES['file']['name'];
      # Nombre de la official
      $tempFile = $_FILES['file']['tmp_name'];
      # Obtenemos la extension del archivo
      $ext = ObtenExtensionArchivo($file_name_ori);      

      # Nombre para todos los archivos
      $name_ori = explode(".", $file_name_ori);
      $name_main = $name_ori[0];
      
      if($fg_creado_teacher==1){
		  
		 
		  
	  }
      
      # Base de datos
      # Con el flag sabemos si es librar de FAME o CAMPUS
      $row3 = RecuperaValor("SELECT POSITION('us' IN '".$clave."')");
      if(empty($fg_fame)){
        if(!empty($row3[0])){
          $rt = RecuperaValor("SELECT COUNT(*) FROM k_vid_content_temp WHERE fl_clave=".$fl_usuario." AND fl_programa=".$fl_usuario." AND no_grado=".$fl_usuario);        
          $cont = $rt[0]+1;
          # Inserta datos        
          $gabriel = "INSERT INTO k_vid_content_temp (fl_usuario, nb_archivo, fl_clave, fl_programa, no_grado, no_orden, ds_title_vid) VALUES (".$fl_usuario.", '".$name_main."', ".$fl_usuario.", ".$fl_usuario.", ".$fl_usuario.", ".$cont.", '".$title_video."')";
          EjecutaQuery($gabriel);
          $fl_video_contenido = $cont;
        }
        else{
          $rt = RecuperaValor("SELECT COUNT(*) FROM k_vid_content_temp WHERE fl_clave=".$clave." AND fl_programa=".$fl_programa." AND no_grado=".$no_grado);        
          $cont = $rt[0]+1;
          # Inserta datos        
          $gabriel = "INSERT INTO k_vid_content_temp (fl_usuario, nb_archivo, fl_clave, fl_programa, no_grado, no_orden, ds_title_vid) VALUES (".$fl_usuario.", '".$name_main."', ".$clave.", ".$fl_programa.", ".$no_grado.", ".$cont.", '".$title_video."')";
          EjecutaQuery($gabriel);
          $fl_video_contenido = $cont;
        }
      }
      else{        
        if(!empty($row3[0])){
          $rt = RecuperaValor("SELECT COUNT(*) FROM k_vid_content_temp WHERE fl_clave=".$fl_usuario." AND fl_programa=".$fl_usuario." AND no_grado=".$fl_usuario." AND fg_fame='1'");
          $cont = $rt[0]+1;
          # Inserta datos        
          $gabriel = "INSERT INTO k_vid_content_temp (fl_usuario, nb_archivo, fl_clave, fl_programa, no_grado, no_orden, fg_fame, ds_title_vid
          ) VALUES (".$fl_usuario.", '".$name_main."', ".$fl_usuario.", ".$fl_usuario.", ".$fl_usuario.", ".$cont.", '1', '".$title_video."')";
          EjecutaQuery($gabriel);
          $fl_video_contenido = $cont;
        }
        else{
          $rt = RecuperaValor("SELECT COUNT(*) FROM k_vid_content_temp WHERE fl_clave=".$clave." AND fl_programa=".$fl_programa." AND no_grado=".$no_grado." AND fg_fame='1'");        
          $cont = $rt[0]+1;
          # Inserta datos        
          $gabriel = "INSERT INTO k_vid_content_temp (fl_usuario, nb_archivo, fl_clave, fl_programa, no_grado, no_orden, fg_fame, ds_title_vid) VALUES (".$fl_usuario.", '".$name_main."', ".$clave.", ".$fl_programa.", ".$no_grado.", ".$cont.", '1', '".$title_video."')";
          EjecutaQuery($gabriel);
          $fl_video_contenido = $cont;
        }
      }
      // EjecutaQuery($gabriel);
      
      # Ruta para los videos
      # tmp
      $ruta_tmp = SP_HOME."/AD3M2SRC4/tmp/";
      if(empty($fg_fame)){
        // $ruta_str1 = VID_CAM_STU_LIB."/video_".$clave."_".$fl_programa."_".$no_grado;
        $ruta_str1 = VID_CAM_STU_LIB;
        # Creamos la carpeta student library
        if (!file_exists(VID_CAM_STU_LIB)) {
          mkdir(VID_CAM_STU_LIB, 0777, true);        
        }
        # Cambiamos los permisos de la carpeta 
        chmod(VID_CAM_STU_LIB, 0777);
      }
      else{
        $ruta_str1 = VID_FAME_STU_LIB."/video_".$clave."_".$fl_programa;
        # Creamos la carpeta student library
        if (!file_exists(VID_FAME_STU_LIB)) {
          mkdir(VID_FAME_STU_LIB, 0777, true);        
        }
        # Cambiamos los permisos de la carpeta 
        chmod(VID_FAME_STU_LIB, 0777);
      }
      $ruta_str2 = $ruta_str1."/video_".$fl_video_contenido;
      # Ruta para los videos SD
      $ruta_sd = $ruta_str2."/video_".$fl_video_contenido."_sd";
      # Ruta para los videos HD
      $ruta_hd = $ruta_str2."/video_".$fl_video_contenido."_hd";    

      # Creamos la carpeta del video
      if (!file_exists($ruta_str1)) {
        mkdir($ruta_str1, 0777, true);        
      }
      # Cambiamos los permisos de la carpeta 
      chmod($ruta_str1, 0777);
      
      # Creamos la carpeta del video
      if (!file_exists($ruta_str2)) {
        mkdir($ruta_str2, 0777, true);        
      }
      # Cambiamos los permisos de la carpeta 
      chmod($ruta_str2, 0777);

      ## INICIO HD ##
      # Creamos la carpeta del video hd
      if (!file_exists($ruta_hd)) {
        mkdir($ruta_hd, 0777, true);
      }
      
      # Cambiamos los permisos de la carpeta hd
      chmod($ruta_hd, 0777);
      
      # Buscamos si existe el archivo
      # En caso de que no solo va a subir el archivo
      if(file_exists($ruta_hd."/".$file_name_ori))
        unlink($ruta_hd."/".$file_name_ori);
      # Subimos el archivo original
      $comando_mov_to_mp4_hd = "";
      move_uploaded_file($tempFile, $ruta_hd."/".$file_name_ori);
      
      # Si el archivo tiene la extension MOV tendremos que convertirlo a MP4
      $file_name_mp4_hd = $name_main.".mp4";
      if($ext=="mov" || $ext=="MOV" || $ext=="flv" ||$ext=="avi" ){       
        if($ext=="flv"){
          # Nombre del archivo FLV
          $comando_mov_to_mp4_hd = CMD_FFMPEG." -i $ruta_hd/$file_name_ori -ar 22050 -vcodec libx264 $ruta_hd/$file_name_mp4_hd";
        }
        else{
          # Nombre del archivo MP4        
          $comando_mov_to_mp4_hd = CMD_FFMPEG." -i $ruta_hd/$file_name_ori -vcodec copy $ruta_hd/$file_name_mp4_hd";
		  
			    if( ($fg_creado_teacher==1) && (($ext=="MOV")||($ext=="mov")) ){
				      $comando_mov_to_mp4_hd=CMD_FFMPEG." -i $ruta_hd/$file_name_ori -vcodec h264 -acodec mp2 $ruta_hd/$file_name_mp4_hd";
				  
			    }
		  
		  
		  
        }
      }     
      chmod($ruta_hd."/".$file_name_mp4_hd, 0777);
      $name_img_hd = $ruta_hd."/img_%d.png";
      $file_mp4_hd = $ruta_hd."/".$file_name_mp4_hd;
      $comando_image_hd = VIDEOS_CMD_HLS." -i $file_mp4_hd -ss 00:00:02 -vframes 1 $name_img_hd";
      ## FIN HD ##
      
       ## INICIO SD ##
      # Creamos la carpeta del video hd
      if (!file_exists($ruta_sd)) {
        mkdir($ruta_sd, 0777, true);
      }
      
      # Cambiamos los permisos de la carpeta hd
      chmod($ruta_sd, 0777);
      
      # Buscamos si existe el archivo
      # En caso de que no solo va a subir el archivo
      if(file_exists($ruta_sd."/".$file_name_ori))
        unlink($ruta_sd."/".$file_name_ori);
      # Subimos el archivo original
      $comando_mov_to_mp4_sd = "";
      copy($ruta_hd."/".$file_name_ori, $ruta_sd."/".$file_name_ori);
      $paso=2;
      # Si el archivo tiene la extension MOV tendremos que convertirlo a MP4
      $file_name_mp4_sd = $name_main.".mp4";
      if($ext=="mov" || $ext=="MOV" || $ext=="flv" ){       
        if($ext=="flv"){
          # Nombre del archivo FLV
          $comando_mov_to_mp4_sd = CMD_FFMPEG." -i $ruta_sd/$file_name_ori -ar 22050 -vcodec libx264 $ruta_sd/$file_name_mp4_sd";
        }
        else{
          # Nombre del archivo MP4        
          $comando_mov_to_mp4_sd = CMD_FFMPEG." -i $ruta_sd/$file_name_ori -vcodec copy $ruta_sd/$file_name_mp4_sd";
		  
		      #solo si es un mov/mov para front FAME
			  if( ($fg_creado_teacher==1) && (($ext=="MOV")||($ext=="mov")) ){
				      $comando_mov_to_mp4_sd=CMD_FFMPEG." -i $ruta_sd/$file_name_ori -vcodec h264 -acodec mp2 $ruta_sd/$file_name_mp4_sd";
				  
			  }
		  
        }
      }
      else{
        rename($ruta_hd."/".$file_name_ori, $ruta_hd."/".$name_main.".mp4");
        rename($ruta_sd."/".$file_name_ori, $ruta_sd."/".$name_main.".mp4"); 
        $paso=3;		
      }      
      chmod($ruta_sd."/".$file_name_mp4_sd, 0777);
      $name_img_sd = $ruta_sd."/img_%d.png";
      $file_mp4_sd = $ruta_sd."/".$file_name_mp4_sd;
      $comando_image_sd = VIDEOS_CMD_HLS." -i $file_mp4_sd -ss 00:00:02 -vframes 1 $name_img_sd";
      ## FIN SD ##
      
      # Ejecutamos todos los comandos uno detras del otro
      if(!empty($comando_mov_to_mp4_hd))
        $comando_final .= $comando_mov_to_mp4_hd;
      else
        $comando_final .= "";
      if(!empty($comando_mov_to_mp4_sd))
        $comando_final .= " && ".$comando_mov_to_mp4_sd;
      else
        $comando_final .= "";
      # Ejecutamos el comando final
      if(!empty($comando_final)){
        exec($comando_final); 
      }
      $comando_img_final = "";
      # Ejecutamos los comando para las imagenes
      if(!empty($comando_image_hd))
        $comando_img_final .= $comando_image_hd;
      else
        $comando_img_final .= "";
      if(!empty($comando_image_sd))
        $comando_img_final .= " && ".$comando_image_sd;
      else
        $comando_img_final .= "";
      # Ejecutamos el comando final imagen
      if(!empty($comando_img_final)){
        exec($comando_img_final); 
      }
      
      //pagina de videos de oirnetacion
      if($fg_video_orientation==1){
          
          $output_sd = $ruta_sd."/output_sd.php";
          chmod($output_sd, 0777);
          $file_name_hls = $ruta_sd."/".$name_main."_sd.m3u8";

          $output_hd = $ruta_hd."/output_hd.php";
          chmod($output_hd, 0777);
          $file_name_hd_hls = $ruta_hd."/".$name_main."_hd.m3u8";

          # Comando para convertir el archivo mp4 a m3u8
          $comando_mp4_to_hls_sd  = "/var/www/html/vanas/fame/ffmpeg/ffmpeg -i $ruta_sd"."/$file_name_mp4_sd  -s hd1080 -c:v libx264 -crf 23 -c:a aac -strict -2 -c:a:0 copy -c:a:1 copy -c:s copy -hls_list_size 0 -hls_segment_size 500000 $file_name_hls ";
          $comando_mp4_to_hls_sd .= " 1>$output_sd 2>&1";

          $comando_mp4_to_hls_hd  = "/var/www/html/vanas/fame/ffmpeg/ffmpeg -i $ruta_hd"."/$file_name_mp4_sd  -s hd1080 -c:v libx264 -crf 23 -c:a aac -strict -2 -c:a:0 copy -c:a:1 copy -c:s copy -hls_list_size 0 -hls_segment_size 500000 $file_name_hd_hls ";
          $comando_mp4_to_hls_hd .= " 1>$output_hd 2>&1";


          # Ejecutamos el comando para generar los m3u8
          exec($comando_mp4_to_hls_sd." >> /dev/null &");
          exec($comando_mp4_to_hls_hd." >> /dev/null &");



          $comando_final=$comando_mp4_to_hls_sd." >> /dev/null &";

      }




	  #Si es del estufusnte library entonces convertimos a m 
	  if($fg_creado_teacher==1){
		  
	        $ruta_str1 = VID_FAME_STU_LIB."/video_".$clave."_".$fl_programa;
			
			$ruta_str2 = $ruta_str1."/video_".$fl_video_contenido;
			
			#Recuperamos el id del registro que anteriormente se creo
            $Query="SELECT fl_vid_contet_temp FROM k_vid_content_temp WHERE  fl_clave=$clave AND  fl_programa=$fl_programa  AND no_orden=$cont ";
            $mo=RecuperaValor($Query);
            $fl_video_conten_temp=$mo[0];

			$Query="INSERT INTO k_video_contenido_sp(cl_pagina_sp,fl_programa_sp,ds_ruta_video,ds_progreso,ds_title_vid,fl_vid_contet_temp)VALUES($fl_programa,$fl_programa,'$name_main','0','$title_video',$fl_video_conten_temp) ";
		    EjecutaQuery($Query);
			
			
			# Ruta para los videos SD
		    $ruta_sd_completa = $ruta_str2."/video_".$fl_video_contenido."_sd";
			# Ruta para los videos HD
			$ruta_hd_completa = $ruta_str2."/video_".$fl_video_contenido."_hd";
			
			
			$ruta_img=$ruta_sd_completa;
			$file_name = $name_main;
			
			#reombramos el archivo. SD
		    rename($ruta_sd_completa."/".$file_name.".mp4", $ruta_sd_completa."/".$file_name."_sd.mp4");		
			#Renombramos el archivo HD
			rename($ruta_hd_completa."/".$file_name.".mp4", $ruta_hd_completa."/".$file_name."_hd.mp4");
			
            
			
			
			
			# Nombre del archivo m3u8
		    $file_name_mp4 = $ruta_sd_completa."/".$file_name."_sd.mp4";
	        $file_name_hls = $ruta_sd_completa."/".$file_name."_sd.m3u8";
	        $output = $ruta_sd_completa."/output".$fl_video_contenido."_sd.php";
	        chmod($output, 0777);
	       
	  
			# Comando para convertir el archivo mp4 a m3u8
			$comando_mp4_to_hls_sd  = "/var/www/html/vanas/fame/ffmpeg/ffmpeg -i $file_name_mp4  -s hd1080 -c:v libx264 -crf 23 -c:a aac -strict -2 -c:a:0 copy -c:a:1 copy -c:s copy -hls_list_size 0 -hls_segment_size 500000 $file_name_hls ";
			$comando_mp4_to_hls_sd .= " 1>$output 2>&1";

			# Convertimos el MP4 to HLS m3u8
            $file_name_mp4_hd = $ruta_hd_completa."/".$file_name."_hd.mp4";
            $file_name_hls_hd = $ruta_hd_completa."/".$file_name."_hd.m3u8";
            $comando_mp4_to_hls_hd = "/var/www/html/vanas/fame/ffmpeg/ffmpeg -i $file_name_mp4_hd  -s hd1080 -c:v libx264 -crf 23 -c:a aac -strict -2 -c:a:0 copy -c:a:1 copy -c:s copy -hls_list_size 0 -hls_segment_size 500000 $file_name_hls_hd"; 
	  
	        
			
	        #Cuando es un archivo .mov,fly, creamos la imagen , ya que proceso anterior no se crea en ningun lado.
			 if($ext=="mov" || $ext=="MOV" || $ext=="flv"||$ext=="avi" ){   
			 
			 #Nota: el proceso cambia para extensiones .fly y .mov,.avi solo se crea la thumb default para vista previa
			 #para SD
			 chmod($ruta_sd_completa."/".$file_name."_sd.mp4", 0777);
             $name_img_sd = $ruta_sd_completa."/img_1.png";
			 if($ext=="avi")
             $file_mp4_sd = $ruta_sd_completa."/".$file_name.".avi";
		     else
		     $file_mp4_sd = $ruta_sd_completa."/".$file_name."_sd.mp4";
			 $comando_image_sd = VIDEOS_CMD_HLS." -i $file_mp4_sd -vf thumbnail,scale=861:485 -frames:v 1 $name_img_sd";
			 
			 #para HD
			 chmod($ruta_hd_completa."/".$file_name."_hd.mp4", 0777);
			 $name_img_hd = $ruta_hd_completa."/img_1.png";
			 if($ext=="avi")
             $file_mp4_hd = $ruta_hd_completa."/".$file_name.".avi";
		     else
			 $file_mp4_hd = $ruta_hd_completa."/".$file_name."_hd.mp4";
             $comando_image_hd = VIDEOS_CMD_HLS." -i $file_mp4_hd -vf thumbnail,scale=861:485 -frames:v 1 $name_img_hd";
    
			 $comando_img_final="";
			 $comando_img_final .= $comando_image_hd." && ".$comando_image_sd;
			 exec($comando_img_final); 
			 
			 
			 }
             #Genrramos la imagen de caratula
			 if($ext=="mp4"){

                 $name_img_hd = $ruta_hd_completa."/img_1.png";   
                 $file_mp4_hd = $ruta_hd_completa."/".$file_name."_hd.mp4";
                 $comando_image_hd = VIDEOS_CMD_HLS." -i $file_mp4_hd -vf thumbnail,scale=861:485 -frames:v 1 $name_img_hd";
                 exec($comando_image_hd); 

                 $name_img_sd = $ruta_sd_completa."/img_1.png";   
                 $file_mp4_sd = $ruta_sd_completa."/".$file_name."_sd.mp4";
                 $comando_image_sd = VIDEOS_CMD_HLS." -i $file_mp4_sd -vf thumbnail,scale=861:485 -frames:v 1 $name_img_sd";
                 exec($comando_image_sd); 

             }
			 
			
			# Ejecutamos el comando para generar los m3u8
            exec($comando_mp4_to_hls_sd." >> /dev/null &");
            exec($comando_mp4_to_hls_hd." >> /dev/null &");
			
	        
	  
	  
	        #Comando para generarlos thumbails del video linea de tiempo.
	        $params=ObtenConfiguracion(135);
	        exec(VIDEOS_CMD_HLS." -i $file_name_mp4_hd $params $ruta_img/img%d.jpg");
	  
	        
	  
	  
	  
	  }
	  
	  
	  
      $result['success'] = "Correct!!!";
      $result['valores'] = 
      array(
      "name_ori" => $name_ori,
	  "ruta_hd"=>$ruta_hd,
      "file_name_ori"=>$file_name_ori,
      "name_main" => $name_main,
      "ruta_str" => $ruta_str2,
      "comnado_final" => $comando_final,
      "comandoimagen" => $comando_img_final,
	  "paso" => $paso,
      "gabriel" => $gabriel,
      "clave" => $clave,
      "title_video" => $title_video
      );
  }
  else{
    $result['error'] = "You should upload file MOV OR MP4";
  }
  
  echo json_encode((Object) $result);
  
?>
