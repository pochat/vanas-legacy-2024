<?php
  
  # Libreria de funciones
  require("../modules/common/lib/cam_general.inc.php");
  require_once('composer/vendor/autoload.php'); // Envío de correos
  
  
  # Recibe parametros
  $ds_first_name= RecibeParametroHTML('ds_firts_name');
  $ds_last_name = RecibeParametroHTML('ds_last_name');
  $ds_email = RecibeParametroHTML('email');
  $fg_aceptar = RecibeParametroBinario('fg_aceptar');
  

  
  # Crea cookie con identificador de sesion y redirige al home del sistema
 // ActualizaSesion($cl_sesion, $fg_admon);
  header("Location: ".$pag);
  
?>