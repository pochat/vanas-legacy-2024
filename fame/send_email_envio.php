<?php

  # Libreria de funciones
  require_once("../lib/sp_general.inc.php");

  # Recibe  datos de envio e email.
  $ds_first_name= RecibeParametroHTML('fname');
  $ds_last_name = RecibeParametroHTML('lname');
  $ds_email = RecibeParametroHTML('email');
  
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
 
 $fg_tipo_registro="1";//selecciono el primer check box
  
 $fg_aceptar = RecibeParametroBinario('fg_aceptar');

 
 
 #Verificamos si la cuenta de correo ya esta activo entonces , ya no se le enviara el correo y mostrar mensaje de que la cuenta ya esta regitrada.
 $Query="SELECT COUNT(1),fg_system FROM c_usuario WHERE  ds_email='$ds_email' AND fg_system='F' ";
 $row=RecuperaValor($Query);
 $ds_email_registrado=$row[0];
 $fg_system=$row['fg_system'];
 
 if($fg_system != 'F'){
 
  
  
      #Revuperamos el ultimo id del correo para saber y llevar su bitacora.
      $Query="SELECT MAX(fl_envio_correo) AS fl_envio_correo FROM k_envio_email_reg_selfp ";
      $row=RecuperaValor($Query);
      $no_envio=$row[0];
      $no_envio=$no_envio + 1 ;
  
      # Genera una nueva clave para la liga de acceso al contrato
      $str = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890";
      for($i = 0; $i < 40; $i++)
          $ds_cve .= substr($str, rand(0,62), 1);
      $ds_cve .= date("Ymd").$no_envio;
  
      #subtaremos 10 caracteres apartir del ultimo digito yle asignamos la fecha actual en formato año/mes/dia/no_confirmacion/no_registro
      $no_codigo_confirmacion = substr("$ds_cve", -30, 30);


      #Identificamos al instituto.
      $Query="SELECT fl_instituto,fl_usuario_sp FROM c_instituto WHERE ds_instituto='$ds_name_school'  ";
      $row=RecuperaValor($Query);
      $fl_instituto=$row['fl_instituto'];
      $fl_usuario_sp=$row['fl_usuario_sp'];
      
      
      #Recupermao el admin del Instituto.
      $Query="SELECT ds_nombres,ds_apaterno FROM c_usuario WHERE fl_usuario=$fl_usuario_sp ";
      $row=RecuperaValor($Query);
      $ds_fname_invitador=str_texto($row[0]);
      $ds_lname_invitador=str_texto($row[1]);
      
      
       
      
      
      /*
    MJD  El envio de correeos a teache es libre
      #verificamos cuantos correos ha enviado este instituto SIN CONTAR EL ADMIN.
      $Query="SELECT COUNT(*) FROM k_envio_email_reg_selfp WHERE fl_invitado_por_instituto=$fl_instituto AND fg_tipo_registro<>'A'  AND fg_tipo_registro<>'T' AND fg_confirmado='1'  ";
      $row=RecuperaValor($Query);
      $no_correos_enviados=$row[0];

*/





      #Obtenemos el no.de correos permitidos para enviar (se suma 1 por que se cuanta el administardor del intituto) 
 //     $no_correos_permitidos=ObtenConfiguracion(102) ;
 //     if($no_correos_enviados <= $no_correos_permitidos) {#Solamente envia correos si esta dentro del rango permitido. 

	         #se genera el cuerpo del documento de email
	         $ds_encabezado = genera_documentoSP($clave, 1, True,'','',100,$ds_cve,$ds_first_name,$ds_last_name);
	         $ds_cuerpo = genera_documentoSP($clave, 2, True,'','',100,$ds_cve,$ds_first_name,$ds_last_name);
	         $ds_pie = genera_documentoSP($clave, 3, True,'','',100,$ds_cve,$ds_first_name,$ds_last_name);
 
	         $template_email=$ds_encabezado.$ds_cuerpo;
	         $template_email.=$ds_pie;
	         $ds_contenido=$template_email;
 
              $ds_contenido = str_replace("#fame_fname_invited#", $ds_fname_invitador, $ds_contenido); # first name a quein se le envia el correo
              $ds_contenido = str_replace("#fame_lname_invited#", $ds_lname_invitador, $ds_contenido);  #bont link redireccion 
      
             
      	     $nombre_quien_escribe=$ds_first_name." ".$ds_last_name;
  
  

        $ds_email_de_quien_envia_mensaje=ObtenConfiguracion(4);  
        $ds_email_destinatario=$ds_email;
        $nb_nombre_dos=ObtenEtiqueta(949);#nombre de quien envia el mensaje

        # Inicializa variables de ambiente para envio de correo
        ini_set("SMTP", MAIL_SERVER);
        ini_set("smtp_port", MAIL_PORT);
        ini_set("sendmail_from", MAIL_FROM); 
        $message  = $ds_contenido;
        $message = utf8_decode(str_ascii(str_uso_normal($message)));
    
        $bcc = ObtenConfiguracion(107);
        $ds_titulo=ObtenEtiqueta(950);#etiqueta de asunto del mensjae para el anunciante
        $mail = EnviaMailHTML($nb_nombre_dos, $ds_email_de_quien_envia_mensaje, $ds_email_destinatario, $ds_titulo, $message, $bcc);
    



//	}



          if($mail)
          {
              #indicamos que el tipo de registro es Teacher, ya que en este modiulo solo se puede enviar invitacion a teachher(por el momento)
              $fg_tipo_registro="T";
      
              #recuperamos quien esta enviando la invitacion
              $Query="SELECT fl_instituto FROM c_instituto WHERE ds_instituto='$ds_name_school' ";
              $row=RecuperaValor($Query);
              $fl_instituto=$row[0];
      
              if(empty($fl_instituto))
                  $fl_instituto=NULL;
      
		     






			  #Verificamos si anteriormente se le habia mandado un correo, si ya esixte este correo entonces borramos la bitacora de envio de correo .
              $Query="SELECT fl_envio_correo FROM k_envio_email_reg_selfp WHERE ds_email='$ds_email' AND fg_confirmado='0'  ";
              $row=RecuperaValor($Query);
              $fl_ya_se_envio_email=$row[0];  
              
              if ($fl_ya_se_envio_email){
                  
                  #eliminamos el correo anteiror enviado 
                  $Query="DELETE FROM k_envio_email_reg_selfp WHERE fl_envio_correo=$fl_ya_se_envio_email ";
                  EjecutaQuery($Query);   
                  
                  
                  
              }
      
              #Si efectivamenete se envio el email entonces se guarda la bitacora de envio
              $Query="INSERT INTO k_envio_email_reg_selfp (ds_first_name,ds_last_name,ds_email,no_registro,fg_confirmado, ";
	          $Query.="nb_name_school,fl_pais,ds_codigo_telefono,ds_numero_telefono,fg_tipo_registro,fl_invitado_por_instituto, ";
	          $Query.=" fe_alta,fe_ultmod)"; 
              $Query.="values('$ds_first_name','$ds_last_name','$ds_email','$no_codigo_confirmacion','0', ";
	          $Query.="'$ds_name_school',$fl_country,'$ds_codigo_telefono','$ds_numero_telefono','$fg_tipo_registro',$fl_instituto, ";
	          $Query.=" CURRENT_TIMESTAMP,CURRENT_TIMESTAMP )";
              $fl_envio_correo=EjecutaInsert($Query);
      

          }
 
          
 }
  
  
 
  

  
  
  
  
  # Funcion para generar EMAIL DE INVITACION
function genera_documentoSP($clave, $opc, $correo=False, $firma=False, $no_contrato=1,$fl_template,$ds_cve,$ds_firts_name,$ds_last_name) {

    
    
    
    
    $texto_boton=ObtenEtiqueta(920);
    $dominio_campus = ObtenConfiguracion(116);
   
    $src_redireccion=$dominio_campus."/fame/confirmation.php?r=".$ds_cve;#bueno
    
    














    
    # Recupera datos del template del documento
    switch($opc)
    {
        case 1: $campo = "ds_encabezado"; break;
        case 2: $campo = "ds_cuerpo"; break;
        case 3: $campo = "ds_pie"; break;
        case 4: $campo = "nb_template"; break;
    }
    $Query  = "SELECT $campo FROM k_template_doc WHERE fl_template=$fl_template ";
    $row = RecuperaValor($Query);
    $cadena = str_uso_normal($row[0]);
    
    $nombre_=$ds_firts_name." ".$ds_last_name;
    
    # Sustituye variables con datos del alumno
    $cadena = str_replace("#sp_invitation_name#", "".$nombre_, $cadena); # first name a quein se le envia el correo
    $cadena = str_replace("#sp_invitation_link#", "".$src_redireccion, $cadena);  #bont link redireccion 
    

    
    return ($cadena);
}



?>