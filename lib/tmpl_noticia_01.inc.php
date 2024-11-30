<?php
  
  # 21 - Contenido estandar
  # Un bloque de texto
  
  # Variables usadas en este template
  $titulo         = EscogeIdioma($nb_titulo, $tr_titulo);
  $texto_01       = EscogeIdioma($ds_contenido[1], $tr_contenido[1]);
  $img_archivo_01 = EscogeIdioma($nb_archivo_i[1], $tr_archivo_i[1]);
  $img_caption_01 = EscogeIdioma($ds_caption_i[1], $tr_caption_i[1]);
  $img_alt_01     = EscogeIdioma($ds_alt_i[1], $tr_alt_i[1]);
  $img_liga_01    = $ds_liga_i[1];
  $img_archivo_02 = EscogeIdioma($nb_archivo_i[2], $tr_archivo_i[2]);
  $img_caption_02 = EscogeIdioma($ds_caption_i[2], $tr_caption_i[2]);
  $img_alt_02     = EscogeIdioma($ds_alt_i[2], $tr_alt_i[2]);
  $img_liga_02    = $ds_liga_i[2];
  
  # Si no se especifica la imagen, toma la de default
  if(empty($img_archivo_01))
    $img_archivo_01 = IMG_DEFAULT;
  
  # Header
  PresentaHeader($fl_funcion, True);  
  
  # Cuerpo del Home
  echo "
    <img src='".SP_IMAGES."/$img_archivo_01' width='720' height='150' border='0'>
    <table border='".D_BORDES."' width='720' cellspacing='0' cellpadding='0'>
      <tr>
        <td width='20' height='20'>&nbsp;</td>
        <td>&nbsp;</td>
        <td width='20'>&nbsp;</td>
      </tr>
      <tr>
        <td height='30'>&nbsp;</td>
        <td><b>$titulo</b></td>
        <td>&nbsp;</td>
      </tr>";
  if(!empty($img_archivo_02)) {
    echo "
      <tr><td colspan='3'>&nbsp;</td></tr>
      <tr>
        <td>&nbsp;</td>
        <td align='left'><img src='".SP_IMAGES."/$img_archivo_02' width='".ObtenConfiguracion(17)."' border='0' /></td>
        <td>&nbsp;</td>
      </tr>
      <tr><td colspan='3'>&nbsp;</td></tr>";
  }
  else {
    echo "
      <tr><td colspan='3'>&nbsp;</td></tr>
      <tr>
        <td>&nbsp;</td>
        <td align='left'><img src='".SP_IMAGES."/".NEWS_IMG_DEF."' width='".ObtenConfiguracion(15)."' height='".ObtenConfiguracion(16)."' /></td>
        <td>&nbsp;</td>
      </tr>
      <tr><td colspan='3'>&nbsp;</td></tr>";
  }
  echo "
      <tr>
        <td>&nbsp;</td>
        <td>$texto_01</td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td colspan='3'>&nbsp;</td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td><a href='".PAGINA_SECCION."?seccion=".MENU_NOTICIAS."' class='links_news'>".ObtenEtiqueta(69)."</a></td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td colspan='3' height='20'>&nbsp;</td>
      </tr>
    </table>\n";
  
  # Footer
  PresentaFooter( );
  
?>