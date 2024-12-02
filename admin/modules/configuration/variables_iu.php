<?php
  
  # Libreria de funciones
	require '../../lib/general.inc.php';
  
	# Verifica que exista una sesion valida en el cookie y la resetea
	ValidaSesion( );
  
	# Recibe la clave
  $clave = RecibeParametroNumerico('clave');
	
  # Verifica si se esta insertando
  if(empty($clave)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
	# Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermiso(FUNC_VARIABLES, PERMISO_MODIFICACION)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Recibe parametros
  $fg_error = 0;
	$ds_configuracion = RecibeParametroHTML('ds_configuracion');
	$ds_valor = RecibeParametroHTML('ds_valor');
	
	# Valida campos obligatorios
  if($ds_valor == "")
    $ds_valor_err = ERR_REQUERIDO;
  
  # Regresa a la forma con error
  $fg_error = $ds_valor_err;
  if($fg_error) {
    echo "<html><body><form name='datos' method='post' action='".ObtenProgramaNombre(PGM_FORM)."'>\n";
    Forma_CampoOculto('clave' , $clave);
    Forma_CampoOculto('fg_error' , $fg_error);
    Forma_CampoOculto('ds_configuracion' , $ds_configuracion);
    Forma_CampoOculto('ds_valor' , $ds_valor);
    Forma_CampoOculto('ds_valor_err' , $ds_valor_err);
    echo "\n</form>
<script>
  document.datos.submit();
</script></body></html>";
    exit;
  }
  
  # En esta funcion solo se puede actualizar
	$Query  = "UPDATE c_configuracion SET ";
	$Query .= "ds_valor='$ds_valor' ";
	$Query .= "WHERE cl_configuracion = $clave";
  EjecutaQuery($Query);
  
	# Redirige al listado
  header("Location: ".ObtenProgramaBase( ));
  
?>