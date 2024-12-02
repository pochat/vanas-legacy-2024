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
  else
    $permiso = PERMISO_ALTA;
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermiso(FUNC_ZONAS, $permiso)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Inicializa variables
  if(!$fg_error) { // Sin error, viene del listado
    if(!empty($clave)) { // Actualizacion, recupera de la base de datos
      $Query  = "SELECT nb_zona_horaria, no_gmt, fg_default, no_latitude, fg_latitude, no_longitude, fg_longitude ";
      $Query .= "FROM c_zona_horaria ";
      $Query .= "WHERE fl_zona_horaria=$clave";
      $row = RecuperaValor($Query);
      $nb_zona_horaria = str_texto($row[0]);
      $no_gmt = $row[1];
      $fg_default = $row[2];
      $no_latitude = $row[3];
      $fg_latitude = $row[4];
      $no_longitude = $row[5];
      $fg_longitude = $row[6];
    }
    else { // Alta, inicializa campos
      $nb_zona_horaria = "";
      $no_gmt = "";
      $fg_default = "";
      $no_latitude = "0.0";
      $fg_latitude = "N";
      $no_longitude = "0.0";
      $fg_longitude = "W";
    }
    $nb_zona_horaria_err = "";
    $no_gmt_err = "";
  }
  else { // Con error, recibe parametros (viene de la pagina de actualizacion)
    $nb_zona_horaria = RecibeParametroHTML('nb_zona_horaria');
    $nb_zona_horaria_err = RecibeParametroNumerico('nb_zona_horaria_err');
    $no_gmt = RecibeParametroFlotante('no_gmt');
    $no_gmt_err = RecibeParametroNumerico('no_gmt_err');
    $fg_default = RecibeParametroBinario('fg_default');
    $no_latitude = RecibeParametroFlotante('no_latitude');
    $fg_latitude = RecibeParametroHTML('fg_latitude');
    $no_longitude = RecibeParametroFlotante('no_longitude');
    $fg_longitude = RecibeParametroHTML('fg_longitude');
  }
  
  # Presenta forma de captura
  PresentaHeader( );
  PresentaEncabezado(FUNC_ZONAS);
  
  # Inicia forma de captura
  Forma_Inicia($clave);
  if($fg_error)
    Forma_PresentaError( );
  
  # Revisa si es un registro nuevo
  Forma_CampoTexto(ObtenEtiqueta(440), True, 'nb_zona_horaria', $nb_zona_horaria, 100, 50, $nb_zona_horaria_err);
  Forma_Espacio( );
  Forma_CampoTexto(ObtenEtiqueta(441), True, 'no_gmt', $no_gmt, 6, 5, $no_gmt_err);
  Forma_CampoCheckbox(ObtenEtiqueta(442), 'fg_default', $fg_default);
  Forma_Espacio( );
  Forma_CampoTexto(ObtenEtiqueta(443), False, 'no_latitude', $no_latitude, 9, 10);
  $opc = array(ObtenEtiqueta(445), ObtenEtiqueta(446)); // North-South
  $val = array('N', 'S');
  Forma_CampoSelect('', False, 'fg_latitude', $opc, $val, $fg_latitude);
  Forma_Espacio( );
  Forma_CampoTexto(ObtenEtiqueta(444), False, 'no_longitude', $no_longitude, 9, 10);
  $opc = array(ObtenEtiqueta(448), ObtenEtiqueta(447)); // West-East
  $val = array('W', 'E');
  Forma_CampoSelect('', False, 'fg_longitude', $opc, $val, $fg_longitude);
  Forma_Espacio( );
  
  # Verifica que el usuario tenga permiso para guardar los cambios (solo en modo modificacion)
  if($permiso == PERMISO_DETALLE)
    $fg_guardar = ValidaPermiso(FUNC_ZONAS, PERMISO_MODIFICACION);
  else
    $fg_guardar = True;
  Forma_Termina($fg_guardar);
  
  # Pie de Pagina
  PresentaFooter( );
  
?>