<?php
  
  # Libreria de funciones
  require '../../lib/general.inc.php';
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  ValidaSesion( );
  
  # Recibe parametros
  $clave = RecibeParametroNumerico('clave');
  $fg_error = RecibeParametroNumerico('fg_error');
  
  # Determina si es alta o modificacion
  if(!empty($clave))
    $permiso = PERMISO_DETALLE;
  else
    $permiso = PERMISO_ALTA;
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermiso(FUNC_MENSAJES, $permiso)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Inicializa variables
  if(!$fg_error) { // Sin error, viene del listado
    if(!empty($clave)) { // Actualizacion, recupera de la base de datos
      $row = RecuperaValor("SELECT ds_titulo, tr_titulo, ds_mensaje, tr_mensaje, fg_severidad, fg_tipo FROM c_mensaje WHERE cl_mensaje=$clave");
      $ds_titulo = str_texto($row[0]);
      $tr_titulo = str_texto($row[1]);
      $ds_mensaje = str_texto($row[2]);
      $tr_mensaje = str_texto($row[3]);
      $fg_severidad = $row[4];
      $fg_tipo = $row[5];
    }
    else { // Alta, inicializa campos
      $ds_titulo = "";
      $tr_titulo = "";
      $ds_mensaje = "";
      $tr_mensaje = "";
      $fg_severidad = "";
      $fg_tipo = "";
    }
    $cl_mensaje_nueva = "";
    $cl_mensaje_err = "";
    $ds_mensaje_err = "";
  }
  else { // Con error, recibe parametros (viene de la pagina de actualizacion)
    $cl_mensaje_nueva = RecibeParametroNumerico('cl_mensaje_nueva');
    $cl_mensaje_err = RecibeParametroNumerico('cl_mensaje_err');
    $ds_titulo = RecibeParametroHTML('ds_titulo');
    $tr_titulo = RecibeParametroHTML('tr_titulo');
    $ds_mensaje = RecibeParametroHTML('ds_mensaje');
    $ds_mensaje_err = RecibeParametroNumerico('ds_mensaje_err');
    $tr_mensaje = RecibeParametroHTML('tr_mensaje');
    $fg_severidad = RecibeParametroHTML('fg_severidad');
    $fg_tipo = RecibeParametroNumerico('fg_tipo');
  }
  
  # Presenta forma de captura
  PresentaHeader( );
  PresentaEncabezado(FUNC_MENSAJES);
  Forma_Inicia($clave);
  if($fg_error)
    Forma_PresentaError( );
  
  # Si se esta editando
  if(!empty($clave)) {
    Forma_CampoInfo(ETQ_CLAVE, $clave);
    Forma_CampoOculto('cl_mensaje_nueva', $cl_mensaje_nueva);
  }
  else
    Forma_CampoTexto(ETQ_CLAVE, True, 'cl_mensaje_nueva', $cl_mensaje_nueva, 5, 10, $cl_mensaje_err);
  
  Forma_Espacio( );
  Forma_CampoTexto(ETQ_TITULO, False, 'ds_titulo', $ds_titulo, 255, 60);
  Forma_CampoTexto(ETQ_TRADUCCION, False, 'tr_titulo', $tr_titulo, 255, 60);
  Forma_Espacio( );
  Forma_CampoTexto(ObtenEtiqueta(130), True, 'ds_mensaje', $ds_mensaje, 500, 100, $ds_mensaje_err);
  Forma_CampoTexto(ETQ_TRADUCCION, False, 'tr_mensaje', $tr_mensaje, 500, 100);
  Forma_Espacio( );
  $opc = array(ETQ_TIT_INFO,ETQ_TIT_WARN, ETQ_TIT_ERROR, ETQ_TIT_CONFIRM);
  $val = array('I', 'W', 'E', 'P');
  Forma_CampoSelect(ObtenEtiqueta(131), False, 'fg_severidad', $opc, $val, $fg_severidad);
  $opc = array(ETQ_ACEPTAR, ETQ_ACEPTAR.'-'.ETQ_CANCELAR, ETQ_SI.'-'.ETQ_NO.'-'.ETQ_CANCELAR);
  $val = array('1', '2', '3');
  Forma_CampoSelect(ETQ_TIPO, False, 'fg_tipo', $opc, $val, $fg_tipo);
  
  # Verifica que el usuario tenga permiso para guardar los cambios (solo en modo modificacion)
  if($permiso == PERMISO_DETALLE)
    $fg_guardar = ValidaPermiso(FUNC_MENSAJES, PERMISO_MODIFICACION);
  else
    $fg_guardar = True;
  Forma_Termina($fg_guardar);
  
  # Pie de Pagina
  PresentaFooter( );
  
?>