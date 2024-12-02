<?php
  # Libreria de funciones
  require("../lib/sp_general.inc.php");
  require("../lib/sp_session.inc.php");
  // require("../lib/sp_forms.inc.php");
  require("lib/app_forms.inc.php");
  require("app_form.inc.php");
  
  $ds_code = RecibeParametroHTML('code');
  $fl_programa=RecibeParametroNumerico('fl_programa');
  $mn_tuiton=RecibeParametroHTML('mn_tuiton');
  $mn_app_fee=RecibeParametroHTML('mn_app_fee');
  $can_tax=RecibeParametroFlotante('can_tax');
  
  

  #vERIFICAMO QUE Exista el codigo valido
  $Query="SELECT ds_code, ds_descuento ,a.fe_start,a.fe_end
		FROM c_cupones a, 
		k_cupones_course b 
		WHERE a.fl_cupon=b.fl_cupon AND fl_programa='$fl_programa' AND fg_activo='1' 
		AND CURDATE() BETWEEN fe_start AND fe_end  AND ds_code='$ds_code' "; 
  $row=RecuperaValor($Query);
  $ds_code_=$row[0];
  $ds_descuento=$row[1];
  
  
  #Identificamos si es precio.
  if (strpos($ds_descuento, '$') !== false) {
      

      #Dejamos solo la cantidad
      $ds_descuento = str_replace("$", "", $ds_descuento);
      $mn_total_pagar=($mn_app_fee+$mn_tuiton)-$ds_descuento;

  }

  #Identificamos si es porcentaje.
  if (strpos($ds_descuento, '%') !== false) {

      #Dejamos solo la cantidad
	  $ds_descuento = str_replace("%", "", $ds_descuento);
      
      $mn_total=$mn_app_fee+$mn_tuiton;
	  $mn_total_pagar=($mn_total * $ds_descuento)/100;

  }

  #Si tiene tax obtenemos la cantidad del tax
  if($can_tax){
	  $mn_tax=number_format($mn_total_pagar*$can_tax,2);
	  
	  
  }else{
	  $mn_tax=0;
  }
  
  
  
  
  if($ds_code_==$ds_code){
      
     $result['success'] = 1;
     $result['mn_total_pagar']=$mn_total_pagar;
	 $result['mn_tax']=$mn_tax;
  }else{
     $result['success'] = 0;

  }
  
  
  
  echo json_encode((Object) $result);
  
  
?>