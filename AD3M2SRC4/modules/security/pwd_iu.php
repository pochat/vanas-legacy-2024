<?php
	
  # Libreria de funciones
	require '../../lib/general.inc.php';
  
	# Verifica que exista una sesion valida en el cookie y la resetea
	$fl_usuario_actual = ValidaSesion( );
  
  # Recibe la clave
  $clave = RecibeParametroNumerico('clave');
	
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
  
	# Recibe parametros
  $fg_error = 0;
	$ds_password_act = RecibeParametroHTML('ds_password_act');
  $ds_password = RecibeParametroHTML('ds_password');
  $ds_password_conf = RecibeParametroHTML('ds_password_conf');
  
  # Valida campos obligatorios
  if(!$fg_otro AND empty($ds_password_act))
    $ds_password_act_err = ERR_REQUERIDO;
  if(empty($ds_password))
    $ds_password_err = ERR_REQUERIDO;
  if(empty($ds_password_conf))
    $ds_password_conf_err = ERR_REQUERIDO;
  
	# Valida confirmacion de la contrasenia
  if((!empty($ds_password) OR !empty($ds_password_conf)) AND $ds_password <> $ds_password_conf)
    $ds_password_conf_err = 101; // La contrase&ntilde; y su confirmaci&oacutE;n no coinciden.
  
	# Valida la contrasena actual
  if(!$fg_otro AND !empty($ds_password_act)) {
    $ds_password_act = sha256($ds_password_act);
    $row = RecuperaValor("SELECT count(1) FROM c_usuario WHERE fl_usuario=$clave AND ds_password='$ds_password_act'");
    if($row[0] != 1)
		  $ds_password_act_err = 103; // La contrase&ntilde;a es incorrecta.
	}
	
	# Regresa a la forma con error
  $fg_error = $ds_password_act_err || $ds_password_err || $ds_password_conf_err;
  if($fg_error) {
    echo "<html><body><form name='datos' method='post' action='".ObtenProgramaNombre(PGM_FORM)."'>\n";
    Forma_CampoOculto('clave' , $clave);
    Forma_CampoOculto('fg_error' , $fg_error);
    Forma_CampoOculto('ds_password_act_err' , $ds_password_act_err);
    Forma_CampoOculto('ds_password_err' , $ds_password_err);
    Forma_CampoOculto('ds_password_conf_err' , $ds_password_conf_err);
    echo "\n</form>
<script>
  document.datos.submit();
</script></body></html>";
    exit;
  }
  
  # Actualiza el password del usuario
  $ds_password = sha256($ds_password);
  $Query  = "UPDATE c_usuario SET ds_password='$ds_password' ";
  $Query .= "WHERE fl_usuario=$clave";
  EjecutaQuery($Query);
  
  # Regresa a la pagina solicitada
  if($fg_otro)
    header("Location: users.php");
  else
    header("Location: ".PAGINA_INICIO);
  
?>