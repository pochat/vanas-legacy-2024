<?php
  
  # Libreria de funciones
	require '../../lib/general.inc.php';
  
	# Verifica que exista una sesion valida en el cookie y la resetea
	$fl_usuario = ValidaSesion( );
  
	# Recibe la clave
  $clave = RecibeParametroNumerico('clave');
  $cl_pagina_nueva = RecibeParametroNumerico('cl_pagina_nueva');
  
  # Determina si es alta o modificacion
  if(!empty($clave))
    $permiso = PERMISO_MODIFICACION;
  else
    $permiso = PERMISO_ALTA;
  
	# Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermiso(FUNC_FIXED, $permiso)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
 
  # Recibe parametros
  $fg_error = 0;
	$nb_pagina = RecibeParametroHTML('nb_pagina');
  $ds_pagina = RecibeParametroHTML('ds_pagina');
  $ds_titulo = RecibeParametroHTML('ds_titulo');
  $tr_titulo = RecibeParametroHTML('tr_titulo');
  $ds_contenido = RecibeParametroHTML('ds_contenido');
  $tr_contenido = RecibeParametroHTML('tr_contenido');
  $fl_programa = RecibeParametroNumerico('fl_programa');
  $no_grado = RecibeParametroNumerico('no_grado');
  $archivo = RecibeParametroHTML('archivo');  
  $no_videos = RecibeParametroNumerico('no_videos');
  $archivo_a = RecibeParametroHTML('archivo_a');
  

	# Valida campos obligatorios
  if(empty($clave) AND empty($cl_pagina_nueva))
    $cl_pagina_err = ERR_REQUERIDO;
  if(empty($nb_pagina))
    $nb_pagina_err = ERR_REQUERIDO;
   
  # Valida que no exista el registro
  $row = RecuperaValor("SELECT fg_fijo FROM c_pagina WHERE cl_pagina = $cl_pagina_nueva");
  $fg_fijo = !empty($row[0])?$row[0]:NULL;
  if($fg_fijo == 0)
  {
    $row = EjecutaQuery("SELECT cl_pagina, fl_programa, no_grado FROM c_pagina 
                         WHERE cl_pagina=$cl_pagina_nueva AND fl_programa = $fl_programa AND no_grado = $no_grado");
    if(CuentaRegistros($row) > 0)
      $cl_pagina_err = ERR_DUPVAL2;
  }
  else
  {
    if(empty($clave) AND ExisteEnTabla('c_pagina', 'cl_pagina', $cl_pagina_nueva))
      $cl_pagina_err = ERR_DUPVAL;
  }
  
  # Valida enteros
  if(empty($clave) AND !empty($cl_pagina_nueva) AND !ValidaEntero($cl_pagina_nueva))
    $cl_pagina_err = ERR_ENTERO;
  if(empty($clave) AND !empty($cl_pagina_nueva) AND ($cl_pagina_nueva > MAX_SMALLINT))
    $cl_pagina_err = ERR_SMALLINT;
  
	# Regresa a la forma con error
  $fg_error = (!empty($cl_pagina_err)?$cl_pagina_err:NULL) || (!empty($nb_pagina_err)?$nb_pagina_err:NULL);
  if($fg_error) {
    echo "<html><body><form name='datos' method='post' action='".ObtenProgramaNombre(PGM_FORM)."'>\n";
    Forma_CampoOculto('clave' , $clave.'_'.$fl_programa.'_'.$no_grado);
    Forma_CampoOculto('fg_error' , $fg_error);
    Forma_CampoOculto('cl_pagina_nueva' , $cl_pagina_nueva);
    Forma_CampoOculto('cl_pagina_err' , $cl_pagina_err);
    Forma_CampoOculto('nb_pagina' , $nb_pagina);
    Forma_CampoOculto('nb_pagina_err' , $nb_pagina_err);
    Forma_CampoOculto('ds_pagina' , $ds_pagina);
    Forma_CampoOculto('ds_titulo' , $ds_titulo);
    Forma_CampoOculto('tr_titulo' , $tr_titulo);
    Forma_CampoOculto('ds_contenido' , $ds_contenido);
    Forma_CampoOculto('tr_contenido' , $tr_contenido);
    Forma_CampoOculto('fl_programa' , $fl_programa);
    Forma_CampoOculto('no_grado' , $no_grado);
    Forma_CampoOculto('no_videos' , $no_videos);
    Forma_CampoOculto('archivo_a' , $archivo_a);
    echo "\n</form>
<script>
  document.datos.submit();
</script></body></html>";
    exit;
  }
  
  # Parametros para convertir archivos mov en flv
  $parametros = ObtenConfiguracion(12);
  $ruta_tmp = $_SERVER['DOCUMENT_ROOT'].PATH_TMP;
  $ruta_str = PATH_STREAMING;
  
  # Recibe archivo de video
  // if(!empty($archivo)) {
    // $ext = strtoupper(ObtenExtensionArchivo($archivo));
    // $ds_vl_ruta = $archivo;
    
    // # Mueve el archivo subido al directorio para streaming
    // if(file_exists($ruta_str."/".$archivo))
      // unlink($ruta_str."/".$archivo);
    // rename($ruta_tmp."/".$archivo, $ruta_str."/".$archivo);
    
    // # Convierte archivos .mov a .flv
    // if($ext == "MOV" OR $ext == "MP4") {
      // $file_mov_lecture = $ruta_str."/".$archivo;
      // $file_flv = 'CAM_CONTENT_' . substr($archivo, 0, (strlen($archivo)-4)) . '.flv';
      // if(file_exists($ruta_str."/".$file_flv))
        // unlink($ruta_str."/".$file_flv);
      // $comando_1 = CMD_FFMPEG." -i \"$file_mov_lecture\" $parametros \"$ruta_str/$file_flv\"";
      // $ds_vl_ruta = $file_flv;
    // }
    
    // # Creacion de liga para servidor de streaming
    // $comando_2 = "ln -s \"".$ruta_str."/".$ds_vl_ruta."\" ".PATH_LINKS;
    
  // }
  // echo "cl_pagina_nueva=".$cl_pagina_nueva."<br>fl_programa=".$fl_programa."<br/>".$no_grado;
  // exit;
  # Actualiza o inserta el registro
  if(!empty($clave)) {
    $Query  = "UPDATE c_pagina SET nb_pagina='$nb_pagina', ds_pagina='$ds_pagina', ds_titulo='$ds_titulo', tr_titulo='$tr_titulo', ";
    $Query .= "ds_contenido='$ds_contenido', tr_contenido='$tr_contenido' ";
    $Query .= "WHERE cl_pagina=$clave ";
    $Query .= "AND fl_programa=$fl_programa ";
    $Query .= "AND no_grado=$no_grado ";
    
    $cl_pagina = $clave;
  }
  else {
    $Query  = "INSERT INTO c_pagina (cl_pagina, nb_pagina, ds_pagina, ds_titulo, tr_titulo, ds_contenido, tr_contenido, fl_programa, no_grado) ";
    $Query .= "VALUES($cl_pagina_nueva, '$nb_pagina', '$ds_pagina', '$ds_titulo', '$tr_titulo', '$ds_contenido', '$tr_contenido', $fl_programa, $no_grado)";
  
    $cl_pagina = $cl_pagina_nueva;
  }
  
  EjecutaQuery($Query);
  #echo "$Query <br>";
  
  #Inserta registros de videos nuevos
  // if(!empty($archivo))
  // {
    // $Query2 = "INSERT INTO k_video_contenido (cl_pagina, fl_programa, no_grado, ds_ruta_video) ";
    // $Query2 .= "VALUES($cl_pagina, $fl_programa, $no_grado, '$ds_vl_ruta')";
    // EjecutaQuery($Query2);
    #echo "$Query2 <br>";
  // }*/
  # VIDEOS
  if(empty($clave)){
    $Queryx = "SELECT no_orden, nb_archivo, ds_title_vid FROM k_vid_content_temp WHERE fl_clave=".$fl_usuario." AND fl_programa=".$fl_usuario." AND no_grado=".$fl_usuario." ORDER BY no_orden";
    $rsx = EjecutaQuery($Queryx);
    $tot_reg = CuentaRegistros($rsx);
    # Carpetas orginiales
    $ruta1 = VID_CAM_STU_LIB."/video_us".$fl_usuario."_us".$fl_usuario."_us".$fl_usuario;
    # Carpeta nueva
    $ruta1_new = VID_CAM_STU_LIB."/video_".$cl_pagina_nueva."_".$fl_programa."_".$no_grado;
    if(!empty($tot_reg)){
      # Reemplazamos la ruta
      rename($ruta1, $ruta1_new);
      // echo "<div>Ruta inicial:<br>
      // Ruta original: $ruta1 <br>
      // Ruta original nueva: $ruta1_new <br></div>";
      for($i=0;$rowx=RecuperaRegistro($rsx);$i++){
        $no_orden = $rowx[0];
        $nb_archivo = $rowx[1];
        $ds_title_vid = $rowx[2];
        $ruta2 = $ruta1_new."/video_".$no_orden;
        
        # Insertamos los registros en la tabla original
        $Query22  = "INSERT INTO k_video_contenido (cl_pagina, fl_programa, no_grado, ds_ruta_video, ds_title_vid) ";
        $Query22 .= "VALUES($cl_pagina_nueva, $fl_programa, $no_grado, '$nb_archivo', '".$ds_title_vid."')";
        $fl_vid_new = EjecutaInsert($Query22);
        $ruta2_new = $ruta1_new."/video_".$fl_vid_new;
        # Ruta para los videos SD
        $ruta_sd = $ruta2_new."/video_".$no_orden."_sd";
        $ruta_sd_new = $ruta2_new."/video_".$fl_vid_new."_sd";
        # Ruta para los videos HD
        $ruta_hd = $ruta2_new."/video_".$no_orden."_hd";
        $ruta_hd_new = $ruta2_new."/video_".$fl_vid_new."_hd";
        
        # Ruta para el archivo
        $output_hd = $ruta_hd_new."/output".$fl_vid_new.".php";
        $output_sd = $ruta_sd_new."/output".$fl_vid_new.".php";
        // echo "<div style='padding-left:30px;'>Rutas de los videos: <br>
        // Ruta 2 original: $ruta2 <br/> 
        // Ruta 2 nueva: $ruta2_new <br/></div>";
        // echo "<div style='padding-left:50px; color:blue;'>Rutas HD: <br>
        // Ruta HD original: $ruta_hd <br/> 
        // Ruta HD nueva: $ruta_hd_new <br/>
        // <p style='color:yellow;'>$output_hd</p>
        // </div>";
        // echo "<div style='padding-left:50px; color:green;'>Rutas SD: <br>
        // Ruta HD original: $ruta_sd <br/> 
        // Ruta HD nueva: $ruta_sd_new <br/>
        // <p style='color:yellow;'>$output_sd</p>
        // </div>";
        # Renombramos las carpteas
        rename($ruta2, $ruta2_new);
        rename($ruta_hd, $ruta_hd_new);
        rename($ruta_sd, $ruta_sd_new);
        
        
        ##  Comandos ###
        $attr_comando = "-s hd1080 -c:v libx264 -crf 23 -c:a aac -strict -2 -c:a:0 copy -c:a:1 copy -c:s copy -hls_list_size 0 -hls_segment_size 500000 ";
        $mp4_hd = $ruta_hd_new."/".$nb_archivo.".mp4";
        $m3u8_hd = $ruta_hd_new."/".$nb_archivo.".m3u8";
        $comando_hd = VIDEOS_CMD_HLS." -i $mp4_hd $attr_comando $m3u8_hd 1>$output_hd 2>&1 ";
        $mp4_sd = $ruta_sd_new."/".$nb_archivo.".mp4";
        $m3u8_sd = $ruta_sd_new."/".$nb_archivo.".m3u8";
        $comando_sd = VIDEOS_CMD_HLS." -i $mp4_sd $attr_comando $m3u8_sd 1>$output_sd 2>&1 ";
        // echo "<div style='padding-left:50px; color:red;'>COMANDOS<br>
        // HDHDHDHDHDHD: $comando_hd <br/> 
        // SDDSDSDSDSD: $comando_sd <br/>
        // <p style='color:yellow;'>$output_sd</p>
        // </div>";
        
        ## Ejecutamos los comandos ####
        exec($comando_hd." >> /dev/null &");
        exec($comando_sd." >> /dev/null &");
      }
      
    }    
  }
  else{
    $Queryx = "SELECT no_orden, nb_archivo, ds_title_vid FROM k_vid_content_temp WHERE fl_clave=".$clave." AND fl_programa=".$fl_programa." AND no_grado=".$no_grado." ORDER BY no_orden";
    $rsx = EjecutaQuery($Queryx);
    $tot_reg = CuentaRegistros($rsx);
    # Carpeta
    // $ruta1 = VID_CAM_STU_LIB."/video_".$clave."_".$fl_programa."_".$no_grado;
    $ruta1 = VID_CAM_STU_LIB;
    if(!empty($tot_reg)){
      # Reemplazamos la ruta
      // rename($ruta1, $ruta1_new);
      // echo "<div>Ruta inicial:<br>
      // Ruta original: $ruta1 <br></div>";
      for($i=0;$rowx=RecuperaRegistro($rsx);$i++){
        $no_orden = $rowx[0];
        $nb_archivo = $rowx[1];
        $ds_title_vid = $rowx[2];
        $ruta2 = $ruta1."/video_".$no_orden;
        
        # Insertamos los registros en la tabla original
        $Query22  = "INSERT INTO k_video_contenido (cl_pagina, fl_programa, no_grado, ds_ruta_video, ds_title_vid) ";
        $Query22 .= "VALUES($cl_pagina, $fl_programa, $no_grado, '$nb_archivo', '".$ds_title_vid."')";
        $fl_vid_new = EjecutaInsert($Query22);
        $ruta2_new = $ruta1."/video_".$fl_vid_new;
        # Ruta para los videos SD
        $ruta_sd = $ruta2_new."/video_".$no_orden."_sd";
        $ruta_sd_new = $ruta2_new."/video_".$fl_vid_new."_sd";
        # Ruta para los videos HD
        $ruta_hd = $ruta2_new."/video_".$no_orden."_hd";
        $ruta_hd_new = $ruta2_new."/video_".$fl_vid_new."_hd";
        
        # Ruta para el archivo
        $output_hd = $ruta_hd_new."/output".$fl_vid_new.".php";
        $output_sd = $ruta_sd_new."/output".$fl_vid_new.".php";
        // echo "<div style='padding-left:30px;'>Rutas de los videos: <br>
        // Ruta 2 original: $ruta2 <br/> 
        // Ruta 2 nueva: $ruta2_new <br/></div>";
        // echo "<div style='padding-left:50px; color:blue;'>Rutas HD: <br>
        // Ruta HD original: $ruta_hd <br/> 
        // Ruta HD nueva: $ruta_hd_new <br/>
        // <p style='color:yellow;'>$output_hd</p>
        // </div>";
        // echo "<div style='padding-left:50px; color:green;'>Rutas SD: <br>
        // Ruta HD original: $ruta_sd <br/> 
        // Ruta HD nueva: $ruta_sd_new <br/>
        // <p style='color:yellow;'>$output_sd</p>
        // </div>";
        # Renombramos las carpteas
        rename($ruta2, $ruta2_new);
        rename($ruta_hd, $ruta_hd_new);
        rename($ruta_sd, $ruta_sd_new);
        
        
        ##  Comandos ###
        $attr_comando = "-s hd1080 -c:v libx264 -crf 23 -c:a aac -strict -2 -c:a:0 copy -c:a:1 copy -c:s copy -hls_list_size 0 -hls_segment_size 500000 ";
        $mp4_hd = $ruta_hd_new."/".$nb_archivo.".mp4";
        $m3u8_hd = $ruta_hd_new."/".$nb_archivo.".m3u8";
        $comando_hd = VIDEOS_CMD_HLS." -i $mp4_hd $attr_comando $m3u8_hd 1>$output_hd 2>&1 ";
        $mp4_sd = $ruta_sd_new."/".$nb_archivo.".mp4";
        $m3u8_sd = $ruta_sd_new."/".$nb_archivo.".m3u8";
        $comando_sd = VIDEOS_CMD_HLS." -i $mp4_sd $attr_comando $m3u8_sd 1>$output_sd 2>&1 ";
        // echo "<div style='padding-left:50px; color:red;'>COMANDOS<br>
        // HDHDHDHDHDHD: $comando_hd <br/> 
        // SDDSDSDSDSD: $comando_sd <br/>
        // <p style='color:yellow;'>$output_sd</p>
        // </div>";
        
        ## Ejecutamos los comandos ####
        exec($comando_hd." >> /dev/null &");
        exec($comando_sd." >> /dev/null &");
      }
      
    }
  }
  # ELiminamos los registros temporales
  EjecutaQuery("DELETE FROM k_vid_content_temp WHERE fl_usuario=".$fl_usuario." AND fg_fame='0'");
  
  # Si recibe un archivo de las lecciones lo copiamos
  if(!empty($archivo_a)){
    # Obtenemos la ruta del video de la leccion
    $ruta_leccion_hd = VID_CAM_LEC."/video_".$archivo_a."/video_".$archivo_a."_vl_hd";
    $ruta_leccion_sd = VID_CAM_LEC."/video_".$archivo_a."/video_".$archivo_a."_vl_sd";    
    # Ruta del student library
    $ruta_library = VID_CAM_STU_LIB."/video_".$cl_pagina."_".$fl_programa."_".$no_grado;
    // echo "leccion HD: ".$ruta_leccion_hd;
    // echo "<br>leccion SD: ".$ruta_leccion_sd;
    // echo "<br>library:".$ruta_library."</br>"; 
    # Obtenemos el nombre del video de lessons
    $rowe = RecuperaValor("SELECT ds_vl_ruta FROM c_leccion WHERE fl_leccion=".$archivo_a);
    $ds_vl_ruta = $rowe[0];
    # Nombre para todos los archivos
    $name_ori = explode(".", $ds_vl_ruta);
    $nb_archivo = $name_ori[0];
    # Insertamos el registro para el nvevo video
    $Query22  = "INSERT INTO k_video_contenido (cl_pagina, fl_programa, no_grado, ds_ruta_video, ds_progreso) ";
    $Query22 .= "VALUES($cl_pagina, $fl_programa, $no_grado, '$nb_archivo', '100')";
    $fl_video_contenido = EjecutaInsert($Query22);
    $ruta_video_cont = $ruta_library."/video_".$fl_video_contenido;
    // echo "<br>creamos: <br>".$ruta_video_cont;
    # Creamos la carpeta student library
    if (!file_exists($ruta_video_cont)) {
      mkdir($ruta_video_cont, 0777, true);
      chmod($ruta_video_cont, 0777);
    }
    # Copiamos las carpeta de lessons a library
    // echo "<br>COMANDOS <br>";
    // echo '<br> HD  '.$ruta_leccion_hd.' '.$ruta_video_cont;
    exec("cp -a ".$ruta_leccion_hd." ".$ruta_video_cont);
    // echo '<br> SD  '.$ruta_leccion_sd.' '.$ruta_video_cont;
    exec("cp -a ".$ruta_leccion_sd." ".$ruta_video_cont);
    
    # Cambiamos nombres de las carpetas copiadas x el nuevo fl_video_contenido
    // echo "<BR>CAMBIAMOS NOMBRES: <BR>";
    $rutaold_hd = $ruta_video_cont."/video_".$archivo_a."_vl_hd";
    $rutaold_sd = $ruta_video_cont."/video_".$archivo_a."_vl_sd";
    // echo "Ruta11111<br> ".$rutaold_hd."<br>".$rutaold_sd."<br/>";
    $rutanew_hd = $ruta_video_cont."/video_".$fl_video_contenido."_hd";
    $rutanew_sd = $ruta_video_cont."/video_".$fl_video_contenido."_sd";
    // echo "Ruta2nueva<br> ".$rutanew_hd."<br>".$rutanew_sd;
    exec("mv ".$rutaold_hd." ".$rutanew_hd); 
    chmod($rutanew_hd, 0777);
    exec("mv ".$rutaold_sd." ".$rutanew_sd); 
    chmod($rutanew_sd, 0777);
    
    
    // echo "<br> cambiamos los nombres de los archivos output:<br>";
    $outputold_hd = $rutanew_hd."/output".$archivo_a."_hd.php";
    $outputold_sd = $rutanew_sd."/output".$archivo_a."_sd.php";
    // echo "<br>output viejitos:<br>".$outputold_hd."<br>".$outputold_sd;
    $outputnew_hd = $rutanew_hd."/output".$fl_video_contenido.".php";
    $outputnew_sd = $rutanew_sd."/output".$fl_video_contenido.".php";
    // echo "<br>output Nuevos:<br>".$outputnew_hd."<br>".$outputnew_sd;
    exec("mv ".$outputold_hd." ".$outputnew_hd); 
    chmod($outputnew_hd, 0777);
    exec("mv ".$outputold_sd." ".$outputnew_sd); 
    chmod($outputnew_sd, 0777);    

  }


  # Convierte archivo de video, crea liga y elimina mov
  // if(!empty($comando_1))
    // exec($comando_1);
  // if(!empty($comando_2) AND FG_PRODUCCION)
    // exec($comando_2);
  // if(!empty($file_mov_lecture))
    // unlink($file_mov_lecture);
  
  # Redirige al listado
  header("Location: ".ObtenProgramaBase( ));
 
?>