<?php
  
  # Libreria de funciones
  require '../../lib/general.inc.php';
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  $fl_usuario = ValidaSesion( );
  
  # Recibe la clave
  $clave = RecibeParametroNumerico('clave');
  
  # Determina si es alta o modificacion
  if(!empty($clave))
    $permiso = PERMISO_MODIFICACION;
  else
    $permiso = PERMISO_ALTA;
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermiso(FUNC_MEDIA, $permiso)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Recibe parametros
  $fg_error = 0;
  $fl_programa = RecibeParametroNumerico('fl_programa');
  $no_grado = RecibeParametroNumerico('no_grado');
  $no_semana = RecibeParametroNumerico('no_semana');
  $ds_titulo = RecibeParametroHTML('ds_titulo');
  $ds_leccion = RecibeParametroHTML('ds_leccion');
  $ds_vl_ruta = RecibeParametroHTML('ds_vl_ruta');
  $ds_vl_duracion = RecibeParametroHTML('ds_vl_duracion');
  $fe_vl_alta = RecibeParametroHTML('fe_vl_alta');
  $fg_animacion = RecibeParametroBinario('fg_animacion');
  $fg_ref_animacion = RecibeParametroBinario('fg_ref_animacion');
  $no_sketch = RecibeParametroNumerico('no_sketch');
  $fg_ref_sketch = RecibeParametroBinario('fg_ref_sketch');
  $archivo = RecibeParametroHTML('archivo');
  $archivo_a = RecibeParametroHTML('archivo_a');
  $ds_as_ruta = RecibeParametroHTML('ds_as_ruta');
  $ds_as_duracion = RecibeParametroHTML('ds_as_duracion');
  $fe_as_alta = RecibeParametroHTML('fe_as_alta');
  $archivo1 = RecibeParametroHTML('archivo1');
  $archivo1_a = RecibeParametroHTML('archivo1_a');  
  
  # Valor de rubric
  $no_val_rub = RecibeParametroNumerico('no_val_rub');
  $no_ter_co = RecibeParametroNumerico('no_ter_co');
  $sum_val_grade = RecibeParametroNumerico('sum_val_grade');
  
  # Valores de los videos
  $fg_reset_video_drop_vl = RecibeParametroBinario('fg_reset_video_drop_vl');
  $fg_reset_video_drop_vf = RecibeParametroBinario('fg_reset_video_drop_vf');
  $fg_upload_videos = RecibeParametroNumerico('fg_upload_videos');
  $fg_tipo_video = RecibeParametroHTML('fg_tipo_video');
  $ds_progress_video_vl = RecibeParametroHTML('ds_progress_video_vl');
  $ds_progress_video_vf = RecibeParametroHTML('ds_progress_video_vf');
  
  
  # Valida campos obligatorios
  if(empty($fl_programa))
    $fl_programa_err = ERR_REQUERIDO;
  if(empty($no_grado))
    $no_grado_err = ERR_REQUERIDO;
  if(empty($no_semana))
    $no_semana_err = ERR_REQUERIDO;
  if(empty($ds_titulo))
    $ds_titulo_err = ERR_REQUERIDO;
  if(empty($ds_leccion))
    $ds_leccion_err = ERR_REQUERIDO;
  
  # Valida enteros
  if(empty($no_grado_err) AND !ValidaEntero($no_grado))
    $no_grado_err = ERR_ENTERO;
  if(empty($no_grado_err) AND $no_grado > MAX_TINYINT)
    $no_grado_err = ERR_TINYINT;
  if(empty($no_semana_err) AND !ValidaEntero($no_semana))
    $no_semana_err = ERR_ENTERO;
  if(empty($no_semana_err) AND $no_semana > MAX_TINYINT)
    $no_semana_err = ERR_TINYINT;
  if($no_sketch > MAX_TINYINT)
    $no_sketch_err = ERR_TINYINT;
  
  /* Validacion de rubric */
  # 1- Validamos si NO existenten criterios y el rubric tiene valor
  if($no_val_rub > 0){
      $cont_criterios = RecuperaValor("SELECT COUNT(1) FROM k_criterio_programa WHERE fl_programa = $clave");
      if(empty($cont_criterios[0]))
          $no_val_rub_err = 1; // No hay registros en tabla
  }
  
  # 2- Validamos SI existenten criterios y el rubric NO tiene valor
  $cont_criterios = RecuperaValor("SELECT COUNT(1) FROM k_criterio_programa WHERE fl_programa = $clave");
  if(($cont_criterios[0]) AND empty($no_val_rub))
      $no_val_rub_err = 2; // Hay registros en tabla, pero el rubric no tiene valor
  
  # 3- Validamos que todos los criterios tengan un valor 
  $cont_criterios = RecuperaValor("SELECT COUNT(1) FROM k_criterio_programa WHERE fl_programa = $clave AND no_valor IS NULL");
  if(($cont_criterios[0]))
      $no_val_rub_err = 3; // Existen criterios sin valor asignado
  
  # 4- Validamos el max grade
  // Es mayor a 100
  if($sum_val_grade > 100)
      $no_max_grade_err = 1;
  
  // Valida el valor de los criterios
  if($sum_val_grade != 100 and $sum_val_grade > 0)
      $no_max_grade_err = 2;
  
  // Comprobamos error para marcar tab 
  if($no_val_rub_err OR $no_max_grade_err)
      $tab_rubric_err = 1;
  
  
  
  # Verifica que no exista la leccion
  if(empty($fl_programa_err) AND empty($no_grado_err) AND empty($no_semana_err)) {
    $Query  = "SELECT count(1) ";
    $Query .= "FROM c_leccion ";
    $Query .= "WHERE fl_programa=$fl_programa ";
    $Query .= "AND no_grado=$no_grado ";
    $Query .= "AND no_semana=$no_semana ";
    if(!empty($clave))
      $Query .= "AND fl_leccion<>$clave";
    $row = RecuperaValor($Query);
    if(!empty($row[0]))
      $no_semana_err = 109; # Existing lesson found for this program.
  }
  
  
  # Regresa a la forma con error
  $fg_error = $fl_programa_err || $no_grado_err || $no_semana_err || $ds_titulo_err || $ds_leccion_err || $no_sketch_err
              || $no_val_rub_err  || $no_max_grade_err  || $tab_rubric_err;
  
  if($fg_error) {
    echo "<html><body><form name='datos' method='post' action='".ObtenProgramaNombre(PGM_FORM)."'>\n";
    Forma_CampoOculto('clave' , $clave);
    Forma_CampoOculto('fg_error' , $fg_error);
    Forma_CampoOculto('fl_programa' , $fl_programa);
    Forma_CampoOculto('fl_programa_err' , $fl_programa_err);
    Forma_CampoOculto('no_grado' , $no_grado);
    Forma_CampoOculto('no_grado_err' , $no_grado_err);
    Forma_CampoOculto('no_semana' , $no_semana);
    Forma_CampoOculto('no_semana_err' , $no_semana_err);
    Forma_CampoOculto('ds_titulo' , $ds_titulo);
    Forma_CampoOculto('ds_titulo_err' , $ds_titulo_err);
    Forma_CampoOculto('ds_leccion' , $ds_leccion);
    Forma_CampoOculto('ds_leccion_err' , $ds_leccion_err);
    Forma_CampoOculto('ds_vl_ruta' , $ds_vl_ruta);
    Forma_CampoOculto('ds_vl_duracion' , $ds_vl_duracion);
    Forma_CampoOculto('fe_vl_alta' , $fe_vl_alta);
    Forma_CampoOculto('fg_animacion' , $fg_animacion);
    Forma_CampoOculto('fg_ref_animacion' , $fg_ref_animacion);
    Forma_CampoOculto('no_sketch' , $no_sketch);
    Forma_CampoOculto('no_sketch_err' , $no_sketch_err);
    Forma_CampoOculto('fg_ref_sketch' , $fg_ref_sketch);
    Forma_CampoOculto('ds_as_ruta' , $ds_as_ruta);
    Forma_CampoOculto('ds_as_duracion' , $ds_as_duracion);
    Forma_CampoOculto('fe_as_alta' , $fe_as_alta);
    Forma_CampoOculto('archivo_a' , $archivo_a);
    Forma_CampoOculto('archivo1_a' , $archivo1_a);
    Forma_CampoOculto('ds_progress_video_vl' , $ds_progress_video_vl);
    Forma_CampoOculto('ds_progress_video_vf' , $ds_progress_video_vf);
    
    # Rubric
    Forma_CampoOculto('no_ter_co' , $no_ter_co);
    Forma_CampoOculto('no_val_rub' , $no_val_rub);
    Forma_CampoOculto('no_val_rub_err' , $no_val_rub_err);
    Forma_CampoOculto('no_max_grade_err' , $no_max_grade_err);
    Forma_CampoOculto('sum_val_grade' , $sum_val_grade);
    Forma_CampoOculto('tab_rubric_err' , $tab_rubric_err);
    Forma_CampoOculto('tab_description_err' , $tab_description_err);
    
    
    echo "\n</form>
<script>
  document.datos.submit();
</script></body></html>";
    exit;
  }
  if(VIDEOS_FLASH==true){
  # Obtenemos la extesion del archivo
  $name_ori = explode(".", $archivo); 
    
  # Inicializa variables para procesamiento de archivos
  $parametros = ObtenConfiguracion(12); // Para convertir archivos mov en flv
  $ruta_tmp = $_SERVER[DOCUMENT_ROOT].PATH_TMP;
  $ruta = PATH_STREAMING;
  
  # Recibe archivo de lecture
  if(!empty($archivo)) {    
    $ds_vl_ruta = $archivo;
    # Mueve el archivo subido al directorio para streaming
    if(file_exists($ruta."/".$archivo))
      unlink($ruta."/".$archivo);
    rename($ruta_tmp."/".$archivo, $ruta."/".$archivo);

    # Si el archivo es ext mov lo convertira a mp4
    if($name_ori[1]=="mov" || $name_ori[1]=="MOV" || $name_ori[1]=="MP4" || $name_ori[1]=="mp4" ){
      $comando_mov_flv = CMD_FFMPEG." -i $ruta/$archivo $parametros $ruta/$name_ori[0].flv";      
    }
    
    # Creacion de liga para servidor de streaming
    $comando_2 = "ln -s \"".$ruta."/$name_ori[0].flv\" ".PATH_LINKS;
    
    # Prepara la fecha de alta del archivo
    $fe_vl_alta = "CURRENT_TIMESTAMP";
    
  }
  else {
    if(!empty($archivo_a))
    {
      $row = RecuperaValor("SELECT ds_vl_ruta FROM c_leccion WHERE fl_leccion = $archivo_a");
      $ds_vl_ruta = $row[0];
    }
    if(empty($ds_vl_ruta)) {
      $ds_vl_duracion = "";
      $fe_vl_alta = "NULL";
    }
    else
      $fe_vl_alta = "fe_vl_alta";
  }
  
  # Recibe archivo de brief
  if(!empty($_FILES['archivo1']['tmp_name'])) {
    $ds_as_ruta = $_FILES['archivo1']['name'];
    
    # Mueve el archivo subido al directorio para streaming
    if(file_exists($ruta."/".$ds_as_ruta))
      unlink($ruta."/".$ds_as_ruta);
    move_uploaded_file($_FILES['archivo1']['tmp_name'], $ruta."/".$ds_as_ruta);
    
    # Creacion de liga para servidor de streaming
    $comando_4 = "ln -s \"".$ruta."/".$ds_as_ruta."\" ".PATH_LINKS;
    
    # Prepara la fecha de alta del archivo
    $fe_as_alta = "CURRENT_TIMESTAMP";
  }
  else {
    if(!empty($archivo1_a))
    {
      $row1 = RecuperaValor("SELECT ds_as_ruta FROM c_leccion WHERE fl_leccion = $archivo1_a");
      $ds_as_ruta = $row1[0];
    }
    if(empty($ds_as_ruta)) {
      $ds_as_duracion = "";
      $fe_as_alta = "NULL";
    }
    else
      $fe_as_alta = "fe_as_alta";
  }
  }
  else{
    # Recibe archivo de lecture
    if(!empty($archivo))
      $fe_vl_alta = "CURRENT_TIMESTAMP";
    else
      $fe_vl_alta = "fe_vl_alta";
    # Recibe archivo de brief
    if(!empty($archivo_a))
      $fe_as_alta = "CURRENT_TIMESTAMP";
    else
      $fe_as_alta = "fe_as_alta";
  
  }
  # Inserta o actualiza el registro
  if(empty($clave)) {
    $Query  = "INSERT INTO c_leccion (fl_programa, no_grado, no_semana, ds_titulo, ds_leccion, ds_vl_ruta, ds_vl_duracion, fe_vl_alta, ";
    $Query .= "ds_as_ruta, ds_as_duracion, fe_as_alta, fg_animacion, fg_ref_animacion, no_sketch, fg_ref_sketch,no_valor_rubric) ";
    $Query .= "VALUES($fl_programa, $no_grado, $no_semana, '$ds_titulo', '$ds_leccion', '$ds_vl_ruta', '$ds_vl_duracion', CURRENT_TIMESTAMP, ";
    $Query .= "'$ds_as_ruta', '$ds_as_duracion', CURRENT_TIMESTAMP, '$fg_animacion', '$fg_ref_animacion', $no_sketch, '$fg_ref_sketch',$no_val_rub) ";
    $fl_leccion = EjecutaInsert($Query);
  }
  else {    
    $Query  = "UPDATE c_leccion SET fl_programa=$fl_programa, no_grado=$no_grado, no_semana=$no_semana, ds_titulo='$ds_titulo', ";
    $Query .= "ds_leccion='$ds_leccion', ds_vl_ruta='$ds_vl_ruta', ds_vl_duracion='$ds_vl_duracion', fe_vl_alta=$fe_vl_alta, ";
    $Query .= "ds_as_ruta='$ds_as_ruta', ds_as_duracion='$ds_as_duracion', fe_as_alta=$fe_as_alta, ";
    $Query .= "fg_animacion='$fg_animacion', fg_ref_animacion='$fg_ref_animacion', no_sketch=$no_sketch, fg_ref_sketch='$fg_ref_sketch',no_valor_rubric=$no_val_rub ";
    $Query .= "WHERE fl_leccion=$clave";
    EjecutaQuery($Query);
  }  
  
  # Si recibe un parametro de que setrata de videos
  if(VIDEOS_FLASH==false){
  if(!empty($fg_upload_videos) || $fg_reset_video_drop_vl==1 || $fg_reset_video_drop_vf==1 || ($fg_reset_video_drop_vl==1 && $fg_reset_video_drop_vf==1)){    
    # Tiene clave
    if(!empty($clave)){
      # Si hay algun video 
      $Query1 = "SELECT fl_video_temp, ds_type, nb_archivo FROM k_video_temp WHERE fl_usuario=$fl_usuario AND fl_leccion_sp=$clave AND fg_campus='1' ";
      $row1 = RecuperaValor($Query1);
      $video_ext = $row1[0];
      $ds_type = $row1[1];
      $ds_vl_ruta = $row1[2];    
      $ds_as_ruta = $row1[2];
      if(!empty($video_ext) || $fg_reset_video_drop_vl==1 || $fg_reset_video_drop_vf==1 || ($fg_reset_video_drop_vl==1 && $fg_reset_video_drop_vf==1)){

        # Tambien eliminamos la carpeta que se creo de remplzao con el video anterior       
        // if($fg_reset_video_drop_vl==0 && $fg_reset_video_drop_vf==0){
          // $row5 = RecuperaValor("SELECT nb_archivo, ds_type FROM k_video_temp WHERE fl_usuario=$fl_usuario AND fl_leccion_sp=".$clave." AND fg_campus='1'");
          // $ds_vl_ruta = $row5[0];    
          // $ds_as_ruta = $row5[0];
          // if($ds_type=="VL")
            // eliminarDirec(VID_CAM_LEC."/video_re".$clave);
          // else
            // eliminarDirec(VID_CAM_BREF."/video_re".$clave);
        // }
        
        # Cambiamos los nombres de las carpetas
        if($ds_type=="VL"){
          $ruta_str_2 = VID_CAM_LEC."/video_".$clave;
          $campo = "ds_vl_ruta";
        }
        else{
          $ruta_str_2 = VID_CAM_BREF."/video_".$clave;
          $campo = "ds_as_ruta";
        }
        # Si quiere resetear los dos videos al mismo tiempo
        if($fg_reset_video_drop_vl==1 && $fg_reset_video_drop_vf==1){
          ## Inicia el proceso del VL SD Y DH ######
          # SD VL
          $ruta_vl_sd_2 = $ruta_str_2."/video_".$clave."_vl_sd";        
          $file_name_vl = array_shift(explode('.',$ds_vl_ruta));        
          # Nombre del archivo m3u8      
          $file_name_mp4_vl = $ruta_vl_sd_2."/".$file_name_vl.".mp4";
          $file_name_hls_vl = $ruta_vl_sd_2."/".$file_name_vl.".m3u8";
          $output_sd_vl = $ruta_vl_sd_2."/output".$clave."_sd.php";
          # Comando para convertir el archivo mp4 a m3u8
          $comando_mp4_to_hls_sd_vl  = VIDEOS_CMD_HLS." -i $file_name_mp4_vl  -s hd1080 -c:v libx264 -crf 23 -c:a aac -strict -2 -c:a:0 copy -c:a:1 copy -c:s copy -hls_list_size 0 -hls_segment_size 500000 $file_name_hls_vl ";
          $comando_mp4_to_hls_sd_vl .= " 1>$output_sd_vl 2>&1 ";
          # HD VL
          $ruta_vl_hd_2 = $ruta_str_2."/video_".$clave."_vl_hd";
          # Nombre del archivo m3u8      
          $file_name_mp4_vl_hd = $ruta_vl_hd_2."/".$file_name_vl.".mp4";
          $file_name_hls_vl_hd = $ruta_vl_hd_2."/".$file_name_vl.".m3u8";
          # Comando para convertir el archivo mp4 a m3u8
          $comando_mp4_to_hls_hd_vl  = VIDEOS_CMD_HLS." -i $file_name_mp4_vl_hd -s hd1080 -c:v libx264 -crf 23 -c:a aac -strict -2 -c:a:0 copy -c:a:1 copy -c:s copy -hls_list_size 0 -hls_segment_size 500000 $file_name_hls_vl_hd ";
          # Si recibe comando lo ejecutara
          # Recordando que para recibir un comando 
          if(!empty($comando_mp4_to_hls_sd_vl) && !empty($comando_mp4_to_hls_hd_vl)){
            # Ejecutamos el comando background
            exec($comando_mp4_to_hls_sd_vl." >> /dev/null &");
            exec($comando_mp4_to_hls_hd_vl." >> /dev/null &");
            # comando para obtener la imagen del video
            $name_img = $ruta_vl_sd_2."/img_%d.png";
            $mp4 =  $ruta_vl_sd_2."/".$file_name_vl.".mp4";
            $comando_image = VIDEOS_CMD_HLS." -i $mp4 -ss 00:00:02 -vframes 1 $name_img";
            exec($comando_image." >> /dev/null &");          
          }
          ## Finaliza el proceso del VL SD Y DH ######
          #################################################
          ## Inicializa el proceso del VF SD Y DH ######
          # SD VL
          $ruta_vf_sd_2 = $ruta_str_2."/video_".$clave."_vb_sd";        
          $file_name_vf = array_shift(explode('.',$ds_as_ruta));        
          # Nombre del archivo m3u8      
          $file_name_mp4_vf = $ruta_vf_sd_2."/".$file_name_vf.".mp4";
          $file_name_hls_vf = $ruta_vf_sd_2."/".$file_name_vf.".m3u8";
          $output_sd_vf = $ruta_vf_sd_2."/output".$clave."_sd.php";
          # Comando para convertir el archivo mp4 a m3u8
          $comando_mp4_to_hls_sd_vf  = VIDEOS_CMD_HLS." -i $file_name_mp4_vf  -s hd1080 -c:v libx264 -crf 23 -c:a aac -strict -2 -c:a:0 copy -c:a:1 copy -c:s copy -hls_list_size 0 -hls_segment_size 500000 $file_name_hls_vf ";
          $comando_mp4_to_hls_sd_vf .= " 1>$output_sd_vf 2>&1 ";
          # HD VL
          $ruta_vf_hd_2 = $ruta_str_2."/video_".$clave."_vb_hd";
          # Nombre del archivo m3u8      
          $file_name_mp4_vf_hd = $ruta_vf_hd_2."/".$file_name_vf.".mp4";
          $file_name_hls_vf_hd = $ruta_vf_hd_2."/".$file_name_vf.".m3u8";
          # Comando para convertir el archivo mp4 a m3u8
          $comando_mp4_to_hls_hd_vf  = VIDEOS_CMD_HLS." -i $file_name_mp4_vf_hd -s hd1080 -c:v libx264 -crf 23 -c:a aac -strict -2 -c:a:0 copy -c:a:1 copy -c:s copy -hls_list_size 0 -hls_segment_size 500000 $file_name_hls_vf_hd ";
          # Si recibe comando lo ejecutara
          # Recordando que para recibir un comando 
          if(!empty($comando_mp4_to_hls_sd_vf) && !empty($comando_mp4_to_hls_hd_vf)){
            # Ejecutamos el comando background
            exec($comando_mp4_to_hls_sd_vf." >> /dev/null &");
            exec($comando_mp4_to_hls_hd_vf." >> /dev/null &");
            # comando para obtener la imagen del video
            $name_img_vf = $ruta_vf_sd_2."/img_%d.png";
            $mp4_vf =  $ruta_vf_sd_2."/".$file_name_vf.".mp4";
            $comando_image_vf = VIDEOS_CMD_HLS." -i $mp4_vf -ss 00:00:02 -vframes 1 $name_img_vf";
            exec($comando_image_vf." >> /dev/null &");          
          }
          ## Finaliza el proceso del VF SD Y DH ######
        }
        else{
          if($fg_tipo_video=="VL" || $fg_reset_video_drop_vl==1){
            # SD
            $ruta_sd_2 = $ruta_str_2."/video_".$clave."_vl_sd";
            # HD
            $ruta_hd_2 = $ruta_str_2."/video_".$clave."_vl_hd";
            $file_name = array_shift(explode('.',$ds_vl_ruta));
          }
          else{
            # SD
            $ruta_sd_2 = $ruta_str_2."/video_".$clave."_vb_sd";
            # HD
            $ruta_hd_2 = $ruta_str_2."/video_".$clave."_vb_hd";
            $file_name = array_shift(explode('.',$ds_as_ruta));
          }
          # Nombre del archivo m3u8      
          $file_name_mp4 = $ruta_sd_2."/".$file_name.".mp4";
          $file_name_hls = $ruta_sd_2."/".$file_name.".m3u8";
          $output = $ruta_sd_2."/output".$clave."_sd.php";
          # Comando para convertir el archivo mp4 a m3u8
          $comando_mp4_to_hls_sd  = VIDEOS_CMD_HLS." -i $file_name_mp4  -s hd1080 -c:v libx264 -crf 23 -c:a aac -strict -2 -c:a:0 copy -c:a:1 copy -c:s copy -hls_list_size 0 -hls_segment_size 500000 $file_name_hls ";
          $comando_mp4_to_hls_sd .= " 1>$output 2>&1";
          # Convertimos el MP4 to HLS m3u8
          $file_name_mp4_hd = $ruta_hd_2."/".$file_name.".mp4";
          $file_name_hls_hd = $ruta_hd_2."/".$file_name.".m3u8";
          $comando_mp4_to_hls_hd = VIDEOS_CMD_HLS." -i $file_name_mp4_hd  -s hd1080 -c:v libx264 -crf 23 -c:a aac -strict -2 -c:a:0 copy -c:a:1 copy -c:s copy -hls_list_size 0 -hls_segment_size 500000 $file_name_hls_hd"; 
          // echo "if(!empty($comando_mp4_to_hls_sd) && !empty($comando_mp4_to_hls_hd)){";exit;
          # Si recibe comando lo ejecutara
          # Recordando que para recibir un comando 
          if(!empty($comando_mp4_to_hls_sd) && !empty($comando_mp4_to_hls_hd)){
            # Ejecutamos el comando background
            exec($comando_mp4_to_hls_sd." >> /dev/null &");
            exec($comando_mp4_to_hls_hd." >> /dev/null &");
            # comando para obtener la imagen del video
            $name_img = $ruta_sd_2."/img_%d.png";
            $mp4 =  $ruta_sd_2."/".$file_name.".mp4";
            $comando_image = VIDEOS_CMD_HLS." -i $mp4 -ss 00:00:02 -vframes 1 $name_img";
            exec($comando_image." >> /dev/null &");
            if($fg_reset_video_drop_vf==0 || $fg_reset_video_drop_vl==0)
              EjecutaQuery("DELETE FROM k_video_temp WHERE fl_usuario=$fl_usuario AND fl_leccion_sp=$clave AND fg_campus='1'");
            EjecutaQuery("UPDATE c_leccion SET ".$campo."='".$file_name.".mp4', fe_vl_alta=NOW() WHERE fl_leccion=$clave");
          }
        }

      }
    }
    # Es nuevo
    else{     
      
      # Buscamos el registro del usuario
      $roww = RecuperaValor("SELECT nb_archivo, ds_type FROM k_video_temp WHERE fl_usuario=$fl_usuario AND fg_campus='1'");
      $nb_archivo = $roww[0];
      $ds_type = $roww[1];
      if(!empty($nb_archivo)){        
        # Cambiamos los nombres de las carpetas
        if($ds_type=="VL"){
          $ruta_str_1 = VID_CAM_LEC."/video_us".$fl_usuario;
          $ruta_str_2 = VID_CAM_LEC."/video_".$fl_leccion;
        }
        else{
          $ruta_str_1 = VID_CAM_BREF."/video_us".$fl_usuario;
          $ruta_str_2 = VID_CAM_BREF."/video_".$fl_leccion;
        }
        rename($ruta_str_1, $ruta_str_2);
        if($fg_tipo_video=="VL"){
          # SD
          $ruta_sd_1 = $ruta_str_2."/video_us".$fl_usuario."_vl_sd";
          $ruta_sd_2 = $ruta_str_2."/video_".$fl_leccion."_vl_sd";
          rename($ruta_sd_1, $ruta_sd_2);
          # HD
          $ruta_hd_1 = $ruta_str_2."/video_us".$fl_usuario."_vl_hd";
          $ruta_hd_2 = $ruta_str_2."/video_".$fl_leccion."_vl_hd";
          rename($ruta_hd_1, $ruta_hd_2);
          $campo = "ds_vl_ruta";
        }
        else{
           # SD
          $ruta_sd_1 = $ruta_str_2."/video_us".$fl_usuario."_vb_sd";
          $ruta_sd_2 = $ruta_str_2."/video_".$fl_leccion."_vb_sd";
          rename($ruta_sd_1, $ruta_sd_2);
          # HD
          $ruta_hd_1 = $ruta_str_2."/video_us".$fl_usuario."_vb_hd";
          $ruta_hd_2 = $ruta_str_2."/video_".$fl_leccion."_vb_hd";
          rename($ruta_hd_1, $ruta_hd_2);
          $campo = "ds_as_ruta";
        }
        
        # Nombre del archivo m3u8
        $file_name = array_shift(explode('.',$nb_archivo));
        $file_name_mp4 = $ruta_sd_2."/".$file_name.".mp4";
        $file_name_hls = $ruta_sd_2."/".$file_name.".m3u8";
        $output = $ruta_sd_2."/output".$fl_leccion."_sd.php";
        # Comando para convertir el archivo mp4 a m3u8
        $comando_mp4_to_hls_sd  = VIDEOS_CMD_HLS." -i $file_name_mp4  -s hd1080 -c:v libx264 -crf 23 -c:a aac -strict -2 -c:a:0 copy -c:a:1 copy -c:s copy -hls_list_size 0 -hls_segment_size 500000 $file_name_hls ";
        $comando_mp4_to_hls_sd .= " 1>$output 2>&1 ";
        
        # Convertimos el MP4 to HLS m3u8
        $file_name_mp4_hd = $ruta_hd_2."/".$file_name.".mp4";
        $file_name_hls_hd = $ruta_hd_2."/".$file_name.".m3u8";
        $comando_mp4_to_hls_hd = VIDEOS_CMD_HLS." -i $file_name_mp4_hd  -s hd1080 -c:v libx264 -crf 23 -c:a aac -strict -2 -c:a:0 copy -c:a:1 copy -c:s copy -hls_list_size 0 -hls_segment_size 500000 $file_name_hls_hd";
        // echo "if(!empty($comando_mp4_to_hls_sd) && !empty($comando_mp4_to_hls_hd)){";exit;
        # Si recibe comando lo ejecutara
        # Recordando que para recibir un comando 
        if(!empty($comando_mp4_to_hls_sd) && !empty($comando_mp4_to_hls_hd)){
          # Ejecutamos el comando background
          exec($comando_mp4_to_hls_sd." >> /dev/null &");
          exec($comando_mp4_to_hls_hd." >> /dev/null &");
          # comando para obtener la imagen del video
          $name_img = $ruta_sd_2."/img_%d.png";
          $mp4 =  $ruta_sd_2."/".$nb_archivo.".mp4";
          $comando_image = VIDEOS_CMD_HLS." -i $mp4 -ss 00:00:01 -vframes 1 $name_img";
          exec($comando_image." >> /dev/null &");
        }
        EjecutaQuery("DELETE FROM k_video_temp WHERE fl_usuario=$fl_usuario AND fg_campus='1'");
        EjecutaQuery("UPDATE c_leccion SET ".$campo."='$nb_archivo' WHERE fl_leccion=".$fl_leccion);
     }
    }
  }
  
  # Si selecciona un video de los existentes 
  # Hacemos copia de las carpetas
  if(!empty($archivo_a) || !empty($archivo1_a)){
    # Ejecutamos comando para mover los archivos
    // if(!empty($clave)){
      if(empty($clave))
        $clave = $fl_leccion;
      # ruta de la leccion copiada VIDEO LECTURE
      if(!empty($archivo_a)){
        # Para los videos SD
        Copy_Video($archivo_a, $clave, "vl", "sd");
        # Para los videos HD
        Copy_Video($archivo_a, $clave, "vl", "hd");
        # Actualizamos el registro
        $row0 = RecuperaValor("SELECT ds_vl_ruta FROM c_leccion WHERE fl_leccion=".$archivo_a);        
        EjecutaQuery("UPDATE c_leccion SET ds_vl_ruta='".$row0[0]."', fe_vl_alta=CURRENT_TIMESTAMP, ds_progress_video_vl='100' WHERE fl_leccion=$clave ");       
      }
      # ruta de la leccion copiada VIDEO BRIEF
      if(!empty($archivo1_a)){
        # Para los videos SD
        Copy_Video($archivo1_a, $clave, "vb", "sd");
        # Para los videos HD
        Copy_Video($archivo1_a, $clave, "vb", "hd");
        # Actualizamos el registro
        $row0 = RecuperaValor("SELECT ds_as_ruta FROM c_leccion WHERE fl_leccion=".$archivo1_a);        
        EjecutaQuery("UPDATE c_leccion SET ds_as_ruta='".$row0[0]."', fe_as_alta=CURRENT_TIMESTAMP, ds_progress_video_vf='100' WHERE fl_leccion=$clave ");       
      }
    // }    
  }
    }
  if(VIDEOS_FLASH==true){
  # Convierte de mov  a flv
  if(!empty($comando_mov_flv) AND FG_PRODUCCION)
    exec($comando_mov_flv);
  
  # Crea liga archivo 1
  if(!empty($comando_2) AND FG_PRODUCCION)
    exec($comando_2);
  
  # Crea liga archivo 2
  if(!empty($comando_4) AND FG_PRODUCCION)
    exec($comando_4);
  }
  
  # Redirige al listado
  header("Location: ".ObtenProgramaBase( ));
  
  # Funcion par acopiar los videos  
  function Copy_Video($origen, $destino, $type_vid, $type_flow){
    if($type_vid=="vl"){
      $ruta_copiada_vl_sd = VID_CAM_LEC."/video_".$origen."/video_".$origen."_".$type_vid."_".$type_flow."/";
      $ruta_destino_vl_sd1 = VID_CAM_LEC."/video_".$destino;        
    }
    else{
      $ruta_copiada_vl_sd = VID_CAM_BREF."/video_".$origen."/video_".$origen."_".$type_vid."_".$type_flow."/";
      $ruta_destino_vl_sd1 = VID_CAM_BREF."/video_".$destino;    
    }
    # Si existen las carpetas
    if(!file_exists($ruta_destino_vl_sd1)){
      # Cambiamos los permisos de la carpeta hd
      exec("mkdir -m777 ".$ruta_destino_vl_sd1);
    }
    $oldname = $ruta_destino_vl_sd1."/video_".$origen."_".$type_vid."_".$type_flow."/";
    $newname = $ruta_destino_vl_sd1."/video_".$destino."_".$type_vid."_".$type_flow."/";
    # Si existen las carpetas la eliminan
    if(file_exists($newname)){
      # Cambiamos los permisos
      exec("rm -rf ".$newname);
    }
    exec("cp -a ".$ruta_copiada_vl_sd." ".$ruta_destino_vl_sd1);
    // rename($oldname, $newname);
    exec("mv ".$oldname." ".$newname);
    chmod($newname, 0777);
    # Archivo del proceso
    $oldoutput = $newname."output".$origen."_sd.php";
    $newoutput = $newname."output".$destino."_sd.php";
    exec("mv ".$oldoutput." ".$newoutput); 
    chmod($newoutput, 0777);
  }
  
?>