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
 $fname=RecibeParametroHTML('fname');
 $lname=RecibeParametroHTML('lname');
 $fe_nacimiento = "'".ValidaFecha($fe_nacimiento)."'";
 $fg_desbloquear_curso=RecibeParametroBinario('fg_desbloquear_curso');


 
 #Recuperamos quien se registro:
 $Query="SELECT ds_first_name,ds_last_name,ds_email,fl_invitado_por_instituto, fl_usu_invita FROM k_envio_email_reg_selfp WHERE fl_envio_correo=$fl_envio_correo ";
 $row=RecuperaValor($Query);
 $ds_nombres= $row[0];
 $ds_apaterno= $row[1];
 $ds_email=$row[2];
 $fl_instituto=$row[3];
 $fl_usu_invita = $row[4];
 
 
 if(empty($ds_nombres))
   $ds_nombres=$fname;
 if(empty($ds_apaterno))
   $ds_apaterno=$lname; 
 
 
 
 
 
 if($fg_desbloquear_curso==1){
 #Desbloqueamos el curso que tiene asignado este envio de correo
 $Query="SELECT fl_invitado_por_usuario,fl_programa_sp,ds_email FROM c_desbloquear_curso_alumno WHERE fl_envio_correo=$fl_envio_correo ";
 $row=RecuperaValor($Query);
 $fl_invitado_por=$row['fl_invitado_por_usuario'];
 $fl_programa_sp=$row['fl_programa_sp'];
 $ds_email_confirmado=$row['ds_email'];
 
 #Actualizamos datos de nombre en k_envio_email_reg_selfp
 EjecutaQuery("UPDATE k_envio_email_reg_selfp SET ds_first_name='$ds_nombres',ds_last_name='$ds_apaterno' WHERE fl_envio_correo=$fl_envio_correo ");
 
 #Verificamos que no existe un curso asignado
 $rowb=RecuperaValor("SELECT COUNT(*) FROM k_usuario_programa WHERE fl_programa_sp=$fl_programa_sp AND fl_usuario_sp=$fl_invitado_por "); 
 $existe=$rowb[0];

 
 
 
 
 
 
	 if(empty($existe)) {
	        
           
             #Verificamos si es el segundo email a confirmar, para poder desbloquerlo
             $rowa=RecuperaValor("
                        SELECT COUNT(*) 
                        FROM k_envio_email_reg_selfp a, c_desbloquear_curso_alumno b 
                        WHERE a.fl_envio_correo=b.fl_envio_correo  AND fg_desbloquear_curso='1'
                        AND fl_invitado_por_usuario=$fl_invitado_por AND fg_confirmado='1' ");
             
             $no_email_confirmados=$rowa[0]+1; #CuentaNoCursosPorMetodoEnvioEmails($fl_invitado_por,$fl_programa_sp)+1;//se le suma uno por que seria el que estaria confirmando en este momento.
         
             #vERIFICAMOS CUANTOS REQUEISTOS ES ARA CUMPLIR LA CNDICION.
             $ERY="SELECT no_email_desbloquear FROM c_programa_sp WHERE fl_programa_sp=$fl_programa_sp ";
             $r=RecuperaValor($ERY);
             $requisito_email=$r['no_email_desbloquear'];
         
             if(empty($requisito_email))
                 $requisito_email=ObtenConfiguracion(122);
         
         
             #buscamos un teacher activo y se lo asignamos esto es aleatoriamente.
             $Query="SELECT fl_usuario FROM c_usuario WHERE fl_instituto=4 AND fl_perfil_sp=".PFL_MAESTRO_SELF." AND fg_activo='1' ORDER BY RAND() LIMIT 1 ";
             $ru=RecuperaValor($Query);
             $fl_maestro=$ru['fl_usuario'];
         
             #Recupermaos el nombre del programa 
             $Query2="SELECT nb_programa,no_dias_trial FROM c_programa_sp WHERE fl_programa_sp=$fl_programa_sp ";
             $row2=RecuperaValor($Query2);
             $nb_programa_desbloquear=str_texto($row2['nb_programa']);
             $no_dias_permitidos_curso=$row2['no_dias_trial'];
         
	        
             
		     if($no_email_confirmados== $requisito_email ){#Ya existe uno asi , que esta cueta a activarse en total ya seran dos, lo que cumple para poder desbloquera curso.NOTA: los siguientes email que se activen ya no tendran validez, para debloquear curso.
				$fl_maestro="642";
				
				
				  #Verifica que no exista el registro.
				  $Query="SELECT COUNT(*) FROM k_usuario_programa WHERE fl_usuario_sp=$fl_invitado_por AND fl_programa_sp=$fl_programa_sp ";
				  $rw=RecuperaValor($Query);
				  $no_reg=$rw[0];
				
				if(empty($no_reg)){
				
                        
                       
                    
                    
						$Query ="INSERT INTO k_usuario_programa(fl_usuario_sp,fl_programa_sp,ds_progreso,fg_terminado,fg_certificado,fg_status,fg_pagado,mn_pagado,fl_maestro,fg_status_pro,fe_inicio_programa) ";
						$Query.="VALUES($fl_invitado_por,$fl_programa_sp,0,'0','0','RD','0',0,$fl_maestro,'0',CURRENT_TIMESTAMP)";
						$fl_usu_pro=EjecutaInsert($Query);
						
						# Por defaul indicamos que tendran una calificacion de quiz
                        EjecutaQuery("INSERT INTO k_details_usu_pro (fl_usu_pro,fg_quizes,fg_grade_tea) VALUES ($fl_usu_pro,'1','0')");

                      
						
						#Se genera el orden cronologico de desbloqueo.
                        $Quert="SELECT no_orden FROM k_orden_desbloqueo_curso_alumno WHERE fl_alumno=$fl_invitado_por ORDER BY no_orden DESC ";
						$fl=RecuperaValor($Quert);
                        $no_consecutiv=$fl['no_orden']+1;
						
						#Se genera su registro.
                        $fl_consecu=EjecutaInsert("INSERT INTO k_orden_desbloqueo_curso_alumno (fl_alumno,fl_programa_sp,no_orden,fe_creacion,fg_motivo )VALUES($fl_invitado_por , $fl_programa_sp,$no_consecutiv,CURRENT_TIMESTAMP,'EM') ");
                       
                        
                        
                        #Obtenemos la fecha actual y le sumamos el numero de dias permitidos para saber su fecha limite de modo trial para este curso.
                        $Query = "Select CURDATE() ";
                        $row = RecuperaValor($Query);
                        $fe_actual = str_texto($row[0]);
                        if(empty($no_dias_permitidos_curso))
                        $no_dias_permitidos_curso=ObtenConfiguracion(127);
                        
                        
                        $fe_actual=strtotime('+'.$no_dias_permitidos_curso.' day',strtotime($fe_actual));
                        $fe_expiracion_curso_trial= date('Y-m-d',$fe_actual);
                        
                        #SE genera su periodo de prueba de desbloqueo del curso.
                        $Query="INSERT INTO k_periodo_trial_curso_alumno (fl_programa_sp,fl_alumno,fe_periodo_inicial,fe_periodo_final,fe_creacion )";
                        $Query.="VALUES ($fl_programa_sp,$fl_invitado_por,CURRENT_TIMESTAMP,'$fe_expiracion_curso_trial',CURRENT_TIMESTAMP) ";
                        $fl_perio=EjecutaInsert($Query);
                        
						
						
						
					    #Aqui falta un llamado al node para que ejecute la accion de eviernotoficacion al estudiante.  params funcion=fl_invitado_por=fl_usuario_destiatario,ds_email_confirmado: email que esta confirmado su registro,nb_prohrama es el prograa que se va a desbloquear.
					    echo"
					      <script>
						      function EnviaEmail(fl_usuario_destino,ds_email_confirmado,nb_programa_desbloquear){
							  
							     var fl_usuario_destino=fl_usuario_destino;
								 var ds_email_confirmado=ds_email_confirmado;
							     var nb_programa_desbloquear=nb_programa_desbloquear;
								 
								 socket.emit('email-confirmado', fl_usuario_destino,ds_email_confirmado,nb_programa_desbloquear);
								 
							    
							
							}
							
							     EnviaEmail($fl_invitado_por,'$ds_email_confirmado','$nb_programa_desbloquear');
					       </script>
					   
					    ";
                       
                       
                           #Se genera un email de confirmacion al alumno que recibe el curso 
                           $ds_encabezado = genera_documento_sp($fl_invitado_por,1,148,$fl_programa_sp);
                           $ds_cuerpo = genera_documento_sp($fl_invitado_por,2,148,$fl_programa_sp);
                           $ds_pie = genera_documento_sp($fl_invitado_por,3,148,$fl_programa_sp);
                           $ds_contenido=$ds_encabezado.$ds_cuerpo.$ds_pie;
                       
                           #Recupermaos el email del usuario 
                           $Quer="SELECT ds_email FROM c_usuario WHERE fl_usuario=$fl_invitado_por ";
                           $row=RecuperaValor($Quer);
                           $ds_email_destinatario=str_texto($row[0]);
                           
                           $ds_contenido = str_replace("#fame_number_of_days#", $no_dias_permitidos_curso, $ds_contenido);  #no_dias_cuso
                       
                           #Recuperamos el titulo del documento
                           $Query="SELECT nb_template FROM k_template_doc WHERE fl_template=148 ";
                           $row=RecuperaValor($Query);
                           $ds_titulo=str_texto($row[0]);
                       
                           $ds_email_de_quien_envia_mensaje=ObtenConfiguracion(4);
                           $nb_nombre_dos=ObtenEtiqueta(949);#nombre de quien envia el mensaje
                           $bcc=ObtenConfiguracion(107);
                           $message  = $ds_contenido;
                           $message = utf8_decode(str_ascii(str_uso_normal($message)));
                           #Envia email de notificcion al usuario
                           $mail = EnviaMailHTML($nb_nombre_dos, $ds_email_de_quien_envia_mensaje, $ds_email_destinatario, $ds_titulo, $message, $bcc);
                           
                           
				}
				
                
                
                
				
			 }else{
			 
				 
                   echo"<script>

					        function EnviaEmail(fl_usuario_destino,ds_email_confirmado,nb_programa_desbloquear){
							     var fl_usuario_destino=fl_usuario_destino;
								 var ds_email_confirmado=ds_email_confirmado;
							     var nb_programa_desbloquear=nb_programa_desbloquear;
								 
								 socket.emit('email-confirmado', fl_usuario_destino,ds_email_confirmado,nb_programa_desbloquear);
								 
							    
							
							}
							
							EnviaEmail($fl_invitado_por,'$ds_email_confirmado','$nb_programa_desbloquear');
							
						</script>";
             
                    
             
             
             
             
             }
             
             #Se genera la confimacion del email para notifys.
             $Query="INSERT INTO k_confirmacion_email_curso (fl_alumno_beneficiado,fl_programa_sp,ds_email,fg_revisado_alumno,fe_creacion)";
             $Query.="VALUES($fl_invitado_por,$fl_programa_sp,'$ds_email_confirmado','0',CURRENT_TIMESTAMP)";
             $fl_dat=EjecutaInsert($Query);
             
             
             
             
	 }
 
 }
 
 
 
 
 
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
 
 
 if($fg_desbloquear_curso==1){
    
     #Se inserta el gettin estarted por default en primer lugar,para este nuevo alumno,Palica para alumnos de nuevo ingreso de Fame de Vanas.
     $Query="INSERT INTO k_orden_desbloqueo_curso_alumno (fl_alumno,fl_programa_sp,no_orden,fe_creacion,fg_motivo )
         VALUES($fl_usuario_sp,33,1,CURRENT_TIMESTAMP,'') ";
     $fl_regi=EjecutaInsert($Query);
     
    
     
 }
 
 
 
 
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