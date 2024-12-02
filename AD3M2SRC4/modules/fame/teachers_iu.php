<?php
  
  # Libreria de funciones
  require '../../lib/general.inc.php';
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  ValidaSesion();
  
  # Recibe la clave
  $clave = RecibeParametroNumerico('clave');
  
  # Determina si es alta o modificacion
  if(!empty($clave))
    $permiso = PERMISO_MODIFICACION;
  else
    $permiso = PERMISO_ALTA;
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermiso(FUNC_TEACHERS_FAME, $permiso)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Recibe parametros  
  $ds_nombres = RecibeParametroHTML('ds_nombres');
  $ds_apaterno = RecibeParametroHTML('ds_apaterno');  
  $fg_genero=RecibeParametroHTML('fg_genero');
  $fe_nacimiento=RecibeParametroFecha('fe_nacimiento');
  $fe_nacimiento=ValidaFecha($fe_nacimiento);
  $ds_number =RecibeParametroHTML('ds_number');
  $ds_street =RecibeParametroHTML('ds_street');
  $ds_city =RecibeParametroHTML('ds_city');
  $ds_state =RecibeParametroHTML('ds_state');
  $ds_zip =RecibeParametroHTML('ds_zip');
  $fl_pais=RecibeParametroNumerico('fl_pais');
  $ds_email =RecibeParametroHTML('ds_email');
  $ds_phone_number=RecibeParametroHTML('ds_phone_number');
  $ds_alias=trim(RecibeParametroHTML('ds_alias'), ' ');
  $ds_alias_bd=RecibeParametroHTML('ds_alias_bd');
  $fl_usuario_sp = RecibeParametroNumerico('fl_usuario_sp');
  
  $Query="SELECT fl_usuario_sp FROM k_usu_direccion_sp WHERE fl_usuario_sp=$fl_usuario_sp  ";
  $row=RecuperaValor($Query);
  $existe=$row[0];
  
  # Inserta o actualiza el registro
  if(!empty($clave)) {
    if(!empty($existe)){      
      $Query  = "UPDATE k_usu_direccion_sp SET ds_street='$ds_street', ds_state='$ds_state',ds_city='$ds_city', ds_number='$ds_number', ";
      $Query .= "ds_zip='$ds_zip', ds_phone_number='$ds_phone_number', fl_pais=$fl_pais ";
      $Query .= "WHERE fl_usuario_sp=$fl_usuario_sp ";

      EjecutaQuery($Query);     
    }
    else{
      $Query="INSERT INTO k_usu_direccion_sp (fl_usuario_sp,fl_pais,ds_state,ds_city,ds_number,ds_street,ds_zip,ds_phone_number )";
      $Query.="VALUES($fl_usuario_sp, $fl_pais,'$ds_state','$ds_city','$ds_number','$ds_street','$ds_zip','$ds_phone_number' ) ";

      EjecutaQuery($Query);
    }
    
    #actualizamos el nombre del estudiante y correo.
    $Query="UPDATE c_usuario SET ds_nombres='$ds_nombres',ds_apaterno='$ds_apaterno',fg_genero='$fg_genero',fe_nacimiento='$fe_nacimiento'  ";
    # Buscamos si hay un alias
    $rowu = RecuperaValor("SELECT 1 FROM c_usuario WHERE fl_usuario!=$fl_usuario_sp AND ds_alias='".$ds_alias."'");
    $alias_existe = !empty($rowu[0])?$rowu[0]:NULL;
    $update_alias = false;
    if(empty($alias_existe)){
      $Query .= ", ds_alias='$ds_alias' ";
      $update_alias = true;
    }
    $Query.="WHERE fl_usuario=$fl_usuario_sp ";

    EjecutaQuery($Query); 
    # Enviamos notificacion si el usuario cambio su usuario
    if($ds_alias!=$ds_alias_bd && $update_alias == true){
      UserChangeAlias($fl_usuario_sp);
    }
  }
 
  # Redirige al listado
  header("Location: ".ObtenProgramaBase());
?>
