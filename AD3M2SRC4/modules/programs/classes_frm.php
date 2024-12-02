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
  if(!ValidaPermiso(FUNC_CLASSES, $permiso)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Inicializa variables
  if(!$fg_error) { // Sin error, viene del listado
    if(!empty($clave)) { // Actualizacion, recupera de la base de datos
      $Query  = "SELECT fl_programa, nb_class, ds_class, no_grado, no_orden ";
      $Query .= "FROM c_class ";
      $Query .= "WHERE fl_class=$clave";
      $row = RecuperaValor($Query);
      $fl_programa = $row[0];
      $nb_class = str_texto($row[1]);
      $ds_class = str_texto($row[2]);
      $no_grado = $row[3];
      $no_orden = $row[4];
      
    }
    else { // Alta, inicializa campos
      $fl_programa = "";
      $nb_class = "";
      $ds_class = "";
      $no_grado = "";
      $no_orden = "0";
    }
    $fl_programa_err = "";
    $nb_class_err = "";
    $ds_class_err = "";
    $no_grado_err = "";
    $no_orden_err = "";
  }
  else { // Con error, recibe parametros (viene de la pagina de actualizacion)
    $fl_programa = RecibeParametroNumerico('fl_programa');
    $fl_programa_err = RecibeParametroNumerico('fl_programa_err');
    $nb_class = RecibeParametroHTML('nb_class');
    $nb_class_err = RecibeParametroNumerico('nb_class_err');
    $ds_class = RecibeParametroHTML('ds_class');
    $ds_class_err = RecibeParametroNumerico('ds_class_err');
    $no_grado = RecibeParametroNumerico('no_grado');
    $no_grado_err = RecibeParametroNumerico('no_grado_err');
    $no_orden = RecibeParametroNumerico('no_orden');
    $no_orden_err = RecibeParametroNumerico('no_orden_err');
  }
  
  # Presenta forma de captura
  PresentaHeader( );
  PresentaEncabezado(FUNC_CLASSES);
  
  # Inicia forma de captura
  Forma_Inicia($clave);
  if($fg_error)
    Forma_PresentaError( );
  
  # Campos de captura
  $Query  = "SELECT nb_programa, fl_programa FROM c_programa ORDER BY no_orden";
  Forma_CampoSelectBD(ObtenEtiqueta(380), False, 'fl_programa', $Query, $fl_programa);
  Forma_Espacio( );
  
  Forma_CampoTexto(ObtenEtiqueta(640), True, 'nb_class', $nb_class, 50, 30, $nb_class_err);
  Forma_CampoTexto(ETQ_DESCRIPCION, True, 'ds_class', $ds_class, 80, 50, $ds_class_err);
  Forma_Espacio( );

  Forma_CampoTexto(ObtenEtiqueta(375), True, 'no_grado', $no_grado, 3, 5, $no_grado_err);
  Forma_Espacio( );

  Forma_CampoTexto(ETQ_ORDEN, True, 'no_orden', $no_orden, 3, 5, $no_orden_err);
  
  # Verifica que el usuario tenga permiso para guardar los cambios (solo en modo modificacion)
  if($permiso == PERMISO_DETALLE)
    $fg_guardar = ValidaPermiso(FUNC_CLASSES, PERMISO_MODIFICACION);
  else
    $fg_guardar = True;
  Forma_Termina($fg_guardar);
  
  # Pie de Pagina
  PresentaFooter( );
  
?>