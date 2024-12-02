<?php

  # Libreria de funciones
  require '../../lib/general.inc.php';

  # Recibe parametros
  $clave = RecibeParametroNumerico('clave');
  $fl_usuario = RecibeParametroNumerico('usuario');
  $fg_actualizar = RecibeParametroNumerico('fg_actualizar');
  
  # Obtenemos la ultima leccion insertada
  if(empty($clave)){
    $clave = "us".$fl_usuario;    
  }
  
  # Ruta para los videos.
  $ruta_tmp = SP_HOME."/AD3M2SRC4/tmp/";
  $ruta_str = VID_CAM_NEWS."/video_".$clave;

  
  # Ruta para los videos SD.
  $ruta_sd = $ruta_str."/video_".$clave."_sd";
  # Ruta para los videos HD.
  $ruta_hd = $ruta_str."/video_".$clave."_hd";

  #Eliminamos directorio hd/sd antes de subir el video.
  rmdir($ruta_sd);
  rmdir($ruta_hd);
  
  
  
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
      $name_main = "S_NEWS_".$name_ori[0];
      
      # Base de datos      
      # Inserta datos temporales
      if(strpos($clave, "us")===false)
        $gabriel = "INSERT INTO k_vid_news_temp (fl_usuario,nb_archivo,fl_clave,ds_tipo) VALUES (".$fl_usuario.", '".$name_main."', ".$clave.", 'N')";
      else          
        $gabriel = "INSERT INTO k_vid_news_temp (fl_usuario,nb_archivo,ds_tipo) VALUES (".$fl_usuario.", '".$name_main."', 'N')";
      EjecutaQuery($gabriel);

      # Actualizamos
      if(!empty($clave))
        EjecutaQuery("UPDATE c_blog SET ds_ruta_imagen='".$name_main."' WHERE fl_blog=".$clave);      

      # Creamos la carpeta news
      if (!file_exists(VID_CAM_NEWS)) {
        mkdir(VID_CAM_NEWS, 0777, true);        
      }
      # Cambiamos los permisos de la carpeta 
      chmod(VID_CAM_NEWS, 0777);
      
      # Creamos la carpeta del video
      if (!file_exists($ruta_str)) {
        mkdir($ruta_str, 0777, true);        
      }
      # Cambiamos los permisos de la carpeta 
      chmod($ruta_str, 0777);     

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
      if($ext=="mov" || $ext=="MOV" || $ext=="flv"){       
        if($ext=="flv"){
          # Nombre del archivo FLV
          $comando_mov_to_mp4_hd = CMD_FFMPEG." -i $ruta_hd/$file_name_ori -ar 22050 -vcodec libx264 $ruta_hd/$file_name_mp4_hd";
        }
        else{
          # Nombre del archivo MP4        
          $comando_mov_to_mp4_hd = CMD_FFMPEG." -i $ruta_hd/$file_name_ori -vcodec copy $ruta_hd/$file_name_mp4_hd";
        }
      }
      
      chmod($ruta_hd."/".$file_name_mp4_hd, 0777);
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
      
      # Si el archivo tiene la extension MOV tendremos que convertirlo a MP4
      $file_name_mp4_sd = $name_main.".mp4";
      if($ext=="mov" || $ext=="MOV" || $ext=="flv"){       
        if($ext=="flv"){
          # Nombre del archivo FLV
          $comando_mov_to_mp4_sd = CMD_FFMPEG." -i $ruta_sd/$file_name_ori -ar 22050 -vcodec libx264 $ruta_sd/$file_name_mp4_sd";
        }
        else{
          # Nombre del archivo MP4        
          $comando_mov_to_mp4_sd = CMD_FFMPEG." -i $ruta_sd/$file_name_ori -vcodec copy $ruta_sd/$file_name_mp4_sd";
        }
      }
      else{
        rename($ruta_hd."/".$file_name_ori, $ruta_hd."/".$name_main.".mp4");
        rename($ruta_sd."/".$file_name_ori, $ruta_sd."/".$name_main.".mp4");        
      }
      
      chmod($ruta_sd."/".$file_name_mp4_sd, 0777);
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
      
      # Si los archivos son flv los eliminamos
      if($ext=="flv"){
        unlink($ruta_hd."/".$file_name_ori);
        unlink($ruta_sd."/".$file_name_ori);
      }


      $result['success'] = "Correct!!!";
      $result['valores'] = 
      array(
      "name_ori" => $name_ori,
      "name_main" => $name_main,
      "ruta_str" => $ruta_str,
      "comando_final" => $comando_final
      );
  }
  else{
    $result['error'] = "You should upload file MOV OR MP4";
  }
  
  echo json_encode((Object) $result);
  
?>