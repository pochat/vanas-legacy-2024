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
  if(!ValidaPermiso(FUNC_MAESTROS, $permiso)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Recibe parametros
  $fg_error = 0;
	$ds_login = RecibeParametroHTML('ds_login');
  $ds_password = RecibeParametroHTML('ds_password');
  $ds_password_conf = RecibeParametroHTML('ds_password_conf');
  
  $ds_nombres = RecibeParametroHTML('ds_nombres');
  $ds_apaterno = RecibeParametroHTML('ds_apaterno');
  $ds_amaterno = RecibeParametroHTML('ds_amaterno');
  $fg_genero = RecibeParametroHTML('fg_genero');
  $fe_nacimiento = RecibeParametroFecha('fe_nacimiento');
  $ds_email = RecibeParametroHTML('ds_email');
  $ds_number = RecibeParametroHTML('ds_number');
  $fl_perfil = RecibeParametroNumerico('fl_perfil');
  $nb_perfil = RecibeParametroHTML('nb_perfil');
  $fg_activo = RecibeParametroNumerico('fg_activo');
  $fe_alta = RecibeParametroFecha('fe_alta');
  $fe_ultacc = RecibeParametroFecha('fe_ultacc');
  $no_accesos = RecibeParametroNumerico('no_accesos');
  $fl_pais = RecibeParametroNumerico('fl_pais');
  $fl_zona_horaria = RecibeParametroNumerico('fl_zona_horaria');
  # Recibimos cada uno de los grupos y programas 
  $total = RecibeParametroNumerico('total');
  for($i=0;$i<$total;$i++){
    $fl_grupo[$i] = RecibeParametroNumerico('fl_grupo_'.$i);
    $fl_programa[$i] = RecibeParametroNumerico('fl_programa_'.$i);
    $mn_lecture_fee[$i] = RecibeParametroHTML('mn_lecture_fee_'.$i);
    $mn_extra_fee[$i] = RecibeParametroHTML('mn_extra_fee_'.$i);
  }
  
  # Valida campos obligatorios
  if(empty($ds_login))
    $ds_login_err = ERR_REQUERIDO;
  if(empty($clave) AND empty($ds_password))
    $ds_password_err = ERR_REQUERIDO;
  if(empty($clave) AND empty($ds_password_conf))
    $ds_password_conf_err = ERR_REQUERIDO;
  if(empty($ds_nombres))
    $ds_nombres_err = ERR_REQUERIDO;
  if(empty($ds_apaterno))
    $ds_apaterno_err = ERR_REQUERIDO;
  if(empty($ds_email))
    $ds_email_err = ERR_REQUERIDO;
  if(empty($fl_perfil))
    $fl_perfil_err = ERR_REQUERIDO;
  if(empty($fe_nacimiento))
    $fe_nacimiento_err = ERR_REQUERIDO;
    
  # Verifica que tenga algun monto en teachers fee
  for($i=0;$i<$total;$i++){
    if($mn_lecture_fee[$i]=='')
      $mn_lecture_fee_err[$i] = ERR_REQUERIDO;
    if($mn_extra_fee[$i]=='')
      $mn_extra_fee_err[$i] = ERR_REQUERIDO;
  }
  
  # Valida que no exista el registro
  if(empty($clave) AND !empty($ds_login) AND ExisteEnTabla('c_usuario', 'ds_login', $ds_login))
    $ds_login_err = ERR_DUPVAL;
  
  # Valida confirmacion de la contrasenia
  if((empty($clave)) AND ((!empty($ds_password) OR !empty($ds_password_conf)) AND $ds_password <> $ds_password_conf))
    $ds_password_conf_err = 101; // La contrase&ntilde; y su confirmaci&oacutE;n no coinciden.
  
  # Verifica que el formato de la fecha sea valido
  if(!empty($fe_nacimiento) AND !ValidaFecha($fe_nacimiento))
    $fe_nacimiento_err = ERR_FORMATO_FECHA;
  
  # Verifica que el formato del email sea valido
  if(!empty($ds_email) AND !ValidaEmail($ds_email))
    $ds_email_err = ERR_FORMATO_EMAIL;
  
  # Regresa a la forma con error
  $fg_error = $ds_login_err || $ds_password_err || $ds_password_conf_err || $ds_nombres_err || $ds_apaterno_err || $fe_nacimiento_err 
              || $ds_email_err || $fl_perfil_err;
  for($i=0;$i<$total;$i++){
    $fg_error .= $mn_lecture_fee_err[$i] || $mn_extra_fee_err[$i];    
  }
  
  
  if($fg_error) {
    echo "<html><body><form name='datos' method='post' action='".ObtenProgramaNombre(PGM_FORM)."'>\n";
    Forma_CampoOculto('clave' , $clave);
    Forma_CampoOculto('fg_error' , $fg_error);
    Forma_CampoOculto('ds_login' , $ds_login);
    Forma_CampoOculto('ds_login_err' , $ds_login_err);
    Forma_CampoOculto('ds_password_err' , $ds_password_err);
    Forma_CampoOculto('ds_password_conf_err' , $ds_password_conf_err);
    Forma_CampoOculto('ds_nombres' , $ds_nombres);
    Forma_CampoOculto('ds_nombres_err' , $ds_nombres_err);
    Forma_CampoOculto('ds_apaterno' , $ds_apaterno);
    Forma_CampoOculto('ds_apaterno_err' , $ds_apaterno_err);
    Forma_CampoOculto('ds_amaterno' , $ds_amaterno);
    Forma_CampoOculto('fg_genero' , $fg_genero);
    Forma_CampoOculto('fe_nacimiento' , $fe_nacimiento);
    Forma_CampoOculto('fe_nacimiento_err' , $fe_nacimiento_err);
    Forma_CampoOculto('ds_email' , $ds_email);    
    Forma_CampoOculto('ds_email_err' , $ds_email_err);
    Forma_CampoOculto('ds_number' , $ds_number);
    Forma_CampoOculto('fl_perfil' , $fl_perfil);
    Forma_CampoOculto('fl_perfil_err' , $fl_perfil_err);
    Forma_CampoOculto('nb_perfil' , $nb_perfil);
    Forma_CampoOculto('fg_activo' , $fg_activo);
    Forma_CampoOculto('fe_alta' , $fe_alta);
    Forma_CampoOculto('fe_ultacc' , $fe_ultacc);
    Forma_CampoOculto('no_accesos' , $no_accesos);
    Forma_CampoOculto('fl_pais' , $fl_pais);
    Forma_CampoOculto('fl_zona_horaria' , $fl_zona_horaria);
    Forma_CampoOculto('total', $total);
    for($i=0;$i<$total;$i++){
      Forma_CampoOculto('mn_lecture_fee_'.$i, $mn_lecture_fee[$i]);
      Forma_CampoOculto('mn_lecture_fee_err_'.$i, $mn_lecture_fee_err[$i]);
      Forma_CampoOculto('mn_extra_fee_'.$i,$mn_extra_fee[$i] );
      Forma_CampoOculto('mn_extra_fee_err_'.$i, $mn_extra_fee_err[$i]);
    }
    echo "\n</form>
<script>
 document.datos.submit();
</script></body></html>";
    exit;
  }
  
  # Prepara fechas en formato para insertar
  if(!empty($fe_nacimiento))
    $fe_nacimiento = "'".ValidaFecha($fe_nacimiento)."'";
  else
    $fe_nacimiento = "NULL";
  
  # Verifica si se esta insertando
  if(empty($clave)) {
    
    # Genera un identificador de sesion
    $cl_sesion_nueva = sha256($ds_login.$ds_nombres.$ds_apaterno.$ds_password);
    
    # Inserta el usuario
    $Query  = "INSERT INTO c_usuario(ds_login, ds_password, cl_sesion, fg_activo, fe_alta, no_accesos, ";
    $Query .= "ds_nombres, ds_apaterno, ds_amaterno, fg_genero, fe_nacimiento, ds_email, fl_perfil) ";
    $Query .= "VALUES('$ds_login', '".sha256($ds_password)."', '$cl_sesion_nueva', '$fg_activo', CURRENT_TIMESTAMP, 0, ";
    $Query .= "'$ds_nombres', '$ds_apaterno', '$ds_amaterno', '$fg_genero', $fe_nacimiento, '$ds_email', $fl_perfil) ";
    $fl_usuario = EjecutaInsert($Query);
    
    # Inserta el maestro
    $Query  = "INSERT INTO c_maestro(fl_maestro, fl_pais, fl_zona_horaria) ";
    $Query .= "VALUES($fl_usuario, $fl_pais, $fl_zona_horaria) ";
    EjecutaQuery($Query);
  }
  else {
    
    # Actualiza los datos del usuario
    $Query  = "UPDATE c_usuario SET fl_perfil=$fl_perfil, fg_activo='$fg_activo', ds_nombres='$ds_nombres', ds_apaterno='$ds_apaterno', ";
    $Query .= "ds_amaterno='$ds_amaterno', fg_genero='$fg_genero', fe_nacimiento=$fe_nacimiento, ds_email='$ds_email' ";
    $Query .= "WHERE fl_usuario=$clave";
    EjecutaQuery($Query);
    
    # Actualiza los datos del maestro
    $Query  = "UPDATE c_maestro SET fl_pais=$fl_pais, fl_zona_horaria=$fl_zona_horaria, ds_number='$ds_number' ";
    $Query .= "WHERE fl_maestro=$clave";
    EjecutaQuery($Query);
    
    # Inserta o actualiza las tarifas 
    for($i=0;$i<$total;$i++){
      if(ExisteEnTabla('k_maestro_tarifa','fl_maestro',$clave) AND ExisteEnTabla('k_maestro_tarifa','fl_programa',$fl_programa[$i]) 
      AND ExisteEnTabla('k_maestro_tarifa','fl_grupo',$fl_grupo[$i])){
        $Query  = "UPDATE k_maestro_tarifa SET mn_lecture_fee=$mn_lecture_fee[$i],mn_extra_fee=$mn_extra_fee[$i] ";
        $Query .= "WHERE fl_maestro=$clave AND fl_programa=$fl_programa[$i] AND fl_grupo=$fl_grupo[$i]";
      }
      else{
        $Query  = "INSERT INTO k_maestro_tarifa (fl_maestro,fl_programa,fl_grupo,mn_lecture_fee,mn_extra_fee) ";
        $Query .= "VALUES ($clave,$fl_programa[$i],$fl_grupo[$i],$mn_lecture_fee[$i],$mn_extra_fee[$i]) ";
      }
      EjecutaQuery($Query);
    } 
  }
  
  # Redirige al listado
  header("Location: ".ObtenProgramaBase( ));
  
?>