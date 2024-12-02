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
  <div class='row form-group'>
    <div class='col col-xs-12 col-sm-12 col-md-12 col-lg-12'>";        
      # Identificamos si es un app o un pago
    if(strpos($type, "APP") !== False)
      echo "<header class='text-align-center txt-color-white' style='background-color:#0092cd;'><strong><h3>App fee
        <div class='pull-right margin-right-5'><a href='javascript:cerrar();' style='color:white;' title='".ObtenEtiqueta(74)."'><i class='fa fa-close'></i></a></div></h3></strong></header>";
    else        
      echo "<header class='text-align-center txt-color-white' style='background-color:#0092cd;'><strong><h3>".ObtenEtiqueta(481).": $no_pago
      <div class='pull-right margin-right-5'><a  href='javascript:cerrar();' style='color:white;' title='".ObtenEtiqueta(74)."'><i class='fa fa-close'></i></a></div></h3></strong></header>";
  echo "
    </div>
      <div class='col col-xs-12 col-sm-12 col-md-12 col-lg-12'>
        <div class='row form-group smart-form'>
        <label class='col col-xs-6 col-sm-6 col-md-6 col-lg-6 text-align-left'><strong>".ObtenEtiqueta(481).":</strong></label>
        <div class='col col-xs-6 col-sm-6 col-md-6 col-lg-6'>".$mn_pagado."</div>";
        Forma_CampoOculto('mn_pagado', $mn_pagado);
  echo "</div>
      </div>
      <div class='col col-xs-12 col-sm-12 col-md-12 col-lg-12'>";
      # Dependiendo del tipo vamos a mostrar los campos
      # Modificar fecha de pago y fecha de pago app fee
      if($type == "FAPP" || $type == "F"){
        Forma_CampoTexto(ObtenEtiqueta(374).' '.ETQ_FMT_FECHA, True, 'fe_pago1', $fe_pago1, 10, 10, '', False, '', True, '', '', 'smart-form form-group', 'left', 'col col-xs-12 col-sm-12 col-md-12 col-lg-12', 'col col-xs-12 col-sm-12 col-md-12 col-lg-12');        
        Forma_Calendario('fe_pago1');
        Forma_CampoOculto('fe_hr1',$fe_hr1);
      }else{ // Mostrar la fecha de pago        
        echo "
        <div class='row form-group smart-form'>
        <label class='col col-xs-6 col-sm-6 col-md-6 col-lg-6 text-align-left'><strong>".ObtenEtiqueta(374).":</strong></label>
        <div class='col col-xs-6 col-sm-6 col-md-6 col-lg-6'>".$fe_pago."</div>
        </div>";
      }
      echo "
      </div>
      <div class='col col-xs-12 col-sm-12 col-md-12 col-lg-12'>";
      # Refund
      if($type=="R")
        Forma_CampoTexto('Amount Refund',True,'mn_refund', $mn_refund, 10,16,$mn_refund_err);
      # Cambiar metodo de pago tanto del app fee o un pago normal
      if($type=="M" || $type=="MAPP"){      
        $p_opc = array(ObtenEtiqueta(488), ObtenEtiqueta(488).' Manual','Cheque','Credit Card','Wire Transfer/Deposit','Cash');
        Forma_CampoSelect(ObtenEtiqueta(483),True, 'metodopago', $p_opc, array(1,2,3,4,5,6), $cl_metodo_pago,'',True, '', 'left', 'col col-xs-12 col-sm-12 col-md-12 col-lg-12', 'col col-xs-12 col-sm-12 col-md-12 col-lg-12');
      }
      # Cambiar el comentario del app fee o el pago normal
      if($type=="C" || $type=="CAPP")
        Btstrp_Forma_CampoTextArea("<strong>".ObtenEtiqueta(72)."</strong>", False, 'ds_comentario1', $ds_comentario1, 100, 3);      
  echo "
      </div>
      <div class='row col col-sm-12 text-align-center padding-10'>
        <input class='btn btn-primary' type='submit' value='".ObtenEtiqueta(13)."' Onclick='refund();'/>
        <input class='btn btn-primary' type='submit' value='".ObtenEtiqueta(14)."' Onclick='cerrar()'/>
      </div>
  </div>";
  Forma_CampoOculto('cl_sesion',$cl_sesion);
  Forma_CampoOculto('fg_inscrito',$fg_inscrito);
  Forma_CampoOculto('pago_borrar',$pago_borrar);
  Forma_CampoOculto('type',$type);
?>
<script type="text/javascript">
  // DO NOT REMOVE : GLOBAL FUNCTIONS!
  $(document).ready(function() {
    pageSetUp();
  });
 </script> 