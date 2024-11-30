<?php
  
  # Libreria de funciones
  require("../../common/lib/cam_general.inc.php");
  require("../../../lib/sp_forms.inc.php");
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  $fl_alumno = ValidaSesion(False);
  $cl_sesion = $_COOKIE[SESION_CAMPUS];
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermisoCampus(FUNC_ALUMNOS)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  # Recibimos parametros
  $term_pagar = RecibeParametroNumerico('term_pagar',True);
  # Presenta contenido de la pagina
  
  # Recupera el programa y term que esta cursando el alumno
  $fl_programa = ObtenProgramaAlumno($fl_alumno);
  $fl_grupo = ObtenGrupoAlumno($fl_alumno);
  $fl_term = ObtenTermAlumno($fl_alumno);
  
  # Recupera el term inicial
  $Query  = "SELECT fl_term_ini ";
  $Query .= "FROM k_term ";
  $Query .= "WHERE fl_programa=$fl_programa ";
  $Query .= "AND fl_term=$fl_term";
  $row = RecuperaValor($Query);
  $fl_term_ini = $row[0];
  
  # Recupera el tipo de pago para el curso
  $Query  = "SELECT fg_opcion_pago ";
  $Query .= "FROM k_app_contrato ";
  $Query .= "WHERE cl_sesion='$cl_sesion'"; 
  $row = RecuperaValor($Query);
  $fg_opcion_pago = $row[0];
  
  if(empty($fl_term_ini))
    $fl_term_ini=$fl_term;
  
  # Se obtiene la descripcion de la frecuencia del pago
  switch($fg_opcion_pago) {
    case 1:
      $mn_due='mn_a_due';
      $ds_frecuencia='ds_a_freq';
      break;
    case 2:
      $mn_due='mn_b_due';
      $ds_frecuencia='ds_b_freq';
      break;
    case 3:
      $mn_due='mn_c_due';
      $ds_frecuencia='ds_c_freq';
      break;
    case 4:
      $mn_due='mn_d_due';
      $ds_frecuencia='ds_d_freq';
      break;
  }
  $Query  = "SELECT $ds_frecuencia ";
  $Query .= "FROM k_programa_costos ";
  $Query .= "WHERE fl_programa=$fl_programa ";
  $row = RecuperaValor($Query);
  $ds_frecuencia = $row[0];
  
  # Obtenemos el ultimo pago que realizo el estudiante del term actual que cursa
  # esto porque puede haber alumnos que cambiaron de term o repitieron grado
  $ultimo = RecuperaValor("SELECT MAX(no_pago) FROM k_alumno_pago a, k_term_pago b WHERE  a.fl_term_pago=b.fl_term_pago AND fl_alumno=$fl_alumno AND fl_term=$fl_term_ini");
  $last_one = $ultimo[0];
  # Monto obtenido del contrato
  $Query  = "SELECT $mn_due ";
  $Query .= "FROM k_app_contrato ";
  $Query .= "WHERE cl_sesion='$cl_sesion'"; 
  $row = RecuperaValor($Query);
  $mn_due = $row[0];
  
  # Recupera informacion de los pagos
  $fe_actual = ObtenFechaActual();
  $Query  = "SELECT fl_term_pago, no_opcion, no_pago, DATE_FORMAT(fe_pago, '%Y-%m-%d'), DATEDIFF(DATE_FORMAT(fe_pago, '%Y-%m-%d'), '$fe_actual') no_dias ";
  $Query .= "FROM k_term_pago ";
  $Query .= "WHERE fl_term=$fl_term_ini ";
  $Query .= "AND no_opcion=$fg_opcion_pago ";
  if(!empty($last_one))
    $Query .= "AND no_pago>$last_one ";
  $Query .= "ORDER BY fe_pago ASC ";
  $rs = EjecutaQuery($Query);
  for($i=0; $row = RecuperaRegistro($rs); $i++) {
    $fl_term_pago = $row[0];
    $no_opcion = $row[1];
    $no_pago = $row[2];
    $fe_limite_pago = $row[3];
    $no_dias = $row[4];
    
    $Query  = "SELECT fl_term_pago, cl_metodo_pago, fe_pago, mn_pagado, mn_late_fee ";
    $Query .= "FROM k_alumno_pago ";
    $Query .= "WHERE fl_term_pago=$fl_term_pago ";
    $Query .= "AND fl_alumno=$fl_alumno";
    $row = RecuperaValor($Query);
    $fl_t_pago = $row[0];
    $cl_metodo_pago = $row[1];
    $fe_pago = $row[2];
    $mn_pagado = $row[3];
        
    
    if(empty($fl_t_pago)) {
      if(empty($proximo_pago)){
        $proximo_pago=$fl_term_pago;
        $no_opcion_pagar=$no_opcion;
        $no_pago_pagar=$no_pago;
        $fe_limite_pago_pagar=$fe_limite_pago;
        # Si existe atraso de pago se paga mn_late_fee
        if($no_dias<0)
          $mn_due_pagar=$mn_due+ObtenConfiguracion(66);
        else
          $mn_due_pagar=$mn_due;
      }
    }
  }
  # Si existe algun error el term sera el recibido
  if(empty($proximo_pago)){
    $Queryr  = "SELECT no_opcion, no_pago, DATE_FORMAT(fe_pago, '%Y-%m-%d'), ";
    $Queryr .= "DATEDIFF(DATE_FORMAT(fe_pago, '%Y-%m-%d'), '$fe_actual') no_dias ";
    $Queryr .= "FROM k_term_pago WHERE fl_term_pago=$term_pagar";
    $rowr = RecuperaValor($Queryr);
    $no_pago_pagar = $rowr[1];
    $fe_limite_pago_pagar = $rowr[2];
    # Si existe atraso de pago se paga mn_late_fee
    if($no_dias<0)
      $mn_due_pagar=$mn_due+ObtenConfiguracion(66);
    else
      $mn_due_pagar=$mn_due;
    $proximo_pago = $term_pagar;
  }

  # Informacion de paypal
  $urlPaypal = ObtenConfiguracion(61);
  $business = ObtenConfiguracion(62);
  $currency_code = ObtenConfiguracion(82);  
  $item_name =ObtenNombreProgramaAlumno($fl_alumno).' payment no. '.$no_pago_pagar;
  
  # Si el usuario es de canada y el programa requiere tax y ademas sea corto el curso se deberan agragar el tax rate
  # Obtenemos el tax rate de la provincia
  # El tax lo calculara automaticamente paypal dependiendo del tax de la provincia 
  # Recordadno que esto ya se dieron de alta en paypal y deben ser iguales a los de la BD
  $Query9  = "SELECT a.fg_tax_pagos, b.ds_add_country, b.ds_add_state ";
  $Query9 .= "FROM c_programa a, k_ses_app_frm_1 b WHERE a.fl_programa=b.fl_programa AND b.cl_sesion='".$cl_sesion."'";
  $row9 = RecuperaValor($Query9);
  $fg_tax_pagos = $row9[0];
  $ds_add_country = $row9[1];
  $ds_add_state = $row9[2];  
  $tax_rate_paypal = "";
  $pago_tax = false;
  $with_tax = 0;
  if($ds_add_country == 38 AND !empty($fg_tax_pagos)){
    if(!empty($ds_add_state)){
      $row_tax = RecuperaValor("SELECT ds_abreviada,mn_tax FROM k_provincias WHERE fl_provincia='$ds_add_state'");
      $ds_abreviada = $row_tax[0];
      $mn_tax_rate = $row_tax[1];
      $with_tax = $mn_due_pagar*($mn_tax_rate/100);
      $tax_rate_paypal = "<input type='hidden' name='tax' id='tax' value='".$with_tax."'>";
      $pago_tax = true;
    }
  }
  // $mn_due_pagar=0.01;
?>

<div class='row'>
  <div class='col-xs-12'>
    <div class='well well-light padding-10'>
      <div class='row'>
        <div class='col-xs-12'> 
          <div class='well well-light no-margin text-center'>
            <?php 
            
            $url_campus = "https://".ObtenConfiguracion(60); 
            echo 
              "<h3>".ObtenEtiqueta(2231)." ".ObtenNombreUsuario($fl_alumno).": </h3>
              ".ObtenEtiqueta(2232)." ".$fe_limite_pago_pagar."
              <br>
              ".ObtenEtiqueta(2233).": ".ObtenNombreProgramaAlumno($fl_alumno)." ";
           if(!$pago_tax){
            echo "
              <br>
              ".ObtenEtiqueta(2234).": $".number_format ($mn_due_pagar, 2, '.', ',')." {$currency_code} ";
           }
           else{
             echo "
              <br>
              ".ObtenEtiqueta(2235).": $".number_format ($mn_due_pagar, 2, '.', ',')." {$currency_code}
              <br>
              ".ObtenEtiqueta(2236).": $".number_format ($with_tax, 2, '.', ',')." {$currency_code}               
              <br>
              ".ObtenEtiqueta(2234).": $".number_format ($mn_due_pagar + $with_tax, 2, '.', ',')." {$currency_code} ";
           }
            echo "
              <br>
              <br>
              <h2>International & Canadian Students: </h2>
              <a href='javascript: document.datos.submit();' id='paypal_btn'><img src='https://www.paypal.com/en_US/i/btn/btn_paynowCC_LG.gif' border='0' title='PayPal - The safer, easier way to pay online!'></a>
              <form name='datos' action='$urlPaypal' method='post'>
                <input type='hidden' name='cmd' value='_xclick'>
                <input type='hidden' name='business' id='business' value='$business'>
                <input type='hidden' name='currency_code' id='currency_code' value='$currency_code'>
                <input type='hidden' name='item_name' id='item_name' value='$item_name'>
                <input type='hidden' name='amount' id='amount' value='$mn_due_pagar'>
                <!-- Enviamos el fl_alumno y el fl_term_pago-->
                <input type='hidden' name='custom' id='custom' value='$fl_alumno|T$proximo_pago'>
                ".$tax_rate_paypal."
                <!--url que regresa una vez que termino el proceso de comunicacion con paypal-->
                <input type='hidden' name='return' id='return' value='".$url_campus.PATH_N_ALU."/index.php#ajax/paypal_return.php?cm=".$fl_alumno."'>
                <!--Envia datos a la url espefificada -->
                <input type='hidden' name='rm' id='rm' value='2'>
                <!--Si cancela el comprador antes de realizar el pago redirige a la url que se ingresa-->
                <input type='hidden' name='cancel_return' id='cancel_return' value='".$url_campus.PATH_N_ALU."/index.php#ajax/tuition_payment.php'>
              </form>";
            ?>
          </div>
        </div>   
      </div>
    </div>
  </div>   
</div>