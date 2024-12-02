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
  if(!ValidaPermiso(FUNC_CORREOS, $permiso)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Inicializa variables
  if(!$fg_error) { // Sin error, viene del listado
    if(!empty($clave)) { // Actualizacion, recupera de la base de datos
      $Query  = "SELECT ds_area, tr_area, ds_email, no_orden, fg_anexo, ds_etq_anexo, tr_etq_anexo ";
      $Query .= "FROM c_contacto ";
      $Query .= "WHERE fl_contacto=$clave";
      $row = RecuperaValor($Query);
      $ds_area = str_texto($row[0]);
      $tr_area = str_texto($row[1]);
      $ds_email = str_texto($row[2]);
      $no_orden = $row[3];
      $fg_anexo = $row[4];
      $ds_etq_anexo = str_texto($row[5]);
      $tr_etq_anexo = str_texto($row[6]);
    }
    else { // Alta, inicializa campos
      $ds_area = "";
      $tr_area = "";
      $ds_email = "";
      $no_orden = "0";
      $fg_anexo = "0";
      $ds_etq_anexo = "";
      $tr_etq_anexo = "";
    }
    $ds_area_err = "";
    $ds_email_err = "";
    $no_orden_err = "";
    $ds_etq_anexo_err = "";
  }
  else { // Con error, recibe parametros (viene de la pagina de actualizacion)
    $ds_area = RecibeParametroHTML('ds_area');
    $ds_area_err = RecibeParametroNumerico('ds_area_err');
    $tr_area = RecibeParametroHTML('tr_area');
    $ds_email = RecibeParametroHTML('ds_email');
    $ds_email_err = RecibeParametroNumerico('ds_email_err');
    $no_orden = RecibeParametroNumerico('no_orden');
    $no_orden_err = RecibeParametroNumerico('no_orden_err');
    $fg_anexo = RecibeParametroNumerico('fg_anexo');
    $ds_etq_anexo = RecibeParametroHTML('ds_etq_anexo');
    $ds_etq_anexo_err = RecibeParametroNumerico('ds_etq_anexo_err');
    $tr_etq_anexo = RecibeParametroHTML('tr_etq_anexo');
  }
  
  # Presenta forma de captura
  PresentaHeader( );
  PresentaEncabezado(FUNC_CORREOS);
  Forma_Inicia($clave);
  if($fg_error)
    Forma_PresentaError( );
  Forma_CampoTexto(ObtenEtiqueta(240), True, 'ds_area', $ds_area, 255, 50, $ds_area_err);
  Forma_CampoTexto(ETQ_TRADUCCION, False, 'tr_area', $tr_area, 255, 50);
  Forma_CampoTexto(ObtenEtiqueta(121), True, 'ds_email', $ds_email, 255, 70, $ds_email_err);
  Forma_CampoTexto(ETQ_ORDEN, False, 'no_orden', $no_orden, 3, 5, $no_orden_err);
  Forma_Espacio( );
  Forma_CampoCheckbox(ObtenEtiqueta(242), 'fg_anexo', $fg_anexo);
  Forma_CampoTexto(ObtenEtiqueta(241), False, 'ds_etq_anexo', $ds_etq_anexo, 255, 50, $ds_etq_anexo_err);
  Forma_CampoTexto(ETQ_TRADUCCION, False, 'tr_etq_anexo', $tr_etq_anexo, 255, 50);
  Forma_Espacio( );
  
  # Verifica que el usuario tenga permiso para guardar los cambios (solo en modo modificacion)
  if($permiso == PERMISO_DETALLE)
    $fg_guardar = ValidaPermiso(FUNC_CORREOS, PERMISO_MODIFICACION);
  else
    $fg_guardar = True;
  Forma_Termina($fg_guardar);
  
  # Pie de Pagina
  PresentaFooter( );
  
?>