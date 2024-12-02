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
  
   $cl_evento = RecibeParametroNumerico('cl_evento');
   $ds_descripcion = RecibeParametroHTML('ds_descripcion');
   $no_puntos=RecibeParametroNumerico('no_puntos');
   $nb_archivo = RecibeParametroHTML('nb_archivo');
 
  

  
  
  
  
  
  
  # Recibe el archivo seleccionado
  if(!empty($_FILES['archivo']['tmp_name'])) {
    $nb_archivo = $_FILES['archivo']['name'];
    $ext = strtoupper(ObtenExtensionArchivo($nb_archivo));
    switch($ext) {
        case "JPG":  $ruta = SP_IMAGES."/gamification/accion"; break;
        case "PNG":  $ruta = SP_IMAGES."/gamification/accion"; break;
        case "JPEG": $ruta = SP_IMAGES."/gamification/accion"; break;
        default:     $ruta = SP_IMAGES."/gamification/accion"; break;
    }
    move_uploaded_file($_FILES['archivo']['tmp_name'], $ruta."/".$nb_archivo);
    
    
  }


  $ds_imagen=$nb_archivo;
  #Inserta o actualiza el registro
	if(!empty($clave)){
	    # Actualizamos los datos
	    $Query  = "UPDATE k_accion SET cl_evento=".$cl_evento.", ds_imagen='".$ds_imagen."',  ds_accion='".$ds_descripcion."',no_puntos=$no_puntos  ,fe_ultmod=CURRENT_TIMESTAMP ";
	    $Query .= "WHERE fl_accion=$clave";
	    EjecutaQuery($Query);		
	}
	else{
        
      
		# Insertamos la nueva clase global
		$Query  = "INSERT INTO k_accion (cl_evento, ds_accion,no_puntos,ds_imagen, fe_creacion,fe_ultmod ) ";
		$Query .= "VALUES(".$cl_evento.", '".$ds_descripcion."',$no_puntos,'".$ds_imagen."' ,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP )";
		$fl_accion = EjecutaInsert($Query);
 
            
	}

 
 
 
 
	# Redirige al listado
  header("Location: ".ObtenProgramaBase( ));
  
?>