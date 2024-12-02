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
	if(!ValidaPermiso(FUNC_EVENTS, $permiso)) {
		MuestraPaginaError(ERR_SIN_PERMISO);
		exit;
	}

	# Recibe parametros
	$fg_error = 0;
	$cl_clave=RecibeParametroHTML('cl_clave');
    $nb_evento = RecibeParametroHTML('nb_evento');
	$ds_descripcion = RecibeParametroHTML('ds_descripcion');
    
	
	if(empty($no_puntos))
	$no_puntos=0;
  
	# Inserta o actualiza el registro
	if(!empty($clave)){
		# Actualizamos los datos
		$Query  = "UPDATE c_evento SET cl_clave='".$cl_clave."',  nb_evento='".$nb_evento."', ds_evento='".$ds_descripcion."',fe_ultmod=CURRENT_TIMESTAMP ";
		$Query .= "WHERE cl_evento=$clave";
		EjecutaQuery($Query);		
	}
	else{
        
      
		    # Insertamos la nueva clase global
		    $Query  = "INSERT INTO c_evento (cl_clave,nb_evento, ds_evento,fe_creacion,fe_ultmod ) ";
		    $Query .= "VALUES('".$cl_clave."', '".$nb_evento."', '".$ds_descripcion."',CURRENT_TIMESTAMP,CURRENT_TIMESTAMP )";
		    $clave = EjecutaInsert($Query);
 
            
	}

	# Redirige al listado
	header("Location: ".ObtenProgramaBase( ));  
?>