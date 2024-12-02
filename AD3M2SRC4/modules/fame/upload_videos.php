<?php

  # Libreria de funciones
  require '../../lib/general.inc.php';

  # Recibe parametros
  $clave = RecibeParametroNumerico('clave');
  $no_semana = RecibeParametroNumerico('no_semana');
  $fl_usuario = RecibeParametroNumerico('fl_usuario');
  $fg_creado_instituto=RecibeParametroHTML('fg_creado_instituto');

  # Obtenemos la ultima leccion insertada
  if(empty($clave)){
    $clave = "us".$fl_usuario;
  }

  # Parametros para convertir archivos mov en flv
  //$parametros = ObtenConfiguracion(41);

  # Ruta temporal para upload tmp
  $ruta_tmp = SP_HOME."/AD3M2SRC4/tmp/";

  # Ruta para los videos
  $ruta_str = SP_HOME."/vanas_videos/fame/lessons/video_".$clave;

  # Ruta para los videos
  $ruta_sd = $ruta_str."/video_".$clave."_sd";

  # Ruta para los videos HD
  //$ruta_hd = $ruta_str."/video_".$clave."_hd";

  # Elimnamos la ruta y su contenido existente
  if (file_exists($ruta_sd)){
      delete_folder($ruta_sd);
  }

# Recibe archivo de video
  if(!empty($_FILES)){
      # Nombre temporal
      $tempFile = $_FILES['file']['tmp_name'];

      # Nombre original del archivo
      $file_name_ori = $_FILES['file']['name'];

      # Obtenemos la extension del archivo
      $ext = ObtenExtensionArchivo($file_name_ori);

      # Creamos la carpeta del video
      if (!file_exists($ruta_str)) {
        mkdir($ruta_str, 0777, true);
      }

      # Aislamos el nombre sin la extencion
      $name_ori = explode(".", $file_name_ori);
      $name_main = $name_ori[0];

      # Actualizamos el registro de la leccion
      if(is_numeric($clave)){
          # Actualizamos
          EjecutaQuery("UPDATE c_leccion_sp SET ds_vl_ruta=\"$file_name_ori\", fe_vl_alta=NOW(), fl_leccion_copy=0 WHERE fl_leccion_sp=$clave");
          # Si el usuario actualiza el video cambiara el nombre de la carpeta e insertara un registro con la clave y en el lmedia_iu lo elimina
          # Pero si
          $row = RecuperaValor("SELECT ds_vl_ruta FROM c_leccion_sp WHERE fl_leccion_sp=$clave");
          $ds_vl_ruta = $row[0];
          if(!empty($ds_vl_ruta)){
              EjecutaQuery("INSERT INTO k_video_temp (fl_usuario,nb_archivo, fl_leccion_sp) VALUES (\"$fl_usuario\", \"$file_name_ori\", $clave)");
              # Renombramos la carpeta original pero si el usuario ya no decide cambiarlo en lmedia lo regresa
              rename($ruta_str, SP_HOME."/vanas_videos/fame/lessons/video_re".$clave);
          }
      }else{
        if(strpos($clave, "us"))
            $gabriel = "INSERT INTO k_video_temp (fl_usuario,nb_archivo, fl_leccion_sp) VALUES (\"$fl_usuario\", \"$file_name_ori\", \"$clave\")";
        else
            $gabriel = "INSERT INTO k_video_temp (fl_usuario,nb_archivo) VALUES (\"$fl_usuario\", \"$file_name_ori\")";
        EjecutaQuery($gabriel);
      }

      //*** START VIDEO UPLOAD ***//

      # Creamos la carpeta del video sd
      if (!file_exists($ruta_sd)) {
        mkdir($ruta_sd, 0777, true);
      }

      # Buscamos si existe el archivo
      # En caso de que no solo va a subir el archivo
      if(file_exists($ruta_sd."/".$file_name_ori))
        unlink($ruta_sd."/".$file_name_ori);
      move_uploaded_file($tempFile, $ruta_sd."/".$file_name_ori);

      # Renombramos el archivo que se subió
      $file_name = $name_main."_sd.".$ext;
      rename($ruta_sd."/".$file_name_ori, $ruta_sd."/".$file_name);

      //*** END VIDEO UPLOAD ***//

      $result['success'] = "Upload Correct!!!";//
      $result['nombre_archivo_extension']=$file_name_ori;
  }
  else{
    $result['error'] = "You should upload another video";
  }

  echo json_encode((Object) $result);
?>
