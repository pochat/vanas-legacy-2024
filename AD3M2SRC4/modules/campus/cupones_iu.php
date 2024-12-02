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
	if(!ValidaPermiso(FUNC_CUPON, $permiso)) {
		MuestraPaginaError(ERR_SIN_PERMISO);
		exit;
	}

	# Recibe parametros
	$fg_error = 0;
	$nb_cupon = RecibeParametroHTML('nb_cupon');
	$ds_code = RecibeParametroHTML('ds_code');
	$ds_descuento = RecibeParametroHTML('ds_descuento');
	$fe_start = RecibeParametroFecha('fe_start');
	$fe_end = RecibeParametroFecha('fe_end');
	$fg_activo = RecibeParametroBinario('fg_activo');
	# Programas
	$tot_programas = RecibeParametroNumerico('tot_programas');
	$selecionados = 0;
	for($i=1;$i<=$tot_programas;$i++){
		$fl_programa = RecibeParametroNumerico('ch_'.$i);
		if(!empty($fl_programa))
			$selecionados++;
	}

	/*# Valida campos obligatorios
	if(empty($nb_cupon))
		$nb_cupon_err = ERR_REQUERIDO;	
	if(empty($ds_code))
		$ds_code_err = ERR_REQUERIDO;
	if(empty($ds_descuento))
		$ds_descuento_err = ERR_REQUERIDO;
	if(empty($fe_start))
		$fe_start_err = ERR_REQUERIDO;
	if(empty($fe_end))
		$fe_end_err = ERR_REQUERIDO;
	# Validamos formato de las fechas
	if(!empty($fe_start) AND !ValidaFecha($fe_start))
		$fe_start_err = ERR_FORMATO_FECHA;
	if(!empty($fe_end) AND !ValidaFecha($fe_end))
		$fe_end = ERR_FORMATO_FECHA;
  
	# Si existe error
	$fg_error = $nb_cupon_err  || $ds_code_err ||$ds_descuento_err || $fe_start_err || $fe_end_err;  
	if($fg_error) {
		echo "<html><body><form name='datos' method='post' action='".ObtenProgramaNombre(PGM_FORM)."'>\n";
		Forma_CampoOculto('clave', $clave);
		Forma_CampoOculto('fg_error', $fg_error);    
		Forma_CampoOculto('nb_cupon', $nb_cupon);
		Forma_CampoOculto('nb_cupon_err', $nb_cupon_err);    
		Forma_CampoOculto('ds_code', $ds_code);
		Forma_CampoOculto('ds_code_err', $ds_code_err);
		Forma_CampoOculto('fg_activo', $fg_activo);
		Forma_CampoOculto('fg_activo_err', $fg_activo_err);
		Forma_CampoOculto('ds_descuento', $ds_descuento);
		Forma_CampoOculto('ds_descuento_err', $ds_descuento_err);
		Forma_CampoOculto('fe_start', $fe_start);    
		Forma_CampoOculto('fe_start_err', $fe_start_err);    
		Forma_CampoOculto('fe_end', $fe_end);
		Forma_CampoOculto('fe_end_err', $fe_end_err);    
		echo "\n</form>
		<script>
		document.datos.submit();
		</script></body></html>";
		exit;
	}*/
  
	# Prepara fechas en formato para insertar
	if(!empty($fe_start))
		$fe_start = "'".ValidaFecha($fe_start)."'";
	else
		$fe_start = "NULL";

	if(!empty($fe_end))
		$fe_end = "'".ValidaFecha($fe_end)."'";
	else
		$fe_end = "NULL";
  
	# Inserta o actualiza el registro
	if(!empty($clave)){
		# Actualizamos los datos
		$Query  = "UPDATE c_cupones SET nb_cupon='".$nb_cupon."', ds_code='".$ds_code."', ";
		$Query .= "ds_descuento='".$ds_descuento."', fe_start=".$fe_start.", fe_end=".$fe_end.", fg_activo='".$fg_activo."' ";
		$Query .= "WHERE fl_cupon=$clave";
		EjecutaQuery($Query);		
	}
	else{
		# Insertamos la nueva clase global
		$Query  = "INSERT INTO c_cupones (nb_cupon, ds_code, ds_descuento, fe_start, fe_end, fg_activo) ";
		$Query .= "VALUES('".$nb_cupon."', '".$ds_code."', '".$ds_descuento."', ".$fe_start.", ".$fe_end.", '".$fg_activo."')";
		$clave = EjecutaInsert($Query);
	}
	
	# Insertamos los programas para el curso		
	if(!empty($selecionados)){
	EjecutaQuery("DELETE FROM k_cupones_course WHERE fl_cupon=$clave");	
	for($i=1;$i<=$tot_programas;$i++){
		$fl_programa = RecibeParametroNumerico('ch_'.$i);
		EjecutaInsert("INSERT INTO k_cupones_course(fl_cupon, fl_programa) VALUES($clave, $fl_programa)");
	}
	}
	# Redirige al listado
	header("Location: ".ObtenProgramaBase( ));  
?>