<?php
  
  # Libreria de funciones
  require '../../lib/general.inc.php';

  
  # Verifica que exista una sesion valida en el cookie y la resetea
  ValidaSesion( );
  
# Recibe parametrosfg_study_permit
  $origen = RecibeParametroHTML('origen',False,True);
  if(!empty($origen)){
    $clave = RecibeParametroHTML('clave',False,True);
  }
  else{
    $clave = RecibeParametroNumerico('clave');
  }
  $error = RecibeParametroNumerico('error');
  $confirmacion = RecibeParametroNumerico('confirmacion');
  $fg_error = RecibeParametroNumerico('fg_error');
  $falta_break=RecibeParametroNumerico('break');
  
  # anios que falta agreagar el break
  $tot_anios = RecibeParametroNumerico('tot_anios');

  # En esta funcion solo se permite modificacion
  if(!empty($clave))
    $permiso = PERMISO_DETALLE;
  else {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermiso(FUNC_APP_FRM, $permiso)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Recupera datos de la sesion
  $concat = array(ConsultaFechaBD('fe_ultmod', FMT_FECHA), "' '", ConsultaFechaBD('fe_ultmod', FMT_HORA));
  $Query  = "SELECT cl_sesion, fg_paypal, fg_confirmado, fg_pago, fg_inscrito, (".ConcatenaBD($concat).") 'fe_ultmod',fl_pais_campus,fg_stripe,fg_scholarship ";
  $Query .= "FROM c_sesion ";
  $Query .= "WHERE fl_sesion=$clave";
  $row = RecuperaValor($Query);
  $cl_sesion = $row[0];
  $fg_paypal = $row[1];
  $fg_confirmado = $row[2];
  $fg_pago = $row[3];
  $fg_inscrito = $row[4];
  $fe_ultmod = $row[5];
  $fl_pais_selected=$row['fl_pais_campus'];
  $fg_stripe=$row['fg_stripe'];
  $fg_scholarship=$row['fg_scholarship'];
  
  # Recupera datos del aplicante: forma 1
  $Query  = "SELECT ds_fname, ds_mname, ds_lname, ds_number, ds_alt_number, ds_email, fg_gender, ";
  $Query .= ConsultaFechaBD('fe_birth', FMT_FECHA)." fe_birth, ";
  $Query .= "ds_add_number, ds_add_street, ds_add_city, ds_add_state, ds_add_zip, d.ds_pais, ";
  $Query .= "ds_eme_fname, ds_eme_lname, ds_eme_number, ds_eme_relation, e.ds_pais, ";
  $Query .= "fg_ori_via, ds_ori_other, fg_ori_ref, ds_ori_ref_name, nb_programa, nb_periodo, fl_template, b.fl_programa, c.fe_inicio, a.fl_periodo ";
  $Query .=",a.fg_disability,a.ds_disability,a.ds_ruta_foto_permiso,".ConsultaFechaBD('a.fe_start_date',FMT_FECHA)." fe_start_date, ".ConsultaFechaBD('a.fe_expirity_date',FMT_FECHA)." fe_expirity_date ,nb_name_institutcion,a.ds_sin,a.fl_immigrations_status  ";
  $Query .=",race,grade,hispanic,military  ";
  
  /*$Query .= "FROM k_ses_app_frm_1 a, c_programa b, c_periodo c, c_pais d, c_pais e ";
  $Query .= "WHERE a.fl_programa=b.fl_programa ";
  $Query .= "AND a.fl_periodo=c.fl_periodo ";
  $Query .= "AND a.ds_add_country=d.fl_pais ";
  $Query .= "AND a.ds_eme_country=e.fl_pais ";
  $Query .= "AND cl_sesion='$cl_sesion'";*/
  $Query .="FROM k_ses_app_frm_1 a
  JOIN c_programa b ON  a.fl_programa=b.fl_programa 
  join c_periodo c ON a.fl_periodo=c.fl_periodo  
  left join c_pais d ON a.ds_add_country=d.fl_pais
  LEFT join c_pais e ON a.ds_eme_country=e.fl_pais
  WHERE cl_sesion='$cl_sesion' ";
  $row = RecuperaValor($Query);
  $ds_number = str_texto($row[3]);
  $fg_gender = str_texto($row[6]);
  $ds_eme_fname = str_texto($row[14]);
  $ds_eme_lname = str_texto($row[15]);
  $ds_eme_number = str_texto($row[16]);
  $ds_eme_relation = str_texto($row[17]);
  $ds_eme_country = str_texto($row[18]);
  $fg_ori_via = str_texto($row[19]);
  $ds_ori_other = str_texto($row[20]);
  $fg_ori_ref = str_texto($row[21]);
  $ds_ori_ref_name = str_texto($row[22]); 
  $nb_programa = $row[23];
  $nb_periodo = $row[24];
  $fl_template = $row[25];
  $fl_programa = $row[26];
  $fe_inicio = $row[27];
  $fl_periodo = $row[28];
  $fg_disabilityie=$row['fg_disability'];
  $ds_disability=$row['ds_disability'];
  $ds_ruta_foto_permiso=$row['ds_ruta_foto_permiso'];
  $fe_start_date=$row['fe_start_date'];
  $fe_expirity_date=$row['fe_expirity_date'];
  $nb_name_institutcion=str_texto($row['nb_name_institutcion']);
  $ds_sin=$row['ds_sin'];
  $fl_immigrations_status=$row['fl_immigrations_status'];
  $race=$row['race'];
  $grade=$row['grade'];
  $hispanic=!empty($row['hispanic'])?"Yes":"No";
  $military=!empty($row['military'])?"Yes":"No";
  $fg_disability=!empty($row['fg_disability'])?"Yes":"No";
  
  switch ($race) {

      case'W':
          $race="White/Caucasian";
          break;
      case'B':
          $race="Black/African American";
          break;
      case'A':
          $race="American Indian or Alaska Native";
          break;
      case'H':
          $race="Hawaiian Native or other Pacific Islander";
          break;
      case'AS':
          $race="Asian";
          break;
      case'M':
          $race="Multiracial";
          break;
      case'O':
          $race="Other";
          break;
  }

  switch ($grade) {
      case'L':
          $grade="Less than high school graduation";
          break;
      case'H':
          $grade="High school graduate";
          break;
      case'G':
          $grade="GED";
          break;
      case'S':
          $grade="Some post high school, no degree/certificate";
          break;
      case'C':
          $grade="Certificate (less than 2 years)";
          break;
      case'A':
          $grade="Associate degree";
          break;
      case'B':
          $grade="Bachelor's degree";
          break;
      case'M':
          $grade="Master's degree or higher";
          break;

  }


  #Recuperamos el si aplica internacional.
  $Query="SELECT fg_aplicar_international FROM k_app_contrato WHERE cl_sesion='$cl_sesion' ";
  $row=RecuperaValor($Query);
  $fg_aplicar_international=$row['fg_aplicar_international'];
  
  
  #Recupera datos adicionales a la forma 1 y del contrato del aplicante
  $Query  = "SELECT no_contrato, mn_app_fee, mn_tuition, mn_costs, ds_costs, mn_discount, ds_discount, mn_tot_tuition, mn_tot_program, ";
  $Query .= "mn_a_due, mn_a_paid, mn_b_due, mn_b_paid, mn_c_due, mn_c_paid, mn_d_due, mn_d_paid, ";
  $Query .= "ds_cadena, ds_firma_alumno, fg_opcion_pago, DATE_FORMAT(fe_firma, '%M %d, %Y'), ds_p_name, ds_education_number, fg_international, ";
  $Query .= "cl_preference_1, cl_preference_2, ds_m_add_number, ds_m_add_street, ds_m_add_city, ds_m_add_state, ds_m_add_zip, ds_pais, ds_a_email, cl_preference_3,fg_payment,fl_class_time ";
  $Query .= "FROM k_app_contrato  a LEFT JOIN c_pais b ON a.ds_m_add_country=b.fl_pais ";
  $Query .= "WHERE cl_sesion='$cl_sesion' ";
  $Query .= "ORDER BY no_contrato";
  $rs = EjecutaQuery($Query);
  while($row = RecuperaRegistro($rs)) {
    $no_contrato = $row[0];
    $app_fee = $row[1];
    $tuition = $row[2];
    $no_costos_ad = $row[3];
    $ds_costos_ad = $row[4];
    $no_descuento = $row[5];
    $ds_descuento = $row[6];
    $mn_tot_tuition = $row[7];
    $mn_tot_program = $row[8];
    $amount_due_a = $row[9];
    $amount_paid_a = $row[10];
    $amount_due_b = $row[11];
    $amount_paid_b = $row[12];
    $amount_due_c = $row[13];
    $amount_paid_c = $row[14];
    $amount_due_d = $row[15];
    $amount_paid_d = $row[16];
    $ds_cadena[$no_contrato] = $row[17];
    $ds_firma_alumno[$no_contrato] = $row[18];
    $opc_pago = $row[19];
    $fe_firma[$no_contrato] = $row[20];
    $fg_payment=$row['fg_payment'];
	$fl_class_time=$row['fl_class_time'];
    //$cl_preference_1 = $row[24];
    //$cl_preference_2 = $row[25];
    //$cl_preference_3 = $row[33];
  }
  
   #Realizamos la consulta para mostrar los tiempos 

  $Query_classtime="SELECT CONCAT( dia,'-',no_hora,' ',ds_tiempo)AS dia,fl_class_time FROM
            (
            SELECT CASE WHEN cl_dia='1'THEN '".ObtenEtiqueta(2390)."' 
               WHEN cl_dia='2'THEN '".ObtenEtiqueta(2391)."'
               WHEN cl_dia='3'THEN '".ObtenEtiqueta(2392)."' 
               WHEN cl_dia='4'THEN '".ObtenEtiqueta(2393)."' 
               WHEN cl_dia='5'THEN '".ObtenEtiqueta(2394)."' 
               WHEN cl_dia='6'THEN '".ObtenEtiqueta(2395)."' 
               ELSE '".ObtenEtiqueta(2396)."' 
               END dia , A.no_hora,ds_tiempo,A.fl_class_time_programa,B.fl_class_time 
		         FROM k_class_time_programa A
               JOIN k_class_time B ON B.fl_class_time=A.fl_class_time 
		         WHERE B.fl_programa=$fl_programa AND B.fl_periodo=$fl_periodo ) Z;
        ";
  $rs_class_time = EjecutaQuery($Query_classtime);
  $tot_reg_class_time = CuentaRegistros($rs_class_time);
  $nb_dia="";
  
  for($ic=1;$rowc=RecuperaRegistro($rs_class_time);$ic++) {
 $fl_class=$rowc[1];
      $nb_dia_=str_texto($rowc[0]);


      $nb_dia .=" ".$nb_dia_;

      if($ic<=($tot_reg_class_time-1))
          $nb_dia.=" &";
      else
          $nb_dia.= "";


  }
  $nb_dias=$nb_dia;
  
  
  #error
  # Inicializa variables
  if(!$fg_error) { // Sin error, viene del listado
    if(!empty($cl_sesion)) { // Actualizacion, recupera de la base de datos
      $Query  = "SELECT ds_add_number, ds_add_street, ds_add_city, ds_add_state, ds_add_zip, ds_add_country, b.fg_pago,  c.ds_m_add_number, c.ds_m_add_street, ";
      $Query .= "ds_m_add_city, c.ds_m_add_state, c.ds_m_add_zip, c.ds_m_add_country, ds_fname, ds_mname, ds_lname, ds_number, ds_link_to_portfolio, fg_international, ".ConsultaFechaBD('fe_birth', FMT_FECHA).", ";
      $Query .= "a.ds_email, c.ds_a_email, a.ds_ruta_foto, b.fg_archive, a.fg_responsable, c.cl_preference_1, c.cl_preference_2, c.cl_preference_3, a.cl_recruiter ";      
      $Query .= ",a.ds_alt_number, c.ds_p_name, c.ds_education_number, c.ds_usual_name ";
      $Query .= ", c.ds_citizenship, c.fg_study_permit, c.fg_study_permit_other, c.fg_aboriginal, c.ds_aboriginal, c.fg_health_condition, c.ds_health_condition,b.fg_enrollment,a.comments ";
      $Query .= "FROM k_ses_app_frm_1 a, c_sesion b  LEFT JOIN k_app_contrato c ON(b.cl_sesion=c.cl_sesion AND c.no_contrato=1) ";
      $Query .= "WHERE a.cl_sesion ='$cl_sesion' AND b.cl_sesion='$cl_sesion' ";
      $row = RecuperaValor($Query);
      $ds_add_number = $row[0];
      $ds_add_street = $row[1];
      $ds_add_city = str_texto($row[2]);
      $ds_add_state = str_texto($row[3]);
      $ds_add_zip = $row[4];
      $ds_add_country = $row[5];
      $ds_m_add_number = $row[7];
      $ds_m_add_street = $row[8];
      $ds_m_add_city = str_texto($row[9]);
      $ds_m_add_state = str_texto($row[10]);
      $ds_m_add_zip = $row[11];
      $ds_m_add_country = $row[12];
      $ds_fname = $row[13];
      $ds_mname = $row[14];
      $ds_lname = $row[15];
      $ds_number = $row[16];
      $ds_link_to_portfolio = str_texto($row[17]);
      $fg_international = $row[18];
      $fe_birth = $row[19];
      $ds_email = $row[20];
      $ds_a_email = $row[21];
      $ds_ruta_foto = $row[22];
      $fg_archive = $row[23];
      $fg_responsable = $row[24];
      $fg_enrollment=$row['fg_enrollment'];
      $comments=$row['comments'];
      
      //if($fg_responsable==1)
        //  $fg_responsable=0;
      
      
      
      if(!empty($fg_responsable)){
        $Query_res  = "SELECT ds_fname_r, ds_lname_r, ds_email_r, ds_aemail_r, ds_pnumber_r, ds_relation_r, fg_email ";
        $Query_res .= "FROM k_presponsable WHERE cl_sesion='$cl_sesion'";
        $row_res = RecuperaValor($Query_res);
        $ds_fname_r = str_ascii($row_res[0]);
        $ds_lname_r = str_ascii($row_res[1]);
        $ds_email_r = str_ascii($row_res[2]);
        $ds_aemail_r = str_ascii($row_res[3]);
        $ds_pnumber_r= str_ascii($row_res[4]);
        $ds_relation_r = str_ascii($row_res[5]);
        $fg_email = $row_res[6];
      }
      $cl_preference_1 = $row[25];
      $cl_preference_2 = $row[26];
      $cl_preference_3 = $row[27];
      $cl_recruiter = $row[28];
      $ds_alt_number = $row[29];
      $ds_p_name = str_texto($row[30]);
      $ds_education_number = $row[31];
      $ds_usual_name = str_texto($row[32]);
      $ds_citizenship = str_texto($row[33]);
      $fg_study_permit = $row[34];
      $fg_study_permit_other = $row[35];
      $fg_aboriginal = $row[36];
      $ds_aboriginal = str_texto($row[37]);
      $fg_health_condition = $row[38];
      $ds_health_condition = str_texto($row[39]);
    }
    else { // Alta, inicializa campos
      $ds_add_number = "";
      $ds_add_street = "";
      $ds_add_city = "";
      $ds_add_state = "";
      $ds_add_zip = "";
      $ds_add_country = "";
      $ds_m_add_number = "";
      $ds_m_add_street = "";
      $ds_m_add_city = "";
      $ds_m_add_state = "";
      $ds_m_add_zip = "";
      $ds_m_add_country = "";
      $ds_fname = "";
      $ds_mname = "";
      $ds_lname = "";
      $ds_link_to_portfolio = "";
      $fg_international = 0;
      $fe_birth = "";
      $ds_email = "";
      $ds_a_email = "";
      $ds_ruta_foto = "";
      $fg_archive = 0;      
      $ds_alt_number = "";
      $ds_p_name = "";
      $ds_education_number = "";
      $ds_usual_name = "";
      $ds_citizenship = "";
      $fg_study_permit = "";
      $fg_study_permit_other = "";
      $fg_aboriginal = "";
      $ds_aboriginal = "";
      $fg_health_condition = "";
      $ds_health_condition = "";
    }
    $ds_add_number_err = "";
    $ds_add_street_err = "";
    $ds_add_city_err = "";
    $ds_add_state_err = "";
    $ds_add_zip_err = "";
    $ds_add_country_err = "";
    $ds_m_add_number_err = "";
    $ds_m_add_street_err = "";
    $ds_m_add_city_err = "";
    $ds_m_add_state_err = "";
    $ds_m_add_zip_err = "";
    $ds_m_add_country_err = "";
    $ds_fname_err = "";
    $ds_mname_err = "";
    $ds_lname_err = "";
    $ds_email_err = "";
    $ds_a_email_err = "";
    $fg_archive = 0;    
    $ds_alt_number_err = "";
    $ds_p_name_err = "";
    $ds_education_number_err = "";
    $ds_usual_name_err = "";
  }
  else { // Con error, recibe parametros (viene de la pagina de actualizacion)
    $ds_add_number = RecibeParametroHTML('ds_add_number');
    $ds_add_street = RecibeParametroHTML('ds_add_street');
    $ds_add_city = RecibeParametroHTML('ds_add_city');
    $ds_add_state = RecibeParametroHTML('ds_add_state');
    $ds_add_zip = RecibeParametroHTML('ds_add_zip');
    $ds_add_country = RecibeParametroHTML('ds_add_country');
    $ds_add_number_err = RecibeParametroHTML('ds_add_number_err');
    $ds_add_street_err = RecibeParametroHTML('ds_add_street_err');
    $ds_add_city_err = RecibeParametroHTML('ds_add_city_err');
    $ds_add_state_err = RecibeParametroHTML('ds_add_state_err');
    $ds_add_zip_err = RecibeParametroHTML('ds_add_zip_err');
    $ds_add_country_err = RecibeParametroHTML('ds_add_country_err');
    //Mailing addrees
    $ds_m_add_number = RecibeParametroHTML('ds_m_add_number');
    $ds_m_add_street = RecibeParametroHTML('ds_m_add_street');
    $ds_m_add_city = RecibeParametroHTML('ds_m_add_city');
    $ds_m_add_state = RecibeParametroHTML('ds_m_add_state');
    $ds_m_add_zip = RecibeParametroHTML('ds_m_add_zip');
    $ds_m_add_country = RecibeParametroHTML('ds_m_add_country');
    #nombres
    $ds_fname = RecibeParametroHTML('ds_fname');
    $ds_fname_err = RecibeParametroHTML('ds_fname_err');
    $ds_mname = RecibeParametroHTML('ds_mname');
    $ds_lname = RecibeParametroHTML('ds_lname');
    $ds_lname_err = RecibeParametroHTML('ds_lname_err');
    $ds_number = RecibeParametroHTML('ds_number');
    $ds_number_err = RecibeParametroHTML('ds_number_err');
    $ds_link_to_portfolio = RecibeParametroHTML('ds_link_to_portfolio');
    $fg_international = RecibeParametroBinario('fg_international');
    $fe_birth = RecibeParametroHTML('fe_birth');
    $fe_birth_err = RecibeParametroNumerico('fe_birth_err');
    $ds_email = RecibeParametroHTML('ds_email');
    $ds_email_err = RecibeParametroHTML('ds_email_err');
    $ds_ruta_foto = RecibeParametroHTML('ds_ruta_foto');
    $ds_ruta_foto_err = RecibeParametroHTML('ds_ruta_foto_err');   
    $fg_archive = RecibeParametroBinario('fg_archive');
    # Person Responsable
    $fg_responsable = RecibeParametroBinario('fg_responsable');    
    $ds_fname_r = RecibeParametroHTML('ds_fname_r');
    $ds_fname_r_err = RecibeParametroHTML('ds_fname_r_err');    
    $ds_lname_r = RecibeParametroHTML('ds_lname_r');
    $ds_lname_r_err = RecibeParametroHTML('ds_lname_r_err');
    $ds_email_r = RecibeParametroHTML('ds_email_r');
    $ds_email_r_err = RecibeParametroHTML('ds_email_r_err');
    $ds_aemail_r = RecibeParametroHTML('ds_aemail_r');
    $ds_aemail_r_err = RecibeParametroHTML('ds_aemail_r_err');
    $ds_pnumber_r= RecibeParametroHTML('ds_pnumber_r');
    $ds_pnumber_r_err= RecibeParametroHTML('ds_pnumber_r_err');
    $ds_relation_r = RecibeParametroHTML('ds_relation_r');
    $ds_relation_r_err = RecibeParametroHTML('ds_relation_r_err');
    $fg_email = RecibeParametroNumerico('fg_email');
    $cl_preference_1 = RecibeParametroNumerico('cl_preference_1');
    $cl_preference_1_err = RecibeParametroNumerico('cl_preference_1_err');
    $cl_preference_2 = RecibeParametroNumerico('cl_preference_2');
    $cl_preference_2_err = RecibeParametroNumerico('cl_preference_2_err');
    $cl_preference_3 = RecibeParametroNumerico('cl_preference_3');
    $cl_preference_3_err = RecibeParametroNumerico('cl_preference_3_err');
    $cl_recruiter = RecibeParametroNumerico('cl_recruiter');
    $cl_recruiter_err = RecibeParametroNumerico('cl_recruiter_err');
	
	$fl_class_time=RecibeParametroNumerico('fl_class_time');
	$fl_class_time_err=RecibeParametroNumerico('fl_class_time_err');
    
    # Alter number Previous name Student personal education number
    $ds_alt_number = RecibeParametroHTML("ds_alt_number");
    $ds_alt_number_err = RecibeParametroHTML("ds_alt_number_err");
    $ds_p_name = RecibeParametroHTML("ds_p_name");
    $ds_p_name_err = RecibeParametroHTML("ds_p_name_err");
    $ds_education_number = RecibeParametroHTML("ds_education_number");
    $ds_education_number_err = RecibeParametroHTML("ds_education_number_err");
    $ds_usual_name = RecibeParametroHTML("ds_usual_name");
    $ds_usual_name_err = RecibeParametroHTML("ds_usual_name_err");
    $ds_citizenship = RecibeParametroHTML('ds_citizenship');
    $fg_study_permit = RecibeParametroNumerico('fg_study_permit');
    $fg_study_permit_other = RecibeParametroNumerico('fg_study_permit_other');
    $fg_aboriginal = RecibeParametroBinario('fg_aboriginal');
    $ds_aboriginal = RecibeParametroHTML('ds_aboriginal');
    $fg_health_condition = RecibeParametroHTML('fg_health_condition');
    $ds_health_condition = RecibeParametroHTML('ds_health_condition');
    $fg_disabilityie = RecibeParametroBinario('fg_disabilityie');
    $fg_disabilityie_err = RecibeParametroBinario('fg_disabilityie_err');
    $ds_disability = RecibeParametroHTML('ds_disability');
    $ds_disability_err = RecibeParametroHTML('ds_disability_err');
    
    
    $fe_start_date=RecibeParametroHoraMin('fe_start_date');
    $fe_expirity_date=RecibeParametroHoraMin('fe_expirity_date');
    $nb_name_institutcion=RecibeParametroHTML('nb_name_institutcion');
    
    $fg_enrollment=RecibeParametroBinario('fg_enrollment');
    $comments=RecibeParametroHTML('comments');


  }
  
  if($fg_international==1){
      

  }else{


      
  
  }
  

  if($fg_international==1){
      
      if($fg_payment=='C'){
          # Recupera datos de pagos del curso.
          $Query  = "SELECT no_a_payments_internacional_combined, ds_a_freq_internacional_combined, no_b_payments_internacional_combined, ds_b_freq_internacional_combined, no_c_payments_internacional_combined, ds_c_freq_internacional_combined, no_d_payments_internacional_combined, ds_d_freq_internacional_combined, cl_type, ";
          $Query .= "no_a_interes_internacional_combined, no_b_interes_internacional_combined, no_c_interes_internacional_combined, no_d_interes_internacional_combined, no_semanas, mn_tuition_internacional_combined ";
      }else{
          # Recupera datos de pagos del curso.
          $Query  = "SELECT no_a_payments_internacional, ds_a_freq_internacional, no_b_payments_internacional, ds_b_freq_internacional, no_c_payments_internacional, ds_c_freq_internacional, no_d_payments_internacional, ds_d_freq_internacional, cl_type, ";
          $Query .= "no_a_interes_internacional, no_b_interes_internacional, no_c_interes_internacional, no_d_interes_internacional, no_semanas, mn_tuition_internacional ";

      }
  }else{    
      if($fg_payment=='C'){
          # Recupera datos de pagos del curso.
          $Query  = "SELECT no_a_payments_combined, ds_a_freq_combined, no_b_payments_combined, ds_b_freq_combined, no_c_payments_combined, ds_c_freq_combined, no_d_payments_combined, ds_d_freq_combined, cl_type, ";
          $Query .= "no_a_interes_combined, no_b_interes_combined, no_c_interes_combined, no_d_interes_combined, no_semanas, mn_tuition_combined ";
      }else{
          # Recupera datos de pagos del curso.
          $Query  = "SELECT no_a_payments, ds_a_freq, no_b_payments, ds_b_freq, no_c_payments, ds_c_freq, no_d_payments, ds_d_freq, cl_type, ";
          $Query .= "no_a_interes, no_b_interes, no_c_interes, no_d_interes, no_semanas, mn_tuition ";
      }

  }







  
  
  $Query .= "FROM k_programa_costos ";
  $Query .= "WHERE fl_programa = $fl_programa";
  $row = RecuperaValor($Query);
  $no_a_payments = $row[0];
  $ds_a_freq = $row[1];
  $no_b_payments = $row[2];
  $ds_b_freq = $row[3];
  $no_c_payments = $row[4];
  $ds_c_freq = $row[5];
  $no_d_payments = $row[6];
  $ds_d_freq = $row[7];
  $cl_type = $row[8];
  $no_a_interes = $row[9];
  $no_b_interes = $row[10];
  $no_c_interes = $row[11];
  $no_d_interes = $row[12];
  $no_semanas = $row[13];
  $mn_tuition = $row[14];
  
  # Calculos pagos
  $total_tuition = number_format($tuition + $no_costos_ad - $no_descuento, 2, '.', '');
  $total = number_format($app_fee + $total_tuition, 2, '.', '');
  
  # Recupera datos del aplicante: forma 2
  $Query  = "SELECT ds_resp_1, ds_resp_2, ds_resp_3, ds_resp_4, ds_resp_5, ds_resp_6, ds_resp_7 ";
  $Query .= "FROM k_ses_app_frm_2 ";
  $Query .= "WHERE cl_sesion='$cl_sesion'";
  $row = RecuperaValor($Query);
  $ds_resp2_1 = str_texto($row[0]);
  $ds_resp2_2 = str_texto($row[1]);
  $ds_resp2_3 = str_texto($row[2]);
  $ds_resp2_4 = str_texto($row[3]);
  $ds_resp2_5 = str_texto($row[4]);
  $ds_resp2_6 = str_texto($row[5]);
  $ds_resp2_7 = str_texto($row[6]);
  
  # Recupera datos del aplicante: forma 3
  $Query  = "SELECT fg_resp_1_1, fg_resp_1_2, fg_resp_1_3, fg_resp_1_4, fg_resp_1_5, fg_resp_1_6, ";
  $Query .= "fg_resp_2_1, fg_resp_2_2, fg_resp_2_3, fg_resp_2_4, fg_resp_2_5, fg_resp_2_6, fg_resp_2_7, ";
  $Query .= "fg_resp_3_1, fg_resp_3_2 ";
  $Query .= "FROM k_ses_app_frm_4 ";
  $Query .= "WHERE cl_sesion='$cl_sesion'";
  $row = RecuperaValor($Query);
  $fg_resp4_1_1 = str_ascii($row[0]);
  $fg_resp4_1_2 = str_ascii($row[1]);
  $fg_resp4_1_3 = str_ascii($row[2]);
  $fg_resp4_1_4 = str_ascii($row[3]);
  $fg_resp4_1_5 = str_ascii($row[4]);
  $fg_resp4_1_6 = str_ascii($row[5]);
  $fg_resp4_2_1 = str_ascii($row[6]);
  $fg_resp4_2_2 = str_ascii($row[7]);
  $fg_resp4_2_3 = str_ascii($row[8]);
  $fg_resp4_2_4 = str_ascii($row[9]);
  $fg_resp4_2_5 = str_ascii($row[10]);
  $fg_resp4_2_6 = str_ascii($row[11]);
  $fg_resp4_2_7 = str_ascii($row[12]);
  $fg_resp4_3_1 = str_ascii($row[13]);
  $fg_resp4_3_2 = str_ascii($row[14]);
  
  # Recupera datos del aplicante: forma 4
  $Query  = "SELECT ds_resp_1, ds_resp_2_1, ds_resp_2_2, ds_resp_2_3, ds_resp_3, ds_resp_4, ds_resp_5, ds_resp_6, ds_resp_7, ds_resp_8 ";
  $Query .= "FROM k_ses_app_frm_3 ";
  $Query .= "WHERE cl_sesion='$cl_sesion'";
  $row = RecuperaValor($Query);
  $ds_resp3_1 = str_texto($row[0]);
  $ds_resp3_2_1 = str_texto($row[1]);
  $ds_resp3_2_2 = str_texto($row[2]);
  $ds_resp3_2_3 = str_texto($row[3]);
  $ds_resp3_3 = str_texto($row[4]);
  $ds_resp3_4 = str_texto($row[5]);
  $ds_resp3_5 = str_texto($row[6]);
  $ds_resp3_6 = str_texto($row[7]);
  $ds_resp3_7 = str_texto($row[8]);
  $ds_resp3_8 = str_texto($row[9]);
  
  
  # Presenta forma de captura
  PresentaHeader( );
  PresentaEncabezado(FUNC_APP_FRM);
  # Ventana para preview de archivos de flash
  require 'preview.inc.php';
  Forma_Inicia($clave, True);
  /*if($fg_error)
    Forma_PresentaError( );*/
?>
<?php
  
  echo "
  <script type='text/javascript' src='".PATH_JS."/frmApplications.js.php'></script>
  <script type='text/javascript' src='".PATH_JS."/sendtemplate.js.php'></script>";
  
  ?>






  <div id='widget-grid' >
    <?php
    if (!empty($fg_error))
        Forma_PresentaError();
    ?>
    <div role="widget" style="" class="jarviswidget" data-widget-colorbutton="false" data-widget-editbutton="false" data-widget-togglebutton="false" data-widget-deletebutton="false" data-widget-custombutton="false" data-widget-sortable="false">
        <header role="heading">
            <div role="menu" class="jarviswidget-ctrls">   
                <a data-original-title="Fullscreen" href="javascript:void(0);" class="button-icon jarviswidget-fullscreen-btn" rel="tooltip" title="" data-placement="bottom"><i class="fa fa-expand "></i></a>
            </div>
            <span class="widget-icon"> <i class="fa fa-user"></i> </span>
            <h2>
              <strong>
                  <?php
                echo $ds_fname . ' ' . ($ds_mname != '' ? $ds_mname . ' ' : '') . $ds_lname." ";

                switch ($fl_pais_selected) {

                         case '38':
                            $country_campus = "| Campus CANADA";
                            break;
                        case '226':
                            $country_campus = "| Campus USA";
                            break;
                        case '199':
                            $country_campus = "| Campus Spain";
                            break;
                        case '73':
                            $country_campus = "| Campus France";
                            break;
                        case '80':
                            $country_campus = "| Campus Germany";
                            break;
                        case '105':
                            $country_campus = "| Campus Italy";
                            break;
                        case '225':
                            $country_campus = "| United Kingdom";
                            break;
                        case '153':
                            $country_campus = "| Campus New Zealand";
                            break;
                        default:
                            $country_campus = "| Campus CANADA";
                            break;

                    }
                  echo $country_campus;



                  ?>                
              </strong>                
              <small class="font-sm"><?php echo (!empty($ds_login)?$ds_login:NULL) ?></small>
            </h2>
            <div role="menu" class="widget-toolbar">
                <div class="btn-group">
                    <button aria-expanded="false" class="btn dropdown-toggle btn-xs btn-info" data-toggle="dropdown">
                        Actions <i class="fa fa-caret-down"></i>
                    </button>
                    <ul class="dropdown-menu pull-right">
                        <li>
                            <a href="javascript:showDialog();" data-toggle="modal" data-target="#dialog"><i class="fa fa-envelope">&nbsp;</i>Send Letter</a>
                        </li>
                        <li>
                            <a data-toggle="modal" data-target="#copy_app"><i class="fa fa-copy">&nbsp;</i><?php echo ObtenEtiqueta(890); ?></a>
                        </li>                        
                    </ul>                    
                </div>
            </div>
        </header>
        <!-- widget div-->
        <!--<div role="content" class="no-padding">-->
        <div class="no-padding">
            <!-- widget edit box -->
            <div class="jarviswidget-editbox">
                <!-- This area used as dropdown edit box -->
            </div>
            <!-- end widget edit box -->
            <!-- widget content -->
            <div class="widget-body">
                <ul id="myTab1" class="nav nav-tabs bordered">
                    <li class="active">
                        <a href="#student" data-toggle="tab"><i class="fa fa-fw fa-lg fa-user"></i> General</a>
                    </li>
                    <li>
                        <a href="#communicationsHistory" data-toggle="tab"><i class="fa fa-fw fa-lg fa-envelope"></i> Communications</a>
                    </li>
                    <li>
                        <a href="#payments" data-toggle="tab"><i class="fa fa-fw fa-lg fa-usd"></i> Payment History</a>
                    </li>
                    <li>
                        <a href="#applicationForm" data-toggle="tab"><i class="fa fa-fw fa-lg fa-file-text"></i> Application Form</a>
                    </li>
					
					<li>
						<a  href="#rubric" data-toggle="tab" ><i class="fa fa-fw fa-lg fa-table"></i><?php echo 'Rubric' ?>
						</a>
					</li>
					
					<li style="display:none;">
						<a  href="#status" data-toggle="tab" ><i class="fa fa-fw fa-lg fa-asterisk"></i>Status 
						</a>
					</li>
					
					
                </ul>

                <div id="myTabContent1" class="tab-content padding-10 no-border">
                  <div class="tab-pane fade in active" id="student">
                    <div class="row">
                         <div class="col-xs-3 col-sm-3">
                             <?php 
                             Forma_CampoCheckbox('', 'fg_enrollment', $fg_enrollment, '<strong>Ready to enroll</strong>', '', True, '', 'left', 'col-sm-12', 'col-sm-12'); 
                             ?>
                         </div>
                        <div class="col-xs-3 col-sm-3">
                             <?php 
                             Btstrp_Forma_CampoTextArea("<strong>Additional comments</strong>", False, 'comments', $comments, 0, 5);
                             
                             ?>
                         </div>
                         <div class="col-xs-6 col-sm-6">

                          



                         <?php 
                         if(!empty($falta_break)){
                             
                            
                             # Si no existe un break en el curso del alumno mansa error falta un break
                         echo" 
                            <div class='alert alert-danger text-center' role='alert'>
                                ".ObtenEtiqueta(2326)."
                            </div>
                         ";
                         
                         
                         }
                         
                         
                         ?>




                          <ul id="sparks" class="list-unstyled">
                          <?php
                          /*# Si el contrato es mutil anios entonces se enviara un contrato por anio 
                          #  totod esto es como lo marca PCTIA
                          if($cl_type==4)
                            $contratos = 3;
                          else{
                            # En caso de qe el curso sea mayor a 18 meses y menos a  104 (2 anios) entonces se enviaran dos contratos 
                            if($no_semanas>78 AND $no_semanas<104)
                              $contratos = 2;
                            else# si es curso dure menos de 18 meses se enviara un solo contrato
                              $contratos = 1;
                          }*/
                          # Obtenemos el numero de contratos por programa
                          $meses_maximo = ObtenConfiguracion(92); // Agregar en configuracion
                          $meses_x_contrato = 48; // Agregar en configuracion
                          
                          # Obtenemos los numeros de contratos que deben tener
                          $contratos = ceil($no_semanas/$meses_x_contrato);
                          # Obtenemos los contratos que son de 12 meses
                          $no_contratos_floor = floor($no_semanas/$meses_x_contrato);                          
                          
                          $enrol = False;
                          for($i=1; $i<=$contratos; $i++) {
                            # Si no existe el numero de mes en los contratos actualiza
                            $row3 = RecuperaValor("SELECT SUM( no_weeks ) FROM k_app_contrato WHERE cl_sesion = '$cl_sesion' AND no_contrato <$i");                            
                            # Obtenemos el numero de meses que cubre el contrato
                            if($no_semanas <= $meses_maximo){
                              $weeks_contrato = round($no_semanas);
                            }
                            else{
                              if($i<=$no_contratos_floor)
                                $weeks_contrato = $meses_x_contrato;
                              else
                                $weeks_contrato = $no_semanas-$row3[0];
                            }
                            # Obtenemos costo del contrato
                            // $mn_payment_due = ($mn_tuition/$no_semanas)*$weeks_contrato;
                            # Actualizamos contrato
                            EjecutaQuery("UPDATE k_app_contrato SET no_weeks=".$weeks_contrato." WHERE cl_sesion='".$cl_sesion."' AND no_contrato='".$i."'");
                            if(!empty($fl_template)) {
                              //$ds_descarga = "<a href='../reports/documents_rpt.php?c=$clave&con=$i'>".ObtenEtiqueta(346)."</a>";
                             
                              if(empty($ds_cadena[$i]) || (!empty($ds_cadena[$i]) && empty($ds_firma_alumno[$i]))) {
                                //$ds_envia = "&nbsp;&nbsp;&nbsp;<a href='applications_snd.php?c=$clave&con=$i'>".ObtenEtiqueta(347)."</a>";
                                if(empty($ds_cadena[$i])){
                                  $btn_class = "btn-danger";
                                  $txt_color = "text-danger";
                                  $text = ObtenEtiqueta(238);
                                }
                                else{
                                  $btn_class = "btn-warning";
                                  $txt_color = "text-warning";
                                  $text =  ObtenEtiqueta(239);
                                }
                                $ds_envia = '<a href="applications_snd.php?c='.$clave.'&con='.$i.'" class="btn btn-info '.$btn_class.'" rel="tooltip" data-placement="top" data-original-title="' . ObtenEtiqueta(347) . '" >'
                                                        . '<i class="fa fa-envelope">&nbsp;</i>'.$text.' &nbsp; (' . $i.')'
                                                        . '</a>';
                                $ds_firma = "";
                                if($i==1)
                                  $enrol = False;
                              }
                              else {
                                $ds_envia = "";
                                //$ds_firma = "&nbsp;&nbsp;&nbsp;<a href='view_contract.php?c=$clave&con=$i' target='_blank'>".ObtenEtiqueta(348)."</a>";
                                $ds_firma = '<a href="view_contract.php?c='.$clave.'&con='.$i.'" class="txt-success"  target="_blank" rel="tooltip" data-placement="top" data-original-title="' . ObtenEtiqueta(348) . '" ><i class="fa fa-search">&nbsp;</i> ' .ObtenEtiqueta(243).'&nbsp;('. $i . ')</a> ';                                
                                $txt_color = "text-success";
                                if($i==1)
                                  $enrol = True;
                              }
                              $ds_descarga = '<div class="txt-color-blue " style="padding:0px; display: block; float: right; margin: 1px 0 0 10px;">
                                                  <a href="../reports/documents_rpt.php?c='.$clave.'&con='.$i.'"><i class="fa fa-file-pdf-o '.$txt_color.'" style="font-size:2.5em;" rel="tooltip" data-placement="bottom" data-original-title="' . ObtenEtiqueta(346) . '" >&nbsp;</i></a>
                                               </div>';
                            }
                            else {
                              $ds_descarga = ObtenMensaje(213);
                              $ds_envia = "";
                              $ds_firma = "";
                            }
                            //Forma_CampoInfo('Contract '.$i, $ds_descarga.$ds_envia.$ds_firma);
                            $fe_temp = substr($ds_cadena[$i],0,8);
                            if(!empty($fe_temp))
                              $fe_envio = date("M j, Y",strtotime("$fe_temp"));
                            else
                              $fe_envio = "";
                            //Forma_CampoInfo(ObtenEtiqueta(597), $fe_envio); 
                            ?>
                            <li class="sparks-info" style="max-height:60px;">
                                <h5>
                                    <?php echo $ds_envia . $ds_firma ?>
                                    <div>
                                    <small class="<?php echo $txt_color; ?>" rel="tooltip" data-placement="bottom" data-original-title="<?php echo ObtenEtiqueta(597) ?>" >
                                        <strong><?php echo ObtenEtiqueta(884).": ".$fe_envio; ?></strong>
                                    </small>
                                    <br><small class="<?php echo $txt_color; ?>" rel="tooltip" data-placement="bottom" data-original-title="<?php echo ObtenEtiqueta(597) ?>" >
                                        <strong><?php echo ObtenEtiqueta(883).": ".$fe_firma[$i]; ?></strong>
                                    </small>
                                    </div>
                                </h5>
                                <?php echo $ds_descarga ?>
                            </li>
                            <?php
                            # Muestra error de que falta crear break en el anio
                            if(!empty($tot_anios)){
                              echo "<tr><td></td><td class='css_msg_error'>lack break in the year: ";
                              for($i=0;$i<=$tot_anios;$i++){
                                $existe = RecibeParametroNumerico('existe_'.$i);
                                if($existe==0)
                                  echo $anios = RecibeParametroNumerico('anios_'.$i)."&nbsp;";
                              }
                              echo "</td></tr>";
                            }
                          }  
                          ?>
                          </ul>
                      </div>
                    </div>
                    <!-- Datos de envio-->
                    <hr />
                    <div class="col col-xs-12 col-sm-10 col-lg-12">
                      <div class="row">                       
                        <div class="col col-xs-12 col-sm-4"><?php echo Forma_CampoInfo(ObtenEtiqueta(340), $fe_ultmod, 'left', 'col-sm-12', 'col-sm-12'); ?></div>
                        <div class="col col-xs-12 col-sm-4">
                        <?php 
                        if($fg_confirmado == '1')
                          $ds_fg_confirmado = ETQ_SI;
                        else
                          $ds_fg_confirmado = ETQ_NO;
                        Forma_CampoInfo(ObtenEtiqueta(344), $ds_fg_confirmado, 'left', 'col-sm-12', 'col-sm-12');
                        ?>
                        </div>
                        <div class="col col-xs-12 col-sm-4">
                        <?php
                        $ds_fg_paypal = ETQ_NO;                      
                        if(($fg_paypal == '1'||($fg_stripe=='1'))){
                            if($fg_stripe=='1'){
                                $ds_fg_paypal = ETQ_SI;
                            }
                        }
                        if($fg_stripe=='1')
                        Forma_CampoInfo('Payment submitted to Stripe', $ds_fg_paypal, 'left', 'col-sm-12', 'col-sm-12');
                        else
                        Forma_CampoInfo(ObtenEtiqueta(343), $ds_fg_paypal, 'left', 'col-sm-12', 'col-sm-12');
                        ?>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col col-xs-12 col-sm-4">
                        <?php 
                        if($fg_pago == '1')
                          $ds_fg_pago = ETQ_SI;
                        else
                          $ds_fg_pago = ETQ_NO;
                        Forma_CampoInfo(ObtenEtiqueta(341), $ds_fg_pago, 'left', 'col-sm-12', 'col-sm-12');
                        ?>
                        </div>
                        <div class="col col-xs-12 col-sm-4">
                        <?php 
                        if($enrol==True)
                          Forma_CampoCheckbox('', 'fg_inscrito', $fg_inscrito, ObtenEtiqueta(345), '', True, 'disabled="disabled"', 'left', 'col-sm-12', 'col-sm-12');
                        else
                          Forma_CampoCheckbox('', 'fg_inscrito', $fg_inscrito, ObtenEtiqueta(345), '', True, '', 'left', 'col-sm-12', 'col-sm-12'); 
                        ?>
                        </div>
                        <div class="col col-xs-12 col-sm-4">
                        <?php 
                        Forma_CampoCheckbox('', 'fg_archive', $fg_archive, ObtenEtiqueta(854), '', True, '', 'left', 'col-sm-12', 'col-sm-12'); 
                        ?>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col col-xs-12 col-sm-4">
                        <?php 
                         Forma_CampoInfo(ObtenEtiqueta(360), $nb_programa, 'left', 'col-sm-12', 'col-sm-12');
                        ?>
                        </div>
                        <div class="col col-xs-12 col-sm-4">
                        <?php 
                         Forma_CampoInfo(ObtenEtiqueta(342), $nb_periodo, 'left', 'col-sm-12', 'col-sm-12');
                        ?>
                        </div>
                        <div class="col col-xs-12 col-sm-4">  
                        <?php
                        $Query  = "SELECT CONCAT( ds_nombres, ' ', ds_apaterno ) , fl_usuario FROM c_usuario usr, c_perfil per ";
                        $Query .= "WHERE usr.fl_perfil = per.fl_perfil AND usr.fl_perfil=".PERFIL_RECRUITER." AND usr.fg_activo='1' ORDER BY fg_default ASC , ds_nombres ASC ";
                        Forma_CampoSelectBD(ObtenEtiqueta(877), False, 'cl_recruiter', $Query, $cl_recruiter, !empty($cl_recruiter_err)?$cl_recruiter_err:NULL, True, '', 'left', 'col col-sm-12', 'col col-sm-12');
                        ?>
                        </div>
                      </div>
                    </div>
                    <?php
                    # Busca si existen alumnos con el mismo nombre y apellidos JGFL 26/01/2015
                    $Query = "SELECT a.fl_usuario, CONCAT(a.ds_nombres,' ',a.ds_apaterno), c.nb_programa, nb_periodo FROM c_usuario a, k_ses_app_frm_1 b, c_programa c, c_periodo d ";
                    $Query .= "WHERE a.ds_nombres='".$ds_fname."' AND a.ds_apaterno='".$ds_lname."' ";
                    $Query .= "AND a.cl_sesion=b.cl_sesion AND b.fl_programa = c.fl_programa AND b.fl_periodo=d.fl_periodo ";
                    $rs = EjecutaQuery($Query);
                    $registros = CuentaRegistros($rs);
                    # Si existe regitros mostrar tanto la seccion como la tabla de los studens homonimos
                    if(!empty($registros)){
                      Forma_Seccion(ObtenEtiqueta(693));
                      $titulos = array(''.ObtenEtiqueta(360).'|center', ''.ETQ_NOMBRE.'|center',''.ObtenEtiqueta(382).'|center');
                      $ancho_col = array('20%', '20%','10%','3%');
                      Forma_Tabla_Ini('50%', $titulos, $ancho_col);
                      for($i=0;$row=RecuperaRegistro($rs);$i++) {
                        echo "
                        <tr>
                          <td><a href=\"javascript:otro('students_frm.php',$row[0]);\"'>".$row[2]."</a></td>
                          <td><a href=\"javascript:otro('students_frm.php',$row[0]);\"'>".$row[1]."</a></td>
                          <td><a href=\"javascript:otro('students_frm.php',$row[0]);\"'>".$row[3]."</a></td>
                        </tr>";
                      }
                      Forma_Tabla_Fin();
                    }
                    
                    # Datos del aplicante
                    Forma_Seccion(ObtenEtiqueta(61));
                    ?>
					
					<!------========= la funcion validarnspace valida los espacio en blanco ========--->
					
                    <div class="col col-xs-12 col-sm-10 col-lg-12">
                      <div class="row">
                        <div class="col col-xs-12 col-sm-4">
                        <?php
                        Forma_CampoTexto(ObtenEtiqueta(117), True, 'ds_fname', $ds_fname, 50, 35, $ds_fname_err, False, '', True, '', '', "form-group", 'left', 'col col-sm-12', 'col col-sm-12');
                        ?>
                        </div>
                        <div class="col col-xs-12 col-sm-4">
                        <?php
                        Forma_CampoTexto(ObtenEtiqueta(119), False, 'ds_mname', $ds_mname, 50, 35,'', False, '', True, '', '', "form-group", 'left', 'col col-sm-12', 'col col-sm-12');
                        ?>
                        </div>
                        <div class="col col-xs-12 col-sm-4">
                        <?php
                        Forma_CampoTexto(ObtenEtiqueta(118), True, 'ds_lname', $ds_lname, 50, 35, $ds_lname_err, False, '', True, '', '', "form-group", 'left', 'col col-sm-12', 'col col-sm-12');
                        $ruta = PATH_ALU_IMAGES."/id/";
                        ?>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col col-xs-12 col-sm-4">
                        
                        <?php
                        $ruta = PATH_ALU_IMAGES."/id";                        
                        Forma_CampoUpload(ObtenEtiqueta(810), '', 'ds_ruta_foto', $ds_ruta_foto,$ruta,True,'ds_ruta_foto',60, !empty($ds_ruta_foto_err)?$ds_ruta_foto_err:NULL, 'jpg|jpeg', '', 'left', 'col col-sm-12 col-lg-12 col-xs-12 col-md-12', 'col col-sm-12 col-lg-12');                       
                        ?>
                        </div>
                        <div class="col col-xs-12 col-sm-4">
                        <?php
                        Forma_CampoTexto(ObtenEtiqueta(631), False, 'ds_p_name', $ds_p_name, 50, 20, $ds_p_name_err, False, '', True, '', '', "form-group", 'left', 'col col-sm-12', 'col col-sm-12');
                        ?>
                        </div>
                        <div class="col col-xs-12 col-sm-4">
                        <?php
                        Forma_CampoTexto(ObtenEtiqueta(887), False, 'ds_usual_name', $ds_usual_name, 100, 20, $ds_usual_name_err, False, '', True, '', '', "form-group", 'left', 'col col-sm-12', 'col col-sm-12');
                        ?>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col col-xs-12 col-sm-4">
                        <?php
                        Forma_CampoTexto(ObtenEtiqueta(632), False, 'ds_education_number', $ds_education_number, 50, 20, $ds_education_number_err, False, '', True, '', '', "form-group", 'left', 'col col-sm-12', 'col col-sm-12');
                        ?>
                        </div>
                        <div class="col col-xs-12 col-sm-4">
                        <?php
                        Forma_CampoTexto(ObtenEtiqueta(280), True, 'ds_number', $ds_number, 20, 20, !empty($ds_number_err)?$ds_number_err:NULL, False, '', True, '', '', "form-group", 'left', 'col col-sm-12', 'col col-sm-12');
                        ?>
                        </div>
                        <div class="col col-xs-12 col-sm-4">
                        <?php
                        Forma_CampoTexto(ObtenEtiqueta(281), False, 'ds_alt_number', $ds_alt_number, 20, 20, $ds_alt_number_err, False, '', True, '', '', "form-group", 'left', 'col col-sm-12', 'col col-sm-12');
                        ?>
                        </div>
                      </div>
                      <div class="row">                        
                        <div class="col col-xs-12 col-sm-4">
                        <?php
                        Forma_CampoTexto(ObtenEtiqueta(121),True,'ds_email',$ds_email,50,40,$ds_email_err, False, '', True, '', '', "form-group", 'left', 'col col-sm-12', 'col col-sm-12');
                        ?>
                        </div>
                        <div class="col col-xs-12 col-sm-4">
                        <?php
                        Forma_CampoTexto(ObtenEtiqueta(127),False,'ds_a_email',$ds_a_email,50,40,$ds_a_email_err, False, '', True, '', '', "form-group", 'left', 'col col-sm-12', 'col col-sm-12');
                        ?>
                        </div> 
                        <div class="col col-xs-12 col-sm-4">
                        <?php
                        Btstrp_Forma_CampoTextArea("<strong>".ObtenEtiqueta(339)."</strong>", False, 'ds_link_to_portfolio', $ds_link_to_portfolio, 0, 3);
                        
                        //Forma_CampoTexto(str_uso_normal(ObtenEtiqueta(339)),False,'ds_link_to_portfolio', $ds_link_to_portfolio, 255, 40,'', False, '', True, '', '', "form-group", 'left', 'col col-sm-12 col-md-12 col-lg-12 col-xs-12', 'col col-sm-12');
                        ?>
                        </div>
                      </div>
                      <div class="row">
                        
                        <div class="col col-xs-12 col-sm-4">
                            <?php
                        //if($fg_gender == 'M')
                        //  Forma_CampoInfo(ObtenEtiqueta(114), ObtenEtiqueta(115), 'left', 'col col-sm-12', 'col col-sm-12');
                        //else
                        //  Forma_CampoInfo(ObtenEtiqueta(114), ObtenEtiqueta(116), 'left', 'col col-sm-12', 'col col-sm-12');

                        $opc = array(ObtenEtiqueta(115), ObtenEtiqueta(116),"Non-Binary"); // Masculino, Femenino
                        $val = array('M', 'F','N');
                        Forma_CampoSelect(ObtenEtiqueta(114), False, 'fg_gender', $opc, $val, $fg_gender,'','','','left','col col-sm-12','col col-sm-12');
                            ?>
                        </div>
                        <div class="col col-xs-12 col-sm-4">
                        <?php
                        Forma_CampoTexto(ObtenEtiqueta(120),True,'fe_birth',$fe_birth,10,10, !empty($fe_birth_err)?$fe_birth_err:NULL, False, '', True, '', '', "form-group", 'left', 'col col-sm-12', 'col col-sm-12');
                        Forma_Calendario('fe_birth');
                        ?>
                        </div>
                      </div>
                      <script>
                      function mostrar_ocultar(tipo){    
                        if(tipo==1){
                          var international = $("input[name='fg_international']:checked").val();
                          if(international == 'on'){
                            $('#international').attr('style', 'display:inline;');
                            $('#international_ppt').attr('style', 'display:inline;');
                          }
                          else{
                            $('#international').attr('style', 'display:none;');
                            $('#international_ppt').attr('style', 'display:none;');
                              
                          }
                        }
                        if(tipo==2){
                          var international = $("input[name='fg_study_permit']:checked").val();
						  
                          if(international != 'on'){
						    
                            $('#other_permit').attr('style', 'display:inline;');
							$('#permisso').attr('style','display:none;');
							$('#photo_permiso').attr('style','display:none;');
							
							
                          }
                          else{
                            $('#other_permit').attr('style', 'display:none;'); 
							$('#permisso').attr('style','display:inline;');	
							$('#photo_permiso').attr('style','display:inline;');									
                          }
						  
						  
                        }
                        if(tipo==3){
                          var aboriginal = $("input[name='fg_aboriginal']:checked").val();
                          if(aboriginal == 'on'){
                            $('#ds_aboriginal_ppt').attr('style', 'display:inline;');
                          }
                          else{
                            $('#ds_aboriginal_ppt').attr('style', 'display:none;');   
                          }
                        }
                        if(tipo==4){
                          var health = $("input[name='fg_health_condition']:checked").val();
                          if(health == 'on'){      
                            $('#fg_health_ppt').attr('style', 'display:inline;');
                            $('#fg_health').attr('style', 'display:inline;');
                          }
                          else{
                            $('#fg_health_ppt').attr('style', 'display:none;');
                            $('#fg_health').attr('style', 'display:none;');
                          }
                        }



                        if (tipo == 5) {
                            var disabilty = $("input[name='fg_disabilityie']:checked").val();
                            if (disabilty == 'on') {

                                $('#fg_disability_ppt').attr('style', 'display:inline;');
                                $('#fg_disability').attr('style', 'display:inline;');
                            } else {

                                $('#fg_disability_ppt').attr('style', 'display:none;');
                                $('#fg_disability').attr('style', 'display:none;');
                            }
                        }


                      }
                      </script>
                      <div class="row">
                        <div class="col-md-4">
                            <?php

                        if($fl_pais_selected<>38){

                            $Query  = "SELECT CONCAT(no_code,'-',description)immigration, fl_immigrations_status FROM immigrations_status_locale where fl_pais=$fl_pais_selected  ORDER BY fl_immigrations_status ASC ";

                            }else{

                            $Query  = "SELECT CONCAT(no_code,'-',description)immigration, fl_immigrations_status FROM immigrations_status  ORDER BY fl_immigrations_status ASC ";


                        }
                        Forma_CampoSelectBD(ObtenEtiqueta(2663), False, 'fl_immigrations_status', $Query, $fl_immigrations_status, $fl_immigrations_status_err, True, '', 'left', 'col col-sm-12', 'col col-sm-12');


                            ?><br />
                        </div>
                      </div>
                      <div class="row">
                      <div class="col-xs-12 col-sm-4 no-padding padding-bottom-10">
                          <?php

                        if($fl_pais_selected==226){
                           $etq_intern=html_entity_decode(ObtenEtiqueta(2676));
                          }else{
                            $etq_intern= html_entity_decode(ObtenEtiqueta(620));
                        }

                        Forma_CampoCheckbox('','fg_international',$fg_international, $etq_intern, '', True, " onClick='javascript:mostrar_ocultar(1);'", 'left', 'col-sm-12 no-padding', 'col-sm-12 no-padding');
                          ?>
                      </div>
                      <div class="col-xs-12 col-sm-4">
                        <?php
                        if($fg_international == 1)
                          $international = True;
                        else
                          $international = False;
                        Forma_CampoTexto(ObtenEtiqueta(1024),True,'ds_citizenship',$ds_citizenship, 50,0, !empty($ds_citizenship_err)?$ds_citizenship_err:NULL, False, 'international', $international, '', '', "form-group", 'left', 'col col-sm-12', 'col col-sm-12');
                        ?>
                      </div>
                      <div class="col-xs-12 col-sm-4">
                        <?php
                        Forma_CampoTexto('Social Insurance Number',True,'ds_sin',!empty($ds_sin)?$ds_sin:0, 50,0, !empty($ds_sin_err)?$ds_sin_err:NULL, False, 'ds_sin', True, '', '', "form-group", 'left', 'col col-sm-12', 'col col-sm-12');
                        ?>
                      </div>
                      </div>
                      <div class="row">
					  
                      <div class="col-xs-12 col-sm-5 no-padding">
                        <?php
                        Forma_CampoCheckbox('','fg_study_permit',$fg_study_permit, ObtenEtiqueta(1025), '', True, " onClick='javascript:mostrar_ocultar(2);'", 'left', 'col-sm-12 no-padding', 'col-sm-12 no-padding');                         
                        if($fg_study_permit=='0'){
						
                           $permit = 'inline';
                        }else{
						
                           $permit = 'none';
						}
                        ?>
                      </div>  

					   
						
						
                      <div class="col-xs-12 col-sm-5" id="other_permit" style="display:<?php echo $permit; ?>;">
                        <?php                        
                        Forma_CampoCheckbox('','fg_study_permit_other',$fg_study_permit_other, ObtenEtiqueta(1026), '', True, '', 'left', 'col-sm-12 no-padding', 'col-sm-12 no-padding');
                        ?>
                      </div>
					  
					  

                      </div>


                   




					  
					  <?php if($permit){ ?>
					  
					  <div class="row "   id="permisso">
						  <div class="col-xs-12 col-sm-3">
							<?php 
								Forma_CampoTexto(ObtenEtiqueta(2180),True,'fe_start_date',$fe_start_date,10,10, !empty($fe_start_date_err)?$fe_start_date_err:NULL, False, '', True, '', '', "form-group", 'left', 'col col-sm-12', 'col col-sm-12');
								Forma_Calendario('fe_start_date');
							?>
						  </div>
						  <div class="col-xs-12 col-sm-3">
							 <?php 
							 
								Forma_CampoTexto(ObtenEtiqueta(2181),True,'fe_expirity_date',$fe_expirity_date,10,10, !empty($fe_expirity_date_err)?$fe_expirity_date_err:NULL, False, '', True, '', '', "form-group", 'left', 'col col-sm-12', 'col col-sm-12');
								Forma_Calendario('fe_expirity_date');
							 ?>
						  </div>
						  
						  <div class="col-xs-12 col-sm-6">
							 <?php
							 
								Forma_CampoTexto(ObtenEtiqueta(2182),True,'nb_name_institutcion',$nb_name_institutcion, 50,0, !empty($nb_name_institutcion_err)?$nb_name_institutcion_err:NULL, False, 'nb_name_institutcion', $nb_name_institutcion, '', '', "form-group", 'left', 'col col-sm-12', 'col col-sm-12');

							 ?>
						  </div>
					  </div>
						  <div class="row" id="photo_permiso">
						  
							  <div class="col-xs-12 col-sm-4">
								<?php 
									$ruta_permiso = PATH_ALU_IMAGES."/id";                        
									Forma_CampoUpload(ObtenEtiqueta(2183), '', 'ds_ruta_foto_permiso', $ds_ruta_foto_permiso,$ruta_permiso,True,'ds_ruta_foto_permiso',60, !empty($ds_ruta_foto_permiso_err)?$ds_ruta_foto_permiso_err:NULL, 'jpg|jpeg', '', 'left', 'col col-sm-12 col-lg-12 col-xs-12 col-md-12', 'col col-sm-12 col-lg-12');                       
								
								?>
							  </div>
						  
						  </div>
						  
					 <?php } ?>	  
						  
						<script> mostrar_ocultar(2); </script>  
					  
					  
                    </div>
                    <div class="col col-xs-12 col-sm-10 col-lg-12">
                      <?php
                      # Direccion                    
                      Forma_Seccion(ObtenEtiqueta(62));
                      ?>
                      <div class="row">
                        <div class="col col-xs-12 col-sm-4">
                        <?php                        
                        Forma_CampoTexto(ObtenEtiqueta(282), True, 'ds_add_number', $ds_add_number, 20, 16, $ds_add_number_err, False, '', True, '', '', "form-group", 'left', 'col col-sm-12', 'col col-sm-12');
                        ?>
                        </div>
                        <div class="col col-xs-12 col-sm-4">
                        <?php
                        Forma_CampoTexto(ObtenEtiqueta(283), True, 'ds_add_street', $ds_add_street, 50, 32, $ds_add_street_err, False, '', True, '', '', "form-group", 'left', 'col col-sm-12', 'col col-sm-12');
                        ?>
                        </div>
                        <div class="col col-xs-12 col-sm-4">
                        <?php
                        Forma_CampoTexto(ObtenEtiqueta(284), True, 'ds_add_city', $ds_add_city, 50, 32, $ds_add_city_err, False, '', True, '', '', "form-group", 'left', 'col col-sm-12', 'col col-sm-12');
                        ?>
                        </div>
                        <div class="col col-xs-12 col-sm-4">
                        <?php
                        Forma_CampoSelectCombinado("ds_add_state", True, $ds_add_state, 50, 16, $ds_add_country, "ds_add_country", $ds_add_state_err, 'form-group', 'left', 'col col-sm-12', 'col col-sm-12');  
                        ?>
                        </div>
                        <div class="col col-xs-12 col-sm-4">
                        <?php
                        Forma_CampoTexto(ObtenEtiqueta(286), True, 'ds_add_zip', $ds_add_zip, 20, 16, $ds_add_zip_err, False, '', True, '', '', "form-group", 'left', 'col col-sm-12', 'col col-sm-12');
                        ?>
                        </div>
                        <div class="col col-xs-12 col-sm-4">
                        <?php
                        $Query  = "SELECT ds_pais, fl_pais FROM c_pais ORDER BY ds_pais";
                        Forma_CampoSelectBD(ObtenEtiqueta(287), True, 'ds_add_country', $Query, $ds_add_country, $ds_add_country_err, True, '', 'left', 'col col-sm-12', 'col col-sm-12');
                        ?>
                        </div>
                      </div>
                    </div>
                    <div class="col col-xs-12 col-sm-10 col-lg-12">
                      <?php
                      # Direccion de envio de correspondencia
                      Forma_Seccion(ObtenEtiqueta(633));
                      ?>
                      <div class="row">
                        <div class="col col-xs-12 col-sm-4">
                        <?php
                        Forma_CampoTexto(ObtenEtiqueta(282), False, 'ds_m_add_number', $ds_m_add_number, 20, 16, '', False, '', True, '', '', "form-group", 'left', 'col-sm-12', 'col-sm-12');
                        ?>
                        </div>
                        <div class="col col-xs-12 col-sm-4">
                        <?php
                        Forma_CampoTexto(ObtenEtiqueta(283), False, 'ds_m_add_street', $ds_m_add_street, 50, 32, '', False, '', True, '', '', "form-group", 'left', 'col-sm-12', 'col-sm-12');
                        ?>
                        </div>
                        <div class="col col-xs-12 col-sm-4">
                        <?php
                        Forma_CampoTexto(ObtenEtiqueta(284), False, 'ds_m_add_city', $ds_m_add_city, 50, 32, '', False, '', True, '', '', "form-group", 'left', 'col-sm-12', 'col-sm-12');
                        ?>
                        </div>
                        <div class="col col-xs-12 col-sm-4">
                        <?php
                        Forma_CampoTexto(ObtenEtiqueta(285), False, 'ds_m_add_state', $ds_m_add_state, 50, 32, '', False, '', True, '', '', "form-group", 'left', 'col-sm-12', 'col-sm-12');
                        ?>
                        </div>
                        <div class="col col-xs-12 col-sm-4">
                        <?php
                        Forma_CampoTexto(ObtenEtiqueta(286), False, 'ds_m_add_zip', $ds_m_add_zip, 20, 16, '', False, '', True, '', '', "form-group", 'left', 'col-sm-12', 'col-sm-12');
                        ?>
                        </div>
                        <div class="col col-xs-12 col-sm-4">
                        <?php
                        $Query  = "SELECT ds_pais, fl_pais FROM c_pais ORDER BY ds_pais";
                        Forma_CampoSelectBD(ObtenEtiqueta(287), False, 'ds_m_add_country', $Query, $ds_m_add_country, '', True, '', 'left', 'col col-md-12', 'col col-md-12');
                        ?>
                        </div>
                      </div>
                    </div>                    
                    <div class="col col-xs-12 col-sm-10 col-lg-12">
                      <?php
                      # Person Resposible
                      Forma_Seccion(ObtenEtiqueta(865));
                      ?>
                      <div class="row">
                        <div class="col col-xs-12 col-sm-12">
                        <?php
                        if(ExisteEnTabla('k_presponsable', 'cl_sesion', $cl_sesion)){
                          if(!empty($fg_email)){
                            $info = ObtenEtiqueta(831);
                          }
                          else{
                            $info = ObtenEtiqueta(832);
                          }
                        }
                        else{
                          $info = ObtenEtiqueta(833);
                        }
                        Forma_CampoCheckbox('', 'fg_responsable', $fg_responsable, ObtenEtiqueta(866)." (Off) / (On) ".ObtenEtiqueta(867)." <small class='text-danger'>(".$info.")</small>", '', True, '', 'left', 'col-sm-12', 'col-sm-12');                        
                        Forma_CampoOculto('fg_email', !empty($fg_email)?$fg_email:NULL);
                        ?>
                        </div>
                        <div id="person_responsable" style="display: <?php if(empty($fg_responsable)) echo "none"; else echo "inline"; ?>;">
                          <div class="col col-xs-12 col-sm-4">
                          <?php
                          Forma_CampoTexto(ObtenEtiqueta(868), True, 'ds_fname_r', $ds_fname_r, 50, 32, $ds_fname_r_err, False, '', True, '', '', "smart-form form-group", 'left', 'col col-sm-12', 'col col-sm-12');
                          ?>
                          </div>
                          <div class="col col-xs-12 col-sm-4">
                          <?php
                          Forma_CampoTexto(ObtenEtiqueta(869), True, 'ds_lname_r', $ds_lname_r, 50, 32, $ds_lname_r_err, False, '', True, '', '', "smart-form form-group", 'left', 'col col-sm-12', 'col col-sm-12');
                          ?>
                          </div>
                          <div class="col col-xs-12 col-sm-4">
                          <?php
                          Forma_CampoTexto(ObtenEtiqueta(870), True, 'ds_email_r', $ds_email_r, 50, 32, $ds_email_r_err, False, '', True, '', '', "smart-form form-group", 'left', 'col col-sm-12', 'col col-sm-12');
                          ?>
                          </div>
                          <div class="col col-xs-12 col-sm-4">
                          <?php
                          Forma_CampoTexto(ObtenEtiqueta(871), False, 'ds_aemail_r', $ds_aemail_r, 50, 32, $ds_aemail_r_err, False, '', True, '', '', "smart-form form-group", 'left', 'col col-sm-12', 'col col-sm-12');
                          ?>
                          </div>
                          <div class="col col-xs-12 col-sm-4">
                          <?php
                          Forma_CampoTexto(ObtenEtiqueta(872), True, 'ds_pnumber_r', $ds_pnumber_r, 50, 32, $ds_pnumber_r_err, False, '', True, '', '', "smart-form form-group", 'left', 'col col-sm-12', 'col col-sm-12');
                          ?>
                          </div>
                          <div class="col col-xs-12 col-sm-4">
                        <?php
                        Forma_CampoTexto(ObtenEtiqueta(873), True, 'ds_relation_r', $ds_relation_r, 50, 32, $ds_relation_r_err, False, '', True, '', '', "smart-form form-group", 'left', 'col col-sm-12', 'col col-sm-12');
                        ?>
                        </div>
                        </div>
                      </div>
                    </div>
                    
                    <div class="col col-xs-12 col-sm-10 col-lg-12">

                        <?php if($fl_pais_selected==38){ ?>


                          <?php
                          # Voluntary Disclosure
                          Forma_Seccion(ObtenEtiqueta(1034));
                          ?>
                          <div class="row">
                            <div class="col col-xs-12 col-sm-5">
                            <?php
                            Forma_CampoCheckbox('','fg_aboriginal',$fg_aboriginal, ObtenEtiqueta(1027), '', True, " onClick='javascript:mostrar_ocultar(3);'", 'left', 'col-sm-12 no-padding', 'col-sm-12 no-padding');
                            if($fg_aboriginal ==1){
                              $aboriginal = "inline";
                            }else{
                              $aboriginal = "none";
                               }
                            ?>
                            </div>
                            <div class="col col-xs-12 col-sm-5" id="ds_aboriginal_ppt" style="display:<?php echo $aboriginal; ?>;">
                              <div class="smart-form">
                                <section>
                                    <div class="row">
                                      <label class="label"><strong><?php echo ObtenEtiqueta(1028); ?></strong></label>
                                      <?php 
                                      echo "<div class='col col-4'><label class='radio' style='padding-top:0px;'>";
                                      CampoRadio('ds_aboriginal', ObtenEtiqueta(1029), $ds_aboriginal, ObtenEtiqueta(1029));
                                      echo "</label></div>
                                      <div class='col col-4'><label class='radio' style='padding-top:0px;'>";
                                      CampoRadio('ds_aboriginal', ObtenEtiqueta(1030), $ds_aboriginal, ObtenEtiqueta(1030));
                                      echo "</label></div>
                                      <div class='col col-4'><label class='radio' style='padding-top:0px;'>";
                                      CampoRadio('ds_aboriginal', ObtenEtiqueta(1031), $ds_aboriginal, ObtenEtiqueta(1031));
                                      echo "</label></div>";
                                      ?> 
                                    </div>
                                </section>
                              </div>
                            </div>
                          </div>


                          <div class="row">
                            <div class="col col-xs-12 col-sm-5">
                            <?php
                            Forma_CampoCheckbox('','fg_health_condition',$fg_health_condition, ObtenEtiqueta(1032), '', True, " onClick='javascript:mostrar_ocultar(4);'", 'left', 'col-sm-12 no-padding', 'col-sm-12 no-padding');
                            ?>
                            </div>
                            <div class="col-xs-12 col-sm-5">
                            <?php
                            if($fg_health_condition ==1){
                              $health = True;
                            }else{
                              $health = False;
                            }
                            Forma_CampoTexto(ObtenEtiqueta(1033),True,'ds_health_condition',$ds_health_condition, 50,0, !empty($ds_health_condition_err)?$ds_health_condition_err:NULL, False, 'fg_health', $health, '', '', "form-group", 'left', 'col col-sm-12', 'col col-sm-12');
                            ?>
                            </div>
                          </div>

                      <?php } ?>

                      <?php if($fl_pais_selected==226){
                                Forma_Seccion();?>

                            
                            <div class="row">
                                <div class="col col-xs-12 col-sm-4">
                                    <?php
                                    Forma_CampoInfo('Race ', $race, 'left', 'col-sm-12', 'col-sm-12');
                                    ?>
                                </div>
                                <div class="col col-xs-12 col-sm-4">
                                    <?php
                                    Forma_CampoInfo('Are you Hispanic in origin? ', $hispanic, 'left', 'col-sm-12', 'col-sm-12');
                                    ?>
                                </div>
                                <div class="col col-xs-12 col-sm-4">
                                    <?php
                                    Forma_CampoInfo('Are you disabled? ', $fg_disability, 'left', 'col-sm-12', 'col-sm-12');
                                    ?>
                                </div>
                                <div class="col col-xs-12 col-sm-4">
                                    <?php
                                    Forma_CampoInfo('Are you a military veteran? ', $military, 'left', 'col-sm-12', 'col-sm-12');
                                    ?>
                                </div>
                                <div class="col col-xs-12 col-sm-4">
                                    <?php
                                    Forma_CampoInfo('Highest grade completed: ', $grade, 'left', 'col-sm-12', 'col-sm-12');
                                    ?>
                                </div>
                            </div>


                      <?php } ?>



                      <?php if($fl_pais_selected==38){ ?>  
                          <div class="row">
                            <div class="col col-xs-12 col-sm-5">
                            <?php
                            Forma_CampoCheckbox('','fg_disabilityie',$fg_disabilityie, ObtenEtiqueta(1778), '', True, " onClick='javascript:mostrar_ocultar(5);'", 'left', 'col-sm-12 no-padding', 'col-sm-12 no-padding');
                            ?>
                            </div>
                            <div class="col-xs-12 col-sm-5">
                            <?php
                            if($fg_disabilityie ==1)
                                $disability = True;
                            else
                                $disability  = False;                        
                            Forma_CampoTexto(ObtenEtiqueta(1779),True,'ds_disability',$ds_disability, 50,0, !empty($ds_disability_err)?$ds_disability_err:NULL, False, 'fg_disability', $disability, '', '', "form-group", 'left', 'col col-sm-12', 'col col-sm-12');
                            ?>
                            </div>
                          </div>

                    <?php } ?>


                    </div>
                    <div class="col col-xs-12 col-sm-10 col-lg-12">
                      <?php
                      # Contacto de emergencia
                      Forma_Seccion(ObtenEtiqueta(63));
                      ?>
                      <div class="row">
                        <div class="col col-xs-12 col-sm-4">
                        <?php
                        Forma_CampoInfo(ObtenEtiqueta(117), $ds_eme_fname, 'left', 'col-sm-12', 'col-sm-12');
                        ?>
                        </div>
                        <div class="col col-xs-12 col-sm-4">
                        <?php
                        Forma_CampoInfo(ObtenEtiqueta(118), $ds_eme_lname, 'left', 'col-sm-12', 'col-sm-12');
                        ?>
                        </div>
                        <div class="col col-xs-12 col-sm-4">
                        <?php
                        Forma_CampoInfo(ObtenEtiqueta(280), $ds_eme_number, 'left', 'col-sm-12', 'col-sm-12');
                        ?>
                        </div>
                        <div class="col col-xs-12 col-sm-4">
                        <?php
                        Forma_CampoInfo(ObtenEtiqueta(288), $ds_eme_relation, 'left', 'col-sm-12', 'col-sm-12');
                        ?>
                        </div>
                        <div class="col col-xs-12 col-sm-4">
                        <?php
                        Forma_CampoInfo(ObtenEtiqueta(287), $ds_eme_country, 'left', 'col-sm-12', 'col-sm-12');
                        ?>
                        </div>
                        <div class="col col-xs-12 col-sm-4">
                        <?php
                        # Informacion de referencia
                        switch($fg_ori_via) {
                          case 'A': Forma_CampoInfo(ObtenEtiqueta(289), ObtenEtiqueta(290), 'left', 'col-sm-12', 'col-sm-12'); break;
                          case 'B': Forma_CampoInfo(ObtenEtiqueta(289), ObtenEtiqueta(291), 'left', 'col-sm-12', 'col-sm-12'); break;
                          case 'C': Forma_CampoInfo(ObtenEtiqueta(289), ObtenEtiqueta(292), 'left', 'col-sm-12', 'col-sm-12'); break;
                          case 'D': Forma_CampoInfo(ObtenEtiqueta(289), ObtenEtiqueta(293), 'left', 'col-sm-12', 'col-sm-12'); break;
                          case '0': Forma_CampoInfo(ObtenEtiqueta(289), ObtenEtiqueta(294)." - $ds_ori_other", 'left', 'col-sm-12', 'col-sm-12'); break;
                        }
                        ?>
                        </div>
                        <div class="col col-xs-12 col-sm-4">
                        <?php
                        switch($fg_ori_ref) {
                          case '0': Forma_CampoInfo(ObtenEtiqueta(295), ObtenEtiqueta(17), 'left', 'col-sm-12', 'col-sm-12'); break;
                          case 'S': Forma_CampoInfo(ObtenEtiqueta(295), ObtenEtiqueta(296)." - $ds_ori_ref_name", 'left', 'col-sm-12', 'col-sm-12'); break;
                          case 'T': Forma_CampoInfo(ObtenEtiqueta(295), ObtenEtiqueta(297)." - $ds_ori_ref_name", 'left', 'col-sm-12', 'col-sm-12'); break;
                          case 'G': Forma_CampoInfo(ObtenEtiqueta(295), ObtenEtiqueta(298)." - $ds_ori_ref_name", 'left', 'col-sm-12', 'col-sm-12'); break;
                          case 'A': Forma_CampoInfo(ObtenEtiqueta(295), ObtenEtiqueta(811)." - $ds_ori_ref_name", 'left', 'col-sm-12', 'col-sm-12'); break;
                        }                    
                        ?>
                        </div>                      
                      </div>
                    </div>
                  </div>
                  <div class="tab-pane fade" id="communicationsHistory">
                  <?php
                  //  Estos hidden se utilzan para el envio de los correo
                  echo "
                  <input type='hidden' id='fl_sesion' name='fl_sesion' value='$clave'>  
                  <input type='hidden' id='programa' name='programa' value='applications_frm.php'>";                 
                  #Tabla de los templates enviados al alumno
                  if(ExisteEnTabla('k_alumno_template', 'fl_alumno', $clave)){
                    $titulos = array('Subject|center', 'Date|center','','');
                    //$ancho_col = array('15%', '12%','3%','3%');
                    Forma_Tabla_Ini('100%', $titulos, array());
                    $Query  = "SELECT nb_template, fe_envio, a.fl_template, fl_alumno_template FROM k_template_doc a, k_alumno_template b ";
                    $Query .= "WHERE a.fl_template=b.fl_template AND fl_alumno=$clave ORDER BY fe_envio DESC";
                    $rs = EjecutaQuery($Query);
                    echo "</tbody>";
                    for($i=0;$row=RecuperaRegistro($rs);$i++){
                      if($i % 2 == 0)
                        $clase = "css_tabla_detalle";
                      else
                        $clase = "css_tabla_detalle_bg";
                      echo "
                      <tr>
                        <td align='center'>".$row[0]."</td>
                        <td align='center'>".$row[1]."</td>
                        <td><a href='viewemail.php?fl_alumno_template=".$row[3]."&fl_sesion=".$clave."' title='".ObtenEtiqueta(487)."'><i class='fa fa-file-pdf-o fa-2x'></i></a></td>
                        <td><a href=\"javascript:borrar_template('template_delete.php',$row[3],$clave,'".ObtenProgramaActual()."');\"' title='".ObtenEtiqueta(487)."'><i class='fa fa-trash-o fa-2x'></i></a></td>
                      </tr>";
                    }
                    echo "</tbody>";
                    Forma_Tabla_Fin();
                    Forma_Espacio(); 
                  }
                  ?>
                  </div>
                  <div class="tab-pane fade" id="payments">
				  
					   <?php //if($fg_international==1){ ?>

							<div class="row">
								<div class="col-sm-12 no-padding text-right">

									 <?php 
                                     
                                     

                                     Forma_CampoCheckbox2('','fg_aplicar_international',$fg_aplicar_international, "".ObtenEtiqueta(2317)."", '', True, "", 'right', 'col-sm-12 padding-15', 'col-sm-12 padding-15');
							         
                                     
                                     Forma_CampoCheckbox2('','fg_scholarship',$fg_scholarship, "Scholarship/discount", '', True, "", 'right', 'col-sm-12 padding-15', 'col-sm-12 padding-15');
							         
                                   
                                     
                                     
                                     
                                     function Forma_CampoCheckbox2($p_prompt, $p_nombre, $p_valor, $p_texto='', $p_regresa='', $p_editar=True, $p_script='', $align_propmt='right', $col_sm_promt='col-sm-4', $col_sm_cam='col-sm-4') {
                                         
                                         /*echo "
                                         <tr>
                                         <td align='right' valign='middle' class='css_prompt'>$p_prompt:</td>
                                         <td align='left' valign='middle'>";
                                         CampoCheckbox($p_nombre, $p_valor, $p_texto, $p_regresa, $p_editar, $p_script);
                                         echo "</td>
                                         </tr>\n";*/
                                         echo "
                                                    <div class='row form-group smart-form '>
                                                    <label class='$col_sm_promt control-label text-align-$align_propmt'>
                                                        <strong>&nbsp;</strong>
                                                    </label>
                                                    <div class='$col_sm_cam'>
                                                        <div class='checkbox'>
                                                        <label>";
                                         
                                                         CampoCheckbox2($p_nombre, $p_valor, $p_texto, $p_regresa, $p_editar, $p_script);
                                         echo "
                                                        </label>
                                                        </div>
                                                    </div>     
                                                    </div>";
                                     }
                                     
                                     
                                     function CampoCheckbox2($p_nombre, $p_valor, $p_texto='', $p_regresa='', $p_editar=True, $p_script='') {
                                         
                                         echo "<span style='margin-right:22px;'>&nbsp;</span><input class='checkbox' type='checkbox' id='$p_nombre' name='$p_nombre'";
                                         if(!empty($p_regresa)) echo " value='$p_regresa'";
                                         if($p_valor == 1) echo " checked";
                                         if($p_editar == False) echo " disabled=disabled";
                                         if(!empty($p_script)) echo " $p_script";
                                         echo "> <span >$p_texto</span>";
                                     }

                                     
                                     
                                     
                                     
                                     
                                     ?>
								</div>

							</div>

						<?php // } ?>
				  
				  
				  
				  
                      <?php
                  # Datos de Costos y formas de pago
                  Forma_Seccion(ObtenEtiqueta(580));
                  Forma_Espacio( );
                  Forma_Doble_Ini( );
                  //Div_Start_Responsive();
                  echo "
                  <table border='".D_BORDES."' width='100%' cellpadding='3' cellspacing='0' class='table table-striped table-hover dataTable no-footer has-columns-hidden'>
                    <tr>
                      <td colspan='3' align='center' style='background-color:#0092cd; color:#fff;'>".ObtenEtiqueta(581)."</td>
                    </tr>
                    <tr class='css_tabla_detalle'>";
                  if($fl_pais_selected==226){
                      echo"   <td align='left' style='font-weight:bold'>Program costs in USA Dollars (USD)</td>";
                          $simbol = "$";
                  }
                    if ($fl_pais_selected == 199|| $fl_pais_selected == 73|| $fl_pais_selected == 80|| $fl_pais_selected == 105 || $fl_pais_selected == 153) {
                        echo "   <td align='left' style='font-weight:bold'>Program costs in Euro (EUR)</td>";
                          $simbol = "&euro;";
                    }
                      if ($fl_pais_selected == 225) {
                          echo "   <td align='left' style='font-weight:bold'>Program costs in Pound Sterling (GBP)</td>";
                          $simbol = "&pound;";
                      }


                  if($fl_pais_selected == 38) {
                          echo"   <td align='left' style='font-weight:bold'>".ObtenEtiqueta(582)."</td>";
                          $simbol = "$";
                  }


                      echo"   <td align='left'>&nbsp;</td>
                      <td align='center' style='font-weight:bold'>".ObtenEtiqueta(583)."</td>
                    </tr>
                    <tr class='css_tabla_detalle_bg'>
                      <td align='left' style='padding-left:40px'>".ObtenEtiqueta(584)."</td>
                      <td align='left'>&nbsp;</td>
                      <td align='right'>";
                      echo "<div class='row form-group'>
                        <div class='col col-sm-10 col-sm-offset-1 padding-top-10'>
                          <div class='input-group'>";
                            CampoTexto('app_fee', $app_fee, 10, 10, 'form-control', False, 'style="text-align:right" readonly="readonly"');
                      echo "<span class='input-group-addon'>$simbol</span>
                            </div>
                          </div>
                        </div>";
                      echo "
                      </td>
                    </tr>
                    <tr class='css_tabla_detalle'>
                      <td align='left' style='padding-left:40px'>".ObtenEtiqueta(585)."</td>
                      <td align='left'>&nbsp;</td>
                      <td align='right'>";
                      echo "<div class='row form-group'>
                        <div class='col col-sm-10 col-sm-offset-1 padding-top-10'>
                          <div class='input-group'>";
                  CampoTexto('tuition', $tuition, 10, 10, 'form-control', False, 'style="text-align:right" readonly="readonly"');
                  echo "<span class='input-group-addon'>$simbol</span>
                            </div>
                          </div>
                        </div>";
                  echo "
                      </td>
                    </tr>
                    <tr class='css_tabla_detalle_bg'>
                      <td align='left' style='padding-left:40px'>".ObtenEtiqueta(586)."</td>
                      <td align='left'>";
                      echo "<div class='row form-group'><div class='input col col-sm-10 col-sm-offset-1 padding-top-10'>";
                  CampoTexto('ds_costos_ad', $ds_costos_ad, 50, 25, 'form-control');
                      echo "</div></div>";
                  echo "
                      </td>
                      <td align='right'>";
                       echo "<div class='row form-group'>
                        <div class='col col-sm-10 col-sm-offset-1 padding-top-10'>
                          <div class='input-group'>";
                  CampoTexto('no_costos_ad', $no_costos_ad, 10, 10, 'form-control', False, 'style="text-align:right" onchange="calcula()"');
                  echo "<span class='input-group-addon'>$simbol</span>
                            </div>
                          </div>
                        </div>";
                  echo "
                      </td>
                    </tr>
                    <tr class='css_tabla_detalle'>
                      <td align='left' style='padding-left:40px'>".ObtenEtiqueta(587)."</td>
                      <td align='left'>";
                      echo "<div class='row form-group'><div class='input col col-sm-10 col-sm-offset-1 padding-top-10'>";
                  CampoTexto('ds_descuento', $ds_descuento, 250, 25, 'form-control');
                  echo "</div></div>";
                  echo "
                      </td>
                      <td align='right'>";
                      echo "<div class='row form-group'>
                        <div class='col col-sm-10 col-sm-offset-1 padding-top-10'>
                          <div class='input-group'>";
                  CampoTexto('no_descuento', $no_descuento, 10, 10, 'form-control', False, 'style="text-align:right" onchange="calcula()"');
                  echo "<span class='input-group-addon'>$simbol</span>
                            </div>
                          </div>
                        </div>";
                  echo "
                      </td>
                    </tr>
                    <tr class='css_tabla_detalle_bg'>
                      <td align='left' style='padding-left:40px'>".ObtenEtiqueta(588)."</td>
                      <td align='left'>&nbsp;</td>
                      <td align='right'>";
                      echo "<div class='row form-group'>
                      <div class='col col-sm-10 col-sm-offset-1 padding-top-10'>
                        <div class='input-group'>";
                  CampoTexto('total_tuition', $total_tuition, 10, 10, 'form-control', False, 'style="text-align:right" readonly="readonly"');
                   echo "<span class='input-group-addon'>$simbol</span>
                            </div>
                          </div>
                        </div>";
                  echo "
                      </td>
                    </tr>
                    <tr class='css_tabla_detalle'>
                      <td align='left' style='font-weight:bold'>".ObtenEtiqueta(589)."</td>
                      <td align='left'>&nbsp;</td>
                      <td align='right' style='font-weight:bold'>";
                      echo "<div class='row form-group'>
                        <div class='col col-sm-10 col-sm-offset-1 padding-top-10'>
                          <div class='input-group'>";
                  CampoTexto('total', $total, 10, 10, 'form-control', False, 'style="text-align:right" readonly="readonly"');
                   echo "<span class='input-group-addon'>$simbol</span>
                            </div>
                          </div>
                        </div>";
                  echo "
                      </td>
                    </tr>
                  </table>";
                  //Div_close_Resposive();
                  Forma_Doble_Fin( );
                  Forma_Espacio( );
                  Forma_Doble_Ini( );
                  //Div_Start_Responsive();
                  echo "
                  <table border='".D_BORDES."' width='100%' cellpadding='3' cellspacing='0' class='table table-striped table-hover dataTable no-footer has-columns-hidden'>
                    <thead>
                    <tr>
                      <td colspan='5' align='center' style='background-color:#0092cd; color:#fff;'>".ObtenEtiqueta(590)."</td>
                    </tr>
                    <tr class='css_tabla_detalle' align='center' style='font-weight:bold;'>
                      <td>".ObtenEtiqueta(591)."</td>
                      <td>".ObtenEtiqueta(592)."</td>
                      <td>".ObtenEtiqueta(593)."</td>
                      <td>".ObtenEtiqueta(595)."</td>
                      <td>".ObtenEtiqueta(596)."</td>
                    </tr>
                    </thead>
                    <tr class='css_tabla_detalle_bg'>
                      <td width='20%' align='center'>";
                  CampoRadio('opc_pago', 1, $opc_pago, 'A', True, 'disabled="disabled"');
                  echo "
                      </td>
                      <td  align='center' ><span id='no_payme_a'> $no_a_payments</span></td>
                      <td  align='left'><span id='ds_payme_a'>$ds_a_freq</span></td>
                      <td  align='right'>";
                      echo "<div class='row form-group'>
                        <div class='col col-sm-10 col-sm-offset-1 padding-top-10'>
                          <div class='input-group'>";
                  CampoTexto('amount_due_a', $amount_due_a, 10, 10, 'form-control', False, 'style="text-align:right" readonly="readonly"');
                  Forma_CampoOculto('no_payments_a', $no_a_payments);
                  Forma_CampoOculto('interes_a', $no_a_interes);
                  echo "<span class='input-group-addon'>$simbol</span>
                            </div>
                          </div>
                        </div>";
                  echo "
                      </td>
                      <td  align='right'>";
                      echo "<div class='row form-group'>
                        <div class='col col-sm-10 col-sm-offset-1 padding-top-10'>
                          <div class='input-group'>";
                  CampoTexto('amount_paid_a', $amount_paid_a, 10, 10, 'form-control', False, 'style="text-align:right" readonly="readonly"');
                  echo "<span class='input-group-addon'>$simbol</span>
                            </div>
                          </div>
                        </div>";
                  echo "
                      </td>
                    </tr>
                    <tr class='css_tabla_detalle'>
                      <td width='20%' align='center'>";
                  CampoRadio('opc_pago', 2, $opc_pago, 'B', True, 'disabled="disabled"');
                  echo "
                      </td>
                      <td  align='center'><span id='no_payme_b'>$no_b_payments</span></td>
                      <td  align='left'><span id='ds_payme_b'>$ds_b_freq</span></td>
                      <td  align='right'>";
                      echo "<div class='row form-group'>
                        <div class='col col-sm-10 col-sm-offset-1 padding-top-10'>
                          <div class='input-group'>";
                  CampoTexto('amount_due_b', $amount_due_b, 10, 10, 'form-control', False, 'style="text-align:right" readonly="readonly"');
                  Forma_CampoOculto('no_payments_b', $no_b_payments);
                  Forma_CampoOculto('interes_b', $no_b_interes);
                  echo "<span class='input-group-addon'>$simbol</span>
                            </div>
                          </div>
                        </div>";
                  echo "
                      </td>
                      <td  align='right'>";
                      echo "<div class='row form-group'>
                        <div class='col col-sm-10 col-sm-offset-1 padding-top-10'>
                          <div class='input-group'>";
                  CampoTexto('amount_paid_b', $amount_paid_b, 10, 10, 'form-control', False, 'style="text-align:right" readonly="readonly"');
                  echo "<span class='input-group-addon'>$simbol</span>
                            </div>
                          </div>
                        </div>";
                  echo "
                      </td>
                    </tr>
                    <tr class='css_tabla_detalle_bg'>
                      <td width='20%' align='center'>";
                  CampoRadio('opc_pago', 3, $opc_pago, 'C', True, 'disabled="disabled"');
                  echo "
                      </td>
                      <td  align='center'><span id='no_payme_c'>$no_c_payments</span></td>
                      <td  align='left'><span id='ds_payme_c'>$ds_c_freq</span></td>
                      <td  align='right'>";
                      echo "<div class='row form-group'>
                        <div class='col col-sm-10 col-sm-offset-1 padding-top-10'>
                          <div class='input-group'>";
                  CampoTexto('amount_due_c', $amount_due_c, 10, 10, 'form-control', False, 'style="text-align:right" readonly="readonly"');
                  Forma_CampoOculto('no_payments_c', $no_c_payments);
                  Forma_CampoOculto('interes_c', $no_c_interes);
                   echo "<span class='input-group-addon'>$simbol</span>
                            </div>
                          </div>
                        </div>";
                  echo "
                      </td>
                      <td  align='right'>";
                      echo "<div class='row form-group'>
                        <div class='col col-sm-10 col-sm-offset-1 padding-top-10'>
                          <div class='input-group'>";
                  CampoTexto('amount_paid_c', $amount_paid_c, 10, 10, 'form-control', False, 'style="text-align:right" readonly="readonly"');
                   echo "<span class='input-group-addon'>$simbol</span>
                            </div>
                          </div>
                        </div>";
                  echo "
                      </td>
                    </tr>
                    <tr class='css_tabla_detalle'>
                      <td width='20%' align='center'>";
                  CampoRadio('opc_pago', 4, $opc_pago, 'D', True, 'disabled="disabled"');
                  echo "
                      </td>
                      <td  align='center'><span id='no_payme_d'>$no_d_payments</span></td>
                      <td  align='left'><span id='ds_payme_d'>$ds_d_freq</span></td>
                      <td  align='right'>";
                      echo "<div class='row form-group'>
                        <div class='col col-sm-10 col-sm-offset-1 padding-top-10'>
                          <div class='input-group'>";
                  CampoTexto('amount_due_d', $amount_due_d, 10, 10, 'form-control', False, 'style="text-align:right" readonly="readonly"');
                  Forma_CampoOculto('no_payments_d', $no_d_payments);
                  Forma_CampoOculto('interes_d', $no_d_interes);
                  echo "<span class='input-group-addon'>$simbol</span>
                            </div>
                          </div>
                        </div>";
                  echo "
                      </td>
                      <td  align='right'>";
                       echo "<div class='row form-group'>
                        <div class='col col-sm-10 col-sm-offset-1 padding-top-10'>
                          <div class='input-group'>";
                  CampoTexto('amount_paid_d', $amount_paid_d, 10, 10, 'form-control', False, 'style="text-align:right" readonly="readonly"');
                  echo "<span class='input-group-addon'>$simbol</span>
                            </div>
                          </div>
                        </div>";
                  echo "
                      </td>
                    </tr>
                  </table>";
                  //Div_close_Resposive();
                  Forma_Doble_Fin( );
                      ?>
                  </div>
                  <div class="tab-pane fade" id="applicationForm">
                    <div class="row">
                      <div class="col-xs-12" style="padding:10px;">

                          <!-- Availability for Online Live Classes in your local time -->
                          <div class="jarviswidget" id="wid-id-2" role="widget2" style="" data-widget-colorbutton="false" data-widget-editbutton="false" data-widget-togglebutton="false" data-widget-deletebutton="false">
                              <header role="heading">
                                <div class="jarviswidget-ctrls" role="menu">  
                                    <a href="javascript:void(0);" class="button-icon jarviswidget-toggle-btn" rel="tooltip" title="" data-placement="bottom" data-original-title="Collapse"><i class="fa fa-minus "></i></a> 
                                </div>
                                <h2>
                                    <strong>
                                    <?php echo ObtenEtiqueta(621); ?>
                                    </strong>
                                </h2>				

                                <span class="jarviswidget-loader"><i class="fa fa-refresh fa-spin"></i></span>
                              </header>

                              <!-- widget div-->
                              <div role="content">

                                  <!-- widget content -->
                                  <div class="widget-body" style="min-height:0; padding-bottom: 0px;">
                                      <div class="row">
                                          <?php
                                          $opc = array(ObtenEtiqueta(624), ObtenEtiqueta(625), ObtenEtiqueta(626), ObtenEtiqueta(627), ObtenEtiqueta(628), ObtenEtiqueta(629), ObtenEtiqueta(630));
                                          $val = array('1', '2', '3', '4', '5', '6', '7');
                                          ?>
                                          <div class="row padding-bottom-10">
                                            
											<?php 
											if(!empty($fl_class_time)){
											   $opc = array($nb_dias); // Horarios
												$val = array($fl_class);
											?>
											
											<div class="col-sm-4">
                                              <?php echo 
											  Forma_CampoSelect(ObtenEtiqueta(2389), True, 'fl_class_time', $opc, $val, $fl_class_time, !empty($fl_class_time_err)?$fl_class_time_err:NULL, True, '', 'left', 'col col-sm-12', 'col col-sm-12');
            
											 // Forma_CampoSelect(ObtenEtiqueta(622), True, 'fl_class', $opc, $val, $cl_preference_1, $cl_preference_1_err, True, '', 'left', 'col col-sm-12', 'col col-sm-12'); ?>
                                            </div>
											
											
											
											
											<?php }else{ ?>
											
											
											<div class="col-sm-4">
                                              <?php echo Forma_CampoSelect(ObtenEtiqueta(622), True, 'cl_preference_1', $opc, $val, $cl_preference_1, $cl_preference_1_err, True, '', 'left', 'col col-sm-12', 'col col-sm-12'); ?>
                                            </div>
                                            <div class="col-sm-4">
                                              <?php echo Forma_CampoSelect(ObtenEtiqueta(623), True, 'cl_preference_2', $opc, $val, $cl_preference_2, $cl_preference_2_err, True, '', 'left', 'col col-sm-12', 'col col-sm-12'); ?>
                                            </div>
                                            <div class="col-sm-4">
                                            <?php echo Forma_CampoSelect(ObtenEtiqueta(616), True, 'cl_preference_3', $opc, $val, $cl_preference_3, $cl_preference_3_err, True, '', 'left', 'col col-sm-12', 'col col-sm-12');   ?>
                                            </div>
											
											<?php } ?>
											
                                          </div>
                                      </div>
                                  </div>
                                  <!-- end widget content -->

                              </div>
                              <!-- end widget div -->
                          </div>
                      </div>
                      <div class="col-xs-12" style="padding:10px;">

                          <!-- 2. Career Assessment  -->
                          <div class="jarviswidget" id="wid-id-3" role="widget3" style="" data-widget-colorbutton="false" data-widget-editbutton="false" data-widget-togglebutton="false" data-widget-deletebutton="false">
                              <header role="heading">
                                <div class="jarviswidget-ctrls" role="menu">  
                                    <a href="javascript:void(0);" class="button-icon jarviswidget-toggle-btn" rel="tooltip" title="" data-placement="bottom" data-original-title="Collapse"><i class="fa fa-minus "></i></a> 
                                </div>
                                <h2>
                                    <strong>
                                      <?php echo ObtenEtiqueta(56); ?>
                                    </strong>
                                </h2>				

                                <span class="jarviswidget-loader"><i class="fa fa-refresh fa-spin"></i></span>
                              </header>

                              <!-- widget div-->
                              <div role="content">

                                  <!-- widget content -->
                                  <div class="widget-body" style="min-height:0; padding-bottom: 0px;">
                                      <div class="row">
                                          <?php
                                          $opc = array(ObtenEtiqueta(624), ObtenEtiqueta(625), ObtenEtiqueta(626), ObtenEtiqueta(627), ObtenEtiqueta(628), ObtenEtiqueta(629), ObtenEtiqueta(630));
                                          $val = array('1', '2', '3', '4', '5', '6', '7');
                                          ?>
                                          <!--<div class="row padding-bottom-10">-->
                                            <div class="row col-lg-12 col-sm-12">
                                              <div class="col-sm-6">
                                                <?php echo Forma_CampoInfo(ObtenEtiqueta(301), $ds_resp2_1, 'left', 'col col-sm-12', 'col col-sm-12'); ?>
                                              </div>
                                              <div class="col-sm-6">
                                                <?php echo Forma_CampoInfo(ObtenEtiqueta(302), $ds_resp2_2, 'left', 'col col-sm-12', 'col col-sm-12'); ?>
                                              </div>
                                            </div>
                                            <div class="row col-lg-12 col-sm-12">
                                              <div class="col-sm-6">
                                                <?php echo Forma_CampoInfo(ObtenEtiqueta(303), $ds_resp2_3, 'left', 'col col-sm-12', 'col col-sm-12');   ?>
                                              </div>
                                              <div class="col-sm-6">
                                                <?php echo Forma_CampoInfo(ObtenEtiqueta(304), $ds_resp2_4, 'left', 'col col-sm-12', 'col col-sm-12');   ?>
                                              </div>
                                            </div>
                                            <div class="row col-lg-12 col-sm-12">
                                              <div class="col-sm-6">
                                                <?php echo Forma_CampoInfo(ObtenEtiqueta(305), $ds_resp2_5, 'left', 'col col-sm-12', 'col col-sm-12');   ?>
                                              </div>
                                              <div class="col-sm-6">
                                                <?php echo Forma_CampoInfo(ObtenEtiqueta(306), $ds_resp2_6, 'left', 'col col-sm-12', 'col col-sm-12');   ?>
                                              </div>
                                            </div>
                                            <div class="col-sm-6">
                                              <?php echo Forma_CampoInfo(ObtenEtiqueta(307), $ds_resp2_7, 'left', 'col col-sm-12', 'col col-sm-12');   ?>
                                            </div>
                                          <!--</div>-->
                                      </div>
                                  </div>
                                  <!-- end widget content -->

                              </div>
                              <!-- end widget div -->
                          </div>
                      </div>
                      <?php
                      Forma_Seccion(ObtenEtiqueta(78));
                      ?>
                      <!-- 2. Basic Computer Skills  -->
                      <div class="col-xs-12" style="padding:10px;">
                        <div class="jarviswidget" id="wid-id-4" role="widget4" style="" data-widget-colorbutton="false" data-widget-editbutton="false" data-widget-togglebutton="false" data-widget-deletebutton="false">
                          <header role="heading">
                            <div class="jarviswidget-ctrls" role="menu">  
                                <a href="javascript:void(0);" class="button-icon jarviswidget-toggle-btn" rel="tooltip" title="" data-placement="bottom" data-original-title="Collapse"><i class="fa fa-minus "></i></a> 
                            </div>
                            <h2>
                                <strong>
                                  <?php echo ObtenEtiqueta(79); ?>
                                </strong>
                            </h2>				

                            <span class="jarviswidget-loader"><i class="fa fa-refresh fa-spin"></i></span>
                          </header>

                          <!-- widget div-->
                          <div role="content">

                              <!-- widget content -->
                              <div class="widget-body" style="min-height:0; padding-bottom: 0px;">
                                    <div class="row col col-lg-12 col col-sm-12">
                                      <?php
                                        echo "<div class='row'><div class='col-sm-6'>";
                                        switch($fg_resp4_1_1) {
                                          case '1':                                  
                                          Forma_CampoInfo(ObtenEtiqueta(82), ETQ_SI, "left", "col-sm-12", "col-sm-12"); break;                                          
                                          case '0': 
                                          Forma_CampoInfo(ObtenEtiqueta(82), ETQ_NO, "left", "col-sm-12", "col-sm-12"); break;
                                        }
                                        echo "</div>";
                                        echo "<div class='col-sm-6'>";
                                        switch($fg_resp4_1_2) {
                                          case '1': Forma_CampoInfo(ObtenEtiqueta(83), ETQ_SI, "left", "col-sm-12", "col-sm-12"); break;
                                          case '0': Forma_CampoInfo(ObtenEtiqueta(83), ETQ_NO, "left", "col-sm-12", "col-sm-12"); break;
                                        }
                                        echo "</div></div>";
                                        echo "<div class='row'><div class='col-sm-6'>";
                                        switch($fg_resp4_1_3) {
                                          case '1': Forma_CampoInfo(ObtenEtiqueta(84), ETQ_SI, "left", "col-sm-12", "col-sm-12"); break;
                                          case '0': Forma_CampoInfo(ObtenEtiqueta(84), ETQ_NO, "left", "col-sm-12", "col-sm-12"); break;
                                        }
                                        echo "</div>";
                                        echo "<div class='col-sm-6'>";
                                        switch($fg_resp4_1_4) {
                                          case '1': Forma_CampoInfo(ObtenEtiqueta(85), ETQ_SI, "left", "col-sm-12", "col-sm-12"); break;
                                          case '0': Forma_CampoInfo(ObtenEtiqueta(85), ETQ_NO, "left", "col-sm-12", "col-sm-12"); break;
                                        }
                                        echo "</div></div>";
                                        echo "<div class='row'><div class='col-sm-6'>";
                                        switch($fg_resp4_1_5) {
                                          case '1': Forma_CampoInfo(ObtenEtiqueta(86), ETQ_SI, "left", "col-sm-12", "col-sm-12"); break;
                                          case '0': Forma_CampoInfo(ObtenEtiqueta(86), ETQ_NO, "left", "col-sm-12", "col-sm-12"); break;
                                        }
                                        echo "</div>";
                                        echo "<div class='col-sm-6'>";
                                        switch($fg_resp4_1_6) {
                                          case '1': Forma_CampoInfo(ObtenEtiqueta(87), ETQ_SI, "left", "col-sm-12", "col-sm-12"); break;
                                          case '0': Forma_CampoInfo(ObtenEtiqueta(87), ETQ_NO, "left", "col-sm-12", "col-sm-12"); break;
                                        }
                                        echo "</div></div>";                                        
                                      ?>
                                    </div>
                              </div>
                          </div>
                      </div>
                      </div>
                      <!-- Basic Internet Skills -->
                      <div class="col-xs-12" style="padding:10px;">
                        <div class="jarviswidget" id="wid-id-5" role="widget5" style="" data-widget-colorbutton="false" data-widget-editbutton="false" data-widget-togglebutton="false" data-widget-deletebutton="false">
                            <header role="heading">
                              <div class="jarviswidget-ctrls" role="menu">  
                                  <a href="javascript:void(0);" class="button-icon jarviswidget-toggle-btn" rel="tooltip" title="" data-placement="bottom" data-original-title="Collapse"><i class="fa fa-minus "></i></a> 
                              </div>
                              <h2>
                                  <strong>
                                    <?php echo ObtenEtiqueta(80); ?>
                                  </strong>
                              </h2>				

                              <span class="jarviswidget-loader"><i class="fa fa-refresh fa-spin"></i></span>
                            </header>

                            <!-- widget div-->
                            <div role="content">

                                <!-- widget content -->
                                <div class="widget-body" style="min-height:0; padding-bottom: 0px;">
                                  <div class="row col col-lg-12 col col-sm-12">
                                  <?php
                                  echo "<div class='row'>
                                  <div class='col-sm-6'>";
                                  switch($fg_resp4_2_1) {
                                    case '1': Forma_CampoInfo(ObtenEtiqueta(88), ETQ_SI, "left", "col-sm-12","col-sm-12"); break;
                                    case '0': Forma_CampoInfo(ObtenEtiqueta(88), ETQ_NO, "left", "col-sm-12","col-sm-12"); break;
                                  }
                                  echo "</div><div class='col-sm-6'>";
                                  switch($fg_resp4_2_2) {
                                    case '1': Forma_CampoInfo(ObtenEtiqueta(89), ETQ_SI, "left", "col-sm-12","col-sm-12"); break;
                                    case '0': Forma_CampoInfo(ObtenEtiqueta(89), ETQ_NO, "left", "col-sm-12","col-sm-12"); break;
                                  }
                                  echo "</div></div>
                                  <div class='row'><div class='col-sm-6'>";
                                  switch($fg_resp4_2_3) {
                                    case '1': Forma_CampoInfo(ObtenEtiqueta(90), ETQ_SI, "left", "col-sm-12","col-sm-12"); break;
                                    case '0': Forma_CampoInfo(ObtenEtiqueta(90), ETQ_NO, "left", "col-sm-12","col-sm-12"); break;
                                  }
                                  echo "</div><div class='col-sm-6'>";
                                  switch($fg_resp4_2_4) {
                                    case '1': Forma_CampoInfo(ObtenEtiqueta(91), ETQ_SI, "left", "col-sm-12","col-sm-12"); break;
                                    case '0': Forma_CampoInfo(ObtenEtiqueta(91), ETQ_NO, "left", "col-sm-12","col-sm-12"); break;
                                  }
                                  echo "</div></div>
                                  <div class='row'><div class='col-sm-6'>";
                                  switch($fg_resp4_2_5) {
                                    case '1': Forma_CampoInfo(ObtenEtiqueta(92), ETQ_SI, "left", "col-sm-12","col-sm-12"); break;
                                    case '0': Forma_CampoInfo(ObtenEtiqueta(92), ETQ_NO, "left", "col-sm-12","col-sm-12"); break;
                                  }
                                  echo "</div><div class='col-sm-6'>";
                                  switch($fg_resp4_2_6) {
                                    case '1': Forma_CampoInfo(ObtenEtiqueta(93), ETQ_SI, "left", "col-sm-12","col-sm-12"); break;
                                    case '0': Forma_CampoInfo(ObtenEtiqueta(93), ETQ_NO, "left", "col-sm-12","col-sm-12"); break;
                                  }
                                  echo "</div></div>
                                  <div class='row'><div class='col-sm-6'>";
                                  switch($fg_resp4_2_7) {
                                    case '1': Forma_CampoInfo(ObtenEtiqueta(94), ETQ_SI, "left", "col-sm-12","col-sm-12"); break;
                                    case '0': Forma_CampoInfo(ObtenEtiqueta(94), ETQ_NO, "left", "col-sm-12","col-sm-12"); break;
                                  }
                                  echo "</div></div>";
                                  ?>
                                  </div>
                                </div>
                            </div>
                        </div>
                      </div>
                      <!-- Computer Graphics Software Skills -->
                      <div  class="col-xs-12" style="padding:10px;">
                        <div class="jarviswidget" id="wid-id-6" role="widget6" style="" data-widget-colorbutton="false" data-widget-editbutton="false" data-widget-togglebutton="false" data-widget-deletebutton="false">
                          <header role="heading">
                            <div class="jarviswidget-ctrls" role="menu">  
                                <a href="javascript:void(0);" class="button-icon jarviswidget-toggle-btn" rel="tooltip" title="" data-placement="bottom" data-original-title="Collapse"><i class="fa fa-minus "></i></a> 
                            </div>
                            <h2>
                                <strong>
                                  <?php echo ObtenEtiqueta(81); ?>
                                </strong>
                            </h2>				

                            <span class="jarviswidget-loader"><i class="fa fa-refresh fa-spin"></i></span>
                          </header>

                          <!-- widget div-->
                          <div role="content">

                              <!-- widget content -->
                              <div class="widget-body" style="min-height:0; padding-bottom: 0px;">
                                <div class="row col col-lg-12 col col-sm-12">
                                <?php
                                echo "<div class='col-sm-6'>";
                                switch($fg_resp4_3_1) {
                                  case '0': Forma_CampoInfo(ObtenEtiqueta(95), ObtenEtiqueta(97), "left", "col-sm-12", "col-sm-12"); break;
                                  case '1': Forma_CampoInfo(ObtenEtiqueta(95), ObtenEtiqueta(98), "left", "col-sm-12", "col-sm-12"); break;
                                  case '2': Forma_CampoInfo(ObtenEtiqueta(95), ObtenEtiqueta(99), "left", "col-sm-12", "col-sm-12"); break;
                                  case '3': Forma_CampoInfo(ObtenEtiqueta(95), ObtenEtiqueta(107), "left", "col-sm-12", "col-sm-12"); break;
                                }
                                echo "</div><div class='col-sm-4'>";
                                switch($fg_resp4_3_2) {
                                  case '0': Forma_CampoInfo(ObtenEtiqueta(96), ObtenEtiqueta(97), "left", "col-sm-12", "col-sm-12"); break;
                                  case '1': Forma_CampoInfo(ObtenEtiqueta(96), ObtenEtiqueta(98), "left", "col-sm-12", "col-sm-12"); break;
                                  case '2': Forma_CampoInfo(ObtenEtiqueta(96), ObtenEtiqueta(99), "left", "col-sm-12", "col-sm-12"); break;
                                  case '3': Forma_CampoInfo(ObtenEtiqueta(96), ObtenEtiqueta(107), "left", "col-sm-12", "col-sm-12"); break;
                                }
                                echo "</div>";
                                ?>
                                </div>
                              </div>
                          </div>
                        </div>
                      </div>
                      <!-- 4. Expectations Questionnaire -->
                      <div  class="col-xs-12" style="padding:10px;">
                        <div class="jarviswidget" id="wid-id-7" role="widget7" style="" data-widget-colorbutton="false" data-widget-editbutton="false" data-widget-togglebutton="false" data-widget-deletebutton="false">
                          <header role="heading">
                            <div class="jarviswidget-ctrls" role="menu">  
                                <a href="javascript:void(0);" class="button-icon jarviswidget-toggle-btn" rel="tooltip" title="" data-placement="bottom" data-original-title="Collapse"><i class="fa fa-minus "></i></a> 
                            </div>
                            <h2>
                                <strong>
                                  <?php echo ObtenEtiqueta(57); ?>
                                </strong>
                            </h2>				

                            <span class="jarviswidget-loader"><i class="fa fa-refresh fa-spin"></i></span>
                          </header>

                          <!-- widget div-->
                          <div role="content">

                              <!-- widget content -->
                              <div class="widget-body" style="min-height:0; padding-bottom: 0px;">
                                <div class="row col col-lg-12 col col-sm-12">
                                <div class="row">
                                  <div class="col-sm-6">
                                  <?php Forma_CampoInfo(ObtenEtiqueta(308), $ds_resp3_1, "left", "col-sm-12", "col-sm-12"); ?>
                                  </div>
                                  <div class="col-sm-6">
                                  <?php
                                  echo Btstrp_Forma_CampoInfo(ObtenEtiqueta(309), 
                                            '<div class="row"><div class="col-xs-12 col-sm-10 col-sm-offset-1">' . "<strong>1.-</strong> " . $ds_resp3_2_1 . '</div></div>' .
                                            '<div class="row"><div class="col-xs-12 col-sm-10 col-sm-offset-1">' . "<strong>2.-</strong> " . $ds_resp3_2_2 . '</div></div>' .
                                            '<div class="row"><div class="col-xs-12 col-sm-10 col-sm-offset-1">' . "<strong>3.-</strong> " . $ds_resp3_2_3 . '</div></div>');
                                  ?>
                                  </div>
                                </div>
                                <div class="row">
                                  <div class="col-sm-6">
                                  <?php Forma_CampoInfo(ObtenEtiqueta(310), $ds_resp3_3, "left", "col-sm-12", "col-sm-12"); ?>
                                  </div>
                                  <div class="col-sm-6">
                                  <?php Forma_CampoInfo(ObtenEtiqueta(311), $ds_resp3_4, "left", "col-sm-12", "col-sm-12"); ?>
                                  </div>
                                </div>
                                <div class="row">
                                  <div class="col-sm-6">
                                  <?php
                                  Forma_CampoInfo(ObtenEtiqueta(312), $ds_resp3_5, "left", "col-sm-12", "col-sm-12");
                                  ?>
                                  </div>
                                  <div class="col-sm-6">
                                  <?php
                                  switch($ds_resp3_6) {
                                    case 'A': Forma_CampoInfo(ObtenEtiqueta(313), ObtenEtiqueta(314), "left", "col-sm-12", "col-sm-12"); break;
                                    case 'B': Forma_CampoInfo(ObtenEtiqueta(313), ObtenEtiqueta(315), "left", "col-sm-12", "col-sm-12"); break;
                                    case 'C': Forma_CampoInfo(ObtenEtiqueta(313), ObtenEtiqueta(316), "left", "col-sm-12", "col-sm-12"); break;
                                  }
                                  ?>
                                  </div>
                                </div>
                                <div class="row">
                                  <div class="col-sm-6">
                                  <?php
                                  switch($ds_resp3_7) {
                                    case 'A': Forma_CampoInfo(ObtenEtiqueta(317), ObtenEtiqueta(318), "left", "col-sm-12", "col-sm-12"); break;
                                    case 'B': Forma_CampoInfo(ObtenEtiqueta(317), ObtenEtiqueta(319), "left", "col-sm-12", "col-sm-12"); break;
                                    case 'C': Forma_CampoInfo(ObtenEtiqueta(317), ObtenEtiqueta(320), "left", "col-sm-12", "col-sm-12"); break;
                                    case 'D': Forma_CampoInfo(ObtenEtiqueta(317), ObtenEtiqueta(321), "left", "col-sm-12", "col-sm-12"); break;
                                    case 'E': Forma_CampoInfo(ObtenEtiqueta(317), ObtenEtiqueta(322), "left", "col-sm-12", "col-sm-12"); break;
                                  }
                                  ?>
                                  </div>
                                  <div class="col-sm-6">
                                  <?php Forma_CampoInfo(ObtenEtiqueta(323), $ds_resp3_8, "left", "col-sm-12", "col-sm-12"); ?>
                                  </div>
                                </div>
                                </div>
                              </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                
				
				<!-----Rubric--->
				  <div id="fb-root"></div>
				  
				 
				  
				  <div class="tab-pane fade" id="rubric">
				        
							<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-slider/9.7.1/css/bootstrap-slider.min.css" />
							<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-slider/9.7.1/css/bootstrap-slider.css" />
							<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-slider/9.7.1/bootstrap-slider.js" ></script>
						    <!---	<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-slider/9.7.1/bootstrap-slider.min.js" ></script> --->   
						
						
						
						 <!-- <a href="javascript:PresentaRubric();">preeta rubric</a>-->
						
						
						  <!--=============Presenta Rubric================-->
                           <!---===Se hace llmado ajax para que la carga sea mas rapida.=======-->
						    <div id="presenta_rubric_calificar">  </div>
							
				                <script>
					              function PresentaRubric(){
							            var fl_programa=<?php echo $fl_programa?>;
							            var cl_sesion='<?php echo $cl_sesion;?>';
					  
					                        $.ajax({
									            type: 'POST',
									            url: 'muestra_rubric_calificar.php',
									            data: 'cl_sesion='+cl_sesion+
										              '&fl_programa='+fl_programa,									   
									            async: true,
									            success: function (html) {

										            $('#presenta_rubric_calificar').html(html);

									            }
								            });
					  
					  
					  
					              }
				                setTimeout(function(){
				                    PresentaRubric();
				                }, 2000);
					              
					  
					            </script>

				  </div>
				
				<!-----End Rubric--->
				
				  <div class="tab-pane fade" id="status">

                      <div class="row">
                        <div class="col-xs-4 col-sm-4">


					             <?php 

                                 $Querys="SELECT ds_graduate_status,job_title FROM k_ses_app_frm_1 where cl_sesion='$cl_sesion' ";
                                 $rows=RecuperaValor($Querys);
                                 $ds_graduate_status=$rows['ds_graduate_status'];
                                 $job_title=$rows['job_title'];


                                 $opc_quiz = array(ObtenEtiqueta(2654), ObtenEtiqueta(2655), ObtenEtiqueta(2656), ObtenEtiqueta(2657), ObtenEtiqueta(2658), ObtenEtiqueta(2659),'No possible contact after 3 attempts');
                                 $val_quiz = array('1', '2', '3', '4', '5', '6','7');
                                 ?>
                                 <?php echo Forma_CampoSelect('Follow Up Type', False, 'ds_graduate_status', $opc_quiz, $val_quiz, $ds_graduate_status, !empty($ds_graduate_status_err)?$ds_graduate_status_err:NULL, True, '', 'left', 'col col-sm-12', 'col col-sm-12'); ?>
                        </div> 
                        <div class="col-xs-4 col-sm-4">
                            <?php 
                                
                            Forma_CampoTexto('Job Title',True,'job_title',$job_title,50,40,$ds_email_err, False, '', True, '', '', "form-group", 'left', 'col col-sm-12', 'col col-sm-12');
                        ?>
                           
                        </div>         
				    </div>
				  </div>
				  
				</div>
            </div>
        </div>
    </div>
  </div>





  <?php
  
  # AGRV 19/03/14
  # Envia campos ocultos para calcular end date
  Forma_CampoOculto('fl_programa', $fl_programa);
  Forma_CampoOculto('fe_inicio', $fe_inicio);
  Forma_CampoOculto('no_contrato', $no_contrato);
  # Actualizamos los datos de los contratos si no tiene el numero de semanas y el monto a pagar por contrato
  # Para casos anteriores existentes antes de este metodo  
  $row = RecuperaValor("SELECT fg_opcion_pago, no_weeks, mn_payment_due, ds_firma_alumno FROM k_app_contrato WHERE cl_sesion='$cl_sesion' AND no_contrato='1'");
  $fg_opcion_pago = $row[0];
  $no_weeks = $row[1];
  $mn_payment_due = $row[2];
  $ds_firma_alumno = $row[3];
  if(!empty($ds_firma_alumno) && (empty($no_weeks) || empty($mn_payment_due)))
    ContratosDetalles($cl_sesion, $fg_opcion_pago, $fl_programa);
  
  # Verifica que el usuario tenga permiso para guardar los cambios (solo en modo modificacion)
  if($permiso == PERMISO_DETALLE)
    $fg_guardar = ValidaPermiso(FUNC_APP_FRM, PERMISO_MODIFICACION);
  else
    $fg_guardar = True;
  Forma_Termina($fg_guardar);
  
 
  
  # Pie de Pagina
  PresentaFooter( );
   #scripts para que funcione circulos verdes rubric.
  echo"<script src='../../../modules/common/new_campus/js/plugin/easy-pie-chart/jquery.easy-pie-chart.min.js'></script>";
  
  $cuerpo  = "<form id='frm_copy_app'>
              <div class='col-xs-12 col-md-12 col-sm-12 col-lg-12 no-padding'>
                <label class='col-xs-12 col-md-12 col-sm-12 col-lg-12 control-label text-align-left'>
                <strong>".ObtenEtiqueta(510).": </strong> ".$ds_fname." ".$ds_lname."</label>";
  $cuerpo .= "</div>
              <div class='col-xs-12 col-md-12 col-sm-12 col-lg-12 no-padding'>
                <label class='col-xs-12 col-md-12 col-sm-12 col-lg-12 control-label text-align-left'>
                  <strong>".ObtenEtiqueta(360).": </strong>".$nb_programa."</label>
              </div>
              <div class='col-xs-12 col-md-12 col-sm-12 col-lg-12 no-padding' style='border-bottom:1px dashed rgba(0,0,0,.2); '>
                <label class='col-xs-12 col-md-12 col-sm-12 col-lg-12 control-label text-align-left'>
                  <strong>".ObtenEtiqueta(902).": </strong>".$nb_periodo."</label>
              </div>
              <hr>";
  $cuerpo .= "<div class='col-xs-12 col-md-12 col-sm-12 col-lg-12'>
                <div class='form-group'>
                  <strong>".ObtenEtiqueta(903).": </strong>
                  <select class='select2' id='fl_periodo_cp_app' onchange='btn_activar($(this).val());'>";
                  # Buscamos los terms del programa diferentes al que estaba inscrito
                  $Query  = "SELECT fl_periodo, nb_periodo ";
                  $Query .= "FROM c_periodo a ";
                  $Query .= "WHERE EXISTS (SELECT 1 FROM k_term b WHERE b.fl_periodo=a.fl_periodo /*AND a.fe_inicio>curdate() AND fl_periodo<>$fl_periodo*/ AND no_grado=1) ";
                  $Query .= "AND fg_activo='1' /*AND a.fe_inicio>curdate() AND fl_periodo<>$fl_periodo */";
                  $Query .= "ORDER BY fe_inicio";
                  $rs =EjecutaQuery($Query);
                  for($i=0;$row=RecuperaRegistro($rs);$i++){
                    if($row[0]==$fl_periodo){
                      $term_current = "class='text-danger'";
                      $txt = "(Current)";
                    }
                    else{
                      $term_current = "";
                      $txt = "";
                    }
                    $cuerpo .= "<option value='".$row[0]."' ".$term_current.">".$row[1]." ".$txt."</option>";
                  }
  $cuerpo .= "
                  </select>
                </div>
              </div>";
              # Warning que ya se le envio contrato o que ya pago su appfee o tiene un pago
              $row = RecuperaValor("SELECT DATE_FORMAT(fe_pago,'%D %M, %Y') FROM k_ses_pago WHERE cl_sesion='$cl_sesion'");
              
              // If this not work change to only check $enrol on if
              if ($enrol || isset($row[0]) || !isset($row[0])) {
                $cuerpo .= "              
                <div class='col-xs-12 col-md-12 col-sm-12 col-lg-12'>
                <div class='well well-sm well-primary'>
                <h3  class='no-margin'><i class='fa fa-info-circle'></i> ".ObtenEtiqueta(891)."</h3>
                <ul class='list-unstyled'>";
                if($enrol==True)
                  $cuerpo .= "<li class='text-success'><i class='fa fa-check'></i> ".ObtenEtiqueta(892)."</li>";
                else
                  $cuerpo .= "<li><i class='fa fa-bell text-danger'></i> ".ObtenEtiqueta(893)."</li>";
                if($fg_pago=='1')
                  $cuerpo .= "<li class='text-success'><i class='fa fa-check'></i> ".ObtenEtiqueta(894)."</li>";
                else
                  $cuerpo .= "<li><i class='fa fa-bell text-danger'></i> ".ObtenEtiqueta(895)."</li>";
                if(isset($row[0]))
                  $cuerpo .= "<li class='text-success'><i class='fa fa-check'></i> ".ObtenEtiqueta(896)." <strong>(".$row[0].")</strong></li>";
                
                $cuerpo .= "</ul></div></div>";
              }
  $cuerpo .= "<div class='col-xs-12 col-md-12 col-sm-12 col-lg-12'>
                <div class='well well-sm well-primary'>
                  <h3 class='no-margin'><i class='fa fa-info-circle'></i> ".ObtenEtiqueta(897)."</h3>
                  <div class='col col-4'>";
                    for($i=1;$i<=$contratos;$i++){
                      $cuerpo .= "<div class='checkbox'><label><input class='checkbox' id='fg_contrato_".$i."' title='All' type='checkbox'><span></span> ".ObtenEtiqueta(898)." ".$i."</label></div> ";
                    }
  $cuerpo .= "
                    <div class='checkbox'><label><input class='checkbox' id='fg_app_delete' title='All' type='checkbox'><span></span> ".ObtenEtiqueta(899)."</label></div>";
                    /*# Checbox de las cartas enviadas a los aplicantes
                    if(ExisteEnTabla('k_alumno_template', 'fl_alumno', $clave)){
                      $Query  = "SELECT nb_template, fe_envio, a.fl_template, fl_alumno_template FROM k_template_doc a, k_alumno_template b ";
                      $Query .= "WHERE a.fl_template=b.fl_template AND fl_alumno=$clave ORDER BY fe_envio DESC";
                      $rs = EjecutaQuery($Query);
                      echo "</tbody>";
                      for($i=0;$row=RecuperaRegistro($rs);$i++){                      
                        $cuerpo .= "<div class='checkbox'><label><input class='checkbox' id='fl_template".$i."' title='".$row[0]."' type='checkbox'><span></span> ".$row[0]."</label></div> ";
                      }                      
                    }*/
  $cuerpo .= "
                  </div>
                </div>
              </div>
              <div class='col-xs-12 col-md-12 col-sm-12 col-lg-12' id='div_error'></div>";
             
  $cuerpo .= "
            </form>";
  $footer = "
            <div class='form-group'>              
              <button type='submit' class='btn btn-success btn-sm' id='btn_copy_app'>
                <i class='fa fa-thumbs-up'></i> ".ObtenEtiqueta(900)."
              </button>
              <button type='button' class='btn btn-warning' data-dismiss='modal'>
								<i class='fa fa-thumbs-down'></i> ".ObtenEtiqueta(14)."
              </button>
            </div>";
  $script = "
  <script>
    $(document).ready(function(){
      $('#btn_copy_app').click(function(){       
        var val_periodo = $('#fl_periodo_cp_app option:selected').val(), fg_contrato;        
        if( $('#fg_app_delete').prop('checked')){fg_app_delete = 1;}else{fg_app_delete = 0;}        
        if(val_periodo > 0){
          var i,no_contratos = '".$contratos."', contratos_val='';
          for(i=1;i<=no_contratos;i++){
            if($('#fg_contrato_'+i).prop('checked')){fg_contrato = 1;}else{fg_contrato = 0;}
            contratos_val = contratos_val + '&fg_contrato_'+i+'='+fg_contrato;
          }          
          $.ajax({
            type: 'POST',
            url : 'app_copy.php',
            data: 'clave=".$clave."&fl_periodo='+val_periodo+'&origen=applications.php&fg_app_delete='+fg_app_delete+'&no_contratos='+no_contratos+contratos_val,                  
            async: false,
            success: function(html) {
              // Redirecionamos al listado de applications
              $('#div_error').html(\"<div class='alert alert-success fade in'><strong> Correct!</strong></div>\");
              $(location).attr('href','applications.php');            
              // alert(html);
            }
          });          
        }
        else{
          $('#div_error').html(\"<div class='alert alert-danger fade in'><strong>Error!</strong> ".ObtenEtiqueta(904)."</div>\");
        }
      });
      function btn_activar(valor){
        alert(valor)
      }
    });
  </script>";
  PresentaModal("copy_app", ObtenEtiqueta(890), $cuerpo, $footer, $script);
  
 #Script  para la cofirmacion de borrar un pago 
   echo "
   <script>
    function borrar_template(url,fl_alumno_template,fl_sesion,origen) {
    var answer = confirm('".str_ascii(ObtenMensaje(MSG_ELIMINAR))."');
    if(answer) {
      document.parametros.fl_alumno_template.value  = fl_alumno_template;
      document.parametros.fl_sesion.value  = fl_sesion;
      document.parametros.origen.value  = origen;
      document.parametros.action = url;
      document.parametros.submit();
    }
    }
    function otro(url,clave, origen) {
      document.otro.clave.value  = clave;
      document.otro.action = url;
      document.otro.submit();
    }
    /* Person Responsible*/
    $('#fg_responsable').change(function(){           
      if($(this).is(':checked')){
        $('#person_responsable').show();        
      }
      else{
        $('#person_responsable').hide();        
      }      
    });    
  </script>
  <form name=parametros method=post>
    <input type=hidden name=fl_alumno_template>
    <input type=hidden name=fl_sesion>
    <input type=hidden name=origen>
  </form>\n
  <form name=otro method=post>
    <input type=hidden name=clave>
  </form>\n";
  
  
   
#Para calculo de tarifas internacionales.
   
   echo"
                                     <script>
                                     
                                      $(document).ready(function () {

			
                                                   $('#fg_aplicar_international').change(function () {
                                                       
                                                       if ($('#fg_aplicar_international').is(':checked')) {
                                                           var ch=1;
                                                        } else {
                                                           var ch=0;
                                                        }
                                                        
                                                        var fl_programa=$fl_programa;
                                                        
                                                        $.ajax({
                                                            type:'POST',
                                                            url:'calcular_tarifas.php',
                                                            data:'fg_tarifa='+ch+
                                                                 '&fl_programa='+fl_programa,
                                                            async: false
                                                            }).done(function(result){
                                                                  var datas = JSON.parse(result);
                                                                  var mn_app_fee = datas.mn_app_fee;
                                                                  var mn_tuition=datas.mn_tuition;
                                                                  var no_costos_ad=datas.no_costos_ad;
                                                                  var ds_costs=datas.ds_costs;
																  var total_tuition=datas.total_tuition;
																  var total=datas.total;
                                                                  
																  //OptionPayment
																  
																  var no_a_payments=datas.no_a_payments;
																  var ds_a_freq=datas.ds_a_freq;
																  var amount_due_a=datas.amount_due_a;
																  var amount_paid_a=datas.amount_paid_a;
																  
																  var no_b_payments=datas.no_b_payments;
																  var ds_b_freq=datas.ds_b_freq;
																  var amount_due_b=datas.amount_due_b;
																  var amount_paid_b=datas.amount_paid_b;
																  
																  var no_c_payments=datas.no_c_payments;
																  var ds_c_freq=datas.ds_c_freq;
																  var amount_due_c=datas.amount_due_c;
																  var amount_paid_c=datas.amount_paid_c;
																  
																  var no_d_payments=datas.no_d_payments;
																  var ds_d_freq=datas.ds_d_freq;
																  var amount_due_d=datas.amount_due_d;
																  var amount_paid_d=datas.amount_paid_d;
																  
                                                                  
                                                                  $('#app_fee').val(mn_app_fee);
                                                                  $('#tuition').val(mn_tuition);
                                                                  $('#no_costos_ad').val(no_costos_ad);
																  $('#ds_costs').val(ds_costs);
																  $('#total_tuition').val(total_tuition);
																  $('#total').val(total);
																  
																  //Option Payment
																  $('#no_payme_a').empty();
																  $('#no_payme_a').append(no_a_payments);
																  $('#ds_payme_a').empty();
																  $('#ds_payme_a').append(ds_a_freq); 
																  $('#amount_due_a').val(amount_due_a);
																  $('#amount_paid_a').val(amount_paid_a);
																  
																  $('#no_payme_b').empty();
																  $('#no_payme_b').append(no_b_payments);
																  $('#ds_payme_b').empty();
																  $('#ds_payme_b').append(ds_b_freq);
																  $('#amount_due_b').val(amount_due_b);
																  $('#amount_paid_b').val(amount_paid_b);
																  
																  $('#no_payme_c').empty();
																  $('#no_payme_c').append(no_c_payments);
																  $('#ds_payme_c').empty();
																  $('#ds_payme_c').append(ds_c_freq);
																  $('#amount_due_c').val(amount_due_c);
																  $('#amount_paid_c').val(amount_paid_c);
																  
																  $('#no_payme_d').empty();
																  $('#no_payme_d').append(no_d_payments);
																  $('#ds_payme_d').empty();
																  $('#ds_payme_d').append(ds_d_freq);
																  $('#amount_due_d').val(amount_due_d);
																  $('#amount_paid_d').val(amount_paid_d);
																  
																  
                                                                  
                                                                  

                                                             });
                                                            
                                                            
                                                            
                                                            
                                                       

                                                        
                                                   });
                                      });              
                                     </script>
                                     
                                     ";
   
   
   
   
   
?>

