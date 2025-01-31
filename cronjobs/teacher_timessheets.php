<?php
if (PHP_OS == 'Linux') {
    # Include campus libraries
    require '/var/www/html/vanas/lib/com_func.inc.php';
    require '/var/www/html/vanas/lib/sp_config.inc.php';
} else {

     require '../lib/com_func.inc.php';
     require '../lib/sp_config.inc.php';
}




  $from = 'noreply@vanas.ca';

  #template para notificacion
  $Query = "SELECT ds_encabezado, ds_cuerpo, ds_pie,nb_template FROM k_template_doc WHERE fl_template='28' AND fg_activo='1'";
	$st_live_template = RecuperaValor($Query);
	$ds_template = str_uso_normal($st_live_template[0].$st_live_template[1].$st_live_template[2]);
  $nb_template = str_texto($st_live_template[3]);

  # Create a DOM object
#  $ds_template_html = new simple_html_dom();

  # Variables  //el proceso debe ejecutarse al final de cada mes, por ende calcula los del mes actual, pero tiene que adelantarse 1mes para que asi hagan macht con el mes actual.
  $fecha_actual = date("d-m-Y");

  $mes_act = date("m",strtotime($fecha_actual."- 0 month"));
  $anio_act = date('Y',strtotime($fecha_actual."- 0 year"));
  $mes_anio_act = date("m-Y",strtotime($fecha_actual."- 0 month"));
  $current_month = date("M",strtotime($fecha_actual."- 0 month"));

  #$mes_anio_act="11-2024";
  #$mes_act="11";
  #$anio_act="2024";
  #$current_month="November";
  $curdate="DATE_SUB(curdate(),INTERVAL 5 day)";

  # teacher que tiene grupo con alumnos activos en el mes actual
  $Query_1  = "SELECT fl_maestro FROM  k_clase a, c_grupo  b ";
  $Query_1 .= "WHERE a.fl_grupo=b.fl_grupo AND MONTH(fe_clase)='".$mes_act."' AND YEAR( a.fe_clase)='".$anio_act."' ";
  # Comentamos estas lineas porque la catidad de alumno se obtendra del hitorial
  // $Query_1 .= "AND (SELECT COUNT(*) FROM k_alumno_grupo f, c_usuario d ";
  // $Query_1 .= "WHERE f.fl_alumno=d.fl_usuario AND f.fl_grupo=b.fl_grupo AND f.fl_grupo=a.fl_grupo AND d.fg_activo='1')>=0 GROUP BY b.fl_maestro ";
  $Query_1 .= " AND b.no_alumnos>0  GROUP BY b.fl_maestro ";
  $rs_1 = EjecutaQuery($Query_1);
  for($i=0;$row_1 = RecuperaRegistro($rs_1);$i++){
    $fl_maestro = $row_1[0];

    # verificamos que no haya registros en el mes actual del teacher
    $row_2 = RecuperaValor("SELECT fl_maestro_pago FROM k_maestro_pago WHERE DATE_FORMAT(fe_periodo,'%m-%Y')='".$mes_anio_act."' AND fl_maestro=$fl_maestro");
    $fl_maestro_pago = $row_2[0];
    # Si no existe va a insertar sus pagos del mes
    if(empty($fl_maestro_pago)){
      $Insert_pago  = "INSERT INTO k_maestro_pago(fl_maestro, fe_periodo, mn_total, fg_publicar, fg_pagado, fe_pagado) ";
      $Insert_pago .= " VALUES($fl_maestro,$curdate,0.0,'1', '0', Null ) ";
      $fl_maestro_pago=EjecutaInsert($Insert_pago);
      $Insert_pago_det  = "SELECT  a.fl_grupo,d.fl_clase, ";
      $Insert_pago_det .= "CASE d.fg_adicional WHEN '0' THEN IFNULL((SELECT t.mn_lecture_fee FROM k_maestro_tarifa t WHERE t.fl_programa=e.fl_programa AND t.fl_grupo=a.fl_grupo AND t.fl_maestro=$fl_maestro),e.mn_lecture_fee) ";
      $Insert_pago_det .= "ELSE IFNULL((SELECT t.mn_extra_fee FROM k_maestro_tarifa t WHERE t.fl_programa=e.fl_programa AND t.fl_grupo=a.fl_grupo AND t.fl_maestro=$fl_maestro),e.mn_extra_fee) END hourly_rate ";
      $Insert_pago_det .= "FROM c_grupo a, k_clase d, c_programa e, k_term f ,k_semana b LEFT JOIN c_leccion c ON(c.fl_leccion=b.fl_leccion) ";
      $Insert_pago_det .= "WHERE a.fl_term = b.fl_term AND a.fl_grupo=d.fl_grupo AND b.fl_semana=d.fl_semana AND c.fl_programa = e.fl_programa ";
      $Insert_pago_det .= "AND c.fl_programa=e.fl_programa AND a.fl_term = f.fl_term AND b.fl_term = f.fl_term AND DATE_FORMAT(d.fe_clase,'%m-%Y')='".$mes_anio_act."' ";
      $Insert_pago_det .= "AND a.fl_maestro=$fl_maestro ORDER BY d.fe_clase ";
      $rs_det = EjecutaQuery($Insert_pago_det);
      for($j=0;$row_det=RecuperaRegistro($rs_det);$j++){

		  $fl_clase=$row_det['fl_clase'];





        $row_det2 = RecuperaValor("SELECT *FROM k_maestro_pago_det WHERE fl_maestro_pago=$fl_maestro_pago AND fg_tipo='A' AND fl_grupo=".$row_det[0]." AND ds_concepto=".$row_det[1]."");
        if(empty($row_det2[0])){

			$mn_tarifa_hr=$row_det[2];
			$mn_subtotal=$row_det[2];

			#se define la tarifa default por hora.
			$Queryt="SELECT mn_hour_rate FROM c_maestro WHERE fl_maestro=$fl_maestro ";
			$rot=RecuperaValor($Queryt);
			$mn_tarifa_default=$rot['mn_hour_rate'];
			if(!empty($mn_tarifa_default)){
				$mn_tarifa_hr=$mn_tarifa_default;
				$mn_subtotal=$mn_tarifa_default;
			}

          $Query_det  = "INSERT INTO k_maestro_pago_det(fl_maestro_pago,fg_tipo,fl_grupo,ds_concepto,mn_tarifa_hr,no_horas, mn_subtotal) ";
          $Query_det .= "VALUES($fl_maestro_pago,'A',$row_det[0],'".$row_det[1]."',$mn_tarifa_hr,'1',$mn_subtotal) ";
          EjecutaQuery($Query_det);
        }


		$Queryte="SELECT mn_hour_rate FROM c_maestro WHERE fl_maestro=$fl_maestro ";
		$rott=RecuperaValor($Queryte);
		$mn_tarifa_default_rate=$rott['mn_hour_rate'];

		$Query="UPDATE k_clase SET mn_rate=$mn_tarifa_default_rate WHERE fl_clase=$fl_clase ";
        EjecutaQuery($Query);



      }
      EjecutaQuery("UPDATE k_maestro_pago SET mn_total=(SELECT SUM(mn_subtotal) FROM k_maestro_pago_det WHERE fl_maestro_pago=$fl_maestro_pago) WHERE fl_maestro_pago=$fl_maestro_pago ");
    }
    # Si el maestro ya tiene registro del mes solo actualiza el fg_publicar a 1
    else{
      EjecutaQuery("UPDATE k_maestro_pago SET fg_publicar='1' WHERE fl_maestro=$fl_maestro AND DATE_FORMAT(fe_periodo,'%m-%Y')='".$mes_anio_act."' ");
      $Insert_pago_det  = "SELECT  a.fl_grupo,d.fl_clase, ";
      $Insert_pago_det .= "CASE d.fg_adicional WHEN '0' THEN IFNULL((SELECT t.mn_lecture_fee FROM k_maestro_tarifa t WHERE t.fl_programa=e.fl_programa AND t.fl_grupo=a.fl_grupo AND t.fl_maestro=$fl_maestro),e.mn_lecture_fee) ";
      $Insert_pago_det .= "ELSE IFNULL((SELECT t.mn_extra_fee FROM k_maestro_tarifa t WHERE t.fl_programa=e.fl_programa AND t.fl_grupo=a.fl_grupo AND t.fl_maestro=$fl_maestro),e.mn_extra_fee) END hourly_rate ";
      $Insert_pago_det .= "FROM c_grupo a, k_clase d, c_programa e, k_term f ,k_semana b LEFT JOIN c_leccion c ON(c.fl_leccion=b.fl_leccion) ";
      $Insert_pago_det .= "WHERE a.fl_term = b.fl_term AND a.fl_grupo=d.fl_grupo AND b.fl_semana=d.fl_semana AND c.fl_programa = e.fl_programa ";
      $Insert_pago_det .= "AND c.fl_programa=e.fl_programa AND a.fl_term = f.fl_term AND b.fl_term = f.fl_term AND DATE_FORMAT(d.fe_clase,'%m-%Y')='".$mes_anio_act."' ";
      $Insert_pago_det .= "AND a.fl_maestro=$fl_maestro ORDER BY d.fe_clase ";
      $rs_det = EjecutaQuery($Insert_pago_det);
      for($j=0;$row_det=RecuperaRegistro($rs_det);$j++){
        $row_det2 = RecuperaValor("SELECT *FROM k_maestro_pago_det WHERE fl_maestro_pago=$row_2[0] AND fg_tipo='A' AND fl_grupo=".$row_det[0]." AND ds_concepto=".$row_det[1]."");
        if(empty($row_det2[0])){

			$mn_tarifa_hr=$row_det[2];
			$mn_subtotal=$row_det[2];

			#se define la tarifa default por hora.
			$Queryt="SELECT mn_hour_rate FROM c_maestro WHERE fl_maestro=$fl_maestro ";
			$rot=RecuperaValor($Queryt);
			$mn_tarifa_default=$rot['mn_hour_rate'];
			if(!empty($mn_tarifa_default)){
				$mn_tarifa_hr=$mn_tarifa_default;
				$mn_subtotal=$mn_tarifa_default;
			}



          $Query_det  = "INSERT INTO k_maestro_pago_det(fl_maestro_pago,fg_tipo,fl_grupo,ds_concepto,mn_tarifa_hr,no_horas, mn_subtotal) ";
          $Query_det .= "VALUES($row_2[0],'A',$row_det[0],'".$row_det[1]."',$mn_tarifa_hr,'1',$mn_subtotal) ";
          EjecutaQuery($Query_det);
        }
      }
    }

    # Obtenemos el email del teacher
    $row = RecuperaValor("SELECT ds_email, ds_nombres,ds_apaterno  FROM c_usuario WHERE fl_usuario=$fl_maestro");
    $ds_email = str_texto($row[0]);
    $ds_nombres = str_texto($row[1]);
    $ds_apaterno = str_texto($row[2]);
    $variables = array(
      "te_fname" => $ds_nombres,
      "te_lname" => $ds_apaterno,
      "current_month" => $current_month // mes actual
      );

    # template
  #  $ds_template_teacher = GenerateTemplate($ds_template, $variables);
    # Load the template into html
  #  $ds_template_html->load($ds_template_teacher);
    # Get base url (domain)
   # $base_url = $ds_template_html->getElementById("login-redirect")->href;
    # Set url path and query string
  #  $ds_template_html->getElementById("login-redirect")->href = $base_url."/modules/teachers_new/index.php#ajax/home.php";

    # Enviamos el email
  #  SendNoticeMail($client, $from, $ds_email, "", $nb_template, $ds_template_html);

    # Si se envio el correo vamos fuardarlo para posterior uso en las clases globales
    EjecutaQuery("UPDATE k_maestro_pago SET fg_email='1' WHERE fl_maestro_pago=$fl_maestro_pago");


  }
 // EjecutaQuery("UPDATE k_maestro_pago SET mn_total = (SELECT SUM(mn_tarifa_hr) FROM k_maestro_pago_det WHERE fl_maestro_pago=$fl_maestro_pago) WHERE fl_maestro_pago=$fl_maestro_pago");

  # Global Class
  # Insertamos las clases globales
  $mn_cglobal_fee =  ObtenConfiguracion(96);
  $Query  = "SELECT cg.fl_clase_global, cg.ds_clase, no_alumnos, kcg.fl_maestro  FROM c_clase_global cg LEFT JOIN k_clase_cg kcg ON(kcg.fl_clase_global=cg.fl_clase_global) ";
  $Query .= "WHERE MONTH(kcg.fe_clase)='".$mes_act."' AND YEAR( kcg.fe_clase)='".$anio_act."' GROUP BY cg.fl_clase_global ";
  $rs = EjecutaQuery($Query);
  for($j=0;$row=RecuperaRegistro($rs);$j++){
    $fl_clase_global = $row[0];
    $ds_clase = str_texto($row[1]);
    $no_alumnos = $row[2];
    $fl_maestro1 = $row[3];
    # Buscamos cada una de las sesiones del teacher
    $Query0  = "SELECT kcg.no_orden, ds_titulo, ".ConsultaFechaBD('kcg.fe_clase', FMT_FECHA).", 'Global Class' ds_descripion, ";
    $Query0 .= "IFNULL((SELECT kmt.mn_cglobal_fee FROM k_maestro_tarifa_cg kmt WHERE kmt.fl_clase_global=kcg.fl_clase_global AND kmt.fl_maestro=kcg.fl_maestro), ";
    $Query0 .= "'".$mn_cglobal_fee."') mn_cglobal_fee, kcg.fl_clase_cg, kcg.fl_maestro  FROM k_clase_cg kcg WHERE kcg.fl_clase_global=$fl_clase_global ";
    $Query0 .= "AND  DATE_FORMAT(kcg.fe_clase,'%m-%Y')='".$mes_anio_act."' ";
    $rs0 = EjecutaQuery($Query0);
    for($j=0;$row0= RecuperaRegistro($rs0);$j++){
      $no_orden = $row0[0]??NULL;
      $ds_titulo = str_texto($row[1])??NULL;
      $fe_clase = $row0[2]??NULL;
      $ds_descripion = $row0[3]??NULL;
      $mn_cglobal_fee = $row0[4]??NULL;
      $fl_clase_cg = $row0[5]??NULL;
      $fl_maestro = $row0[6]??NULL;
      # Verificamos si hay un registro del mes actual del teachers
      $rowcg = RecuperaValor("SELECT fl_maestro_pago FROM k_maestro_pago WHERE fl_maestro=$fl_maestro AND DATE_FORMAT(fe_periodo, '%m-%Y')='".$mes_anio_act."'");
      if(!empty($rowcg[0])){
        # Actualiza para publicarlo
        EjecutaQuery("UPDATE k_maestro_pago SET fg_publicar='1' WHERE fl_maestro=$fl_maestro AND fl_maestro_pago=$rowcg[0]");

        # Insertamos la clase global si no esta insertada
        $row_cg = RecuperaValor("SELECT fl_maestro_pago_det FROM k_maestro_pago_det WHERE fl_maestro_pago=$rowcg[0] AND fg_tipo='ACG' AND ds_concepto=$fl_clase_cg ");
        if(empty($row_cg[0])){



			#se define la tarifa default por hora.
			$Queryt="SELECT mn_hour_rate_global_class FROM c_maestro WHERE fl_maestro=$fl_maestro ";
			$rot=RecuperaValor($Queryt);
			$mn_tarifa_default=$rot['mn_hour_rate_global_class'];
			if(!empty($mn_tarifa_default)){
				$mn_cglobal_fee=$mn_tarifa_default;
			}

          $Querycg  = "INSERT INTO k_maestro_pago_det (fl_maestro_pago,fg_tipo,fl_grupo,ds_concepto,mn_tarifa_hr,no_horas,mn_subtotal) ";
          $Querycg .= "VALUES ($rowcg[0], 'ACG', $fl_clase_global, $fl_clase_cg, $mn_cglobal_fee, 1, $mn_cglobal_fee)";
          EjecutaQuery($Querycg);
        }
        # Actualizamos el monto
        EjecutaQuery("UPDATE k_maestro_pago SET mn_total = (SELECT SUM(mn_tarifa_hr) FROM k_maestro_pago_det WHERE fl_maestro_pago=$rowcg[0]) ");



		#se define la tarifa default por hora.
		$Queryte="SELECT mn_hour_rate_global_class FROM c_maestro WHERE fl_maestro=$fl_maestro ";
		$rote=RecuperaValor($Queryte);
		$mn_tarifa_default=$rote['mn_hour_rate_global_class'];

		$Query="UPDATE k_clase_cg SET mn_rate=$mn_tarifa_default WHERE fl_clase_cg=$fl_clase_cg ";
        EjecutaQuery($Query);



      }
      else{
        $QueryIn  = "INSERT INTO k_maestro_pago (fl_maestro, fe_periodo, mn_total, fg_publicar, fg_pagado, fe_pagado) ";
        $QueryIn .= "VALUES ($fl_maestro, $curdate, 0.0, '1', '0', NULL) ";
        $fl_maestro_pago = EjecutaInsert($QueryIn);

			#se define la tarifa default por hora.
			$Queryt="SELECT mn_hour_rate_global_class FROM c_maestro WHERE fl_maestro=$fl_maestro ";
			$rot=RecuperaValor($Queryt);
			$mn_tarifa_default=$rot['mn_hour_rate_global_class'];
			if(!empty($mn_tarifa_default)){
				$mn_cglobal_fee=$mn_tarifa_default;
			}


        $Querycg  = "INSERT INTO k_maestro_pago_det (fl_maestro_pago,fg_tipo,fl_grupo,ds_concepto,mn_tarifa_hr,no_horas,mn_subtotal) ";
        $Querycg .= "VALUES ($fl_maestro_pago, 'ACG', $fl_clase_global, $fl_clase_cg, $mn_cglobal_fee, 1, $mn_cglobal_fee)";
        EjecutaQuery($Querycg);
        # Actualizamos el monto
        EjecutaQuery("UPDATE k_maestro_pago SET mn_total = (SELECT SUM(mn_tarifa_hr) FROM k_maestro_pago_det WHERE fl_maestro_pago=$fl_maestro_pago) ");
      }
    }

    # Verifcamos si ya se envio el correo de notificacion
    # En caso de que no lo enviara
    $row_cg1 = RecuperaValor("SELECT fg_email, fl_maestro_pago FROM k_maestro_pago WHERE fl_maestro=$fl_maestro1 AND DATE_FORMAT(fe_periodo, '%m-%Y')='".$mes_anio_act."'");
    $fg_email = $row_cg1[0];
    $fl_maestro_pago_cg = $row[1];
    if(empty($fg_email)){
      # Obtenemos el email del teacher
      $row = RecuperaValor("SELECT ds_email, ds_nombres,ds_apaterno  FROM c_usuario WHERE fl_usuario=$fl_maestro1");
      $ds_email = str_texto($row[0]);
      $ds_nombres = str_texto($row[1]);
      $ds_apaterno = str_texto($row[2]);
      $variables = array(
        "te_fname" => $ds_nombres,
        "te_lname" => $ds_apaterno,
        "current_month" => $current_month // mes actual
      );

      # template
   #   $ds_template_teacher = GenerateTemplate($ds_template, $variables);
      # Load the template into html
  #    $ds_template_html->load($ds_template_teacher);
      # Get base url (domain)
 #     $base_url = $ds_template_html->getElementById("login-redirect")->href;
      # Set url path and query string
  #    $ds_template_html->getElementById("login-redirect")->href = $base_url."/modules/teachers_new/index.php#ajax/home.php";

      # Enviamos el email
  #    SendNoticeMail($client, $from, $ds_email, "", $nb_template, $ds_template_html);
      # Si se envio el correo vamos fuardarlo para posterior uso en las clases globales
      EjecutaQuery("UPDATE k_maestro_pago SET fg_email='1' WHERE fl_maestro_pago=$fl_maestro_pago_cg");
    }
  }




  $QUEY1="SELECT fl_maestro_pago FROM k_maestro_pago WHERE DATE_FORMAT(fe_periodo,'%m-%Y')='".$mes_anio_act."' ";
  $rsm = EjecutaQuery($QUEY1);
  for($jm=0;$rowm=RecuperaRegistro($rsm);$jm++){

      $fl_maestro_pago=$rowm[0];

      $Query="UPDATE k_maestro_pago SET mn_total = (SELECT SUM(mn_tarifa_hr) FROM k_maestro_pago_det WHERE fl_maestro_pago=$fl_maestro_pago) WHERE fl_maestro_pago=$fl_maestro_pago ";
      # Actualizamos el monto
      EjecutaQuery($Query);





  }




  ## Obtenemos lasclases globales GRUPALES en las que se le asigno una sesion
  $Querycg1  = "SELECT a.fl_maestro  ";
  $Querycg1 .= "FROM k_clase_grupo a ";
  $Querycg1 .= "JOIN k_semana_grupo b ON b.fl_semana_grupo=a.fl_semana_grupo ";
  $Querycg1 .= "JOIN c_grupo c ON c.fl_grupo=a.fl_grupo ";
  $Querycg1 .= "WHERE ";
  $Querycg1 .= " DATE_FORMAT(a.fe_clase,'%m-%Y')='$mes_anio_act' AND a.fl_maestro=392  GROUP BY fl_maestro  ";
  $rg1 = EjecutaQuery($Querycg1);
  for($j1=0;$rog1 = RecuperaRegistro($rg1);$j1++){

      $fl_maestro=$rog1['fl_maestro'];

      $rowcg = RecuperaValor("SELECT fl_maestro_pago FROM k_maestro_pago WHERE fl_maestro=$fl_maestro AND DATE_FORMAT(fe_periodo, '%m-%Y')='".$mes_anio_act."'");
      $fl_maestro_pago=$rowcg[0];

    if (empty($fl_maestro_pago)) {

        $Querymp = "INSERT INTO k_maestro_pago (fl_maestro,fe_periodo, mn_total, fg_publicar, fg_pagado, fe_pagado) ";
        echo$Querymp .= "VALUES ($fl_maestro, $curdate, 0.0,'1', '0', Null)";
        exit;$fl_maestro_pago = EjecutaInsert($Querymp);


    }



    ## Obtenemos lasclases globales GRUPALES en las que se le asigno una sesion
    $Querycg  = "SELECT a.fl_grupo,a.fl_clase_grupo, a.nb_clase,b.no_semana,c.nb_grupo  ";
    $Querycg .= ",a.fe_clase, DATE_FORMAT(NOW() - INTERVAL 1 MONTH, '%Y-%m-01') as mes_anterior ";
    $Querycg .= "FROM k_clase_grupo a ";
    $Querycg .= "JOIN k_semana_grupo b ON b.fl_semana_grupo=a.fl_semana_grupo ";
    $Querycg .= "JOIN c_grupo c ON c.fl_grupo=a.fl_grupo ";
    $Querycg .= "WHERE a.fl_maestro=$fl_maestro ";
    $Querycg .= "AND DATE_FORMAT(a.fe_clase,'%m-%Y')='$mes_anio_act' ";
    $rg = EjecutaQuery($Querycg);
    $total_grupales = CuentaRegistros($rg);
    # Por el momento esta variable es utilizada para el monto de los teachers
    $mn_grupal_fee = ObtenConfiguracion(96);
    for($j=0;$rog = RecuperaRegistro($rg);$j++){
        $fl_grupo = $rog[0];
        $fl_clase_grupo =$rog[1];
        $nb_clase = $rog['nb_clase'];
        $no_semana = $rog['no_semana'];
        $nb_grupo=$rog['nb_grupo'];
        $fe_clase=$rog['fe_clase'];
        $fe_mes_anterior=$rog['mes_anterior'];

        #Verifica si existe
        $Queryt="SELECT mn_cgrupo FROM k_maestro_tarifa_gg WHERE fl_maestro=$fl_maestro AND fl_clase_grupo=$fl_clase_grupo  ";
        $rowt=RecuperaValor($Queryt);
        $mn_tarifa=$rowt['mn_cgrupo'];

        if(empty($mn_tarifa)){
            #se define la tarifa default por hora.
            $Queryt="SELECT mn_hour_rate_group_global FROM c_maestro WHERE fl_maestro=$fl_maestro ";
            $rot=RecuperaValor($Queryt);
            $mn_tarifa_default=$rot['mn_hour_rate_group_global'];
            if(!empty($mn_tarifa_default)){
                $mn_tarifa=$mn_tarifa_default;
            }
        }
        if(empty($mn_tarifa))
            $mn_tarifa=ObtenConfiguracion(96);

		#verificamos asistencia review
        $Query_asis="SELECT fl_live_session_grupal FROM k_live_session_grupal WHERE fl_clase_grupo=$fl_clase_grupo ";
        $row_asis=RecuperaValor($Query_asis);
        $fl_live_session_grupal=$row_asis['fl_live_session_grupal'];


        $Querygru="SELECT COUNT(*) FROM k_live_session_asistencia_gg WHERE fl_live_session_gg=$fl_live_session_grupal AND fl_usuario=$fl_maestro ";
        $rowgasis=RecuperaValor($Querygru);
        $asistencia=$rowgasis[0];

		if(!empty($asistencia)){

			# Insertamos la clase global si no esta insertada
			$row_cg = RecuperaValor("SELECT fl_maestro_pago FROM k_maestro_pago_det_review WHERE fl_maestro_pago=$fl_maestro_pago AND fl_grupo=$fl_grupo AND fl_clase_grupo=$fl_clase_grupo ");
			if(empty($row_cg[0])){

				$Querycg  = "INSERT INTO k_maestro_pago_det_review (fl_maestro_pago,fl_grupo,fl_clase_grupo,mn_tarifa_hr,fe_creacion) ";
				$Querycg .= "VALUES ($fl_maestro_pago,  $fl_grupo, $fl_clase_grupo, $mn_tarifa,CURRENT_TIMESTAMP)";
				EjecutaQuery($Querycg);
			}else{

				EjecutaQuery("UPDATE k_maestro_pago_det_review mn_tarifa_hr=$mn_tarifa  WHERE fl_maestro_pago=$fl_maestro_pago  AND fl_grupo=$fl_grupo AND fl_clase_grupo=$fl_clase_grupo ");

			}
		}

		$Queryt="SELECT mn_hour_rate_group_global FROM c_maestro WHERE fl_maestro=$fl_maestro ";
		$rot=RecuperaValor($Queryt);
		$mn_tarifa_default=$rot['mn_hour_rate_group_global'];

		$Query="UPDATE k_clase_grupo SET mn_rate=$mn_tarifa_default WHERE fl_clase_grupo=$fl_clase_grupo ";
        EjecutaQuery($Query);



    }



  }


  #Sumatoria total.
  $QuerySuma="SELECT fl_maestro_pago FROM k_maestro_pago WHERE DATE_FORMAT(fe_periodo,'%m-%Y')='".$mes_anio_act."' ";
  $rssuma=EjecutaQuery($QuerySuma);
  for($su=0;$ro = RecuperaRegistro($rssuma);$su++){

      $fl_maestro_pago=$ro[0];

      #SUMA CLASES NORMALSE GRUPALES
      $QUE="SELECT SUM(mn_tarifa_hr) FROM k_maestro_pago_det_review WHERE fl_maestro_pago=$fl_maestro_pago  ";
      $rop=RecuperaValor($QUE);
      $suma_clase_grupales_review=$rop[0];

      #SUMA CLASES NORMALES
      $QUE="SELECT SUM(mn_tarifa_hr) FROM k_maestro_pago_det WHERE fl_maestro_pago=$fl_maestro_pago  ";
      $rop=RecuperaValor($QUE);
      $suma_actual_clase_normales=$rop[0];


      $suma_total=$suma_clase_grupales_review + $suma_actual_clase_normales;


      EjecutaQuery("UPDATE k_maestro_pago SET mn_total=$suma_total WHERE fl_maestro_pago=$fl_maestro_pago   ");

  }




?>
