<?php
  
  # Libreria de funciones
  require '../../lib/general.inc.php';
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  ValidaSesion( );
  
  # Recibe la clave
  $clave = RecibeParametroNumerico('clave');
  
  # Determina si es alta o modificacion
  if(!empty($clave))
    $permiso = PERMISO_MODIFICACION;
  else
    $permiso = PERMISO_ALTA;
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermiso(FUNC_CRITERIOS, $permiso)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Recibe parametros
  $fg_error = 0;
	$ds_evaluacion = RecibeParametroHTML('ds_evaluacion');
  $no_orden = RecibeParametroNumerico('no_orden');
  $fg_promedio = RecibeParametroBinario('fg_promedio');
  
  # Valida campos obligatorios
  if(empty($ds_evaluacion))
    $ds_evaluacion_err = ERR_REQUERIDO;
  
  # Valida enteros
  if($no_orden > MAX_TINYINT)
    $no_orden_err = ERR_TINYINT;
  
  # Regresa a la forma con error
  $fg_error = $ds_evaluacion_err || $no_orden_err;
  
  if($fg_error) {
    echo "<html><body><form name='datos' method='post' action='".ObtenProgramaNombre(PGM_FORM)."'>\n";
    Forma_CampoOculto('clave', $clave);
    Forma_CampoOculto('fg_error', $fg_error);
    Forma_CampoOculto('ds_evaluacion', $ds_evaluacion);
    Forma_CampoOculto('ds_evaluacion_err', $ds_evaluacion_err);
    Forma_CampoOculto('no_orden' , $no_orden);
    Forma_CampoOculto('no_orden_err' , $no_orden_err);
    Forma_CampoOculto('fg_promedio', $fg_promedio);
    echo "\n</form>
<script>
  document.datos.submit();
</script></body></html>";
    exit;
  }
  
  # Inserta o actualiza el registro
  if(empty($clave)) {
    $Query  = "INSERT INTO c_evaluacion (ds_evaluacion, no_orden, fg_promedio) ";
    $Query .= "VALUES('$ds_evaluacion', $no_orden, '$fg_promedio') ";
  }
  else {
    $Query  = "UPDATE c_evaluacion SET ds_evaluacion='$ds_evaluacion', no_orden=$no_orden, fg_promedio='$fg_promedio' ";
    $Query .= "WHERE fl_evaluacion=$clave";
  }
  EjecutaQuery($Query);
  
  # Redirige al listado
  header("Location: ".ObtenProgramaBase( ));
  
?>