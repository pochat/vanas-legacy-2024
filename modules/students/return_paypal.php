<?php
  
  # Libreria de funciones
  require("../common/lib/cam_general.inc.php");
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  ValidaSesion(False);
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermisoCampus(FUNC_ALUMNOS)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Presenta Contenido
  $titulo = "Tuition Payment";
  PresentaHeader($titulo);
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
                      <td colspan='3' align='center' valign='top' height='80' style='padding-top: 50px' class='division_line'>
                        <h1>Payment confirmed.</h1>
                      </td>
                    </tr>
                    <tr>
                      <td colspan='3' align='center' style='padding-top: 50px'>
                        <a href='payment_history.php'>Check your payment history here...</a>
                      </td>
                    </tr>
                    ";
  
  PresentaFooter( );
  
?>