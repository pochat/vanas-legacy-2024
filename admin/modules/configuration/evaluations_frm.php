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
  if(!ValidaPermiso(FUNC_CRITERIOS, $permiso)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Inicializa variables
  if(!$fg_error) { // Sin error, viene del listado
    if(!empty($clave)) { // Actualizacion, recupera de la base de datos
      $Query  = "SELECT ds_evaluacion, no_orden, fg_promedio ";
      $Query .= "FROM c_evaluacion ";
      $Query .= "WHERE fl_evaluacion=$clave";
      $row = RecuperaValor($Query);
      $ds_evaluacion = str_texto($row[0]);
      $no_orden = $row[1];
      $fg_promedio = $row[2];
    }
    else { // Alta, inicializa campos
      $ds_evaluacion = "";
      $no_orden = "";
      $fg_promedio = "";
    }
    $ds_evaluacion_err = "";
    $no_orden_err = "";
  }
  else { // Con error, recibe parametros (viene de la pagina de actualizacion)
    $ds_evaluacion = RecibeParametroHTML('ds_evaluacion');
    $ds_evaluacion_err = RecibeParametroNumerico('ds_evaluacion_err');
    $no_orden = RecibeParametroNumerico('no_orden');
    $no_orden_err = RecibeParametroNumerico('no_orden_err');
    $fg_promedio = RecibeParametroBinario('fg_promedio');
  }
  
  # Presenta forma de captura
  PresentaHeader( );
  PresentaEncabezado(FUNC_CRITERIOS);
  
  # Inicia forma de captura
  Forma_Inicia($clave);
  if($fg_error)
    Forma_PresentaError( );
  
  # Campos de captura
  Forma_CampoTexto(ObtenEtiqueta(430), True, 'ds_evaluacion', $ds_evaluacion, 50, 50, $ds_evaluacion_err);
  Forma_Espacio( );
  
  Forma_CampoTexto(ETQ_ORDEN, True, 'no_orden', $no_orden, 3, 5, $no_orden_err);
  Forma_CampoCheckbox(ObtenEtiqueta(431), 'fg_promedio', $fg_promedio, ObtenEtiqueta(432));
  Forma_Espacio( );
  
  # Verifica que el usuario tenga permiso para guardar los cambios (solo en modo modificacion)
  if($permiso == PERMISO_DETALLE)
    $fg_guardar = ValidaPermiso(FUNC_CRITERIOS, PERMISO_MODIFICACION);
  else
    $fg_guardar = True;
  Forma_Termina($fg_guardar);
  
  # Pie de Pagina
  PresentaFooter( );
  
?>