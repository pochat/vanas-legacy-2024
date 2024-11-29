<?php
  
  # Libreria de funciones
  require("lib/sp_general.inc.php");
  require("lib/sp_forms.inc.php");
  
  # Recupera los datos del contenido
  $Query  = "SELECT ds_titulo, tr_titulo, ds_contenido, tr_contenido ";
  $Query .= "FROM c_pagina ";
  $Query .= "WHERE cl_pagina=".PAG_FACULTY;
  $row = RecuperaValor($Query);
  $titulo = str_uso_normal(EscogeIdioma($row[0], $row[1]));
  $contenido = str_uso_normal(EscogeIdioma($row[2], $row[3]));
  
  # Valores default
  $img_archivo = IMG_DEFAULT;
  $avatar_width = ObtenConfiguracion(30);
  
  # Header
  PresentaHeader(SEC_FACULTY, True);
  
  # Cuerpo de la pagina
  echo "
    <img src='".SP_IMAGES."/$img_archivo' width='720' height='150' border='0'>
    <table border='".D_BORDES."' width='720' valign='top' cellspacing='0' cellpadding='0'>
      <tr>
        <td valign='top'>
          <table border='".D_BORDES."' width='500' cellspacing='0' cellpadding='0'>
            <tr>
              <td width='20' height='20'>&nbsp;</td>
              <td>&nbsp;</td>
              <td width='20'>&nbsp;</td>
            </tr>
            <tr>
              <td height='30'>&nbsp;</td>
              <td><b>$titulo</b></td>
              <td>&nbsp;</td>
            </tr>
            <tr>
              <td>&nbsp;</td>
              <td valign='top'><br>$contenido</td>
              <td>&nbsp;</td>
            </tr>
            <tr>
              <td colspan='3' height='20'>&nbsp;</td>
            </tr>          
          </table>
        </td>
        <td width='220' valign='top' class='css_faculty'>
          <table border='".D_BORDES."' width='100%' valign='top' cellspacing='0' cellpadding='0'>
            <tr>
              <td width='80'>&nbsp;</td>
              <td width='10'>&nbsp;</td>
              <td width='80'>&nbsp;</td>
              <td width='10'>&nbsp;</td>
            </tr>";
  
  # Recupera los maestros
  $Query  = "SELECT ds_ruta_avatar, ";
  $concat = array('ds_nombres', "' '", 'ds_apaterno');
  $Query .= ConcatenaBD($concat)." 'ds_nombre', ds_empresa ";
  $Query .= "FROM c_maestro a, c_usuario b ";
  $Query .= "WHERE a.fl_maestro=b.fl_usuario ";
  $Query .= "AND fg_activo='1' ";
  $Query .= "ORDER BY ds_nombre ";
  $rs = EjecutaQuery($Query);
  $tot_maestros = CuentaRegistros($rs);
  $tot_renglones = (int) ceil($tot_maestros/2);
  for($i = 0; $i < $tot_renglones; $i++) {
    $ds_ruta_avatar[0] = "&nbsp;";
    $ds_nombre[0] = "&nbsp;";
    $ds_empresa[0] = "&nbsp;";
    $row = RecuperaRegistro($rs);
    if(!empty($row[1])) {
      if(!empty($row[0]))
        $ds_ruta_avatar[0] = "<img src='".PATH_MAE_IMAGES."/avatars/".$row[0]."' width='$avatar_width' border='0' />";
      else
        $ds_ruta_avatar[0] = "<img src='".SP_IMAGES."/".IMG_T_AVATAR_DEF."' width='$avatar_width' border='0' />";
      $ds_nombre[0] = str_uso_normal($row[1]);
      if(!empty($row[2]))
        $ds_empresa[0] = str_uso_normal($row[2]);
    }
    $ds_ruta_avatar[1] = "&nbsp;";
    $ds_nombre[1] = "&nbsp;";
    $ds_empresa[1] = "&nbsp;";
    $row = RecuperaRegistro($rs);
    if(!empty($row[1])) {
      if(!empty($row[0]))
        $ds_ruta_avatar[1] = "<img src='".PATH_MAE_IMAGES."/avatars/".$row[0]."' width='$avatar_width' border='0' />";
      else
        $ds_ruta_avatar[1] = "<img src='".SP_IMAGES."/".IMG_T_AVATAR_DEF."' width='$avatar_width' border='0' />";
      $ds_nombre[1] = str_uso_normal($row[1]);
      if(!empty($row[2]))
        $ds_empresa[1] = str_uso_normal($row[2]);
    }
    echo "
            <tr>
              <td>$ds_ruta_avatar[0]</td>
              <td>&nbsp;</td>
              <td>$ds_ruta_avatar[1]</td>
              <td>&nbsp;</td>
            </tr>
            <tr>
              <td>$ds_nombre[0]</td>
              <td>&nbsp;</td>
              <td>$ds_nombre[1]</td>
              <td>&nbsp;</td>
            </tr>
            <tr>
              <td>$ds_empresa[0]</td>
              <td>&nbsp;</td>
              <td>$ds_empresa[1]</td>
              <td>&nbsp;</td>
            </tr>
            <tr><td colspan='4'>&nbsp;</td></tr>";
  }
  
  echo "
          </table>
        </td>
      </tr>
    </table>\n";
  
  # Footer
  PresentaFooter( );
  
?>