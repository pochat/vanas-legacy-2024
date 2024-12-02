<?php
  
  # Libreria de funciones
  require '../../lib/general.inc.php';
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  ValidaSesion( );
  
  # Recibe parametros
  $pago = RecibeParametroNumerico('borrar');
  $clave = RecibeParametroHTML('clave');
  $fg_app_frm = RecibeParametroNumerico('fg_app_frm');
  $origen = RecibeParametroHTML('origen');
  
  # Determina si es alta o modificacion
  if(!empty($clave))
    $permiso = PERMISO_MODIFICACION;
  else {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermiso(FUNC_APP_FRM, $permiso)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  if(!empty($pago)) {
    if(empty($fg_app_frm)){
      $Query = "SELECT cl_metodo_pago FROM k_alumno_pago WHERE fl_alumno_pago=$pago ";
      $row= RecuperaValor($Query);
      $cl_metodo_pago = $row[0];
      //if($cl_metodo_pago != 1) //modificacion para que puda borrar los pagos de metodo paypal
      $Query = "DELETE FROM k_alumno_pago WHERE fl_alumno_pago=$pago";
      EjecutaQuery($Query);
      // Borra todos los pagos detalle
      $Query_det = "DELETE FROM k_alumno_pago_det WHERE fl_alumno_pago=$pago";
      EjecutaQuery($Query_det);
    }
    else  {
      $Query = "SELECT cl_metodo_pago FROM k_ses_pago WHERE fl_ses_pago=$pago ";
      $row= RecuperaValor($Query);
      $cl_metodo_pago = $row[0];
      if($cl_metodo_pago != 1){
        $Query = "DELETE FROM k_ses_pago WHERE fl_ses_pago=$pago";
        EjecutaQuery($Query);
      }
    }
  }
  if(!empty($fg_app_frm))
    $a ='a-';
  else
    $a = '';
  
  if(!empty($origen))
    $url = $origen."?clave=$clave&destino='borrar_payment.php'";
  else
    $url = "payments_frm.php?clave=".$a."$clave&destino='borrar_payment.php'";
  
  # Redirige al listado
  header("Location:$url");
  
?>