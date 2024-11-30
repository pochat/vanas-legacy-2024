<?php
  
  # 31 - Contenido estandar
  # Un bloque de texto
  
  # Variables usadas en este template
  $titulo         = EscogeIdioma($nb_titulo, $tr_titulo);
  $resumen        = EscogeIdioma($ds_resumen, $tr_resumen);
  $texto_01       = EscogeIdioma($ds_contenido[1], $tr_contenido[1]);
  $texto_02       = EscogeIdioma($ds_contenido[2], $tr_contenido[2]);
  $img_archivo_01 = EscogeIdioma($nb_archivo_i[1], $tr_archivo_i[1]);
  $img_caption_01 = EscogeIdioma($ds_caption_i[1], $tr_caption_i[1]);
  $img_alt_01     = EscogeIdioma($ds_alt_i[1], $tr_alt_i[1]);
  $img_liga_01    = $ds_liga_i[1];
  
  # Si no se especifica la imagen, toma la de default
  if(empty($img_archivo_01))
    $img_archivo_01 = IMG_DEFAULT;
  
  # Header
  PresentaHeader($fl_funcion, True);  
  
  # Cuerpo del Home
  echo "
    <img src='".SP_IMAGES."/$img_archivo_01' width='720' height='150' border='0'>
    <table border='".D_BORDES."' width='720' valign='top' cellspacing='0' cellpadding='0'>";
  if(!empty($resumen)) {
    echo "
      <tr>
        <td valign='top'><table border='".D_BORDES."' width='479' cellspacing='0' cellpadding='0'>";
  }
  echo "
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
            <td valign='top'>$texto_01</td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td colspan='3' height='20'>&nbsp;</td>
          </tr>";
  
  # Cuando se especifica un resumen se muestra la barra lateral derecha en el contenido
  if(!empty($resumen)) {
    echo "
          </table>
        </td>
        <td width='241' valign='top' class='content_sidebar'><table border='".D_BORDES."' width='100%' valign='top' cellspacing='0' cellpadding='0'>
            <tr>
              <td width='10'>&nbsp;</td>
              <td>&nbsp;</td>
              <td width='10'>&nbsp;</td>
            </tr>
            <tr>
              <td>&nbsp;</td>
              <td><b>$resumen</b></td>
              <td>&nbsp;</td>
            </tr>
            <tr>
              <td>&nbsp;</td>
              <td valign='top'><br>$texto_02</td>
              <td>&nbsp;</td>
            </tr>
            <tr>
              <td colspan='3'>&nbsp;</td>
            </tr>
          </table></td>
      </tr>";
  }
  echo "
    </table>";
  
  # Footer
  PresentaFooter( );
  
?>