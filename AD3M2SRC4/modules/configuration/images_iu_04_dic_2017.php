<?php
  
  # Libreria de funciones
	require '../../lib/general.inc.php';
  
	# Verifica que exista una sesion valida en el cookie y la resetea
	ValidaSesion( );
  
	# Recibe la clave
  $clave = RecibeParametroNumerico('clave');
  
  # Verifica si se esta insertando
  if(empty($clave)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
	# Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermiso(FUNC_IMAGENES, PERMISO_MODIFICACION)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Recibe parametros
  $fg_error = 0;
	$ds_imagen = RecibeParametroHTML('ds_configuracion');
  $ds_caption = RecibeParametroHTML('ds_caption');
  $tr_caption = RecibeParametroHTML('tr_caption');
  $nb_archivo = RecibeParametroHTML('nb_archivo');
  $tr_archivo = RecibeParametroHTML('tr_archivo');
  
	# Valida campos obligatorios
  if(empty($nb_archivo) AND empty($_FILES['archivo']['tmp_name']))
    $nb_archivo_err = ERR_REQUERIDO;
  
  # Regresa a la forma con error
  $fg_error = $nb_archivo_err;
  if($fg_error) {
    echo "<html><body><form name='datos' method='post' action='".ObtenProgramaNombre(PGM_FORM)."'>\n";
    Forma_CampoOculto('clave' , $clave);
    Forma_CampoOculto('fg_error' , $fg_error);
    Forma_CampoOculto('ds_imagen' , $ds_imagen);
    Forma_CampoOculto('ds_caption' , $ds_caption);
    Forma_CampoOculto('tr_caption' , $tr_caption);
    Forma_CampoOculto('nb_archivo' , $nb_archivo);
    Forma_CampoOculto('nb_archivo_err' , $nb_archivo_err);
    Forma_CampoOculto('tr_archivo' , $tr_archivo);
    echo "\n</form>
<script>
  document.datos.submit();
</script></body></html>";
    exit;
  }
  
  # Parametros para convertir archivos mov en flv
  $parametros = ObtenConfiguracion(12);
  
  # Recibe el archivo seleccionado
  if(!empty($_FILES['archivo']['tmp_name'])) {
    $nb_archivo = $_FILES['archivo']['name'];
    $ext = strtoupper(ObtenExtensionArchivo($nb_archivo));
    switch($ext) {
      case "SWF": $ruta = SP_FLASH; break;
      case "FLV": $ruta = SP_VIDEOS; break;
      case "MOV": $ruta = SP_VIDEOS; break;
      default:    $ruta = SP_IMAGES; break;
    }
    move_uploaded_file($_FILES['archivo']['tmp_name'], $ruta."/".$nb_archivo);
    
    # Convierte archivos .mov a .flv
    if($ext == "MOV") {
      $file_flv = substr($nb_archivo, 0, (strlen($nb_archivo)-4)) . '.flv';
      if(file_exists($ruta."/".$file_flv))
        unlink($ruta."/".$file_flv);
      $comando = CMD_FFMPEG." -i \"$ruta/$nb_archivo\" $parametros \"$ruta/$file_flv\"";
      exec($comando);
      unlink($ruta."/".$nb_archivo);
      $nb_archivo = $file_flv;
    }
  }
  if(!empty($_FILES['tr_archivo_t']['tmp_name'])) {
    $tr_archivo = $_FILES['tr_archivo_t']['name'];
    $ext = strtoupper(ObtenExtensionArchivo($tr_archivo));
    switch($ext) {
      case "SWF": $ruta = SP_FLASH; break;
      case "FLV": $ruta = SP_VIDEOS; break;
      case "MOV": $ruta = SP_VIDEOS; break;
      default:    $ruta = SP_IMAGES; break;
    }
    move_uploaded_file($_FILES['tr_archivo_t']['tmp_name'], $ruta."/".$tr_archivo);
    
    # Convierte archivos .mov a .flv
    if($ext == "MOV") {
      $file_flv = substr($tr_archivo, 0, (strlen($tr_archivo)-4)) . '.flv';
      if(file_exists($ruta."/".$file_flv))
        unlink($ruta."/".$file_flv);		
      $comando = CMD_FFMPEG." -i \"$ruta/$tr_archivo\" $parametros \"$ruta/$file_flv\"";
      exec($comando);
      unlink($ruta."/".$tr_archivo);
      $tr_archivo = $file_flv;
    }
  }
  
  # En esta funcion solo se puede actualizar
	$Query  = "UPDATE c_imagen SET ds_caption='$ds_caption', tr_caption='$tr_caption', nb_archivo='$nb_archivo', tr_archivo='$tr_archivo' ";
	$Query .= "WHERE cl_imagen=$clave";
  EjecutaQuery($Query);
  
	# Redirige al listado
  header("Location: ".ObtenProgramaBase( ));
  
?>