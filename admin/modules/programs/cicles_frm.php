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
  if(!ValidaPermiso(FUNC_CICLOS, $permiso)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Inicializa variables
  if(!$fg_error) { // Sin error, viene del listado
    if(!empty($clave)) { // Actualizacion, recupera de la base de datos
      $Query  = "SELECT nb_periodo, ";
      $Query .= ConsultaFechaBD('fe_inicio', FMT_CAPTURA)." fe_inicio, ".ConsultaFechaBD('fe_pago', FMT_CAPTURA)." fe_pago, fg_activo ";
      $Query .= "FROM c_periodo ";
      $Query .= "WHERE fl_periodo=$clave";
      $row = RecuperaValor($Query);
      $nb_periodo = str_texto($row[0]);
      $fe_inicio = $row[1];
      $fe_pago = $row[2];
      $fg_activo = $row[3];
    }
    else { // Alta, inicializa campos
      $nb_periodo = "";
      $fe_inicio = "";
      $fe_pago = "";
      $fg_activo = "1";
    }
    $nb_periodo_err = "";
    $fe_inicio_err = "";
    $fe_pago_err = "";
  }
  else { // Con error, recibe parametros (viene de la pagina de actualizacion)
    $nb_periodo = RecibeParametroHTML('nb_periodo');
    $nb_periodo_err = RecibeParametroNumerico('nb_periodo_err');
    $fe_inicio = RecibeParametroFecha('fe_inicio');
    $fe_inicio_err = RecibeParametroNumerico('fe_inicio_err');
    $fe_pago = RecibeParametroFecha('fe_pago');
    $fe_pago_err = RecibeParametroNumerico('fe_pago_err');
    $fg_activo = RecibeParametroNumerico('fg_activo');
  }
  
  # Presenta forma de captura
  PresentaHeader( );
  PresentaEncabezado(FUNC_CICLOS);
  
  # Inicia forma de captura
  Forma_Inicia($clave);
  if($fg_error)
    Forma_PresentaError( );
  
  # Campos de captura
  Forma_CampoTexto(ObtenEtiqueta(370), True, 'nb_periodo', $nb_periodo, 50, 30, $nb_periodo_err);
  Forma_Espacio( );
  
  Forma_CampoTexto(ObtenEtiqueta(371).' '.ETQ_FMT_FECHA, True, 'fe_inicio', $fe_inicio, 10, 10, $fe_inicio_err);
  Forma_Calendario('fe_inicio');
  Forma_CampoTexto(ObtenEtiqueta(374).' '.ETQ_FMT_FECHA, True, 'fe_pago', $fe_pago, 10, 10, $fe_pago_err);
  Forma_Calendario('fe_pago');
  Forma_Espacio( );
  
  Forma_CampoCheckbox(ObtenEtiqueta(372), 'fg_activo', $fg_activo);
  Forma_Espacio( );
  
  # Programas
  $Query  = "SELECT a.fl_programa, nb_programa '".ObtenEtiqueta(360)."', ds_duracion '".ObtenEtiqueta(361)."', ";
  $Query .= "ds_tipo '".ObtenEtiqueta(362)."', no_grado '".ObtenEtiqueta(375)."|right' ";
  $Query .= "FROM c_programa a, k_term b ";
  $Query .= "WHERE a.fl_programa=b.fl_programa ";
  $Query .= "AND b.fl_periodo=$clave ";
  $Query .= "ORDER BY no_orden, no_grado";
  Forma_MuestraTabla($Query, TB_LN_NNN, 'programas', '', '80%');
  
  # Verifica que el usuario tenga permiso para guardar los cambios (solo en modo modificacion)
  if($permiso == PERMISO_DETALLE)
    $fg_guardar = ValidaPermiso(FUNC_CICLOS, PERMISO_MODIFICACION);
  else
    $fg_guardar = True;
  Forma_Termina($fg_guardar);
  
  # Pie de Pagina
  PresentaFooter( );
  
?>