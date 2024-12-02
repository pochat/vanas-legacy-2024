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
  if(!ValidaPermiso(FUNC_REFERRAL_STATUS, $permiso)) {
   MuestraPaginaError(ERR_SIN_PERMISO);
   exit;
  }
  
 
 
  
 
  
  # Inserta o actualiza el registro
  if(empty($clave)) {


  
        
  }else{
  
      
   
  
  
  }
 
  
  

  # Redirige al listado
  header("Location: ".ObtenProgramaBase( ));
?>