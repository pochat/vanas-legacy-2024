<?php

  # Libreria de funciones
  require '../../lib/general.inc.php';

  # Recibe parametros
  $clave = RecibeParametroNumerico('clave');
  $no_semana = RecibeParametroNumerico('no_semana');
  $fl_usuario = RecibeParametroNumerico('fl_usuario');
  # Obtenemos la ultima leccion insertada
  if(empty($clave)){
    $clave = "us".$fl_usuario;    
  }
  # Parametros para convertir archivos mov en flv
  $parametros = ObtenConfiguracion(41);

  # Ruta para los videos
  # tmp
  $ruta_tmp = SP_HOME."/AD3M2SRC4/tmp/";
  $ruta_str = SP_HOME."/vanas_videos/fame/lessons/video_".$clave;
  # Ruta para los videos SD
  $ruta_sd = $ruta_str."/video_".$clave."_sd";
  # Ruta para los videos HD
  $ruta_hd = $ruta_str."/video_".$clave."_hd";
  
  #elimnamos rutas existentes
  # Cambiamos los permisos de la carpeta 
  chmod($ruta_sd, 0777); 
  chmod($ruta_hd, 0777); 
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
      
      # Creamos la carpeta del video
      if (!file_exists($ruta_str)) {
        mkdir($ruta_str, 0777, true);        
      }
      # Cambiamos los permisos de la carpeta 
      chmod($ruta_str, 0777); 

      # Nombre para todos los archivos
      $name_ori = explode(".", $file_name_ori);
      // $name_main = $name_ori[0].$clave."_".$no_semana;
      $name_main = $name_ori[0];
      
      # Actualizamos el registro de la leccion
      if(is_numeric($clave)){
        # Actualizamos
        EjecutaQuery("UPDATE c_leccion_sp SET ds_vl_ruta='".$name_main.".mp4', fe_vl_alta=NOW(), fl_leccion_copy=0 WHERE fl_leccion_sp=$clave");  
        # Si el usuario actualiza el video cambiara el nombre de la carpeta e insertara un registro con la clave y en el lmedia_iu lo elimina
        # Pero si 
        $row = RecuperaValor("SELECT ds_vl_ruta FROM c_leccion_sp WHERE fl_leccion_sp=$clave");
        $ds_vl_ruta = $row[0];
        if(!empty($ds_vl_ruta)){
          EjecutaQuery("INSERT INTO k_video_temp (fl_usuario,nb_archivo, fl_leccion_sp) VALUES (".$fl_usuario.", '".$name_main."', $clave)");
          # Renombramos la carpeta original pero si el usuario ya no decide cambiarlo en lmedia lo regresa
          rename($ruta_str, SP_HOME."/vanas_videos/fame/lessons/video_re".$clave);
        }
             
      }
      else{
        if(strpos($clave, "us"))          
          $gabriel = "INSERT INTO k_video_temp (fl_usuario,nb_archivo, fl_leccion_sp) VALUES (".$fl_usuario.", '".$name_main."', ".$clave.")";
        else          
          $gabriel = "INSERT INTO k_video_temp (fl_usuario,nb_archivo) VALUES (".$fl_usuario.", '".$name_main."')";
        
        EjecutaQuery($gabriel);
      }
      
      /*** INICIO SD ***/
      # Creamos la carpeta del video sd
      if (!file_exists($ruta_sd)) {
        mkdir($ruta_sd, 0777, true);
      }
      
      # Cambiamos los permisos de la carpeta sd
      chmod($ruta_sd, 0777);
      
      # Buscamos si existe el archivo
      # En caso de que no solo va a subir el archivo
      if(file_exists($ruta_sd."/".$file_name_ori))
        unlink($ruta_sd."/".$file_name_ori);
      move_uploaded_file($tempFile, $ruta_sd."/".$file_name_ori);
      
      # Si el archivo tiene la extension MOV tendremos que convertirlo a MP4
      $file_name_mp4 = $name_main."_sd.mp4";
      if($ext=="mov" || $ext=="MOV"){
        # Nombre del archivo MP4        
        $comando_mov_to_mp4 = CMD_FFMPEG." -i $ruta_sd/$file_name_ori -vcodec copy $ruta_sd/$file_name_mp4";      
      }
      # Si el archivo que estan subiendo es mp4 solo lo renombramos
      else{
        $comando_mov_to_mp4 = "";
        rename($ruta_sd."/".$file_name_ori, $ruta_sd."/".$file_name_mp4);
      }
      chmod($ruta_sd."/".$file_name_mp4, 0777);
      /*** FIN SD ***/
      
      /*** INICIO HD ***/
      # Creamos la carpeta del video hd
      if (!file_exists($ruta_hd)) {
        mkdir($ruta_hd, 0777, true);
      }
      
      # Cambiamos los permisos de la carpeta hd
      chmod($ruta_hd, 0777);
      
      # Si el archivo tiene la extension MOV tendremos que convertirlo a MP4
      $file_name_mp4_hd = $name_main."_hd.mp4";
      if($ext=="mov" || $ext=="MOV"){
        # Nombre del archivo MP4        
        $comando_mov_to_mp4_hd = CMD_FFMPEG." -i $ruta_sd/$file_name_ori -vcodec copy $ruta_hd/$file_name_mp4_hd";
      }
      # Si el archivo que estan subiendo es mp4 solo lo renombramos
      else{
        $comando_mov_to_mp4_hd = "";        
        copy($ruta_sd."/".$file_name_mp4, $ruta_hd."/".$file_name_mp4);
        rename($ruta_hd."/".$file_name_mp4, $ruta_hd."/".$file_name_mp4_hd);
      }
      chmod($ruta_hd."/".$file_name_mp4_hd, 0777);
      /*** FIN HD ***/
      
      # Ejecutamos todos los comandos uno detras del otro
      $comando_final = "";
      if(!empty($comando_mov_to_mp4))
        $comando_final .= $comando_mov_to_mp4." && ";
      else
        $comando_final .= "";
      if(!empty($comando_mov_to_mp4_hd))
        $comando_final .= $comando_mov_to_mp4_hd;
      else
        $comando_final .= "";
      # Ejecutamos el comando final
      if(!empty($comando_final)){
        exec($comando_final); 
      }
      
      # Eliminamos el archivo mov
      if($ext=="mov" || $ext=="MOV"){       
        unlink($ruta_sd."/".$file_name_ori);
      }
      
      $result['success'] = "Correct!!! $comando_image";      
  }
  else{
    $result['error'] = "You should upload file MOV OR MP4";
  }
  
  # Cambiamos los permisos de la carpeta hd
  chmod($ruta_hd, 0777);
  chmod($ruta_hd."/".$file_name_mp4_hd, 0777);
  # Cambiamos los permisos de la carpeta sd
  chmod($ruta_sd, 0777);
  # Cambiamos los permisos de la carpeta 
  chmod($ruta_str, 0777); 
  
  
  echo json_encode((Object) $result);
?>
