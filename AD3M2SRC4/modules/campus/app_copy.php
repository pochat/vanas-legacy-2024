<?php
  # Libreria de funciones
  require '../../lib/general.inc.php';
  require SP_HOME.'/lib/sp_session.inc.php';

  # Verifica que exista una sesion valida en el cookie y la resetea
  $fl_usuario = ValidaSesion();
  
  # Recibimos parametros
  $origen = RecibeParametroHTML('origen');
  $clave = RecibeParametroNumerico('clave');
  $fl_periodo = RecibeParametroNumerico('fl_periodo'); // Periodo al que se cambia el applicante
  # Si la copia es desde students se buscara su fl_sesion y con el copiar toda la informacion necesaria
  if(!empty($origen) && $origen=="students.php"){
    $fl_programa = RecibeParametroNumerico('fl_programa');
    $row = RecuperaValor("SELECT fl_sesion FROM c_sesion WHERE cl_sesion=(SELECT cl_sesion FROM c_usuario WHERE fl_usuario=$clave)");
    $clave = $row[0];
  }
  # Recibe el numero de contratos
  $no_contratos = RecibeParametroNumerico('no_contratos');
  # Recibimos si los contratos se vana a enviar
  for($i=1;$i<=$no_contratos;$i++){
    $fg_contrato[$i] = RecibeParametroBinario('fg_contrato_'.$i.'');
  }
  $fg_app_delete = RecibeParametroBinario('fg_app_delete');
  
  # Validamos que reciba una clave
  if(!empty($clave) AND !empty($fl_periodo)){
    
    # Obtenmos la sesion del applicante
    $Query  = "SELECT a.cl_sesion, b.fg_responsable, ds_email, fl_programa, fl_periodo, ds_email, ds_cadena, ds_firma_alumno ";
    $Query .= "FROM c_sesion a, k_ses_app_frm_1 b, k_app_contrato c  ";
    $Query .= "WHERE a.cl_sesion=b.cl_sesion AND b.cl_sesion=b.cl_sesion AND a.fl_sesion=$clave AND c.no_contrato=1 ";
    $row = RecuperaValor($Query);
    $cl_sesion_bd = $row[0];
    $fg_responsable = $row[1];
    $ds_email = $row[2];
    
    # Generamos una nueva sesion
    $cl_sesion_new = SP_GeneraSesion( );
    
    # Copiamos los registros de fl_sesion a la nueva fl_sesion    
    $Query0  = "INSERT INTO c_sesion (cl_sesion,fg_stripe,fl_pais_campus,convenience_fee,fg_app_1,fg_app_2,fg_app_3,fg_app_4,fg_paypal,fg_confirmado,fg_pago,fg_inscrito,fg_archive,fe_ultmod,cl_metodo_pago, ";
    $Query0 .= "fe_pago,mn_pagado,ds_comentario,ds_cheque,ds_transaccion,mn_tax_paypal,ds_tax_provincia,fg_calificado,fl_promedio) ";
    $Query0 .= "SELECT '$cl_sesion_new',fg_stripe,fl_pais_campus,convenience_fee, fg_app_1, fg_app_2, fg_app_3, fg_app_4, fg_paypal, fg_confirmado, fg_pago,fg_inscrito, fg_archive, NOW(), cl_metodo_pago, ";
    $Query0 .= "fe_pago, mn_pagado, ds_comentario, ds_cheque, ds_transaccion, mn_tax_paypal, ds_tax_provincia,fg_calificado,fl_promedio FROM c_sesion WHERE fl_sesion = $clave ";
    $fl_sesion = EjecutaInsert($Query0);   
    
    # Copiamos los registros del k_ses_app_frm_1 al nuevo registro en la misma tabla
    $Query1  = "INSERT INTO k_ses_app_frm_1 (cl_sesion,fl_immigrations_status,ds_sin,fl_programa,fl_periodo,ds_fname,ds_mname,ds_lname,ds_number,ds_alt_number,ds_email,ds_link_to_portfolio,fg_gender,fe_birth, ";
    $Query1 .= "ds_add_number,ds_add_street,ds_add_city,ds_add_state,ds_add_zip,ds_add_country,ds_eme_fname,ds_eme_lname,ds_eme_number,ds_eme_relation,ds_eme_country, ";
    $Query1 .= "fg_ori_via,ds_ori_other,fg_ori_ref,ds_ori_ref_name,ds_ruta_foto,fg_responsable,cl_recruiter,fe_ultmod,ds_ruta_foto_permiso,fe_start_date,fe_expirity_date,nb_name_institutcion) ";
    $Query1 .= "SELECT '$cl_sesion_new',fl_immigrations_status,ds_sin, fl_programa, $fl_periodo, ds_fname, ds_mname, ds_lname, ds_number, ds_alt_number, ds_email, ds_link_to_portfolio, fg_gender, fe_birth, ";
    $Query1 .= "ds_add_number, ds_add_street, ds_add_city, ds_add_state, ds_add_zip, ds_add_country, ds_eme_fname, ds_eme_lname, ds_eme_number, ds_eme_relation, ds_eme_country, ";
    $Query1 .= "fg_ori_via, ds_ori_other, fg_ori_ref, ds_ori_ref_name, ds_ruta_foto, fg_responsable, cl_recruiter, NOW(),ds_ruta_foto_permiso,fe_start_date,fe_expirity_date,nb_name_institutcion FROM k_ses_app_frm_1 WHERE cl_sesion='$cl_sesion_bd' ";
    EjecutaQuery($Query1);
    
    
    # Si tiene una persona responsable debera insertarla con la nueva sesion
    if(!empty($fg_responsable)){
      $Query1_0  = "INSERT INTO k_presponsable (cl_sesion,ds_fname_r,ds_lname_r,ds_email_r,ds_aemail_r,ds_pnumber_r,ds_relation_r,fg_email) ";
      $Query1_0 .= "SELECT '$cl_sesion_new', ds_fname_r, ds_lname_r, ds_email_r, ds_aemail_r, ds_pnumber_r, ds_relation_r, fg_email  ";
      $Query1_0 .= "FROM k_presponsable WHERE cl_sesion='$cl_sesion_bd' ";
      EjecutaQuery($Query1_0);
    }
    
    # Consultamos cuantos contratos tiene    
    for($i=1;$i<=$no_contratos;$i++){   
      $Query  = "INSERT INTO k_app_contrato ";
      $Query .= "(cl_sesion, no_contrato,fl_class_time,fg_health_condition,ds_health_condition, mn_app_fee, mn_tuition, mn_costs, ds_costs, mn_tot_tuition, mn_tot_program, mn_a_due, mn_a_paid, mn_b_due, mn_b_paid, mn_c_due, mn_c_paid, mn_d_due, mn_d_paid, ";
      $Query .= "ds_p_name, ds_education_number, fg_international, cl_preference_1, cl_preference_2, cl_preference_3, ds_m_add_number, ds_m_add_street, ds_m_add_city, ds_m_add_state, ds_m_add_zip, ";
      $Query .= "ds_m_add_country, ds_a_email, no_weeks, ds_usual_name,fg_aplicar_international,fg_payment,mn_discount, ds_discount) ";
      $Query .= "SELECT '$cl_sesion_new', $i,fl_class_time,fg_health_condition,ds_health_condition, mn_app_fee, mn_tuition, mn_costs, ds_costs, mn_tot_tuition, mn_tot_program, mn_a_due, mn_a_paid, mn_b_due, mn_b_paid, mn_c_due, mn_c_paid, ";
      $Query .= "mn_d_due, mn_d_paid,ds_p_name, ds_education_number, fg_international, cl_preference_1, cl_preference_2, cl_preference_3, ds_m_add_number, ds_m_add_street, ds_m_add_city, ";
      $Query .= "ds_m_add_state, ds_m_add_zip, ds_m_add_country, ds_a_email, no_weeks, ds_usual_name,fg_aplicar_international,fg_payment,mn_discount, ds_discount FROM k_app_contrato WHERE cl_sesion='$cl_sesion_bd' ";      
      EjecutaQuery($Query);

      $update="UPDATE k_app_contrato SET fg_international=(SELECT fg_international FROM k_app_contrato WHERE cl_sesion='$cl_sesion_bd') where cl_sesion='$cl_sesion_new' ";
      EjecutaQuery($update);

      $update="UPDATE k_app_contrato SET fg_aplicar_international=(SELECT fg_aplicar_international FROM k_app_contrato WHERE cl_sesion='$cl_sesion_bd') where cl_sesion='$cl_sesion_new' ";
      EjecutaQuery($update);
    }
      
   
    # Copiamos los registros del k_ses_app_frm_2 al nuevo registro en la misma tabla
    $Query2  = "INSERT INTO k_ses_app_frm_2 (cl_sesion,ds_resp_1,ds_resp_2,ds_resp_3,ds_resp_4,ds_resp_5,ds_resp_6,ds_resp_7,fe_ultmod) ";
    $Query2 .= "SELECT '$cl_sesion_new', ds_resp_1, ds_resp_2, ds_resp_3, ds_resp_4, ds_resp_5, ds_resp_6, ds_resp_7, NOW() ";
    $Query2 .= "FROM k_ses_app_frm_2 WHERE cl_sesion='$cl_sesion_bd' ";
    EjecutaQuery($Query2);
    
    # Copiamos los registros del k_ses_app_frm_3 al nuevo registro en la misma tabla
    $Query3  = "INSERT INTO k_ses_app_frm_3 (cl_sesion,ds_resp_1,ds_resp_2_1,ds_resp_2_2,ds_resp_2_3,ds_resp_3,ds_resp_4,ds_resp_5, ";
    $Query3 .= "ds_resp_6,ds_resp_7,ds_resp_8,fe_ultmod) ";
    $Query3 .= "SELECT '$cl_sesion_new', ds_resp_1, ds_resp_2_1, ds_resp_2_2, ds_resp_2_3, ds_resp_3, ds_resp_4, ds_resp_5, ";
    $Query3 .= "ds_resp_6, ds_resp_7, ds_resp_8, NOW() FROM k_ses_app_frm_3 WHERE cl_sesion='$cl_sesion_bd' ";
    EjecutaQuery($Query3);
    
    $Query4  = "INSERT INTO k_ses_app_frm_4 (cl_sesion,fg_resp_1_1,fg_resp_1_2,fg_resp_1_3,fg_resp_1_4,fg_resp_1_5,fg_resp_1_6,fg_resp_2_1,fg_resp_2_2,fg_resp_2_3, ";
    $Query4 .= "fg_resp_2_4,fg_resp_2_5,fg_resp_2_6,fg_resp_2_7,fg_resp_3_1,fg_resp_3_2,fe_ultmod) ";
    $Query4 .= "SELECT '$cl_sesion_new', fg_resp_1_1, fg_resp_1_2, fg_resp_1_3, fg_resp_1_4, fg_resp_1_5, fg_resp_1_6, fg_resp_2_1, fg_resp_2_2, fg_resp_2_3, ";
    $Query4 .= "fg_resp_2_4, fg_resp_2_5, fg_resp_2_6, fg_resp_2_7, fg_resp_3_1, fg_resp_3_2, NOW() FROM k_ses_app_frm_4 WHERE cl_sesion='$cl_sesion_bd' ";
    EjecutaQuery($Query4);
    
    # Buscamos si tiene un pago ya realizado lo copiamos con la nueva sesion
    $row2 = RecuperaValor("SELECT count(*) FROM k_ses_pago WHERE cl_sesion='$cl_sesion_bd'");
    $pago_existe = $row2[0];
    if(!empty($pago_existe)){
      # Buscamos el term inicial
      $Query  = "SELECT CASE b.fl_term_ini WHEN 0 THEN (SELECT fl_term_pago FROM k_term_pago c WHERE b.fl_term=c.fl_term) ";
      $Query .= "WHEN b.fl_term_ini>0 THEN (SELECT fl_term_pago FROM k_term_pago d WHERE d.fl_term=b.fl_term_ini) END fl_term_pago ";
      $Query .= "FROM k_ses_app_frm_1 a, k_term b WHERE a.fl_programa = b.fl_programa AND a.fl_periodo = b.fl_periodo AND a.cl_sesion='$cl_sesion_new' ";
      $row = RecuperaValor($Query);
      $fl_term_ini = $row[0];
      $Querys  = "INSERT INTO k_ses_pago (cl_sesion,fl_term_pago,cl_metodo_pago,fe_pago,mn_pagado,ds_comentario,ds_cheque,mn_late_fee,fg_refund,mn_refund,ds_transaccion ";
      $Querys .= ",mn_tax_paypal,ds_tax_provincia,fe_refund)";
      $Querys .= "SELECT '$cl_sesion_new', $fl_term_ini, cl_metodo_pago, fe_pago, mn_pagado, ds_comentario, ds_cheque, mn_late_fee, fg_refund, mn_refund, ";
      $Querys .= "ds_transaccion, mn_tax_paypal, ds_tax_provincia, fe_refund FROM k_ses_pago WHERE cl_sesion='$cl_sesion_bd'";
      EjecutaQuery($Querys);
    }
    
    # Buscamos los templates que debe tener
    $rst = EjecutaQuery("SELECT fl_template, fe_envio ds_header, ds_body, ds_footer FROM k_alumno_template WHERE fl_alumno=$clave");
    for($k=0;$rowt=RecuperaRegistro($rst);$k++){
      $fl_template = $rowt[0];
      $fe_envio = $rowt[1];
      $ds_header = $rowt[2];
      $ds_body = $rowt[3];
      $ds_footer = $rowt[4];
      $Query  = "INSERT INTO k_alumno_template (fl_alumno,fl_template,fe_envio,ds_header,ds_body,ds_footer) ";
      $Query .= "VALUES ($fl_sesion, $fl_template, '$fe_envio', '$ds_header', '$ds_body', '$ds_footer')";
      EjecutaQuery($Query);
    }

    # Verificamos si se desea enviar encontrato
    for($j=1;$j<=$no_contratos;$j++){
      if(!empty($fg_contrato[$j])){
        # Por ultimo mandamos el contrato al aplicante
        $from_add = ObtenConfiguracion(4);    
        $ds_encabezado = genera_documento($clave, 1, True);
        $ds_cuerpo = genera_documento($clave, 2, True);
        $ds_pie = genera_documento($clave, 3, True);
        
        # Genera una nueva clave para la liga de acceso al contrato
        $str = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890";
        $ds_cve1 = date("Ymd").$j;
        $ds_cve2 = "";
        for($i = 0; $i < 10; $i++){
          $ds_cve2 = $ds_cve2.substr($str, rand(0,62), 1);
        }
        # En ocaciones hace falta caracteres entonces los agregamos
        $size = strlen($ds_cve2);
        if($size!=10){
          $size = 10 - $size;
          for($k=1;$k<=$size;$k++)
            $ds_cve2 = $ds_cve2.$k;
        }          
        $ds_cve = $ds_cve1.$ds_cve2.$fl_sesion;
        
        # Dominio
        $dominio_campus = ObtenConfiguracion(60);
        
        # Envia el correo
        $subject = ObtenEtiqueta(598);
        $message  = $ds_encabezado.$ds_cuerpo;
        $message .= "http://".$dominio_campus."/contract_frm.php?c=$ds_cve<br><br><br>";
        $message .= $ds_pie;
        
        # Copia oculta de confirmacion al administrador
        if(ObtenConfiguracion(59)){
          $bcc = ObtenConfiguracion(20);    
        }
        else{
          $bcc = '';
        }
		
		
	  
    
	  #Recuperamos el alumno
	  $Query="SELECT  ds_email_r  FROM k_presponsable WHERE cl_sesion='$cl_sesion_new' ";
	  $row=RecuperaValor($Query);
	  $ds_email_responsable=$row['ds_email_r'];

	  if($bcc){  
	  
	  if($ds_email_responsable)
		  $bcc.=";".$ds_email_responsable;
	  }else
		  $bcc.=$ds_email_responsable; 
	  
	  
	  $Query="SELECT ds_a_email FROM k_app_contrato WHERE cl_sesion='$cl_sesion_new' ";
	  $row=RecuperaValor($Query);
	  $ds_email_alternative=$row['ds_a_email'];
	  
	  if($bcc){

	  if($ds_email_alternative)
		  $bcc.=";".$ds_email_alternative;
	  }else
		  $bcc=$ds_email_alternative;
	  
		
	  
		
		
		
		
        # Enviamos el contrato
        $mail_apply = EnviaMailHTML('', $from_add, $ds_email, $subject, $message, $bcc);
        # Actualizamos el contrato
        if($mail_apply){
          # Actualiza datos de costos para el contrato
          $Query  = "UPDATE k_app_contrato ";
          $Query .= "SET ds_cadena='$ds_cve' ";
          $Query .= "WHERE cl_sesion='$cl_sesion_new' ";
          $Query .= "AND no_contrato=$j ";
          EjecutaQuery($Query);
        }
      }
    } 
  
    # Verificamos si se desea que se elimine la applicacion original
    if(!empty($fg_app_delete)){
      # sesion
      EjecutaQuery("DELETE FROM c_usuario WHERE fl_sesion=$clave");
      # formas
      EjecutaQuery("DELETE FROM k_ses_app_frm_1 WHERE cl_sesion='$cl_sesion_bd'");
      EjecutaQuery("DELETE FROM k_ses_app_frm_2 WHERE cl_sesion='$cl_sesion_bd'");
      EjecutaQuery("DELETE FROM k_ses_app_frm_3 WHERE cl_sesion='$cl_sesion_bd'");
      EjecutaQuery("DELETE FROM k_ses_app_frm_4 WHERE cl_sesion='$cl_sesion_bd'");
      # contratos
      EjecutaQuery("DELETE FROM k_app_contrato WHERE cl_sesion='$cl_sesion_bd'");
      # pago
      EjecutaQuery("DELETE FROM k_ses_pago WHERE cl_sesion='$cl_sesion_bd'");
      # templates
      EjecutaQuery("DELETE FROM k_alumno_template WHERE fl_alumno=$clave");
    }
  }
?>