<?php
  
  # Libreria de funciones	
	require("../lib/self_general.php");
  
	# Verifica que exista una sesion valida en el cookie y la resetea
  $fl_usuario = ValidaSesion(False,0, True);
  $fl_perfil = ObtenPerfilUsuario($fl_usuario);
  $fl_instituto = ObtenInstituto($fl_usuario);

  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermisoSelf(FUNC_SELF)) {  
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }

  # Check the uploaded file, moves file to /common/tmp on success,
  # returns an array with error message
  require("../../modules/common/new_campus/lib/fileuploader.php");
  # list of valid extensions
  $allowedExtensions = array('mov', 'jpeg', 'jpg', 'mp4', 'png', 'avi');
  # max file size in bytes
  # NOTA en local no funcionaba si integraba el tamaño
  $sizeLimit = 1024 * 1024 * 1024;
  $url_handleUpload = PATH_SELF_UPLOADS_F."/tmp/";
  if (!file_exists($url_handleUpload)) {
    mkdir($url_handleUpload, 0777, true);
  }
  $uploader = new qqFileUploader($allowedExtensions, $sizeLimit);
  $message = $uploader->handleUpload($url_handleUpload, True);
  # Check if uploaded file was successfully moved
  if(!empty($message['error'])){
    echo json_encode((Object)$message);
    exit;
  }

  # Recibe parametros
  $fg_tipo = RecibeParametroHTML('tipo');
  $no_semana = RecibeParametroNumerico('semana');
  $ds_comentario = RecibeParametroHTML('comentarios');
  $nb_archivo = RecibeParametroHTML('archivo', true);
  $ext = strtolower(ObtenExtensionArchivo($nb_archivo));
  $fl_programa = RecibeParametroNumerico('fl_programa');
  $fl_entrega_semanal_sp=RecibeParametroNumerico('fl_entrega_semanal_sp');
  $fg_esta_completo=RecibeParametroNumerico('fg_completo');
  $no_requeridos=RecibeParametroNumerico('no_requeridos');
  $no_requeridos=1;
  ## Revisa total para esta semana
  $row = RecuperaValor("SELECT COUNT(1) FROM k_entregable_sp WHERE fl_entrega_semanal_sp=$fl_entrega_semanal_sp AND fg_tipo='$fg_tipo'");
  $tot_entregables = $row[0];
 
  #Si ya cumplio todos ya no debe de subir nada.
  if(($fg_esta_completo==1)||($no_requeridos==$tot_entregables)){
      $message = array('error' => "".ObtenEtiqueta(2365)."");
      echo json_encode((Object)$message);
      exit;
  }

  # Valida que tengamos un archivo
  if(empty($nb_archivo)) {
    $message = array('error' => "Server Error. Server did not receive a valid file name.");
    echo json_encode((Object)$message);
    exit;
  }
  
  # Valida que sea una imagen si el tipo es Sketch
  if($fg_tipo == "S" AND $ext <> "jpg" AND $ext <> "jpeg" AND $ext <> "png" AND $ext <> "PNG") {
    unlink($url_handleUpload.$nb_archivo);
    
    $message = array('error' => "File Error. The file uploaded is not a valid image");
    echo json_encode((Object)$message);
    exit;
  }
  
  # Recupera los datos de la entrega de la semana
  $ds_login = ObtenMatriculaAlumno($fl_usuario);
  $fl_semana = ObtenFolioSemanaAlumno($no_semana, $fl_programa);
  
  # Create a random int for original size and thumbnail picture
  $rand_int = rand(1, 32000);

  # Nombre y ruta segun cada tipo de archivo
  $nb_archivo_ant = $uploader->getName();

  # Encaso del que el nombre contenga la palabra script la remplazamos para evitar errores
  $nb_archivo = $ds_login."_".$fl_semana."_".$fg_tipo."_".$rand_int.".".$ext;
  switch($ext) {
    case "jpg": 
    case "png": 
    case "PNG": 
    case "jpeg": $ruta = PATH_SELF_UPLOADS_F."/".$fl_instituto."/".CARPETA_USER.$fl_usuario."/sketches"; break;
    default: $ruta = PATH_SELF_UPLOADS_F."/".$fl_instituto."/".CARPETA_USER.$fl_usuario."/videos";
  }
  
  # Si no esta la carpeta la creamos
  // if (!file_exists($ruta)) {
    mkdir($ruta, 0777, true);
  // }

  $nb_archi_thum_img=$nb_archivo;

  # Recibe el archivo seleccionado
  # Eliminamos del tmp y lo copiamos con el nombre modificado a la nueva ruta
  if(file_exists($ruta."/".$nb_archivo))
    unlink($ruta."/".$nb_archivo);
  rename($url_handleUpload.$nb_archivo_ant, $ruta."/".$nb_archivo);
  
  # Ajusta el maximo de dimensiones para imagenes
  if($ext == "jpg" || $ext == "jpeg" || $ext=="png") {
    # Si no existe la carpeta la tiene que crear
    if (!file_exists($ruta."/original/")) {
      mkdir($ruta."/original/", 0777, true);
    }
    # Si no existe la carpeta la tiene que crear
    if (!file_exists($ruta."/regular/")) {
      mkdir($ruta."/regular/", 0777, true);
    }
    # Si no existe la carpeta la tiene que crear
    if (!file_exists($ruta."/thumbs/")) {
      mkdir($ruta."/thumbs/", 0777, true);
    }
    # Si no existe la carpeta la tiene que crear
    if (!file_exists($ruta."/board_thumbs/")) {
      mkdir($ruta."/board_thumbs/", 0777, true);
    }
    if(file_exists($ruta."/original/".$nb_archivo))
      unlink($ruta."/original/".$nb_archivo);
    copy($ruta."/".$nb_archivo, $ruta."/original/".$nb_archivo);
    if(file_exists($ruta."/regular/".$nb_archivo))
      unlink($ruta."/regular/".$nb_archivo);
    if($ext == "jpg" || $ext == "jpeg")
      CreaThumb($ruta."/".$nb_archivo, $ruta."/regular/".$nb_archivo, 0, 0, 0, IMG_REGULAR);
    else
      CreaThumbpng($ruta."/".$nb_archivo, $ruta."/regular/".$nb_archivo, 0, 0, 0, IMG_REGULAR);
    if(file_exists($ruta."/thumbs/".$nb_archivo))
      unlink($ruta."/thumbs/".$nb_archivo);
    if($ext == "jpg" || $ext == "jpeg")
      CreaThumb($ruta."/".$nb_archivo, $ruta."/thumbs/".$nb_archivo, 0, 0, 0, IMG_THUMBS);
    else
      CreaThumbpng($ruta."/".$nb_archivo, $ruta."/thumbs/".$nb_archivo, 0, 0, 0, IMG_THUMBS);
    if($ext == "jpg" || $ext == "jpeg")
      CreaThumb($ruta."/".$nb_archivo, $ruta."/".$nb_archivo, 0, 0, 0, IMG_SKETCHE);
    else
      CreaThumbpng($ruta."/".$nb_archivo, $ruta."/".$nb_archivo, 0, 0, 0, IMG_SKETCHE);

    # Create a 300 width thumbnail for activity board
    if($ext == "jpg" || $ext == "jpeg")
      CreaThumb($ruta."/".$nb_archivo, $ruta."/board_thumbs/".$nb_archivo, IMG_BTHUMBS);
    else
      CreaThumbpng($ruta."/".$nb_archivo, $ruta."/board_thumbs/".$nb_archivo, IMG_BTHUMBS);
  }
  
  # Parametros para convertir archivos mov en flv
  $parametros = ObtenConfiguracion(41);

  # Convierte archivos .mov mp4 avi
  if($ext == "mov" || $ext == "mp4"  || $ext == "avi") {
    $fg_video=1;
    $nb_archivo_del = $nb_archivo;
    $explosion = explode('.', $nb_archivo);
    $file_mp4 = $explosion[0].'.mp4';
    $file_out = $explosion[0].'.m3u8';
    $file_in = $ruta."/".$nb_archivo;
    $convert_to_mp4 = FFMPEG_FAME." -i '$file_in' -vf scale=-1:1080 -pix_fmt yuv420p -vcodec h264 -acodec mp2 -c:a aac -b:a 192k -ac 2 $ruta/$file_mp4";
    $convert_to_hls = FFMPEG_FAME." -i '$file_in' -vf \"pad=ceil(iw/2)*2:ceil(ih/2)*2\" -hls_time 10 -hls_list_size 0 -f hls $ruta/$file_out ";
    //$convert_to_hls = FFMPEG_FAME." -i $file_in -s hd1080 -c:v libx264 -crf 23 -c:a aac -strict -2 -c:a:0 copy -c:a:1 copy -c:s copy -hls_list_size 0 -hls_segment_size 500000 $ruta/$file_out";
    $nb_archivo = $file_out;
  } else {
    $fg_video=0;
  }
  
  # Insertamos el registro al usuario
  $Query = "SELECT fl_entrega_semanal_sp FROM k_entrega_semanal_sp WHERE fl_alumno=$fl_usuario AND fl_leccion_sp=$fl_semana ";
  $row = RecuperaValor($Query);
  $fl_entrega_semanal_sp = $row[0];
  if(empty($fl_entrega_semanal_sp)) {
      $Query  = "INSERT INTO k_entrega_semanal_sp (fl_alumno, fl_leccion_sp)  ";
      $Query .= "VALUES($fl_usuario, $fl_semana)";
      $fl_entrega_semanal_sp = EjecutaInsert($Query);
  }
  
  # Recupera los datos de los entregables
  $Query = "SELECT fl_entregable_sp, no_orden ";
  $Query .= "FROM k_entregable_sp ";
  $Query .= "WHERE fl_entrega_semanal_sp=$fl_entrega_semanal_sp ";
  $Query .= "AND fg_tipo='$fg_tipo' ";
  $Query .= "ORDER BY no_orden DESC ";
  $row = RecuperaValor($Query);
  $fl_entregable_sp = $row[0]??NULL;
  $no_orden = $row[1]??NULL;
  if(empty($no_orden))
      $no_orden = 0;
  $no_orden = $no_orden + 1;

  # Check if the stream of the program is available
  # Hay que preguntarle a Mario o Mauricio
  $nb_programa = ObtenNombreCourse($fl_programa);
  
  # Inserta o Actualiza el entregable de la animacion
  if(empty($fl_entregable_sp) OR $fg_tipo == "S") {
      # Update for desktop.php
      $QueryE  = "INSERT INTO k_entregable_sp (fl_entrega_semanal_sp, fg_tipo, no_orden, ds_ruta_entregable, ds_comentario, fe_entregado) ";
      $QueryE .= "VALUES($fl_entrega_semanal_sp, '$fg_tipo', $no_orden, '$nb_archivo', '$ds_comentario', CURRENT_TIMESTAMP)";
      $fl_entregable_sp = EjecutaInsert($QueryE);

      # Store to the activity board
      $QueryP  = "INSERT INTO k_gallery_post_sp (fl_programa_sp, fl_usuario,fl_entregable_sp,ds_title,ds_post,fe_post,nb_archivo) ";
      $QueryP .= "VALUES ($fl_programa, $fl_usuario, $fl_entregable_sp, '', '', CURRENT_TIMESTAMP, '$nb_archivo')";
      EjecutaQuery($QueryP);

      $row = RecuperaValor("SELECT fl_gallery_post_sp FROM k_gallery_post_sp WHERE fl_entregable_sp=$fl_entregable_sp");
      $fl_gallery_post_sp = $row[0];
  } else {
      # Update for desktop.php
      $Query  = "UPDATE k_entregable_sp SET ds_ruta_entregable='$nb_archivo', ds_comentario='$ds_comentario', fe_entregado=CURRENT_TIMESTAMP ";
      $Query .= "WHERE fl_entregable_sp=$fl_entregable_sp";
      EjecutaQuery($Query);

      # Store to the activity board
      /* Do a safety check for k_gallery_post before updating,
      * incase the assignment has already been uploaded to desktop for older students but
      * never to the activity board.
      * This check may be deleted in the future after the stream and assignment uploads
      * unification has become stable.
      */

      $row = RecuperaValor("SELECT fl_entregable_sp FROM k_gallery_post_sp WHERE fl_entregable_sp=$fl_entregable_sp");
      if(empty($row[0])){
          $Query  = "INSERT INTO k_gallery_post_sp (fl_programa_sp, fl_usuario, fl_entregable,_sp ds_title, ds_post, fe_post, nb_archivo) ";
          $Query .= "VALUES ($fl_programa, $fl_usuario, $fl_entregable_sp, '', '', CURRENT_TIMESTAMP, '$nb_archivo')";
      } else {
          $Query  = "UPDATE k_gallery_post_sp SET fe_post=CURRENT_TIMESTAMP, nb_archivo='$nb_archivo' ";
          $Query .= "WHERE fl_entregable_sp=$fl_entregable_sp";
      }

      EjecutaQuery($Query);
      $row = RecuperaValor("SELECT fl_gallery_post_sp FROM k_gallery_post_sp WHERE fl_entregable_sp=$fl_entregable_sp");
      $fl_gallery_post_sp = $row[0];
  }
  
  # Check if the insert or update was successful
  if(empty($fl_gallery_post_sp)){
      $error = array('error' => "Server Error. This file cannot be uploaded to board.");
      echo json_encode((Object)$error);
      exit();
  }
  
  # Revisa si esta completa la entrega
  // $fl_programa = ObtenProgramaAlumno($fl_alumno);
  // $no_grado = ObtenGradoAlumno($fl_alumno);
  $Query  = "SELECT fg_animacion, fg_ref_animacion, no_sketch, fg_ref_sketch ";
  $Query .= "FROM c_leccion_sp WHERE fl_programa_sp=$fl_programa AND fl_leccion_sp=$fl_semana ";
  $row = RecuperaValor($Query);
  $fg_animacion = $row[0];
  $fg_ref_animacion = $row[1];
  $no_sketch = $row[2];
  $fg_ref_sketch = $row[3];
  $row = RecuperaValor("SELECT COUNT(1) FROM k_entregable_sp WHERE fg_tipo='A' AND fl_entrega_semanal_sp=$fl_entrega_semanal_sp");
  $tot_assignment = $row[0];
  $row = RecuperaValor("SELECT COUNT(1) FROM k_entregable_sp WHERE fg_tipo='AR' AND fl_entrega_semanal_sp=$fl_entrega_semanal_sp");
  $tot_assignment_ref = $row[0];
  $row = RecuperaValor("SELECT COUNT(1) FROM k_entregable_sp WHERE fg_tipo='S' AND fl_entrega_semanal_sp=$fl_entrega_semanal_sp");
  $tot_sketch = $row[0];
  $row = RecuperaValor("SELECT COUNT(1) FROM k_entregable_sp WHERE fg_tipo='SR' AND fl_entrega_semanal_sp=$fl_entrega_semanal_sp");
  $tot_sketch_ref = $row[0];
  $animacion_ok = False;
  if($fg_animacion == "0" OR ($fg_animacion == "1" AND $tot_assignment > 0))
      $animacion_ok = True;

  $animacion_ref_ok = False;

  if($fg_ref_animacion == "0" OR ($fg_ref_animacion == "1" AND $tot_assignment_ref > 0))
      $animacion_ref_ok = True;

  $sketch_ok = False;

  if($tot_sketch >= $no_sketch)
    $sketch_ok = True;

  $sketch_ref_ok = False;

  if($fg_ref_sketch == "0" OR ($fg_ref_sketch == "1" AND $tot_sketch_ref > 0))
    $sketch_ref_ok = True;
  
  # Si ya esta completo, actualiza la entrega semanal
  if($animacion_ok AND $animacion_ref_ok AND $sketch_ok AND $sketch_ref_ok) {
      $Query = "UPDATE k_entrega_semanal_sp SET fe_entregado=CURRENT_TIMESTAMP WHERE fl_entrega_semanal_sp=$fl_entrega_semanal_sp AND fe_entregado IS NULL";
      EjecutaQuery($Query);
      EjecutaQuery("UPDATE k_entrega_semanal_sp SET fg_entregado='1' WHERE fl_entrega_semanal_sp=$fl_entrega_semanal_sp");
  }
  
  # Convierte archivo mov y lo elimina
  if(!empty($convert_to_mp4)){
      exec($convert_to_mp4);
  }
  # COnvertirmos m3u8
  if(!empty($convert_to_hls)){
      exec($convert_to_hls." >> /dev/null &");
  }

  # Return successful board id
  if($ext == "jpg" || $ext == "jpeg" || $ext=="png"){
      $ruta_thumbnail = PATH_SELF_UPLOADS."/".$fl_instituto."/".CARPETA_USER.$fl_usuario."/sketches/thumbs/$nb_archivo";
  } else {
      $ruta_thumbnail = PATH_N_COM_IMAGES."/desktop-upload-video-default.jpg";
  }
  
  if(empty($ds_comentario)){
      $ds_comentario = "No additional comments for this file";
  }  
  
  # BUSCAMOS CUANTOS ENTREGABLES HAY DEL TIPO
  $roww = RecuperaValor("SELECT COUNT(*) FROM k_entregable_sp WHERE fl_entrega_semanal_sp=$fl_entrega_semanal_sp AND fg_tipo='$fg_tipo'");
  $tot_entregados = $roww[0];
  
  # Obtenemos el maestro que tiene asignado el alumno
  $usu_programa = 0;
  if($fl_perfil==PFL_ESTUDIANTE_SELF){
      $rowk = RecuperaValor("SELECT fl_usu_pro, fl_maestro FROM k_usuario_programa WHERE fl_usuario_sp=$fl_usuario AND fl_programa_sp=$fl_programa");
      $fl_usu_pro = $rowk[0];
      $fl_maestro = $rowk[1];
      $rowk1 = RecuperaValor("SELECT fg_grade_tea FROM k_details_usu_pro WHERE fl_usu_pro=$fl_usu_pro");
      $fg_grade_tea = $rowk1[0];
      if($fg_grade_tea==1){
          $usu_programa = $fl_usu_pro;
          # Actualizamos las asigaciones del teacher
          if($animacion_ok AND $animacion_ref_ok AND $sketch_ok AND $sketch_ref_ok){
              # Buscamos si el teacher
              $row3 = RecuperaValor("SELECT no_submitted_assi FROM k_usu_notify WHERE fl_usuario=$fl_maestro");
              $no_assigments = $row3[0] + 1;
              EjecutaQuery("UPDATE k_usu_notify SET no_submitted_assi=".$no_assigments." WHERE fl_usuario=$fl_maestro");
          }
      }
  }
  
  
  # Una vz que subio el archivo vuelve a calcular el peso de su carpeta y actualiza
  # Obtenemos el tamaño de la carpeta del usuario
  File_Size(PATH_SELF_UPLOADS_F."/".$fl_instituto."/".CARPETA_USER.$fl_usuario,2,$fl_usuario);
  
  # Recuperamos los datos del archivo que subio
  $name = ObtenNombreUsuario($fl_usuario);
  $caption = $nb_programa;
  // $ds_description = "Week ".$no_semana." sketch term ".$no_term;
  $ds_description = "Week ".$no_semana;
  
  # Verificamos si ya podemos activar el boton    
  $row_l = RecuperaValor("SELECT fg_complete FROM k_leccion_usu WHERE fl_usuario_sp=$fl_usuario AND fl_leccion_sp=$fl_semana");
  $fg_completa = $row_l[0]??NULL;

  # Vamos activar el boton
  $activa_btn = boton_active($fl_usuario, $fl_semana);

  $ruta_avatar_user_post=ObtenAvatarUsuario($fl_usuario);
  
  #Recuperamos la fecha actual del post.
  $Query="SELECT fe_entregado FROM k_entrega_semanal_sp WHERE fl_entrega_semanal_sp=$fl_entregable_sp ";
  $ro=RecuperaValor($Query);
  $fe_pos=$ro['fe_entregado']??NULL;
  $fe_post=time_elapsed_string($fe_pos);
  
  #Obteemps la compania del usuario que postero esto es para mandarlo al feed.
  $compania=FAMEObtenCompaniaUsuario($fl_usuario,$fl_perfil);
  $ds_profesion=FAMEObtenProfesionUsuario($fl_usuario,$fl_perfil);
  if(empty($compania))
      $compania="&nbsp;";

  $img_thumn=ObtenNombreArchivo($nb_archi_thum_img);
  
  $file_img=$ruta."/".$img_thumn.".png";
  # Generamos el thumnnail de la imagen.
  $comando_imagen=FFMPEG_FAME." -i $ruta/".$nb_archi_thum_img." -vf thumbnail,scale=861:485 -frames:v 1 $file_img ";
  exec($comando_imagen);

  if(empty($comentshare))
      $comentshare = $nb_programa;
  $message = array(
      'success' => $nb_archivo_ant.' was successfully uploaded.',
      'post' => $fl_gallery_post_sp,
      'fg_video'=>$fg_video,
      'ruta_thumb_video'=>PATH_SELF_UPLOADS."/".$fl_instituto."/".CARPETA_USER.$fl_usuario."/videos/".$img_thumn.".png",
      'comando_imagen_video'=>$comando_imagen,
      'thumbnail' => $ruta_thumbnail,
      'comment' => $ds_comentario,
      'key' => $fl_entregable_sp,
      'ext' => $ext,
      'ruta_avatar_user_post'=>$ruta_avatar_user_post,
      'nb_archivo' => $nb_archivo,
      'name' => $name,
      'fe_post_formato'=>$fe_post,
      'caption' => $caption,
      'ds_description' => $ds_description,
      'comentshare' => $comentshare,
      'tot_entregados' => $tot_entregados,
      'fl_usu_pro' => $usu_programa,
      'fl_maestro' => $fl_maestro,
      'fl_perfil' => $fl_perfil,
      'compania'=>$compania,
      'ds_profesion'=>$ds_profesion,
      'comando' => $convert_to_mp4,
      'comando1' => $convert_to_hls,
      'fg_completa' => $fg_completa,
      'active_btn' => $activa_btn
  );

  echo json_encode((Object) $message);
  
?>