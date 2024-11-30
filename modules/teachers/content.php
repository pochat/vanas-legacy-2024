<?php
  
  # Libreria de funciones
  require("../common/lib/cam_general.inc.php");
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  $fl_maestro = ValidaSesion(False);
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermisoCampus(FUNC_MAESTROS)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Recibe parametros
  $fixed_page = RecibeParametroNumerico('page', True);
  if(empty($fixed_page)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Recupera contenido de la pagina fija
  $Query  = "SELECT ds_titulo, tr_titulo, ds_contenido, tr_contenido, fg_fijo ";
  $Query .= "FROM c_pagina ";
  $Query .= "WHERE cl_pagina = $fixed_page ";
  $Query .= "AND (fl_programa = 0 OR fl_programa IN (SELECT DISTINCT fl_programa
                                                     FROM c_grupo LEFT JOIN k_term ON k_term.fl_term = c_grupo.fl_term
                                                     WHERE fl_maestro = $fl_maestro)) ";
  $Query .= "AND (no_grado = 0 OR no_grado IN (SELECT DISTINCT no_grado 
                                               FROM k_term  
                                               WHERE fl_term IN (SELECT DISTINCT fl_term FROM c_grupo WHERE fl_maestro = $fl_maestro))) ";
  $Query .= "ORDER BY fl_programa DESC, no_grado DESC";
  $rs = EjecutaQuery($Query);
  $i = 0;
  while($row = RecuperaRegistro($rs)) {
    $titulo = str_uso_normal(EscogeIdioma($row[0], $row[1]));
    $contenido[$i] = str_uso_normal(EscogeIdioma($row[2], $row[3]));
    $i++;
    if($row[4] <> "0") {
      MuestraPaginaError(ERR_SIN_PERMISO);
      exit;
    }
  }
    
  
  # Presenta detalle de la pagina
  PresentaHeader($titulo);
  echo "
              <tr>
                <td colspan='2' height='10' class='blank_cells'></td>
              </tr>
              <tr>
                <td colspan='2' height='568' class='blank_cells'>
                  <table border='".D_BORDES."' cellpadding='0' cellspacing='0' width='100%'>
                    <tr>
                      <td height='5'>&nbsp;</td>
                    </tr>";
  for($j=0; $j<$i; $j++)
  {
    echo "
                    <tr>
                      <td colspan='3' valign='top' height='80' class='division_line'>
                        <table border='".D_BORDES."' cellpadding='0' cellspacing='0' width='100%'>
                          <tr>
                            <td colspan='3' height='5'>&nbsp;</td>
                          </tr>
                          <tr>
                            <td width='10'>&nbsp;</td>
                            <td class='comment_text'>$contenido[$j]</td>
                            <td width='10'>&nbsp;</td>
                          </tr>
                          <tr>
                            <td colspan='3' height='20'>&nbsp;</td>
                          </tr>
                        </table>
                      </td>
                    </tr>";
  }
  PresentaFooter( );
  
?>