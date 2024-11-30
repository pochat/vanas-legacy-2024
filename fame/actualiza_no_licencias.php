<?php 
	# Libreria de funciones	
	require("../lib/self_general.php");
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  $fl_usuario = ValidaSesion(False,0, True);

  $fl_instituto=ObtenInstituto($fl_usuario);
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermisoSelf(FUNC_SELF)) {  
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  #Este solo se utliza para saber que se van areducir la licencias.
  $opc_renovacion=RecibeParametroNumerico(opc);
  
  $no_total_licencias_actuales=RecibeParametroNumerico('no_total_licencias_actuales');
  $no_usuario_adicional=RecibeParametroHTML('no_usuario_adicional');
  

  if(empty($no_total_licencias_actuales)){#si no existe plan se toma en cueta los usuarios actuales del instituto en modo Trial
      
      $no_total_licencias_actuales=ObtenNumeroUserInst($fl_instituto);
      
  } 
  
  
  
  if($no_usuario_adicional >=0){
  
 #Se realiza la sumatoria de las licencias actuales con lo que se va agrenagdo en el spinner. 
 $no_licencias_totales_actuales=$no_total_licencias_actuales + $no_usuario_adicional;
  }
  
  if($no_usuario_adicional < 0 ){
      
     
      
     $no_licencias_totales_actuales=$no_total_licencias_actuales + ($no_usuario_adicional);
  
  }
  
  
  
  
  
   if($opc_renovacion<>1){
  
  echo"
  $no_licencias_totales_actuales
  
  ";
   }
  
  
  /************************************aqui para abajo se utiliza para las opciones de renovacion***********************************************/
  
  
  if($opc_renovacion==1){  #Solo se utiliza para opciones de renovacion.
       
       $no_total_licencias_actuales=RecibeParametroNumerico('no_total_licencias_actuales');
       $no_usuario_adicional=RecibeParametroNumerico('no_usuario_adicional_');
	    $no_lic_disponibles=RecibeParametroNumerico('no_lic_disponibles');
       $no_licencias_totales=$no_total_licencias_actuales-$no_usuario_adicional;

		echo"$no_licencias_totales ";

  
  }
                                     
?>

