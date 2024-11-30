<?php
  
  # Libreria de funciones
  require("../../common/lib/cam_general.inc.php");
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  ValidaSesion(False);
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermisoCampus(FUNC_ALUMNOS)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
?>

<div class="row">
  <div class="col-xs-12">
    <div class="well well-light padding-10">
      <div class="row">
        <div class="col-xs-12"> 
          <div class="well well-light no-margin padding-10 text-center">
            <h1 style='font-size :28px; font-weight: 500'>Payment Confirmed.</h1>
            <a href='#ajax/payment_history.php' style='font-size: 18px;'>Check your payment history here...</a>
          </div>
        </div>   
      </div>
    </div>
  </div>   
</div>