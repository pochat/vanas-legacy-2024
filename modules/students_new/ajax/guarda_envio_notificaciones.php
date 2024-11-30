<?php 
  # Libreria de funciones
  require("../../common/lib/cam_general.inc.php");

  # Verifica que exista una sesion valida en el cookie y la resetea
  $fl_usuario = ValidaSesion(False);
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermisoCampus(FUNC_ALUMNOS)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }

  #Recibe Parametros
  $fg_copy=RecibeParametroHTML('fg_copy');
  $fl_alumno=RecibeParametroNumerico('fl_alumno');
  $fg_notificacion=RecibeParametroHTML('fg_notificacion');
  
  if($fg_notificacion=='r'){
  
	echo$Query="UPDATE c_alumno SET fg_copy_email_responsable='$fg_copy' WHERE fl_alumno=$fl_alumno ";
	EjecutaQuery($Query);
  
  }else{
	
	echo$Query="UPDATE c_alumno SET fg_copy_email_alternativo='$fg_copy' WHERE fl_alumno=$fl_alumno "; 
    EjecutaQuery($Query);	
	  
  }
  
  
 
  

?>
