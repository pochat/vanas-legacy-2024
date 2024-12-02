<?php
  
  # Libreria de funciones
  require("../../../lib/sp_general.inc.php");
  
  # Recibe parametros
  $archivo = $_GET['archivo'];
  
  # Inicia cuerpo de la pagina
  PresentaInicioPagina(False, False, False);
  echo "
  <table border='".D_BORDES."' width='100%' cellspacing='0' cellpadding='0'>
    <tr>
      <td align='center'>
        <br>
        <br>\n";
  PresentaVideoFP($archivo);
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
