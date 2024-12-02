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
  if(!ValidaPermiso(FUNC_SECCIONES, $permiso)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Inicializa variables
  if(!$fg_error) { // Sin error, viene del listado
    if(!empty($clave)) { // Actualizacion, recupera de la base de datos
      $Query  = "SELECT a.fl_modulo, a.nb_funcion, a.tr_funcion, a.ds_funcion, a.fg_menu, a.no_orden, a.cl_tipo_contenido, ";
      $Query .= "a.fg_tipo_seguridad, a.fg_multiple, a.fg_tipo_orden, a.fg_fijo, a.fl_flujo, a.nb_flash_default, a.tr_flash_default, ";
      $Query .= EscogeIdioma('b.nb_modulo','b.tr_modulo').", b.fl_modulo_padre ";
      $Query .= "FROM c_funcion a LEFT JOIN c_modulo b ON a.fl_modulo=b.fl_modulo ";
      $Query .= "WHERE a.fl_funcion=$clave";
      $row = RecuperaValor($Query);
      $fl_modulo = $row[0];
      $nb_funcion = str_texto($row[1]);
      $tr_funcion = str_texto($row[2]);
      $ds_funcion = str_texto($row[3]);
      $fg_menu = $row[4];
      $no_orden = $row[5];
      $cl_tipo_contenido = $row[6];
      $fg_tipo_seguridad = $row[7];
      $fg_multiple = $row[8];
      $fg_tipo_orden = $row[9];
      $fg_fijo = $row[10];
      $fl_flujo = $row[11];
      $nb_flash_default = str_texto($row[12]);
      $tr_flash_default = str_texto($row[13]);
      $nb_submenu = str_texto($row[14]);
      $fl_modulo_padre = $row[15];
      $fl_menu = $fl_modulo_padre;
      while(!empty($fl_modulo_padre)) {
        $fl_menu = $fl_modulo_padre;
        $row = RecuperaValor("SELECT fl_modulo_padre FROM c_modulo WHERE fl_modulo=$fl_modulo_padre");
        $fl_modulo_padre = $row[0];
      }
      $row = RecuperaValor("SELECT ".EscogeIdioma('nb_modulo','tr_modulo')." FROM c_modulo WHERE fl_modulo=$fl_menu");
      $nb_menu = $row[0];
    }
    else { // Alta, inicializa campos
      $fl_modulo = "";
      $nb_funcion = "";
      $tr_funcion = "";
      $ds_funcion = "";
      $fg_menu = "1";
      $no_orden = "0";
      $cl_tipo_contenido = "";
      $fg_tipo_seguridad = "X";
      $fg_multiple = "0";
      $fg_tipo_orden = "N";
      $fg_fijo = "0";
      $fl_flujo = "";
      $nb_flash_default = "";
      $tr_flash_default = "";
      $nb_submenu = "";
      $fl_menu = "";
      $nb_menu = "";
    }
    $nb_funcion_err = "";
    $no_orden_err = "";
    $nb_submenu_err = "";
    $nb_menu_err = "";
  }
  else { // Con error, recibe parametros (viene de la pagina de actualizacion)
    $fl_modulo = RecibeParametroNumerico('fl_modulo');
    $nb_funcion = RecibeParametroHTML('nb_funcion');
    $nb_funcion_err = RecibeParametroNumerico('nb_funcion_err');
    $tr_funcion = RecibeParametroHTML('tr_funcion');
    $ds_funcion = RecibeParametroHTML('ds_funcion');
    $fg_menu = RecibeParametroNumerico('fg_menu');
    $no_orden = RecibeParametroNumerico('no_orden');
    $no_orden_err = RecibeParametroNumerico('no_orden_err');
    $cl_tipo_contenido = RecibeParametroNumerico('cl_tipo_contenido');
    $fg_tipo_seguridad = RecibeParametroHTML('fg_tipo_seguridad');
    $fg_multiple = RecibeParametroNumerico('fg_multiple');
    $fg_tipo_orden = RecibeParametroHTML('fg_tipo_orden');
    $fg_fijo = RecibeParametroNumerico('fg_fijo');
    $fl_flujo = RecibeParametroNumerico('fl_flujo');
    $nb_flash_default = RecibeParametroHTML('nb_flash_default');
    $tr_flash_default = RecibeParametroHTML('tr_flash_default');
    $nb_submenu = RecibeParametroHTML('nb_submenu');
    $nb_submenu_err = RecibeParametroNumerico('nb_submenu_err');
    $fl_menu = RecibeParametroNumerico('fl_menu');
    $nb_menu = RecibeParametroHTML('nb_menu');
    $nb_menu_err = RecibeParametroNumerico('nb_menu_err');
  }
  
  # Presenta forma de captura
  PresentaHeader( );
  PresentaEncabezado(FUNC_SECCIONES);
  
  # Ventana para preview de archivos de flash
  require 'preview.inc.php';
  
  # Inicia forma para captura de datos
  Forma_Inicia($clave, True);
  if($fg_error)
    Forma_PresentaError( );
  Forma_CampoTexto(ETQ_NOMBRE, True, 'nb_funcion', $nb_funcion, 50, 50, $nb_funcion_err);
  Forma_CampoTexto(ETQ_TRADUCCION, False, 'tr_funcion', $tr_funcion, 50, 50);
  Forma_CampoTexto(ETQ_DESCRIPCION, False, 'ds_funcion', $ds_funcion, 100, 70);
  if($fg_fijo == 1) 
    $texto = ETQ_SI;
  else
    $texto = ETQ_NO;
  Forma_CampoInfo(ObtenEtiqueta(161), $texto);
  Forma_CampoOculto('fg_fijo', $fg_fijo);
  Forma_Espacio( );
  
  # Opciones de Menu
  Forma_CampoLOV(ObtenEtiqueta(160), True, 'fl_menu', $fl_menu, 'nb_menu', $nb_menu, 35, 
      LOV_MENUS, LOV_TIPO_RADIO, LOV_MEDIANO, '', $nb_menu_err);
  Forma_CampoLOV(ObtenEtiqueta(164), True, 'fl_modulo', $fl_modulo, 'nb_submenu', $nb_submenu, 35, 
      LOV_SUBMENUS, LOV_TIPO_RADIO, LOV_MEDIANO, 'fl_menu', $nb_submenu_err);
  Forma_CampoCheckbox(ObtenEtiqueta(165), 'fg_menu', $fg_menu);
  Forma_CampoTexto(ETQ_ORDEN, False, 'no_orden', $no_orden, 3, 5, $no_orden_err);
  Forma_Espacio( );
  
  # Contenido
  $concat = array('nb_tipo_contenido', "' - '", EscogeIdioma('ds_tipo_contenido', 'tr_tipo_contenido'));
  $Query  = "SELECT ".ConcatenaBD($concat).", cl_tipo_contenido ";
  $Query .= "FROM c_tipo_contenido WHERE cl_tipo_contenido <> ".TC_PROGRAMA." ORDER BY cl_tipo_contenido";
  Forma_CampoSelectBD(ObtenEtiqueta(232), False, 'cl_tipo_contenido', $Query, $cl_tipo_contenido);
  Forma_CampoCheckbox(ObtenEtiqueta(179), 'fg_multiple', $fg_multiple);
  $opc = array(ObtenEtiqueta(181), ObtenEtiqueta(178), ObtenEtiqueta(182), ObtenEtiqueta(183));
  $val = array('N', 'T', 'A', 'D'); // N=Numero de orden, T=Titulo alfabetico, A=Fecha ascendente, D=Fecha descendente
  Forma_CampoSelect(ObtenEtiqueta(180), False, 'fg_tipo_orden', $opc, $val, $fg_tipo_orden);
  Forma_Espacio( );
  if(!empty($nb_flash_default)) {
    Forma_CampoPreview(ObtenEtiqueta(184), 'nb_flash_default', $nb_flash_default, SP_IMAGES_W);
    Forma_CampoArchivo(ObtenEtiqueta(216), False, 'archivo_f', 70);
  }
  else
    Forma_CampoArchivo(ObtenEtiqueta(184), False, 'archivo_f', 70);
  if(!empty($tr_flash_default)) {
    Forma_CampoPreview(ObtenEtiqueta(245), 'tr_flash_default', $tr_flash_default, SP_IMAGES_W);
    Forma_CampoArchivo(ObtenEtiqueta(216), False, 'tr_archivo_ft', 70);
  }
  else
    Forma_CampoArchivo(ObtenEtiqueta(245), False, 'tr_archivo_ft', 70);
  Forma_Espacio( );
  
  # Flujo de autorizacion y seguridad
  $concat = array('nb_flujo', "' - '", EscogeIdioma('ds_flujo', 'tr_flujo'));
  $Query  = "SELECT ".ConcatenaBD($concat).", fl_flujo ";
  $Query .= "FROM c_flujo ORDER BY fg_default DESC, nb_flujo";
  Forma_CampoSelectBD(ObtenEtiqueta(140), False, 'fl_flujo', $Query, $fl_flujo);
  $opc = array(ObtenEtiqueta(177), ObtenEtiqueta(176));
  $val = array('X', 'R'); // X=Gratis, R=Restringido
  Forma_CampoSelect(ObtenEtiqueta(175), False, 'fg_tipo_seguridad', $opc, $val, $fg_tipo_seguridad);
  
  # Verifica que el usuario tenga permiso para guardar los cambios (solo en modo modificacion)
  if($permiso == PERMISO_DETALLE)
    $fg_guardar = ValidaPermiso(FUNC_SECCIONES, PERMISO_MODIFICACION);
  else
    $fg_guardar = True;
  Forma_Termina($fg_guardar);
  
  # Pie de Pagina
  PresentaFooter( );
  
?>