<?php

  # Libreria de funciones
  require("../../common/lib/cam_general.inc.php");
  require("../../../lib/sp_forms.inc.php");

  # Verifica que exista una sesion valida en el cookie y la resetea
  $fl_alumno = ValidaSesion(False);
  $cl_sesion = $_COOKIE[SESION_CAMPUS];
  $msg_error=$_GET['msg'];

  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermisoCampus(FUNC_ALUMNOS)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }

  function GetPaymentHeaders(){
    $th = array(
      'th_1' => ObtenEtiqueta(481),
      'th_2' => ObtenEtiqueta(482),
      'th_3' => ObtenEtiqueta(485),
      'th_4' => ObtenEtiqueta(486),
      'th_5' => ObtenEtiqueta(374),
      'th_6' => ObtenEtiqueta(596),
      'th_7' => ObtenEtiqueta(483),
      'th_8' => 'Download'
    );
    echo json_encode((Object) $th);
  }

  function GetPaymentHistory($fl_alumno, $cl_sesion){

    # Initiate variables
    $result["size"] = array();

    # Recupera el programa y term que esta cursando el alumno
    $fl_programa = ObtenProgramaAlumno($fl_alumno);
    $fl_term = ObtenTermAlumno($fl_alumno);
    /*if(empty($fl_programa) AND empty($fl_term)){
      $row = RecuperaValor("SELECT fl_programa FROM k_ses_app_frm_1 WHERE cl_sesion='$cl_sesion' ");
      $fl_programa= $row[0];
      $row = RecuperaValor("SELECT fl_term FROM k_alumno_term WHERE fl_alumno=$fl_alumno");
      $fl_term = $row[0];
    }*/

    # Recupera el term inicial
    /*$Query  = "SELECT fl_term_ini ";
    $Query .= "FROM k_term ";
    $Query .= "WHERE fl_programa=$fl_programa ";
    $Query .= "AND fl_term=$fl_term";
    $row = RecuperaValor($Query);
    $fl_term_ini = $row[0];*/

    # Recupera el tipo de pago para el curso
    $Query  = "SELECT fg_opcion_pago,tax_mn_cost ";
    $Query .= "FROM k_app_contrato  ";
    $Query .= "WHERE cl_sesion='$cl_sesion'";
    $row = RecuperaValor($Query);
    $fg_opcion_pago = $row[0];
    $tax_mn_cost= !empty($row['tax_mn_cost'])?$row['tax_mn_cost']:0;

    if(empty($fl_term_ini))
      $fl_term_ini=$fl_term;

    # Se obtiene la descripcion de la frecuencia del pago
    switch($fg_opcion_pago) {
      case 1:
        $mn_due='mn_a_due';
        $ds_frecuencia='ds_a_freq';
        $ds_pagos='no_a_payments';
        break;
      case 2:
        $mn_due='mn_b_due';
        $ds_frecuencia='ds_b_freq';
        $ds_pagos='no_b_payments';
        if ($tax_mn_cost > 0) {
                 $tax_mn_cost = $tax_mn_cost / 2;
         }
        break;
      case 3:
        $mn_due='mn_c_due';
        $ds_frecuencia='ds_c_freq';
        $ds_pagos='no_c_payments';
        if ($tax_mn_cost > 0) {
                 $tax_mn_cost = $tax_mn_cost / 4;
         }
        break;
      case 4:
        $mn_due='mn_d_due';
        $ds_frecuencia='ds_d_freq';
        $ds_pagos='no_d_payments';
        break;
    }
    $Query  = "SELECT $ds_frecuencia, $ds_pagos, no_semanas ";
    $Query .= "FROM k_programa_costos ";
    $Query .= "WHERE fl_programa=$fl_programa ";
    $row = RecuperaValor($Query);
    $ds_frecuencia = $row[0];
    $no_pagos_opcion = $row[1];
    $no_semanas = $row[2];

    #Obtenemos al otro alumno con el mismo nombre y apellidos
    $row2 = RecuperaValor("SELECT ds_nombres, ds_apaterno, fg_genero, ds_login FROM c_usuario WHERE fl_usuario=$fl_alumno");
    $ds_nombres=$row2[0];
    $ds_apaterno=$row2[1];
    $fg_genero=$row2[2];
    $ds_login= $row2[3];

    # Busca si existen alumnos con el mismo nombre y apellidos
    $Query = "SELECT fl_usuario, CONCAT(ds_nombres,' ',ds_apaterno), ds_login,cl_sesion,fg_activo FROM c_usuario WHERE ds_nombres='".$ds_nombres."' AND ds_apaterno='".$ds_apaterno."' AND ds_login <> '".$ds_login."' AND fl_perfil_sp IS null ";
    $rs = EjecutaQuery($Query);
    for($i=0;$row=RecuperaRegistro($rs);$i++) {

        $cl_sesion1=$row['cl_sesion'];
        $fg_activo=$row['fg_activo'];
        $Querysp="SELECT fl_programa FROM k_ses_app_frm_1 WHERE cl_sesion='$cl_sesion1' ";
        $rowes=RecuperaValor($Querysp);
        $fl_programa1=$rowes['fl_programa'];

        $Querypr ="SELECT nb_programa FROM c_programa WHERE fl_programa=$fl_programa1 ";
        $rowp=RecuperaValor($Querypr);
        $nb_programa=$rowp['nb_programa'];

        if($fg_activo=='1'){
            $fg_activo="Active";
        }else{
            $fg_activo="Inactive";
        }


        $users[$i] = "$nb_programa - <a href='".PAGINA_SALIR."'>".$row[1]."&nbsp;".$row[2]."</a> $fg_activo ";
    }
    if($i > 0){
      $result["size"] += array("total_logins" => $i);
      $result["logins"] = (Object) $users;
    }

    //para obtener informacion del pago de app_fee
    $Query = "SELECT DISTINCT fl_sesion, CASE a.cl_metodo_pago WHEN 1 THEN 'Paypal' WHEN 2 THEN 'Paypal Manual' WHEN 3 THEN 'Cheque' WHEN 4 THEN 'Credit Card' ";
    $Query .= "WHEN 5 THEN 'Wire Transfer' WHEN 6 THEN 'Cash' END cl_metodo_pago, (CONCAT(DATE_FORMAT(fe_pago, '%d-%m-%Y'), ' ', DATE_FORMAT(fe_pago, '%H:%i:%s'))) fe_pago, ";
    $Query .="CONCAT('','',FORMAT(c.mn_app_fee,2)),ds_comentario,fg_pago, ".ConsultaFechaBD('b.fe_ultmod',FMT_FECHA).", fg_inscrito FROM c_sesion a, k_ses_app_frm_1 b, k_app_contrato c  WHERE a.cl_sesion='$cl_sesion' AND b.cl_sesion='$cl_sesion'  ";
    $row = RecuperaValor($Query);
    $fl_sesion =$row[0];
    $cl_metodo_app = $row[1];
    $fe_pago_app = $row[2];
    $mn_pagado_app = $row[3];
    $ds_comentario_app = $row[4];
    $fg_pago_app = $row[5];
    $fe_ultmod1 =  str_texto($row[6]);
    $fg_inscrito = $row[7];

    if(!empty($mn_pagado_app)){
      $result["app_fee"] = array(
        'td_1' => 'Once ',
        'td_2' => 'Once',
        'td_3' => $fe_ultmod1,
        'td_4' => $mn_pagado_app,
        'td_5' => $fe_pago_app,
        'td_6' => $mn_pagado_app,
        'td_7' => $cl_metodo_app,
        'td_8' => "<a href='".PATH_N_ALU_PAGES."/invoice.php?fl_sesion=$fl_sesion'><img src='".PATH_ADM_IMAGES."/icon_pdf.gif' width=30 height=30 border=0 title='".ObtenEtiqueta(487)."'></a>"
      );
    }

    $Query  = "SELECT DATE_FORMAT(fe_firma, '%b %d, %Y') fe_firma,mn_costs,ds_costs,mn_discount,ds_discount FROM k_app_contrato WHERE cl_sesion='$cl_sesion'";
    $row = RecuperaValor($Query);
    $fe_firma = $row['fe_firma'];
    $mn_costs=!empty($row['mn_costs'])?$row['mn_costs']:0;
    $ds_costs=$row['ds_costs'];
    $mn_discount = !empty($row['mn_discount'])?$row['mn_discount']:0;
    $ds_discount = $row['ds_discount'];

    if($mn_costs>0){

        $result["additional_cost"] = array(
            'td_1' => 'Additional costs ',
            'td_2' => '',
            'td_3' => $fe_firma,
            //'td_4' => "$" . $mn_costs,
            'td_4' => $mn_costs,
            'td_5' => $fe_firma,
            //'td_6' => "$".$mn_costs,
            'td_6' => $mn_costs,
            'td_7' => 'Additional costs '.$ds_costs.'',
            'td_8' => "<a href='".PATH_CAMPUS."/students/invoice_additional.php?fl_sesion=$fl_sesion'><img src='".PATH_ADM_IMAGES."/icon_pdf.gif' width=30 height=30 border=0 title='".ObtenEtiqueta(487)."'></a>"
         );

      }



    #Obtenemos la fecha actual
    $fe_actual  = ObtenFechaActual();

    # Obtenemos lospagosqueya realizo
    $Queryp  = "SELECT  a.fl_term_pago, b.no_opcion, b.no_pago, ".ConsultaFechaBD('b.fe_pago', FMT_FECHA).",
    (SELECT $mn_due FROM k_app_contrato WHERE cl_sesion='$cl_sesion' AND no_contrato='1'), ";
    $Queryp .= "CASE a.cl_metodo_pago ";
    $Queryp .= "WHEN 1 THEN 'Stripe' WHEN 2 THEN 'Manual' WHEN 3
    THEN 'Cheque' WHEN 4 THEN 'Credit Card' WHEN 5 THEN 'Wire Transfer/Deposit' WHEN 6 THEN 'Cash' ";
    $Queryp .= "END ds_metodo_pago, ";
    $concat = array(ConsultaFechaBD('a.fe_pago', FMT_FECHA), "' '", ConsultaFechaBD('a.fe_pago', FMT_HORA)); // formato de la fecha en que pago
    $Queryp .= " ".ConcatenaBD($concat).", a.mn_pagado, a.ds_comentario, a.fl_alumno_pago, a.cl_metodo_pago, a.fg_refund,
    DATEDIFF(a.fe_pago, '$fe_actual') no_dias, ";
    $Queryp .= "(SELECT nb_periodo FROM c_periodo r,k_term d WHERE r.fl_periodo=d.fl_periodo AND d.fl_term=b.fl_term) nb_periodo ";
    $Queryp .= "FROM k_alumno_pago a, k_term_pago b ";
    $Queryp .= "WHERE a.fl_term_pago = b.fl_term_pago AND  a.fl_alumno=$fl_alumno ORDER BY b.fe_pago";
    $rsp = EjecutaQuery($Queryp);
    for($i=0;$rowp = RecuperaRegistro($rsp);$i++){
      $fl_term_pago_p = $rowp[0];
      $no_opcion_p = $rowp[1];
      $no_pago_p = $rowp[2];
      $fe_limite_pago_p = $rowp[3];
      $mn_pago_p = $rowp[4];
      $ds_metodo_pago_p = $rowp[5];
      $fe_pago_p = $rowp[6];
      $mn_pagado_p = $rowp[7];
      $ds_comentario_det_p = $rowp[8];
      $fl_alumno_pago_p = $rowp[9];
      $cl_metodo_pago_det_p = $rowp[10];
      $fg_refund_p = $rowp[11];
      $no_dias = $rowp[12];

      # numero de pago
      $numero_pago = $i + 1;

      /***
       * Casdo particular Ariel Rabesca
       *
       */
      if($fl_alumno==2829){

          $ds_frecuencia="Monthly";



      }

      /**
        * MJD 2023 JUL,24
        *
        */
     /*   if ($fg_opcion_pago == 1) {

            if ($mn_costs > 0) {
                $mn_pago_p = $mn_pago_p - $mn_costs;
                $mn_pagado_p = $mn_pagado_p - $mn_costs;
            }

        }
        */

        $mn_pago_p = $mn_pago_p + $tax_mn_cost;

        $result["fee".$i] = array(
        'td_1' => $numero_pago,
        'td_2' => $ds_frecuencia,
        'td_3' => $fe_limite_pago_p,
        //'td_4' => "$" . $mn_pago_p,
        'td_4' => $mn_pago_p,
        'td_5' => $fe_pago_p,
        'td_6' => $mn_pagado_p,
        'td_7' => $ds_metodo_pago_p,
        'td_8' => "<a href='".PATH_N_ALU_PAGES."/invoice.php?f=$fl_term_pago_p&pago=$no_pago_p&n_pago=$numero_pago'><img src='".PATH_ADM_IMAGES."/icon_pdf.gif' width=30 height=30 border=0 title='".ObtenEtiqueta(487)."'></a>"
      );
      $pagos_realizados ++;
    }
    $result["size"] += array("total_payments_realizados" => $i);

    # Verificamos si repitio el grado
    $Query3 = "SELECT no_grado FROM k_alumno_term a, k_term b WHERE a.fl_term=b.fl_term and fl_alumno=$fl_alumno  ";
    $rs3 = EjecutaQuery($Query3);
    $r = '';
    $repetido = 0 ;
    for($i=0; $row3=RecuperaRegistro($rs3); $i++){
      if($r == $row3[0])
        $repetido++;
      $no_grado_re = $row3[0];
      $r = $no_grado_re;
    }

    if(empty($pagos_realizados))
        $pagos_realizados=0;

    # Obtenemos el term inicial actual
    $Query  = "SELECT fl_term_ini FROM k_term WHERE fl_programa=$fl_programa AND fl_term=$fl_term";
    $row = RecuperaValor($Query);
    $fl_term_ini = $row[0];
    if(empty($fl_term_ini))
      $fl_term_ini=$fl_term;
    # Obtenemos el term inicial cuando se incribio
    $row = RecuperaValor("SELECT MIN(fl_term) FROM k_alumno_term WHERE fl_alumno=$fl_alumno ");
    $term_ini = $row[0];

    if($fl_alumno==2829){ #Caso en particular para Ariel Rabesca que reptie term.
        $term_ini=$fl_term;
        $fl_term_ini=$fl_term;

    }


    # Si el term inicial incrito es igual al term inicial actual entonces el term inicial es el incrito
    if($term_ini==$fl_term_ini){
      $fl_term_ini=$term_ini;
      if(!empty($pagos_realizados))
        $pagos_extras = "AND no_pago>$pagos_realizados ";
    }
    else{ # si el term incrito es diferente del term actual entonces el term inicial es el actual
      $fl_term_ini = $fl_term_ini;
      # Obtenemos el total de pagos y numeros de term para identificar los meses que cubre un term
      $row1 = RecuperaValor("SELECT no_grados, $ds_pagos FROM c_programa a, k_programa_costos k WHERE a.fl_programa=k.fl_programa AND a.fl_programa =$fl_programa");
      $no_grados = $row1[0];
      $no_x_payments = $row1[1];
      if($repetido>0)
        $meses_x_term = ($no_x_payments/$no_grados)*$repetido;
      else
        $meses_x_term = $no_x_payments/$no_grados;
      if($repetido>0){
        # Obtenemos el total se pagos realizados y si recursa un term tendran que haber pagos extras de los
        $pagos_extras = "AND no_pago>$pagos_realizados-$meses_x_term ";
      }
      else{
        $pagos_extras="";
        $fl_term_ini = $term_ini;
      }
    }

	#Con esto se corrrige el error comun(pagos_repetidos,que aveces no se registran), se establece los pagos faltantes.
    if($fl_alumno==289){
	if(empty($pagos_extras)){
        $pagos_extras="AND no_pago>$pagos_realizados ";
    }
	}

	#Con esto eliminamos errores de query cuando el term es repetido.
	if(($fl_alumno<>963)&&($fl_alumno<>965)){
		if($pagos_extras){

			$data = explode("-", $pagos_extras);
			$pagos_extras=$data[0];


		}
    }
	if($fl_alumno==3833){
        $pagos_extras="";
    }

    #Obtenemos la fecha actual
    $fe_actual  = ObtenFechaActual();
    # Datos de pagos que no se han realizado
    $Query  = "SELECT fl_term_pago, no_opcion, no_pago, ".ConsultaFechaBD('fe_pago', FMT_FECHA)." , $mn_due, DATEDIFF(a.fe_pago, '$fe_actual') no_dias ";
    $Query .= "FROM k_term_pago a, k_app_contrato b ";
    $Query .= "WHERE fl_term=$fl_term_ini ";
    $Query .= "AND no_opcion=$fg_opcion_pago AND no_contrato=1 AND cl_sesion='$cl_sesion' $pagos_extras ";
    $Query .= "ORDER BY no_pago ";
    $rs = EjecutaQuery($Query);
    for($i=0; $row = RecuperaRegistro($rs); $i++) {
      $fl_term_pago = $row[0];
      $no_opcion = $row[1];
      $no_pago = $row[2];
      if($fl_term_ini != $term_ini){
        if($repetido>0)
          $no_pago_lista = ($pagos_realizados + 1) + $i;
        else
          $no_pago_lista = $pagos_realizados + $i;
      }
      else
        $no_pago_lista = $no_pago;
      $fe_limite_pago = $row[3];
      $mn_pago = $row[4];
      $no_dias = $row[5];

      //para obtener los pagos
      $concat = array(ConsultaFechaBD('fe_pago', FMT_FECHA), "' '", ConsultaFechaBD('fe_pago', FMT_HORA));
      $Query  = "SELECT fl_term_pago, ";
      $Query .= "CASE cl_metodo_pago WHEN 1 THEN '".ObtenEtiqueta(488)."' WHEN 2 THEN '".ObtenEtiqueta(488)." Manual' ";
      $Query .= "WHEN 3 THEN 'Cheque' WHEN 4 THEN 'Credit Card' WHEN 5 THEN 'Wire Transfer/Deposit' WHEN 6 THEN 'Cash' ";
      $Query .= "END ds_metodo_pago, ";
      $Query .= "(".ConcatenaBD($concat).") fe_pago, mn_pagado, ds_comentario, fl_alumno_pago, cl_metodo_pago,fg_refund, ";
      $Query .= "(SELECT nb_periodo FROM c_periodo b, k_term_pago d, k_term e
      WHERE b.fl_periodo=e.fl_periodo AND d.fl_term=e.fl_term AND d.fl_term_pago=a.fl_term_pago) nb_periodo ";
      $Query .= "FROM k_alumno_pago a ";
      $Query .= "WHERE fl_term_pago=$fl_term_pago ";
      $Query .= "AND fl_alumno=$fl_alumno";
      $row = RecuperaValor($Query);
      $fl_t_pago = $row[0];
      $ds_metodo_pago = $row[1];
      if(empty($ds_metodo_pago))
        $ds_metodo_pago = "(To be paid)";
      $fe_pago = $row[2];
      if(empty($fe_pago))
        $fe_pago = "(To be paid)";
      $mn_pagado = $row[3];
      if(empty($mn_pagado))
        $mn_pagado = "(To be paid)";
      $ds_comentario_det = str_uso_normal($row[4]);
      $fl_alumno_pago = $row[5];
      $cl_metodo_pago_det = $row[6];
      $fg_refund = $row[7];
      $nb_period = $row[8];

      $Query  = "SELECT $mn_due ";
      $Query .= "FROM k_app_contrato ";
      $Query .= "WHERE cl_sesion='$cl_sesion'";
      $row = RecuperaValor($Query);
      $mn_due = $row[0];

      if(empty($fl_t_pago)) {
        if(empty($proximo_pago)){
          $pay_now=True;
          $proximo_pago=$fl_term_pago;
          $no_opcion_pagar=$no_opcion;
          $no_pago_pagar=$no_pago;
          $fe_limite_pago_pagar=$fe_limite_pago;
          $mn_due_pagar=$mn_due;
        }
        else {
          $pay_now=false;
        }
      }
      # Si existe en la tabla pero la fecha pago fue despues de fe limite se le agrega el latte fee,
      # Si no existe en registro en k_alumno_pago y no dias es menor a 0 (fecha limite se paso) agrega el latte fee,
      # Si los dias nos mayores a 0(fecha limite no se ha pasado) obtiene el pago de k_app_contrato
      if(ExisteEnTabla('k_alumno_pago','fl_term_pago',$fl_term_pago) AND $dias<0){
          $mn_due_pagar = Number_Format($mn_due + ObtenConfiguracion(66),2,'.',',');
          $mn_due_pay=$mn_due+ObtenConfiguracion(66);//MJD
          $fg_late_fee=1;
          $mn_late_fee = "<br>Late Fee: ".ObtenConfiguracion(66);
      }else{
          if($no_dias<0 AND !ExisteEnTabla('k_alumno_pago', 'fl_term_pago', $fl_term_pago)){
              $mn_due_pagar = Number_Format($mn_due + ObtenConfiguracion(66),2,'.',',');
              $mn_due_pay=$mn_due+ObtenConfiguracion(66);//mjd
              $fg_late_fee=1;
              $mn_late_fee = "<br>Late Fee: ".ObtenConfiguracion(66);
          }else{
              $mn_due_pagar = Number_Format($mn_due,2,'.',',');
              $mn_due_pay=$mn_due;//MJD
              $fg_late_fee=0;
              $mn_late_fee = null;
          }
      }
    #  $fl_pais_campus=226;

      #$mn_due_pagar=1400;


      if($fl_term_pago==6350 && $fl_alumno== 11512 & $no_pago_lista==3)
      {
            $mn_due_pagar = 3638;

          $mn_due_pay=3638;
      }




      $convenie_fee_description="";
      $mn_convenie_fee=0;
      $mn_due_pagar_total=0;
      if($pay_now){
          $receipt = "<a href='#ajax/tuition_payment.php'>Pay Now!</a>";

          #Bank Usa /CANADA
          /*if($fl_pais_campus==226){
              $public_key=ObtenConfiguracion(166);
              $currency="USD";
          }else{
              $public_key=ObtenConfiguracion(111);
              $currency="CAD";
          }*/

            $Qyery = "SELECT fl_pais_campus FROM c_sesion WHERE cl_sesion='$cl_sesion' ";
            $row = RecuperaValor($Qyery);
            $fl_pais_campus = $row['fl_pais_campus'];



            switch ($fl_pais_campus) {

                case '38':
                    $public_key = ObtenConfiguracion(111);
                    $currency = "CAD";
                    $symbol = "$";
                    break;
                case '226':
                    $public_key = ObtenConfiguracion(166);
                    $currency = "USD";
                    $symbol = "$";
                    break;
                case '199':
                    $public_key = ObtenConfiguracion(111);
                    $currency = "EUR";
                    $symbol = "€";

                    break;
                case '73':
                    $public_key = ObtenConfiguracion(111);
                    $currency = "EUR";
                    $symbol = "€";

                    break;
                case '80':
                    $public_key = ObtenConfiguracion(111);
                    $currency = "EUR";
                    $symbol = "€";

                    break;
                case '105':
                    $public_key = ObtenConfiguracion(111);
                    $currency = "EUR";
                    $symbol = "€";

                    break;
                case '225':
                    $public_key = ObtenConfiguracion(111);
                    $currency = "GBP";
                    $symbol = "£";
                    break;
                case '153':
                    $public_key = ObtenConfiguracion(111);
                    $currency = "EUR";
                    $symbol = "€";
                    break;

                default:
                    $public_key = ObtenConfiguracion(111);
                    $currency = "CAD";
                    $symbol = "$";

                    break;

            }



            $convenie_fee = ObtenConfiguracion(165);

            $mn_convenie_fee=(($mn_due_pay + $tax_mn_cost) * $convenie_fee)/100;
              $mn_due_pagar_total=($mn_due_pay + $tax_mn_cost)+$mn_convenie_fee;
              $convenie_fee_description="<small>Credit card convenience fee: ". $symbol."".number_format($mn_convenie_fee,2)." ".$currency."</small>";
            $receipt="

                        <form action='ajax/procesar_pago.php' method='POST'>
                            <input type='hidden' name='fl_alumno' id='fl_alumno' value='".$fl_alumno."'>
                            <input type='hidden' name='fl_term_pago' id='fl_term_pago' value='".$fl_term_pago."'>
                            <input type='hidden' name='fl_pais_campus' id='fl_pais_campus' value='".$fl_pais_campus."'>
                            <input type='hidden' name='tax_mn_cost' id='tax_mn_cost' value='". $tax_mn_cost."'>
                            <input type='hidden' name='fl_term' id='fl_term' value='" . $fl_term_ini . "'>
                            <input type='hidden' name='fl_programa' id='fl_programa' value='".$fl_programa."'>
                            <input type='hidden' name='mn_total' id='mn_total' value='".($mn_due_pay + $tax_mn_cost)."'>
                            <input type='hidden' name='mn_total_total' id='mn_total_total' value='".$mn_due_pagar_total."'>
                            <input type='hidden' name='mn_convenie_fee' id='mn_convenie_fee' value='".$mn_convenie_fee."'>
                            <input type='hidden' name='fg_late_fee' id='fg_late_fee' value='".$fg_late_fee."'>
                          <script
                            src='https://checkout.stripe.com/checkout.js' class='stripe-button'
                            data-key='".$public_key."'
                            data-amount='".($mn_due_pagar_total*100)."'
                            data-name='VANAS'
                            data-html='True'
                            data-currency=$currency
                            data-description='Convenience Fee: ". $symbol."".$mn_convenie_fee." '
                            data-label='Pay Now ". $symbol." ".number_format($mn_due_pagar_total,2)." ".$currency." '
                            data-image='https://".ObtenConfiguracion(60)."/images/".ObtenNombreImagen(19)."'
                            data-locale='en'>
                          </script>
                        </form>

                        ";

      }else{
          $receipt = "";
      }
      if(empty($fl_t_pago))
        $muestra = True;
      else
        $muestra = False;

      /*Caso especial de Dylan Lawor  en el nuevo hay que poder definir y que sus pagos sean por alumno, y por fecha actualmente esta todo general., asi sera mas facil manipular el cobro por estudiante*/
      if(($fl_alumno==3833)&&($fl_term_pago==4095)){
          $mn_due_pagar="4928.00";
      }

      /*  if ($fg_opcion_pago == 1) {
            if ($mn_costs > 0) {
                $mn_due_pagar = $mn_due_pagar - $mn_costs;
                $mn_pagado = $mn_pagado - $mn_costs;
            }
        }
        */

        $mn_due_pagar = $mn_due_pay + $tax_mn_cost;



        $result["fee_faltan".$i] = array(
        'td_0' => $pagos_realizados,
        'td_1' => $no_pago_lista,
        'td_2' => $ds_frecuencia,
        'td_3' => $fe_limite_pago,
        'td_4' => $mn_due_pagar." ".$mn_late_fee,
        //'td_4' => "$".$mn_due_pagar,
        'td_5' => $fe_pago,
        'td_6' => $mn_pagado,
        'td_7' => $ds_metodo_pago,
        'td_8' => $receipt,
        'td_9' => $muestra
      );
    }
    $result["size"] += array("total_payments_faltan" => $i);

    # De la fecha inicial del curso hasta su fecha completado, obtenemos los años  jgfl
    $Query  = "SELECT ".ConsultaFechaBD('c.fe_inicio',FMT_FECHA).", nb_programa, fg_taxes, fg_desercion ";
    $Query .= "FROM k_term b, c_periodo c, k_alumno_term d, k_pctia e, c_programa f ";
    $Query .= "WHERE b.fl_periodo=c.fl_periodo AND b.fl_programa=f.fl_programa ";
    $Query .= "AND b.fl_term=d.fl_term AND d.fl_alumno=e.fl_alumno AND d.fl_alumno='$fl_alumno' ";
    $Query .= "AND no_grado=1 ";
    $row = RecuperaValor($Query);
    $fe_inicio_programa = $row[0];
    $nb_programa = $row[1];
    $fg_taxes = $row[2];
    $fg_desercion = $row[3];

    # Obtenemos el pago total que realizo el estudiante
    $rowt = RecuperaValor("SELECT $mn_due FROM k_app_contrato WHERE cl_sesion='$cl_sesion'");
    $mn_due_tax = $rowt[0];

     # Conocemos si el programa tiene permitido generar el taxes
     # Le sumamos lo numero de meses a la fecha inicial para obtener el fecha final
    # Calculamos la cantidad que se paga por mes
    $fe_inicio1 = DATE_FORMAT(date_create($fe_inicio_programa),'Y-m-d');
    $mes_inicio1 = DATE_FORMAT(date_create($fe_inicio_programa),'m');
    $anio_inicio1 = DATE_FORMAT(date_create($fe_inicio_programa),'Y');
    if(!empty($fg_taxes)){
      # Inicia tabla de los T220a y validamos si con el programa se realizan
      $meses = ($no_semanas/4);
      $fe_nueva = strtotime ( '+ '.($meses-1).' month' , strtotime ( $fe_inicio1 ) ) ;
      $fe_fin1 = date ( 'Y-m-d' , $fe_nueva );
      $mes_fin1 = date ( 'm' , $fe_nueva );
      $anio_fin1 = date ( 'Y' , $fe_nueva );
      $anios1 = $anio_fin1 - $anio_inicio1;

      $total = 0;
      for($i=0;$i<=$anios1;$i++){
        $anios2=$anio_inicio1+$i;
        if($anios2<date('Y')){
          # Obtiene los meses que conforman el anio para el que se pago
          if($anio_inicio1==$anio_fin1)
            $num_meses_anio=$mes_fin1-$mes_inicio1+1;
          else{
            $num_meses_anio = 12;
            if($anios2==$anio_fin1)
              $num_meses_anio = $mes_fin1;
            if($anios2==$anio_inicio1)
              $num_meses_anio = 12-$mes_inicio1+1;
          }

          # Monto pagado en el anio
          $monto = ($mn_due_tax / ($meses/$no_pagos_opcion)) * $num_meses_anio;
          $monto = number_format($monto,2,'.',',');

          # Obtenemos los meses que cubren lo pagos
          # Obtenemos su nombre para mostrarlos en la tabla
          if($anios2==$anio_inicio1){
            if($anio_inicio1==$anio_fin1){
              $mes_ini= $mes_inicio1;
              $mes_fin= $mes_fin1;
            }
            else{
              $mes_ini =$mes_inicio1;
              $mes_fin=12;
            }
          }
          else {
            $mes_ini =1;
            $mes_fin=$mes_fin1;
            if($anios2 != $anio_fin1)
              $mes_fin=12;
          }

          # Si el alumno se retiro antes de acabar el curso
          # Obtenemos el ultimo pago y hasta ahi sumamos las cantidades
          if(!empty($fg_desercion) AND ($anios2!=$anio_fin1 AND $anios2!=$anio_inicio1)){
            $Query = "SELECT DATE_FORMAT(fe_pago,'%m') FROM k_alumno_pago WHERE fl_alumno=$fl_alumno AND DATE_FORMAT(fe_pago, '%Y')='$anios2' order by fe_pago DESC ";
            $row = RecuperaValor($Query);
            $num_meses_anio = $row[0];
            $mes_fin = $row[0];
          }

          # Monto pagado en el anio
          $monto = ($mn_due_tax / ($meses/$no_pagos_opcion)) * $num_meses_anio;
          $monto = number_format($monto,2,'.',',');

          $mes_ini = ObtenNombreMes($mes_ini);
          $mes_fin = ObtenNombreMes($mes_fin);

          if(($fl_alumno==1535)){

              if($anios2==2019){#Caso especifico para QiJun
                  $monto=number_format(11324,2,'.',',');
              }else{
                  $monto=number_format(7450,2,'.',',');
              }
          }



          # Datos de los taxes
          $pdf_form = "<a href='../students/taxes.php?anio=$anios2&fl_alumno=$fl_alumno&fl_term=$fl_term&num_meses_anio=$num_meses_anio&monto=$monto' target='_blank'><img src='".PATH_ADM_IMAGES."/icon_pdf.gif' width=30 height=30 border=0 title='".ObtenEtiqueta(487)."'></a>";
          $result["form".$i] = array(
            'td_1' => $nb_programa,
            'td_2' => $anios2,
            'td_3' => $mes_ini,
            'td_4' => $mes_fin,
            'td_5' => $monto,
            'td_6' => $pdf_form
          );
          $total++;
        }
      }
      $result["size"] += array("total_forms" => $total);
      $mensaje1 = "";

    }
    else{
      $mensaje1 = str_replace("#course_name#", "<strong>".$nb_programa."</strong>", ObtenEtiqueta(2238));
    }
    $mensaje2 = ObtenEtiqueta(2239);
    $mensaje2 = str_replace("#current_year#", "<strong>".date('Y')."</strong>", $mensaje2);
    $mensaje2 = str_replace("#program_start_date#", "<strong>".$anio_inicio1."</strong>", $mensaje2);


    $result["taxes"] = array('fg_taxes'=>$fg_taxes,'mensaje'=>$mensaje1, 'mensaje2'=>$mensaje2);
    echo json_encode((Object)$result);
  }

  function GetTaxFormHeaders(){
    $th = array(
      'th_1' => ObtenEtiqueta(360),
      'th_2' => "Year",
      'th_3' => "Initial Month",
      'th_4' => "Final Month",
      'th_5' => ObtenEtiqueta(583),
      'th_6' => 'Download'
    );
    echo json_encode((Object) $th);
  }
?>

<div class="row">

  <!-- T2202A -->
  <div id="tax-container" class="col-xs-12" style="display:none;">
    <div class="well well-light padding-10">
      <div class="row">
        <div class="col-xs-12"> 
          <div class="well well-light no-margin no-padding">
            <div class="well well-light no-margin no-padding">
              <h6 class="text-center no-margin padding-5"><?php echo ObtenEtiqueta(692); ?></h6>
              <!--Si no muestra tax form entonces mostrara mensaje-->
              <div id="mensaje_taxform" class="text-align-center alert-danger padding-10" style="margin:10px;"><div id="msg_taxes"></div><?php echo str_uso_normal(ObtenMensaje(228)); ?></div>
              <div id="mensaje_taxform_extras" class="text-align-center alert-danger padding-10 hidden" style="margin:10px;"></div>
            </div>
            <table id="tax-form-table" class="table table-striped table-hover"></table>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Payment history -->
  <div class="col-xs-12">
    <div class="well well-light padding-10">
      <div class="row">
        <div class="col-xs-12"> 
          <div class="well well-light no-margin no-padding">
            <div class="well well-light no-margin no-padding">
              <h6 class="text-center no-margin padding-5">Payment History</h6>
            </div>
            <table id="payment-table" class="table table-striped table-hover"></table>
            <?php
             if(!empty($msg_error)){
                 echo"<div class='text-align-center alert-danger padding-10'>
                               $msg_error 
                        </div>";
             }
            
            ?>
          </div>
        </div>
      </div>
    </div>
  </div>
 
<!-- Login as  -->
  <div id="login-container" class="col-xs-12" style="display:none;">
    <div class="well well-light padding-10">
      <div class="row">
        <div class="col-xs-12"> 
          <div class="well well-light no-margin no-padding">
            <div class="well well-light no-margin no-padding">
              <h6 class="text-center no-margin padding-5"><?php echo ObtenEtiqueta(694); ?></h6>
            </div>
            <table id="login-table" class="table table-striped table-hover"></table>
          </div>
        </div>
      </div>
    </div>
  </div>


</div>

<script type="text/javascript">

  // Initiate variables
  var loginContainer, loginTable, paymentTable, taxContainer, taxFormTable, result;
  loginContainer = $("#login-container");
  loginTable = $("#login-table");
  paymentTable = $("#payment-table");
  taxContainer = $("#tax-container");
  taxFormTable = $("#tax-form-table");

  result = <?php GetPaymentHistory($fl_alumno, $cl_sesion); ?>;

  // Login with found user section
  if(result.size.total_logins){
    // display the container
    loginContainer.toggle();

    for(var i=0; i<result.size.total_logins; i++){
      var login = result.logins;
      loginTable.append("<tr class='text-center'><td>"+login[i]+"</td></tr>");
    }
  }

  // Payment history section.
  var headers;
  headers = <?php GetPaymentHeaders(); ?>;

  // setup payment table headers
  var th = "";
  for (var k in headers){
    th += "<th>"+headers[k]+"</th>";
  }
  paymentTable.append("<thead><tr>"+th+"</tr></thead>");

  var app_fee;
  app_fee = result.app_fee;

  // app fee
  var td = "<tr>";
  for(var k in app_fee){
    td += "<td>"+app_fee[k]+"</td>";
  }
  td += "</tr>";

//aditional cost
    var additional_cost;
    additional_cost = result.additional_cost;

  td += "<tr>";
  for(var a in additional_cost){
    td += "<td>"+additional_cost[a]+"</td>";
  }
  td += "</tr>";


  // fees realizdos
  for(var i=0; i<result.size.total_payments_realizados; i++){
    var fee = result["fee"+i];
    td += "<tr>";
    td += "<td>"+fee.td_1+"</td>";
    td += "<td>"+fee.td_2+"</td>";
    td += "<td>"+fee.td_3+"</td>";
    td += "<td>"+fee.td_4+"</td>";
    td += "<td>"+fee.td_5+"</td>";
    td += "<td>"+fee.td_6+"</td>";
    td += "<td>"+fee.td_7+"</td>";
    td += "<td>"+fee.td_8+"</td>";
    td += "</tr>";
  }
  for(var i=0; i<result.size.total_payments_faltan; i++){
    var fee_faltan = result["fee_faltan"+i];
    //if(fee_faltan.td_1 > fee_faltan.td_0){
      if(fee_faltan.td_9){
        td += "<tr>";
        td += "<td>"+fee_faltan.td_1+"</td>";
        td += "<td>"+fee_faltan.td_2+"</td>";
        td += "<td>"+fee_faltan.td_3+"</td>";
        td += "<td>"+fee_faltan.td_4+"</td>";
        td += "<td>"+fee_faltan.td_5+"</td>";
        td += "<td>"+fee_faltan.td_6+"</td>";
        td += "<td>"+fee_faltan.td_7+"</td>";
        td += "<td>"+fee_faltan.td_8+"</td>";
        td += "</tr>";
      }
    //}
    
  }
  paymentTable.append("<tbody>"+td+"</tbody>");
  // Nuevo mensaje   
    $("#msg_taxes").empty().append(result.taxes.mensaje2);
  // T2202A section
  if(result.taxes.fg_taxes==1){
    if(result.size.total_forms){
      // display the container
      taxContainer.toggle();

      var headers;
      headers = <?php GetTaxFormHeaders(); ?>;

      // setup T2202A table headers
      var th = "";
      for (var k in headers){
        th += "<th>"+headers[k]+"</th>";
      }
      taxFormTable.append("<thead><tr>"+th+"</tr></thead>");

      // tax forms
      var td = "";
      for(var i=0; i<result.size.total_forms; i++){
        var form = result["form"+i];
        td += "<tr>";
        td += "<td>"+form.td_1+"</td>";
        td += "<td>"+form.td_2+"</td>";
        td += "<td>"+form.td_3+"</td>";
        td += "<td>"+form.td_4+"</td>";
        td += "<td>"+form.td_5+"</td>";
        td += "<td>"+form.td_6+"</td>";
        td += "</tr>";
      }
      taxFormTable.append("<tbody>"+td+"</tbody>");
      $("#mensaje_taxform").css("display","none");
    }
    else{
      taxContainer.css("display","inline"); 
    }

  }
  else{
    taxContainer.css("display","inline"); 
    $("#tax-form-table").remove();
    $("#mensaje_taxform_extras").removeClass('hidden').append(result.taxes.mensaje);
  }
</script>
