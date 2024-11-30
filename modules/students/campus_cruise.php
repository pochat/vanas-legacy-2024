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
  $titulo = "Campus Cruise";
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
                      <td colspan='3' align='center' valign='top' height='80' class='division_line'>
                        <img src='".PATH_COM_IMAGES."/campus_cruise.jpg' border='none' />
                      </td>
                    </tr>
                    ";
  
  PresentaFooter( );
  
?>