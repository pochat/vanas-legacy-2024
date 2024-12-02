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
  if(!ValidaPermiso(FUNC_ESCALAS, $permiso)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Inicializa variables
  if(!$fg_error) { // Sin error, viene del listado
    if(!empty($clave)) { // Actualizacion, recupera de la base de datos
      $Query  = "SELECT cl_calificacion, ds_calificacion, fg_aprobado, no_equivalencia, no_min, no_max ";
      $Query .= "FROM c_calificacion ";
      $Query .= "WHERE fl_calificacion=$clave";
      $row = RecuperaValor($Query);
      $cl_calificacion = str_texto($row[0]);
      $ds_calificacion = str_texto($row[1]);
      $fg_aprobado = $row[2];
      $no_equivalencia = $row[3];
      $no_min = $row[4];
      $no_max = $row[5];
    }
    else { // Alta, inicializa campos
      $cl_calificacion = "";
      $ds_calificacion = "";
      $fg_aprobado = "1";
      $no_equivalencia = "0.0";
      $no_min = "0.0";
      $no_max = "0.0";
    }
    $cl_calificacion_err = "";
    $no_equivalencia_err = "";
    $no_min_err = "";
    $no_max_err = "";
  }
  else { // Con error, recibe parametros (viene de la pagina de actualizacion)
    $cl_calificacion = RecibeParametroHTML('cl_calificacion');
    $cl_calificacion_err = RecibeParametroNumerico('cl_calificacion_err');
    $ds_calificacion = RecibeParametroHTML('ds_calificacion');
    $fg_aprobado = RecibeParametroBinario('fg_aprobado');
    $no_equivalencia = RecibeParametroFlotante('no_equivalencia');
    $no_equivalencia_err = RecibeParametroNumerico('no_equivalencia_err');
    $no_min = RecibeParametroFlotante('no_min');
    $no_min_err = RecibeParametroNumerico('no_min_err');
    $no_max = RecibeParametroFlotante('no_max');
    $no_max_err = RecibeParametroNumerico('no_max_err');
  }
  
  # Presenta forma de captura
  PresentaHeader( );
  PresentaEncabezado(FUNC_ESCALAS);
  
  # Inicia forma de captura
  Forma_Inicia($clave);
  if($fg_error)
    Forma_PresentaError( );
  
  # Campos de captura
  Forma_CampoTexto(ObtenEtiqueta(400), True, 'cl_calificacion', $cl_calificacion, 4, 5, $cl_calificacion_err);
  Forma_CampoTexto(ETQ_DESCRIPCION, False, 'ds_calificacion', $ds_calificacion, 100, 50);
  Forma_Espacio( );
  
  Forma_CampoCheckbox(ObtenEtiqueta(401), 'fg_aprobado', $fg_aprobado);
  Forma_CampoTexto(ObtenEtiqueta(402), True, 'no_equivalencia', $no_equivalencia, 6, 5, $no_equivalencia_err);
  Forma_CampoTexto(ObtenEtiqueta(403), True, 'no_min', $no_min, 6, 5, $no_min_err);
  Forma_CampoTexto(ObtenEtiqueta(404), True, 'no_max', $no_max, 6, 5, $no_max_err);
  Forma_Espacio( );
  
  # Verifica que el usuario tenga permiso para guardar los cambios (solo en modo modificacion)
  if($permiso == PERMISO_DETALLE)
    $fg_guardar = ValidaPermiso(FUNC_ESCALAS, PERMISO_MODIFICACION);
  else
    $fg_guardar = True;
  Forma_Termina($fg_guardar);
  
  # Pie de Pagina
  PresentaFooter( );
  
?>