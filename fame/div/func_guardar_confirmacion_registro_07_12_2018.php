<?php
require '../lib/self_general.php';


  
 #Rcibe datos generales de registro.
 
 $ds_name_school= RecibeParametroHTML('ds_name_school');
 $fl_country= RecibeParametroNumerico('fl_country');
 $ds_pass1= RecibeParametroHTML('ds_pass1'); 
 $confirm_pass=RecibeParametroHTML('ds_pass2'); 
 $cl_clave_pais=RecibeParametroHTML('cl_iso_pais');
 $ds_coddigo_pais2=RecibeParametroHTML('ds_coddigo_pais2');#siempre viene vacio no importa:
 $ds_codigo_pais=RecibeParametroHTML('ds_codigo_pais');
 $ds_codigo_telefono=RecibeParametroHTML('ds_codigo_telefono');
 $ds_numero_telefono=RecibeParametroHTML('ds_numero_telefono');
 $cl_tipo_registro=RecibeParametroNumerico('fg_option');
 $fl_envio_correo=RecibeParametroNumerico('fl_envio_correo');
 $fl_estado=RecibeParametroHTML('fl_estado');
$ds_codigo_pais="+".$ds_codigo_pais;
$ds_alias=RecibeParametroHTML('ds_alias');
 
$ds_codigo_areas = intval(preg_replace('/[^0-9]+/', '', $ds_codigo_telefono), 10); 
$ds_codigo_telefono="(".$ds_codigo_areas.")";

 #Recuperamos quien se registro:
 $Query="SELECT ds_first_name,ds_last_name,ds_email FROM k_envio_email_reg_selfp WHERE fl_envio_correo=$fl_envio_correo ";
 $row=RecuperaValor($Query);
 $ds_nombres= $row[0];
 $ds_apaterno= $row[1];
 $ds_email=$row[2];

 
 $ds_login=$ds_email;
 # Genera un identificador de sesion
 $cl_sesion_nueva = sha256($ds_login.$ds_nombres.$ds_apaterno.$ds_pass1);
 $fg_activo=1;//se activa cu cuenta
 
 
 
 
 
 $fl_perfil_sp=PFL_ADMINISTRADOR;#Pefil de administrador de self pace.(hace falta checar que pefrli tiene );
 $fl_perfil=0;
 
 
 #CREAOS EL USUARIO Y PASSOWR PARA ACCEDER AL SISTEMA. y se activa su cuenta
 # Inserta el usuario
 $Query  = "INSERT INTO c_usuario(ds_login, ds_password, cl_sesion,ds_alias, fg_activo, fe_alta, no_accesos, ";
 $Query .= "ds_nombres, ds_apaterno, ds_email, fl_perfil,fl_perfil_sp,fg_system) ";
 $Query .= "VALUES('$ds_login', '".sha256($ds_pass1)."', '$cl_sesion_nueva','$ds_alias', '$fg_activo', CURRENT_TIMESTAMP, 0, ";
 $Query .= "'$ds_nombres', '$ds_apaterno','$ds_email', $fl_perfil,$fl_perfil_sp,'F') ";
 $fl_usuario=EjecutaInsert($Query);
 
#Se genera su direccion del usuario no del instituto, por el momento solo es su fl_sp.
 $Query ="INSERT INTO k_usu_direccion_sp ( fl_usuario_sp,fl_pais,ds_state ) ";
 $Query .="VALUES ($fl_usuario,$cl_clave_pais,'$fl_estado' ) ";
 $fl_direccion=EjecutaInsert($Query); 
 
 # Insertamos el administrador
 $QueryA  = "INSERT INTO c_administrador_sp (fl_adm_sp,fl_zona_horaria,ds_ruta_avatar,ds_ruta_foto,ds_oficial,ds_website, ";
 $QueryA .= "ds_gustos,ds_pasatiempos,ds_notas,ds_power,ds_favorite_movie) ";
 $QueryA .= "VALUES ($fl_usuario,0,'','','','','','','','','') ";
 EjecutaQuery($QueryA);
 
      # Genera una nueva clave para id
      $str = "1234567890";
      for($i = 0; $i < 6; $i++){
          $ds_id .= substr($str, rand(0,3), 1);
      }
 
 $ds_login3 = substr(strtolower($ds_apaterno), 0, 1) . substr(strtolower($ds_nombres), 0, 1);
 $ds_login3 = $ds_login3 .$ds_id  ;
 $ds_login3 = $ds_login3 . str_pad($fl_usuario, 4, "0", STR_PAD_LEFT);

 #genermossu id
 $Query="UPDATE c_usuario SET ds_login='$ds_login3',fg_system='F' WHERE fl_usuario=$fl_usuario ";
 EjecutaQuery($Query);
 
 
 
 
 
 #se registra la institucion y por lo tanto cuenta con N dias de prueba (c_configuracion:101;) free
 $no_dias_permitidos_para_modo_trial=ObtenConfiguracion(101);
 
 #Realizamos el calculo de numeros de dias permitidos apartir de la fecha de registro del instituto.
 
 
         #1. Obtemeos la fecha actual y le sumamos el numero de dias permitidos para saber su fecha limite de modo trial.
         $Query = "Select CURDATE() ";
         $row = RecuperaValor($Query);
         $fe_actual = str_texto($row[0]);
         $fe_actual=strtotime('+'.$no_dias_permitidos_para_modo_trial.' day',strtotime($fe_actual));
         $fe_expiracion_modo_trial= date('Y-m-d',$fe_actual);

         
 $Query="INSERT into c_instituto (cl_tipo_instituto,fl_usuario_sp,ds_instituto,fl_pais,ds_codigo_pais,ds_codigo_area,no_telefono,fe_creacion,fe_trial_expiracion,fg_tiene_plan,fg_activo )";
 $Query.="VALUES($cl_tipo_registro,$fl_usuario,'$ds_name_school',$fl_country,'$ds_codigo_pais','$ds_codigo_telefono','$ds_numero_telefono',CURRENT_TIMESTAMP,'$fe_expiracion_modo_trial','0','1')";
 $fl_instituto=EjecutaInsert($Query);
 
  #actualiza a activo el tipo de registro.
 $Query="UPDATE k_envio_email_reg_selfp SET fg_confirmado='1',fl_invitado_por_instituto=$fl_instituto ";
 $Query.="WHERE fl_envio_correo=$fl_envio_correo  ";
 EjecutaQuery($Query);
 #se actualiza y recuperamos a que instituto pertenece.
 $Query="UPDATE c_usuario SET fl_instituto=$fl_instituto,fg_system='F' WHERE fl_usuario=$fl_usuario ";
 EjecutaQuery($Query);
 
 
 
 #Se genera la tarifa de la institucion.
 $Query="SELECT no_ini, no_fin,ds_descuento_mensual,mn_mensual,ds_descuento_anual,mn_anual,mn_descuento_licencia,fg_activo  FROM c_princing WHERE fl_instituto=1 ORDER BY fl_princing ASC ";
 $rs = EjecutaQuery($Query);
       for($i=1;$row=RecuperaRegistro($rs);$i++) {
          
          $mn_rango_ini= $row['no_ini'];
          $mn_rango_fin= $row['no_fin'];
          $ds_descuento_mensual=$row['ds_descuento_mensual'];
          $ds_descuento_anual=$row['ds_descuento_anual'];
          $mn_anual=$row['mn_anual'];
          $mn_mensual=$row['mn_mensual'];
		  $mn_descuento_licencia=$row['mn_descuento_licencia'];
          $fg_activo=$row['fg_activo'];
          
          $Query="SELECT MAX(fl_princing)AS fl_princing FROM c_princing  ";
          $row=RecuperaValor($Query);
          $fl_princing_max=$row['fl_princing'];
          $fl_princing =$fl_princing_max + 1;
          
          
          $Query="INSERT INTO c_princing (fl_princing,no_ini,no_fin,ds_descuento_mensual,mn_mensual,ds_descuento_anual,mn_anual,fl_instituto,mn_descuento_licencia,fg_activo ) ";
          $Query.="VALUES($fl_princing,$mn_rango_ini,$mn_rango_fin,$ds_descuento_mensual,$mn_mensual,$ds_descuento_anual,$mn_anual,$fl_instituto,$mn_descuento_licencia,'$fg_activo')";
          EjecutaQuery($Query);
          
          
 
       }
 
 

 #Se envia correo de bienvenida al usuario:
 EnviaMaildeBienvendida($fl_usuario,$fl_instituto);    
       
  
 #Envia notificacion de confirmacion de registro.
 
 $ds_encabezado=genera_documento_aceptacionFAME($fl_envio_correo,1,139,PFL_ADMINISTRADOR,$fl_usu_invita);
 $ds_cuerpo=genera_documento_aceptacionFAME($fl_envio_correo,2,139,PFL_ADMINISTRADOR,$fl_usu_invita);
 $ds_pie=genera_documento_aceptacionFAME($fl_envio_correo,3,139,PFL_ADMINISTRADOR,$fl_usu_invita);
 
 $row=RecuperaValor("SELECT nb_template FROM k_template_doc WHERE fl_template=139 ");
 $nb_template=str_texto($row[0]);
 
 
 $ds_contenido =$ds_encabezado.$ds_cuerpo.$ds_pie;
 
 
 $ds_email_destinatario=ObtenConfiguracion(107);#fame 
 
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
 
 #envia copia admin fame
 //$mail = EnviaMailHTML($nb_quien_envia_email, $ds_email_de_quien_envia_mensaje, $bbc, $ds_titulo, $message, $bcc);
 
 
 #end envio noitificcion         
       
 
 
 $cl_sesion=$cl_sesion_nueva;

 # Crea cookie con identificador de sesion
 setcookie(SESION_RM, $cl_sesion, time( )+SESION_VIGENCIA_RM, "/");
 setcookie(SESION_CHECK_RM, 'True', time( )+SESION_VIGENCIA_RM, "/");
 //EjecutaQuery("UPDATE c_usuario SET fg_remember_me='1' WHERE cl_sesion='$cl_sesion'");
 ActualizaSesion($cl_sesion, false);
                           
?>