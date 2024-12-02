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
  if(!ValidaPermiso(FUNC_STUDENTS_FAME, $permiso)) {
   MuestraPaginaError(ERR_SIN_PERMISO);
   exit;
  }
  
  # Recibe parametros
  $nb_programa=RecibeParametroHTML('nb_programa');
  
  $fname = RecibeParametroHTML('fname');
  $lname = RecibeParametroHTML('lname');  
  $ds_numero_casa =RecibeParametroHTML('ds_numero_casa');
  $fe_nacimiento=RecibeParametroFecha('fe_nacimiento');
  $fg_genero=RecibeParametroHTML('fg_genero');
  $ds_calle =RecibeParametroHTML('ds_calle');
  $ds_ciudad =RecibeParametroHTML('ds_ciudad');
  $ds_estado =RecibeParametroHTML('ds_estado');
  $ds_zip =RecibeParametroHTML('ds_zip');
  $fl_pais=RecibeParametroNumerico('fl_pais');
  $ds_email =RecibeParametroHTML('ds_email');
  $fl_usuario_sp=RecibeParametroNumerico('fl_usuario_sp');
  $ds_numero_telefono=RecibeParametroHTML('ds_numero_telefono');
  $fl_grado = RecibeParametroNumerico('fl_grado');
  $ds_alias = RecibeParametroHTML('ds_alias');
  $ds_alias_bd = RecibeParametroHTML('ds_alias_bd');
  $fe_nacimiento=ValidaFecha($fe_nacimiento);
  $fl_maestro=RecibeParametroNumerico('fl_maestro');
  $fl_programa_sp=RecibeParametroNumerico('fl_programa_sp');
  
  $Query="SELECT fl_usuario_sp FROM k_usu_direccion_sp WHERE fl_usuario_sp=$fl_usuario_sp  ";
  $row=RecuperaValor($Query);
  $existe=$row[0];
  
  # Inserta o actualiza el registro
  if(!empty($clave)) {
   
  
      if(!empty($existe)){
      
      $Query="UPDATE k_usu_direccion_sp SET ds_street='$ds_calle', ds_state='$ds_estado',ds_city='$ds_ciudad',ds_number='$ds_numero_casa',ds_zip='$ds_zip',ds_phone_number='$ds_numero_telefono',fl_pais=$fl_pais ";
      $Query.="WHERE fl_usuario_sp=$fl_usuario_sp ";
      EjecutaQuery($Query);
      
	  EjecutaQuery("UPDATE k_usuario_programa SET fl_maestro=$fl_maestro WHERE fl_usuario_sp=$fl_usuario_sp AND fl_programa_sp=$fl_programa_sp ");
	  
	  
      }else{
      $Query="INSERT INTO k_usu_direccion_sp (fl_usuario_sp,fl_pais,ds_state,ds_city,ds_number,ds_street,ds_zip,ds_phone_number )";
      $Query.="VALUES($clave,$fl_pais,'$ds_estado','$ds_ciudad','$ds_numero_casa','$ds_calle','$ds_zip','$ds_numero_telefono' ) ";
      EjecutaQuery($Query);
      }
      #actualizamos el nombre del estudiante y correo.
      $Query="UPDATE c_usuario SET ds_nombres='$fname',ds_apaterno='$lname',fg_genero='$fg_genero',fe_nacimiento='$fe_nacimiento'  ";
      # Buscamos si hay un alias
      $rowu = RecuperaValor("SELECT 1 FROM c_usuario WHERE fl_usuario!=$fl_usuario_sp AND ds_alias='".$ds_alias."'");
      $alias_existe = $rowu[0];
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
      
      # Bucsca si hay registro del alumno
      if(ExisteEnTabla('c_alumno_sp', 'fl_alumno_sp', $fl_usuario_sp)){
        $QueryE = "UPDATE c_alumno_sp SET fl_grado='".$fl_grado."' WHERE fl_alumno_sp=$fl_usuario_sp";
      }
      else{
        $QueryE = "INSERT INTO c_alumno_sp (fl_grado) VALUES ($fl_grado)";
      }
      
      EjecutaQuery($QueryE);
      
      
      
  }
 
  
  

  # Redirige al listado
 // header("Location: pricing_frm.php");
  # Redirige al listado
  header("Location: ".ObtenProgramaBase( ));
?>