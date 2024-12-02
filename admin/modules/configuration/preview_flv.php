<?php
  
  # Libreria de funciones
  require("../../../lib/sp_general.inc.php");
  
  # Recibe parametros
  $archivo = $_GET['archivo'];
  $vid_width_01   = ObtenConfiguracion(13);
  $vid_height_01  = ObtenConfiguracion(14);
  
  # Inicia cuerpo de la pagina
  PresentaInicioPagina(False, False, False);
  echo "
  <table border='".D_BORDES."' width='100%' cellspacing='0' cellpadding='0'>
    <tr>
      <td align='center'>
        <br>
        <br>\n";
  PresentaVideo($archivo, $vid_width_01, $vid_height_01);
  echo "
      </td>
    </tr>
    <tr>
      <td align='center' class='default'>
        <br>
        <input type='button' id='buttons' value='".ObtenEtiqueta(74)."' onClick='javascript:window.close();'>
      </td>
    </tr>
   </table>
</body>
</html>";
  
?>