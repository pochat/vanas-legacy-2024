<?php
  # La libreria de funciones
  require '../../lib/general.inc.php';
  
  # Recupera el usuario actual
  ValidaSesion( );
  
  # Recibe parametros
  $clave = RecibeParametroNumerico('clave');
  $pago_borrar = RecibeParametroNumerico('pago_borrar');
  $fg_inscrito = RecibeParametroNumerico('fg_inscrito');
  $no_pago = RecibeParametroNumerico('no_pago');
  $type = RecibeParametroHTML('type');
  if(strpos($type, "APP") !== False)
    $clave = $pago_borrar;
  
  $row = RecuperaValor("SELECT cl_sesion, DATE_FORMAT(fe_pago,'%Y-%m-%d'),DATE_FORMAT(fe_pago,'%H:%i:%s'), cl_metodo_pago, ds_comentario FROM c_sesion WHERE fl_sesion=$clave");
  $cl_sesion = $row[0];
  $fe_pagoapp = $row[1];
  $fe_hrapp = $row[2];
  $cl_metodo_pagoapp = $row[3];
  $ds_comentarioapp = str_texto($row[4]);
  # dependiendo si ya esta incrito
  if(!empty($fg_inscrito))
    $row = RecuperaValor("SELECT mn_pagado, cl_metodo_pago, DATE_FORMAT(fe_pago,'%b %d, %Y'), DATE_FORMAT(fe_pago,'%Y-%m-%d'),DATE_FORMAT(fe_pago,'%H:%i:%s'),ds_comentario FROM k_alumno_pago WHERE fl_alumno_pago=$pago_borrar AND fl_alumno=$clave");
  else{
    $row = RecuperaValor("SELECT mn_pagado, cl_metodo_pago, DATE_FORMAT(fe_pago,'%b %d, %Y'), DATE_FORMAT(fe_pago,'%Y-%m-%d'),DATE_FORMAT(fe_pago,'%H:%i:%s'),ds_comentario FROM k_ses_pago WHERE fl_ses_pago=$pago_borrar AND cl_sesion='$cl_sesion'");
  }
  $mn_pagado = $row[0];
  $cl_metodo_pago = $row[1];
  $fe_pago = $row[2];
  $fe_pago1 = $row[3];
  $fe_hr1 = $row[4];
  $ds_comentario1 = str_texto($row[5]);
  if(strpos($type, "APP") !== False){
    # Obtener los correos de vanas y alumno    
    $row1 = RecuperaValor("SELECT mn_app_fee FROM k_app_contrato WHERE cl_sesion='$cl_sesion'");
    $mn_pagado = $row1[0];
    $fe_pago = $fe_pagoapp;
    $fe_pago1=$fe_pagoapp;
    $fe_hr1 = $fe_hrapp;
    $cl_metodo_pago = $cl_metodo_pagoapp;
    $ds_comentario1 = $ds_comentarioapp;
  }
  echo "
  <table border='".D_BORDES."' cellpadding='2px'>";
    # Identificamos si es un app o un pago
    if(strpos($type, "APP") !== False){
      ECHO "
      <tr>
        <td class='css_etq_texto' align='center' valign='top' colspan='2' >App fee</td>
      </tr>";
    }else
      Forma_CampoInfo(ObtenEtiqueta(481),$no_pago);

    Forma_CampoInfo(ObtenEtiqueta(486),$mn_pagado);    
    Forma_CampoOculto('mn_pagado', $mn_pagado);
    
    # Dependiendo del tipo vamos a mostrar los campos
    # Modificar fecha de pago y fecha de pago app fee
    if($type == "FAPP" || $type == "F"){
      Forma_CampoTexto(ObtenEtiqueta(374).' '.ETQ_FMT_FECHA, True, 'fe_pago1', $fe_pago1, 10, 10);
      Forma_Calendario('fe_pago1');
      Forma_CampoOculto('fe_hr1',$fe_hr1);
    }else // Mostrar la fecha de pago
      Forma_CampoInfo(ObtenEtiqueta(374),$fe_pago);
    # Refund
    if($type=="R")
      Forma_CampoTexto('Amount Refund',True,'mn_refund', $mn_refund, 10,16,$mn_refund_err);
    # Cambiar metodo de pago tanto del app fee o un pago normal
    if($type=="M" || $type=="MAPP"){      
      $p_opc = array(ObtenEtiqueta(488), ObtenEtiqueta(488).' Manual','Cheque','Credit Card','Wire Transfer/Deposit','Cash');
      Forma_CampoSelect(ObtenEtiqueta(483),True, 'metodopago', $p_opc, array(1,2,3,4,5,6), $cl_metodo_pago,'',True);
    }
    # Cambiar el comentario del app fee o el pago normal
    if($type=="C" || $type=="CAPP")
      Forma_CampoTextArea(ObtenEtiqueta(72), False, 'ds_comentario1', $ds_comentario1, 30, 2);

  echo "
    <tr>
    <td align='left'><input type='submit' value='".ObtenEtiqueta(347)."' Onclick='refund();'/></td>
    <td align='right'><input type='submit' value='".ObtenEtiqueta(14)."' Onclick='cerrar()'/></td>
    </tr>
  </table>";
  Forma_CampoOculto('cl_sesion',$cl_sesion);
  Forma_CampoOculto('fg_inscrito',$fg_inscrito);
  Forma_CampoOculto('pago_borrar',$pago_borrar);
  Forma_CampoOculto('type',$type);

?>