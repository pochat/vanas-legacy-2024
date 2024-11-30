<?php
  
  # Libreria de funciones	
	require("../lib/self_general.php");
  
  # Recibe parametros
  $fl_entrega_semanal_sp = RecibeParametroNumerico('fl_entrega_semanal_sp');
  $clave = RecibeParametroNumerico('clave');
  $fl_usuario = RecibeParametroNumerico('fl_usuario');
 
  # Dialogo para asignar calificacion
  echo "
  <div class='modal-dialog' role='document' id='modal_actions' style=' width:300px; height:400px;'>
  <div class='modal-content'>
    <div class='modal-header'>
      <button type='button' class='close' data-dismiss='modal' aria-label='Close'><span aria-hidden='true'>&times;</span></button>
      <h4 class='modal-title' id='gridModalLabel'><i class='fa fa-exclamation-triangle'></i> <strong>Assing grade</strong></h4>
    </div>
    <div class='modal-body'>
      <form name='Assig_gradde_sp' id='Assig_gradde_sp' method='POST' action='".PATH_N_MAE_PAGES."/assign_grades.php'>
        <label><strong><p>Grade for <b>$ds_nombre</b> on week <b>$no_semana</b>:</p></strong></label>
        <select name='fl_calificacion' id='fl_calificacion' class='select2'>
          <option value=0>Pending</option>";
          $Query  = "SELECT fl_calificacion, cl_calificacion, ds_calificacion, fg_aprobado ";
          $Query .= "FROM c_calificacion /*WHERE no_equivalencia <= $max_calificacion*/ ORDER BY no_min DESC";
          $rs = EjecutaQuery($Query);
          while($row = RecuperaRegistro($rs)) {
            echo "
            <option value=$row[0]";
            if($row[0] == $fl_promedio_semana)
              echo " selected";
            echo ">$row[1] ".str_uso_normal($row[2])."</option>";
          }
        echo "
        </select>";
      echo "
        <input type='hidden' name='fl_entrega_semanal_sp' id='fl_entrega_semanal_sp' value='$fl_entrega_semanal_sp'>
        <!--<input type='hidden' name='clave' id='clave' value='$clave'>
        <input type='hidden' name='fl_usuario' id='fl_usuario' value='$fl_usuario'>-->
      </form>
    </div>
    <div class='modal-footer'>
      <button type='button' class='btn btn-secondary' data-dismiss='modal' id='cerrar_modal'><i class='fa fa-times-circle'></i> ".ObtenEtiqueta(1066)."</button>
      <button type='submit' class='btn btn-primary' onclick='AssignGradeSP()' id='envio_boton' ><i class='fa fa-check-circle'></i> Assing grade</button>
    </div>
  </div>
  </div>
  <script>
  // volver a cargar las imagenes
  pageSetUp();
  </script>";
  
?>