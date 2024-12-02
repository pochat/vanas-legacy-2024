<?php
  
  # Libreria de funciones
  require '../../lib/general.inc.php';
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  ValidaSesion( );
  
  # Recibe la clave
  $clave = RecibeParametroNumerico('clave');
  
  # Determina si es alta o modificacion
  if(!empty($clave))
    $permiso = PERMISO_MODIFICACION;
  else
    $permiso = PERMISO_ALTA;
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermiso(FUNC_BLOGS, $permiso)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Recibe parametros
  $fg_error = 0;
  $fe_blog = RecibeParametroFecha('fe_blog');
  $ds_titulo = RecibeParametroHTML('ds_titulo');
  $ds_resumen = RecibeParametroHTML('ds_resumen');
  $ds_blog = RecibeParametroHTML('ds_blog');
  $ds_ruta_imagen = RecibeParametroHTML('ds_ruta_imagen');
  $ds_ruta_video = RecibeParametroHTML('ds_ruta_video');
  $archivo = RecibeParametroHTML('archivo');
  $fl_usuario = RecibeParametroNumerico('fl_usuario');
  $nb_usuario = RecibeParametroHTML('nb_usuario');
  $fg_maestros = RecibeParametroBinario('fg_maestros');
  $fg_alumnos = RecibeParametroBinario('fg_alumnos');
  $fg_notificacion = RecibeParametroBinario('fg_notificacion');
  $no_hits = RecibeParametroNumerico('no_hits');
  
  # Valida campos obligatorios
  if(empty($ds_titulo))
    $ds_titulo_err = ERR_REQUERIDO;
  if(empty($fe_blog))
    $fe_blog_err = ERR_REQUERIDO;
  
  # Verifica que el formato de la fecha sea valido
  if(!empty($fe_blog) AND !ValidaFecha($fe_blog))
    $fe_blog_err = ERR_FORMATO_FECHA;
  
  # Verifica que el tipo de archivo para avatar sea JPG
  $ext = strtolower(ObtenExtensionArchivo($_FILES['archivo_img']['name']));
  if(!empty($ext) AND $ext!='jpg')
    $ds_ruta_imagen_err = ERR_ARCHIVO_JPEG;  
  
  # Regresa a la forma con error
  $fg_error = $ds_titulo_err || $fe_blog_err || $ds_ruta_imagen_err;
  
  if($fg_error) {
    echo "<html><body><form name='datos' method='post' action='".ObtenProgramaNombre(PGM_FORM)."'>\n";
    Forma_CampoOculto('clave', $clave);
    Forma_CampoOculto('fg_error', $fg_error);
    Forma_CampoOculto('fe_blog', $fe_blog);
    Forma_CampoOculto('fe_blog_err', $fe_blog_err);
    Forma_CampoOculto('ds_titulo', $ds_titulo);
    Forma_CampoOculto('ds_titulo_err', $ds_titulo_err);
    Forma_CampoOculto('ds_resumen', $ds_resumen);
    Forma_CampoOculto('ds_blog', $ds_blog);
    Forma_CampoOculto('ds_ruta_imagen', $ds_ruta_imagen);
    Forma_CampoOculto('ds_ruta_imagen_err', $ds_ruta_imagen_err);
    Forma_CampoOculto('ds_ruta_video', $ds_ruta_video);
    Forma_CampoOculto('archivo', $archivo);
    Forma_CampoOculto('fl_usuario', $fl_usuario);
    Forma_CampoOculto('nb_usuario', $nb_usuario);
    Forma_CampoOculto('fg_maestros', $fg_maestros);
    Forma_CampoOculto('fg_alumnos', $fg_alumnos);
    Forma_CampoOculto('fg_notificacion', $fg_notificacion);
    Forma_CampoOculto('no_hits', $no_hits);
    echo "\n</form>
<script>
  document.datos.submit();
</script></body></html>";
    exit;
  }
  
  # Parametros para convertir archivos mov en flv
  $parametros = ObtenConfiguracion(12);
  $ruta_tmp = $_SERVER[DOCUMENT_ROOT].PATH_TMP;
  $ruta_str = PATH_STREAMING;
  
  # Recibe el archivo seleccionado
  if(!empty($_FILES['archivo_img']['tmp_name'])) {
    $nb_archivo_img = $_FILES['archivo_img']['name'];
    $ext = strtoupper(ObtenExtensionArchivo($nb_archivo_img));
    $ruta = SP_IMAGES;
    move_uploaded_file($_FILES['archivo_img']['tmp_name'], $ruta."/news/".$nb_archivo_img);
    
    # Genera thumbnail para la imagen
    if($ext == "JPG") {
      CreaThumb($ruta."/news/".$nb_archivo_img, SP_IMAGES."/news/".$nb_archivo_img, ObtenConfiguracion(27), 0, 0);
      CreaThumb($ruta."/news/".$nb_archivo_img, SP_THUMBS."/news/".$nb_archivo_img, ObtenConfiguracion(28), 0, 0);
    }
  }
  else
    $nb_archivo_img = $ds_ruta_imagen;
     
  # Recibe archivo de video
  if(!empty($archivo)) {
    $archivo2 = 'S_NEWS_'.$archivo;
    $ds_vl_ruta = $archivo2;
    
    # Mueve el archivo subido al directorio para streaming
    rename($ruta_tmp."/".$archivo, $ruta_str."/".$ds_vl_ruta);
    
    # Creacion de liga para servidor de streaming
    $comando_2 = "ln -s \"".$ruta_str."/".$ds_vl_ruta."\" ".PATH_LINKS;
  }
  else
    $ds_vl_ruta = $ds_ruta_video;
  
  # Prepara fechas en formato para insertar
  if(!empty($fe_blog))
    $fe_blog = "'".ValidaFecha($fe_blog)."'";
  else
    $fe_blog = "NULL";
  
  # Inserta o actualiza el registro
  if(empty($clave)) {
    $Query  = "INSERT INTO c_blog (fe_blog, ds_titulo, ds_resumen, ds_blog, ds_ruta_imagen, ds_ruta_video, fl_usuario, fg_maestros, fg_alumnos, ";
    $Query .= "fg_notificacion) ";
    $Query .= "VALUES($fe_blog, '$ds_titulo', '$ds_resumen', '$ds_blog', '$nb_archivo_img', '$ds_vl_ruta', $fl_usuario, ";
    $Query .= "'$fg_maestros', '$fg_alumnos', '$fg_notificacion')";
    $fl_blog = EjecutaInsert($Query);
    
    # Envia Notificaciones
    if($fg_notificacion == "1") {
      if($fg_maestros == "1") {
        $rs = EjecutaQuery("SELECT fl_maestro FROM c_maestro");
        while($row = RecuperaRegistro($rs))
          EjecutaQuery("INSERT INTO k_not_blog (fl_blog, fl_usuario) VALUES($fl_blog, $row[0])");
      }
      if($fg_alumnos == "1") {
        $rs = EjecutaQuery("SELECT fl_alumno FROM c_alumno");
        while($row = RecuperaRegistro($rs))
          EjecutaQuery("INSERT INTO k_not_blog (fl_blog, fl_usuario) VALUES($fl_blog, $row[0])");
      }
    }
  }
  else {
    $Query  = "UPDATE c_blog SET fe_blog=$fe_blog, ds_titulo='$ds_titulo', ds_resumen='$ds_resumen', ds_blog='$ds_blog', ";
    $Query .= "ds_ruta_imagen='$nb_archivo_img', ds_ruta_video='$ds_vl_ruta' ";
    $Query .= "WHERE fl_blog=$clave";
    EjecutaQuery($Query);
  }

  # Send out email notifications
  if(empty($clave) && $fg_notificacion == "1") {
    require 'schoolnews_iu_sendmail.php';
  }
  
  # Crea liga archivo
  if(!empty($comando_2) AND FG_PRODUCCION)
    exec($comando_2);
  
  # Redirige al listado
  header("Location: ".ObtenProgramaBase( ));
  
?>