<?php

  # Libreria de funciones
  require("lib/sp_general.inc.php");
  require("lib/sp_session.inc.php");
  require("lib/sp_forms.inc.php");

  # Recibe parametros
  $clave = RecibeParametroHTML('clave');
  $cl_sesion = RecibeParametroHTML('cl_sesion');
  $fg_error = 0;
  $opc_pago = RecibeParametroHTML('opc_pago');
  $fl_programa = RecibeParametroNumerico('fl_programa');


  #Recuperamos info del programa.
  $Query="SELECT fl_periodo FROM  k_ses_app_frm_1 WHERE cl_sesion='$cl_sesion' ";
  $row=RecuperaValor($Query);
  $fl_periodo=$row['fl_periodo'];

  $Query="SELECT fl_sesion,fl_pais_campus FROM c_sesion WHERE cl_sesion='$cl_sesion' ";
  $row=RecuperaValor($Query);
  $fl_sesion=$row['fl_sesion'];
  $fl_pais_campus=$row['fl_pais_campus'];

  #Recuperamos Nombre del programa y fecha de inicio
  $Query="SELECT nb_programa FROM c_programa where fl_programa=$fl_programa ";
  $row=RecuperaValor($Query);
  $nb_programa=$row['nb_programa'];

  #Recuperamos el periodo.
  $Query="SELECT nb_periodo FROM c_periodo WHERE fl_periodo=$fl_periodo  ";
  $row=RecuperaValor($Query);
  $nb_periodo=$row['nb_periodo'];


  # Obtenemos la frecuencia que haya seleccionado
  switch($opc_pago){
    CASE 1: $frecuencia = 'ds_a_freq'; break;
    CASE 2: $frecuencia = 'ds_b_freq'; break;
    CASE 3: $frecuencia = 'ds_c_freq'; break;
    CASE 4: $frecuencia = 'ds_d_freq'; break;
  }
  if($fl_pais_campus==226){
      $row = RecuperaValor("SELECT $frecuencia FROM k_programa_costos_pais WHERE fl_programa=$fl_programa and fl_pais=$fl_pais_campus ");
  }else{
    $row = RecuperaValor("SELECT $frecuencia FROM k_programa_costos WHERE fl_programa=$fl_programa ");
  }

  $ds_frecuencia =$row[0];
  $conf1 = RecibeParametroHTML('conf1');
  $conf2 = RecibeParametroHTML('conf2');
  $conf3 = RecibeParametroHTML('conf3');
  $conf4 = RecibeParametroHTML('conf4');
  $ds_firma = RecibeParametroHTML('ds_firma');
  $ds_firma_rep_legal = RecibeParametroHTML('ds_firma_rep_legal');
  $rep_legal = RecibeParametroHTML('rep_legal');
  $cl_metodo_pago = RecibeParametroNumerico('cl_metodo_pago');
  $ds_metodo_otro = RecibeParametroHTML('ds_metodo_otro');

  $no_contrato = substr($clave, 8, 1);
  //$fl_sesion = substr($clave, 19, strlen($clave)-19);

  # Valida campos obligatorios
  if(empty($conf1))
    $conf1_err = ERR_REQUERIDO;
  if(empty($conf2))
    $conf2_err = ERR_REQUERIDO;
  if(empty($conf3))
    $conf3_err = ERR_REQUERIDO;
  if(empty($conf4))
    $conf4_err = ERR_REQUERIDO;
  if(empty($ds_firma))
    $ds_firma_err = ERR_REQUERIDO;
  if(empty($ds_firma_rep_legal) && !empty($rep_legal))
    $ds_firma_rep_legal_err = ERR_REQUERIDO;
  # En el primer contrato muestra error si no eligio un metodo de pago
  if($no_contrato==1){
    if(empty($cl_metodo_pago) || ($cl_metodo_pago==5 AND empty($ds_metodo_otro)))
      $method_err = 218;
  }

  # Valida que la firma del alumno coincida con el nombre
  $Query  = "SELECT ds_fname, ds_mname, ds_lname ";
  $Query .= "FROM k_ses_app_frm_1 ";
  $Query .= "WHERE cl_sesion='$cl_sesion'";
  $row = RecuperaValor($Query);
  if(!empty($row[1]))
    $ds_nombre = strtoupper(str_texto(trim($row[0])).' '.str_texto(trim($row[1])).' '.str_texto(trim($row[2])));
  else
    $ds_nombre = strtoupper(str_texto(trim($row[0])).' '.str_texto(trim($row[2])));
  $ds_firma_val = strtoupper(str_texto(trim($ds_firma)));
  $ds_firma_rep_legal_val = strtoupper(str_texto(trim($ds_firma_rep_legal)));
  if($ds_firma_val != $ds_nombre)
    $ds_firma_err = 225;
  if($ds_firma_val == $ds_firma_rep_legal_val)
    $ds_firma_err = 226;

  # Regresa a la forma con error
  $fg_error = $conf1_err || $conf2_err || $conf3_err || $conf4_err || $ds_firma_err || $ds_firma_rep_legal_err || $method_err;

  if($fg_error) {
    echo "<html><body><form name='datos' method='post' action='contract_frm.php'>\n";
    Forma_CampoOculto('clave' , $clave);
    Forma_CampoOculto('fl_sesion' , $fl_sesion);
    Forma_CampoOculto('fg_error' , $fg_error);
    Forma_CampoOculto('opc_pago' , $opc_pago);
    Forma_CampoOculto('conf1' , $conf1);
    Forma_CampoOculto('conf1_err' , $conf1_err);
    Forma_CampoOculto('conf2' , $conf2);
    Forma_CampoOculto('conf2_err' , $conf2_err);
    Forma_CampoOculto('conf3' , $conf3);
    Forma_CampoOculto('conf3_err' , $conf3_err);
    Forma_CampoOculto('conf4' , $conf4);
    Forma_CampoOculto('conf4_err' , $conf4_err);
    Forma_CampoOculto('ds_firma' , $ds_firma);
    Forma_CampoOculto('ds_firma_err' , $ds_firma_err);
    Forma_CampoOculto('ds_firma_rep_legal' , $ds_firma_rep_legal);
    Forma_CampoOculto('ds_firma_rep_legal_err' , $ds_firma_rep_legal_err);
    Forma_CampoOculto('cl_metodo_pago',$cl_metodo_pago);
    Forma_CampoOculto('ds_metodo_otro',$ds_metodo_otro);
    Forma_CampoOculto('method_err', $method_err);
    Forma_CampoOculto('no_contrato', $no_contrato);
    echo "\n</form>
<script>
  document.datos.submit();
</script></body></html>";
    exit;
  }

  # Actualiza la forma de pago para todos los contratos relacionados al estudiante
  $Query  = "UPDATE k_app_contrato ";
  $Query .= "SET fg_opcion_pago=$opc_pago, ds_frecuencia='$ds_frecuencia' ";
  # Guardamos el metodo de pago
  $Query .= ", cl_metodo_pago='$cl_metodo_pago', ds_metodo_otro='$ds_metodo_otro' ";
  $Query .= "WHERE cl_sesion='$cl_sesion'";
  EjecutaQuery($Query);

  # Guarda la fecha, firmas del estudiante y representante legal y contenido del contrato
  $Query  = "UPDATE k_app_contrato ";
  $Query .= "SET fe_firma=CURRENT_TIMESTAMP, ds_firma_alumno='$ds_firma', ds_firma_padre='$ds_firma_rep_legal_val' ";
  $Query .= "WHERE cl_sesion='$cl_sesion' ";
  $Query .= "AND no_contrato=$no_contrato ";
  EjecutaQuery($Query);

  if($fl_pais_campus==226){
      $ds_encabezado = htmlentities(genera_documento($fl_sesion, 1, False, False, 201));
      $ds_cuerpo = htmlentities(genera_documento($fl_sesion, 2, False, False, 201));
      $ds_pie = htmlentities(genera_documento($fl_sesion, 3, False, False, 201));
  }else{

      $ds_encabezado = htmlentities(genera_documento($fl_sesion, 1, False, False, $no_contrato));
      $ds_cuerpo = htmlentities(genera_documento($fl_sesion, 2, False, False, $no_contrato));
      $ds_pie = htmlentities(genera_documento($fl_sesion, 3, False, False, $no_contrato));
  }
  # Guarda el contenido del contrato
  $Query  = "UPDATE k_app_contrato ";
  $Query .= "SET ds_header='$ds_encabezado', ds_contrato='$ds_cuerpo', ds_footer='$ds_pie' ";
  $Query .= "WHERE cl_sesion='$cl_sesion' ";
  $Query .= "AND no_contrato=$no_contrato ";
  EjecutaQuery($Query);



  # Prepara variables de ambiente para envio de correo
  $app_frm_email = MAIL_FROM;
  $admin = ObtenConfiguracion(20);
  ini_set("SMTP", MAIL_SERVER);
  ini_set("smtp_port", MAIL_PORT);
  ini_set("sendmail_from", MAIL_FROM);

  # Envia correo de confirmacion al Administrador
  $subject = ObtenEtiqueta(598);
  $message  = "Contract accepted by:\n <br>";
  $message .= ObtenEtiqueta(117).": $row[0]\n <br>";
  $message .= ObtenEtiqueta(119).": $row[1]\n <br>";
  $message .= ObtenEtiqueta(118).": $row[2]\n <br>";
  $message .= ObtenEtiqueta(512).": $nb_programa\n <br>";
  $message .= ObtenEtiqueta(60).": $nb_periodo\n <br>";
  $message .= "\n";
  $message .= "\n\n";
  $message = utf8_encode(str_ascii($message));
  $headers  = "From: $app_frm_email\r\nReply-To: $app_frm_email\r\n";
  #$mail_sent = mail($admin, $subject, $message, $headers);
  $mail_sent = Mailer($admin,$subject,$message,'','','',$app_frm_email);

  # Redirige al contrato
  header("Location: contract_frm.php?success=1&c=$clave");
?>