<?php
require '../lib/self_general.php';


 #Rcibe datos generales de registro.
 $ds_pass= RecibeParametroHTML('ds_pass3'); 
 $confirm_pass=RecibeParametroHTML('ds_pass4'); 
 $fe_nacimiento=RecibeParametroFecha('fe_nacimiento');
 $fg_tipo_sexo=RecibeParametroHTML('cl_sexo');
 $cl_tipo_registro=RecibeParametroNumerico('fg_option');
 $fl_envio_correo=RecibeParametroNumerico('fl_envio_correo');
 $fg_tipo_registro=RecibeParametroHTML('fg_tipo_registro');//Teacher-Alumno
 $fl_grado=RecibeParametroNumerico('cl_grado');//esclusivo alumno.
 $fg_falta_autorizacion=RecibeParametroBinario('fg_falta_autorizacion');//falta autorixacion del tutor.
 $fe_nacimiento=trim($fe_nacimiento);
 
 $fe_nacimiento = "'".ValidaFecha($fe_nacimiento)."'";

 
 #Recuperamos quien se registro:
 $Query="SELECT ds_first_name,ds_last_name,ds_email,fl_invitado_por_instituto, fl_usu_invita FROM k_envio_email_reg_selfp WHERE fl_envio_correo=$fl_envio_correo ";
 $row=RecuperaValor($Query);
 $ds_nombres= $row[0];
 $ds_apaterno= $row[1];
 $ds_email=$row[2];
 $fl_instituto=$row[3];
 $fl_usu_invita = $row[4];
 
 $ds_login=$ds_email;
 # Genera un identificador de sesion
 $cl_sesion_nueva = sha256($ds_login.$ds_nombres.$ds_apaterno.$ds_pass);

 
 if($fg_tipo_registro=="S"){
     $fl_perfil_sp=PFL_ESTUDIANTE_SELF;//perfil de student.
     
     if($fg_falta_autorizacion)
         $fg_activo=0;
     else
         $fg_activo=1;
     
     
 }else{
     $fl_perfil_sp=PFL_MAESTRO_SELF;//perfil de teacher.
     $fg_activo=1;//se activa cu cuenta
 
 }

 

 $fl_perfil=0;
 
 
 #CREAOS EL USUARIO Y PASSOWR PARA ACCEDER AL SISTEMA. y se activa su cuenta
 # Inserta el usuario
 $Query  = "INSERT INTO c_usuario(ds_login, ds_password,ds_alias, cl_sesion, fg_activo, fe_alta, no_accesos, ";
 $Query .= "ds_nombres, ds_apaterno, ds_email, ";
 if($fl_perfil_sp==PFL_ESTUDIANTE_SELF)
 $Query.="fg_genero,fe_nacimiento, ";
 $Query.="fl_perfil,fl_perfil_sp,fl_instituto, fl_usu_invita) ";
 $Query .= "VALUES('$ds_login', '".sha256($ds_pass)."','$ds_login', '$cl_sesion_nueva', '$fg_activo', CURRENT_TIMESTAMP, 0, ";
 $Query .= "'$ds_nombres', '$ds_apaterno','$ds_email', ";
 if($fl_perfil_sp==PFL_ESTUDIANTE_SELF)
 $Query .= "'$fg_tipo_sexo',$fe_nacimiento, ";
 $Query .=" $fl_perfil,$fl_perfil_sp,$fl_instituto, $fl_usu_invita) ";
 $fl_usuario_sp=EjecutaInsert($Query);
 
 
#Se genera su direccion , por el momento solo es su fl_sp.
  $row0 = RecuperaValor("SELECT COUNT(*) FROM k_usu_direccion_sp WHERE fl_usuario_sp=$fl_envio_correo");
  if(!empty($row0[0])){
    EjecutaQuery("UPDATE k_usu_direccion_sp SET fl_usuario_sp=$fl_usuario_sp WHERE fl_usuario_sp=$fl_envio_correo");
  }
  else{
    $Query ="INSERT INTO k_usu_direccion_sp ( fl_usuario_sp ) ";
    $Query .="VALUES ($fl_usuario_sp ) ";
    $fl_direccion=EjecutaInsert($Query); 
  }
 
 # Genera una nueva clave para id
 $str = "1234567890";
 for($i = 0; $i < 6; $i++){
     $ds_id .= substr($str, rand(0,3), 1);
 }
 
 $ds_login3 = substr(strtolower($ds_apaterno), 0, 1) . substr(strtolower($ds_nombres), 0, 1);
 $ds_login3 = $ds_login3 .$ds_id  ;
 $ds_login3 = $ds_login3 . str_pad($fl_usuario_sp, 4, "0", STR_PAD_LEFT);

 #actualizamos su ID DS_LOGIN
 $Query="UPDATE c_usuario SET ds_login='$ds_login3' WHERE fl_usuario=$fl_usuario_sp ";
 EjecutaQuery($Query);
 
 
 #Validamos las licencias ocupadas.
 if($fl_perfil_sp==PFL_ESTUDIANTE_SELF){
     
     
      $no_licencias_disponibles=ObtenNumLicenciasDisponibles($fl_instituto);
      $no_licencias_usadas=ObtenNumLicenciasUsadas($fl_instituto);
      
      $nuevo_no_licencias_disponibles=$no_licencias_disponibles - 1 ;
      $no_nuevo_licencias_usadas= $no_licencias_usadas + 1 ;      
      $Qury="UPDATE k_current_plan SET no_licencias_disponibles=$nuevo_no_licencias_disponibles, no_licencias_usadas=$no_nuevo_licencias_usadas WHERE fl_instituto=$fl_instituto ";
      EjecutaQuery($Qury);
      
      
 
 
 }
 
 
 
 
 
 
 if($fg_tipo_registro=="S"){
     
     #Se inserta el estudiante:
     $Query="INSERT INTO c_alumno_sp(fl_alumno_sp,fl_grado)";
     $Query.="VALUES($fl_usuario_sp,$fl_grado)";
     $fl_maestro=EjecutaInsert($Query);
    // $row2 = RecuperaValor("SELECT COUNT(*) FROM k_responsable_alumno WHERE fl_usuario=$fl_envio_correo");
    $row2 = RecuperaValor("SELECT fl_responsable_alumno, cl_parentesco FROM k_responsable_alumno WHERE fl_usuario=$fl_envio_correo");
    $fl_responsable_alumno = $row2[0];
    $cl_parentesco = $row2[1];
    if(!empty($row2[0]))
      $Queryy = "UPDATE  k_responsable_alumno SET fl_usuario=$fl_usuario_sp WHERE fl_usuario=$fl_envio_correo  ";
    else
      $Queryy = "INSERT INTO k_responsable_alumno (cl_parentesco,fl_usuario) VALUES ($cl_parentesco, $fl_usuario_sp)";
    EjecutaQuery($Queryy);    
     
     
 }else{
 
     #Se inserta el teacher:
     $Query="INSERT INTO c_maestro_sp(fl_maestro_sp)";
     $Query.="VALUES($fl_usuario_sp)";
     $fl_maestro=EjecutaInsert($Query);
 }

 
 
 
  #actualiza a activo el tipo de registro.
 $Query="UPDATE k_envio_email_reg_selfp SET fg_confirmado='1' WHERE fl_envio_correo=$fl_envio_correo  ";
 EjecutaQuery($Query);
 
 $cl_sesion=$cl_sesion_nueva;
 

 
 #Recuperamos el no. de usuarios que tiene el instituto y le sumaos el nuevo registro
 $Query="SELECT no_usuarios FROM c_instituto WHERE fl_instituto=$fl_instituto ";
 $row=RecuperaValor($Query);
 $no_usuarios_actual = $row[0] +1 ;
 
 #Actualizamos el registro de numero de usuarios que tiee el isntituto.
 $Query="UPDATE c_instituto SET no_usuarios=$no_usuarios_actual WHERE fl_instituto=$fl_instituto ";
 EjecutaQuery($Query);
 
 
 
 #Se envia correo de bienvenida al usuario:
EnviaMaildeBienvendida($fl_usuario_sp,$fl_instituto);    
 
 #Envia notificacion de confirmacion de registro.




$ds_encabezado=genera_documento_aceptacionFAME($fl_envio_correo,1,139,$fl_perfil_sp,$fl_usu_invita);
$ds_cuerpo=genera_documento_aceptacionFAME($fl_envio_correo,2,139,$fl_perfil_sp,$fl_usu_invita);
$ds_pie=genera_documento_aceptacionFAME($fl_envio_correo,3,139,$fl_perfil_sp,$fl_usu_invita);

$row=RecuperaValor("SELECT nb_template FROM k_template_doc WHERE fl_template=139 ");
$nb_template=str_texto($row[0]);


$ds_contenido =$ds_encabezado.$ds_cuerpo.$ds_pie;

#Recupermaos el email de quien invito a este usuario;
$Query="SELECT ds_email FROM c_usuario WHERE fl_usuario=$fl_usu_invita ";
$row=RecuperaValor($Query);
$ds_email_destinatario=$row[0];#fame 

# Inicializa variables de ambiente para envio de correo
ini_set("SMTP", MAIL_SERVER);
ini_set("smtp_port", MAIL_PORT);
ini_set("sendmail_from", MAIL_FROM); 
$message  = $ds_contenido;
$message = utf8_decode(str_ascii(str_uso_normal($message)));
$bcc=ObtenConfiguracion(107);
$nb_quien_envia_email=ObtenEtiqueta(949);#Vamcouver School nombre de quien envia el mensaje
$ds_email_de_quien_envia_mensaje=ObtenConfiguracion(4);
$ds_titulo=$nb_template;#etiqueta de asunto del mensjae para el envio
$mail = EnviaMailHTML($nb_quien_envia_email, $ds_email_de_quien_envia_mensaje, $ds_email_destinatario, $ds_titulo, $message, $bcc);

#envia copia al admin fame
//$mail = EnviaMailHTML($nb_quien_envia_email, $ds_email_de_quien_envia_mensaje, $bbc, $ds_titulo, $message, $bcc);


#end envio noitificcion
 
 
 
 # Crea cookie con identificador de sesion
 setcookie(SESION_RM, $cl_sesion, time( )+SESION_VIGENCIA_RM, "/");
 setcookie(SESION_CHECK_RM, 'True', time( )+SESION_VIGENCIA_RM, "/");
 //EjecutaQuery("UPDATE c_usuario SET fg_remember_me='1' WHERE cl_sesion='$cl_sesion'");
 ActualizaSesion($cl_sesion, false);
                           
?>