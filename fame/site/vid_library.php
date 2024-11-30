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
  $clave = RecibeParametroNumerico('clave');
  $fl_programa = RecibeParametroNumerico('fl_programa');
  $no_grado = RecibeParametroNumerico('no_grado');  
  $fl_usuario = RecibeParametroNumerico('usuario');
  $title_video = RecibeParametroHTML('title_video');
  if(empty($title_video))
  $title_video = "Untitled";
  

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
    
      #Recuperamos el consecutivo.
      $rt = RecuperaValor("SELECT MAX(no_orden) FROM k_vid_content_temp WHERE fl_clave=".$fl_programa." AND fl_programa=".$fl_programa." AND fg_fame='1'");
	  $no_orden = $rt[0]+1;

	  # Inserta datos        
	  $gabriel = "INSERT INTO k_vid_content_temp (fl_usuario, nb_archivo, fl_clave, fl_programa, no_grado, no_orden, fg_fame, ds_title_vid
	  ) VALUES (".$fl_usuario.", '".$file_name_ori."', ".$fl_programa.", ".$fl_programa.",0, ".$no_orden.", '1', '".$title_video."')";
	  $fl_video_conten_temp=EjecutaInsert($gabriel);
	
	  #Inserta datos.
      $Queryk_video="INSERT INTO k_video_contenido_sp(cl_pagina_sp,fl_programa_sp,ds_ruta_video,ds_progreso,ds_title_vid,fl_vid_contet_temp,fe_creacion)VALUES($fl_programa,$fl_programa,'$file_name_ori','0','$title_video',$fl_video_conten_temp,CURRENT_TIMESTAMP) ";
      $fl_video_contenido=EjecutaInsert($Queryk_video);

      
      # Ruta para los videos
      # tmp
      $ruta_tmp = SP_HOME."/AD3M2SRC4/tmp/";
      $ruta_str1 = $_SERVER[DOCUMENT_ROOT]."/fame/site/uploads/".$fl_instituto."/attachments/student_library/video_".$fl_video_contenido."/video_".$no_orden."";
        # Creamos la carpeta student library
      if (!file_exists($ruta_str1)) {
          mkdir($ruta_str1, 0777, true);        
        }
     # Cambiamos los permisos de la carpeta 
     chmod($ruta_str1, 0777);

     #Copiamos el archivo a ruta final
     move_uploaded_file($tempFile, $ruta_str1."/".$file_name_ori);

     #reombramos el archivo a SD
     rename($ruta_str1."/".$file_name_ori."", $ruta_str1."/".$name_main."_sd.".$ext."");
     
     $file_name_hls = $ruta_str1."/".$name_main."_sd.m3u8";
     $output = $ruta_str1."/output.txt";

     $file_name_origen=$ruta_str1."/".$name_main."_sd.".$ext."";


     #Comando para generar imagen caratula del video
     $name_img_sd = $ruta_str1."/img_%d.png";
     $comando_image_sd ="/usr/bin/ffmpeg -i '$file_name_origen' -ss 00:00:01 -vframes 1 $name_img_sd";
     exec($comando_image_sd." >> /dev/null &");

     $caratula_imagen=ObtenConfiguracion(116)."fame/site/uploads/".$fl_instituto."/attachments/student_library/video_".$fl_video_contenido."/video_".$no_orden."/img_1.png?".$fl_video_contenido;



     # Comando para convertir el video a m3u8
     $comando_orig_to_hls_sd  = "/usr/bin/ffmpeg -i '$file_name_origen' -profile:v baseline -level 3.0 -vf scale=-1:1080 -start_number 0 -hls_time 10 -hls_list_size 0 -f hls $file_name_hls ";
     $comando_orig_to_hls_sd .= " 1>$output 2>&1 ";
     exec($comando_orig_to_hls_sd." >> /dev/null &");

     
     #Comando para generarlos thumbails del video linea de tiempo.
     $params=ObtenConfiguracion(135);
     exec(CMD_FFMPEG." -i $file_name_origen $params $ruta_str1/img%d.jpg >> /dev/null &");


	  
      $result['success'] = "Correct!!!";
      $result['valores'] = 
      array(
      "archivo_subio" => $name_ori,
      "ruta_str1" => $ruta_str1,
      "comnado_video_hls" => $comando_orig_to_hls_sd,
      "comando_thumbnails_imagen" => $comando_image_sd,
      "gabriel" => $gabriel,
      "clave" => $clave,
      "caratula_imagen"=>$caratula_imagen,
      "fl_video_contenido"=>$fl_video_contenido,
      "title_video" => $title_video
      );
  }
  else{
    $result['error'] = "You should upload file ";
  }
  
  echo json_encode((Object) $result);
  
?>
