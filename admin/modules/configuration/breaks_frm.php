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
  if(!ValidaPermiso(FUNC_BREAKS, $permiso)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Inicializa variables
  if(!$fg_error) { // Sin error, viene del listado
    if(!empty($clave)) { // Actualizacion, recupera de la base de datos
      $Query  = "SELECT ds_break, ".ConsultaFechaBD('fe_ini', FMT_CAPTURA)." fe_ini, ";
      $Query .= "".ConsultaFechaBD('fe_fin', FMT_CAPTURA)." fe_fin, no_days ";
      $Query .= "FROM c_break ";
      $Query .= "WHERE fl_break=$clave";
      $row = RecuperaValor($Query);
      
      $ds_break = str_texto($row[0]);
      $fe_ini = $row[1];
      $fe_fin = $row[2];
      $no_days = $row[3];
    }
    else { // Alta, inicializa campos
      $ds_break = "";
      $fe_ini = "";
      $fe_fin = "";
      $no_days = "0";
    }
    $ds_break_err = "";
    $fe_ini_err = "";
    $fe_fin_err = "";
    $no_days_err = "";
  }
  else { // Con error, recibe parametros (viene de la pagina de actualizacion)
    $ds_break = RecibeParametroHTML('ds_break');
    $ds_break_err = RecibeParametroNumerico('ds_break_err');
    $fe_ini = RecibeParametroFecha('fe_ini');
    $fe_ini_err = RecibeParametroNumerico('fe_ini_err');
    $fe_fin = RecibeParametroFecha('fe_fin');
    $fe_fin_err = RecibeParametroNumerico('fe_fin_err');
    $no_days = RecibeParametroNumerico('no_days');
    $no_days_err = RecibeParametroNumerico('no_days_err');
  }
  
  # Presenta forma de captura
  PresentaHeader( );
  PresentaEncabezado(FUNC_BREAKS);
  
  # Inicia forma de captura
  Forma_Inicia($clave);
  if($fg_error)
    Forma_PresentaError( );
  echo "
    <script type='text/javascript' src='".PATH_JS."/frmBreaks.js.php'></script>";
  
  # Campos de captura
  Forma_CampoTexto(ObtenEtiqueta(650), True, 'ds_break', $ds_break, 50, 30, $ds_break_err);
  Forma_Espacio( );
  
  Forma_CampoTexto(ObtenEtiqueta(371).' '.ETQ_FMT_FECHA, True, 'fe_ini', $fe_ini, 10, 10, $fe_ini_err, False, '', True, "OnChange='ActualizaDias( );'");
  Forma_Calendario('fe_ini');
  Forma_Espacio( );
  
  Forma_CampoTexto(ObtenEtiqueta(513).' '.ETQ_FMT_FECHA, True, 'fe_fin', $fe_fin, 10, 10, $fe_fin_err, False, '', True, "OnChange='ActualizaDias( );'");
  Forma_Calendario('fe_fin');
  Forma_Espacio( );
  
  Forma_CampoInfo(ObtenEtiqueta(700), "<div id='dias'></div>");
  echo "<script>ActualizaDias( );</script>";
  
  # Verifica que el usuario tenga permiso para guardar los cambios (solo en modo modificacion)
  if($permiso == PERMISO_DETALLE)
    $fg_guardar = ValidaPermiso(FUNC_BREAKS, PERMISO_MODIFICACION);
  else
    $fg_guardar = True;
  Forma_Termina($fg_guardar);
  
  # Pie de Pagina
  PresentaFooter( );
  
?>