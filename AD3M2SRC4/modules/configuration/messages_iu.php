<?php
  
  # Libreria de funciones
	require '../../lib/general.inc.php';
  
	# Verifica que exista una sesion valida en el cookie y la resetea
	ValidaSesion( );
  
	# Recibe la clave
  $cl_mensaje = RecibeParametroNumerico('clave');
  $cl_mensaje_nueva = RecibeParametroNumerico('cl_mensaje_nueva');
  
  # Determina si es alta o modificacion
  if(!empty($cl_mensaje))
    $permiso = PERMISO_MODIFICACION;
  else
    $permiso = PERMISO_ALTA;
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermiso(FUNC_MENSAJES, $permiso)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Recibe parametros
  $fg_error = 0;
	$ds_titulo = RecibeParametroHTML('ds_titulo');
	$tr_titulo = RecibeParametroHTML('tr_titulo');
	$ds_mensaje = RecibeParametroHTML('ds_mensaje');
  $tr_mensaje = RecibeParametroHTML('tr_mensaje');
  $fg_severidad = RecibeParametroHTML('fg_severidad');
	$fg_tipo = RecibeParametroNumerico('fg_tipo');
  
	# Valida campos obligatorios
  if(empty($cl_mensaje) AND empty($cl_mensaje_nueva))
    $cl_mensaje_err = ERR_REQUERIDO;
  if(empty($ds_mensaje))
    $ds_mensaje_err = ERR_REQUERIDO;
  
  # Valida que no exista el registro
  if(empty($cl_mensaje) AND ExisteEnTabla('c_mensaje', 'cl_mensaje', $cl_mensaje_nueva))
    $cl_mensaje_err = ERR_DUPVAL;
  
  # Valida enteros
  if(empty($cl_mensaje) AND !empty($cl_mensaje_nueva) AND !ValidaEntero($cl_mensaje_nueva))
    $cl_mensaje_err = ERR_ENTERO;
  if(empty($cl_mensaje) AND !empty($cl_mensaje_nueva) AND ($cl_mensaje_nueva > MAX_SMALLINT))
    $cl_mensaje_err = ERR_SMALLINT;
  
	# Regresa a la forma con error
  $fg_error = $cl_mensaje_err || $ds_mensaje_err;
  if($fg_error) {
    echo "<html><body><form name='datos' method='post' action='".ObtenProgramaNombre(PGM_FORM)."'>\n";
    Forma_CampoOculto('clave' , $cl_mensaje);
    Forma_CampoOculto('fg_error' , $fg_error);
    Forma_CampoOculto('cl_mensaje_nueva' , $cl_mensaje_nueva);
    Forma_CampoOculto('cl_mensaje_err' , $cl_mensaje_err);
    Forma_CampoOculto('ds_titulo' , $ds_titulo);
    Forma_CampoOculto('tr_titulo' , $tr_titulo);
    Forma_CampoOculto('ds_mensaje' , $ds_mensaje);
    Forma_CampoOculto('ds_mensaje_err' , $ds_mensaje_err);
    Forma_CampoOculto('tr_mensaje' , $tr_mensaje);
    Forma_CampoOculto('fg_severidad' , $fg_severidad);
    Forma_CampoOculto('fg_tipo' , $fg_tipo);
    echo "\n</form>
<script>
  document.datos.submit();
</script></body></html>";
    exit;
  }
  
  # Actualiza o inserta el registro
  if(!empty($cl_mensaje)) {
    $Query  = "UPDATE c_mensaje SET ";
    $Query .= "ds_titulo='$ds_titulo', tr_titulo='$tr_titulo', ds_mensaje='$ds_mensaje', tr_mensaje='$tr_mensaje', ";
    $Query .= "fg_severidad='$fg_severidad', fg_tipo='$fg_tipo' ";
    $Query .= "WHERE cl_mensaje = $cl_mensaje";
  }
  else {
    $Query  = "INSERT INTO c_mensaje (cl_mensaje, ds_titulo, tr_titulo, ds_mensaje, tr_mensaje, fg_severidad, fg_tipo) ";
    $Query .= "VALUES($cl_mensaje_nueva, '$ds_titulo', '$tr_titulo', '$ds_mensaje', '$tr_mensaje', '$fg_severidad', '$fg_tipo')";
	}
	EjecutaQuery($Query);
  
	# Redirige al listado
  header("Location: ".ObtenProgramaBase( ));
  
?>