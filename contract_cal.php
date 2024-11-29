<?php
  # Libreria de funciones
  require("lib/sp_general.inc.php");
  
  # Recibe parametros
  $clave = RecibeParametroNumerico('clave');
  $no_contrato = RecibeParametroNumerico('no_contrato');
  $fg_opcion_pago = RecibeParametroNumerico('fg_opcion');
  $fl_programa = RecibeParametroNumerico('fl_programa');
  
  # Obtemos la sesion
  $row = RecuperaValor("SELECT cl_sesion FROM c_sesion WHERE fl_sesion=$clave");
  $cl_sesion = $row[0];
  ContratosDetalles($cl_sesion, $fg_opcion_pago, $fl_programa, $no_contrato);
  /*# Definimos la opcion de pago
  switch($fg_opcion_pago){
    case 1: $no_payments = 'no_a_payments'; $mn_due = 'mn_a_due'; $mn_paid = 'mn_a_paid'; break;
    case 2: $no_payments = 'no_b_payments'; $mn_due = 'mn_b_due'; $mn_paid = 'mn_b_paid'; break;
    case 3: $no_payments = 'no_c_payments'; $mn_due = 'mn_c_due'; $mn_paid = 'mn_c_paid'; break;
    case 4: $no_payments = 'no_d_payments'; $mn_due = 'mn_d_due'; $mn_paid = 'mn_d_paid'; break;    
  }
  
  # Obtenemos el numero de pagos
  $Query  = "SELECT b.no_semanas, b.$no_payments, b.$mn_due, b.$mn_paid FROM c_programa a, k_programa_costos b ";
  $Query .= "WHERE a.fl_programa = b.fl_programa AND a.fl_programa=$fl_programa ";
  $row = RecuperaValor($Query);
  $no_semanas = $row[0];
  $no_x_payments = $row[1];
  $mn_x_due = $row[2];
  $mn_paid = $row[3];
  $row = RecuperaValor("SELECT no_semanas, $no_payments FROM c_programa a, k_programa_costos b WHERE a.fl_programa=b.fl_programa AND a.fl_programa=$fl_programa");
  $no_semanas = $row[0];
  $no_x_payments = $row[1];
  $row4 = RecuperaValor("SELECT b.$mn_due, b.$mn_paid, mn_costs, mn_discount FROM k_app_contrato b WHERE cl_sesion='$cl_sesion'");
  $mn_x_due = $row4[0];
  $mn_paid = ($row4[1]+$row4[2])-$row4[3];
  
  # Usamos la formula
  $meses_x_curso = $no_semanas/4;
  if($meses_x_curso>=12)
    $no_pagos_year = 12/($meses_x_curso/$no_x_payments);
  else
    $no_pagos_year = 1;
  $payment_x_year = $mn_x_due*$no_pagos_year;
  
  # Obtenemos todos el numero de contratos
  $row1 = RecuperaValor("SELECT COUNT(*) FROM k_app_contrato WHERE cl_sesion='$cl_sesion'");
  $no_contratos = $row1[0];
  if($no_contratos==1){
    # Acualizamos la informacion
    EjecutaQuery("UPDATE k_app_contrato SET mn_payment_due='$payment_x_year' WHERE cl_sesion='$cl_sesion' AND no_contrato=1");  
    // echo "UPDATE k_app_contrato SET mn_payment_due='$payment_x_year' WHERE cl_sesion='$cl_sesion' AND no_contrato=1";
  }
  else{
    # mas de un contrato
    for($i=1;$i<=$no_contratos;$i++){
      if($i==$no_contratos){
        $no_contratos = $no_contratos-1;
        if($no_contratos==0)
          $no_contratos = 1;
        $payment_x_year = $mn_paid-($payment_x_year*$no_contratos);
      }
      else
        $payment_x_year = $payment_x_year;
      # Actualiamos el monto del contrato
      EjecutaQuery("UPDATE k_app_contrato SET mn_payment_due='$payment_x_year' WHERE cl_sesion='$cl_sesion' AND no_contrato=$i");  
       // echo "UPDATE k_app_contrato SET mn_payment_due='$payment_x_year' WHERE cl_sesion='$cl_sesion' AND no_contrato=$i <br>";
    }
  }
  # Acualizamos la informacion
  //EjecutaQuery("UPDATE k_app_contrato SET mn_payment_due='$payment_x_year' WHERE cl_sesion='$cl_sesion' AND no_contrato=1");  
  echo "UPDATE k_app_contrato SET mn_payment_due='$payment_x_year' WHERE cl_sesion='$cl_sesion' AND no_contrato=1";
  # Si aun no cubre el total de pagos entonces los demas pagos van para el siguiente contrato
  $pagos_restan = $no_x_payments-$no_pagos_year;
  // if(!empty($pagos_restan)){    
    $pago_restante = $mn_x_due*$pagos_restan;
    //EjecutaQuery("UPDATE k_app_contrato SET mn_payment_due='$pago_restante' WHERE cl_sesion='$cl_sesion' AND no_contrato=2");
    echo "UPDATE k_app_contrato SET mn_payment_due='$pago_restante' WHERE cl_sesion='$cl_sesion' AND no_contrato=2";
  // }
  
  # Buscamos el numero de contratos
  $rs1 = EjecutaQuery("SELECT no_contrato FROM k_app_contrato WHERE cl_sesion='$cl_sesion'");
  $no_contratos = CuentaRegistros($rs1);
  if($no_contratos>1){
    for($i=1;$row2=RecuperaRegistro($rs1);$i++){
      if($i != $no_contratos)
        echo "UPDATE k_app_contrato SET mn_payment_due='$payment_x_year' WHERE cl_sesion='$cl_sesion' AND no_contrato=$i <br/>";
      else
        echo "UPDATE k_app_contrato SET mn_payment_due='$pago_restante' WHERE cl_sesion='$cl_sesion' AND no_contrato=$i ultimo<br/>";
    }
  }*/
  
  
?>