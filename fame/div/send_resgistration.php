<?php

  # Libreria de funciones
  require_once("../../lib/sp_general.inc.php");
  # clases.
  require(CLASSES.'EnumTipoRegistro.php');

  # Variable initialization to avoid errors
  $ds_lname_invitador=NULL;
  $clave=NULL;
  
  # Recibe  datos
  $ds_first_name= RecibeParametroHTML('ds_firts_name');
  $ds_last_name = RecibeParametroHTML('ds_last_name');
  $ds_email = RecibeParametroHTML('ds_email');
  $fg_aceptar = RecibeParametroBinario('fg_aceptar');
  $fg_student=RecibeParametroBinario('fg_student');#Para indicar que proviene de un registro directo del estudiante new_registration.php
  $fl_pais=RecibeParametroNumerico('fl_pais');
  
  #Nos indica que el proceso viene desdel feed.  
  $fl_envio=RecibeParametroNumerico('fl_envio');
  
  #Verificamos si la cuenta de correo ya esta registrada, entonces , ya no se le enviara el correo y mostrar mensaje de que la cuenta ya esta regitrada.
  $Query="SELECT COUNT(1) FROM c_usuario WHERE ds_email='$ds_email' AND fg_system='F' ";
  $row=RecuperaValor($Query);
  $ds_email_registrado=$row[0];

  if ($ds_email_registrado==0) {

      #Revuperamos el ultimo id del correo para saber y llevar su bitacora.
      $Query="SELECT MAX(fl_envio_correo) AS fl_envio_correo FROM k_envio_email_reg_selfp ";
      $row=RecuperaValor($Query);
      $no_envio=$row[0];
      $no_envio=$no_envio + 1 ;
  
      # Genera una nueva clave para la liga de acceso al contrato
      $str = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890";
      $ds_cve=NULL;
      for($i = 0; $i < 40; $i++)
          $ds_cve .= substr($str, rand(0,62), 1);
      $ds_cve .= date("Ymd").$no_envio;
  
      #subtaremos 10 caracteres apartir del ultimo digito yle asignamos la fecha actual en formato año/mes/dia/no_confirmacion/no_registro
      $no_codigo_confirmacion = substr("$ds_cve", -30, 30);

       #se genera el cuerpo del documento de email
       $ds_encabezado = genera_documentoSP($clave, 1, True,'','',100,$ds_cve,$ds_first_name,$ds_last_name);
       $ds_cuerpo = genera_documentoSP($clave, 2, True,'','',100,$ds_cve,$ds_first_name,$ds_last_name);
       $ds_pie = genera_documentoSP($clave, 3, True,'','',100,$ds_cve,$ds_first_name,$ds_last_name);
 
       $template_email=$ds_encabezado.$ds_cuerpo;
       $template_email.=$ds_pie;
       $ds_contenido=$template_email;

       $invitado_por=ObtenEtiqueta(1766);
       $ds_instituto=ObtenEtiqueta(1766);
       if($fg_student==1){  
           $role="Student";
       }else{
           $role="Administrator";
       }
       
       $ds_contenido = str_replace("#fame_fname_invited#", $invitado_por, $ds_contenido); # first name a quein se le envia el correo
       $ds_contenido = str_replace("#fame_lname_invited#", $ds_lname_invitador, $ds_contenido);  #bont link redireccion 
       $ds_contenido = str_replace("#nb_instituto#", $ds_instituto, $ds_contenido);  #instituto quien envia correo
       $ds_contenido=str_replace("#perfil_user#",$role,$ds_contenido);
       $nombre_quien_escribe=$ds_first_name." ".$ds_last_name;
  
       $ds_email_de_quien_envia_mensaje=ObtenConfiguracion(107);  
       $ds_email_destinatario=$ds_email;
       $nb_nombre_dos=ObtenEtiqueta(949);#nombre de quien envia el mensaje

        # Inicializa variables de ambiente para envio de correo
        ini_set("SMTP", MAIL_SERVER);
        ini_set("smtp_port", MAIL_PORT);
        ini_set("sendmail_from", MAIL_FROM); 
        $message  = $ds_contenido;
        $message = utf8_decode(str_ascii(str_uso_normal($message)));
        $ds_titulo=ObtenEtiqueta(950);#etiqueta de asunto del mensjae para el anunciante
        $bcc = ObtenConfiguracion(107);
        $mail = EnviaMailHTML($nb_nombre_dos, $ds_email_de_quien_envia_mensaje, $ds_email_destinatario, $ds_titulo, $message, $bcc);

        $copy_send_email=ObtenConfiguracion(131);
        if($copy_send_email){
			
		$bcc=$copy_send_email;
        #Se cuelve enviar la invitacion desde otro correo
        $mail = EnviaMailHTML($nb_nombre_dos, $copy_send_email, $ds_email_destinatario, $ds_titulo, $message, $copy_send_email);
        }

  if(!empty($mail)) {

	  #Verificamos si anteriormente se le habia mandado un correo, si ya esixte este correo entonces borramos la bitacora de envio de correo .
      $Query="SELECT fl_envio_correo,fl_usuario FROM k_envio_email_reg_selfp WHERE ds_email='$ds_email' AND fg_confirmado='0'  ";
      $row=RecuperaValor($Query);
      $fl_ya_se_envio_email=$row[0]??NULL;
      $fl_user_existente=$row['fl_usuario']??NULL;
      
      if (!empty($fl_ya_se_envio_email)){
          
          #eliminamos el correo anteiror enviado 
          $Query="DELETE FROM k_envio_email_reg_selfp WHERE fl_envio_correo=$fl_ya_se_envio_email ";
          EjecutaQuery($Query);   
          
          EjecutaQuery("DELETE FROM c_usuario WHERE fl_usuario=$fl_user_existente ");
          
      }
	  
	  if($fg_student==1){

	      $fg_tipo_registro=EnumTipoRegistro::Student;
	      $fl_usu_invita=642;#indica que fue invitado por MarioVanas
          $fl_invitado_por="4";#Vacouver animation school FAME
          $fg_debloqueo_curso="1";
          $fl_perfil_sp=PFL_ESTUDIANTE_SELF;
		
	  }else{
	      $fg_tipo_registro=EnumTipoRegistro::Administrador;
	      $fl_usu_invita=0;
	      $fl_invitado_por="1";//Indica que fue invitado por Vancouver School
          $fl_pais=0;
          $fg_debloqueo_curso="0";
          $fl_perfil_sp=PFL_ADMINISTRADOR;
      }

      $fl_new_usuario=EjecutaInsert("insert into c_usuario (ds_nombres,ds_apaterno,ds_email, fl_perfil_sp) values('$ds_first_name','$ds_last_name','$ds_email', $fl_perfil_sp) ");
	  #Generamos su token
	  $token=sha256($fl_new_usuario);
	  EjecutaQuery("UPDATE c_usuario SET token='$token' WHERE fl_usuario=$fl_new_usuario ");

      #Si efectivamenete se envio el email entonces se guarda la bitacora de envio
      $Query="INSERT INTO k_envio_email_reg_selfp (ds_first_name,fl_pais,ds_last_name,ds_email,no_registro,fg_confirmado,fg_tipo_registro,fl_invitado_por_instituto,fe_alta,fe_ultmod,fl_usu_invita,fg_desbloquear_curso,fl_usuario)"; 
      $Query.="values('$ds_first_name',$fl_pais,'$ds_last_name','$ds_email','$no_codigo_confirmacion','0','$fg_tipo_registro','$fl_invitado_por',CURRENT_TIMESTAMP,CURRENT_TIMESTAMP,$fl_usu_invita,'$fg_debloqueo_curso',$fl_new_usuario)";
      $fl_envio_corr=EjecutaInsert($Query);

	  #Proviende desde el feed.
      if(!empty($fl_envio)){
		  
		  $Query="UPDATE k_envio_email_reg_selfp SET fl_friends_invitation=$fl_envio WHERE fl_envio_correo=$fl_envio_corr  ";
		  EjecutaQuery($Query);
	  }
    }
  }

  # Funcion para generar EMAIL DE INVITACION
function genera_documentoSP($clave, $opc, $correo=False, $firma=False, $no_contrato=1,$fl_template,$ds_cve,$ds_firts_name,$ds_last_name) {
    
    $texto_boton=ObtenEtiqueta(920);
    $dominio_campus = ObtenConfiguracion(116);
    //$dominio_campus = "localhost:64573/vanas";#pruebas
    // $src_redireccion="http://".$dominio_campus."/fame/confirmation.php?r=".$ds_cve;#bueno
    $src_redireccion= $dominio_campus."/fame/confirmation.php?r=".$ds_cve;#bueno
    /*
    $boton="<table width='100%'><tr><td align='center'><a href='".$src_redireccion."' style='background-color: #008CBA;
                border: none;
                color: white;
                padding: 15px 32px;
                text-align: center;
                text-decoration: none;
                display: inline-block;
                font-size: 16px;
                margin: 4px 2px;
                cursor: pointer;'> ".$texto_boton."</a></td></tr></table> "; 
    */

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
