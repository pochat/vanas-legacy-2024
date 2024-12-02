<?php
  
  # Libreria de funciones
  require '../../lib/general.inc.php';
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  $fl_usuario_actual = ValidaSesion( );
  
  # Recibe parametros
  $clave = RecibeParametroNumerico('clave');
  $fg_error = RecibeParametroNumerico('fg_error');
  
  # Si no se envio usuario, cambia el password al usuario actual
  if(empty($clave))
    $clave = $fl_usuario_actual;
  
  # Revisa si se esta cambiando el password propio o a otro usuario
  if($clave == $fl_usuario_actual) {
    $funcion = FUNC_PWD;
    $fg_otro = False;
  }
  else {
    $funcion = FUNC_PWD_OTROS;
    $fg_otro = True;
  }
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermiso($funcion, PERMISO_EJECUCION)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Inicializa variables
  if($fg_error) {
    $ds_password_act_err = RecibeParametroNumerico('ds_password_act_err');
    $ds_password_err = RecibeParametroNumerico('ds_password_err');
    $ds_password_conf_err = RecibeParametroNumerico('ds_password_conf_err');
  }
  else {
    $ds_password_act_err = "";
    $ds_password_err = "";
    $ds_password_conf_err = "";
  }
  
  # Recupera datos del usuario
  $row = RecuperaValor("SELECT ds_login FROM c_usuario WHERE fl_usuario=$clave");
  $ds_login = str_texto($row[0]);
  
  # Presenta forma de captura
  PresentaHeader( );
  PresentaEncabezado($funcion);
  Forma_Inicia($clave);
  if($fg_error)
    Forma_PresentaError( );
  Forma_CampoInfo(ETQ_USUARIO, $ds_login);
  Forma_Espacio( );
  
  # Solo pide la contrasenia actual si es el mismo usuario
  if(!$fg_otro) {
    Forma_CampoTexto(ObtenEtiqueta(123), True, 'ds_password_act', '', 16, 16, $ds_password_act_err, True);
    Forma_Espacio( );
  }
  Forma_CampoTexto(ObtenEtiqueta(125), True, 'ds_password', '', 16, 16, $ds_password_err, True);
  Forma_CampoTexto(ObtenEtiqueta(124), True, 'ds_password_conf', '', 16, 16, $ds_password_conf_err, True);
  if($fg_otro)
    $pag_cancelar = 'usuarios.php';
  else
    $pag_cancelar = PAGINA_INICIO;
  Forma_Termina(True, $pag_cancelar);
  
  # Pie de Pagina
  PresentaFooter( );
  
?>