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
  if(!ValidaPermiso(FUNC_ETIQUETAS, $permiso)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Inicializa variables
  if(!$fg_error) { // Sin error, viene del listado
    if(!empty($clave)) { // Actualizacion, recupera de la base de datos
        $row = RecuperaValor("SELECT id,host_email_zoom,client_id_zoom,client_secret_zoom,host_id,fg_activo FROM zoom WHERE id=$clave");
        $id = $row["id"];
        $host_email_zoom = str_texto($row["host_email_zoom"]);
        $client_id_zoom = str_texto($row["client_id_zoom"]);
        $client_secret_zoom = str_texto($row["client_secret_zoom"]);
        $host_id=$row['host_id'];
        $fg_activo=$row['fg_activo'];
    }
    else { // Alta, inicializa campos
        $id = "";
        $host_email_zoom = "";
        $client_id_zoom = "";
    }
    $id = "";
    $host_email_zoom_err = "";
    
  }
  else { // Con error, recibe parametros (viene de la pagina de actualizacion)
      $host_email_zoom = RecibeParametroNumerico("host_email_zoom");
      $host_email_zoom_err = RecibeParametroNumerico("host_email_zoom_err");
      $client_id_zoom = RecibeParametroHTML("client_id_zoom");
      $client_id_zoom_err = RecibeParametroNumerico("client_id_zoom_err");
      $host_id = RecibeParametroHTML("host_id");
      $host_id_err = RecibeParametroHTML("host_id_err");
      $fg_activo = RecibeParametroHTML("fg_activo");
    
  }
  
  # Presenta forma de captura
  PresentaHeader( );
  PresentaEncabezado(227);
  Forma_Inicia($clave);
  if($fg_error)
    Forma_PresentaError( );
  
  # Si se esta editando
  if(!empty($clave)) {
    Forma_CampoInfo(ETQ_CLAVE, $clave);
  }
  else
    Forma_CampoTexto(ETQ_CLAVE, True, 'id', $id, 5, 10, $id_err);
  
  Forma_Espacio( );
  Forma_CampoTexto('Email Host', True, 'host_email_zoom', $host_email_zoom, 50, 50, $$host_email_zoom_err);
  Forma_CampoTexto("Host ID", False, 'host_id', $host_id, 1000, 50);
  Forma_CampoTexto('Client ID', True, 'client_id_zoom', $client_id_zoom, 1000, 50, $client_id_zoom_err);
  Forma_CampoTexto("Secret ID", False, 'client_secret_zoom', $client_secret_zoom, 1000, 50);
  Forma_CampoCheckbox('Active', 'fg_activo', $fg_activo);
  
  // Forma_CampoTexto(ETQ_TRADUCCION, False, 'tr_etiqueta', $tr_etiqueta, 1000, 50);
  
  # Verifica que el usuario tenga permiso para guardar los cambios (solo en modo modificacion)
  if($permiso == PERMISO_DETALLE)
    $fg_guardar = ValidaPermiso(FUNC_ETIQUETAS, PERMISO_MODIFICACION);
  else
    $fg_guardar = True;
  Forma_Termina($fg_guardar);
  
  # Pie de Pagina
  PresentaFooter();
  
?>