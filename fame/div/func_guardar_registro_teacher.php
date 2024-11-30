<?php


require '../../AD3M2SRC4/lib/general.inc.php';

 # Recibe  datos de envio e email.
  //$ds_first_name= RecibeParametroHTML('fname');
  //$ds_last_name = RecibeParametroHTML('lname');
  //$ds_email = RecibeParametroHTML('email');
  
 #Rcibe datos generales de registro.
 
 
 $ds_pass= RecibeParametroHTML('ds_pass3'); 
 $confirm_pass=RecibeParametroHTML('ds_pass4'); 
 $cl_tipo_registro=RecibeParametroNumerico('fg_option');
 $fl_envio_correo=RecibeParametroNumerico('fl_envio_correo');


 
 #Recuperamos quien se registro:
 $Query="SELECT ds_first_name,ds_last_name,ds_email, fl_invitado_por_instituto, fl_usuario FROM k_envio_email_reg_selfp_adm WHERE fl_envio_correo=$fl_envio_correo ";
 $row=RecuperaValor($Query);
 $ds_nombres= $row[0];
 $ds_apaterno= $row[1];
 $ds_email=$row[2];
 $fl_instituto=$row[3];
 $fl_usuario=$row[4];

 
 $ds_login=$ds_email;
 # Genera un identificador de sesion
 $cl_sesion_nueva = sha256($ds_login.$ds_nombres.$ds_apaterno.$ds_pass);
 $fg_activo=1;//se activa cu cuenta
 
 $fl_perfil=1;//falta checar que pefil tine esta cuentade administrador.
 
 
 
 #CREAOS EL USUARIO Y PASSOWR PARA ACCEDER AL SISTEMA. y se activa su cuenta
 # Inserta el usuario
 $Query  = "INSERT INTO c_usuario (fl_usuario, ds_login, ds_password, cl_sesion, fg_activo, fe_alta, no_accesos, ";
 $Query .= "ds_nombres, ds_apaterno, ds_email, fl_perfil, fg_system, fl_instituto) ";
 $Query .= "VALUES('$fl_usuario', '$ds_login', '".sha256($ds_pass)."', '$cl_sesion_nueva', '$fg_activo', CURRENT_TIMESTAMP, 0, ";
 $Query .= "'$ds_nombres', '$ds_apaterno','$ds_email', $fl_perfil, 'F', $fl_instituto) ";
 $QUery.="ON DUPLICATE KEY UPDATE";
 $Query .= "ds_login = '$ds_login',
            ds_password = '".sha256($ds_pass1)."', 
            cl_sesion = '$cl_sesion_nueva', 
            fg_activo = '$fg_activo', 
            fe_alta = CURRENT_TIMESTAMP, 
            no_accesos = 0, 
            ds_nombres = '$ds_nombres', 
            ds_apaterno = '$ds_apaterno', 
            ds_email = '$ds_email', 
            fl_perfil = '$fl_perfil',
            fg_system = 'F', 
            fl_instituto = $fl_instituto";

 $fl_usuario_sp=EjecutaInsert($Query);
 
 EjecutaQuery("DELETE FROM k_notify_fame_feed WHERE fl_usuario=$fl_usuario_sp  ");
 #Inserta por default las notificaciones.
 $Query="INSERT INTO k_notify_fame_feed (fl_usuario,fg_nuevo_post,fg_coment_post,fg_like_post,fg_ayuda_post,fg_follow)";
 $Query.="VALUES($fl_usuario_sp,'1','1','1','1','1')";
 EjecutaQuery($Query);
 
 #Generamos su token
 $token=sha256($fl_usuario_sp);
 EjecutaQuery("UPDATE c_usuario SET token='$token' WHERE fl_usuario=$fl_usuario_sp ");
 
 
  #actualiza a activo el tipo de registro.
 $Query="UPDATE k_envio_email_reg_selfp_adm SET fg_confirmado='1' WHERE fl_envio_correo=$fl_envio_correo  ";
 EjecutaQuery($Query);
 
 
 
                           
?>
