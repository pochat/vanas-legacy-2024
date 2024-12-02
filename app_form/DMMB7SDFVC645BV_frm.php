<?php
  
  # Libreria de funciones
  require("../lib/sp_general.inc.php");
  require("../lib/sp_session.inc.php");
  require("../lib/sp_forms.inc.php");
  require("app_form.inc.php");
  
  # Recupera sesion del cookie
  $clave = SP_RecuperaSesion( );
  
  # Si no es una sesion valida redirige a la forma inicial
  if(empty($clave)) {
    header("Location: ABSP4MDSFSDF8V_frm.php");
    exit;
  }
  
  # Verificamos si el programa solo de paga app fee o es app fee + costo del programa
  # Verificamos si en el pago del curso se agreagara el tax rate
  $Query  = "SELECT fg_total_programa, b.fl_programa, b.ds_add_number, b.ds_add_street,b.ds_add_city, b.ds_add_state, b.ds_add_zip, ";
  $Query .= "b.ds_add_country, a.fg_tax_rate, a.fl_template ";
  $Query .= "FROM c_programa a, k_ses_app_frm_1 b WHERE a.fl_programa=b.fl_programa AND b.cl_sesion='".$clave."'";
  $row = RecuperaValor($Query);
  $fg_total_programa = $row[0];
  $fl_programa = $row[1];
  $ds_add_number = $row[2];
  $ds_add_street = str_texto($row[3]);
  $address1 = $ds_add_number." ".$ds_add_street; // numero y calle
  $ds_add_city = str_texto($row[4]); //ciudad
  $ds_add_state = $row[5]; // provincia o estado
  $ds_add_zip = $row[6]; //zip
  $ds_add_country = $row[7]; //pais
  $fg_tax_rate = $row[8]; // si necesita que se cobre impuesto 
  $fl_template = $row[9];

  # Reinicia la sesion
  SP_ActualizaSesion($clave);
  
  # Header
  PresentaHeaderAF( );
  
  # Informacion de paypal
  $urlPaypal = ObtenConfiguracion(61);
  $business = ObtenConfiguracion(62);
  $currency_code = ObtenConfiguracion(82);
  $tax_rate_paypal = "";
  #Obtenemos el total a pagar de k_pp_contrato sumando su mn_app fee mas mn_tuition
  $row = RecuperaValor("SELECT mn_app_fee, mn_tuition FROM k_app_contrato WHERE cl_sesion='".$clave."'");
  # Si fg_total_programa es uno entonces pagara app fee mas monto del programa
  if(!empty($fg_total_programa)){
    $mn_due_pagar = $row[0] + $row[1];
    $item_name = ObtenEtiqueta(697);
    # PC| Indetificacion para Pago Completo
    $custom = "PC|$clave";    
  }
  else{ // Solo paga App fee
    $mn_due_pagar = $row[0];
    $item_name =ObtenEtiqueta(689);
    $custom = $clave;
  } 
  # Si no es de cadana y el programa no requiere tax
  $mn_tax_rate = 0;
  $tax_rate_paypal = "<input type='hidden' name='tax' id='tax' value='$mn_tax_rate'>";
  
  # Los canadienses van a pagar el tax dependiendo de la provincia
  if($ds_add_country==38){
    # Primera condicion es pago completo
    # Segunda condicion es pago del tax del app fee en programas largos    
    if(!empty($ds_add_state) && (!empty($fg_tax_rate) || empty($fg_tax_rate))){
      $row_tax = RecuperaValor("SELECT ds_abreviada,mn_tax FROM k_provincias WHERE fl_provincia='$ds_add_state'");
      $ds_abreviada = $row_tax[0];
      $mn_tax_rate = $row_tax[1];
      $tax_rate_paypal = "<input type='hidden' name='tax' id='tax' value='".$mn_due_pagar*($mn_tax_rate/100)."'>";
    }
  }
  $url_campus = "http://".ObtenConfiguracion(60); 
  # Cuerpo del Home
  echo "
    <table border='".D_BORDES."' width='100%' valign='top' cellspacing='0' cellpadding='0' class='app_form'>
      <tr>
        <td width='20' height='20'>&nbsp;</td>
        <td colspan='2'>&nbsp;</td>
        <td width='20'>&nbsp;</td>
      </tr>
      <tr>
        <td height='30'>&nbsp;</td>
        <td colspan='2'><b>".ObtenEtiqueta(58)."</b></td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td colspan='2' valign='top'>
          <br>".ObtenEtiqueta(324);
          # Se paga solo e app fee si el fg_total_programa es cero si no es app mas costo programa
          if(!empty($fg_total_programa))
            echo "<br>".ObtenEtiqueta(699);
          echo ";
          <br><br>".ObtenEtiqueta(325).":
        </td>
        <td>&nbsp;</td>
      </tr>
      <tr><td colspan='4'>&nbsp;</td></tr>
      <tr>
        <td>&nbsp;</td>
        <td valign='middle' class='row_highlight_1'>
          <ul><li>".ObtenEtiqueta(326)."</li></ul>
        </td>
        <td valign='top' align='center' class='row_highlight_1'>
          <br>
            <a href='javascript: document.datos.submit();' id='paypal_btn'><img src='https://www.paypal.com/en_US/i/btn/btn_paynowCC_LG.gif' border='0' title='PayPal - The safer, easier way to pay online!'></a>
            <form name='datos' action='$urlPaypal'>
              <input type='hidden' name='cmd' value='_xclick'>
              <input type='hidden' name='business' id='business' value='$business'>
              <input type='hidden' name='currency_code' id='currency_code' value='$currency_code'>
              <input type='hidden' name='item_name' id='item_name' value='$item_name'>
              <input type='hidden' name='amount' id='amount' value='$mn_due_pagar'>
              <input type='hidden' name='custom' id='custom' value='$custom'>
              ".$tax_rate_paypal."
              <!--url que regresa una vez que termino el proceso de comunicacion con paypal-->
              <input type='hidden' name='return' id='return' value='".$url_campus."/app_form/ELOA900CS2YC32_frm.php'>
              <!--Envia datos a la url espefificada -->
              <input type='hidden' name='rm' id='rm' value='2'>
              <!--Si cancela el comprador antes de realizar el pago redirige a la url que se ingresa-->
              <input type='hidden' name='cancel_return' id='cancel_return' value='".$url_campus."/app_form/DMMB7SDFVC645BV_frm.php'>
            </form>
          <br>
        </td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td valign='middle' class='row_highlight_2'>
          <ul><li>".ObtenEtiqueta(327)."</li></ul>
        </td>
        <td valign='top' align='center' class='row_highlight_2'>
          <br>
          <form method='post' action='ELOA900CS2YC32_frm.php'>
            <input type='hidden' name='origen' id='origen' value='DMMB7SDFVC645BV_frm.php'>
            <input type='hidden' name='custom' id='custom' value='$clave'>
            <input type='submit' id='buttons' value='".ObtenEtiqueta(330)."'>
          </form>
        </td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td colspan='2' valign='top'>
          <br>
          <br>
		  <br>
          <br>
		  <br>
          <br>
          <form method='link' action='CHDSF776RSDV85_frm.php'>
            <input type='submit' id='buttons' value='".ObtenEtiqueta(40)."'>
          </form>
        </td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td colspan='4' height='20'>&nbsp;</td>
      </tr>
    </table>";
  
  # Footer
  PresentaFooterAF( );
  
?>