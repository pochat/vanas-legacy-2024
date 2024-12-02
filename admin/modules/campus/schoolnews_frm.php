<?php
  
  # Libreria de funciones
  require '../../lib/general.inc.php';
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  $fl_usuario_mod = ValidaSesion( );
  
  # Recibe parametro
  $clave = RecibeParametroNumerico('clave');
  $fg_error = RecibeParametroNumerico('fg_error');
  
  # Determina si es alta o modificacion
  if(!empty($clave))
    $permiso = PERMISO_DETALLE;
  else
    $permiso = PERMISO_ALTA;
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermiso(FUNC_BLOGS, $permiso)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Inicializa variables
  if(!$fg_error) { // Sin error, viene del listado
    if(!empty($clave)) { // Actualizacion, recupera de la base de datos
      $Query  = "SELECT ".ConsultaFechaBD('fe_blog', FMT_CAPTURA)." 'fe_blog', ds_titulo, ds_resumen, ds_blog, ds_ruta_imagen, ds_ruta_video, ";
      $Query .= "a.fl_usuario, fg_maestros, fg_alumnos, fg_notificacion, no_hits, ";
      $concat = array('ds_nombres', "' '", 'ds_apaterno', "' '", NulosBD('ds_amaterno', ''));
      $Query .= ConcatenaBD($concat)." 'nb_usuario' ";
      $Query .= "FROM c_blog a, c_usuario b ";
      $Query .= "WHERE a.fl_usuario=b.fl_usuario ";
      $Query .= "AND fl_blog=$clave";
      $row = RecuperaValor($Query);
      $fe_blog = $row[0];
      $ds_titulo = str_texto($row[1]);
      $ds_resumen = str_texto($row[2]);
      $ds_blog = str_texto($row[3]);
      $ds_ruta_imagen = str_texto($row[4]);
      $ds_ruta_video = str_texto($row[5]);
      $fl_usuario = $row[6];
      $fg_maestros = $row[7];
      $fg_alumnos = $row[8];
      $fg_notificacion = $row[9];
      $no_hits = $row[10];
      $nb_usuario = str_texto($row[11]);
    }
    else { // Alta, inicializa campos
      $fe_blog = date(EscogeIdioma("d-m-Y", "m-d-Y"));
      $ds_titulo = "";
      $ds_resumen = "";
      $ds_blog = "";
      $ds_ruta_imagen = "";
      $ds_ruta_video = "";
      $fl_usuario = $fl_usuario_mod;
      $fg_maestros = "";
      $fg_alumnos = "";
      $fg_notificacion = "";
      $no_hits = "0";
      $concat = array('ds_nombres', "' '", 'ds_apaterno', "' '", NulosBD('ds_amaterno', ''));
      $Query  = "SELECT ".ConcatenaBD($concat)." 'nb_usuario' ";
      $Query .= "FROM c_usuario ";
      $Query .= "WHERE fl_usuario=$fl_usuario_mod";
      $row = RecuperaValor($Query);
      $nb_usuario = str_texto($row[0]);
    }
    $fe_blog_err = "";
    $ds_titulo_err = "";
  }
  else { // Con error, recibe parametros (viene de la pagina de actualizacion)
    $fe_blog = RecibeParametroFecha('fe_blog');
    $fe_blog_err = RecibeParametroNumerico('fe_blog_err');
    $ds_titulo = RecibeParametroHTML('ds_titulo');
    $ds_titulo_err = RecibeParametroNumerico('ds_titulo_err');
    $ds_resumen = RecibeParametroHTML('ds_resumen');
    $ds_blog = RecibeParametroHTML('ds_blog');
    $ds_ruta_imagen = RecibeParametroHTML('ds_ruta_imagen');
    $ds_ruta_imagen_err = RecibeParametroHTML('ds_ruta_imagen_err');
    $ds_ruta_video = RecibeParametroHTML('ds_ruta_video');
    $archivo = RecibeParametroHTML('archivo');
    $fl_usuario = RecibeParametroNumerico('fl_usuario');
    $nb_usuario = RecibeParametroHTML('nb_usuario');
    $fg_maestros = RecibeParametroBinario('fg_maestros');
    $fg_alumnos = RecibeParametroBinario('fg_alumnos');
    $fg_notificacion = RecibeParametroBinario('fg_notificacion');
    $no_hits = RecibeParametroNumerico('no_hits');
  }
  
  # Presenta forma de captura
  PresentaHeader( );
  PresentaEncabezado(FUNC_BLOGS);
  
  # Ventana para preview
  require 'preview.inc.php';
  
  # Inicia forma de captura
  Forma_Inicia($clave, True);
  if($fg_error)
    Forma_PresentaError( );
  
  # Datos generales
  Forma_CampoInfo(ObtenEtiqueta(454), $nb_usuario);
  Forma_CampoOculto('fl_usuario', $fl_usuario);
  Forma_CampoOculto('nb_usuario', $nb_usuario);
  Forma_CampoTexto(ObtenEtiqueta(450).' '.ETQ_FMT_FECHA, True, 'fe_blog', $fe_blog, 10, 10, $fe_blog_err);
  Forma_Calendario('fe_blog');
  Forma_CampoInfo(ObtenEtiqueta(455), $no_hits);
  Forma_CampoOculto('no_hits', $no_hits);
  Forma_Espacio( );
  
  # Datos de comportamiento para las notificaciones
  if(!empty($clave)) {
    if($fg_maestros == "1")
      Forma_CampoInfo(ObtenEtiqueta(451), ETQ_SI);
    else
      Forma_CampoInfo(ObtenEtiqueta(451), ETQ_NO);
    Forma_CampoOculto('fg_maestros', $fg_maestros);
    if($fg_alumnos == "1")
      Forma_CampoInfo(ObtenEtiqueta(452), ETQ_SI);
    else
      Forma_CampoInfo(ObtenEtiqueta(452), ETQ_NO);
    Forma_CampoOculto('fg_alumnos', $fg_alumnos);
    if($fg_notificacion == "1")
      Forma_CampoInfo(ObtenEtiqueta(453), ETQ_SI);
    else
      Forma_CampoInfo(ObtenEtiqueta(453), ETQ_NO);
    Forma_CampoOculto('fg_notificacion', $fg_notificacion);
  }
  else {
    Forma_CampoCheckbox(ObtenEtiqueta(451), 'fg_maestros', $fg_maestros);
    Forma_CampoCheckbox(ObtenEtiqueta(452), 'fg_alumnos', $fg_alumnos);
    Forma_CampoCheckbox(ObtenEtiqueta(453), 'fg_notificacion', $fg_notificacion);
  }
  Forma_Espacio( );
  
  # Cuerpo de la noticia
  Forma_CampoTexto(ETQ_TITULO, True, 'ds_titulo', $ds_titulo, 100, 70, $ds_titulo_err);
  Forma_CampoTinyMCE(ObtenEtiqueta(191), False, 'ds_resumen', $ds_resumen, 50, 5);
  Forma_CampoTinyMCE(ObtenEtiqueta(456), False, 'ds_blog', $ds_blog, 50, 20);
  
  # Carga de archivos anexos
  if(!empty($ds_ruta_imagen)) {
    Forma_CampoPreview(ObtenEtiqueta(208), 'ds_ruta_imagen', $ds_ruta_imagen, SP_IMAGES_W."/news", False, True);
    Forma_CampoArchivo(ObtenEtiqueta(216), False, 'archivo_img', 70, $ds_ruta_imagen_err);
  }
  else
    Forma_CampoArchivo(ObtenEtiqueta(208), False, 'archivo_img', 70);
  Forma_CampoInfo('NOTE', ObtenEtiqueta(458)); // NOTE: (Explicacion uso imagen y thumb) 
  Forma_Espacio( );
  if(!empty($ds_ruta_video)) {
    $ext = strtoupper(ObtenExtensionArchivo($ds_ruta_video));
    switch($ext) {
      case "SWF": $ruta = SP_FLASH_W; break;
      case "FLV": $ruta = PATH_STREAMING; break;
      default:    $ruta = SP_VIDEOS_W; break;
    }
    Forma_CampoPreview(ObtenEtiqueta(457), 'ds_ruta_video', $ds_ruta_video, $ruta, True);
    Forma_FileUploader(ObtenEtiqueta(216), False, 'archivo', "'flv', 'mov', 'mp4'", '500 * 1024 * 1024', '', False);
  }
  else
    Forma_FileUploader(ObtenEtiqueta(457), False, 'archivo', "'flv', 'mov', 'mp4'", '500 * 1024 * 1024', '', False);
  Forma_CampoInfo('NOTE', ObtenEtiqueta(459)); // NOTE: (Explicacion uso de imagen o video) 
  Forma_CampoInfo('NOTE', ObtenEtiqueta(187)); // NOTE: (Explicacion del codigo a usar para incrustar un video en el TinyMCE)
  Forma_Espacio( );
  
  # Verifica que el usuario tenga permiso para guardar los cambios (solo en modo modificacion)
  if($permiso == PERMISO_DETALLE)
    $fg_guardar = ValidaPermiso(FUNC_BLOGS, PERMISO_MODIFICACION);
  else
    $fg_guardar = True;
  Forma_Termina($fg_guardar);
  
  # Pie de Pagina
  PresentaFooter( );
  
?>