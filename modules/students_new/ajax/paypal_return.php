<?php

	# Libreria de funciones
  require("../../common/lib/cam_general.inc.php");
  require("../../../lib/sp_forms.inc.php");

  # Recibe Paramtros
  $fl_alumno = RecibeParametroHTML('cm', False, True);
  
  # Si no obtiene alumno entonces Si el alumno no existe quiere decir que es un applicacion y se va direccinar a ELOA900CS2YC32_frm.php
  if(empty($fl_alumno)){
    echo "
    <script type='text/javascript'>
      window.location='".SP_HOME."/app_form/ELOA900CS2YC32_frm.php';
    </script>";
  }
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermisoCampus(FUNC_ALUMNOS)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  echo '
  <div id="row">
    <div class="col-lg-12 col-xs-12 col-sm-12 col-md-12">
      <div class="listing">      
        <div class="well well-light  padding-10">
          <div id="item-body" class="modal-body">
              <div class="row">				
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">				
                  <div class="row">
                    <div class="well well-light no-margin text-center">
                      <div class="text-center error-box">
                        <a href="#ajax/payment_history.php" title="Payment History">
                          <strong><h1 class="text"><i class="fa fa-thumbs-o-up"></i><br/>'.ObtenEtiqueta(809).'</h1></strong>
                        </a>
                        <br>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
          </div>
        </div>
      </div>
  </div>';

?>