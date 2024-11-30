<?php

 
    # Libreria de funciones	
	require("lib/self_general.php");
  
  # Recibe  datos
  $fl_instituto= RecibeParametroHTML('fl_instituto');
  $fl_usuario = RecibeParametroHTML('fl_usuario');
 

   #se genera el cuerpo del documento de email
   $ds_encabezado = genera_documento_sp($fl_usuario, 1,126,'');
   $ds_cuerpo = genera_documento_sp($fl_usuario, 2,126,'');
   $ds_pie = genera_documento_sp($fl_usuario, 3,126,'');
 
   $template_email=$ds_encabezado.$ds_cuerpo.$ds_pie;
   $ds_contenido=$template_email;

   
   
   $Query="SELECT fl_usuario_sp FROM c_instituto WHERE fl_instituto=$fl_instituto ";
   $row=RecuperaValor($Query);
   $fl_usuario_adm=$row[0];

   
   #Recupermaos el email al que vamosenviar el correo.
   $Query="SELECT U.ds_email FROM c_usuario U WHERE U.fl_usuario =$fl_usuario_adm ";
	$row=RecuperaValor($Query);
	$ds_email=$row[0];

	$ds_titulo=ObtenEtiqueta(1756);#etiqueta de asunto del mensjae para el anunciante reduce my contrcat 
	$ds_email_de_quien_envia_mensaje=ObtenConfiguracion(4);
	//$ds_email="mjimenez@loomtek.com.mx";	
	$ds_email_destinatario=$ds_email;
	$nb_nombre_dos=ObtenEtiqueta(949);#nombre de quien envia el mensaje         
	$bcc=ObtenConfiguracion(107);
	$message  = $ds_contenido;
	$message = utf8_decode(str_ascii(str_uso_normal($message)));
	$mail = EnviaMailHTML($nb_nombre_dos, $ds_email_de_quien_envia_mensaje, $ds_email_destinatario, $ds_titulo, $message, $bcc);

   
  //$mail=true;
  
  if($mail)
  {
      echo" <h5 > <i class='fa fa-check-circle  text-success success-icon-shadow'></i> <strong>".ObtenEtiqueta(1757)." </strong></h5>
	  <a class='btn btn-success btn-sm hidden' href='".PAGINA_INICIO."' id='redirigir'><i class='fa fa-upload'></i> redirige home</a>";
  
     echo "
    <script>
      $('#btn_canel').addClass('hidden');
	  $('#btn_evio_email').addClass('hidden');
	  $('#msg_final').removeClass('hidden');
	  
	  setTimeout(function(){ 
	document.getElementById('redirigir').click();//clic au   
		}, 4000);
	  
     </script>";
  
  
  }
 
  

?>