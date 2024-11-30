<?php
  
  # Libreria de funciones
  require("../lib/self_general.php");

  # Obtenemos el usuario y el instituto
  $fl_usuario = ValidaSesion(False,0, True);
  $fl_instituto = ObtenInstituto($fl_usuario);
  $perfil_usuario = ObtenPerfilUsuario($fl_usuario);

  $first_name=RecibeParametroHTML('fname');
  $last_name=RecibeParametroHTML('lname');
  $ds_email_destinatario=RecibeParametroHTML('email');
  
  #Recuperamos los datos de quien, esta enviando el email.
  $Query="SELECT ds_nombres, ds_apaterno FROM c_usuario WHERE fl_usuario=$fl_usuario ";
  $rol=RecuperaValor($Query);
  $fname_invitador=str_texto($rol[0]);
  $lname_invitador=str_texto($rol[1]);


  $ruta_avatar=ObtenAvatarUsuario($fl_usuario);


  #Verificamos si la cuenta de correo ya esta registrada en FAME, entonces , ya no se le enviara el correo y mostrar mensaje de que la cuenta ya esta regitrada.
  $Query="SELECT COUNT(*) FROM c_usuario WHERE ds_email='$ds_email_destinatario' AND fl_perfil_sp IS NOT NULL ";
  $row=RecuperaValor($Query);
  $ds_email_registrado=$row[0];

  if(!empty($ds_email_registrado)){
	  
	   #Sale el error que ya eiste el email.
	   $result['fg_email_repetido'] = 1;
	     
  }else{ #Procede a enviar la invitacion.
	  
	  
	  $ds_email_de_quien_envia_mensaje=ObtenConfiguracion(107);
	  $bcc = "terrylonz@gmail.com";


	  #Recuperamos el ultimo id del correo para saber y llevar su bitacora.
      $Query="SELECT MAX(fl_envio_correo) AS fl_envio_correo FROM k_envio_email_reg_selfp ";
      $row=RecuperaValor($Query);
      $no_envio=$row[0];
      $no_envio=$no_envio + 1 ;
                    
      # Genera una nueva clave para la liga de acceso al contrato
      $str = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890";
      $ds_cve="";
      for($i = 0; $i < 40; $i++)
         $ds_cve .= substr($str, rand(0,62), 1);
      $ds_cve .= date("Ymd").$no_envio;
                    
      #subtaremos 10 caracteres apartir del ultimo digito yle asignamos la fecha actual en formato año/mes/dia/no_confirmacion/no_registro
      $no_codigo_confirmacion = substr("$ds_cve", -30, 30);
	  

	  # Obtenmos el template
	  $ds_header = genera_documento_sp('', 1, 170);
	  $ds_body = genera_documento_sp('', 2, 170);
	  $ds_footer = genera_documento_sp('', 3, 170);
	  $ds_mensaje=$ds_header.$ds_body.$ds_footer;
    
	  $dominio_campus = ObtenConfiguracion(116);
      $src_redireccion=$dominio_campus."/fame/confirmation.php?r=".$ds_cve."&ff=1";#bueno
	  $mn_descuento_cupon=ObtenConfiguracion(136);
	  $no_dias_cupon_valido=ObtenConfiguracion(137);
	  $ds_cupon=ObtenConfiguracion(138);
	  
	  #Calculamos la fecha de expiracion del cupon.
	  $fe_actual=ObtenerFechaActual();
	  $fe_expiracion_cupon=strtotime('+'.$no_dias_cupon_valido.' day',strtotime($fe_actual));
      $fe_expiracion_cupon= date('Y-m-d',$fe_expiracion_cupon);
	  
	  
	  #Daos un formato ala fecha de expiracion.
	  $fe_expiracion=GeneraFormatoFecha($fe_expiracion_cupon);
	  
	  
	
	  $ds_mensaje = str_replace("#fame_fname#", $first_name, $ds_mensaje);
	  $ds_mensaje = str_replace("#fame_lname#", $last_name, $ds_mensaje);
	  $ds_mensaje = str_replace("#fame_fname_friends#", $fname_invitador, $ds_mensaje);
	  $ds_mensaje = str_replace("#fame_lname_friends#", $lname_invitador, $ds_mensaje);
	  $ds_mensaje = str_replace("#fame_coupon_amount#", $mn_descuento_cupon, $ds_mensaje);
	  $ds_mensaje = str_replace("#fame_external_invite_coupon_expirity_date#", $fe_expiracion, $ds_mensaje);
	  $ds_mensaje = str_replace("#fame_external_invite_coupon_code#", $ds_cupon, $ds_mensaje);
	  $ds_mensaje = str_replace("#fame_ds_avatar_ori#", $dominio_campus.$ruta_avatar, $ds_mensaje);
	  $ds_mensaje = str_replace("#fame_link#", $src_redireccion, $ds_mensaje);
	  
	 
	  # Nombre del template
	  $Query0 = "SELECT nb_template FROM k_template_doc WHERE fl_template=155 AND fg_activo='1'";
	  $template = RecuperaValor($Query0);
	  $nb_template = str_uso_normal($template[0]);
      # Este email es necesario
      $from = ObtenConfiguracion(107);#de donde sale el email.
	  
      #El subject sera el nombre de quien envia,el email
      $nb_template=$fname_invitador." ".$lname_invitador." ".$nb_template;

	  
	  # Enviamos el correo al usuario dependiendo de la accion
      $send_email=EnviaMailHTML($from, $from, $ds_email_destinatario, $nb_template, $ds_mensaje);
	  
	  if($send_email=1){
		  
		  EjecutaQuery("DELETE FROM k_envio_email_reg_selfp WHERE ds_email='$ds_email_destinatario' ");
		  EjecutaQuery("DELETE FROM k_friends_invitation WHERE ds_email='$ds_email_destinatario' ");

		  
	     #Si efectivamenete se envio el email entonces se guarda la bitacora de envio
         $Query = "INSERT INTO k_friends_invitation (ds_first_name,ds_last_name,ds_email,fg_confirmado,fl_usu_invita,no_registro,fe_alta,fe_ultmod,fe_expiracion,ds_cupon)";
         $Query .= "values('$first_name','$last_name','$ds_email_destinatario',0,$fl_usuario,'$no_codigo_confirmacion',CURRENT_TIMESTAMP,CURRENT_TIMESTAMP,'$fe_expiracion_cupon','$ds_cupon')";
         $fl_envio = EjecutaInsert($Query);
	   	   
		 #Si efectivamenete se envio el email entonces se guarda la bitacora de envio
		 $Query="INSERT INTO k_envio_email_reg_selfp (ds_first_name,ds_last_name,ds_email,no_registro,fg_confirmado,fg_tipo_registro,fl_invitado_por_instituto,fe_alta,fe_ultmod,fl_usu_invita,fg_desbloquear_curso,fl_friends_invitation,fg_feed,fe_expiracion,ds_cupon)"; 
		 $Query.="values('$first_name','$last_name','$ds_email_destinatario','$no_codigo_confirmacion','0','S',4,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP,$fl_usuario,'1',$fl_envio,'1','$fe_expiracion_cupon','$ds_cupon')";
		 $fl_envio_=EjecutaInsert($Query);

	
		  
		  
	  }
	 
	  
	  
	  $result['fg_email_repetido'] = 0;
	  $result['fg_enviado']=1;
	  
  }

 echo json_encode((Object) $result);



  
  
 
  
 ?>