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
  if(!ValidaPermiso(FUNC_ESCALAS, $permiso)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Recibe parametros
  $fg_error = 0;
	$cl_calificacion = RecibeParametroHTML('cl_calificacion');
  $ds_calificacion = RecibeParametroHTML('ds_calificacion');
  $fg_aprobado = RecibeParametroBinario('fg_aprobado');
  $no_equivalencia = RecibeParametroFlotante('no_equivalencia');
  $no_min = RecibeParametroFlotante('no_min');
  $no_max = RecibeParametroFlotante('no_max');
  
  # Valida campos obligatorios
  if(empty($cl_calificacion))
    $cl_calificacion_err = ERR_REQUERIDO;
  
  # Valida flotantes
  if($no_equivalencia > 100.0)
    $no_equivalencia_err = 110; # The number must be less or equal to 100.0
  if($no_min > 100.0)
    $no_min_err = 110;
  if($no_max > 100.0)
    $no_max_err = 110;
  
	# Regresa a la forma con error
  $fg_error = $cl_calificacion_err || $no_equivalencia_err || $no_min_err || $no_max_err;
  
  if($fg_error) {
    echo "<html><body><form name='datos' method='post' action='".ObtenProgramaNombre(PGM_FORM)."'>\n";
    Forma_CampoOculto('clave', $clave);
    Forma_CampoOculto('fg_error', $fg_error);
    Forma_CampoOculto('cl_calificacion', $cl_calificacion);
    Forma_CampoOculto('cl_calificacion_err', $cl_calificacion_err);
    Forma_CampoOculto('ds_calificacion', $ds_calificacion);
    Forma_CampoOculto('fg_aprobado', $fg_aprobado);
    Forma_CampoOculto('no_equivalencia', $no_equivalencia);
    Forma_CampoOculto('no_equivalencia_err', $no_equivalencia_err);
    Forma_CampoOculto('no_min', $no_min);
    Forma_CampoOculto('no_min_err', $no_min_err);
    Forma_CampoOculto('no_max', $no_max);
    Forma_CampoOculto('no_max_err', $no_max_err);
    echo "\n</form>
<script>
  document.datos.submit();
</script></body></html>";
    exit;
  }
  
  # Inserta o actualiza el registro
  if(empty($clave)) {
    $Query  = "INSERT INTO c_calificacion (cl_calificacion, ds_calificacion, fg_aprobado, no_equivalencia, no_min, no_max) ";
    $Query .= "VALUES('$cl_calificacion', '$ds_calificacion', '$fg_aprobado', $no_equivalencia, $no_min, $no_max) ";
  }
  else {
    $Query  = "UPDATE c_calificacion SET cl_calificacion='$cl_calificacion', ds_calificacion='$ds_calificacion', fg_aprobado='$fg_aprobado', ";
    $Query .= "no_equivalencia=$no_equivalencia, no_min=$no_min, no_max=$no_max ";
    $Query .= "WHERE fl_calificacion=$clave";
  }
  EjecutaQuery($Query);
  
  # Redirige al listado
  header("Location: ".ObtenProgramaBase( ));
  
?>