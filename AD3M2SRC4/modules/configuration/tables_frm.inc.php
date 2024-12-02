<?php
  
  echo "
<script src='".PATH_JS."/frmColumnas.js.php'></script>

<div id='frmColumnas'>
  <form>
  <center>
  <table border='".D_BORDES."' width='100%'>
    <tr><td width='30%'>&nbsp;</td><td id='validateTips' align='left' class='css_msg_error'>&nbsp;</td></tr>\n";
  Forma_CampoInfo(ObtenEtiqueta(252), '<div id=ds_no_columna></div>');
  Forma_CampoOculto('no_columna', '');
  Forma_Espacio( );
  Forma_CampoTexto(ETQ_TITULO, True, 'nb_columna', '', 255, 30);
  Forma_CampoTexto(ETQ_TRADUCCION, False, 'tr_columna', '', 255, 30);
  Forma_Espacio( );
  $opc = array(ObtenEtiqueta(253), ObtenEtiqueta(254), ObtenEtiqueta(255));
  $val = array('L', 'C', 'R');
  Forma_CampoSelect(ObtenEtiqueta(251), 'fg_align', $opc, $val, '');
  Forma_CampoTexto(ObtenEtiqueta(218), False, 'no_width_c', '', 4, 5);
  echo "
  </table>
  </center>
  </form>
</div>";
  
?>