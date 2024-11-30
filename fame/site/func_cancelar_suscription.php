<?php 
	# Libreria de funciones	
	require("../lib/self_general.php");
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  $fl_usuario = ValidaSesion(False,0, True);

  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermisoSelf(FUNC_SELF)) {  
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
 
  $fl_instituto=ObtenInstituto($fl_usuario);
  
  $nb_nombre_usuario_actual=ObtenNombreUsuario($fl_usuario);
  $fl_current_plan=RecibeParametroNumerico('fl_current_plan');
 
 
  #Cancelamos la suscription
  $Query="UPDATE k_current_plan SET fg_activo='0' WHERE fl_current_plan=$fl_current_plan ";
  EjecutaQuery($Query);
 

 

?>







  
     
      
 <script>
     document.getElementById('cerrar_modal').click();//clic automatico que se ejuta y sale modal
 </script>
                                     


