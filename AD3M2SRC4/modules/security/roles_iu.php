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
  if(!ValidaPermiso(FUNC_PERFILES, $permiso)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Recibe parametros
  $fg_error = 0;
	$nb_perfil = RecibeParametroHTML('nb_perfil');
  $ds_perfil = RecibeParametroHTML('ds_perfil');
	$fg_admon = RecibeParametroBinario('fg_admon');
  $total_permisos = RecibeParametroNumerico('total_permisos');
  
  # Los permisos se reciben en arreglos concatenados con el identificador de la funcion 
  for($i = 1; $i <= $total_permisos; $i++) {
		$F[$i] = RecibeParametroNumerico('F'.$i);
		$X[$i] = RecibeParametroNumerico('X'.$i);
		$D[$i] = RecibeParametroNumerico('D'.$i);
		$C[$i] = RecibeParametroNumerico('C'.$i);
		$A[$i] = RecibeParametroNumerico('A'.$i);
		$B[$i] = RecibeParametroNumerico('B'.$i);
	}
  
  # Valida campos obligatorios
  if(empty($nb_perfil))
    $nb_perfil_err = ERR_REQUERIDO;
  if(empty($ds_perfil))
    $ds_perfil_err = ERR_REQUERIDO;
  $fg_error = $nb_perfil_err || $ds_perfil_err;
  
  # Regresa a la forma con error
  if($fg_error) {
    echo "<html><body><form name='datos' method='post' action='".ObtenProgramaNombre(PGM_FORM)."'>\n";
    Forma_CampoOculto('clave' , $clave);
    Forma_CampoOculto('fg_error' , $fg_error);
    Forma_CampoOculto('nb_perfil' , $nb_perfil);
    Forma_CampoOculto('nb_perfil_err' , $nb_perfil_err);
    Forma_CampoOculto('ds_perfil' , $ds_perfil);
    Forma_CampoOculto('ds_perfil_err' , $ds_perfil_err);
    Forma_CampoOculto('fg_admon' , $fg_admon);
    echo "\n</form>
<script>
  document.datos.submit();
</script></body></html>";
    exit;
  }
  
	# Verifica si se esta insertando
	if(empty($clave)) {
		$Query  = "INSERT INTO c_perfil (nb_perfil, ds_perfil, fg_admon) ";
		$Query .= "VALUES ('$nb_perfil', '$ds_perfil', '$fg_admon') ";
    $clave = EjecutaInsert($Query);
	}
  else {
		$Query  = "UPDATE c_perfil SET ";
		$Query .= "nb_perfil='$nb_perfil', ds_perfil='$ds_perfil', fg_admon='$fg_admon' ";
		$Query .= "WHERE fl_perfil=$clave";
		EjecutaQuery($Query);
  }
  
  # Elimna los permisos anteriores del perfil
  EjecutaQuery("DELETE FROM k_per_funcion WHERE fl_perfil=$clave");
  
  # Inserta los permisos para el perfil
  for($i = 1; $i <= $total_permisos; $i++) {
    $Query  = "INSERT INTO k_per_funcion ";
    $Query .= "(fl_perfil, fl_funcion, fg_ejecucion, fg_detalle, fg_modificacion, fg_alta, fg_baja) ";
    $Query .= "VALUES ($clave, $F[$i], '$X[$i]', '$D[$i]', '$C[$i]', '$A[$i]', '$B[$i]')";
    EjecutaQuery($Query);
  }
  
	# Redirige al listado
  header("Location: ".ObtenProgramaBase( ));
  
?>