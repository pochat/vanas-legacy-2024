<?php
  
  # Libreria de funciones
  require '../../lib/general.inc.php';
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  ValidaSesion( );
  
  # Recibe la clave
  $clave = RecibeParametroNumerico('clave'); // fl_alumno o fl_sesion
   
  # Determina si es alta o modificacion
  if(!empty($clave))
    $permiso = PERMISO_MODIFICACION;
  else {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermiso(FUNC_PAGOS, PERMISO_DETALLE)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Constante
  define('ERR_REQUERIDO_PAGO', 17);
  
  # Recibe la clave
  $fg_error = 0;
  $fl_term_pago = RecibeParametroNumerico('fl_term_pago'); //fl_term_pago
  $mn_pago = RecibeParametroFlotante('mn_pago'); //pago
  $no_pago = RecibeParametroNumerico('no_pago');
  $cl_metodo_pago = RecibeParametroNumerico('cl_metodo_pago');
  $ds_cheque = RecibeParametroHTML('ds_cheque');
  $ds_comentario = RecibeParametroHTML('ds_comentario');
  $mn_pagado_app = RecibeParametroHTML('mn_pagado_app');
  $fg_app_frm = RecibeParametroNumerico('fg_app_frm');
  $fe_fecha = RecibeParametroFecha('fe_fecha');
  $fg_realizar = RecibeParametroBinario('fg_realizar');
  $fg_pago = RecibeParametroBinario('fg_pago');
  $cl_sesion = RecibeParametroHTML('cl_sesion');
  $mn_late_fee = RecibeParametroFlotante('mn_late_fee'); 
  $fl_programa = RecibeParametroNumerico('fl_programa');
  $fg_opcion_pago = RecibeParametroNumerico('fg_opcion_pago');
  $tot_pagos = RecibeParametroNumerico('tot_pagos');
  
  # Redirecciona al listado si ya fue fue el ultimo pago
  if(empty($fl_term_pago)){
    # Redirige al listado
    header("Location: ".ObtenProgramaBase( ));
  }
  
  # Valida campos obligatorios
  if(empty($fe_fecha))
    $fe_fecha_err = ERR_REQUERIDO;
  
  # Verifica que el formato de la fecha sea valido
  if(!empty($fe_fecha) AND !ValidaFecha($fe_fecha))
    $fe_fecha_err = ERR_FORMATO_FECHA;
    
  #Valida que sea confirmado el pago
  if($fg_realizar==0)
    $fg_realizar_err = ERR_REQUERIDO_PAGO;
  if(empty($cl_metodo_pago))
    $cl_metodo_pago_err = ERR_REQUERIDO;
  
  # Regresa a la forma con error
  $fg_error = $fe_fecha_err || $fg_realizar_err || $cl_metodo_pago_err;
  if($fg_error) {
    echo "<html><body><form name='datos' method='post' action='".ObtenProgramaNombre(PGM_FORM)."'>\n";
    Forma_CampoOculto('clave', $clave);
    Forma_CampoOculto('fg_error', $fg_error);
    Forma_CampoOculto('fe_fecha', $fe_fecha);
    Forma_CampoOculto('fe_fecha_err', $fe_fecha_err);
    Forma_CampoOculto('fg_app_frm', $fg_app_frm);
    Forma_CampoOculto('cl_metodo_pago', $cl_metodo_pago);
    Forma_CampoOculto('cl_metodo_pago_err', $cl_metodo_pago_err);
    Forma_CampoOculto('ds_cheque', $ds_cheque);
    Forma_CampoOculto('ds_comentario', $ds_comentario);
    Forma_CampoOculto('fg_realizar', $fg_realizar);
    Forma_CampoOculto('fg_realizar_err', $fg_realizar_err);
    Forma_CampoOculto('mn_late_fee', $mn_late_fee);
    echo "\n</form>
      <script>
        document.datos.submit();
      </script></body></html>";
    exit;
  }
     
  # Solo si se pidio realizar el pago
  if(!empty($fg_realizar)) {
    # Prepara fechas en formato para insertar
    if(!empty($fe_fecha))
      $fe_fecha = "'".ValidaFecha($fe_fecha)." ".date('H:i:s')."'";
    else
      $fe_fecha = "NULL";
      
    # Si tiene late fee se aumenta al mn_pago
    if(!empty($mn_late_fee))
      $mn_pago = $mn_pago + $mn_late_fee;

    # Inserta o actualiza el registro
    if(!empty($fg_pago)) {
      if(empty($fg_app_frm)){
        $Query  = "INSERT INTO k_alumno_pago (fl_alumno, fl_term_pago, cl_metodo_pago, fe_pago, mn_pagado, ds_comentario, ds_cheque, mn_late_fee) ";
        $Query .= "VALUES ($clave, $fl_term_pago, $cl_metodo_pago, $fe_fecha, $mn_pago, '$ds_comentario', '$ds_cheque', $mn_late_fee) "; 
      }
      else{
        $Query  = "INSERT INTO k_ses_pago (cl_sesion, fl_term_pago, cl_metodo_pago, fe_pago, mn_pagado, ds_comentario, ds_cheque, mn_late_fee) ";
        $Query .= "VALUES ('$cl_sesion', $fl_term_pago, $cl_metodo_pago, $fe_fecha, $mn_pago, '$ds_comentario', '$ds_cheque', $mn_late_fee) "; 
      }
    }
    else {
      $Query  = "UPDATE c_sesion SET fg_pago='1', cl_metodo_pago=$cl_metodo_pago, fe_pago=$fe_fecha, mn_pagado=$mn_pagado_app, ds_comentario='$ds_comentario', ds_cheque='$ds_cheque' ";
      $Query .= "WHERE cl_sesion='$cl_sesion'";
    }
    EjecutaQuery($Query);
  }
  
  # Redirige al listado
  header("Location: ".ObtenProgramaBase( ));
  
?>