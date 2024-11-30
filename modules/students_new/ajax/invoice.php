<?php
  # Libreria de funciones
  require("../../common/lib/cam_general.inc.php");
  require_once('../../../AD3M2SRC4/lib/tcpdf/config/lang/eng.php');
  require_once('../../../AD3M2SRC4/lib/tcpdf/tcpdf.php');

  #variable para el TAX
  $TAX = ObtenConfiguracion(63);

  # Recibe parametros
  $fl_term_pago = RecibeParametroNumerico('f', True);
  $no_pago = RecibeParametroNumerico('pago', True);
  $fl_sesion = RecibeParametroNumerico('fl_sesion', True);
  $n_pago = RecibeParametroNumerico('n_pago', True);

  # Verifica que exista una sesion valida en el cookie y la resetea
  $destino = RecibeParametroHTML('destino', False, True);
  if($destino <> 'payments_frm.php') {
  //if(empty($destino)) {
    $fl_alumno = ValidaSesion(False);
    $row = RecuperaValor("SELECT cl_sesion, nb_grupo,ds_login FROM c_usuario a, k_alumno_grupo b, c_grupo c WHERE fl_usuario=$fl_alumno AND a.fl_usuario=b.fl_alumno AND b.fl_grupo=c.fl_grupo");
    $cl_sesion = str_texto($row[0]);
    $grupo = $row[1];
    $ds_id=$row['ds_login'];
    /*if(empty($cl_sesion) AND empty($grupo)){
      $row = RecuperaValor("SELECT cl_sesion, nb_grupo FROM c_usuario a, k_alumno_historia b, c_grupo c WHERE fl_usuario=242 AND a.fl_usuario=b.fl_alumno AND b.fl_grupo=c.fl_grupo");
      $cl_sesion = str_texto($row[0]);
      $grupo = $row[1];
    }*/
    $row = RecuperaValor("SELECT fg_inscrito FROM c_sesion WHERE cl_sesion='$cl_sesion'");
    $fg_inscrito = $row[0];
    # Verifica que el usuario tenga permiso de usar esta funcion
    if(!ValidaPermisoCampus(FUNC_ALUMNOS) ) {
      MuestraPaginaError(ERR_SIN_PERMISO);
      exit;
    }
  }
  else{
    $row = RecuperaValor("SELECT cl_sesion, fg_inscrito,id_alumno FROM c_sesion WHERE fl_sesion=$fl_sesion");
    $cl_sesion = str_texto($row[0]);
    $fg_inscrito = $row[1];

    $row = RecuperaValor("SELECT fl_usuario, nb_grupo FROM c_usuario a, k_alumno_grupo b, c_grupo c WHERE cl_sesion='$cl_sesion' AND a.fl_usuario=b.fl_alumno AND b.fl_grupo=c.fl_grupo");
    $fl_alumno = $row[0];
    if(empty($fl_alumno)){
      $rowalu= RecuperaValor("SELECT fl_usuario FROM c_usuario WHERE cl_sesion='$cl_sesion'");
      $fl_alumno = $rowalu[0];
    }
    $grupo = $row[1];
    if(empty($grupo))
      $grupo ='(no assigment)';
  }

  #Verifica si existe el id de alumno
  $Query="SELECT ds_login FROM c_usuario where fl_usuario=$fl_alumno ";
  $row=RecuperaValor($Query);
  $ds_id=$row['ds_login'];

  if(empty($ds_id)){
      #en caso de venir vacio se asigna el id e sesion.
      $Query="SELECT id_alumno FROM c_sesion where cl_sesion='$cl_sesion' ";
      $row=RecuperaValor($Query);
      $ds_id=$row['id_alumno'];
  }

  # Recupera datos del alumno: forma 1
  $Query  = "SELECT ds_fname, ds_mname, ds_lname, ".ConsultaFechaBD('fe_ultmod',FMT_FECHA).", ";
  $Query .= "b.fg_total_programa, b.fg_tax_rate, b.fl_template, b.no_grados ";
  $Query .= "FROM k_ses_app_frm_1 a, c_programa b ";
  $Query .= "WHERE a.fl_programa=b.fl_programa AND cl_sesion='$cl_sesion'";
  $row = RecuperaValor($Query);
  $ds_fname = str_texto($row[0]);
  $ds_mname = str_texto($row[1]);
  $ds_lname = str_texto($row[2]);
  $fe_ulmod = str_texto($row[3]);
  $fg_total_programa = $row[4];
  $fg_tax_rate = $row[5];
  $fl_template = $row[6];
  $no_grados = $row[7];
  # Recupera los datos pagos
  $concat = array('b.ds_nombres', "' '", 'b.ds_apaterno', "' '", NulosBD('b.ds_amaterno', ''));
  if(!empty($fg_inscrito)){
    $Query = "SELECT f.fl_programa, f.nb_programa, e.no_grado, d.nb_grupo, b.ds_login, c.fl_grupo, d.fl_term, ".ConcatenaBD($concat)." ds_nombre, ";
    $Query .= "CASE g.fg_opcion_pago WHEN 1 THEN (SELECT ds_a_freq FROM k_programa_costos WHERE fl_programa=e.fl_programa) ";
    $Query .= "WHEN 2 THEN (SELECT ds_b_freq FROM k_programa_costos WHERE fl_programa=e.fl_programa) ";
    $Query .= "WHEN 3 THEN (SELECT ds_c_freq FROM k_programa_costos WHERE fl_programa=e.fl_programa) ";
    $Query .= "WHEN 4 THEN (SELECT ds_d_freq FROM k_programa_costos WHERE fl_programa=e.fl_programa) END frecuencia, ";
    $Query .= "nb_periodo, ".ConsultaFechaBD('h.fe_inicio', FMT_FECHA).", no_semanas, g.fg_opcion_pago  ";
    $Query .= "FROM c_usuario b, k_alumno_grupo c, c_grupo d, k_term e, c_programa f, k_app_contrato g, c_periodo h, k_programa_costos i ";
    $Query .= "WHERE b.fl_usuario=c.fl_alumno ";
    $Query .= "AND c.fl_grupo=d.fl_grupo ";
    $Query .= "AND d.fl_term=e.fl_term ";
    $Query .= "AND e.fl_programa=f.fl_programa ";
    $Query .= "AND b.cl_sesion=g.cl_sesion ";
    $Query .= "AND e.fl_periodo=h.fl_periodo ";
    $Query .= "AND b.fl_usuario=$fl_alumno ";
    $Query .= "AND g.no_contrato=1  AND f.fl_programa = i.fl_programa  ";
  }
  else{
    $Query  = "SELECT d.fl_programa, d.nb_programa,'' no_grado,'' nb_grupo, '' ds_login,'' fl_grupo, e.fl_term, CONCAT(a.ds_fname, ' ', a.ds_lname, ' ', IFNULL(a.ds_mname, '')) ds_nombre, ";
    $Query .= "CASE c.fg_opcion_pago WHEN 1 THEN (SELECT ds_a_freq FROM k_programa_costos WHERE fl_programa=e.fl_programa) ";
    $Query .= "WHEN 2 THEN (SELECT ds_b_freq FROM k_programa_costos WHERE fl_programa=e.fl_programa) ";
    $Query .= "WHEN 3 THEN (SELECT ds_c_freq FROM k_programa_costos WHERE fl_programa=e.fl_programa) ";
    $Query .= "WHEN 4 THEN (SELECT ds_d_freq FROM k_programa_costos WHERE fl_programa=e.fl_programa) END frecuencia, f.nb_periodo, ";
    $Query .= "".ConsultaFechaBD('f.fe_inicio', FMT_FECHA).", no_semanas, c.fg_opcion_pago  ";
    $Query .= "FROM k_ses_app_frm_1 a, c_sesion b,k_app_contrato c, c_programa d, k_term e, c_periodo f, k_programa_costos g ";
    $Query .= "WHERE a.cl_sesion=b.cl_sesion AND e.fl_programa=d.fl_programa AND b.cl_sesion=c.cl_sesion AND a.fl_programa = d.fl_programa ";
    $Query .= "AND a.fl_periodo=f.fl_periodo AND e.fl_periodo=f.fl_periodo AND b.cl_sesion='$cl_sesion' AND c.no_contrato=1  AND e.no_grado=1 AND d.fl_programa = g.fl_programa";
  }

  $row = RecuperaValor($Query);
  $fl_programa = $row[0];
  $nb_programa = str_texto($row[1]);
  $no_grado = $row[2];
  $nb_grupo = str_texto($row[3]);
  $ds_login = str_texto($row[4]);
  $fl_grupo = $row[5];
  $fl_term = $row[6];
  $ds_nombre = str_texto($row[7]);
  $frecuencia = str_texto($row[8]);
  $nb_periodo = str_texto($row[9]);
  $fe_inicio_pro = $row[10];
  $Date=$fe_inicio_pro;
  $no_semanas = $row[11];
    $meses_duracion = $no_semanas/4; //meses de duracion del programa
  $fg_opcion_pago = $row[12]; // opcion de pago
  #obtenemos la fecha del term_ini si el grado es mayor a 1
  $row1 = RecuperaValor("SELECT a.fl_term, no_grado, fe_pago FROM k_term_pago a, k_term b WHERE a.fl_term=b.fl_term AND fl_term_pago=$fl_term_pago");
  $fl_term_ini = $row1[0];
  $fe_term_pago = $row1[2];

  ##Caso particular Ariel Rabesca term 2 repirio term 2.
  if($fl_alumno==2829){
      $fl_term_ini=$fl_term;


  }
  $rowe = RecuperaValor("SELECT fl_pais_campus FROM c_sesion WHERE cl_sesion='".$cl_sesion."'");
  $fl_pais_campus = $rowe[0];


  if($no_grado <> 1 AND !empty($fg_inscrito)){
    //$row = RecuperaValor("SELECT fl_term_ini FROM k_term a WHERE fl_term=$fl_term AND fl_programa=$fl_programa AND no_grado=$no_grado ");
    $row2 = RecuperaValor("SELECT fe_inicio FROM k_term a, c_periodo b WHERE fl_term=$fl_term_ini AND a.fl_periodo = b.fl_periodo");
    $fe_inicio_pro = $row2[0];
    $Date = $row2[0];
  }
  else
    $fe_term_pago = $row1[2];

  # Metodo de pago
  if(!empty($fg_inscrito)){
    $Query  = "SELECT fl_alumno_pago,fl_term_pago, CASE cl_metodo_pago WHEN 1 THEN '".ObtenEtiqueta(488)."' WHEN 2 THEN '".ObtenEtiqueta(488)." Manual' WHEN 3 THEN 'Cheque' ";
    $Query .= "WHEN 4 THEN 'Credit Card' WHEN 5 THEN 'Wire Transfer/Deposit' WHEN 6 THEN 'Cash' END cl_metodo_pago, ds_comentario, ds_cheque, ";
    $Query .= "(CONCAT(DATE_FORMAT(fe_pago, '%d-%m-%Y'), ' ', DATE_FORMAT(fe_pago, '%H:%i:%s'))) fe_pago, mn_late_fee, ds_transaccion ds_transaccion_pago, cl_metodo_pago, fg_refund, mn_refund ";
    $Query .= ", DATE_FORMAT(fe_refund,'%M %d, %Y'), mn_tax_paypal, ds_tax_provincia,mn_convenience_fee FROM k_alumno_pago WHERE fl_term_pago=$fl_term_pago AND fl_alumno=$fl_alumno";
  }
  else{
    $Query  = "SELECT fl_ses_pago,fl_ses_pago, CASE cl_metodo_pago WHEN 1 THEN '".ObtenEtiqueta(488)."' WHEN 2 THEN '".ObtenEtiqueta(488)." Manual' WHEN 3 THEN 'Cheque' ";
    $Query .= "WHEN 4 THEN 'Credit Card' WHEN 5 THEN 'Wire Transfer/Deposit' WHEN 6 THEN 'Cash' END cl_metodo_pago, ds_comentario, ds_cheque, ";
    $Query .= "(CONCAT(DATE_FORMAT(fe_pago, '%d-%m-%Y'), ' ', DATE_FORMAT(fe_pago, '%H:%i:%s'))) fe_pago, mn_late_fee, '' ds_transaccion, '' cl_metodo_pago, fg_refund, mn_refund ";
    $Query .= ", DATE_FORMAT(fe_refund,'%M %d, %Y'), mn_tax_paypal, ds_tax_provincia,mn_convenience_fee FROM k_ses_pago WHERE fl_term_pago=$fl_term_pago AND cl_sesion='$cl_sesion'";
  }
  $row = RecuperaValor($Query);
  $fl_alumno_pago = $row[0];
  $Metodo = $cl_metodo_pago = $row[2];
  $ds_comentario = $row[3];
  $ds_cheque = $row[4];
  $data1= str_texto($row[5]);
  $mn_late_fee = $row[6];
  $cl_metodo_pago = $row[8];
  $ds_transaccion_pago = str_texto($row[7]);
  if($cl_metodo_pago == 1 AND !empty($ds_transaccion_pago))
    $ds_transaccion_pago = '<b>'.ObtenEtiqueta(702).'</b>'.$ds_transaccion_pago;
  else
    $ds_transaccion_pago ='';
  $fg_refund = $row[9];
  $mn_refund = $row[10];
  $fe_refund = $row[11];
  $mn_tax_paypal_pago = $row[12];
  $ds_tax_provincia = str_texto($row[13]);
  $convenience_fee_tuition=!empty($row['mn_convenience_fee'])?$row['mn_convenience_fee']:0;

  if($fl_pais_campus<>226){
      $Metodo = $cl_metodo_pago = $row[2];
  }else{
      $Metodo = $cl_metodo_pago = "Stripe";
  }

  # Se obtiene la descripcion de la frecuencia del pago
  if(!empty($fl_term_pago) AND !empty($no_pago)){
    switch($fg_opcion_pago) {
      case 1:
        $mn_due = 'mn_a_due';
        $mn_paid = 'mn_a_paid';
        $no_invoices = 1;
        break;
      case 2:
        $mn_due = 'mn_b_due';
        $mn_paid = 'mn_b_paid';
        $no_invoices = 2;
        break;
      case 3:
        $mn_due = 'mn_c_due';
        $mn_paid = 'mn_c_paid';
        $no_invoices = 4;
        break;
      case 4:
        $mn_due = 'mn_d_due';
        $mn_paid = 'mn_d_paid';
        break;
    }

    #obtenemos los precios de cada uno de los contratos de los alumnos
    $Query  = "SELECT mn_app_fee, mn_tuition, mn_costs, ds_costs, mn_discount, ds_discount, mn_tot_tuition, mn_tot_program,$mn_due, $mn_paid ";
    $Query .= "FROM k_app_contrato WHERE cl_sesion='$cl_sesion' AND no_contrato=1";
    $row = RecuperaValor($Query);
    $mn_app_fee = $row[0];
    $mn_tuition = $row[1];
    $mn_costs = $row[2];
    $ds_costs = $row[3];
    $mn_discount = $row[4];
    $ds_discount = $row[5];
    $mn_tot_tuition = $row[6];
    $mn_tot_program = $row[7];
    $mn_x_due = $row[8];
    $mn_x_paid = $row[9];

    # Verificamos si repitio el grado
    $Query3 = "SELECT COUNT(no_grado), no_grado FROM k_alumno_term a, k_term b WHERE a.fl_term=b.fl_term and fl_alumno=$fl_alumno group by no_grado ";
    $rs3 = EjecutaQuery($Query3);
    for($i=0; $row3=RecuperaRegistro($rs3); $i++){
      $no_grado_re = $row3[0];
      if($no_grado_re > 1){
        #menos uno por el pago regular
        $repetido = $row3[0]-1;
      }
    }

    #numero de pagos, meses que cubre un pago
    $numero_pagos =  $mn_x_paid/$mn_x_due;
    $no_meses_op = $meses_duracion/$numero_pagos; //numero de meses por opcion
    $desfase = ($no_pago-1)*$no_meses_op;
    $nuevafecha = strtotime ( "+ ".$desfase." month", strtotime($fe_inicio_pro));
    $fe_mesini_pago = date ( 'd-m-Y' , $nuevafecha );

    # Obtenemos el term inicial actual
    $Query  = "SELECT fl_term_ini FROM k_term WHERE fl_programa=$fl_programa AND fl_term=$fl_term";
    $row = RecuperaValor($Query);
    $term_ini_act = $row[0];
    if(empty($term_ini_act))
      $term_ini_act=$fl_term_ini;
    # Obtenemos el term inicial cuando se incribio
    $row = RecuperaValor("SELECT MIN(fl_term) FROM k_alumno_term WHERE fl_alumno=$fl_alumno ");
    $term_ini = $row[0];
    if($term_ini != $term_ini_act){
      if($repetido>0){
        $numero_pagos_t = (($numero_pagos/$no_grados)*$repetido)+$numero_pagos;
      }
      else
        $numero_pagos_t = $numero_pagos;
    }
    else
      $numero_pagos_t = $numero_pagos;

  #calculos para el descuento
  $porc_interes = $mn_x_paid/$mn_tot_tuition;
  $pago_s_interes = $mn_tuition/$numero_pagos;
  $pago_c_interes = round($pago_s_interes * $porc_interes);



  if ($fg_opcion_pago > 1) {
        $pago_normal_x_mes = round($mn_x_due / $no_meses_op, 2);
  } else {
    $pago_normal_x_mes = round(($mn_tot_tuition - ((!empty($mn_costs) ? $mn_costs : 0))) / $no_meses_op, 2); //2023 jul 7  remove aditional cost, present other invoice
  }



    if($mn_discount>0)
    $descuento = $mn_x_due-round($pago_c_interes);
  else
    $descuento=0;

  #calculos de pago adicional
  if($mn_costs>0)
    $adicional =  $mn_x_due-round($pago_c_interes);
  else
    $adicional=0;
  }


if ($fg_opcion_pago > 1) {
    $mn_costs_invoice = 0;
    if ($mn_costs > 0) {

        $mn_costs_invoice = $mn_costs / $no_invoices;
    }
    $mn_cost_x_mes = 0;
    if ($mn_costs > 0) {

        $mn_cost_x_mes = $mn_costs_invoice / $no_meses_op;
    }

}




#damos a conocer si el estudiantes es de canada
  $Query  ="SELECT ds_add_number, ds_add_street, ds_add_city, ds_add_state, ds_add_zip, nb_pais, ds_add_country ";
  $Query .="FROM k_ses_app_frm_1 a, c_pais b WHERE cl_sesion='$cl_sesion' AND a.ds_add_country=b.fl_pais ";
  $row = RecuperaValor($Query);
  $ds_add_number = $row[0];
  $ds_add_street = $row[1];
  $ds_add_city = $row[2];
  $ds_add_state = $row[3];
  $ds_add_zip = $row[4];
  $ds_add_country = $row[5];
  $cl_pais = $row[6];
  if($cl_pais==38){
    $rowstate = RecuperaValor("SELECT ds_provincia FROM k_provincias WHERE fl_provincia=$ds_add_state");
    $ds_add_state = $rowstate[0];
    if(empty($ds_add_state))
      $ds_add_state = $row[3];
  }


  //obtener valores de c_sesion mediate fl_sesion
  //para obtener informacion del pago de app_fee
  $Query = "SELECT fl_sesion,  CASE cl_metodo_pago WHEN 1 THEN ' ' WHEN 2 THEN 'Payment Manual' WHEN 3 THEN 'Cheque' WHEN 4 THEN 'Credit Card' ";
  $Query .= "WHEN 5 THEN 'Wire Transfer/Deposit' WHEN 6 THEN 'Cash' END cl_metodo_pago, (CONCAT(DATE_FORMAT(fe_pago, '%d-%m-%Y'), ' ', DATE_FORMAT(fe_pago, '%H:%i:%s'))) fe_pago, ";
  $Query .="mn_pagado,ds_comentario, ds_transaccion, cl_metodo_pago, mn_tax_paypal, ds_tax_provincia,convenience_fee FROM c_sesion  WHERE fl_sesion='$fl_sesion' ";
  $row=RecuperaValor($Query);
  $cl_metodo_app = $row[1];
  $fe_pago_app = str_texto($row[2]);
  $mn_pagado_app = $row[3];
  $ds_comentario_app = str_texto($row[4]);
  $ds_transaccion = str_texto($row[5]);
  $cl_metodo_pago_app = $row[6];
  $convenience_fee_app_fee=!empty($row['convenience_fee'])?$row['convenience_fee']:0;
  if($cl_metodo_pago_app == 1 AND !empty($ds_transaccion))
    $ds_transaccion = '<b>'.ObtenEtiqueta(702).'</b>'.$ds_transaccion;
  else
    $ds_transaccion ='';
  $mn_tax_paypal_appfee = $row[7];
  $ds_tax_provinciaapp = str_texto($row[8]);

  #Recuperamos segun registro que tiene el estudiante.
  if(/*($mn_tax_paypal_appfee<=0)&&*/(($cl_pais==38)||$cl_pais==226)){
      $Query="SELECT ds_add_state ,b.mn_tax,b.ds_type ";
      $Query.="FROM k_ses_app_frm_1 a ";
      $Query.="JOIN k_provincias b ON a.ds_add_state=b.fl_provincia ";
      $Query.="WHERE cl_sesion='$cl_sesion'  ";
      $ro=RecuperaValor($Query);
      $mn_tax_=$ro[1]/100;
      $mn_tax_pdf=number_format($ro[1],0);
      $mn_tax_paypal_appfee=$mn_pagado_app*$mn_tax_;



      $Query="SELECT ds_tax_provincia FROM c_sesion WHERE cl_sesion='$cl_sesion' ";
      $tf=RecuperaValor($Query);
      $ds_tax_provinciaapp=$tf[0];
  }
  /*$convenience_fee=0;
  $Query="SELECT convenience_fee FROM c_sesion WHERE cl_sesion='$cl_sesion' ";
  $ro=RecuperaValor($Query);
  $mn_convenience_fee=$ro['convenience_fee'];
  if(!empty($mn_convenience_fee)){

      $convenience_fee_percentage=ObtenConfiguracion(165);
      #calculate convenience fee app_fee.
      $convenience_fee=(($mn_pagado_app + $mn_tax_paypal_appfee)*$convenience_fee_percentage)/100;
  }
  */
  //validamos que informacion se podra en el encabezado del archivo
  if(empty($no_pago)){
    $Date = $fe_pago_app;
    $cl_metodo_pago = $cl_metodo_app;
    $Payment_Number = 'Once';
  }
  else{
    $cl_metodo_pago = $Metodo;
    $Payment_Number = $n_pago.' of '.$numero_pagos_t;
  }

  if($fl_pais_campus==226){
      $Metodo = $cl_metodo_pago = "Stripe";

  }

  # Si es un App_fee utiliza transaccion y day1
  if(empty($no_pago)){
    $Date=$Date;
    $fl_alumno_pago = 0;
  }
  else{
    $Date=$data1;
    $fl_alumno_pago = $n_pago;
  }




  // create new PDF document
  $pdf = new TCPDF('P', 'mm', 'LETTER', true);

  //do not show header or footer
  $pdf->SetPrintHeader(false);
  $pdf->SetPrintFooter(false);

  // set default monospaced font
  $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

  //set margins
  $pdf->SetMargins(1.5, PDF_MARGIN_TOP, 1.5);
  $pdf->SetHeaderMargin(5);
  $pdf->SetFooterMargin(50);

  //set auto page breaks
  $pdf->SetAutoPageBreak(TRUE, 5);

  //set image scale factor
  $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

  //set some language-dependent strings
  $pdf->setLanguageArray($l);

  // ---------------------------------------------------------

  // set font
  $pdf->SetFont('dejavusans', '', 10);

  #encabezado de los recibos
  $htmlcontent = '
    <table style="width:350px;" border="0">
      <tr>
        <td width="100%">
          <img src="../../../images/Vanas_doc_logo.jpg" border="0" />
        </td>
      </tr>
    </table>
    <br />
    <table style="width:540px;">
      <tr>
        <td width="52%" rowspan="3">
          <div style="font-size:12px;">'.ObtenEtiqueta(516).'<br />
            '.ObtenEtiqueta(517).','.ObtenEtiqueta(518).' <br />
           '.ObtenEtiqueta(519).' <br />
            '.MAIL_FROM.'
          </div>
        </td>
        <td width="18%">&nbsp;</td>
        <td colspan="2" width="30%" style="font-size:16px; font-weight:bold; text-align: center">Sales Receipt</td>
      </tr>
      <tr style="text-align:center;">
        <td width="18%">&nbsp;</td>
        <td width="15%" style="border-left:1px #cccccc solid; border-right:1px #cccccc solid; border-bottom:1px #cccccc solid; border-top:1px #cccccc solid;  font-size:12px;">Date</td>
        <td width="15%" style="border-left:1px #cccccc solid; border-right:1px #cccccc solid; border-bottom:1px #cccccc solid; border-top:1px #cccccc solid;  font-size:12px;">Sale No.</td>
      </tr>
      <tr style="text-align:center;">
        <td width="18%">&nbsp;</td>
        <td width="15%" style="border-left:1px #cccccc solid; border-right:1px #cccccc solid; border-bottom:1px #cccccc solid; border-top:1px #cccccc solid; font-size:12px; ">'.$Date.'</td>
        <td width="15%" style="border-left:1px #cccccc solid; border-right:1px #cccccc solid; border-bottom:1px #cccccc solid; border-top:1px #cccccc solid;  font-size:12px;">'.$fl_alumno_pago.'</td>
      </tr>
    </table>
    <br /><br />
    <table border="0" style="width:540px;">
      <tr >
        <td width="52%"; style="font-size:12px;   border-left:1px #cccccc solid; border-right:1px #cccccc solid; border-bottom:1px #cccccc solid; border-top:1px #cccccc solid; " ><br />Sold to</td>
        <td width="3%"></td>
        <td width="15%" style="text-align:center; font-size:12px;  border-left:1px #cccccc solid; border-right:1px #cccccc solid; border-bottom:1px #cccccc solid; border-top:1px #cccccc solid;  ">Cheque No.</td>
        <td width="15%" style="text-align:center; font-size:12px;   border-left:1px #cccccc solid; border-right:1px #cccccc solid; border-bottom:1px #cccccc solid; border-top:1px #cccccc solid;  ">Payment Method</td>
        <td width="15%" style="text-align:center; font-size:12px;   border-left:1px #cccccc solid; border-right:1px #cccccc solid; border-bottom:1px #cccccc solid; border-top:1px #cccccc solid;  ">Payment Number</td>
      </tr>
      <tr>
        <td rowspan="3" style="font-size:12px;  border-left:1px #cccccc solid; border-right:1px #cccccc solid; border-bottom:1px #cccccc solid; border-top:1px #cccccc solid;">'.$ds_fname.' '.$ds_lname.' '.$ds_mname.'<br>'.$nb_programa.'<br />' .$ds_add_number.'  '.$ds_add_street.'<br />'.$ds_add_city.', '.$ds_add_state.'<br />'.$ds_add_country.', '.$ds_add_zip.'<br /> Group: '.$grupo.'<br />Student ID:<br>'.$ds_id.'</td>
        <td></td>
        <td style="text-align:center; font-size:12px;  border-left:1px #cccccc solid; border-right:1px #cccccc solid; border-bottom:1px #cccccc solid; border-top:1px #cccccc solid;">'.$ds_cheque.'</td>
        <td style="text-align:center; font-size:12px;  border-left:1px #cccccc solid; border-right:1px #cccccc solid; border-bottom:1px #cccccc solid; border-top:1px #cccccc solid;">'.$cl_metodo_pago.'</td>
        <td style="text-align:center; font-size:12px;  border-left:1px #cccccc solid; border-right:1px #cccccc solid; border-bottom:1px #cccccc solid; border-top:1px #cccccc solid;"> '.$Payment_Number.'</td>
      </tr>
    </table><br /><br /><br /><br><br><br>';
  $htmlcontent .='
    <br /> <br /> <br /><br />
    <table border="1" style="width:540px;">
      <tr style="font-weight:bold; background-color:#FFFFFF; font-size:12px;" >
        <td width="70%"; style="text-align:center; border:1px #cccccc solid;">'.ObtenEtiqueta(19).'</td>
        <td width="10%"; style="text-align:center; border:1px #cccccc solid; ">Qty</td>
        <td width="10%"; style="text-align:center; border:1px #cccccc solid; ">Fee</td>
        <td width="10%"; style="text-align:center; border:1px #cccccc solid;">'.ObtenEtiqueta(583).'</td>
      </tr>';

    if(!empty($fl_term_pago) AND !empty($no_pago)){
      $suma=0;
      $acumulado_pagos_normal_x_mes=0;
      for($i=0;$i<=$no_meses_op-1;$i++){
        if($fl_alumno == 190)
          $mes_ini_pago = strtotime ( "+ ".$i ." month", strtotime($fe_term_pago));
        else
          $mes_ini_pago = strtotime ( "+ ".$i ." month", strtotime($fe_mesini_pago)); //Esta la comentamos ahora tomamos la fecha de pago del term que se pago
        $dia = date('d',$mes_ini_pago);
        $mes= date('m', $mes_ini_pago);
        $anio = date('Y', $mes_ini_pago);

        $cont++;

        switch ($mes) {
          case 1: $mes_pago = "January"; break;
          case 2: $mes_pago ="February"; break;
          case 3: $mes_pago = "March"; break;
          case 4: $mes_pago = "April"; break;
          case 5: $mes_pago = "May"; break;
          case 6: $mes_pago = "June"; break;
          case 7: $mes_pago = "July"; break;
          case 8: $mes_pago = "August"; break;
          case 9: $mes_pago = "September"; break;
          case 10: $mes_pago = "October"; break;
          case 11: $mes_pago = "November"; break;
          case 12: $mes_pago = "December"; break;
        }

        if($i==$no_meses_op)
          $pago_normal_x_mes = round($pago_c_interes-$suma,2);
        else
          $suma+=$pago_normal_x_mes;


        if ($mn_costs > 0) {

            $htmlcontent .= '
          <tr style="font-size:12px;">
            <td height="15"; style="border-left:1px #cccccc solid; border-right:1px #cccccc solid;">' . ObtenEtiqueta(599) . '<br />' . ObtenEtiqueta(60) . ':&nbsp;&nbsp;' . $mes_pago . ', ' . $anio . '</td>
            <td style="text-align:center; border-left:1px #cccccc solid; border-right:1px #cccccc solid">1</td>
            <td style="text-align:right; border-left:1px #cccccc solid; border-right:1px #cccccc solid">' . number_format(round($pago_normal_x_mes - $mn_cost_x_mes,2), 2, '.', ',') . '</td>
            <td style="text-align:right; border-left:1px #cccccc solid; border-right:1px #cccccc solid">' . number_format(round($pago_normal_x_mes - $mn_cost_x_mes,2), 2, '.', ',') . '</td>
          </tr>';


        }else{

            $htmlcontent .= '
          <tr style="font-size:12px;">
            <td height="15"; style="border-left:1px #cccccc solid; border-right:1px #cccccc solid;">' . ObtenEtiqueta(599) . '<br />' . ObtenEtiqueta(60) . ':&nbsp;&nbsp;' . $mes_pago . ', ' . $anio . '</td>
            <td style="text-align:center; border-left:1px #cccccc solid; border-right:1px #cccccc solid">1</td>
            <td style="text-align:right; border-left:1px #cccccc solid; border-right:1px #cccccc solid">' . number_format($pago_normal_x_mes, 2, '.', ',') . '</td>
            <td style="text-align:right; border-left:1px #cccccc solid; border-right:1px #cccccc solid">' . number_format($pago_normal_x_mes, 2, '.', ',') . '</td>
          </tr>';

        }

        if (($mn_costs > 0)) {
            $acumulado_pagos_normal_x_mes += ($pago_normal_x_mes - $mn_cost_x_mes);
        } else {

            $acumulado_pagos_normal_x_mes += $pago_normal_x_mes;
        }

      }

      # Si se atraso al pago aumenta su mn_pago total
      if($mn_late_fee > 0){
        $htmlcontent .='
          <tr style="font-size:12px; padding:10px;">
            <td height="15"; style="border-left:1px #cccccc solid; border-right:1px #cccccc solid;">'.ObtenEtiqueta(703).'</td>
            <td style="text-align:center; border-left:1px #cccccc solid; border-right:1px #cccccc solid">1</td>
            <td style="text-align:right; border-left:1px #cccccc solid; border-right:1px #cccccc solid">'.$mn_late_fee.'</td>
            <td style="text-align:right; border-left:1px #cccccc solid; border-right:1px #cccccc solid">'.$mn_late_fee.'</td>
          </tr>';
      }

      #si el descuento es mayor a cero muestra
     /* if($mn_discount>0 ){
        $htmlcontent .='
        <tr style="font-size:12px;">
          <td height="15"; style="border-left:1px #cccccc solid; border-right:1px #cccccc solid;">'.$ds_discount.'</td>
          <td style="text-align:center; border-left:1px #cccccc solid; border-right:1px #cccccc solid; #cccccc solid;">1</td>
          <td style="text-align:right; border-left:1px #cccccc solid; border-right:1px #cccccc solid; #cccccc solid;">'.number_format($descuento, 2, '.',',').'</td>
          <td style="text-align:right; border-left:1px #cccccc solid; border-right:1px #cccccc solid;  #cccccc solid;">'.number_format($descuento, 2, '.',',').'</td>
        </tr>';
      }
      */
      #si el pago adicional es mayor a cero muestra
     /* if($mn_costs>0 ){

        if ($ds_costs == "Fame Learning Resources" || $ds_costs == "FAME Learning Resources") {
            $ds_costs = "Learning Resources";
        }

        $htmlcontent .='
        <tr style="font-size:12px;">
          <td height="15"; style="border-left:1px #cccccc solid; border-right:1px #cccccc solid; border-bottom:1px #cccccc solid;">'.$ds_costs.'</td>
          <td style="text-align:center; border-left:1px #cccccc solid; border-right:1px #cccccc solid;  #cccccc solid;">1</td>
          <td style="text-align:right; border-left:1px #cccccc solid; border-right:1px #cccccc solid;  #cccccc solid;">'.number_format($mn_costs_invoice, 2, '.',',').'</td>
          <td style="text-align:right; border-left:1px #cccccc solid; border-right:1px #cccccc solid;  #cccccc solid;">'.number_format($mn_costs_invoice, 2, '.',',').'</td>
        </tr>';
      }
      */
      # Refund
      if(!empty($fg_refund)){
        $htmlcontent .='
        <tr style="font-size:12px;">
          <td height="15"; style="border-left:1px #cccccc solid; border-right:1px #cccccc solid; border-bottom:1px #cccccc solid;">Refund Date: '.$fe_refund.'</td>
          <td style="text-align:center; border-left:1px #cccccc solid; border-right:1px #cccccc solid; border-bottom:1px #cccccc solid;">1</td>
          <td style="text-align:right; border-left:1px #cccccc solid; border-right:1px #cccccc solid; border-bottom:1px #cccccc solid;">'.number_format($mn_refund, 2, '.',',').'</td>
          <td style="text-align:right; border-left:1px #cccccc solid; border-right:1px #cccccc solid; border-bottom:1px #cccccc solid;">'.number_format($mn_refund, 2, '.',',').'</td>
        </tr>';
      }


      #El total se agrega

      //$subtotal = $pago_c_interes+$descuento+$adicional+$mn_late_fee-$mn_refund;
      $subtotal=$acumulado_pagos_normal_x_mes+$mn_late_fee-$mn_refund;
	  if(!empty($fg_tax_rate)){
	  $total=$subtotal+$mn_tax_paypal_pago;
	  }else{
	  $total=$subtotal;

	  }

      if(!empty($fg_tax_rate)){

		  if(($mn_tax_paypal_pago<=0)&&(($cl_pais==38)||($cl_pais==226))){

			  $Query="SELECT ds_add_state ,b.mn_tax,b.ds_type ";
			  $Query.="FROM k_ses_app_frm_1 a ";
			  $Query.="JOIN k_provincias b ON a.ds_add_state=b.fl_provincia ";
			  $Query.="WHERE cl_sesion='$cl_sesion'  ";
			  $ro=RecuperaValor($Query);
			  $mn_tax_=$ro[1]/100;
			  $mn_tax_paypal_pago=$subtotal*$mn_tax_;
			  $total=$subtotal+$mn_tax_paypal_pago;

		  }

	  }


      $htmlcontent .='
        <tr style="font-size:12px;">
          <td height="15"; COLSPAN="2" style="border-left:1px #cccccc solid;  border-top:1px #cccccc solid; border-right:1px #cccccc solid;">Comments</td>
          <td style="border-left:1px #cccccc solid;  border-top:1px #cccccc solid; border-right:1px #cccccc solid;">Subtotal: </td>
          <td align="right" style="border-left:1px #cccccc solid; border-top:1px #cccccc solid; border-right:1px #cccccc solid;">'.number_format(round($subtotal),2,'.',',').'</td>
        </tr>';
      # Si se le cobro tax en paypal y el programa fue corto ademas el programa requiero tax se mostrara este
      if($cl_pais==38 AND !empty($fg_tax_rate)){









        $htmlcontent .='
        <tr style="font-size:12px;">
          <td height="15"; COLSPAN="2"; style="border-left:1px #cccccc solid; border-right:1px #cccccc solid; ">&nbsp;</td>
          <td style="border-left:1px #cccccc solid; border-right:1px #cccccc solid; border-bottom:1px #cccccc solid; ">'.$ds_tax_provincia.'  '.number_format($mn_tax_pdf).'%: ';

        $htmlcontent .='';
        $htmlcontent.='
        </td>
          <td align="right" style="border-left:1px #cccccc solid; border-right:1px #cccccc solid; border-bottom:1px #cccccc solid; " >'.number_format($mn_tax_paypal_pago,2,'.',',').' ';

        $htmlcontent .='';

        $htmlcontent.='
        </td>
        </tr>';
      }
      if($convenience_fee_tuition){

          $htmlcontent .='
        <tr style="font-size:12px;">
          <td height="15"; COLSPAN="2" style="border-left:0px #cccccc solid;  border-top:0px #cccccc solid; border-right:0px #cccccc solid;"></td>
          <td style="border-left:1px #cccccc solid;  border-top:0px #cccccc solid; border-right:0px #cccccc solid;">Convenience fee: </td>
          <td align="right" style="border-left:1px #cccccc solid; border-top:0px #cccccc solid; border-right:1px #cccccc solid;">'.number_format($convenience_fee_tuition,2,'.',',').'</td>
        </tr>';

      }

      $htmlcontent .='
        <tr style="border:1px ; font-size:12px;">
          <td height="15"; COLSPAN="2"; style="border-left:1px #cccccc solid; border-right:1px #cccccc solid; border-bottom:1px #cccccc solid;  ">'.$ds_comentario.'<br />'.$ds_transaccion_pago.'</td>
          <td style="border-left:1px #cccccc solid; border-right:1px #cccccc solid; border-bottom:1px #cccccc solid; ">Total: </td>
          <td align="right" style="border-left:1px #cccccc solid; border-right:1px #cccccc solid; border-bottom:1px #cccccc solid; " >'.number_format(round($total+$convenience_fee_tuition),2,'.',',').'</td>
        </tr>';
    }
    else{

      $htmlcontent .='
        <tr style="border-left:1px #cccccc border-right:1px #cccccc solid; font-size:12px;" >
          <td width="50%"; style="border-left:1px #cccccc solid; border-right:1px #cccccc solid;"><br />'.ObtenEtiqueta(584).'<br /></td>
          <td width="10%"; style="text-align:center; border:1px #cccccc solid; ">1</td>
          <td width="20%"; style="text-align:right; border:1px #cccccc solid; ">'.number_format($mn_pagado_app,2,'.',',').'</td>
          <td width="20%"; style="text-align:right; border:1px #cccccc solid;">'.number_format($mn_pagado_app,2,'.',',').'</td>
        </tr>';

      if($cl_pais==38 AND (!empty($fg_tax_rate) || empty($fg_tax_rate))){
        $tax = $TAX/100;
        $subtotal = $mn_pagado_app;
        $taxx = $subtotal*$tax;
        $total = $subtotal+$taxx+$mn_tax_paypal_appfee;
        $htmlcontent .='
        <tr style="font-size:12px;">
          <td Colspan="2"; align="left">Comments <br />'.$ds_comentario_app.'<br />'.$ds_transaccion.'</td>
          <td>Subtotal: <br /> '.$ds_tax_provinciaapp.' ('.$mn_tax_pdf.'%): ';
        if($convenience_fee_app_fee){
            $htmlcontent .='<br>Convenience fee: ';
        }


        $htmlcontent.='
        <br /> Total: </td>
          <td align="right">'.number_format(round(round($subtotal)),2,'.',',').'<br />'.number_format($mn_tax_paypal_appfee,2,'.',',').'  ';

        if($convenience_fee_app_fee){
            $htmlcontent .=' <br>'.number_format($convenience_fee_app_fee,2,'.',',').' ';
        }


        $htmlcontent.=' <br />'.number_format($total+$convenience_fee_app_fee,2,'.',',').'</td>
        </tr>';
      }
      else{
        $htmlcontent .='
        <tr style="text-align:right; font-size:12px;">
          <td Colspan="2" align="left">Comments <br />'.$ds_comentario_app.'<br />'.$ds_transaccion.'</td>
          <td align="left">Total: </td>
          <td align="right">'.number_format($mn_pagado_app,2,'.',',').'</td>
        </tr>';
      }
    }
  $htmlcontent .='
    </table>';

  class ConPies extends TCPDF {
    //Pie de pagina
    function Footer()
    {
      $bussuness= ObtenConfiguracion(64);
      $this->SetY(-10);
      $this->Cell(0,10,$bussuness,0,0,'L');
    }
    function Header()
    {
    }
  }
  // creamos un nuevo objeto usando la clase extendida ConPies
  $pdf = new ConPies();

  $pdf->SetFont('Times','',8);
  // add a page
  $pdf->AddPage("P");

  //Proteje el archivo contra copia
  $pdf->SetProtection($permissions = array('copy'));

  // output the HTML content
  $pdf->writeHTML($htmlcontent, true, 0, true, 0);

  $nombre_archivo = 'Invoice '.$ds_fname.' '.$ds_lname.' '.$Date.'.pdf';
  //Close and output PDF document
  $pdf->Output($nombre_archivo, 'D');
?>