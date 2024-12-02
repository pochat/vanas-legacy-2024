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
  if(!ValidaPermiso(FUNC_PAISES, $permiso)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Inicializa variables
  if(!$fg_error) { // Sin error, viene del listado
    if(!empty($clave)) { // Actualizacion, recupera de la base de datos
      $Query  = "SELECT cl_iso2, nb_pais, ds_pais, cl_iso3, cl_iso_num ";
      $Query .= "FROM c_pais ";
      $Query .= "WHERE fl_pais=$clave";
      $row = RecuperaValor($Query);
      $cl_iso2 = str_texto($row[0]);
      $nb_pais = str_texto($row[1]);
      $ds_pais = str_texto($row[2]);
      $cl_iso3 = str_texto($row[3]);
      $cl_iso_num = str_texto($row[4]);
    }
    else { // Alta, inicializa campos
      $cl_iso2 = "";
      $nb_pais = "";
      $ds_pais = "";
      $cl_iso3 = "";
      $cl_iso_num = "";
    }
    $ds_pais_err = "";
  }
  else { // Con error, recibe parametros (viene de la pagina de actualizacion)
    $cl_iso2 = RecibeParametroHTML('cl_iso2');
    $nb_pais = RecibeParametroHTML('nb_pais');
    $ds_pais = RecibeParametroHTML('ds_pais');
    $ds_pais_err = RecibeParametroNumerico('ds_pais_err');
    $cl_iso3 = RecibeParametroHTML('cl_iso3');
    $cl_iso_num = RecibeParametroHTML('cl_iso_num');
  }
  
  # Presenta forma de captura
  PresentaHeader( );
  PresentaEncabezado(FUNC_PAISES);
  
  # Inicia forma de captura
  Forma_Inicia($clave);
  if($fg_error)
    Forma_PresentaError( );
  
  # Revisa si es un registro nuevo
  Forma_CampoTexto(ObtenEtiqueta(350), False, 'cl_iso2', $cl_iso2, 2, 5);
  Forma_CampoTexto(ObtenEtiqueta(351), False, 'nb_pais', $nb_pais, 50, 30);
  Forma_CampoTexto(ObtenEtiqueta(352), True, 'ds_pais', $ds_pais, 80, 50, $ds_pais_err);
  Forma_CampoTexto(ObtenEtiqueta(353), False, 'cl_iso3', $cl_iso3, 3, 5);
  Forma_CampoTexto(ObtenEtiqueta(354), False, 'cl_iso_num', $cl_iso_num, 3, 5);
  
  # Verifica que el usuario tenga permiso para guardar los cambios (solo en modo modificacion)
  if($permiso == PERMISO_DETALLE)
    $fg_guardar = ValidaPermiso(FUNC_PAISES, PERMISO_MODIFICACION);
  else
    $fg_guardar = True;
  Forma_Termina($fg_guardar);
  
  # Pie de Pagina
  PresentaFooter( );
  
?>