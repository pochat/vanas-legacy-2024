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
  if(!ValidaPermiso(FUNC_CERTIFICADO_FAME, $permiso)) {
   MuestraPaginaError(ERR_SIN_PERMISO);
   exit;
  }
  
  # Recibe parametros
  $nb_programa=RecibeParametroHTML('nb_programa');
  
  $fname = RecibeParametroHTML('fname');
  $lname = RecibeParametroHTML('lname');  
  $ds_numero_casa =RecibeParametroHTML('ds_numero_casa');
  $ds_calle =RecibeParametroHTML('ds_calle');
  $ds_ciudad =RecibeParametroHTML('ds_ciudad');
  $ds_estado =RecibeParametroHTML('ds_estado');
  $ds_zip =RecibeParametroHTML('ds_zip');
  $fl_pais=RecibeParametroNumerico('fl_pais');
  $ds_email =RecibeParametroHTML('ds_email');
  $fl_usuario_sp=RecibeParametroNumerico('fl_usuario_sp');
  $ds_numero_telefono=RecibeParametroHTML('ds_numero_telefono');
  $fg_status = RecibeParametroHTML("fg_status");
  
  # Inserta o actualiza el registro
  if(!empty($clave)) {
   
  
      
      
      $Query="UPDATE k_usu_direccion_sp SET ds_street='$ds_calle', ds_state='$ds_estado',ds_city='$ds_ciudad',ds_number='$ds_numero_casa',ds_zip=$ds_zip,ds_phone_number='$ds_numero_telefono',fl_pais=$fl_pais ";
      $Query.="WHERE fl_usuario_sp=$fl_usuario_sp ";
      EjecutaQuery($Query);
      
      #actualizamos el nombre del estudiante y correo.
      $Query="UPDATE c_usuario SET ds_nombres='$fname',ds_apaterno='$lname'  ";
      $Query.="WHERE fl_usuario=$clave ";
      EjecutaQuery($Query);
      
      # Actualizamos el status del certificado del programa
      $Query  = "UPDATE k_usuario_programa SET fg_status='$fg_status' ";
      if($fg_status=="SD")
        $Query .=", fe_enviado=CURDATE(), fe_entregado=CURDATE() ";
      $Query .= "WHERE fl_usu_pro=$clave";
      EjecutaQuery($Query);
      
  }
 
  
  

  # Redirige al listado
 // header("Location: pricing_frm.php");
  # Redirige al listado
  header("Location: ".ObtenProgramaBase( ));
?>