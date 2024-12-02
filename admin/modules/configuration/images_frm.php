<?php
  
  # Libreria de funciones
  require '../../lib/general.inc.php';
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  ValidaSesion( );
  
  # Recibe parametro
  $clave = RecibeParametroNumerico('clave');
  $fg_error = RecibeParametroNumerico('fg_error');
  
  # Determina si es alta o modificacion
  if(!empty($clave))
    $permiso = PERMISO_DETALLE;
  else {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermiso(FUNC_IMAGENES, $permiso)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Inicializa variables
  if(!$fg_error) { // Sin error, viene del listado
    if(!empty($clave)) { // Actualizacion, recupera de la base de datos
      $row = RecuperaValor("SELECT ds_imagen, ds_caption, tr_caption, nb_archivo, tr_archivo FROM c_imagen WHERE cl_imagen=$clave");
      $ds_imagen = str_texto($row[0]);
      $ds_caption = str_texto($row[1]);
      $tr_caption = str_texto($row[2]);
      $nb_archivo = str_texto($row[3]);
      $tr_archivo = str_texto($row[4]);
    }
    else { // Alta, inicializa campos
      MuestraPaginaError(ERR_SIN_PERMISO);
      exit;
    }
    $nb_archivo_err = "";
  }
  else { // Con error, recibe parametros (viene de la pagina de actualizacion)
    $ds_imagen = RecibeParametroHTML('ds_imagen');
    $ds_caption = RecibeParametroHTML('ds_caption');
    $tr_caption = RecibeParametroHTML('tr_caption');
    $nb_archivo = RecibeParametroHTML('nb_archivo');
    $nb_archivo_err = RecibeParametroNumerico('nb_archivo_err');
    $tr_archivo = RecibeParametroHTML('tr_archivo');
  }
  
  # Presenta forma de captura
  PresentaHeader( );
  PresentaEncabezado(FUNC_IMAGENES);
  
  # Ventana para preview
  require 'preview.inc.php';
  
  # Forma para captura de datos
  Forma_Inicia($clave, True);
  if($fg_error)
    Forma_PresentaError( );
  Forma_CampoInfo(ETQ_CLAVE, $clave);
  Forma_CampoInfo(ETQ_DESCRIPCION, $ds_imagen);
  Forma_CampoOculto('ds_imagen' , $ds_imagen);
  Forma_Espacio( );
  if(!empty($nb_archivo)) {
    Forma_Sencilla_Ini(ObtenEtiqueta(208));
    $ext = strtoupper(ObtenExtensionArchivo($nb_archivo));
    switch($ext) {
      case "SWF": $ruta = SP_FLASH_W; break;
      case "FLV": $ruta = SP_VIDEOS_W; break;
      default:    $ruta = SP_IMAGES_W; break;
    }
    if($ext <> "EXE" AND $ext <> "PDF" AND $ext <> "ZIP") {
      if($ext <> "FLV") // Preview de imagenes o flash
        echo "<a href=\"javascript:Preview('$ruta/".$nb_archivo."');\">$nb_archivo</a>";
      else // Preview Archivos FLV
        echo "<a href='preview_flv.php?archivo=$nb_archivo' target='_blank'>$nb_archivo</a>";
    }
    else // Archivos EXE PDF y ZIP, pone liga para descargar el archivo 
      echo "<a href=\"$ruta/".$nb_archivo."\" target='_blank'>$nb_archivo</a>";
    Forma_Sencilla_Fin( );
    Forma_CampoArchivo(ObtenEtiqueta(216), False, 'archivo', 60);
    Forma_CampoOculto('nb_archivo', $nb_archivo);
  }
  else
    Forma_CampoArchivo(ObtenEtiqueta(208), True, 'archivo', 60, $nb_archivo_err);
  Forma_CampoTexto(ObtenEtiqueta(207), False, 'ds_caption', $ds_caption, 50, 30);
  Forma_Espacio( );
  if(!empty($tr_archivo)) {
    Forma_Sencilla_Ini(ObtenEtiqueta(245));
    $ext = strtoupper(ObtenExtensionArchivo($tr_archivo));
    switch($ext) {
      case "SWF": $ruta = SP_FLASH_W; break;
      case "FLV": $ruta = SP_VIDEOS_W; break;
      default:    $ruta = SP_IMAGES_W; break;
    }
    if($ext <> "EXE" AND $ext <> "PDF" AND $ext <> "ZIP") {
      if($ext <> "FLV") // Preview de imagenes o flash
        echo "<a href=\"javascript:Preview('$ruta/".$tr_archivo."');\">$tr_archivo</a>";
      else // Preview Archivos FLV
        echo "<a href='preview_flv.php?archivo=$tr_archivo' target='_blank'>$tr_archivo</a>";
    }
    else // Archivos EXE PDF y ZIP, pone liga para descargar el archivo 
      echo "<a href=\"$ruta/".$tr_archivo."\" target='_blank'>$tr_archivo</a>";
    Forma_Sencilla_Fin( );
    Forma_CampoArchivo(ObtenEtiqueta(216), False, 'tr_archivo_t', 60);
    Forma_CampoOculto('tr_archivo', $tr_archivo);
  }
  else
    Forma_CampoArchivo(ObtenEtiqueta(245), False, 'tr_archivo_t', 60);
  Forma_CampoTexto(ObtenEtiqueta(207), False, 'tr_caption', $tr_caption, 50, 30);
  
  # Verifica que el usuario tenga permiso para guardar los cambios (solo en modo modificacion)
  if($permiso == PERMISO_DETALLE)
    $fg_guardar = ValidaPermiso(FUNC_IMAGENES, PERMISO_MODIFICACION);
  else
    $fg_guardar = True;
  Forma_Termina($fg_guardar);
  
  # Pie de Pagina
  PresentaFooter( );
  
?>