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
  
  
  $Query="SELECT no_total_licencias FROM k_current_plan WHERE fl_instituto =$fl_instituto ";
  $row=RecuperaValor($Query);
  $no_total_licencias=$row['no_total_licencias'];
  if(empty($no_total_licencias)){#si no existe plan se toma en cueta los usuarios actuales del instituto en modo Trial
  
      $no_total_licencias=ObtenNumeroUserInst($fl_instituto);
      
  }
      
 

  
 
  
  
  
  echo"
  $no_total_licencias 
  
  ";
  
  
  
  
  
  
                                     
?>

 <input type="hidden" name="no_total_licencias" id="no_total_licencias" value="<?php  echo $no_total_licencias ?>" />