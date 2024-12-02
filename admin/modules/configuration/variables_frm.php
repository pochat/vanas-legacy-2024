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
  if(!ValidaPermiso(FUNC_VARIABLES, $permiso)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Inicializa variables
  if(!$fg_error) { // Sin error, viene del listado
    if(!empty($clave)) { // Actualizacion, recupera de la base de datos
      $row = RecuperaValor("SELECT ds_configuracion, ds_valor FROM c_configuracion WHERE cl_configuracion=$clave");
      $ds_configuracion = str_texto($row[0]);
      $ds_valor = str_texto($row[1]);
    }
    else { // Alta, inicializa campos
      MuestraPaginaError(ERR_SIN_PERMISO);
      exit;
    }
    $ds_valor_err = "";
  }
  else { // Con error, recibe parametros (viene de la pagina de actualizacion)
    $ds_configuracion = RecibeParametroHTML('ds_configuracion');
    $ds_valor = RecibeParametroHTML('ds_valor');
    $ds_valor_err = RecibeParametroNumerico('ds_valor_err');
  }
  
  # Presenta forma de captura
  PresentaHeader( );
  PresentaEncabezado(FUNC_VARIABLES);
  Forma_Inicia($clave);
  if($fg_error)
    Forma_PresentaError( );
  Forma_CampoInfo(ETQ_CLAVE, $clave);
  Forma_CampoInfo(ObtenEtiqueta(135), $ds_configuracion);
  Forma_CampoOculto('ds_configuracion' , $ds_configuracion);
  Forma_Espacio( );
  Forma_CampoTexto(ObtenEtiqueta(136), True, 'ds_valor', $ds_valor, 255, 60, $ds_valor_err);
  
  # Verifica que el usuario tenga permiso para guardar los cambios (solo en modo modificacion)
  if($permiso == PERMISO_DETALLE)
    $fg_guardar = ValidaPermiso(FUNC_VARIABLES, PERMISO_MODIFICACION);
  else
    $fg_guardar = True;
  Forma_Termina($fg_guardar);
  
  # Pie de Pagina
  PresentaFooter( );
  
?>