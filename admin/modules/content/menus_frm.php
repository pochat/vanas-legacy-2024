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
  if(!ValidaPermiso(FUNC_MENUS, $permiso)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Inicializa variables
  if(!$fg_error) { // Sin error, viene del listado
    if(!empty($clave)) { // Actualizacion, recupera de la base de datos
      $row = RecuperaValor("SELECT nb_modulo, tr_modulo, ds_modulo, fg_fijo FROM c_modulo WHERE fl_modulo=$clave");
      $nb_modulo = str_texto($row[0]);
      $tr_modulo = str_texto($row[1]);
      $ds_modulo = str_texto($row[2]);
      $fg_fijo = $row[3];
    }
    else { // Alta, inicializa campos
      $nb_modulo = "";
      $tr_modulo = "";
      $ds_modulo = "";
      $fg_fijo = "0";
    }
    $nb_modulo_err = "";
  }
  else { // Con error, recibe parametros (viene de la pagina de actualizacion)
    $nb_modulo = RecibeParametroHTML('nb_modulo');
    $nb_modulo_err = RecibeParametroNumerico('nb_modulo_err');
    $tr_modulo = RecibeParametroHTML('tr_modulo');
    $ds_modulo = RecibeParametroHTML('ds_modulo');
    $fg_fijo = RecibeParametroNumerico('fg_fijo');
  }
  
  # Presenta forma de captura
  PresentaHeader( );
  PresentaEncabezado(FUNC_MENUS);
  
  # Forma para submenus
  require 'menus_frm.inc.php';
  
  # Forma para captura de datos
  Forma_Inicia($clave);
  if($fg_error)
    Forma_PresentaError( );
  Forma_CampoTexto(ETQ_NOMBRE, True, 'nb_modulo', $nb_modulo, 50, 50, $nb_modulo_err);
  Forma_CampoTexto(ETQ_TRADUCCION, False, 'tr_modulo', $tr_modulo, 50, 50);
  Forma_Espacio( );
  Forma_CampoTexto(ETQ_DESCRIPCION, False, 'ds_modulo', $ds_modulo, 100, 70);
  if($fg_fijo == 1) 
    $texto = ETQ_SI;
  else
    $texto = ETQ_NO;
  Forma_CampoInfo(ObtenEtiqueta(161), $texto);
  Forma_Espacio( );
  
  # Submenus
  Forma_Doble_Ini( );
  if(empty($clave))
    $clave = "0";
  $etq_traduccion = ETQ_TRADUCCION;
  if(!FG_TRADUCCION)
    $etq_traduccion .= "|hidden";
  $Query  = "SELECT a.fl_modulo, a.nb_modulo '".ObtenEtiqueta(164)."', ";
  $Query .= "a.tr_modulo '$etq_traduccion', a.ds_modulo '".ETQ_DESCRIPCION."', a.no_orden '".ETQ_ORDEN."|right', ";
  $Query .= "CASE a.fg_menu WHEN '1' THEN '".ETQ_SI."' ELSE '".ETQ_NO."' END '".ObtenEtiqueta(165)."|center', ";
  $Query .= "CASE a.fg_fijo WHEN '1' THEN '".ETQ_SI."' ELSE '".ETQ_NO."' END '".ObtenEtiqueta(161)."|center', 'E|hidden' 'parent|hidden' ";
  $Query .= "FROM c_modulo a, c_modulo b ";
	$Query .= "WHERE a.fl_modulo_padre=b.fl_modulo ";
  $Query .= "AND a.fl_modulo_padre=$clave ";
  $Query .= "ORDER BY a.no_orden";
  MuestraTabla($Query, TB_LE_IUD, 'submenus');
  Forma_Doble_Fin( );
  
  # Verifica que el usuario tenga permiso para guardar los cambios (solo en modo modificacion)
  if($permiso == PERMISO_DETALLE)
    $fg_guardar = ValidaPermiso(FUNC_MENUS, PERMISO_MODIFICACION);
  else
    $fg_guardar = True;
  Forma_Termina($fg_guardar);
  
  # Pie de Pagina
  PresentaFooter( );
  
?>