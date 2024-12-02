<?php

  # Libreria de funciones
  require '../../lib/general.inc.php';

  # Recibe parametros
  $clave = RecibeParametroNumerico('clave');
  $no_semana = RecibeParametroNumerico('no_semana');
  $fl_usuario = RecibeParametroNumerico('usuario');
  $fg_actualizar = RecibeParametroNumerico('fg_actualizar');
  $type = RecibeParametroHTML('type');
  
  # Obtenemos la ultima leccion insertada
  if(empty($clave)){
    $clave = "us".$fl_usuario;    
  }
  
  # Ruta para los videos
  # tmp
  $ruta_tmp = SP_HOME."/AD3M2SRC4/tmp/";
  $ruta_str = VID_CAM_LEC."/video_".$clave;
  if($type=="VL"){
    $ruta_str = VID_CAM_LEC."/video_".$clave;
    # Ruta para los videos SD
    $ruta_sd = $ruta_str."/video_".$clave."_vl_sd";
    # Ruta para los videos HD
    $ruta_hd = $ruta_str."/video_".$clave."_vl_hd";
    # Campo a actualizar
    $campo = "ds_vl_ruta";
  }
  else{
    $ruta_str = VID_CAM_BREF."/video_".$clave;
    # Ruta para los videos SD
    $ruta_sd = $ruta_str."/video_".$clave."_vb_sd";
    # Ruta para los videos HD
    $ruta_hd = $ruta_str."/video_".$clave."_vb_hd";
    # Campo a actualizar
    $campo = "ds_as_ruta";
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
      
      # Actualizamos el registro de la leccion
      if($fg_actualizar==1){
        # Si el usuario actualiza el video cambiara el nombre de la carpeta e insertara un registro con la clave y en el lmedia_iu lo elimina
        $row = RecuperaValor("SELECT ".$campo." FROM c_leccion WHERE fl_leccion=$clave");
        $ds_vl_ruta = $row[0];
        if(!empty($ds_vl_ruta)){
          # los que tiene el fg_campus prendido son de campus los apagados son de fame
          EjecutaQuery("INSERT INTO k_video_temp (fl_usuario,nb_archivo, fl_leccion_sp, fg_campus, ds_type) VALUES (".$fl_usuario.", '".$name_main."', $clave, '1', '".$type."')");
          # Renombramos la carpeta original pero si el usuario ya no decide cambiarlo en lmedia lo regresa
          rename($ruta_str, VID_CAM_LEC."/video_re".$clave);
          # Creamos la carpeta del video
          if (!file_exists($ruta_str)) {
            mkdir($ruta_str, 0777, true);
            # Cambiamos los permisos de la carpeta 
            chmod($ruta_str, 0777);
          }
          # En caso de que ya se tenga alun archivo no se elimina
          if($type=="VL"){            
            exec('mv '.VID_CAM_LEC."/video_re".$clave.'/video_'.$clave.'_vb_sd '.$ruta_str);
            exec('mv '.VID_CAM_LEC."/video_re".$clave.'/video_'.$clave.'_vb_hd '.$ruta_str);
          }
          else{
            exec('mv '.VID_CAM_LEC."/video_re".$clave.'/video_'.$clave.'_vl_sd '.$ruta_str);
            exec('mv '.VID_CAM_LEC."/video_re".$clave.'/video_'.$clave.'_vl_hd '.$ruta_str);
          }
        }        
        
      }
      else{
        # Inserta datos
        if(strpos($clave, "us")===false)
          $gabriel = "INSERT INTO k_video_temp (fl_usuario,nb_archivo, fl_leccion_sp, fg_campus, ds_type) VALUES (".$fl_usuario.", '".$name_main."', ".$clave.", '1', '".$type."')";
        else          
          $gabriel = "INSERT INTO k_video_temp (fl_usuario,nb_archivo, fg_campus, ds_type) VALUES (".$fl_usuario.", '".$name_main."', '1', '".$type."')";
        // echo $gabriel;exit;
        EjecutaQuery($gabriel);
      }
      # Actualizamos
      if(!empty($clave))
        EjecutaQuery("UPDATE c_leccion SET ".$campo."='".$name_main.".mp4', fe_vl_alta=NOW() WHERE fl_leccion=$clave");      
      
      # Creamos la carpeta del video
      if (!file_exists($ruta_str)) {
        mkdir($ruta_str, 0777, true);        
      }
      # Cambiamos los permisos de la carpeta 
      chmod($ruta_str, 0777);     

      ### INICIO SD ###
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
      $file_name_mp4 = $name_main.".mp4";
      if($ext=="mov" || $ext=="MOV" || $ext=="flv"){
        if($ext=="flv"){
          # Nombre del archivo FLV
          $comando_mov_to_mp4 = CMD_FFMPEG." -i $ruta_sd/$file_name_ori -ar 22050 -vcodec libx264 $ruta_sd/$file_name_mp4";
        }
        else{
          # Nombre del archivo MP4        
          $comando_mov_to_mp4 = CMD_FFMPEG." -i $ruta_sd/$file_name_ori -vcodec copy $ruta_sd/$file_name_mp4";
        }
      }
      # Si el archivo que estan subiendo es mp4 solo lo renombramos
      else{
        $comando_mov_to_mp4 = "";
        rename($ruta_sd."/".$file_name_ori, $ruta_sd."/".$file_name_mp4);
      }

      # Cambiamos permisos del video
      chmod($ruta_sd."/".$file_name_mp4, 0777);
      ### FIN SD ###
      
      ### INICIO HD ###
      # Creamos la carpeta del video hd
      if (!file_exists($ruta_hd)) {
        mkdir($ruta_hd, 0777, true);
      }
      
      # Cambiamos los permisos de la carpeta hd
      chmod($ruta_hd, 0777);
      
      # Si el archivo tiene la extension MOV tendremos que convertirlo a MP4
      $file_name_mp4_hd = $name_main.".mp4";
      if($ext=="mov" || $ext=="MOV" || $ext=="flv"){
        if($ext=="flv"){
          # Nombre del archivo FLV
          $comando_mov_to_mp4_hd = CMD_FFMPEG." -i $ruta_sd/$file_name_ori -ar 22050 -vcodec libx264 $ruta_hd/$file_name_mp4_hd";
        }
        else{
          # Nombre del archivo MP4        
          $comando_mov_to_mp4_hd = CMD_FFMPEG." -i $ruta_sd/$file_name_ori -vcodec copy $ruta_hd/$file_name_mp4_hd";
        }
      }
      # Si el archivo que estan subiendo es mp4 solo lo renombramos
      else{
        $comando_mov_to_mp4_hd = "";        
        copy($ruta_sd."/".$file_name_mp4, $ruta_hd."/".$file_name_mp4);
        rename($ruta_hd."/".$file_name_mp4, $ruta_hd."/".$file_name_mp4_hd);
      }
      # Cambiamos permisos del video
      chmod($ruta_hd."/".$file_name_mp4_hd, 0777);
      ### FIN HD ###
      
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
      if($ext=="mov" || $ext=="MOV" || $ext=="flv"){       
        unlink($ruta_sd."/".$file_name_ori);
      }

      $result['success'] = "Correct!!!";
      $result['valores'] = 
      array(
      "name_ori" => $name_ori,
      "name_main" => $name_main,
      "ruta_str" => $ruta_str,
      "type" => $type,
      "comando_final" => $comando_final
      );
  }
  else{
    $result['error'] = "You should upload file MOV OR MP4";
  }
  
  echo json_encode((Object) $result);
  
?>