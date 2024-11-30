<?php 
# Libreria de funciones	
require("lib/self_general.php");



 #Recibimos parametros 
 $fl_envio_correo=RecibeParametroNumerico('fl_envio_correo');
 $cl_parentesco=RecibeParametroNumerico('cl_parentesco');
 $ds_fname=RecibeParametroHTML('fname_parentesco');
 $ds_lname=RecibeParametroHTML('lname_parentesco');
 $ds_email_alumno=RecibeParametroHTML('ds_email_estudiante');
 $ds_email=RecibeParametroHTML('email_parentesco');
 $fg_resend=RecibeParametroNumerico('fg_resend');
 $fname_alumno=RecibeParametroHTML('fname_alumno');
 $lname_alumno=RecibeParametroHTML('lname_alumno');
 if($fg_resend==1){#el dato vien del login y tien un resend.
 
     $fl_usuario=RecibeParametroNumerico('fl_usuario');
     
     #Recupermaos datos faltantes.
     $Query="SELECT ds_fname, ds_lname,cl_parentesco,ds_email FROM k_responsable_alumno WHERE fl_envio_correo=$fl_envio_correo AND fl_usuario=$fl_usuario ";
     $riu=RecuperaValor($Query);
     $ds_fname=str_texto($riu[0]);
     $ds_lname=str_texto($riu[1]);
     $cl_parentesco=$riu[2];
     $ds_email=str_texto($riu[3]);
 
 
 } 
 
 #Recupermaos el parentesco.
 $Query ="SELECT nb_parentesco FROM c_parentesco WHERE cl_parentesco=$cl_parentesco ";
 $row=RecuperaValor($Query);
 $nb_parentesco=$row[0];
 
 
 
 
 
 #Generamos una llave de autorizacion para dar permisos del tutor.
 $str = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890";
 for($i = 0; $i < 40; $i++)
     $ds_cve .= substr($str, rand(0,62), 1);
 $ds_cve .= date("Ymd").$no_envio;
 
 #subtaremos 10 caracteres apartir del ultimo digito yle asignamos la fecha actual en formato año/mes/dia/no_confirmacion/no_registro
 $no_codigo_autorizacion = substr("$ds_cve", -30, 30);
 
 #Al final se le pasa como aparametro 1 que significara que esta autorizando e acceso.
 $dominio_campus = ObtenConfiguracion(116)."/fame/confirmation.php?r=".$ds_cve."&a=1";
 
 #Atraves del fl_envio_correo se obtiene usuario y el instituto al que pertenece.
 $Queryi="SELECT b.ds_instituto FROM k_envio_email_reg_selfp a JOIN c_instituto b ON b.fl_instituto=a.fl_invitado_por_instituto WHERE a.fl_envio_correo=$fl_envio_correo ";
 $rowi=RecuperaValor($Queryi);
 $nb_instituto=$rowi[0];
 
         #Variables de email
		 #se genera el cuerpo del documento de email$fl_usuario(reducir licencias)
		 $ds_encabezado = genera_documento_sp($fl_usuario, 1,114,'',$fl_envio_correo);
		 $ds_cuerpo = genera_documento_sp($fl_usuario, 2,114,'',$fl_envio_correo);
		 $ds_pie = genera_documento_sp($fl_usuario,3,114,'',$fl_envio_correo);
		 $ds_contenido=$ds_encabezado.$ds_cuerpo.$ds_pie;

		$ds_contenido = str_replace("#fame_parent_relationship#", $nb_parentesco, $ds_contenido);  #relacion _alumno		
		$ds_contenido = str_replace("#fame_fname_parent#", $ds_fname , $ds_contenido);  #nombre papa
		$ds_contenido = str_replace("#fame_lname_parent#", $ds_lname , $ds_contenido);  #apellido papa

        $ds_contenido = str_replace("#fame_fname#", $fname_alumno , $ds_contenido);  #nombre alumno
		$ds_contenido = str_replace("#fame_lname#", $lname_alumno , $ds_contenido);  #apellido alumno
        $ds_contenido = str_replace("#nb_instituto#", $nb_instituto , $ds_contenido);  #nombre instituto.
        $ds_contenido = str_replace("#fame_link_authorization#", $dominio_campus, $ds_contenido);  #apellido
        
        
        

		$ds_titulo=ObtenEtiqueta(1654);#etiqueta de asunto del mensjae para el anunciante reduce my contrcat 
		$ds_email_de_quien_envia_mensaje=ObtenConfiguracion(107);  
		$ds_email_destinatario=$ds_email;
		$nb_nombre_dos=ObtenEtiqueta(949);#nombre de quien envia el mensaje         
		$bcc=ObtenConfiguracion(107);
		$message  = $ds_contenido;
		$message = utf8_decode(str_ascii(str_uso_normal($message)));
 
 
 
		 #Verificamos si existe un registro 
		 $Query="SELECT ds_email_alumno FROM k_responsable_alumno WHERE fl_envio_correo=$fl_envio_correo ";
		 $row=RecuperaValor($Query);
		 $existe_email=$row[0];

         $copy_send_email=ObtenConfiguracion(131);
 if(empty($existe_email)){
 
   
 
	
					#Envia email de notificcion al papa
			        $mail = EnviaMailHTML($nb_nombre_dos, $ds_email_de_quien_envia_mensaje, $ds_email_destinatario, $ds_titulo, $message, $bcc);
	  
		
		
					
					if($copy_send_email){
						
					$bcc=$copy_send_email;
					#Se cuelve enviar la invitacion desde otro correo
					$mail = EnviaMailHTML($nb_nombre_dos, $copy_send_email, $ds_email_destinatario, $ds_titulo, $message, $copy_send_email);
					}
 
		
		
		if($mail){
		    #Guarda registro 
			$Query="INSERT INTO k_responsable_alumno (fl_usuario, cl_parentesco,ds_fname,ds_lname,ds_email,ds_email_alumno,no_codigo_autorizacion,fg_autorizado,fl_envio_correo)";
			$Query.="VALUES($fl_envio_correo, $cl_parentesco,'$ds_fname','$ds_lname','$ds_email','$ds_email_alumno','$no_codigo_autorizacion','0',$fl_envio_correo)";
			EjecutaQuery($Query);
		}
 
 
 }else{
 
           $Query="UPDATE  k_responsable_alumno SET  ds_fname='$ds_fname',ds_lname='$ds_lname',ds_email='$ds_email',no_codigo_autorizacion='$no_codigo_autorizacion',fg_autorizado='0' WHERE  fl_envio_correo=$fl_envio_correo  ";
           EjecutaQuery($Query);
	  
	  
	        $mail = EnviaMailHTML($nb_nombre_dos, $ds_email_de_quien_envia_mensaje, $ds_email_destinatario, $ds_titulo, $message, $bcc);
 
	
			if($copy_send_email){
				
			$bcc=$copy_send_email;
			#Se cuelve enviar la invitacion desde otro correo
			$mail = EnviaMailHTML($nb_nombre_dos, $copy_send_email, $ds_email_destinatario, $ds_titulo, $message, $copy_send_email);
			}
 
 
 
 }
          if($fg_resend==1){
              
           #Prsnta forma existo
              if($mail){
                  $forma_mensaje="<div class=\"alert alert-info alert-dismissable\"><a href=\"#\" class=\"close\" data-dismiss=\"alert\" aria-label=\"close\">&times;</a><i class=\"fa fa-check-circle-o\" aria-hidden=\"true\"></i> ".ObtenEtiqueta(2117)." </span></div>";
                 echo"<script>$('#envia_resend').html('$forma_mensaje');</script>";
              
              }
              
              
          }
 
 
?>
