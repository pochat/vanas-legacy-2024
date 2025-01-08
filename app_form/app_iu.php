<?php

  # Libreria de funciones
  require("../lib/sp_general.inc.php");
  require("../lib/sp_session.inc.php");
  require("../lib/sp_forms.inc.php");

  $file_name_txt="log.txt";

  # Recibe parametro con la clave de sesion
  ## INICIO FORM 1 ###
  $clave = RecibeParametroHTML('clave');
  $fg_paso = RecibeParametroNumerico('fg_paso');
  $fl_pais_selected=RecibeParametroNumerico('fl_pais_selected');
  // 1
  $ds_fname = RecibeParametroHTML('ds_fname');
  $ds_mname = RecibeParametroHTML('ds_mname');
  $ds_lname = RecibeParametroHTML('ds_lname');
  $ds_email = RecibeParametroHTML('ds_email');
  // 2
  $fl_programa = $_POST['fl_programa'];
  $fl_periodo = $_POST['fl_periodo'];
  $fg_payment =RecibeParametroHTML('fg_payment');
  $ds_ruta_foto = RecibeParametroHTML('ds_ruta_foto');
  $img_cargada = RecibeParametroHTML('img_cargada');
  $ds_p_name = RecibeParametroHTML('ds_p_name');
  $ds_usual_name = RecibeParametroHTML('ds_usual_name');
  $ds_education_number = RecibeParametroHTML('ds_education_number');

  #Recuperamos lo del app_contrato
  if(empty($fg_payment)){
      $Query="SELECT fg_payment FROM k_app_contrato WHERE cl_sesion='$clave' ";
      $ro_=RecuperaValor($Query);
      $fg_payment=$ro_['fg_payment'];
  }

  // 3
  $fg_international = RecibeParametroHTML('fg_international');
  $ds_sin = intval(str_replace('-', '', RecibeParametroHTML('ds_sin')));
  $ds_citizenship = RecibeParametroHTML('ds_citizenship');
  $fg_study_permit = RecibeParametroBinario('fg_study_permit');
  $fg_study_permit_other = RecibeParametroBinario('fg_study_permit_other');
  $fl_immigrations_status=RecibeParametroNumerico('fl_immigrations_status');
  $passport_number = RecibeParametroHTML('passport_number');
  $passport_exp_date = RecibeParametroFecha('passport_exp_date');
  $passport_exp_date = ValidaFecha($passport_exp_date);

  //if($fg_study_permit_other=='true')
    //  $fg_study_permit_other=1;
  //else
    //  $fg_study_permit_other=0;

  $ds_number = RecibeParametroHTML('ds_number');
  $ds_alt_number = RecibeParametroHTML('ds_alt_number');

  #Campos ocultos.
  $fe_start_date= RecibeParametroFecha('fe_start_date');
  $fe_start_date = ValidaFecha($fe_start_date);
  $fe_expirity_date=RecibeParametroFecha('fe_expirity_date');
  $fe_expirity_date = ValidaFecha($fe_expirity_date);
  $nb_name_institutcion=RecibeParametroHTML('nb_name_institutcion');
  $ds_ruta_foto_permiso=RecibeParametroHTML('ds_ruta_foto_permiso');
  //$ds_ruta_foto_permiso="";
  $img_cargada_permiso = RecibeParametroHTML('img_cargada_permiso');

  # Email
  $ds_email_conf = RecibeParametroHTML('ds_email_conf');
  $ds_a_email = RecibeParametroHTML('ds_a_email');
  $ds_link_to_portfolio = RecibeParametroHTML('ds_link_to_portfolio');
  $fg_gender = RecibeParametroHTML('fg_gender');
  $fe_birth = RecibeParametroFecha('fe_birth');
  $fe_birth = ValidaFecha($fe_birth);

  // 4
  $ds_add_country = RecibeParametroHTML('ds_add_country');
  $ds_add_state = RecibeParametroHTML('ds_add_state');
  $fg_provincia = RecibeParametroNumerico('fg_provincia');
  $fl_provincia = RecibeParametroNumerico('fl_provincia');
  $ds_add_city = RecibeParametroHTML('ds_add_city');
  $ds_add_street = RecibeParametroHTML('ds_add_street');
  $ds_add_number = RecibeParametroHTML('ds_add_number');
  $ds_add_zip = RecibeParametroHTML('ds_add_zip');
  $ds_m_add_number = RecibeParametroHTML('ds_m_add_number');
  $ds_m_add_street = RecibeParametroHTML('ds_m_add_street');
  $ds_m_add_city = RecibeParametroHTML('ds_m_add_city');
  $ds_m_add_state = RecibeParametroHTML('ds_m_add_state');
  $ds_m_add_zip = RecibeParametroHTML('ds_m_add_zip');
  $ds_m_add_country = RecibeParametroHTML('ds_m_add_country');
  $ds_eme_fname = RecibeParametroHTML('ds_eme_fname');
  $ds_eme_lname = RecibeParametroHTML('ds_eme_lname');
  $ds_eme_number = RecibeParametroHTML('ds_eme_number');
  $ds_eme_relation = RecibeParametroHTML('ds_eme_relation');
  $ds_eme_relation_other = RecibeParametroHTML('ds_eme_relation_other');
  $ds_eme_country = RecibeParametroHTML('ds_eme_country');
  // 5
  $fg_aboriginal = RecibeParametroBinario('fg_aboriginal');
  $ds_aboriginal = RecibeParametroHTML('ds_aboriginal');
  $fg_health_condition = RecibeParametroHTML('fg_health_condition');
  $ds_health_condition = RecibeParametroHTML('ds_health_condition');
  $fg_disabilityie = RecibeParametroBinario('fg_disabilityie');
  $ds_disability = RecibeParametroHTML('ds_disability');
  $fg_responsable = RecibeParametroBinario('fg_responsable');
  $ds_fname_r = RecibeParametroHTML('ds_fname_r');
  $ds_lname_r = RecibeParametroHTML('ds_lname_r');
  $ds_email_r = RecibeParametroHTML('ds_email_r');
  $ds_aemail_r = RecibeParametroHTML('ds_aemail_r');
  $ds_pnumber_r = RecibeParametroHTML('ds_pnumber_r');
  $ds_relation_r = RecibeParametroHTML('ds_relation_r');
  $ds_relation_r_other = RecibeParametroHTML('ds_relation_r_other');

  //fg_disabilitie
  $race=RecibeParametroHTML('race');
  $military=RecibeParametroHTML('military');
  $hispanic=RecibeParametroBinario('hispanic');
  $grade=RecibeParametroHTML('grade');


  // 6
  $cl_preference_1 = RecibeParametroNumerico('cl_preference_1');
  $cl_preference_2 = RecibeParametroNumerico('cl_preference_2');
  $cl_preference_3 = RecibeParametroNumerico('cl_preference_3');

  $fl_class_time=$_POST['fl_class_time'];


  // 7
  $cl_recruiter = RecibeParametroNumerico('cl_recruiter');
  $fg_ori_via = RecibeParametroHTML('fg_ori_via');
  $ds_ori_other = RecibeParametroHTML('ds_ori_other');
  $fg_ori_ref = RecibeParametroHTML('fg_ori_ref');
  $ds_ori_ref_name = RecibeParametroHTML('ds_ori_ref_name');
  #### FIN FORM 1 ####
  #### INICIO FORM 2 ####
  $direccion = RecibeParametroHTML('direccion');
  $ds_resp_1 = RecibeParametroHTML('ds_resp_1');
  $ds_resp_2 = RecibeParametroHTML('ds_resp_2');
  $ds_resp_3 = RecibeParametroHTML('ds_resp_3');
  $ds_resp_4 = RecibeParametroHTML('ds_resp_4');
  $ds_resp_5 = RecibeParametroHTML('ds_resp_5');
  $ds_resp_6 = RecibeParametroHTML('ds_resp_6');
  $ds_resp_7 = RecibeParametroHTML('ds_resp_7');
  #### FIN FORM 2 ####
  #### INICIO FORM 3 ####
  $ds_resp_1_f3 = RecibeParametroHTML('ds_resp_1_f3');
  $ds_resp_2_1_f3 = RecibeParametroHTML('ds_resp_2_1_f3');
  $ds_resp_2_2_f3 = RecibeParametroHTML('ds_resp_2_2_f3');
  $ds_resp_2_3_f3 = RecibeParametroHTML('ds_resp_2_3_f3');
  $ds_resp_3_f3 = RecibeParametroHTML('ds_resp_3_f3');
  $ds_resp_4_f3 = RecibeParametroHTML('ds_resp_4_f3');
  $ds_resp_5_f3 = RecibeParametroHTML('ds_resp_5_f3');
  $ds_resp_6_f3 = RecibeParametroHTML('ds_resp_6_f3');
  $ds_resp_7_f3 = RecibeParametroHTML('ds_resp_7_f3');
  $ds_resp_8_f3 = RecibeParametroHTML('ds_resp_8_f3');
  #### FIN FORM 3 ####

  #### INICIO FORM 3 ####
  $fg_resp_1_1 = RecibeParametroHTML('fg_resp_1_1');
  $fg_resp_1_2 = RecibeParametroHTML('fg_resp_1_2');
  $fg_resp_1_3 = RecibeParametroHTML('fg_resp_1_3');
  $fg_resp_1_4 = RecibeParametroHTML('fg_resp_1_4');
  $fg_resp_1_5 = RecibeParametroHTML('fg_resp_1_5');
  $fg_resp_1_6 = RecibeParametroHTML('fg_resp_1_6');
  $fg_resp_2_1 = RecibeParametroHTML('fg_resp_2_1');
  $fg_resp_2_2 = RecibeParametroHTML('fg_resp_2_2');
  $fg_resp_2_3 = RecibeParametroHTML('fg_resp_2_3');
  $fg_resp_2_4 = RecibeParametroHTML('fg_resp_2_4');
  $fg_resp_2_5 = RecibeParametroHTML('fg_resp_2_5');
  $fg_resp_2_6 = RecibeParametroHTML('fg_resp_2_6');
  $fg_resp_2_7 = RecibeParametroHTML('fg_resp_2_7');
  $fg_resp_3_1 = RecibeParametroHTML('fg_resp_3_1');
  $fg_resp_3_2 = RecibeParametroHTML('fg_resp_3_2');
  #### FIN FORM 3 ####
  $fg_cupon = RecibeParametroNumerico('fg_cupon');

  # Verificamos is existe solo actualizamos
  $row =  RecuperaValor("SELECT COUNT(1) FROM c_sesion WHERE cl_sesion='".$clave."'");
  if(!empty($row[0]))
    $fg_nueva = false;
  else
    $fg_nueva = true;

  # Verificamos si existe sesion
  $row1 =  RecuperaValor("SELECT COUNT(1) FROM k_ses_app_frm_1 WHERE cl_sesion='".$clave."'");
  if(!empty($row1[0]))
    $fg_new_ses = false;
  else
    $fg_new_ses = true;

  if($fg_paso>3){
      #Recuperamos datos del aplicante.
      $QueryAply="SELECT ds_fname,ds_lname,ds_email,ds_mname FROM  k_ses_app_frm_1 WHERE cl_sesion='".$clave."' ";
      $rowaply=RecuperaValor($QueryAply);
      $ds_fname=$rowaply[0];
      $ds_lname=$rowaply[1];
      $ds_email_aply=$rowaply[2];
      $ds_mname=$rowaply[3];
  }
  # verificamos si existe sesion en form1
  if($fg_nueva==true){
    $QueryS  = "INSERT INTO c_sesion ";
    $QueryS .= "(cl_sesion, fg_app_1,fl_pais_campus, fe_ultmod) ";
    $QueryS .= "VALUES ('".$clave."', '1',".$fl_pais_selected.", CURRENT_TIMESTAMP)";
    $fl_sesion = EjecutaInsert($QueryS);

	GeneraLog($file_name_txt,date("F j, Y, g:i a")."1-".$QueryS);
  }
  else{
      $QueryS  = "UPDATE c_sesion SET fg_app_1='1',fl_pais_campus=$fl_pais_selected,  fe_ultmod=CURRENT_TIMESTAMP ";
      $QueryS .= "WHERE cl_sesion='".$clave."'";
      EjecutaQuery($QueryS);
	  GeneraLog($file_name_txt,date("F j, Y, g:i a")."1-".$QueryS);
  }

  # Insertamos la sesion
  if($fg_paso==1){
    # Insertamos el registro en la primer forma
    if($fg_new_ses==true) {
      $Queryf1  = 'INSERT INTO k_ses_app_frm_1 ';
      $Queryf1 .= '(cl_sesion, fl_programa, fl_periodo, ds_fname, ds_mname, ds_lname, ds_email, ds_number, fe_ultmod, cl_recruiter, fg_email) ';
      $Queryf1 .= 'VALUES ("'.$clave.'", '.$fl_programa.', '.$fl_periodo.', "'.$ds_fname.'", "'.$ds_mname.'", "'.$ds_lname.'", "'.$ds_email.'", "'.$ds_number.'", CURRENT_TIMESTAMP, 0, "1")';
    }
    else {
      $Queryf1  = 'UPDATE k_ses_app_frm_1 SET ds_fname="'.$ds_fname.'", ds_mname="'.$ds_mname.'", ds_lname="'.$ds_lname.'", ';
      $Queryf1 .= 'ds_email="'.$ds_email.'", ds_number="'.$ds_number.'", fe_ultmod=CURRENT_TIMESTAMP  ';
      $Queryf1 .= 'WHERE cl_sesion="'.$clave.'" ';

      $Query_country = 'UPDATE k_ses_app_frm_1 SET ds_add_country="'.$ds_add_country.'"  WHERE cl_sesion="'.$clave.'" ';
      EjecutaQuery($Query_country);
      GeneraLog($file_name_txt, date("F j, Y, g:i a") . "1-" . $Query_country);

      $Query_country1 = 'UPDATE k_ses_app_frm_1 SET fe_birth="'.$fe_birth.'"  WHERE cl_sesion="'.$clave.'" ';
      EjecutaQuery($Query_country1);
      GeneraLog($file_name_txt, date("F j, Y, g:i a") . "1-" . $Query_country1);
    }






    #Actualizmos el fl_class_time
 #   $Query="UPDATE k_app_contrato SET fl_class_time=$fl_class_time WHERE cl_sesion='$clave' ";
 #   EjecutaQuery($Query);



   // $Querysteps="DELETE FROM steps_completed WHERE cl_sesion='$clave' AND step_complete='1' ";
   // EjecutaQuery($Querysteps);
   // $Querysteps="INSERT INTO steps_completed(cl_sesion,step_complete,fe_creacion)VALUES('$clave','1',CURRENT_TIMESTAMP) ";
   // EjecutaInsert($Querysteps);


    # Enviamos un Emial con la notificacion de que inicio el registro y con la clave por si
    # cierra la sesion
    # No envia el correo si ya lo envio una vez
    $row = RecuperaValor("SELECT COUNT(*) FROM k_ses_app_frm_1 WHERE cl_sesion='".$clave."' AND fg_email='1'");
    if($row[0]==0){
      # Datos para email
      $email_noreply = ObtenConfiguracion(4);
      $app_copy = ObtenConfiguracion(20);
      $row0 = RecuperaValor("SELECT fl_sesion FROM c_sesion WHERE cl_sesion='".$clave."'");
      $fl_sesion = $row0[0];
      # Obtenemos el template
      $rowT = RecuperaValor("SELECT nb_template, ds_encabezado, ds_cuerpo, ds_pie FROM k_template_doc WHERE fl_template=150 AND fg_activo='1'");
      $nb_template = $rowT[0];
      $header = str_uso_normal($rowT[1]);
      $body = str_uso_normal($rowT[2]);
      $footer = str_uso_normal($rowT[3]);
      $message = $header.$body.$footer;
      $link = ObtenConfiguracion(121)."/app_form?c=A99".$fl_sesion."f0rm&co=$fl_pais_selected&p=1&cd=1";
      $message = str_replace("#st_fname#", "".$ds_fname, $message);
      $message = str_replace("#st_mname#", "".$ds_mname, $message);
      $message = str_replace("#st_lname#", "".$ds_lname, $message);
      $message = str_replace("#vanas_link_app#", "".$link, $message);


      EnviaMailHTML($email_noreply, $email_noreply, $ds_email, $nb_template, $message);
    }




  }

  # Recupera infromacion del programa seleccionado
 if($fg_international==1){


		  if($fg_payment=='C'){#Modalidad Combined

                $Query  = "SELECT mn_app_fee_internacional_combined, mn_tuition_internacional_combined, mn_costs_internacional_combined, ds_costs_internacional_combined, ";
                $Query .=" mn_a_due_internacional_combined, mn_a_paid_internacional_combined, mn_b_due_internacional_combined, mn_b_paid_internacional_combined, mn_c_due_internacional_combined, mn_c_paid_internacional_combined, mn_d_due_internacional_combined, mn_d_paid_internacional_combined, cl_type, no_semanas ";

		  }else{

			    $Query  = "SELECT mn_app_fee_internacional, mn_tuition_internacional, mn_costs_internacional, ds_costs_internacional, ";
			    $Query .=" mn_a_due_internacional, mn_a_paid_internacional, mn_b_due_internacional, mn_b_paid_internacional, mn_c_due_internacional, mn_c_paid_internacional, mn_d_due_internacional, mn_d_paid_internacional, cl_type, no_semanas ";



		  }

           switch($fl_pais_selected){
                case 38:
                    $Query .= "FROM k_programa_costos ";
                    break;
                case 226:
                    $Query .= "FROM k_programa_costos_pais  ";
                    break;
                default:
                    $Query .= "FROM k_programa_costos_pais  ";
                    break;
           }




          if($fg_paso==1)
              $Query .= "WHERE fl_programa=$fl_programa ";
          else
              $Query .= "WHERE fl_programa=(SELECT fl_programa FROM k_ses_app_frm_1 WHERE cl_sesion='".$clave."')";

            if ($fl_pais_selected <> 38) {
              $Query .= "AND fl_pais=$fl_pais_selected ";
            }


          $row = RecuperaValor($Query);
          $mn_app_intenra=$row[0];

          if(empty($mn_app_intenra)){##Si por aluna razon no esta colocados esas tarifa entonces tomaran por defual las locales.

              $Query  = "SELECT mn_app_fee, mn_tuition, mn_costs, ds_costs, ";
              $Query .=" mn_a_due, mn_a_paid, mn_b_due, mn_b_paid, mn_c_due, mn_c_paid, mn_d_due, mn_d_paid, cl_type, no_semanas ";
              $Query .= "FROM k_programa_costos ";
              if($fg_paso==1)
                  $Query .= "WHERE fl_programa=$fl_programa";
              else
                  $Query .= "WHERE fl_programa=(SELECT fl_programa FROM k_ses_app_frm_1 WHERE cl_sesion='".$clave."')";


          }



  }else{


      if($fg_payment=='C'){

          $Query  = "SELECT mn_app_fee_combined, mn_tuition_combined, mn_costs_combined, ds_costs_combined, ";
          $Query .=" mn_a_due_combined, mn_a_paid_combined, mn_b_due_combined, mn_b_paid_combined, mn_c_due_combined, mn_c_paid_combined, mn_d_due_combined, mn_d_paid_combined, cl_type, no_semanas ";


      }else{


          $Query  = "SELECT mn_app_fee, mn_tuition, mn_costs, ds_costs, ";
          $Query .=" mn_a_due, mn_a_paid, mn_b_due, mn_b_paid, mn_c_due, mn_c_paid, mn_d_due, mn_d_paid, cl_type, no_semanas ";

      }

      switch($fl_pais_selected){
          case 38:
              $Query .= "FROM k_programa_costos ";
              break;
          case 226:
              $Query .= "FROM k_programa_costos_pais  ";
              break;
        default:
              $Query .= "FROM k_programa_costos_pais  ";
              break;
      }



          if($fg_paso==1)
              $Query .= "WHERE fl_programa=$fl_programa";
          else
              $Query .= "WHERE fl_programa=(SELECT fl_programa FROM k_ses_app_frm_1 WHERE cl_sesion='".$clave."')";

          if($fl_pais_selected<>38)
              $Query .=" AND fl_pais=$fl_pais_selected ";





  }
  GeneraLog($file_name_txt,date("F j, Y, g:i a")."6-".$Query);
  $row = RecuperaValor($Query);
  $mn_app_fee = $row[0];
  if(empty($mn_app_fee))
    $mn_app_fee = 0.0;
  $mn_tuition = $row[1];
  if(empty($mn_tuition))
    $mn_tuition = 0.0;
  $mn_costs = $row[2];
  if(empty($mn_costs))
    $mn_costs = 0.0;
  $ds_costs = $row[3];
  $mn_a_due = $row[4];
  if(empty($mn_a_due))
    $mn_a_due = 0.0;
  $mn_a_paid = $row[5];
  if(empty($mn_a_paid))
    $mn_a_paid = 0.0;
  $mn_b_due = $row[6];
  if(empty($mn_b_due))
    $mn_b_due = 0.0;
  $mn_b_paid = $row[7];
  if(empty($mn_b_paid))
    $mn_b_paid = 0.0;
  $mn_c_due = $row[8];
  if(empty($mn_c_due))
    $mn_c_due = 0.0;
  $mn_c_paid = $row[9];
  if(empty($mn_c_paid))
    $mn_c_paid = 0.0;
  $mn_d_due = $row[10];
  if(empty($mn_d_due))
    $mn_d_due = 0.0;
  $mn_d_paid = $row[11];
  if(empty($mn_d_paid))
    $mn_d_paid = 0.0;
  $cl_type = $row[12];
  $no_semanas = $row[13];
  $mn_tot_tuition = $mn_tuition + $mn_costs;
  $mn_tot_program = $mn_tot_tuition + $mn_app_fee;

  # Obtenemos el numero de contratos por programa
  $meses_maximo = ObtenConfiguracion(92); // Agregar en configuracion
  $meses_x_contrato = 48; // Agregar en configuracion

  # Obtenemos los numeros de contratos que deben tener
  $no_contratos_ceil = ceil($no_semanas/$meses_x_contrato);
  # Obtenemos los contratos que son de 12 meses
  $no_contratos_floor = floor($no_semanas/$meses_x_contrato);
  if($fg_paso==2){

      $Queryp ="UPDATE k_app_contrato SET  ";
      $Queryp .= "ds_p_name='$ds_p_name', ds_education_number='$ds_education_number', ds_usual_name='$ds_usual_name' ";
      $Queryp .="WHERE cl_sesion='".$clave."' ";
      EjecutaQuery($Queryp);

	  GeneraLog($file_name_txt,date("F j, Y, g:i a")."7-".$Queryp);
      # Foto
      if(!empty($_FILES['ds_ruta_foto']['tmp_name'][0])) {
          $foto_size = ObtenConfiguracion(80);
          $ruta = PATH_ALU_IMAGES_F."/id";
          $Query  = "SELECT ds_ruta_foto ";
          $Query .= "FROM k_ses_app_frm_1 ";
          $Query .= "WHERE cl_sesion='$clave'";
          $row = RecuperaValor($Query);
          # Si existe foto la elimina
          if(!empty($row[0]))
              unlink($ruta."/".$row[0]);
          $ext = strtolower(ObtenExtensionArchivo($_FILES['ds_ruta_foto']['name']));
          # Obtenemos datos
          $row = RecuperaValor("SELECT fl_sesion FROM c_sesion  WHERE cl_sesion='$clave'");
          $fl_sesion = $row[0];
          $row22 = RecuperaValor("SELECT ds_fname, ds_lname, ds_mname FROM k_ses_app_frm_1  WHERE cl_sesion='$clave'");
          $ds_fname = $row22[0];
          $ds_lname = $row22[1];
          $ds_mname = $row22[2];
          $ds_ruta_foto = $ds_fname."_";
          if(!empty($ds_mname))
              $ds_ruta_foto .= $ds_mname."_";
          $ds_ruta_foto .= $ds_lname."_ID_".$fl_sesion.".".$ext;
          $ds_ruta_foto = NombreArchivoDecente($ds_ruta_foto);
          # copiamos el archivo
          move_uploaded_file($_FILES['ds_ruta_foto']['tmp_name'], $ruta."/".$ds_ruta_foto);
          if($ext == "jpg" || $ext=="png"){
              CreaThumb($ruta."/".$ds_ruta_foto, $ruta."/".$ds_ruta_foto, 0, 0, $foto_size);
          }
      }
      else{
          $ds_ruta_foto = $img_cargada;
      }

      $Queryp ="UPDATE k_app_contrato SET  ";
      $Queryp .= "ds_p_name='$ds_p_name', ds_education_number='$ds_education_number', ds_usual_name='$ds_usual_name' ";
      $Queryp .="WHERE cl_sesion='".$clave."' ";
      EjecutaQuery($Queryp);

      $Queryf2  = "UPDATE k_ses_app_frm_1 SET  ds_ruta_foto='".$ds_ruta_foto."' ";
      $Queryf2 .= "WHERE cl_sesion='".$clave."' ";
      EjecutaQuery($Queryf2);
  }

  if($fg_paso==1){

    # Inicializa los datos de la forma para la sesion
	$row3 = RecuperaValor("SELECT fl_programa FROM k_ses_app_frm_1 WHERE cl_sesion='".$clave."'");
    EjecutaQuery("DELETE FROM k_app_contrato WHERE cl_sesion='$clave'");
	if($row3[0]!=$fl_programa){
		EjecutaQuery("DELETE FROM k_app_contrato WHERE cl_sesion='$clave'");
	}

    $no_contratos_ceil = ($no_contratos_ceil == 0) ? "1" : $no_contratos_ceil;




    for($i = 1; $i <= $no_contratos_ceil; $i++) {
      # Obtenemos el numero de meses que cubre el contrato
      # Si el curso dura menos o igual a 18 los meses son lo que semanas entre 4
      # Si el curso dura mas de 18 es mas de un contrato
        # Son 12 meses por contrato a excepcion del ultimo
        # Ejemplo curso dura 30 meses son 2 meses de 12 y un ocntrato de 6 meses
      if($no_semanas <= $meses_maximo){
        $weeks_contrato = round($no_semanas);
      }
      else{
        if($i<=$no_contratos_floor)
          $weeks_contrato = $meses_x_contrato;
        else{
          $row3 = RecuperaValor("SELECT SUM( no_weeks ) FROM k_app_contrato WHERE cl_sesion = '$clave' AND no_contrato <$i");
          $weeks_contrato = $no_semanas-$row3[0];
        }
      }
      $Query  = "INSERT INTO k_app_contrato ";
      $Query .= "(cl_sesion, no_contrato, mn_app_fee, mn_tuition, mn_costs, ds_costs, mn_tot_tuition, mn_tot_program, mn_a_due, mn_a_paid, mn_b_due, mn_b_paid, mn_c_due, mn_c_paid, mn_d_due, mn_d_paid, ";
      $Query .= "ds_p_name, ds_education_number, ds_usual_name, no_weeks,fg_payment ) ";
      $Query .= "VALUES('$clave', $i, $mn_app_fee, $mn_tuition, $mn_costs, '$ds_costs', $mn_tot_tuition, $mn_tot_program, $mn_a_due, $mn_a_paid, $mn_b_due, $mn_b_paid, $mn_c_due, $mn_c_paid, $mn_d_due, $mn_d_paid, ";
      $Query .= "'$ds_p_name', '$ds_education_number', ";
      $Query .= " '$ds_usual_name', $weeks_contrato,'$fg_payment' ) ";
      EjecutaQuery($Query);


	  GeneraLog($file_name_txt,date("F j, Y, g:i a")."8-".$Query);
    }

	$Queryf12  = "UPDATE k_ses_app_frm_1 SET fl_programa=$fl_programa, fl_periodo=$fl_periodo ";
    $Queryf12 .= "WHERE cl_sesion='".$clave."' ";
	EjecutaQuery($Queryf12);

	 GeneraLog($file_name_txt,date("F j, Y, g:i a")."9-".$Queryf12."....no_contratos->". $no_contratos_ceil);



  }

  if($fg_paso==3){


    $Query = 'UPDATE k_ses_app_frm_1 SET passport_number="' . $passport_number . '" WHERE cl_sesion="' . $clave . '" ';
    EjecutaQuery($Query);

    $Query = 'UPDATE k_ses_app_frm_1 SET passport_exp_date="' . $passport_exp_date . '" WHERE cl_sesion="' . $clave . '" ';
    EjecutaQuery($Query);


    if($fg_international!=0){

          #Actaulizamos los montos del las tarifas del aplicante, ci elifi¿gio internacional
          if(!empty($mn_app_intenra)){
              $Query="UPDATE k_app_contrato SET ";
              $Query.="mn_app_fee=$mn_app_fee,mn_tuition=$mn_tuition,mn_costs=$mn_costs,ds_costs='$ds_costs',mn_tot_tuition=$mn_tot_tuition,  ";
              $Query.="mn_tot_program=$mn_tot_program,mn_a_due=$mn_a_due, mn_a_paid=$mn_a_paid,mn_b_due=$mn_b_due ,mn_b_paid=$mn_b_paid,mn_c_due=$mn_c_due, mn_c_paid=$mn_c_paid,mn_d_due=$mn_d_due,mn_d_paid=$mn_d_paid , fg_aplicar_international='1' ";
              $Query.="WHERE cl_sesion='".$clave."' ";
              EjecutaQuery($Query);
          }

      }

      # Foto permiso institucion
      if(!empty($_FILES['ds_ruta_foto_permiso']['tmp_name'][0])) {
          $foto_size = ObtenConfiguracion(80);
          $ruta = PATH_ALU_IMAGES_F."/id";
          $Query  = "SELECT ds_ruta_foto_permiso ";
          $Query .= "FROM k_ses_app_frm_1 ";
          $Query .= "WHERE cl_sesion='$clave'";
          $row = RecuperaValor($Query);

          # Si existe foto la elimina
          if(!empty($row[0]))
              unlink($ruta."/".$row[0]);
          $ext = strtolower(ObtenExtensionArchivo($_FILES['ds_ruta_foto_permiso']['name']));
          # Obtenemos datos
          $row = RecuperaValor("SELECT fl_sesion FROM c_sesion  WHERE cl_sesion='$clave'");
          $fl_sesion = $row[0];
          $row22 = RecuperaValor("SELECT ds_fname, ds_lname, ds_mname FROM k_ses_app_frm_1  WHERE cl_sesion='$clave'");
          $ds_fname = $row22[0];
          $ds_lname = $row22[1];
          $ds_mname = $row22[2];
          $ds_ruta_foto_permiso = $ds_fname."_";
          if(!empty($ds_mname))
              $ds_ruta_foto_permiso .= $ds_mname."_";
          $ds_ruta_foto_permiso .= $ds_lname."_ID_school_permiss".$fl_sesion.".".$ext;
          $ds_ruta_foto_permiso = NombreArchivoDecente($ds_ruta_foto_permiso);
          # copiamos el archivo
          move_uploaded_file($_FILES['ds_ruta_foto_permiso']['tmp_name'], $ruta."/".$ds_ruta_foto_permiso);
          if($ext == "jpg" || $ext=="png"){
              CreaThumb($ruta."/".$ds_ruta_foto_permiso, $ruta."/".$ds_ruta_foto_permiso, 0, 0, $foto_size);
          }
      } else {
          $ds_ruta_foto_permiso = $img_cargada;
      }

      $Queryf1 = 'UPDATE k_ses_app_frm_1 SET fl_immigrations_status='.$fl_immigrations_status.', ds_alt_number="'.$ds_alt_number.'", ds_link_to_portfolio="'.$ds_link_to_portfolio.'", fg_gender="'.$fg_gender.'", ds_ruta_foto_permiso="'.$ds_ruta_foto_permiso.'",nb_name_institutcion="'.$nb_name_institutcion.'", ds_sin="'.$ds_sin.'" ';

      if ($fg_international!=0) {
          if(!empty($fe_start_date))
          $Queryf1 .= ', fe_start_date="'.$fe_start_date.'" ';
          if(!empty($fe_expirity_date))
          $Queryf1 .=', fe_expirity_date="'.$fe_expirity_date.'" ';

      }

      $Queryf1 .= ' WHERE cl_sesion="'.$clave.'" ';
      EjecutaQuery($Queryf1);
      GeneraLog($file_name_txt, date("F j, Y, g:i a") . "" . $Queryf1);

    for($i = 1; $i <= $no_contratos_ceil; $i++) {
      $Query = 'UPDATE k_app_contrato SET fg_international="'.$fg_international.'", ds_citizenship="'.$ds_citizenship.'", fg_study_permit="'.$fg_study_permit.'", fg_study_permit_other="'.$fg_study_permit_other.'", ds_a_email="'.$ds_a_email.'", ds_sin="'.$ds_sin.'" WHERE cl_sesion="'.$clave.'" ';
      EjecutaQuery($Query);
    }


//    $Querysteps="DELETE FROM steps_completed WHERE cl_sesion='$clave' AND step_complete='3' ";
//    EjecutaQuery($Querysteps);
//    $Querysteps="INSERT INTO steps_completed(cl_sesion,step_complete,fe_creacion)VALUES('$clave','3',CURRENT_TIMESTAMP) ";
//    EjecutaInsert($Querysteps);

  }

  if($fg_paso==4){

    #case especific usa,
      if($ds_add_country==226){
          $Querystate="SELECT ds_provincia FROM k_provincias WHERE fl_pais=$ds_add_country AND fl_provincia=$ds_add_state  ";
          $rp=RecuperaValor($Querystate);
          $ds_add_state=$rp[0];
      }


    $Queryf1 = 'UPDATE k_ses_app_frm_1 SET ds_add_country="'.$ds_add_country.'", ds_add_state="'.$ds_add_state.'", ds_add_city="'.$ds_add_city.'", ds_add_street="'.$ds_add_street.'", ds_add_number="'.$ds_add_number.'", ds_add_zip="'.$ds_add_zip.'" WHERE cl_sesion="'.$clave.'" ';
    EjecutaQuery($Queryf1);
    GeneraLog($file_name_txt, date("F j, Y, g:i a") . "" . $Queryf1);
//    $Querysteps="DELETE FROM steps_completed WHERE cl_sesion='$clave' AND step_complete='4' ";
//    EjecutaQuery($Querysteps);
//    $Querysteps="INSERT INTO steps_completed(cl_sesion,step_complete,fe_creacion)VALUES('$clave','4',CURRENT_TIMESTAMP) ";
//    EjecutaInsert($Querysteps);

    //recuperamos el tax. y asignamos
    //canada USA
    if (is_numeric($ds_add_state)) {

        $Query = "SELECT mn_tax FROM k_provincias where fl_provincia= $ds_add_state ";
        $row = RecuperaValor($Query);
        $percentage_tax = str_texto($row[0]);

        $mn_tax_cost= ($mn_costs * $percentage_tax) / 100;

        /*$Query = "SELECT mn_tot_program FROM k_app_contrato WHERE cl_sesion='$clave' ";
        $row = RecuperaValor($Query);
        $mn_tot_program = $row['mn_tot_program'];
        $mn_tot_program = $mn_tot_program + $mn_tax_cost;
        */

        $Query = "UPDATE k_app_contrato SET tax_mn_cost=$mn_tax_cost WHERE cl_sesion='$clave' ";
        EjecutaQuery($Query);

    }



}

  if($fg_paso==5){
    for($i = 1; $i <= $no_contratos_ceil; $i++) {
      $Query = 'UPDATE k_app_contrato SET  ds_m_add_number="'.$ds_m_add_number.'", ds_m_add_street="'.$ds_m_add_street.'", ds_m_add_city="'.$ds_m_add_city.'", ds_m_add_state="'.$ds_m_add_state.'", ds_m_add_zip="'.$ds_m_add_zip.'", ds_m_add_country="'.$ds_m_add_country.'" WHERE cl_sesion="'.$clave.'" ';
      EjecutaQuery($Query);
    }

//    $Querysteps="DELETE FROM steps_completed WHERE cl_sesion='$clave' AND step_complete='5' ";
//    EjecutaQuery($Querysteps);
//    $Querysteps="INSERT INTO steps_completed(cl_sesion,step_complete,fe_creacion)VALUES('$clave','5',CURRENT_TIMESTAMP) ";
//    EjecutaInsert($Querysteps);

  }

  if($fg_paso==6){
    $Queryf1 = 'UPDATE k_ses_app_frm_1 SET ds_eme_fname="'.$ds_eme_fname.'", ds_eme_lname="'.$ds_eme_lname.'", ds_eme_number="'.$ds_eme_number.'", ds_eme_relation="'.$ds_eme_relation.'", ds_eme_relation_other="'.$ds_eme_relation_other.'" ,ds_eme_country="'.$ds_eme_country.'" WHERE cl_sesion="'.$clave.'" ';

//    $Querysteps="DELETE FROM steps_completed WHERE cl_sesion='$clave' AND step_complete='6' ";
//    EjecutaQuery($Querysteps);
//    $Querysteps="INSERT INTO steps_completed(cl_sesion,step_complete,fe_creacion)VALUES('$clave','6',CURRENT_TIMESTAMP) ";
//    EjecutaInsert($Querysteps);

  }

  if($fg_paso==7){

      if($fl_pais_selected==38){
          for($i = 1; $i <= $no_contratos_ceil; $i++) {
              $Query = 'UPDATE k_app_contrato SET  fg_aboriginal="'.$fg_aboriginal.'", ds_aboriginal="'.$ds_aboriginal.'", fg_health_condition="'.$fg_health_condition.'", ds_health_condition="'.$ds_health_condition.'" WHERE cl_sesion="'.$clave.'" ';
              EjecutaQuery($Query);
          }
          $Queryf1 = 'UPDATE k_ses_app_frm_1 SET fg_disability="'.$fg_disabilityie.'", ds_disability="'.$ds_disability.'" WHERE cl_sesion="'.$clave.'" ';
      }
      if($fl_pais_selected<>38){

          $Queryf1 = 'UPDATE k_ses_app_frm_1 SET fg_disability="'.$fg_disabilityie.'", race="'.$race.'", military="'.$military.'", grade="'.$grade.'", hispanic="'.$hispanic.'"  WHERE cl_sesion="'.$clave.'" ';

      }


//      $Querysteps="DELETE FROM steps_completed WHERE cl_sesion='$clave' AND step_complete='7' ";
//      EjecutaQuery($Querysteps);
//      $Querysteps="INSERT INTO steps_completed(cl_sesion,step_complete,fe_creacion)VALUES('$clave','7',CURRENT_TIMESTAMP) ";
//      EjecutaInsert($Querysteps);

  }
  if($fg_paso==8){

    if($fg_responsable==0){

        $Queryf1 = "UPDATE k_ses_app_frm_1 SET fg_responsable='1' WHERE cl_sesion='".$clave."'";

      # Verificamos si existe el dato en la bd
      if(ExisteEnTabla('k_presponsable', 'cl_sesion', $clave)){
        $Query_respon  = 'UPDATE k_presponsable SET ds_fname_r = "'.$ds_fname_r.'" , ds_lname_r = "'.$ds_lname_r.'", ds_email_r = "'.$ds_email_r.'", ';
        $Query_respon .= 'ds_aemail_r = "'.$ds_aemail.'", ds_pnumber_r = "'.$ds_pnumber_r.'" ,ds_relation_r = "'.$ds_relation_r.'", ds_relation_r_other="'.$ds_relation_r_other.'" ';
        $Query_respon .= 'WHERE cl_sesion = "'.$clave.'" ';

      }
      else{
        $Query_respon  = 'INSERT INTO k_presponsable (cl_sesion,ds_fname_r,ds_lname_r,ds_email_r,ds_aemail_r,ds_pnumber_r, ds_relation_r, ds_relation_r_other) ';
        $Query_respon .= 'VALUES ("'.$clave.'", "'.$ds_fname_r.'", "'.$ds_lname_r.'", "'.$ds_email_r.'", "'.$ds_aemail_r.'", "'.$ds_pnumber_r.'", "'.$ds_relation_r.'", "'.$ds_relation_r_other.'") ';
      }
      EjecutaQuery($Query_respon);
      # Enviamos notificacion al reponsable
      # Enviamos un Emial con la notificacion de que inicio el registro y con la clave por si
      # cierra la sesion
      # No envia el correo si ya lo envio una vez
      $row = RecuperaValor("SELECT COUNT(*) FROM k_presponsable WHERE cl_sesion='".$clave."' AND fg_email='1'");
      if($row[0]==0){
        $email_noreply = ObtenConfiguracion(4);
        $app_copy = ObtenConfiguracion(20);
        $rows = RecuperaValor("SELECT fl_sesion FROM c_sesion WHERE cl_sesion='".$clave."'");
        $message = genera_documento($rows[0], 2, 38) ;
        $email = EnviaMailHTML($email_noreply, $email_noreply, $ds_email_r, "Person responsible for making tuition payments", $message);
        if($email==1)
          EjecutaQuery("UPDATE k_presponsable SET fg_email='1' WHERE cl_sesion='".$clave."'");
      }
    }
    else{
      EjecutaQuery("DELETE FROM k_presponsable WHERE cl_sesion='".$clave."'");
      $Queryf1 = "UPDATE k_ses_app_frm_1 SET fg_responsable='0' WHERE cl_sesion='".$clave."'";
    }

//    $Querysteps="DELETE FROM steps_completed WHERE cl_sesion='$clave' AND step_complete='8' ";
//    EjecutaQuery($Querysteps);
//    $Querysteps="INSERT INTO steps_completed(cl_sesion,step_complete,fe_creacion)VALUES('$clave','8',CURRENT_TIMESTAMP) ";
//    EjecutaInsert($Querysteps);

  }

  if($fg_paso==9){
    for($i = 1; $i <= $no_contratos_ceil; $i++) {
      $Query = "UPDATE k_app_contrato SET  cl_preference_1='".$cl_preference_1."', cl_preference_2='".$cl_preference_2."', cl_preference_3='".$cl_preference_3."' WHERE cl_sesion='".$clave."' ";
      EjecutaQuery($Query);
    }



//    $Querysteps="DELETE FROM steps_completed WHERE cl_sesion='$clave' AND step_complete='9' ";
//    EjecutaQuery($Querysteps);
//    $Querysteps="INSERT INTO steps_completed(cl_sesion,step_complete,fe_creacion)VALUES('$clave','9',CURRENT_TIMESTAMP) ";
//    EjecutaInsert($Querysteps);

  }

  if($fg_paso==10){
    $Queryf1  = "UPDATE k_ses_app_frm_1 SET cl_recruiter='".$cl_recruiter."', fg_ori_via='".$fg_ori_via."', ";
    $Queryf1 .= "ds_ori_other='".$ds_ori_other."', fg_ori_ref='".$fg_ori_ref."', ds_ori_ref_name='".$ds_ori_ref_name."' ";
    $Queryf1 .= "WHERE cl_sesion='".$clave."'";
    # Enviamos el Email si no ha sido enviado
    if(EnviarEmailForms($clave,'',$fg_paso))
      EjecutaQuery("UPDATE k_ses_app_frm_1 SET fg_email_end='1' WHERE cl_sesion='".$clave."'");

//    $Querysteps="DELETE FROM steps_completed WHERE cl_sesion='$clave' AND step_complete='10' ";
//    EjecutaQuery($Querysteps);
//    $Querysteps="INSERT INTO steps_completed(cl_sesion,step_complete,fe_creacion)VALUES('$clave','10',CURRENT_TIMESTAMP) ";
//    EjecutaInsert($Querysteps);

  }
  if(!empty($Queryf1)){
      EjecutaQuery($Queryf1);

      if($fg_paso==1){

          $Query_country = 'UPDATE k_ses_app_frm_1 SET ds_add_country="'.$ds_add_country.'"  WHERE cl_sesion="'.$clave.'" ';
          EjecutaQuery($Query_country);
          GeneraLog($file_name_txt, date("F j, Y, g:i a") . "class-" . $Query_country);
          $Query_country1 = 'UPDATE k_ses_app_frm_1 SET fe_birth="' . $fe_birth . '"  WHERE cl_sesion="' . $clave . '" ';
          EjecutaQuery($Query_country1);
        GeneraLog($file_name_txt, date("F j, Y, g:i a") . "class-" . $Query_country);

          $Query="UPDATE k_app_contrato SET fl_class_time=$fl_class_time WHERE cl_sesion='$clave' ";
          EjecutaQuery($Query);

          #Enviamos notificacion al administradores.
          $subject = ObtenEtiqueta(336);
          $smtp = ObtenConfiguracion(4);
          $app_frm_email = ObtenConfiguracion(20);
          $message  = "Application form component Step 1/5 submitted <br><br>";
          $message .= ObtenEtiqueta(61)."<br><br>";
          $message .= ObtenEtiqueta(117).": $ds_fname<br><br>";
          $message .= ObtenEtiqueta(119).": $ds_mname<br><br>";
          $message .= ObtenEtiqueta(118).": $ds_lname<br><br>";
          $message .= ObtenEtiqueta(121).": $ds_email<br><br>";
          $message .= "Tel: $ds_number<br>";

          #Recupermaos pais
          $Query="SELECT a.ds_add_country FROM k_ses_app_frm_1 a  WHERE cl_sesion='$clave' ";
          $rop=RecuperaValor($Query);
          $ds_pais = $rop[1];
          if(is_numeric($ds_add_country)) {

              $Query="SELECT ds_pais FROM c_pais where fl_pais= $ds_add_country ";
              $row=RecuperaValor($Query);
              $ds_add_country=str_texto($row[0]);

          }
          $message .= ObtenEtiqueta(287).": $ds_add_country<br>";
          $message .= ObtenEtiqueta(120).": $fe_birth<br>";

          # Envia correo de confirmacion al Administrador
          $QueryE  = "SELECT a.fl_programa, a.fl_periodo, ds_fname, ds_mname, ds_lname, ds_number,ds_alt_number, ds_email, fg_gender, ";
          $QueryE .= ConsultaFechaBD('fe_birth', FMT_CAPTURA)." fe_birth, ds_add_number, ";
          $QueryE .= "ds_add_street, ds_add_city, ds_add_state, ds_add_zip, ds_add_country, fg_responsable, ds_eme_fname, ds_eme_lname, ds_eme_number, ";
          $QueryE .= "ds_eme_relation, ds_eme_relation_other, ds_eme_country, cl_recruiter, fg_ori_via, ds_ori_other, fg_ori_ref, ds_ori_ref_name, ";
          $QueryE .= "nb_programa, nb_periodo ";
          //$QueryE .= ", e.ds_pais ";
          $QueryE .= "FROM k_ses_app_frm_1 a, c_programa b, c_periodo c ";
          $QueryE .= "WHERE a.fl_programa=b.fl_programa ";
          $QueryE .= "AND a.fl_periodo=c.fl_periodo ";
          //$QueryE .= "AND a.ds_add_country=d.fl_pais ";
          //$QueryE .= "AND a.ds_eme_country=e.fl_pais ";
          $QueryE .= "AND cl_sesion='".$clave."'";
          $rowE = RecuperaValor($QueryE);
          $fl_programa = $rowE[0];
          $fl_periodo = $rowE[1];
          $nb_programa = $rowE[28];
          $nb_periodo = $rowE[29];
          $message .= ObtenEtiqueta(55)."<br>";
          $message .= ObtenEtiqueta(59).": $nb_programa<br>";
          $message .= ObtenEtiqueta(60).": $nb_periodo<br>";



          #Recuperamos la preferencia de horario.
          $QueryClass="SELECT CONCAT(
                                CASE WHEN cl_dia='1'THEN '".ObtenEtiqueta(2390)."'
                                WHEN cl_dia='2'THEN '".ObtenEtiqueta(2391)."'
                                WHEN cl_dia='3'THEN '".ObtenEtiqueta(2392)."'
                                WHEN cl_dia='4'THEN '".ObtenEtiqueta(2393)."'
                                WHEN cl_dia='5'THEN '".ObtenEtiqueta(2394)."'
                                WHEN cl_dia='6'THEN '".ObtenEtiqueta(2395)."'
                                ELSE '".ObtenEtiqueta(2396)."'
                                END,' ',no_hora,' ',ds_tiempo,'') FROM k_class_time_programa WHERE fl_class_time=$fl_class_time ";
          $rowclass=RecuperaValor($QueryClass);
          $no_time=$rowclass[0];
          GeneraLog($file_name_txt,date("F j, Y, g:i a")."class-".$QueryClass);
          $message .= ObtenEtiqueta(2389).": $no_time<br>";

          $message = utf8_encode(str_ascii($message));
          #Enviamos notificacion a los administrators.
          $email = EnviaMailHTML($smtp, $smtp, $app_frm_email, $subject." Step 1/5", $message);






      }


  }
  # Verifica si se esta insertando
  $row2 = RecuperaValor("SELECT COUNT(1) FROM k_ses_app_frm_2 WHERE cl_sesion='$clave'");
  if(empty($row2[0]))
    $fg_nueva2 = True;
  else
    $fg_nueva2 = False;

  if($fg_paso==11){
    # Inserta o actualiza los datos de la forma para la sesion
    if($fg_nueva2) {
      $Queryf2  = 'INSERT INTO k_ses_app_frm_2 ';
      $Queryf2 .= '(cl_sesion, ds_resp_1, ds_resp_2, ds_resp_3, ds_resp_4, ds_resp_5, ds_resp_6, ds_resp_7, fe_ultmod) ';
      $Queryf2 .= 'VALUES ("'.$clave.'", ';
      $Queryf2 .= ' "'.$ds_resp_1.'", "'.$ds_resp_2.'", "'.$ds_resp_3.'", "", "", "", "", CURRENT_TIMESTAMP)';
      EjecutaQuery($Queryf2);
    }
    else {
      $Queryf2  = 'UPDATE k_ses_app_frm_2 SET ds_resp_1="'.$ds_resp_1.'", ds_resp_2="'.$ds_resp_2.'", ds_resp_3="'.$ds_resp_3.'", ';
      $Queryf2 .= 'fe_ultmod=CURRENT_TIMESTAMP ';
      $Queryf2 .= 'WHERE cl_sesion="'.$clave.'" ';
      EjecutaQuery($Queryf2);
    }

//    $Querysteps="DELETE FROM steps_completed WHERE cl_sesion='$clave' AND step_complete='11' ";
//    EjecutaQuery($Querysteps);
//    $Querysteps="INSERT INTO steps_completed(cl_sesion,step_complete,fe_creacion)VALUES('$clave','11',CURRENT_TIMESTAMP) ";
//    EjecutaInsert($Querysteps);

  }
  if($fg_paso==12){
    $Queryf2  = 'UPDATE k_ses_app_frm_2 SET ds_resp_4="'.$ds_resp_4.'", ds_resp_5="'.$ds_resp_5.'", ds_resp_6="'.$ds_resp_6.'", ';
    $Queryf2 .= 'ds_resp_7="'.$ds_resp_7.'", fe_ultmod=CURRENT_TIMESTAMP ';
    $Queryf2 .= 'WHERE cl_sesion="'.$clave.'" ';
    EjecutaQuery($Queryf2);

    # Actualiza los datos de la forma para la sesion
    $Query  = "UPDATE c_sesion SET fg_app_2='1', fe_ultmod=CURRENT_TIMESTAMP ";
    $Query .= "WHERE cl_sesion='$clave'";
    EjecutaQuery($Query);

    #Recuperamos sus respuestas.
    $QueryResp="SELECT ds_resp_1,ds_resp_2,ds_resp_3,ds_resp_4,ds_resp_5,ds_resp_6,ds_resp_7 FROM k_ses_app_frm_2 WHERE cl_sesion='".$clave."' ";
    $resp=RecuperaValor($QueryResp);
    $resp1=$resp[0];
    $resp2=$resp[1];
    $resp3=$resp[2];
    $resp4=$resp[3];
    $resp5=$resp[4];
    $resp6=$resp[5];
    $resp7=$resp[6];

    $subject = ObtenEtiqueta(336);
    $smtp = ObtenConfiguracion(4);
    $app_frm_email = ObtenConfiguracion(20);
    $message  = "Application form component Step 3/5 submitted <br>";
    $message .= ObtenEtiqueta(61)."<br>";
    $message .= ObtenEtiqueta(117).": $ds_fname<br>";
    $message .= ObtenEtiqueta(119).": $ds_mname<br><br>";
    $message .= ObtenEtiqueta(118).": $ds_lname<br><br>";
    $message .= ObtenEtiqueta(121).": $ds_email_aply<br><br>";
    $message .= ObtenEtiqueta(301).": $resp1<br><br>";
    $message .= ObtenEtiqueta(302).": $resp2<br><br>";
    $message .= ObtenEtiqueta(303).": $resp3<br><br>";
    $message .= ObtenEtiqueta(304).": $resp4<br><br>";
    $message .= ObtenEtiqueta(305).": $resp5<br><br>";
    $message .= ObtenEtiqueta(306).": $resp6<br><br>";
    $message .= ObtenEtiqueta(307).": $resp7<br><br>";
    $message = utf8_encode(str_ascii($message));



    #Enviamos notificacion a los administrators.
    $email = EnviaMailHTML($smtp, $smtp, $app_frm_email, $subject." Step 3/5", $message);

//    $Querysteps="DELETE FROM steps_completed WHERE cl_sesion='$clave' AND step_complete='12' ";
//    EjecutaQuery($Querysteps);
//    $Querysteps="INSERT INTO steps_completed(cl_sesion,step_complete,fe_creacion)VALUES('$clave','12',CURRENT_TIMESTAMP) ";
//    EjecutaInsert($Querysteps);


  }
  //if(!empty($Queryf2)){
  //    EjecutaQuery($Queryf2);
  //}




  # Verifica si se esta insertando form4
  $row4 = RecuperaValor("SELECT COUNT(1) FROM k_ses_app_frm_4 WHERE cl_sesion='$clave'");
  if(empty($row4[0]))
    $fg_nueva4 = True;
  else
    $fg_nueva4 = False;
  if($fg_paso==13){
    # Inserta o actualiza los datos de la forma para la sesion
    if($fg_nueva4) {
      $Queryf4  = 'INSERT INTO k_ses_app_frm_4 ';
      $Queryf4 .= '(cl_sesion, fg_resp_1_1, fg_resp_1_2, fg_resp_1_3, fg_resp_1_4, fg_resp_1_5, fg_resp_1_6, fe_ultmod) ';
      $Queryf4 .= 'VALUES ("'.$clave.'", ';
      $Queryf4 .= ' "'.$fg_resp_1_1.'", "'.$fg_resp_1_2.'", "'.$fg_resp_1_3.'", "'.$fg_resp_1_4.'", "'.$fg_resp_1_5.'", "'.$fg_resp_1_6.'", CURRENT_TIMESTAMP) ';
    }
    else {
      $Queryf4  = 'UPDATE k_ses_app_frm_4 SET fg_resp_1_1="'.$fg_resp_1_1.'", fg_resp_1_2="'.$fg_resp_1_2.'", fg_resp_1_3="'.$fg_resp_1_3.'", ';
      $Queryf4 .= 'fg_resp_1_4="'.$fg_resp_1_4.'", fg_resp_1_5="'.$fg_resp_1_5.'", fg_resp_1_6="'.$fg_resp_1_6.'", fe_ultmod=CURRENT_TIMESTAMP ';
      $Queryf4 .= 'WHERE cl_sesion="'.$clave.'" ';
    }

//    $Querysteps="DELETE FROM steps_completed WHERE cl_sesion='$clave' AND step_complete='13' ";
//    EjecutaQuery($Querysteps);
//    $Querysteps="INSERT INTO steps_completed(cl_sesion,step_complete,fe_creacion)VALUES('$clave','13',CURRENT_TIMESTAMP) ";
//    EjecutaInsert($Querysteps);

  }
  if($fg_paso==14){
    $Queryf4  = 'UPDATE k_ses_app_frm_4 SET fg_resp_2_1="'.$fg_resp_2_1.'", fg_resp_2_2="'.$fg_resp_2_2.'", fg_resp_2_3="'.$fg_resp_2_3.'", ';
    $Queryf4 .= 'fg_resp_2_4="'.$fg_resp_2_4.'", fg_resp_2_5="'.$fg_resp_2_5.'", fg_resp_2_6="'.$fg_resp_2_6.'", fg_resp_2_7="'.$fg_resp_2_7.'", fe_ultmod=CURRENT_TIMESTAMP ';
    $Queryf4 .= 'WHERE cl_sesion="'.$clave.'" ' ;

//    $Querysteps="DELETE FROM steps_completed WHERE cl_sesion='$clave' AND step_complete='14' ";
//    EjecutaQuery($Querysteps);
//    $Querysteps="INSERT INTO steps_completed(cl_sesion,step_complete,fe_creacion)VALUES('$clave','14',CURRENT_TIMESTAMP) ";
//    EjecutaInsert($Querysteps);

  }
  if($fg_paso==15){
    $Queryf4  = 'UPDATE k_ses_app_frm_4 SET fg_resp_3_1="'.$fg_resp_3_1.'", fg_resp_3_2="'.$fg_resp_3_2.'", fe_ultmod=CURRENT_TIMESTAMP ';
    $Queryf4 .= 'WHERE cl_sesion="'.$clave.'" ';
    EjecutaQuery("UPDATE c_sesion SET fg_app_4='1'  WHERE cl_sesion='".$clave."' ");

//    $Querysteps="DELETE FROM steps_completed WHERE cl_sesion='$clave' AND step_complete='15' ";
//    EjecutaQuery($Querysteps);
//    $Querysteps="INSERT INTO steps_completed(cl_sesion,step_complete,fe_creacion)VALUES('$clave','15',CURRENT_TIMESTAMP) ";
//    EjecutaInsert($Querysteps);


  }
  if(!empty($Queryf4)){
      EjecutaQuery($Queryf4);
  }
  if($fg_paso==15){
      #Recuperamos sus respuestas.
      $QueryResp="SELECT fg_resp_1_1,fg_resp_1_2,fg_resp_1_3,fg_resp_1_4,fg_resp_1_5,fg_resp_1_6,fg_resp_2_1,fg_resp_2_2,fg_resp_2_3,fg_resp_2_4,fg_resp_2_5,fg_resp_2_6,fg_resp_2_7,fg_resp_3_1,fg_resp_3_2 FROM k_ses_app_frm_4 WHERE cl_sesion='".$clave."' ";
      $resp=RecuperaValor($QueryResp);
      $resp1=!empty($resp[0])?"Yes":"No";
      $resp2=!empty($resp[1])?"Yes":"No";
      $resp3=!empty($resp[2])?"Yes":"No";
      $resp4=!empty($resp[3])?"Yes":"No";
      $resp5=!empty($resp[4])?"Yes":"No";
      $resp6=!empty($resp[5])?"Yes":"No";

      $resp2_1=!empty($resp[6])?"Yes":"No";
      $resp2_2=!empty($resp[7])?"Yes":"No";
      $resp2_3=!empty($resp[8])?"Yes":"No";
      $resp2_4=!empty($resp[9])?"Yes":"No";
      $resp2_5=!empty($resp[10])?"Yes":"No";
      $resp2_6=!empty($resp[11])?"Yes":"No";
      $resp2_7=!empty($resp[12])?"Yes":"No";

      $resp3_1=!empty($resp[13])?"Yes":"No";
      $resp3_2=!empty($resp[14])?"Yes":"No";

      #Enbvia email de step 4
      $subject = ObtenEtiqueta(336);
      $smtp = ObtenConfiguracion(4);
      $app_frm_email = ObtenConfiguracion(20);
      $message  = "Application form component Step 4/5 submitted <br>";
      $message .= ObtenEtiqueta(61)."<br>";
      $message .= ObtenEtiqueta(117).": $ds_fname<br><br>";
      $message .= ObtenEtiqueta(119).": $ds_mname<br><br>";
      $message .= ObtenEtiqueta(118).": $ds_lname<br><br>";
      $message .= ObtenEtiqueta(121).": $ds_email_aply<br><br>";
      $message .= ObtenEtiqueta(82).": $resp1<br><br>";
      $message .= ObtenEtiqueta(83).": $resp2<br><br>";
      $message .= ObtenEtiqueta(84).": $resp3<br><br>";
      $message .= ObtenEtiqueta(85).": $resp4<br><br>";
      $message .= ObtenEtiqueta(86).": $resp5<br><br>";
      $message .= ObtenEtiqueta(87).": $resp6<br><br>";

      $message .= ObtenEtiqueta(88).": $resp2_1<br><br>";
      $message .= ObtenEtiqueta(89).": $resp2_2<br><br>";
      $message .= ObtenEtiqueta(90).": $resp2_3<br><br>";
      $message .= ObtenEtiqueta(91).": $resp2_4<br><br>";
      $message .= ObtenEtiqueta(92).": $resp2_5<br><br>";
      $message .= ObtenEtiqueta(93).": $resp2_6<br><br>";
      $message .= ObtenEtiqueta(94).": $resp2_7<br><br>";
      $message .= ObtenEtiqueta(95).": $resp3_1<br><br>";
      $message .= ObtenEtiqueta(96).": $resp3_2<br><br>";

      $message = utf8_encode(str_ascii($message));
      #Enviamos notificacion a los administrators.
      $email = EnviaMailHTML($smtp, $smtp, $app_frm_email, $subject." Step 4/5", $message);


  }



  # Verifica si se esta insertando
  $row3 = RecuperaValor("SELECT COUNT(1) FROM k_ses_app_frm_3 WHERE cl_sesion='$clave'");
  if(empty($row3[0]))
    $fg_nueva3 = True;
  else
    $fg_nueva3 = False;

  if($fg_paso==16){
    # Inserta o actualiza los datos de la forma para la sesion
    if($fg_nueva3) {
      $Queryf3  = 'INSERT INTO k_ses_app_frm_3 ';
      $Queryf3 .= '(cl_sesion, ds_resp_1, ds_resp_2_1, ds_resp_2_2, ds_resp_2_3, ds_resp_3, fe_ultmod) ';
      $Queryf3 .= 'VALUES ("'.$clave.'", ';
      $Queryf3 .= ' "'.$ds_resp_1_f3.'", "'.$ds_resp_2_1_f3.'", "'.$ds_resp_2_2_f3.'", "'.$ds_resp_2_3_f3.'", "'.$ds_resp_3_f3.'", CURRENT_TIMESTAMP)';
    }
    else {
      $Queryf3  = 'UPDATE k_ses_app_frm_3 SET ds_resp_1="'.$ds_resp_1_f3.'", ds_resp_2_1="'.$ds_resp_2_1_f3.'", ';
      $Queryf3 .= 'ds_resp_2_2="'.$ds_resp_2_2_f3.'", ds_resp_2_3="'.$ds_resp_2_3_f3.'", ';
      $Queryf3 .= 'ds_resp_3="'.$ds_resp_3_f3.'", fe_ultmod=CURRENT_TIMESTAMP ';
      $Queryf3 .= 'WHERE cl_sesion="'.$clave.'" ';
    }

//    $Querysteps="DELETE FROM steps_completed WHERE cl_sesion='$clave' AND step_complete='16' ";
//    EjecutaQuery($Querysteps);
//    $Querysteps="INSERT INTO steps_completed(cl_sesion,step_complete,fe_creacion)VALUES('$clave','16',CURRENT_TIMESTAMP) ";
//    EjecutaInsert($Querysteps);


  }
  if($fg_paso==17){
      $Queryf3  = 'UPDATE k_ses_app_frm_3 SET ds_resp_4="'.$ds_resp_4_f3.'", ds_resp_5="'.$ds_resp_5_f3.'", ';
      $Queryf3 .= 'ds_resp_6="'.$ds_resp_6_f3.'", fe_ultmod=CURRENT_TIMESTAMP ';
      $Queryf3 .= 'WHERE cl_sesion="'.$clave.'" ';

//      $Querysteps="DELETE FROM steps_completed WHERE cl_sesion='$clave' AND step_complete='17' ";
//      EjecutaQuery($Querysteps);
//      $Querysteps="INSERT INTO steps_completed(cl_sesion,step_complete,fe_creacion)VALUES('$clave','17',CURRENT_TIMESTAMP) ";
//      EjecutaInsert($Querysteps);

  }
  if($fg_paso==18){
      $Queryf3  = 'UPDATE k_ses_app_frm_3 SET ds_resp_7="'.$ds_resp_7_f3.'", ds_resp_8="'.$ds_resp_8_f3.'", ';
      $Queryf3 .= 'fe_ultmod=CURRENT_TIMESTAMP ';
      $Queryf3 .= 'WHERE cl_sesion="'.$clave.'" ';
      EjecutaQuery("UPDATE c_sesion SET fg_app_3='1'  WHERE cl_sesion='".$clave."'");

 /*     $Querysteps="DELETE FROM steps_completed WHERE cl_sesion='$clave' AND step_complete='18' ";
      EjecutaQuery($Querysteps);
      $Querysteps="INSERT INTO steps_completed(cl_sesion,step_complete,fe_creacion)VALUES('$clave','18',CURRENT_TIMESTAMP) ";
      EjecutaInsert($Querysteps);
*/
  }
  if(!empty($Queryf3)){
      EjecutaQuery($Queryf3);
  }
  if($fg_paso==18){

      #Recuperamos sus respuestas.
      $QueryResp="SELECT ds_resp_1,ds_resp_2_1,ds_resp_2_2, ds_resp_2_3, ds_resp_3,ds_resp_4,ds_resp_5,ds_resp_6,ds_resp_7,ds_resp_8 FROM k_ses_app_frm_3  WHERE cl_sesion='".$clave."' ";
      $resp=RecuperaValor($QueryResp);
      $resp1=$resp[0];
      $resp2=$resp[1];
      $resp3=$resp[2];
      $resp4=$resp[3];

      $resp_3=$resp[4];
      $resp_4=$resp[5];
      $resp_5=$resp[6];
      $resp_6=$resp[7];if($resp_5=='A')$resp_5="Myself";if($resp_5=='B')$resp_5="My Instructor";if($resp_5=='C')$resp_5="Both";
      $resp_7=$resp[8];if($resp_7=='A')$resp_7="Less than an hour";if($resp_7=='B')$resp_7="1 hour";if($resp_7=='C')$resp_7="1.5 hour";if($resp_7=='D')$resp_7="2 hours";if($resp_7=='E')$resp_7="2.5 or more hours";
      $resp_8=$resp[9];

      #Enbvia email de step 4
      $subject = ObtenEtiqueta(336);
      $smtp = ObtenConfiguracion(4);
      $app_frm_email = ObtenConfiguracion(20);
      $message  = "Application form component Step 5/5 submitted <br>";
      $message .= ObtenEtiqueta(61)."<br>";
      $message .= ObtenEtiqueta(117).": $ds_fname<br><br>";
      $message .= ObtenEtiqueta(119).": $ds_mname<br><br>";
      $message .= ObtenEtiqueta(118).": $ds_lname<br><br>";
      $message .= ObtenEtiqueta(121).": $ds_email_aply<br><br>";
      $message .= ObtenEtiqueta(308).": $resp1<br><br>";
      $message .= ObtenEtiqueta(309).": $resp2<br><br>";
      $message .= " $resp3<br><br>";
      $message .= " $resp4<br><br>";


      $message .= ObtenEtiqueta(310).": $resp_3<br><br>";
      $message .= ObtenEtiqueta(311).": $resp_4<br><br>";
      $message .= ObtenEtiqueta(312).": $resp_5<br><br>";
      $message .= ObtenEtiqueta(313).": $resp_6<br><br>";
      $message .= ObtenEtiqueta(317).": $resp_7<br><br>";
      $message .= ObtenEtiqueta(323).": $resp_8<br><br>";

      $message = utf8_encode(str_ascii($message));
      #Enviamos notificacion a los administrators.
      $email = EnviaMailHTML($smtp, $smtp, $app_frm_email, $subject." Step 5/5", $message);

   #   $Query  = "UPDATE c_sesion SET fg_paypal='0', fg_confirmado='1', fg_pago='0', fe_ultmod=CURRENT_TIMESTAMP ";
	#  $Query .= "WHERE cl_sesion='$clave' ";
	#  EjecutaQuery($Query);

  }



  if($fg_paso==19){
   EnviarEmailForms($clave,false);
   # Actualiza estado del registro de aplicacion
   $Query  = "UPDATE c_sesion SET fg_paypal='0', fg_confirmado='1', fg_pago='0', fe_ultmod=CURRENT_TIMESTAMP ";
   $Query .= "WHERE cl_sesion='$clave'";
   EjecutaQuery($Query);
   # Si existe un registro de algun cupon lo eliminara
   EjecutaQuery("DELETE FROM k_app_cupon WHERE fl_sesion=(SELECT fl_sesion FROM c_sesion WHERE cl_sesion='".$clave."')");


//   $Querysteps="DELETE FROM steps_completed WHERE cl_sesion='$clave' AND step_complete='19' ";
//   EjecutaQuery($Querysteps);
//   $Querysteps="INSERT INTO steps_completed(cl_sesion,step_complete,fe_creacion)VALUES('$clave','19',CURRENT_TIMESTAMP) ";
//   EjecutaInsert($Querysteps);

  }
  # Actualiza los datos con el descuento
  if(!empty($fg_cupon)){
	$ds_code = RecibeParametroHTML('ds_code');
	$mn_descuento = RecibeParametroHTML('mn_descuento');
	# Insertamos en una tabla temporal el descuento
	# Este debe realizarse por paypal en caso contrario el contrato no sera
	# Tomando en cuenta
	$roww = RecuperaValor("SELECT fl_sesion FROM c_sesion WHERE cl_sesion='".$clave."'");
	EjecutaQuery("DELETE FROM k_app_cupon WHERE fl_sesion=".$roww[0]);
	$Queri  = "INSERT INTO k_app_cupon(fl_sesion, ds_code, ds_descuento, fe_app) ";
	$Queri .= "VALUES($roww[0], '".$ds_code."', '".$mn_descuento."', NOW()) ";
	EjecutaQuery($Queri);
	/*# Obtenemos la informacion del usuario
	$QueryR  = "SELECT mn_app_fee, mn_tuition, mn_a_due, mn_a_paid, no_a_payments, ";
	$QueryR .= "mn_b_due, mn_b_paid, no_b_payments, mn_c_due, mn_c_paid, no_c_payments,  mn_d_due, mn_d_paid,no_d_payments ";
	$QueryR .= "FROM k_programa_costos WHERE fl_programa='".$fl_programa."'";
	$rowr = RecuperaValor($QueryR);
	$mn_app_fee = $rowr[0];
	$mn_tuition = $rowr[1];
	$mn_a_due = $rowr[2];
	$mn_a_paid = $rowr[3];
	$no_a_payments = $rowr[4];
	$mn_b_due = $rowr[5];
	$mn_b_paid = $rowr[6];
	$no_b_payments = $rowr[7];
	$mn_c_due = $rowr[8];
	$mn_c_paid = $rowr[9];
	$no_c_payments = $rowr[10];
	$mn_d_due = $rowr[11];
	$mn_d_paid = $rowr[12];
	$no_d_payments = $rowr[13];

	$rowq = RecuperaValor("SELECT");
	# Calculos
	$mn_discount = $mn_descuento;
	$ds_discount = "You have discount with code: ".$ds_code;
	$mn_tot_tuition = $mn_tuition - $mn_discount;
	$mn_tot_program = $mn_tot_tuition + $mn_app_fee;
	# Obtenemos la informacion del programa
	if(!empty($no_a_payments)){
		$mn_a_paid = $mn_a_paid - $mn_discount;
		$mn_a_due = $mn_a_paid/$no_a_payments;
	}
	else{
		$mn_a_paid = 0;
		$mn_a_due = 0;
	}

	if(!empty($no_b_payments)){
		$mn_b_paid = $mn_b_paid - $mn_discount;
		$mn_b_due = $mn_b_paid/$no_b_payments;
	}
	else{
		$mn_b_paid = 0;
		$mn_b_due = 0;
	}
	if(!empty($no_c_payments)){
		$mn_c_paid = $mn_c_paid - $mn_discount;
		$mn_c_due = $mn_c_paid/$no_c_payments;
	}
	else{
		$mn_c_paid = 0;
		$mn_c_due = 0;
	}
	if(!empty($no_d_payments)){
		$mn_d_paid = $mn_d_paid - $mn_discount;
		$mn_d_due = $mn_d_paid/$no_d_payments;
	}
	else{
		$mn_d_paid = 0;
		$mn_d_due = 0;
	}
	$r  = "UPDATE k_app_contrato SET mn_discount=".$mn_discount.", ds_discount='".$ds_discount."', mn_tot_tuition=".$mn_tot_tuition.",  ";
	$r .= "mn_tot_program=".$mn_tot_program.", mn_a_due=".$mn_a_due.", mn_a_paid=".$mn_a_paid.", mn_b_due=".$mn_b_due.", mn_b_paid=".$mn_b_paid.", ";
	echo $r .= "mn_c_due=".$mn_c_due.", mn_c_paid=".$mn_c_paid.", mn_d_due=".$mn_d_due.", mn_d_paid=".$mn_d_paid." WHERE cl_sesion='".$clave."'";
	EjecutaQuery($r);*/



  }

  function EnviarEmailForms($clave,$email_f1=true,$fg_paso=''){
    # Prepara variables de ambiente para envio de correo FORM1
    $smtp = ObtenConfiguracion(4);
    $app_frm_email = ObtenConfiguracion(20);

    # sesion
    $QueryS = "SELECT ".ConsultaFechaBD('fe_ultmod', FMT_CAPTURA)." 'fe_ultmod',fg_stripe FROM c_sesion WHERE cl_sesion='".$clave."'";
    $rowS = RecuperaValor($QueryS);
    $fe_ultmod = $rowS[0];
    $fg_stripe=$rowS[1];

    # Envia correo de confirmacion al Administrador
    $QueryE  = "SELECT a.fl_programa, a.fl_periodo, ds_fname, ds_mname, ds_lname, ds_number,ds_alt_number, ds_email, fg_gender, ";
    $QueryE .= ConsultaFechaBD('fe_birth', FMT_CAPTURA)." fe_birth, ds_add_number, ";
    $QueryE .= "ds_add_street, ds_add_city, ds_add_state, ds_add_zip, ds_add_country, fg_responsable, ds_eme_fname, ds_eme_lname, ds_eme_number, ";
    $QueryE .= "ds_eme_relation, ds_eme_relation_other, ds_eme_country, cl_recruiter, fg_ori_via, ds_ori_other, fg_ori_ref, ds_ori_ref_name, ";
    $QueryE .= "nb_programa, nb_periodo ";
    //$QueryE .= ", e.ds_pais ";
    $QueryE .= "FROM k_ses_app_frm_1 a, c_programa b, c_periodo c ";
    $QueryE .= "WHERE a.fl_programa=b.fl_programa ";
    $QueryE .= "AND a.fl_periodo=c.fl_periodo ";
    //$QueryE .= "AND a.ds_add_country=d.fl_pais ";
    //$QueryE .= "AND a.ds_eme_country=e.fl_pais ";
    $QueryE .= "AND cl_sesion='".$clave."'";
    $rowE = RecuperaValor($QueryE);
    $fl_programa = $rowE[0];
    $fl_periodo = $rowE[1];
    $ds_fname = $rowE[2];
    $ds_mname = $rowE[3];
    $ds_lname = $rowE[4];
    $ds_number = $rowE[5];
    $ds_alt_number = $rowE[6];
    $ds_email = $rowE[7];
    $fg_gender = $rowE[8];
    $fe_birth = $rowE[9];
    $ds_add_number = str_texto($rowE[10]);
    $ds_add_street = str_texto($rowE[11]);
    $ds_add_city = str_texto($rowE[12]);
    $ds_add_state = str_texto($rowE[13]);
    $ds_add_zip = str_texto($rowE[14]);
    $ds_add_country = $rowE[15];
    $fg_responsable = $rowE[16];
    $ds_eme_fname = str_texto($rowE[17]);
    $ds_eme_lname = str_texto($rowE[18]);
    $ds_eme_number = $rowE[19];
    $ds_eme_relation = str_texto($rowE[20]);
    $ds_eme_relation_other = str_texto($rowE[21]);
    $ds_eme_country = $rowE[22];
    $cl_recruiter = $rowE[23];
    $fg_ori_via = $rowE[24];
    $ds_ori_other = $rowE[25];
    $fg_ori_ref = $rowE[26];
    $ds_ori_ref_name = $rowE[27];
    $nb_programa = $rowE[28];
    $nb_periodo = $rowE[29];

    #Recupermaos pais
    $Query="SELECT b.ds_pais,a.ds_add_country FROM k_ses_app_frm_1 a JOIN c_pais b ON b.fl_pais=a.ds_eme_country WHERE cl_sesion='$clave' ";
    $rop=RecuperaValor($Query);
    $ds_pais = $rop[0];
    $ds_add_country=$rop['ds_add_country'];

    //if(empty($ds_add_country)){
      //  EjecutaQuery("UPDATE k_ses_app_frm_1 set ds_add_country=$ds_pais WHERE cl_sesion='$clave'   ");
    //}



    if(is_numeric($ds_add_country)) {

        $Query="SELECT ds_pais FROM c_pais where fl_pais= $ds_add_country ";
        $row=RecuperaValor($Query);
        $ds_add_country=str_texto($row[0]);

    }

    if(is_numeric($ds_add_state)) {

        $Query="SELECT ds_provincia FROM k_provincias where fl_provincia= $ds_add_state ";
        $row=RecuperaValor($Query);
        $ds_add_state=str_texto($row[0]);

    }



    # Consulta contrato
    $QueryC  = "SELECT ds_p_name, ds_education_number, fg_international, ds_m_add_number, ds_m_add_street, ds_m_add_city, ds_m_add_state, ds_m_add_zip, ds_m_add_country,  ";
    $QueryC .= "ds_a_email, cl_preference_1, cl_preference_2, cl_preference_3,fl_class_time FROM k_app_contrato WHERE cl_sesion='".$clave."' AND no_contrato=1";
    $rowC = RecuperaValor($QueryC);
    $ds_p_name = str_texto($rowC[0]);
    $ds_education_number = str_texto($rowC[1]);
    $fg_international = $rowC[2];
    $ds_m_add_number = str_texto($rowC[3]);
    $ds_m_add_street = str_texto($rowC[4]);
    $ds_m_add_city = str_texto($rowC[5]);
    $ds_m_add_state = str_texto($rowC[6]);
    $ds_m_add_zip = str_texto($rowC[7]);
    $ds_m_add_country = str_texto($rowC[8]);
    $ds_a_email = str_texto($rowC[9]);
    $cl_preference_1 = $rowC[10];
    $cl_preference_2 = $rowC[11];
    $cl_preference_3 = $rowC[12];
    $fl_class_time=$rowC['fl_class_time'];



    # Recupera datos del aplicante: forma 2
    $Query  = "SELECT ds_resp_1, ds_resp_2, ds_resp_3, ds_resp_4, ds_resp_5, ds_resp_6, ds_resp_7 ";
    $Query .= "FROM k_ses_app_frm_2 ";
    $Query .= "WHERE cl_sesion='$clave'";
    $row = RecuperaValor($Query);
    $ds_resp2_1 = str_ascii($row[0]);
    $ds_resp2_2 = str_ascii($row[1]);
    $ds_resp2_3 = str_ascii($row[2]);
    $ds_resp2_4 = str_ascii($row[3]);
    $ds_resp2_5 = str_ascii($row[4]);
    $ds_resp2_6 = str_ascii($row[5]);
    $ds_resp2_7 = str_ascii($row[6]);

    # Recupera datos del aplicante: forma 3
    $Query  = "SELECT fg_resp_1_1, fg_resp_1_2, fg_resp_1_3, fg_resp_1_4, fg_resp_1_5, fg_resp_1_6, ";
    $Query .= "fg_resp_2_1, fg_resp_2_2, fg_resp_2_3, fg_resp_2_4, fg_resp_2_5, fg_resp_2_6, fg_resp_2_7, ";
    $Query .= "fg_resp_3_1, fg_resp_3_2 ";
    $Query .= "FROM k_ses_app_frm_4 ";
    $Query .= "WHERE cl_sesion='$clave'";
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
    $Query .= "WHERE cl_sesion='$clave'";
    $row = RecuperaValor($Query);
    $ds_resp3_1 = str_ascii($row[0]);
    $ds_resp3_2_1 = str_ascii($row[1]);
    $ds_resp3_2_2 = str_ascii($row[2]);
    $ds_resp3_2_3 = str_ascii($row[3]);
    $ds_resp3_3 = str_ascii($row[4]);
    $ds_resp3_4 = str_ascii($row[5]);
    $ds_resp3_5 = str_ascii($row[6]);
    $ds_resp3_6 = str_ascii($row[7]);
    $ds_resp3_7 = str_ascii($row[8]);
    $ds_resp3_8 = str_ascii($row[9]);
    # Priemr email cuando termina la forma 1
    if($email==true){
      $subject = ObtenEtiqueta(336);
      $message  = "Application form component 1 submitted <br>";
      $message .= "<br>";
      $message .= ObtenEtiqueta(55)."<br>";
      $rs = RecuperaValor("SELECT nb_programa FROM c_programa WHERE fl_programa = $fl_programa");
      $message .= ObtenEtiqueta(59).": $rs[0]<br>";
      $message .= "<br>";
      $rs = RecuperaValor("SELECT nb_periodo FROM c_periodo WHERE fl_periodo = $fl_periodo");
      $message .= ObtenEtiqueta(60).": $rs[0]<br>";
      $message .= "<br>";
      $message .= ObtenEtiqueta(61)."<br>";
      $message .= ObtenEtiqueta(117).": $ds_fname<br>";
      $message .= ObtenEtiqueta(119).": $ds_mname<br>";
      $message .= ObtenEtiqueta(118).": $ds_lname<br>";
      $message .= "<br>";
      $message .= ObtenEtiqueta(631).": $ds_p_name<br>";
      $message .= ObtenEtiqueta(632).": $ds_education_number<br>";
      if($fg_international == '0')
        $ds_international = ObtenEtiqueta(17);
      else
        $ds_international = ObtenEtiqueta(16);
      $message .= ObtenEtiqueta(620).": $ds_international<br>";
      $message .= "<br>";

      $message .= ObtenEtiqueta(280).": $ds_number<br>";
      $message .= ObtenEtiqueta(281).": $ds_alt_number<br>";
      $message .= ObtenEtiqueta(121).": $ds_email<br>";
      $message .= ObtenEtiqueta(127).": $ds_a_email<br>";
      $message .= "<br>";

      $message .= ObtenEtiqueta(114).": ";
      if($fg_gender == 'M')
        $message .= ObtenEtiqueta(115)."<br>";
      else
        $message .= ObtenEtiqueta(116)."<br>";
      $message .= ObtenEtiqueta(120).": $fe_birth<br>";
      $message .= "<br>";

      switch($cl_preference_1) {
        case 1: $ds_preference_1 = ObtenEtiqueta(624); break;
        case 2: $ds_preference_1 = ObtenEtiqueta(625); break;
        case 3: $ds_preference_1 = ObtenEtiqueta(626); break;
        case 4: $ds_preference_1 = ObtenEtiqueta(627); break;
        case 5: $ds_preference_1 = ObtenEtiqueta(628); break;
        case 6: $ds_preference_1 = ObtenEtiqueta(629); break;
        case 7: $ds_preference_1 = ObtenEtiqueta(630); break;
      }
      switch($cl_preference_2) {
        case 1: $ds_preference_2 = ObtenEtiqueta(624); break;
        case 2: $ds_preference_2 = ObtenEtiqueta(625); break;
        case 3: $ds_preference_2 = ObtenEtiqueta(626); break;
        case 4: $ds_preference_2 = ObtenEtiqueta(627); break;
        case 5: $ds_preference_2 = ObtenEtiqueta(628); break;
        case 6: $ds_preference_2 = ObtenEtiqueta(629); break;
        case 7: $ds_preference_2 = ObtenEtiqueta(630); break;
      }
      switch($cl_preference_3) {
        case 1: $ds_preference_3 = ObtenEtiqueta(624); break;
        case 2: $ds_preference_3 = ObtenEtiqueta(625); break;
        case 3: $ds_preference_3 = ObtenEtiqueta(626); break;
        case 4: $ds_preference_3 = ObtenEtiqueta(627); break;
        case 5: $ds_preference_3 = ObtenEtiqueta(628); break;
        case 6: $ds_preference_3 = ObtenEtiqueta(629); break;
        case 7: $ds_preference_3 = ObtenEtiqueta(630); break;
      }


      $message .= ObtenEtiqueta(621)."<br>";
      $message .= ObtenEtiqueta(622).": $ds_preference_1<br>";
      $message .= ObtenEtiqueta(623).": $ds_preference_2<br>";
      $message .= ObtenEtiqueta(616).": $ds_preference_3<br>";
      $message .= "<br>";
      # Address
      $message .= ObtenEtiqueta(62)."<br>";
      $message .= ObtenEtiqueta(282).": $ds_add_number<br>";
      $message .= ObtenEtiqueta(283).": $ds_add_street<br>";
      $message .= ObtenEtiqueta(284).": $ds_add_city<br>";
      # si el pais es canada mostra la provincia que selecciono
      if($ds_add_country==38){
        $row = RecuperaValor("SELECT ds_provincia FROM k_provincias WHERE fl_provincia=$fl_provincia");
        $ds_add_state = $row[0];
      }
      $message .= ObtenEtiqueta(285).": $ds_add_state<br>";
      $message .= ObtenEtiqueta(286).": $ds_add_zip<br>";
      $rs = RecuperaValor("SELECT ds_pais FROM c_pais WHERE fl_pais = $ds_add_country");
      $message .= ObtenEtiqueta(287).": $rs[0]<br>";
      $message .= "<br>";
      $QueryR = "SELECT ds_fname_r, ds_lname_r, ds_email_r, ds_aemail_r, ds_pnumber_r, ds_relation_r, ds_relation_r_other ";
      $QueryR .= "FROM k_presponsable WHERE cl_sesion='".$clave."'";
      $rowR = RecuperaValor($QueryR);
      $ds_fname_r = str_texto($rowR[0]);
      $ds_lname_r = str_texto($rowR[1]);
      $ds_email_r = str_texto($rowR[2]);
      $ds_aemail_r = str_texto($rowR[3]);
      $ds_pnumber_r = str_texto($rowR[4]);
      $ds_relation_r = $rowR[5];
      $ds_relation_r_other = str_texto($rowR[6]);
      # Person Responsible
      $message .= ObtenEtiqueta(865).".<br>";
      if($fg_responsable==1)
        $message .= ObtenEtiqueta(866);
      else{
        $message .= ObtenEtiqueta(867);
        $message .= "<br> ".ObtenEtiqueta(868).": ".$ds_fname_r."<br>";
        $message .= " ".ObtenEtiqueta(869).": ".$ds_lname_r."<br>";
        $message .= " ".ObtenEtiqueta(870).": ".$ds_email_r."<br>";
        $message .= " ".ObtenEtiqueta(871).": ".$ds_aemail_r."<br>";
        $message .= " ".ObtenEtiqueta(872).": ".$ds_pnumber_r."<br>";
        if($ds_relation_r == ObtenEtiqueta(2254))
          $message .= " ".ObtenEtiqueta(873).": ".$ds_relation_r_other."<br>";
        else
          $message .= " ".ObtenEtiqueta(873).": ".$ds_relation_r."<br>";
      }
      $message .="<br><br>";
      # Mailing Address (If different from above)
      $message .= ObtenEtiqueta(633)."<br>";
      $message .= ObtenEtiqueta(282).": $ds_m_add_number<br>";
      $message .= ObtenEtiqueta(283).": $ds_m_add_street<br>";
      $message .= ObtenEtiqueta(284).": $ds_m_add_city<br>";
      $message .= ObtenEtiqueta(285).": $ds_m_add_state<br>";
      $message .= ObtenEtiqueta(286).": $ds_m_add_zip<br>";
      $rs = RecuperaValor("SELECT ds_pais FROM c_pais WHERE fl_pais = $ds_m_add_country");
      $message .= ObtenEtiqueta(287).": $rs[0]<br>";
      $message .= "<br>";

      # Emergency Contact Information
      $message .= ObtenEtiqueta(63)."<br>";
      $message .= ObtenEtiqueta(117).": $ds_eme_fname<br>";
      $message .= ObtenEtiqueta(118).": $ds_eme_lname<br>";
      $message .= ObtenEtiqueta(280).": $ds_eme_number<br>";
      if($ds_eme_relation == ObtenEtiqueta(2254))
        $message .= ObtenEtiqueta(288).": $ds_eme_relation_other<br>";
      else
        $message .= ObtenEtiqueta(288).": $ds_eme_relation<br>";
      $rs = RecuperaValor("SELECT ds_pais FROM c_pais WHERE fl_pais = $ds_eme_country");
      $message .= ObtenEtiqueta(287).": $rs[0]<br>";
      $message .= "<br>";
      # Representative
      $message .= ObtenEtiqueta(876)."<br>";
      $rowr = RecuperaValor("SELECT CONCAT(ds_nombres,' ', ds_apaterno) FROM c_usuario WHERE fl_usuario=$cl_recruiter");
      $message .= ObtenEtiqueta(877).": $rowr[0]<br>";
      $message .= ObtenEtiqueta(289)." ";
      switch($fg_ori_via) {
        case 'A': $message .= ObtenEtiqueta(290)."<br>"; break;
        case 'B': $message .= ObtenEtiqueta(291)."<br>"; break;
        case 'C': $message .= ObtenEtiqueta(292)."<br>"; break;
        case 'D': $message .= ObtenEtiqueta(293)."<br>"; break;
        case '0': $message .= ObtenEtiqueta(294)." - $ds_ori_other<br>"; break;
      }
      $message .= "<br>";
      $message .= ObtenEtiqueta(295)." ";
      switch($fg_ori_ref) {
        case '0': $message .= ObtenEtiqueta(17)."<br>"; break;
        case 'S': $message .= ObtenEtiqueta(296)." - $ds_ori_ref_name<br>"; break;
        case 'T': $message .= ObtenEtiqueta(297)." - $ds_ori_ref_name<br>"; break;
        case 'G': $message .= ObtenEtiqueta(298)." - $ds_ori_ref_name<br>"; break;
        case 'A': $message .= ObtenEtiqueta(811)." - $ds_ori_ref_name<br>"; break;
      }
      $message .= "<br><br>";
      $message = utf8_encode(str_ascii($message));
      $email = EnviaMailHTML($smtp, $smtp, $app_frm_email, $subject, $message);



    }
    else{

      # Envia correo de confirmacion al aplicante
      $subject = ObtenEtiqueta(335);
      $message  = "Dear $ds_fname $ds_lname,<br>";
      $message .= "<br>";
      $message .= ObtenEtiqueta(331)."<br>";
      $message .= ObtenEtiqueta(332)."<br>";
      $message .= ObtenEtiqueta(333)."<br><br>";
      $message .= ObtenEtiqueta(337)."<br>";
      $message .= ObtenEtiqueta(338)."<br>";
      $message = utf8_encode(str_ascii($message));
      if($fg_paso==19)
      EnviaMailHTML($smtp, $smtp, $ds_email, $subject, $message);


      if($fg_paso==10){
          $step=" Step 2/5";



      }else{
          $step="";
      }
      # Envia correo de confirmacion al Administrador
      $subject = ObtenEtiqueta(336).$step;
      $message  = "Application form submitted $step $fe_ultmod<br>";
      if($fg_stripe == '1')
        $message .= ObtenEtiqueta(343).".<br>";
      else
        $message .= "Payment not submitted.<br>";
      $message .= "<br>";
      $message .= ObtenEtiqueta(55)."<br>";
      $message .= ObtenEtiqueta(59).": $nb_programa<br>";
      $message .= ObtenEtiqueta(60).": $nb_periodo<br>";
      $message .= "<br>";
      $message .= ObtenEtiqueta(61)."<br>";
      $message .= ObtenEtiqueta(117).": $ds_fname<br>";
      $message .= ObtenEtiqueta(119).": $ds_mname<br>";
      $message .= ObtenEtiqueta(118).": $ds_lname<br>";
      $message .= "<br>";
      $message .= ObtenEtiqueta(280).": $ds_number<br>";
      $message .= ObtenEtiqueta(281).": $ds_alt_number<br>";
      $message .= ObtenEtiqueta(121).": $ds_email<br>";
      $message .= "<br>";
      $message .= ObtenEtiqueta(114).": ";
      if($fg_gender == 'M')
        $message .= ObtenEtiqueta(115)."<br>";
      else
        $message .= ObtenEtiqueta(116)."<br>";

      $message .= "<br>";
      $message .= ObtenEtiqueta(62)."<br>";
      $message .= ObtenEtiqueta(282).": $ds_add_number<br>";
      $message .= ObtenEtiqueta(283).": $ds_add_street<br>";
      $message .= ObtenEtiqueta(284).": $ds_add_city<br>";
      $message .= ObtenEtiqueta(285).": $ds_add_state<br>";
      $message .= ObtenEtiqueta(286).": $ds_add_zip<br>";
      $message .= ObtenEtiqueta(287).": $ds_add_country<br>";
      $message .= "<br>";
      $message .= ObtenEtiqueta(63)."<br>";
      $message .= ObtenEtiqueta(117).": $ds_eme_fname<br>";
      $message .= ObtenEtiqueta(118).": $ds_eme_lname<br>";
      $message .= ObtenEtiqueta(280).": $ds_eme_number<br>";
      $message .= ObtenEtiqueta(288).": $ds_eme_relation<br>";
      #Recuperamos la ciudad.
      $rs = RecuperaValor("SELECT ds_pais FROM c_pais WHERE fl_pais = $ds_eme_country ");
      $message .= ObtenEtiqueta(287).":$rs[0]<br>";
      $message .= "<br>";
    if($fg_paso==10){

    }

 if($fg_paso<>10){
      $message .= ObtenEtiqueta(289)." ";
      switch($fg_ori_via) {
        case 'A': $message .= ObtenEtiqueta(290)."<br>"; break;
        case 'B': $message .= ObtenEtiqueta(291)."<br>"; break;
        case 'C': $message .= ObtenEtiqueta(292)."<br>"; break;
        case 'D': $message .= ObtenEtiqueta(293)."<br>"; break;
        case '0': $message .= ObtenEtiqueta(294)." - $ds_ori_other<br>"; break;
      }
      $message .= ObtenEtiqueta(295)." ";
      switch($fg_ori_ref) {
        case '0': $message .= ObtenEtiqueta(17)."<br>"; break;
        case 'S': $message .= ObtenEtiqueta(296)." - $ds_ori_ref_name<br>"; break;
        case 'T': $message .= ObtenEtiqueta(297)." - $ds_ori_ref_name<br>"; break;
        case 'G': $message .= ObtenEtiqueta(298)." - $ds_ori_ref_name<br>"; break;
        case 'A': $message .= ObtenEtiqueta(811)." - $ds_ori_ref_name<br>"; break;
      }
      $message .= "<br><br>";
      $message .= ObtenEtiqueta(56)."<br>";
      $message .= ObtenEtiqueta(301)."<br>$ds_resp2_1<br>";
      $message .= ObtenEtiqueta(302)."<br>$ds_resp2_2<br>";
      $message .= ObtenEtiqueta(303)."<br>$ds_resp2_3<br>";
      $message .= ObtenEtiqueta(304)."<br>$ds_resp2_4<br>";
      $message .= ObtenEtiqueta(305)."<br>$ds_resp2_5<br>";
      $message .= ObtenEtiqueta(306)."<br>$ds_resp2_6<br>";
      $message .= ObtenEtiqueta(307)."<br>$ds_resp2_7<br>";
      $message .= "<br>";

      $etq_si = ObtenEtiqueta(16);
      $etq_no = ObtenEtiqueta(17);
      $message .= ObtenEtiqueta(78)."<br>";

      $message .= ObtenEtiqueta(79)."<br>";
      $message .= ObtenEtiqueta(82)."<br>";
      switch($fg_resp4_1_1) {
        case '1': $message .= $etq_si."<br>"; break;
        case '0': $message .= $etq_no."<br>"; break;
      }
      $message .= ObtenEtiqueta(83)."<br>";
      switch($fg_resp4_1_2) {
        case '1': $message .= $etq_si."<br>"; break;
        case '0': $message .= $etq_no."<br>"; break;
      }
      $message .= ObtenEtiqueta(84)."<br>";
      switch($fg_resp4_1_3) {
        case '1': $message .= $etq_si."<br>"; break;
        case '0': $message .= $etq_no."<br>"; break;
      }
      $message .= ObtenEtiqueta(85)."<br>";
      switch($fg_resp4_1_4) {
        case '1': $message .= $etq_si."<br>"; break;
        case '0': $message .= $etq_no."<br>"; break;
      }
      $message .= ObtenEtiqueta(86)."<br>";
      switch($fg_resp4_1_5) {
        case '1': $message .= $etq_si."<br>"; break;
        case '0': $message .= $etq_no."<br>"; break;
      }
      $message .= ObtenEtiqueta(87)."<br>";
      switch($fg_resp4_1_6) {
        case '1': $message .= $etq_si."<br>"; break;
        case '0': $message .= $etq_no."<br>"; break;
      }

      $message .= ObtenEtiqueta(80)."<br>";
      $message .= ObtenEtiqueta(88)."<br>";
      switch($fg_resp4_2_1) {
        case '1': $message .= $etq_si."<br>"; break;
        case '0': $message .= $etq_no."<br>"; break;
      }
      $message .= ObtenEtiqueta(89)."<br>";
      switch($fg_resp4_2_2) {
        case '1': $message .= $etq_si."<br>"; break;
        case '0': $message .= $etq_no."<br>"; break;
      }
      $message .= ObtenEtiqueta(90)."<br>";
      switch($fg_resp4_2_3) {
        case '1': $message .= $etq_si."<br>"; break;
        case '0': $message .= $etq_no."<br>"; break;
      }
      $message .= ObtenEtiqueta(91)."<br>";
      switch($fg_resp4_2_4) {
        case '1': $message .= $etq_si."<br>"; break;
        case '0': $message .= $etq_no."<br>"; break;
      }
      $message .= ObtenEtiqueta(92)."<br>";
      switch($fg_resp4_2_5) {
        case '1': $message .= $etq_si."<br>"; break;
        case '0': $message .= $etq_no."<br>"; break;
      }
      $message .= ObtenEtiqueta(93)."<br>";
      switch($fg_resp4_2_6) {
        case '1': $message .= $etq_si."<br>"; break;
        case '0': $message .= $etq_no."<br>"; break;
      }
      $message .= ObtenEtiqueta(94)."<br>";
      switch($fg_resp4_2_7) {
        case '1': $message .= $etq_si."<br>"; break;
        case '0': $message .= $etq_no."<br>"; break;
      }

      $message .= ObtenEtiqueta(81)."<br>";
      $message .= ObtenEtiqueta(95)."<br>";
      switch($fg_resp4_3_1) {
        case '0': $message .= ObtenEtiqueta(97)."<br>"; break;
        case '1': $message .= ObtenEtiqueta(98)."<br>"; break;
        case '2': $message .= ObtenEtiqueta(99)."<br>"; break;
        case '3': $message .= ObtenEtiqueta(107)."<br>"; break;
      }
      $message .= ObtenEtiqueta(96)."<br>";
      switch($fg_resp4_3_2) {
        case '0': $message .= ObtenEtiqueta(97)."<br>"; break;
        case '1': $message .= ObtenEtiqueta(98)."<br>"; break;
        case '2': $message .= ObtenEtiqueta(99)."<br>"; break;
        case '3': $message .= ObtenEtiqueta(107)."<br>"; break;
      }

      $message .= "<br>";
      $message .= ObtenEtiqueta(57)."<br>";
      $message .= ObtenEtiqueta(308)."<br>$ds_resp3_1<br>";
      $message .= ObtenEtiqueta(309)."<br>";
      $message .= "1: $ds_resp3_2_1<br>";
      $message .= "2: $ds_resp3_2_2<br>";
      $message .= "3: $ds_resp3_2_3<br>";
      $message .= ObtenEtiqueta(310)."<br>$ds_resp3_3<br>";
      $message .= ObtenEtiqueta(311)."<br>$ds_resp3_4<br>";
      $message .= ObtenEtiqueta(312)."<br>$ds_resp3_5<br>";
      $message .= ObtenEtiqueta(313)."<br>";
      switch($ds_resp3_6) {
        case 'A': $message .= ObtenEtiqueta(314)."<br>"; break;
        case 'B': $message .= ObtenEtiqueta(315)."<br>"; break;
        case 'C': $message .= ObtenEtiqueta(316)."<br>"; break;
      }
      $message .= ObtenEtiqueta(317)."<br>";
      switch($ds_resp3_7) {
        case 'A': $message .= ObtenEtiqueta(318)."<br>"; break;
        case 'B': $message .= ObtenEtiqueta(319)."<br>"; break;
        case 'C': $message .= ObtenEtiqueta(320)."<br>"; break;
        case 'D': $message .= ObtenEtiqueta(321)."<br>"; break;
        case 'E': $message .= ObtenEtiqueta(322)."<br>"; break;
      }
      $message .= ObtenEtiqueta(323)."<br>$ds_resp3_8<br>";

}

      $message .= "<br><br>";
      $message = utf8_encode(str_ascii($message));

      $email = EnviaMailHTML($smtp, $smtp, $app_frm_email, $subject, $message);

      # Actualiza estado del registro de aplicacion
      $Query  = "UPDATE c_sesion SET $Query_extra ";
      $Query .= "WHERE cl_sesion='$clave'";
      EjecutaQuery($Query);
    }
    return $email;
  }



 function GeneraLog($file_name_txt,$contenido_log=''){

    $fch= fopen($file_name_txt, "a+"); // Abres el archivo para escribir en él
    fwrite($fch, "\n".$contenido_log); // Grabas
    fclose($fch); // Cierras el archivo.
}
?>
