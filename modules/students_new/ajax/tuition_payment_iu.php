<?php
  
  # Libreria de funciones
  require("../../common/lib/cam_general.inc.php");
  
  # Recibe parametros
  $mn_pagado = RecibeParametroHTML('amt', False, True);
  $fl_alumno = RecibeParametroHTML('cm', False, True);
  $ds_transaccion = RecibeParametroHTML('tx', False, True);
  # PC| Indetificacion para Pago Completo
  if(strpos($fl_alumno, "PC|") !== False) {
    echo "
    <form name='pc' method='get' action='".SP_HOME."/app_form/ELOA900CS2YC32_frm.php'>
      <input type='hidden' name='cm' id='cm' value='$fl_alumno'>
      <input type='hidden' name='st' id='st' value='Completed'>
      <input type='hidden' name='mount' id='mount' value='$mn_pagado'>
      <input type='hidden' name='tx' id='tx' value='$ds_transaccion'>
    </form>
    <script>
      document.pc.submit();
    </script>";
    exit;
  }
  else
    $fl_alumno = $fl_alumno;

  # Recupera el programa y term que esta cursando el alumno
  $fl_programa = ObtenProgramaAlumno($fl_alumno);
  $fl_term = ObtenTermAlumno($fl_alumno);
  
  $fe_actual = ObtenFechaActual();//fecha actual
  
  # Recupera la sesion
  $Query  = "SELECT cl_sesion ";
  $Query .= "FROM c_usuario ";
  $Query .= "WHERE fl_usuario=$fl_alumno";
  $row = RecuperaValor($Query);
  $cl_sesion = $row[0];
  
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
  
  # Obtenemos el ultimo pago que realizo el estudiante del term actual que cursa
  # esto porque puede haber alumnos que cambiaron de term o repitieron grado
  $ultimo = RecuperaValor("SELECT MAX(no_pago) FROM k_alumno_pago a, k_term_pago b WHERE  a.fl_term_pago=b.fl_term_pago AND fl_alumno=$fl_alumno");
  $last_one = $ultimo[0];
  
  # Recupera informacion de los pagos
  $Query  = "SELECT fl_term_pago, no_opcion, no_pago, DATEDIFF(DATE_FORMAT(fe_pago, '%Y-%m-%d'), '$fe_actual') no_dias ";
  $Query .= "FROM k_term_pago ";
  $Query .= "WHERE fl_term=$fl_term_ini ";
  $Query .= "AND no_opcion=$fg_opcion_pago AND no_pago>$last_one ";
  $rs = EjecutaQuery($Query);
  for($i=0; $row = RecuperaRegistro($rs); $i++) {
    $fl_term_pago = $row[0];
    $no_dias = $row[3];

    $Query  = "SELECT fl_term_pago ";
    $Query .= "FROM k_alumno_pago ";
    $Query .= "WHERE fl_term_pago=$fl_term_pago ";
    $Query .= "AND fl_alumno=$fl_alumno";
    $row = RecuperaValor($Query);
    $fl_t_pago = $row[0];

    if(empty($fl_t_pago)) {
      if(empty($pago_actual)){
        $pago_actual=$fl_term_pago;
        # Validamos si los dias son menos a 0 entonces ya se paso el pago y debe pagar el late fee si no es un pago normal
        if($no_dias < 0)
          $mn_late_fee = ObtenConfiguracion(66);
        else
          $mn_late_fee = '0.00';
        if(!ExisteEnTabla('k_alumno_pago', 'fl_alumno', $fl_alumno, 'ds_transaccion', "'$ds_transaccion'", True)) {
          $Query  = "INSERT INTO k_alumno_pago (fl_alumno, fl_term_pago, cl_metodo_pago, fe_pago, mn_pagado, mn_late_fee, ds_transaccion)  ";
          $Query .= "VALUES ($fl_alumno,$pago_actual, '1', CURRENT_TIMESTAMP, '$mn_pagado', $mn_late_fee, '$ds_transaccion')";
          $fl_alumno_pago = EjecutaInsert($Query);
        }
      }
    }
  }
  
  # Regresa a la pagina de origen
  //$pag = PATH_N_ALU."/index.php#ajax/return_paypal.php";
  //header("Location: ".$pag);
  //header("Location: return_paypal.php");
  # Esta parte pertenece al return_paypal pero la cambiamos porque recaragaba varias veces la pagina 
  # que por consecuencia registraba mas de un pago
  # Confirmacion si el pago se realizo correctamente
  if(!empty($fl_alumno_pago))
    $confirmed = "Payment Confirmed.";
  else
    $confirmed = "Payment Error.";

  echo "
  <div class='row'>
    <div class='col-xs-12'>
      <div class='well well-light padding-10'>
        <div class='row'>
          <div class='col-xs-12'> 
            <div class='well well-light no-margin padding-10 text-center'>
              <h1 style='font-size :28px; font-weight: 500'>$confirmed</h1>
              <a href='#ajax/payment_history.php' style='font-size: 18px;'>Check your payment history here...</a>
            </div>
          </div>   
        </div>
      </div>
    </div>   
  </div>";
  
?>