<?php
  
  # Libreria de funciones
  require("../common/lib/cam_general.inc.php");
  require("../../lib/sp_forms.inc.php");
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  $fl_alumno = ValidaSesion(False);
  $cl_sesion = $_COOKIE[SESION_CAMPUS];
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermisoCampus(FUNC_ALUMNOS)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Presenta contenido de la pagina
  $titulo = "Tuition Payment";
  PresentaHeader($titulo);
  
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
  
  
  # Recupera informacion de los pagos
  $fe_actual = ObtenFechaActual();
  $Query  = "SELECT fl_term_pago, no_opcion, no_pago, DATE_FORMAT(fe_pago, '%Y-%m-%d'), DATEDIFF(DATE_FORMAT(fe_pago, '%Y-%m-%d'), '$fe_actual') no_dias ";
  $Query .= "FROM k_term_pago ";
  $Query .= "WHERE fl_term=$fl_term_ini ";
  $Query .= "AND no_opcion=$fg_opcion_pago";
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
    
    $Query  = "SELECT $mn_due ";
    $Query .= "FROM k_app_contrato ";
    $Query .= "WHERE cl_sesion='$cl_sesion'"; 
    $row = RecuperaValor($Query);
    $mn_due = $row[0];
    
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
     
  # Informacion de paypal
  $urlPaypal = ObtenConfiguracion(61);
  $business = ObtenConfiguracion(62);
  $currency_code = 'CAD';  
  $item_name =ObtenNombreProgramaAlumno($fl_alumno).' payment no. '.$no_pago_pagar;
  
  echo "
          <tr>
                <td colspan='2' height='10' class='blank_cells'></td>
              </tr>
              <tr>
                <td colspan='2' height='568' class='blank_cells'>
                  <table border='0' cellpadding='0' cellspacing='0' width='100%'>
                    <tr>
                      <td height='5'></td>
                    </tr>
                    <tr>
                      <td colspan='3' align='left' valign='top' height='80' style='padding: 20px 0 0 20px;' class='division_line comment_text'>
                        <h3>Dear ".ObtenNombreUsuario($fl_alumno).": </h3>
                        Your next payment is on ".$fe_limite_pago_pagar."
                        <br>
                        Program Name: ".ObtenNombreProgramaAlumno($fl_alumno)."
                        <br>
                        Amount to pay: $".number_format ($mn_due_pagar, 2, '.', ',')." CAD
                        <br>
                        <br>
                        <h2>International & Canadian Students: </h2>
                        <a href='javascript: document.datos.submit();' id='paypal_btn'><img src='https://www.paypal.com/en_US/i/btn/btn_paynowCC_LG.gif' border='0' title='PayPal - The safer, easier way to pay online!'></a>
                        <form name='datos' action='$urlPaypal'>
                          <input type='hidden' name='cmd' value='_xclick'>
                          <input type='hidden' name='business' id='business' value='$business'>
                          <input type='hidden' name='currency_code' id='currency_code' value='$currency_code'>
                          <input type='hidden' name='item_name' id='item_name' value='$item_name'>
                          <input type='hidden' name='amount' id='amount' value='$mn_due_pagar'>
                          <input type='hidden' name='custom' id='custom' value='$fl_alumno'>
                        </form>
                      </td>
                    </tr>
                    <tr>
                      <td colspan='3' align='center' valign='top' height='80' class='division_line'>
                      </td>
                    </tr>";
  PresentaFooter( );
?>