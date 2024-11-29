<?php

  # Libreria de funciones
  require("lib/sp_general.inc.php");

  # Recibe parametros
  $fg_error = RecibeParametroNumerico('fg_error');
  $fg_exito = RecibeParametroNumerico('success', True);
  if(!$fg_error)
  {
    $clave = RecibeParametroHTML('c', True, True);
    $fl_sesion = RecibeParametroHTML('s', True, True);
    $opc_pago = '1';
    $conf1 = '';
    $conf2 = '';
    $conf3 = '';
    $conf4 = '';
    $ds_firma = '';
    $ds_firma_rep_legal = '';
    $cl_metodo_pago = 0;
    $ds_metodo_otro = '';
    if(empty($fl_sesion)){

        $Query="SELECT cl_sesion FROM k_app_contrato WHERE ds_cadena='$clave' ";
        $row=RecuperaValor($Query);
        $cl_sesion_=$row['cl_sesion'];


        $Query="SELECT fl_sesion FROM c_sesion WHERE cl_sesion='$cl_sesion_' ";
        $row=RecuperaValor($Query);
        $fl_sesion=$row['fl_sesion'];

    }
  }
  else
  {
    $clave = RecibeParametroHTML('clave');
    $fl_sesion=RecibeParametroHTML('fl_sesion');
    $opc_pago = RecibeParametroHTML('opc_pago');
    $conf1 = RecibeParametroHTML('conf1');
    $conf1_err = RecibeParametroHTML('conf1_err');
    $conf2 = RecibeParametroHTML('conf2');
    $conf2_err = RecibeParametroHTML('conf2_err');
    $conf3 = RecibeParametroHTML('conf3');
    $conf3_err = RecibeParametroHTML('conf3_err');
    $conf4 = RecibeParametroHTML('conf4');
    $conf4_err = RecibeParametroHTML('conf4_err');
    $ds_firma = RecibeParametroHTML('ds_firma');
    $ds_firma_err = RecibeParametroHTML('ds_firma_err');
    $ds_firma_rep_legal = RecibeParametroHTML('ds_firma_rep_legal');
    $ds_firma_rep_legal_err = RecibeParametroHTML('ds_firma_rep_legal_err');
    if($conf1_err==3 || $conf2_err==3 || $conf3_err==3 || $conf4_err==3 || $ds_firma_err==3 || $ds_firma_rep_legal_err==3)
      $cod_error = 223;
    else
      $cod_error = $ds_firma_err;
    $cl_metodo_pago = RecibeParametroNumerico('cl_metodo_pago');
    $ds_metodo_otro = RecibeParametroHTML('ds_metodo_otro');
    $no_contrato = RecibeParametroNumerico('no_contrato');
    # En el primer contrato muestra error si no eligio un metodo de pago
    if($no_contrato==1){
      // if(empty($cl_metodo_pago) || ($cl_metodo_pago==5 && empty($ds_metodo_otro)))
        // $method_err = 218;
      $method_err = RecibeParametroNumerico('method_err');
    }
  }

  $dias_vigencia = ObtenConfiguracion(57);
  $fecha_ori = substr($clave, 0, 8);
  $fecha_max = date("Ymd",strtotime("$fecha_ori + $dias_vigencia days"));
  $no_contrato = substr($clave, 8, 1);
  //$cl_validacion = substr($clave, 9, 10);//MJD no servia se manda el codigo orignal enviado
  //$fl_sesion = substr($clave, 19, strlen($clave)-19);
  $cl_validacion=$clave;



  # Recupera datos de la sesion
  $Query  = "SELECT cl_sesion, fg_app_1,fl_pais_campus ";
  $Query .= "FROM c_sesion ";
  $Query .= "WHERE fl_sesion=$fl_sesion";
  $row = RecuperaValor($Query);
  $cl_sesion = $row[0];
  $fl_pais_campus=$row['fl_pais_campus'];

  # Recupera datos del aplicante: forma 1
  $Query  = "SELECT ";
  $Query .= ConsultaFechaBD('fe_birth', FMT_FECHA)." fe_birth, fl_template, a.fl_programa ";
  $Query .= "FROM k_ses_app_frm_1 a, c_programa b ";
  $Query .= "WHERE a.fl_programa=b.fl_programa ";
  $Query .= "AND cl_sesion='$cl_sesion'";
  $row = RecuperaValor($Query);
  $fe_birth = str_texto($row[0]);
  $fl_template = html_entity_decode($row[1]);
  $fl_programa = $row[2];
  $edad_legal = ObtenConfiguracion(58);
  $fecha_minima = date("Ymd",strtotime("$fe_birth + $edad_legal years"));

  if($fecha_minima <= date("Ymd")){
    $legal = true;
  }else{
    $legal = false;
  }

  #Recupera datos adicionales a la forma 1 y del contrato del aplicante
  $Query  = "SELECT ds_cadena, ds_firma_alumno, mn_a_due, mn_a_paid, mn_b_due, mn_b_paid, mn_c_due, mn_c_paid, mn_d_due, mn_d_paid,tax_mn_cost ";
  $Query .= "FROM k_app_contrato ";
  $Query .= "WHERE cl_sesion='$cl_sesion' ";
  $Query .= "AND no_contrato=$no_contrato ";
  $row = RecuperaValor($Query);
  $cve_validacion = $row[0];
  $ds_firma_alumno = $row[1];
  $amount_due_a = $row[2];
  $amount_paid_a = $row[3];
  $amount_due_b = $row[4];
  $amount_paid_b = $row[5];
  $amount_due_c = $row[6];
  $amount_paid_c = $row[7];
  $amount_due_d = $row[8];
$amount_paid_d = $row[9];
$tax_mn_cost = $row['tax_mn_cost'];

$tax_mn_cost_x_invoice_a = 0;
$tax_mn_cost_x_invoice_b = 0;
$tax_mn_cost_x_invoice_c = 0;

if ($tax_mn_cost>0) {
    $tax_mn_cost_x_invoice_a = $tax_mn_cost;
    $tax_mn_cost_x_invoice_b = $tax_mn_cost / 2;
    $tax_mn_cost_x_invoice_b_paid = $tax_mn_cost;
    $tax_mn_cost_x_invoice_c = $tax_mn_cost / 4;
    $tax_mn_cost_x_invoice_c_paid = $tax_mn_cost;
}


# Recupera datos de pagos del curso
  $Query  = "SELECT no_a_payments, ds_a_freq, no_b_payments, ds_b_freq, no_c_payments, ds_c_freq, no_d_payments, ds_d_freq ";
  if($fl_pais_campus==226){
      $Query .= "FROM k_programa_costos_pais ";
  }else{
      $Query .= "FROM k_programa_costos ";
  }
  $Query .= "WHERE fl_programa = $fl_programa";
  if($fl_pais_campus==226){
      $Query .= " AND fl_pais = $fl_pais_campus ";

  }

  $row = RecuperaValor($Query);
  $no_a_payments = $row[0];
  $ds_a_freq = $row[1];
  $no_b_payments = $row[2];
  $ds_b_freq = $row[3];
  $no_c_payments = $row[4];
  $ds_c_freq = $row[5];
  $no_d_payments = $row[6];
  $ds_d_freq = $row[7];

  # Inicia cuerpo de la pagina
  echo "
<html xmlns='http://www.w3.org/1999/xhtml' xml:lang='es'>
<head>
<title>Vanas Contract Signing</title>
<meta http-equiv='cache-control' content='max-age=0'>
<meta http-equiv='cache-control' content='no-cache'>
<meta http-equiv='expires' content='0'>
<meta http-equiv='pragma' content='no-cache'>
<link type='text/css' href='".PATH_CSS."/vanas.css' rel='stylesheet' />
<script src='https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js'></script>
</head>
<body class='css_fondo'>
<center>
  <table border='".D_BORDES."' width='760' cellspacing='0' cellpadding='0'>
    <tr>
      <td align='left'>";

  if($fg_error)
    echo "
        &nbsp;
      </td>
    </tr>
    <tr>
      <td height='40' align='center' class='css_default' style='font-weight:bold; color:red'>
        ".html_entity_decode(ObtenMensaje($cod_error));

  $header = "
        <table border='".D_BORDES."' width='100%' cellPadding='0' cellSpacing='0' class='css_default'>
          <tr>
            <td align='center'>
              <img src='".SP_IMAGES."/login.jpg' border='0'>
              <br>
              <h1>Vancouver Animation School</h1>
            </td>
          </tr>
          <tr>
            <td align='center'>
              <p class='css_default'>";
  $footer  = "
                <br><br><br>
              </p>
            </td>
          </tr>";
  if($fg_exito || (!empty($ds_firma_alumno) && $clave==$cve_validacion))
    $footer .= "
          <tr>
            <td align='center'>
              <a href='".PATH_ADM."/modules/reports/documents_rpt.php?c=$fl_sesion&con=$no_contrato'>
                <img src='".SP_IMAGES."/contract_logo.jpg' border='0'></a><br>
              <a href='".PATH_ADM."/modules/reports/documents_rpt.php?c=$fl_sesion&con=$no_contrato'><h3>".ObtenEtiqueta(608)."</h3></a>
            </td>
          </tr>";
  $footer .= "
          <tr>
            <td align='center'>
              <a href='".INICIO_W."'>Go to Vanas website</a>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</center>
</body>
</html>";

  if($fg_exito)
  {
    echo $header.html_entity_decode(ObtenMensaje(224)).$footer;
    exit;
  }
  if(empty($cl_sesion) || $clave!=$cve_validacion)
  {
    echo $header.html_entity_decode(ObtenMensaje(220)).$footer;
    exit;
  }
  if(!empty($ds_firma_alumno) && $clave==$cve_validacion)
  {
    echo $header.html_entity_decode(ObtenMensaje(221)).$footer;
    exit;
  }
  if($clave==$cve_validacion && $fecha_max < date("Ymd"))
  {
    echo $header.html_entity_decode(ObtenMensaje(222)).$footer;
    exit;
  }

  if($clave==$cve_validacion && empty($ds_firma_alumno) && $fecha_max >= date("Ymd"))
  {

      if($fl_pais_campus==226){
          $ds_encabezado = genera_documento($fl_sesion, 1, False, True, 201);
          $ds_cuerpo = genera_documento($fl_sesion, 2, False, True,201);
          $ds_pie = genera_documento($fl_sesion, 3, False, True, 201);
      }else{

          $ds_encabezado = genera_documento($fl_sesion, 1, False, True, $no_contrato);
          $ds_cuerpo = genera_documento($fl_sesion, 2, False, True, $no_contrato);
          $ds_pie = genera_documento($fl_sesion, 3, False, True, $no_contrato);
      }



    echo "
        $ds_encabezado $ds_cuerpo $ds_pie
      </td>
    </tr>
  </table>
  <form name='datos' method='post' action='contract_iu.php'>
  <table border='".D_BORDES."' width='760' cellspacing='0' cellpadding='0'>
    <tr>
      <td colspan='2' height='40' align='center' class='css_default'>
        &nbsp;
      </td>
    </tr>
    <tr>
      <td colspan='2' align='center' class='css_default' style='font-weight:bold; border-top:2px solid black; border-bottom:2px solid;'>
        <br>".ObtenEtiqueta(607)."
      </td>
    </tr>";
    if($fg_error  AND !empty($method_err)){

      echo "
      <tr>
      <td colspan='2' align='center' class='css_default' style='font-weight:bold; color:red'>
        ".html_entity_decode(ObtenMensaje($method_err))."
      </td>
      </tr> ";
    }
    # La seleccion de metodo de pago se mostrar en el primer contrato
    if($no_contrato==1){
      echo "
      <!--- Metodo de pagos --->
      <tr>
        <td colspan='2' align='center' class='css_default' style='font-weight:bold;'>".ObtenEtiqueta(483)."</td>
      </tr>
      <tr>
        <td colspan='2' align='center' class='css_default' style='font-weight:bold;'>
          <table>
            <tr>
            <script language='javascript'>
            function otro(cl_metodo){
              if(cl_metodo==5)
                $('#div_metodo_otro').show();
              else
                $('#div_metodo_otro').hide();
            }
            </script>";
            $rs = EjecutaQuery("SELECT cl_metodo_pago, ds_metodo_pago FROM k_methods_payments WHERE cl_metodo_pago <> 4");
            for($k=0;$row=RecuperaRegistro($rs);$k++){
              $cl_metodo_pago_bd = $row[0];
              $ds_metodo_pago = $row[1];
              echo "<td><input type='radio' id='cl_metodo_pago' name='cl_metodo_pago' onclick='javascript:otro($cl_metodo_pago_bd);' value='$cl_metodo_pago_bd'";
              if($cl_metodo_pago_bd==$cl_metodo_pago)
                echo "checked";
              echo ">".$ds_metodo_pago."</td>";
            }
            # Mostrar el campo de otro
            if($cl_metodo_pago == 5)
              $fg_other = 'inline';
            else
              $fg_other = 'none';
      echo "
            </tr>
          </table>
        </td>
      </tr>
      <tr>
      <td colspan='2' style='text-align:center;'>
          <div id='div_metodo_otro' style='display:$fg_other;'>* Other <input type='text' id='ds_metodo_otro' name='ds_metodo_otro' value='$ds_metodo_otro'></div>
        </td>
      </tr>";
    }
    echo "
    <tr>";
    if($fg_error)
      echo "
      <td colspan='2' height='40' align='center' class='css_default' style='font-weight:bold; color:red'>
        ".html_entity_decode(ObtenMensaje($cod_error));
    else
      echo "
      <td colspan='2' height='40' align='center' class='css_default'>";
    echo "
        &nbsp;
      </td>
    </tr>";
    if($no_contrato==1)
    {
      echo "
    <tr>
      <td colspan='2' align='center' class='css_default'>
        <table class='css_default' border='0' cellpadding='3' cellspacing='0' width='80%'>
          <tr style='background-color:#E6E1DE'>
            <td colspan='5' align='center' style='font-weight:bold;' >".ObtenEtiqueta(590)."</td>
          </tr>
          <tr style='background-color:#E6E1DE' align='center'>
            <td width='20%'>".ObtenEtiqueta(591)."</td>
            <td width='20%'>".ObtenEtiqueta(592)."</td>
            <td width='20%'>".ObtenEtiqueta(593)."</td>
            <td width='20%'>".ObtenEtiqueta(595)."</td>
            <td width='20%'>".ObtenEtiqueta(596)."</td>
          </tr>
          <tr>
            <td align='center' width='20%'>
              <input id='opc_pago' name='opc_pago' value='1' type='radio' onclick='inf_contract(1);' ";
      if($opc_pago == '1')
        echo "
                checked";
      echo "
                > A
            </td>
            <td align='center'>$no_a_payments</td>
            <td align='center'>$ds_a_freq</td>
            <td align='right'>$ ".($amount_due_a + $tax_mn_cost_x_invoice_a)."</td>
            <td align='right'>$ ".($amount_paid_a + $tax_mn_cost_x_invoice_a)."</td>
          </tr>";
      if(!empty($no_b_payments))
      {
        echo "
          <tr style='background-color:#E6E1DE'>
            <td align='center' width='20%'>
              <input id='opc_pago' name='opc_pago' value='2' type='radio' onclick='inf_contract(2);' ";
        if($opc_pago == '2')
          echo "
                checked";
        echo "
                > B
            </td>
            <td align='center'>$no_b_payments</td>
            <td align='center'>$ds_b_freq</td>
            <td align='right'>$ ".($amount_due_b + $tax_mn_cost_x_invoice_b)."</td>
            <td align='right'>$ ".($amount_paid_b + $tax_mn_cost_x_invoice_b_paid)."</td>
          </tr>";
      }
      if(!empty($no_c_payments))
      {
        echo "
          <tr>
            <td align='center' width='20%'>
              <input id='opc_pago' name='opc_pago' value='3' type='radio' onclick='inf_contract(3);' ";
        if($opc_pago == '3')
          echo "
                checked";
        echo "
                > C
            </td>
            <td align='center'>$no_c_payments</td>
            <td align='center'>$ds_c_freq</td>
            <td align='right'>$ ".($amount_due_c + $tax_mn_cost_x_invoice_b)."</td>
            <td align='right'>$ ".($amount_paid_c + $tax_mn_cost_x_invoice_c_paid)."</td>
          </tr>";
      }
      if(!empty($no_d_payments))
      {
        echo "
          <tr style='background-color:#E6E1DE'>
            <td align='center' width='20%'>
              <input id='opc_pago' name='opc_pago' value='4' type='radio' onclick='inf_contract(4);' ";
        if($opc_pago == '4')
          echo "
                checked";
        echo "
                > D
            </td>
            <td align='center'>$no_d_payments</td>
            <td align='center'>$ds_d_freq</td>
            <td align='right'>$ $amount_due_d</td>
            <td align='right'>$ $amount_paid_d</td>
          </tr>";
      }
      echo "
        </table>
      </td>
    </tr>";
    }
    echo "
    <tr>
      <td colspan='2' align='left' class='css_default'>
        <br><input type='checkbox' id='conf1' name='conf1' value='1' ";
    if($conf1 == '1')
      echo "checked";
    echo "
            >".ObtenEtiqueta(602)."
      </td>
    </tr>
    <tr>
      <td colspan='2' align='left' class='css_default'>
        <input type='checkbox' id='conf2' name='conf2' value='1' ";
    if($conf2 == '1')
      echo "checked";
    echo "
            >".html_entity_decode(ObtenEtiqueta(603))."
      </td>
    </tr>
    <tr>
      <td colspan='2' align='left' class='css_default'>
        <input type='checkbox' id='conf3' name='conf3' value='1' ";
    if($conf3 == '1')
      echo "checked";
    echo "
            >".ObtenEtiqueta(604)."
      </td>
    </tr>
    <tr>
      <td colspan='2' align='left' class='css_default'>
        <input type='checkbox' id='conf4' name='conf4' value='1' ";
    if($conf4 == '1')
      echo "checked";


    # Valida que la firma del alumno coincida con el nombre
    $Query  = "SELECT ds_fname, ds_mname, ds_lname ";
    $Query .= "FROM k_ses_app_frm_1 ";
    $Query .= "WHERE cl_sesion='$cl_sesion' ";
    $row = RecuperaValor($Query);
    if(!empty($row[1]))
        $ds_firma = strtoupper(str_texto(trim(html_entity_decode($row[0]))).' '.str_texto(trim(html_entity_decode($row[1]))).' '.str_texto(trim(html_entity_decode($row[2]))));
    else
        $ds_firma = strtoupper(str_texto(trim(html_entity_decode($row[0]))).' '.str_texto(trim(html_entity_decode($row[2]))));

    $class_danger="";
    if(!$legal){
        #buscamos quein lleno en la aplicacion de responsable.
        $Query="SELECT ds_fname_r,ds_lname_r FROM k_presponsable WHERE cl_sesion='$cl_sesion' ";
        $row = RecuperaValor($Query);
        if($row[0])
        $ds_firma_rep_legal = strtoupper(str_texto(trim($row[0])).' '.str_texto(trim($row[1])));
    }
    if(empty($ds_firma_rep_legal)){
        $ds_firma_rep_legal=null;
        $class_danger="color:#f00;";
    }



    echo "
            >".ObtenEtiqueta(605)."
      </td>
    </tr>
    <tr>
      <td align='right' class='css_default'>
        <br>".ObtenEtiqueta(600)."
      </td>
      <td align='center' class='css_default'>
        <br><input type='text' id='ds_firma' name='ds_firma' value='".str_uso_normal($ds_firma)."' maxlength='150' size='50'>
            <input type='hidden' id='cl_sesion' name='cl_sesion' value=$cl_sesion>
            <input type='hidden' id='no_contrato' name='no_contrato' value=$no_contrato>
            <input type='hidden' id='clave' name='clave' value=$clave>
            <input type='hidden' id='fl_programa' name='fl_programa' value=$fl_programa>
      </td>
    </tr>";
    # Por las nuevas regulaciones comentamos el tipo de template
    # Usuario de menor edad tiene que firmar su tutor
    if(!$legal /*&& $fl_template == 2*/)
    echo "
    <tr>
      <td align='right' class='css_default' style='$class_danger'>
        <br>".ObtenEtiqueta(606)."
      </td>
      <td align='center' class='css_default'>
        <br><input type='text' id='ds_firma_rep_legal' name='ds_firma_rep_legal' value='$ds_firma_rep_legal' maxlength='150' size='50'>
            <input type='hidden' id='rep_legal' name='rep_legal' value='1'>
      </td>
    </tr>";
    echo "
    <tr>
      <td colspan='2' align='center' class='css_default'>
        <br>
        <input type='submit'  id='buttons' value='".ObtenEtiqueta(601)."'>
      </td>
    </tr>
  </table>
  </form>";
  }

  echo "
</center>
<script>



function inf_contract(opcion){
  var no_contrato = '".$no_contrato."';
  var fl_sesion = '".$fl_sesion."';
  var fl_programa = '".$fl_programa."';
  $.ajax({
    type: 'POST',
    url : 'contract_cal.php',
    data: 'clave=".$fl_sesion."&no_contrato='+no_contrato+'&fg_opcion='+opcion+'&fl_programa='+fl_programa,
    async: false,
    success: function(html) {
      // alert(html);
      //$('#pagos').html(html);
    }
  });
}

/*Este es un valor por default*/
var valor =  $('input[name=opc_pago]:checked').prop('value');
var no_contrato = '".$no_contrato."';
if(no_contrato==1)
  inf_contract(valor);
</script>
</body>
</html>";
?>
