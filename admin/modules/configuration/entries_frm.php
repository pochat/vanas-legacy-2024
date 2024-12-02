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
  if(!ValidaPermiso(FUNC_REGISTROS, $permiso)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Inicializa variables
  if(!$fg_error) { // Sin error, viene del listado
    if(!empty($clave)) { // Actualizacion, recupera de la base de datos
      $Query  = "SELECT a.fl_tabla, a.nb_tabla, a.tr_tabla, c.no_renglon ";
      $Query .= "FROM c_tabla a, k_columna_tabla b, k_celda_tabla c ";
      $Query .= "WHERE a.fl_tabla=b.fl_tabla ";
      $Query .= "AND b.fl_columna=c.fl_columna ";
      $Query .= "AND c.fl_celda=$clave";
      $row = RecuperaValor($Query);
      $fl_tabla = $row[0];
      $nb_tabla = str_texto(EscogeIdioma($row[1], $row[2]));
      $no_renglon = $row[3];
      $Query  = "SELECT fl_columna, nb_columna, tr_columna ";
      $Query .= "FROM k_columna_tabla ";
      $Query .= "WHERE fl_tabla=$fl_tabla ";
      $Query .= "ORDER BY no_orden";
      $rs = EjecutaQuery($Query);
      for($i = 0; $row = RecuperaRegistro($rs); $i++) {
        $fl_columna[$i] = $row[0];
        $nb_columna[$i] = str_texto(EscogeIdioma($row[1], $row[2]));
        $Query  = "SELECT fl_celda, ds_celda, tr_celda, ds_href ";
        $Query .= "FROM k_celda_tabla ";
        $Query .= "WHERE fl_columna=$fl_columna[$i] ";
        $Query .= "AND no_renglon=$no_renglon";
        $row2 = RecuperaValor($Query);
        $fl_celda[$i] = $row2[0];
        $ds_celda[$i] = str_texto($row2[1]);
        $tr_celda[$i] = str_texto($row2[2]);
        $ds_href[$i] = str_texto($row2[3]);
      }
      $no_columnas = $i;
    }
    else { // Alta, inicializa campos
      $fl_tabla = "";
      $no_renglon = "";
      $no_columnas = 0;
    }
    $no_renglon_err = "";
  }
  else { // Con error, recibe parametros (viene de la pagina de actualizacion)
    $fl_tabla = RecibeParametroNumerico('fl_tabla');
    $nb_tabla = RecibeParametroHTML('nb_tabla');
    $no_renglon = RecibeParametroNumerico('no_renglon');
    $no_renglon_err = RecibeParametroNumerico('no_renglon_err');
    $no_columnas = RecibeParametroNumerico('no_columnas');
    for($i = 0; $i < $no_columnas; $i++) {
      $fl_columna[$i] = RecibeParametroNumerico('fl_columna_'.$i);
      $nb_columna[$i] = RecibeParametroHTML('nb_columna_'.$i);
      $fl_celda[$i] = RecibeParametroNumerico('fl_celda_'.$i);
      $ds_celda[$i] = RecibeParametroHTML('ds_celda_'.$i);
      $tr_celda[$i] = RecibeParametroHTML('tr_celda_'.$i);
      $ds_href[$i] = RecibeParametroHTML('ds_href_'.$i);
    }
  }
  
  # Presenta forma de captura
  PresentaHeader( );
  PresentaEncabezado(FUNC_REGISTROS);
  Forma_Inicia($clave);
  if($fg_error)
    Forma_PresentaError( );
  if(!empty($clave)) {
    Forma_CampoInfo(ObtenEtiqueta(221), $nb_tabla);
    Forma_CampoOculto('fl_tabla', $fl_tabla);
    Forma_CampoOculto('nb_tabla', $nb_tabla);
  }
  else {
    $Query  = "SELECT ".EscogeIdioma('nb_tabla', 'tr_tabla')." 'ds_tabla', fl_tabla ";
    $Query .= "FROM c_tabla ";
    $Query .= "ORDER BY ds_tabla";
    Forma_CampoSelectBD(ObtenEtiqueta(221), 'fl_tabla', $Query, $fl_tabla);
  }
  Forma_Espacio( );
  
  Forma_CampoTexto(ObtenEtiqueta(260), True, 'no_renglon', $no_renglon, 3, 5, $no_renglon_err);
  Forma_Espacio( );
  
  if(!empty($clave)) {
    for($i = 0; $i < $no_columnas; $i++) {
      Forma_CampoOculto('fl_columna_'.$i, $fl_columna[$i]);
      Forma_CampoOculto('nb_columna_'.$i, $nb_columna[$i]);
      Forma_CampoOculto('fl_celda_'.$i, $fl_celda[$i]);
      Forma_CampoTexto($nb_columna[$i], False, 'ds_celda_'.$i, $ds_celda[$i], 255, 50);
      Forma_CampoTexto(ETQ_TRADUCCION, False, 'tr_celda_'.$i, $tr_celda[$i], 255, 50);
      Forma_CampoTexto(ObtenEtiqueta(203), False, 'ds_href_'.$i, $ds_href[$i], 255, 50);
      Forma_Espacio( );
    }
  }
  Forma_CampoOculto('no_columnas', $no_columnas);
  
  # Verifica que el usuario tenga permiso para guardar los cambios (solo en modo modificacion)
  if($permiso == PERMISO_DETALLE)
    $fg_guardar = ValidaPermiso(FUNC_REGISTROS, PERMISO_MODIFICACION);
  else
    $fg_guardar = True;
  Forma_Termina($fg_guardar);
  
  # Pie de Pagina
  PresentaFooter( );
  
?>