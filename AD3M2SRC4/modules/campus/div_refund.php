<?php
    
  # Libreria de funciones
  require '../../lib/general.inc.php';

  # Verifica que exista una sesion valida en el cookie y la resetea
  ValidaSesion( );
  # Recibe parametros
  $mn_pagado = $_REQUEST['mn_pagado'];
  $mn_refund = $_REQUEST['mn_refund'];
  $pago_borrar = $_REQUEST['pago_borrar'];
  $fg_inscrito = $_REQUEST['fg_inscrito'];
  $cl_sesion = $_REQUEST['cl_sesion'];
  $cl_metodo_pago = $_REQUEST['cl_metodo_pago'];
  $type = $_REQUEST['type'];
  $fe_pago1 = $_REQUEST['fe_pago1'];
  $fe_pago1 = ValidaFecha($fe_pago1);
  $fe_hr1 = $_REQUEST['fe_hr1'];
  $ds_comentario = $_REQUEST['ds_comentario1'];
  
  # Valida los campos que no esten en blanco y los correos que sean validos
  if($type=='R'){
    if(empty($mn_refund)){
      $mn_refund_err = ERR_REQUERIDO;
      $mn_refund_err2 ="".ObtenMensaje(mn_refund_err)."Amount Refund";
    }
    if($mn_refund > $mn_pagado)
      $mn_refund_err2 = "Amount Refund is great";
  }
  if($type=='F' || $type=='FAPP'){
    if(empty($fe_pago1))
      $mn_refund_err2 = "Insert date";
  }

  # Dependiendo del typo si es un refund o un cambio de metodo de pago
  if($type=='R')
    $update = " fg_refund='1', mn_refund=$mn_refund, fe_refund=NOW() ";
  if($type=="M" || $type=="MAPP")
    $update = " cl_metodo_pago=$cl_metodo_pago ";
  if($type=="F" || $type=="FAPP")
    $update = " fe_pago='$fe_pago1 $fe_hr1' ";
  if($type=="C" || $type=="CAPP")
    $update = " ds_comentario='$ds_comentario' ";

  $fg_error = $mn_refund_err || $mn_refund_err2;
  
  if(empty($fg_error)){
    if(strpos($type, "APP") === False){
      if(!empty($fg_inscrito))
        $Query = "UPDATE k_alumno_pago SET $update WHERE fl_alumno_pago=$pago_borrar";
      else
        $Query = "UPDATE k_ses_pago SET $update WHERE fl_ses_pago=$pago_borrar  AND cl_sesion='$cl_sesion' ";
    }
    else
      $Query = "UPDATE c_sesion SET $update WHERE fl_sesion=$pago_borrar";      
    EjecutaQuery($Query);
    echo '1';
  }else
    echo $mn_refund_err2;
    
?>