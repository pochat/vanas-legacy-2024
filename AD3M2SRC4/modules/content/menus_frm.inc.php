<?php
  
  echo "
<script src='".PATH_JS."/frmSubmenus.js.php'></script>

<div id='frmSubmenus'>
  <form>
  <center>
  <table border='".D_BORDES."' width='100%'>
    <tr><td width='30%'>&nbsp;</td><td id='validateTips' align='left' class='css_msg_error'>&nbsp;</td></tr>\n";
  Forma_CampoTexto(ObtenEtiqueta(164), True, 'nb_submenu', '', 50, 30);
  Forma_CampoTexto(ETQ_TRADUCCION, False, 'tr_submenu', '', 50, 30);
  Forma_Espacio( );
  Forma_CampoTexto(ETQ_DESCRIPCION, False, 'ds_submenu', '', 100, 50);
  Forma_CampoTexto(ETQ_ORDEN, True, 'no_orden', '', 3, 5);
  Forma_CampoCheckbox(ObtenEtiqueta(165), 'fg_menu', '');
  Forma_Espacio( );
  Forma_CampoInfo(ObtenEtiqueta(161), '<div id=ds_fg_fijo></div>');
  Forma_CampoOculto('ds_fg_fijo', '');
  Forma_CampoOculto('fl_parent', '');
  echo "
  </table>
  </center>
  </form>
</div>";
  
?>