<?php
  
  # Libreria de funciones
  require '../../lib/general.inc.php';
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  ValidaSesion( );

  # Recibe la clave
  $fg_error = 0;
  $clave = RecibeParametroNumerico('clave');
  
  
  # Determina si es alta o modificacion
  if(!empty($clave))
    $permiso = PERMISO_MODIFICACION;
  else
    $permiso = PERMISO_ALTA;
    
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermiso(FUNC_MAESTROS, $permiso)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }

  # Recibimos los datos de la tabla automatica
  # Datos de la tabla automatica
  $fe_periodo = RecibeParametroHTML('fe_periodo');
	$tot_aut = RecibeParametroNumerico('tot_aut');
  $mn_total_aut = 0;
  for($i=0;$i<$tot_aut;$i++){
    $fl_grupo_aut[$i] = RecibeParametroNumerico('fl_grupo_aut'.$i);
    $mn_tarifa_hr_aut[$i] = RecibeParametroHTML('mn_tarifa_hr_aut'.$i);
    $fl_clase[$i] = RecibeParametroNumerico('fl_clase'.$i);
    $mn_subtotal_aut[$i] = $mn_tarifa_hr_aut[$i] * 1;
    $subtract_class[$i] = RecibeParametroBinario('subtract_class'.$i);
    # Se debe obtener true si para insertar el monto
    if($subtract_class[$i]==1)
      $mn_subtotal_aut[$i] = $mn_subtotal_aut[$i];
    else # Si es falso el monto sera cero
      $mn_subtotal_aut[$i] = 0;
    $mn_total_aut += $mn_subtotal_aut[$i];
  }
  # Datos de la tabla manual
  $tot_manual = RecibeParametroNumerico('tot_manual');
  $mn_total_man = 0;
  for($j=0;$j<$tot_manual;$j++){
    $ds_concepto[$j] = RecibeParametroHTML('ds_concepto'.$j);
    $mn_tarifa_hr[$j] = RecibeParametroHTML('mn_tarifa_hr'.$j);
    $no_horas[$j] = RecibeParametroHTML('no_horas'.$j);
    $mn_subtotal_manual[$j] = ($mn_tarifa_hr[$j]*$no_horas[$j]);
    $fl_maestro_pago_det[$j] = RecibeParametroNumerico('fl_maestro_pago_det'.$j);
    $mn_total_man += $mn_subtotal_manual[$j] ;
  }

  $fl_maestro_pago = RecibeParametroNumerico('fl_maestro_pago');
  /*if($fl_maestro_pago==$clave)
    $fl_maestro_pago = '';*/
  $fg_publicar = RecibeParametroBinario('fg_publicar');
  $fg_pagado = RecibeParametroBinario('fg_pagado');
  $mn_total = $mn_total_aut +$mn_total_man;
  if(!empty($fl_maestro_pago))
    $mn_total = RecibeParametroHTML('mn_total');
  
  # validamos los campos de la tabla manual
  for($j=0;$j<$tot_manual;$j++){
    if(empty($ds_concepto[$j]))
      $ds_concepto_err[$j]=ERR_REQUERIDO;
    if(empty($mn_tarifa_hr[$j]))
      $mn_tarifa_hr_err[$j]=ERR_REQUERIDO;
    if(empty($no_horas[$j]))
      $no_horas_err[$j]=ERR_REQUERIDO;
  }

  # Regresa a la forma con error
  $fg_error = "";
  for($j=0;$j<$tot_manual;$j++){
    $fg_error .= $ds_concepto_err[$j] || $mn_tarifa_hr_err[$j] || $no_horas_err[$j];
  }

  if($fg_error) {
    echo "<html><body><form name='datos' method='post' action='".ObtenProgramaNombre(PGM_FORM)."'>\n";
    Forma_CampoOculto('clave' , $clave);
    Forma_CampoOculto('fg_error' , $fg_error);
    Forma_CampoOculto('fe_periodo' , $fe_periodo);
    Forma_CampoOculto('tot_manual' , $tot_manual);
    for($j=0;$j<$tot_manual;$j++){
      Forma_CampoOculto('ds_concepto'.$j , $ds_concepto[$j]);
      Forma_CampoOculto('ds_concepto_err'.$j , $ds_concepto_err[$j]);
      Forma_CampoOculto('mn_tarifa_hr'.$j , $mn_tarifa_hr[$j]);
      Forma_CampoOculto('mn_tarifa_hr_err'.$j , $mn_tarifa_hr_err[$j]);
      Forma_CampoOculto('no_horas'.$j , $no_horas[$j]);
      Forma_CampoOculto('no_horas_err'.$j , $no_horas_err[$j]);
    }

    echo "\n</form>
<script>
  document.datos.submit();
</script></body></html>";
    exit;
  }


  # Insertamos o actualizamos los pagos 
  if(empty($fl_maestro_pago)){
    $fe_periodo = strftime("%Y-%m-%d", strtotime(date('d')."-".$fe_periodo));// tomamos el periodo y le agregamos el dia para guardalo
    $Query  = "INSERT INTO k_maestro_pago(fl_maestro, fe_periodo, mn_total, fg_publicar, fg_pagado, fe_pagado) ";
    $Query .= " VALUES($clave,'".$fe_periodo."',$mn_total,'0', '0', 'Null' ) ";
    $fl_maestro_pago=EjecutaInsert($Query);
  }
  else{
    if(!empty($fg_pagado))
      $fe_pagado = 'NOW()';
    else
      $fe_pagado = 'null';
    $Query  = "UPDATE k_maestro_pago SET mn_total=$mn_total, fg_publicar='$fg_publicar', fg_pagado='$fg_pagado', fe_pagado=$fe_pagado ";
    $Query .= "WHERE fl_maestro_pago=$fl_maestro_pago ";
    EjecutaQuery($Query);
  }


  # Inserta o actualiza los datos de la tabla automatica y manual si existen datos en las mismas
  if(!empty($fl_maestro_pago)){
    # Buscamos si ya se registraron algunas clases
    
    if(!empty($tot_aut)){
      for($i=0;$i<$tot_aut;$i++){
        # Si ya hay clases registradas para este maestro solo va actualizar los datos
        $search = RecuperaValor("SELECT fl_maestro_pago_det FROM k_maestro_pago_det WHERE fl_maestro_pago=$clave AND fl_grupo=$fl_grupo_aut[$i] AND ds_concepto=$fl_clase[$i] AND fg_tipo='A' ");
        if(!empty($search[0]))
          $Query = "UPDATE k_maestro_pago_det SET fl_maestro_pago=$fl_maestro_pago WHERE fl_maestro_pago_det=$search[0]";
        else{
          $search1 = RecuperaValor("SELECT COUNT(*) FROM k_maestro_pago_det WHERE fl_maestro_pago=$fl_maestro_pago AND fl_grupo=$fl_grupo_aut[$i] AND ds_concepto=$fl_clase[$i] AND fg_tipo='A'");
          if($search1[0])
            $Query  = "UPDATE k_maestro_pago_det SET mn_subtotal=".$mn_subtotal_aut[$i].", fg_subtract_class='".$subtract_class[$i]."' WHERE fl_maelstro_pago=$fl_maestro_pago AND ds_concepto=".$fl_clase[$i]."";
          else{
            $Query  = "INSERT INTO k_maestro_pago_det(fl_maestro_pago,fg_tipo,fl_grupo,ds_concepto,mn_tarifa_hr,no_horas, mn_subtotal, fg_subtract_class) ";
            $Query .= "VALUES($fl_maestro_pago,'A',$fl_grupo_aut[$i],'$fl_clase[$i]',$mn_tarifa_hr_aut[$i],'1',$mn_subtotal_aut[$i], '$subtract_class[$i]') ";
          }   
        }
        EjecutaQuery($Query);
      }
    }
    # Datos manuales
    if(!empty($tot_manual)){
      for($i=0;$i<$tot_manual;$i++){
        EjecutaQuery("UPDATE k_maestro_pago_det SET fl_maestro_pago=$fl_maestro_pago WHERE fl_maestro_pago_det=$fl_maestro_pago_det[$i]");
      }
    }
  }
   
  # Redirige al listado
  header("Location: ".ObtenProgramaBase( ));
  
?>