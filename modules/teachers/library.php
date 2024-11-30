<?php
  
  # Libreria de funciones
  require("../common/lib/cam_general.inc.php");
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  ValidaSesion( );
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermisoCampus(FUNC_MAESTROS)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Recupera contenido de la pagina fija
  $Query  = "SELECT ds_contenido, tr_contenido ";
  $Query .= "FROM c_pagina ";
  $Query .= "WHERE cl_pagina=8";
  $row = RecuperaValor($Query);
  $contenido = str_uso_normal(EscogeIdioma($row[0], $row[1]));
  
  $path = "";
  $file = "";
  $tipo = "content";
  $titulo = "Library";
  $perfil = "teacher";
  
  PresentaHeader( );
  PresentaColIzq($titulo, $perfil);
  
  #Presenta Contenido
  echo "
              <tr>
                <td colspan='2' height='10' class='blank_cells'></td>
              </tr>
              <tr>
                <td colspan='2' height='568' class='blank_cells'>
                  <table border='".D_BORDES."' cellpadding='0' cellspacing='0' width='100%'>
                    <tr>
                      <td height='5'>&nbsp;</td>
                    </tr>
                    <tr>
                      <td colspan='3' valign='top' height='80' class='division_line'>
                        <table border='".D_BORDES."' cellpadding='0' cellspacing='0' width='100%'>
                          <tr>
                            <td colspan='3' height='5'>&nbsp;</td>
                          </tr>
                          <tr>
                            <td width='10'>&nbsp;</td>
                            <td class='comment_text'>$contenido</td>
                            <td width='10'>&nbsp;</td>
                          </tr>
                          <tr>
                            <td colspan='3' height='20'>&nbsp;</td>
                          </tr>
                        </table>
                      </td>
                    </tr>
                    ";
  PresentaColDer($tipo, $perfil);
  PresentaFooter();
  
?>