<?php
  
  # Libreria de funciones
  require("../lib/sp_general.inc.php");
  require("../lib/sp_session.inc.php");
  // require("../lib/sp_forms.inc.php");
  require("lib/app_forms.inc.php");
  require("app_form.inc.php");

  $fl_usuario = RecibeParametroNumerico('fl_usuario');
  $opc = RecibeParametroNumerico('opc');
 
 
  $Query="UPDATE c_usuario SET ds_graduate_status=$opc,fe_graduate_status=CURRENT_TIMESTAMP WHERE fl_usuario=$fl_usuario ";
  EjecutaQuery($Query);
 

  echo "<br><br><div class='text-center'><h1 class='text-success' style='font-size:27px;'><i style='font-size:27px;' class='fa fa-check-circle-o' aria-hidden='true'></i> ".ObtenEtiqueta(2660)."</h1></div>";

?>
