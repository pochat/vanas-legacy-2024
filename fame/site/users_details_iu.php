<?php 
	# Libreria de funciones	
	require("../lib/self_general.php");
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  $fl_usuario = ValidaSesion(False,0, True);
  $fl_perfil = ObtenPerfilUsuario($fl_usuario);

  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermisoSelf(FUNC_SELF)) {  
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  $clave = RecibeParametroNumerico('clave');
  $ds_nombres = RecibeParametroHTML('ds_nombres');
  $ds_apaterno = RecibeParametroHTML('ds_apaterno');
  $fg_genero = RecibeParametroHTML('fg_genero');
  $fe_nacimiento = RecibeParametroHTML('fe_nacimiento');
  $ds_number = RecibeParametroHTML('ds_number');
  $ds_street = RecibeParametroHTML('ds_street');
  $ds_city = RecibeParametroHTML('ds_city');
  $ds_state = RecibeParametroHTML('ds_state');
  $ds_zip = RecibeParametroHTML('ds_zip');
  $ds_email = RecibeParametroHTML('ds_email');
  $ds_phone_number = RecibeParametroHTML('ds_phone_number');
  $fl_pais = RecibeParametroNumerico('fl_pais');
  $fl_grado = RecibeParametroNumerico('fl_grado');
  $fl_instituto = RecibeParametroNumerico('fl_instituto');
  $ds_fname=RecibeParametroHTML('ds_fname');
  $ds_lname=RecibeParametroHTML('ds_lname');
  $cl_parentesco=RecibeParametroHTML('cl_parentesco');
   $email_parentesco=RecibeParametroHTML('email_parentesco');
  $fg_confirmado = RecibeParametroNumerico('fg_confirmado');
  
  $ds_alias=RecibeParametroHTML('ds_alias');
  
  # Validamos el formato de la fecha
  $fe_nacimiento = ValidaFecha($fe_nacimiento);
  
  # Si recibimos la clave entonces actualizamos
  if(!empty($clave)){

      if(($fl_perfil==25)||($fl_perfil==13)){

          EjecutaQuery("UPDATE c_usuario SET fl_instituto=$fl_instituto WHERE fl_usuario= $clave ");

      }



    # Modificamos los datos
    if(!empty($fg_confirmado))
      $Query = "UPDATE c_usuario SET ds_nombres='$ds_nombres', ds_apaterno='$ds_apaterno', fg_genero='$fg_genero',ds_alias='$ds_alias', fe_nacimiento='$fe_nacimiento', ds_email='$ds_email' WHERE fl_usuario=$clave";
    else
      $Query = "UPDATE k_envio_email_reg_selfp SET ds_first_name='$ds_nombres', ds_last_name='$ds_apaterno', fg_genero='$fg_genero', fe_nacimiento='$fe_nacimiento', ds_email='$ds_email' WHERE fl_envio_correo=$clave";
    EjecutaQuery($Query);
    # Modificamos las direcciones
    # Buscamos si hay registro
    if(empty($fg_confirmado)){
      if(!ExisteEnTabla('k_usu_direccion_sp', 'fl_usuario_sp', $clave)){
        $Queryd  = "INSERT INTO k_usu_direccion_sp (fl_usuario_sp,fl_pais,ds_state,ds_city,ds_number,ds_street,ds_zip,ds_phone_number) ";
        $Queryd .= "VALUES ($clave, $fl_pais, '$ds_state', '$ds_city', '$ds_number', '$ds_street', '$ds_zip', '$ds_phone_number') ";
      }
      else{
        $Queryd  = "UPDATE k_usu_direccion_sp SET fl_pais=$fl_pais, ds_state='$ds_state', ds_city='$ds_city', ds_number='$ds_number', ds_street='$ds_street', ";
        $Queryd .= "ds_zip='$ds_zip', ds_phone_number='$ds_phone_number' ";
        $Queryd .= "WHERE fl_usuario_sp=$clave";
      }
    }
    else{
      $Queryd  = "UPDATE k_usu_direccion_sp SET fl_pais=$fl_pais, ds_state='$ds_state', ds_city='$ds_city', ds_number='$ds_number', ds_street='$ds_street', ";
      $Queryd .= "ds_zip='$ds_zip', ds_phone_number='$ds_phone_number' ";
      $Queryd .= "WHERE fl_usuario_sp=$clave";
    }
    echo $Query;
    EjecutaQuery($Queryd);
	 
    #Actualizamos la relacion del alumno
    if(ExisteEnTabla('k_responsable_alumno','fl_usuario', $clave)){
      $Query = "UPDATE k_responsable_alumno SET cl_parentesco=$cl_parentesco,ds_fname='$ds_fname',ds_lname='$ds_lname',ds_email='$email_parentesco' WHERE fl_usuario=$clave ";
    }
    else{
      $Query  = "INSERT INTO k_responsable_alumno (cl_parentesco, fl_usuario, ds_fname, ds_lname, ds_email) ";
      $Query .= "VALUES ($cl_parentesco, $clave, '$ds_fname', '$ds_lname', '$email_parentesco');";
    }
    EjecutaQuery($Query);
    
    if(!empty($fg_confirmado)){
      #actualizamos el grado del alumno.
      $Query="UPDATE c_alumno_sp SET fl_grado=$fl_grado WHERE fl_alumno_sp=$clave ";
      EjecutaQuery($Query);
    }

  }
?>