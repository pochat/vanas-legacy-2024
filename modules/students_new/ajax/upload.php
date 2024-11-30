<?php

  # Libreria de funciones
  require("../../common/lib/cam_general.inc.php");

  # Verifica que exista una sesion valida en el cookie y la resetea
  $fl_alumno = ValidaSesion(False);

  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermisoCampus(FUNC_ALUMNOS)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }

  # Check the uploaded file, moves file to /common/tmp on success,
  # returns an array with error message
  require("../../common/new_campus/lib/fileuploader.php");
  # list of valid extensions
  $allowedExtensions = array('mov', 'jpeg', 'jpg', 'mp4');
  # max file size in bytes
  # NOTA en local no funcionaba si integraba el tamaño
  $sizeLimit = 500 * 1024 * 1024;
  $uploader = new qqFileUploader($allowedExtensions, $sizeLimit);
  $message = $uploader->handleUpload(PATH_CAMPUS_F."/common/tmp/", True);
  # Check if uploaded file was successfully moved
  if(isset($message['error'])){
    echo json_encode((Object)$message);
    exit;
  }

  # Recibe parametros
  $fg_tipo = RecibeParametroHTML('tipo');
  $no_semana = RecibeParametroNumerico('semana');
  $ds_comentario = RecibeParametroHTML('comentarios');
  $nb_archivo = RecibeParametroHTML('archivo', true);
  $ext = strtolower(ObtenExtensionArchivo($nb_archivo));
  $fbhidden = RecibeParametroBinario('fbhidden');
  $comentshare = RecibeParametroHTML('comentshare');
  $nb_user = ObtenNombreUsuario($fl_alumno);
  $fl_term = ObtenTermAlumno($fl_alumno);
  $row = RecuperaValor("SELECT no_grado FROM k_term WHERE fl_term=".$fl_term."");
  $no_term = $row[0];

  # Valida campos obligatorios
  if(empty($nb_archivo)) {
    $message = array('error' => "Server Error. Server did not receive a valid file name.");
    echo json_encode((Object)$message);
    exit;
  }

  # Valida que sea una imagen si el tipo es Sketch
  if($fg_tipo == "S" AND $ext <> "jpg" AND $ext <> "jpeg") {
    unlink(PATH_CAMPUS_F."/common/tmp/".$nb_archivo);

    $message = array('error' => "File Error. The file uploaded is not a valid image.");
    echo json_encode((Object)$message);
    exit;
  }

  # Recupera los datos de la entrega de la semana
  $ds_login = ObtenMatriculaAlumno($fl_alumno);
  $fl_grupo = ObtenGrupoAlumno($fl_alumno);
  $fl_semana = ObtenFolioSemanaAlumno($fl_alumno, $no_semana);

  # Create a random int for original size and thumbnail picture
  $rand_int = rand(1, 32000);

  # Nombre y ruta segun cada tipo de archivo
  //$nb_archivo_ant = $nb_archivo;
  $nb_archivo_ant = $uploader->getName();
  # Encaso del que el nombre contenga la palabra script la remplazamos para evitar errores

  $nb_archivo = $ds_login."_".$fl_semana."_".$fg_tipo."_".$rand_int.".".$ext;
  switch($ext) {
    case "jpg":
    case "jpeg": $ruta = $_SERVER['DOCUMENT_ROOT'].PATH_ALU."/sketches"; break;
    default: $ruta = $_SERVER['DOCUMENT_ROOT'].PATH_ALU."/videos";
  }

  # Recibe el archivo seleccionado
  if(file_exists($ruta."/".$nb_archivo))
    unlink($ruta."/".$nb_archivo);
  rename(PATH_CAMPUS_F."/common/tmp/".$nb_archivo_ant, $ruta."/".$nb_archivo);

  # Ajusta el maximo de dimensiones para imagenes
  if($ext == "jpg" OR $ext == "jpeg") {
    if(file_exists($ruta."/original/".$nb_archivo))
      unlink($ruta."/original/".$nb_archivo);
    copy($ruta."/".$nb_archivo, $ruta."/original/".$nb_archivo);
    if(file_exists($ruta."/regular/".$nb_archivo))
      unlink($ruta."/regular/".$nb_archivo);
    CreaThumb($ruta."/".$nb_archivo, $ruta."/regular/".$nb_archivo, 0, 0, 0, 280);
    if(file_exists($ruta."/thumbs/".$nb_archivo))
      unlink($ruta."/thumbs/".$nb_archivo);
    CreaThumb($ruta."/".$nb_archivo, $ruta."/thumbs/".$nb_archivo, 0, 0, 0, 90);
    CreaThumb($ruta."/".$nb_archivo, $ruta."/".$nb_archivo, 0, 0, 0, 720);

    # Create a 300 width thumbnail for activity board
    CreaThumb($ruta."/".$nb_archivo, $ruta."/board_thumbs/".$nb_archivo, 300);
  }

  # Parametros para convertir archivos mov en flv
$parametros = "-vcodec h264 -acodec mp2";//ObtenConfiguracion(41);

  # Convierte archivos .mov a .ogg
  //if($ext == "mov" OR $ext == "mp4") {
  if($ext == "mov") {
    $file_ogg = substr($nb_archivo, 0, (strlen($nb_archivo)-4)) . '.mp4';
    if(file_exists($ruta."/".$file_ogg))
      unlink($ruta."/".$file_ogg);
    $file_mov = $ruta."/".$nb_archivo;
    //$comando = CMD_FFMPEG." -i \"$file_mov\" $parametros \"$ruta/$file_ogg\"";
    $comando = CMD_FFMPEG . " -i \"$file_mov\" $parametros \"$ruta/$file_ogg\"";
    $nb_archivo = $file_ogg;

    # create the video thumbnail
    # store to gallery folder as well

    $video_size = "300x300";
    $second = 3;

    # store the created thumbnail image to the sketch/board_image folder

    $thumbnail = substr($nb_archivo, 0, (strlen($nb_archivo)-4)) . '.jpg';
    # used for testing: need to reconfigure the path to ffmpeg here
    //$comando_thumb = "/var/www/html/bin/ffmpeg -i $file_mov -an -vframes 1 -s $video_size -ss $second \"$ruta/board_thumbs/$thumbnail\"";

    # replaced by a default thumbnail for now
    //$comando_thumb = CMD_FFMPEG." -i $file_mov -an -vframes 1 -s $video_size -ss $second \"$ruta/board_thumbs/$thumbnail\"";
  }

  # Inserta los datos de la entrega semanal si no existen aun
  $Query  = "SELECT fl_entrega_semanal FROM k_entrega_semanal WHERE fl_alumno=$fl_alumno AND fl_grupo=$fl_grupo AND fl_semana=$fl_semana";
  $row = RecuperaValor($Query);
  $fl_entrega_semanal = $row[0];
  if(empty($fl_entrega_semanal)) {
    $Query  = "INSERT INTO k_entrega_semanal (fl_alumno, fl_grupo, fl_semana) ";
    $Query .= "VALUES($fl_alumno, $fl_grupo, $fl_semana)";
    $fl_entrega_semanal = EjecutaInsert($Query);
  }

  # Recupera los datos de los entregables
  $Query  = "SELECT fl_entregable, no_orden ";
  $Query .= "FROM k_entregable ";
  $Query .= "WHERE fl_entrega_semanal=$fl_entrega_semanal ";
  $Query .= "AND fg_tipo='$fg_tipo' ";
  $Query .= "ORDER BY no_orden DESC";
  $row = RecuperaValor($Query);
  $fl_entregable = $row[0];
  $no_orden = $row[1];
  if(empty($no_orden))
    $no_orden = 0;
  $no_orden = $no_orden + 1;

  # Check if the stream of the program is available
  $nb_programa = ObtenNombreProgramaAlumno($fl_alumno);
  $Query = "SELECT fl_tema FROM c_f_tema WHERE nb_tema='$nb_programa'";
  $row = RecuperaValor($Query);
  $fl_tema = $row[0];

  if(empty($fl_tema)){
    // Possibility of this error are :
    // 1. If there is no such stream for this program. i.e. The stream for this program is not setup
    // 2. This program is not allowed to post anything to streams
    $message = array('error' => "Server Error. There is no stream for this program.");
    echo json_encode((Object)$message);
    exit;
  }

  # Inserta o Actualiza el entregable de la animacion
  if(empty($fl_entregable) OR $fg_tipo == "S") {
    # Update for desktop.php
    $Query  = "INSERT INTO k_entregable (fl_entrega_semanal, fg_tipo, no_orden, ds_ruta_entregable, ds_comentario, fe_entregado) ";
    $Query .= "VALUES($fl_entrega_semanal, '$fg_tipo', $no_orden, '$nb_archivo', '$ds_comentario', CURRENT_TIMESTAMP)";
    $fl_entregable = EjecutaInsert($Query);

    # Store to the activity board
    $Query  = "INSERT INTO k_gallery_post (fl_tema, fl_usuario, fl_entregable, ds_title, ds_post, fe_post, nb_archivo) ";
    $Query .= "VALUES ($fl_tema, $fl_alumno, $fl_entregable, '', '', CURRENT_TIMESTAMP, '$nb_archivo')";
    EjecutaQuery($Query);

    $row = RecuperaValor("SELECT fl_gallery_post FROM k_gallery_post WHERE fl_entregable=$fl_entregable");
    $fl_gallery_post = $row[0];
  }
  else {
    # Update for desktop.php
    $Query  = "UPDATE k_entregable SET ds_ruta_entregable='$nb_archivo', ds_comentario='$ds_comentario', fe_entregado=CURRENT_TIMESTAMP ";
    $Query .= "WHERE fl_entregable=$fl_entregable";
    EjecutaQuery($Query);

    # Store to the activity board

    /* Do a safety check for k_gallery_post before updating,
     * incase the assignment has already been uploaded to desktop for older students but
     * never to the activity board.
     * This check may be deleted in the future after the stream and assignment uploads
     * unification has become stable.
     */
    $row = RecuperaValor("SELECT fl_entregable FROM k_gallery_post WHERE fl_entregable=$fl_entregable");
    if(empty($row[0])){
      $Query  = "INSERT INTO k_gallery_post (fl_tema, fl_usuario, fl_entregable, ds_title, ds_post, fe_post, nb_archivo) ";
      $Query .= "VALUES ($fl_tema, $fl_alumno, $fl_entregable, '', '', CURRENT_TIMESTAMP, '$nb_archivo')";
    } else {
      $Query  = "UPDATE k_gallery_post SET fe_post=CURRENT_TIMESTAMP, nb_archivo='$nb_archivo' ";
      $Query .= "WHERE fl_entregable=$fl_entregable";
    }
    EjecutaQuery($Query);
    $row = RecuperaValor("SELECT fl_gallery_post FROM k_gallery_post WHERE fl_entregable=$fl_entregable");
    $fl_gallery_post = $row[0];
  }

  # Check if the insert or update was successful
  if(empty($fl_gallery_post)){
    $error = array('error' => "Server Error. This file cannot be uploaded to board.");
    echo json_encode((Object)$error);
    exit;
  }

  # Revisa si esta completa la entrega
  $fl_programa = ObtenProgramaAlumno($fl_alumno);
  $no_grado = ObtenGradoAlumno($fl_alumno);
  $Query  = "SELECT fg_animacion, fg_ref_animacion, no_sketch, fg_ref_sketch ";
  $Query .= "FROM c_leccion WHERE fl_programa=$fl_programa AND no_grado=$no_grado AND no_semana=$no_semana";
  $row = RecuperaValor($Query);
  $fg_animacion = $row[0];
  $fg_ref_animacion = $row[1];
  $no_sketch = $row[2];
  $fg_ref_sketch = $row[3];
  $row = RecuperaValor("SELECT COUNT(1) FROM k_entregable WHERE fg_tipo='A' AND fl_entrega_semanal=$fl_entrega_semanal");
  $tot_assignment = $row[0];
  $row = RecuperaValor("SELECT COUNT(1) FROM k_entregable WHERE fg_tipo='AR' AND fl_entrega_semanal=$fl_entrega_semanal");
  $tot_assignment_ref = $row[0];
  $row = RecuperaValor("SELECT COUNT(1) FROM k_entregable WHERE fg_tipo='S' AND fl_entrega_semanal=$fl_entrega_semanal");
  $tot_sketch = $row[0];
  $row = RecuperaValor("SELECT COUNT(1) FROM k_entregable WHERE fg_tipo='SR' AND fl_entrega_semanal=$fl_entrega_semanal");
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
    $Query = "UPDATE k_entrega_semanal SET fe_entregado=CURRENT_TIMESTAMP WHERE fl_entrega_semanal=$fl_entrega_semanal AND fe_entregado IS NULL";
    EjecutaQuery($Query);
    EjecutaQuery("UPDATE k_entrega_semanal SET fg_entregado='1' WHERE fl_entrega_semanal=$fl_entrega_semanal");
  }

  # Convierte archivo mov y lo elimina
  if(!empty($comando_thumb))
    exec($comando_thumb);
  if(!empty($comando))
    exec($comando);
  if(!empty($file_mov))
    unlink($file_mov);

  # Return successful board id

  if($ext == 'mov' OR $ext == 'mp4'){
    $ruta_thumbnail = PATH_N_COM_IMAGES."/desktop-upload-video-default.jpg";
  } else {
    $ruta_thumbnail = PATH_ALU."/sketches/thumbs/$nb_archivo";
  }
  if(empty($ds_comentario)){
    $ds_comentario = "No additional comments for this file";
  }

  # Recuperamos los datos del archivo que subio
  $name = $nb_user;
  $caption = $nb_programa;
  $ds_description = "Week ".$no_semana." sketch term ".$no_term;
  if(empty($comentshare))
    $comentshare = $nb_programa;
  $message = array(
    'success' => $nb_archivo_ant.' was successfully uploaded.',
    'post' => $fl_gallery_post,
    'thumbnail' => $ruta_thumbnail,
    'comment' => $ds_comentario,
    'key' => $fl_entregable,
    'fbhidden' => $fbhidden,
    'ext' => $ext,
    'nb_archivo' => $nb_archivo,
    'name' => $name,
    'caption' => $caption,
    'ds_description' => $ds_description,
    'comentshare' => $comentshare,
    'link_share' => ObtenConfiguracion(60)
  );
  echo json_encode((Object) $message);
?>