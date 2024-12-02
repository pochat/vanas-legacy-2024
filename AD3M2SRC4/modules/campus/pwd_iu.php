<?php
	
  # Libreria de funciones
	require '../../lib/general.inc.php';
  
	# Verifica que exista una sesion valida en el cookie y la resetea
	$fl_usuario_actual = ValidaSesion( );

  # Recibe la clave
  $clave = RecibeParametroNumerico('clave');
  $funcion_origen=RecibeParametroNumerico('funcion_origen');
  
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
    if(!empty($ds_password_act_err)){
      echo "
      <script>
        $('#div_ds_password_act').addClass('row form-group has-error');
        $('#ds_password_act').after('&nbsp;');
        $('#ds_password_act').after('".ObtenMensaje($ds_password_act_err)."');
      </script>";
    }
    if(!empty($ds_password_err)){
      echo "
      <script>
        $('#div_ds_password').addClass('row form-group has-error');
        $('#ds_password').after('&nbsp;');
        $('#ds_password').after('".ObtenMensaje($ds_password_err)."');
      </script>";
    }
    if(!empty($ds_password_conf_err)){
      echo "
      <script>
        $('#div_ds_password_conf').addClass('row form-group has-error');
        $('#ds_password_conf').after('&nbsp;');
        $('#ds_password_conf').after('".ObtenMensaje($ds_password_conf_err)."');
      </script>";
    }
    Forma_CampoOculto('fg_error',$fg_error);
    exit;
  }
  
  # Actualiza el password del usuario
  $ds_password = sha256($ds_password);
  $Query  = "UPDATE c_usuario SET ds_password='$ds_password' ";
  $Query .= "WHERE fl_usuario=$clave";
  EjecutaQuery($Query);
 
?>


<script>
$('#div_ds_password_act').addClass('row form-group');
$('#ds_password_act').after('&nbsp;');
$('#div_ds_password').addClass('row form-group');
$('#ds_password').after('&nbsp;');
$('#div_ds_password_conf').addClass('row form-group');
$('#ds_password_conf').after('&nbsp;');
</script>


<div class="alert alert-success fade in">
  <button class="close" data-dismiss="alert">
    ×
  </button>
  <i class="fa-fw fa fa-check"></i>
  <strong><?php echo ObtenEtiqueta(126); ?></strong>
</div>
<?php
 

Forma_CampoOculto('fg_error', '0');
  # Regresa a la pagina solicitada
if($fg_otro){
    if($funcion_origen==FUNC_MAESTROS){

    }else{
		
      //  header("Location: students.php");
    }
}else{
    header("Location: ".PAGINA_INICIO);
}
?>