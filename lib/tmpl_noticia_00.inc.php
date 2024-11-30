<?php
  
  # 20 - Caratula de Noticias
  # Caratula con lista de noticias
  
  # Variables usadas en este template
  $titulo         = EscogeIdioma($nb_titulo, $tr_titulo);
  $img_archivo_01 = EscogeIdioma($nb_archivo_i[1], $tr_archivo_i[1]);
  $img_caption_01 = EscogeIdioma($ds_caption_i[1], $tr_caption_i[1]);
  $img_alt_01     = EscogeIdioma($ds_alt_i[1], $tr_alt_i[1]);
  $img_liga_01    = $ds_liga_i[1];
  
  # Si no se especifica la imagen, toma la de default
  if(empty($img_archivo_01))
    $img_archivo_01 = IMG_DEFAULT;
  
  # Recupera los contenidos que se van a mostrar para esta seccion
  $cols = 0;
  $rengs = 0;
  $Query  = "SELECT fl_contenido, nb_titulo, tr_titulo, ds_resumen, tr_resumen ";
  $Query .= "FROM c_contenido ";
  $Query .= "WHERE fl_funcion=$fl_funcion ";
  $Query .= "AND fg_activo=1 ";
  $Query .= "AND cl_template NOT IN(".TMPL_CARATULA.") ";
  $Query .= "AND (fe_ini IS NULL OR fe_ini <= CURRENT_TIMESTAMP) ";
  $Query .= "AND (fe_fin IS NULL OR DATE_ADD(fe_fin, INTERVAL 1 DAY) >= CURRENT_TIMESTAMP) ";
  $Query .= "ORDER BY ";
  if($fg_multiple == 1) { // Si la seccion permite multiples contenidos aplica el tipo de ordenamiento de la seccion
    switch($fg_tipo_orden) {
      case 'A': $Query .= "fe_evento"; break;      // Fecha ascendente
      case 'D': $Query .= "fe_evento DESC"; break; // Fecha descendente
      case 'T': $Query .= "nb_titulo"; break;      // Titulo
      default: $Query .= "no_orden";               // Numero de orden
    }
  }
  else // Si la seccion permite solo un contenido y hay mas de uno, se muestra el que tenga fecha de publicacion mas reciente
    $Query .= "fe_ini DESC";
  $rs = EjecutaQuery($Query);
  for($tot_regs = 0; $row = RecuperaRegistro($rs); $tot_regs++) {
    $nb_titulo_m[$tot_regs] = str_uso_normal(EscogeIdioma($row[1], $row[2]));
    $ds_resumen_m[$tot_regs] = str_uso_normal(EscogeIdioma($row[3], $row[4]));
    $Query  = "SELECT ds_caption, tr_caption, nb_archivo, tr_archivo, ds_alt, tr_alt ";
    $Query .= "FROM k_imagen_dinamica ";
    $Query .= "WHERE fl_contenido=$row[0] ";
    $Query .= "AND no_orden=2";
    $row2 = RecuperaValor($Query);
    $ds_caption_m[$tot_regs] = str_uso_normal(EscogeIdioma($row2[0], $row2[1]));
    $nb_archivo_m[$tot_regs] = str_uso_normal(EscogeIdioma($row2[2], $row2[3]));
    $ds_alt_m[$tot_regs] = str_uso_normal(EscogeIdioma($row2[4], $row2[5]));
    $ds_liga_m[$tot_regs] = PAGINA_CONTENIDO."?contenido=$row[0]";
  }
  if($tot_regs > 0) {
    $cols = 4;
    $rengs = (int) ceil($tot_regs/$cols);
  }
  
  # Header
  PresentaHeader(0, True);
  
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
      </tr>
      <tr><td colspan='3'>&nbsp;</td></tr>
      <tr>
        <td>&nbsp;</td>
        <td>
          <table width='100%' border='".D_BORDES."' cellspacing='0' cellpadding='0'>";
  for($i = 0; $i < $tot_regs; $i++) {
    if($i % 2 == 0)
      $clase = "row_highlight_1";
    else
      $clase = "row_highlight_2";
    echo "
            <tr>
              <td class='$clase'><table width='100%' border='".D_BORDES."' cellspacing='0' cellpadding='0'>
                <tr>
                  <td width='".ObtenConfiguracion(15)."' valign='middle' align='center'>";
    if(!empty($nb_archivo_m[$i]))
      echo "<a href='$ds_liga_m[$i]'><img src='".SP_THUMBS."/$nb_archivo_m[$i]' width='".ObtenConfiguracion(15)."' border='0'/></a>";
    else
      echo "<a href='$ds_liga_m[$i]'><img src='".SP_IMAGES."/".NEWS_THUMB_DEF."' border='0'/></a>";
    echo "</td>
                  <td width='10'>&nbsp;</td>
                  <td align='left' valign='middle'><p><a href='$ds_liga_m[$i]'>$nb_titulo_m[$i]</a></p>
                    <p>$ds_resumen_m[$i]</p></td>
                </tr>
              </table></td>
            </tr>";
  }
  echo "
          </table>
        </td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td colspan='3' height='20'>&nbsp;</td>
      </tr>
    </table>\n";

  # Footer
  PresentaFooter( );
  
?>