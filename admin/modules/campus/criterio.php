<?php
  #Librerias
  require("../../lib/general.inc.php");
  
  ValidaSesion();

  #Recibe criterio
  $criterio = RecibeParametroHTML('criterio');
  $nuevo = RecibeParametroHTML('nuevo');
  $fe_limit = RecibeParametroHTML('opcion1');
  $opc_fechas = RecibeParametroHTML('opcion2');
  $fpagos1 = RecibeParametroHTML('fpagos1'); 
  $fpagos2 = RecibeParametroHTML('fpagos2'); 
  $fpagos3 = RecibeParametroHTML('fpagos3'); 
  $fpagos4 = RecibeParametroHTML('fpagos4'); 
  $fpagos5 = RecibeParametroHTML('fpagos5'); 
  $fpagos6 = RecibeParametroHTML('fpagos6'); 
  $fg_students = RecibeParametroHTML('fg_students');
  $fg_nstudents = RecibeParametroHTML('fg_nstudents');
  $fl_pais = RecibeParametroHTML('opcion3');
  $fg_earned_un = RecibeParametroHTML('fg_earned_un');
  $fg_activo_in = RecibeParametroHTML('fg_activo_in');
  $startdue = RecibeParametroHTML('startdue');
  $enddue = RecibeParametroHTML('enddue');
  $startdate = RecibeParametroHTML('startdate');
  $enddate = RecibeParametroHTML('enddate');
  $fg_detalle = RecibeParametroHTML('fg_detalle');
  $startdetalle = RecibeParametroHTML('startdetalle');
  $enddetalle = RecibeParametroHTML('enddetalle');
  
 
  # Regresa al detalle
  echo "<html><body><form name='datos' method='post' action='payments.php'>\n";
    Forma_CampoOculto('criterio', $criterio);
    Forma_CampoOculto('actual', '12');
    Forma_CampoOculto('nuevo', '0');
    Forma_CampoOculto('opcion1', $fe_limit);
    Forma_CampoOculto('opcion2', $opc_fechas);
    Forma_CampoOculto('fpagos1', $fpagos1);
    Forma_CampoOculto('fpagos2', $fpagos2);
    Forma_CampoOculto('fpagos3', $fpagos3);
    Forma_CampoOculto('fpago4', $fpagos4);
    Forma_CampoOculto('fpagos5', $fpagos5);
    Forma_CampoOculto('fpagos6', $fpagos6);
    Forma_CampoOculto('fg_students', $fg_students);
    Forma_CampoOculto('fg_nstudents', $fg_nstudents);
    Forma_CampoOculto('opcion3', $fl_pais);
    Forma_CampoOculto('fg_earned_un', $fg_earned_un);
    Forma_CampoOculto('fg_activo_in', $fg_activo_in);
    Forma_CampoOculto('startdue', $startdue);
    Forma_CampoOculto('enddue', $enddue);
    Forma_CampoOculto('startdate', $startdate);
    Forma_CampoOculto('enddate', $enddate);
    echo "\n</form>
  <script>
    document.datos.submit();
  </script></body></html>";

?>