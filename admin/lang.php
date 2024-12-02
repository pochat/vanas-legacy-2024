<?php
  
  # Libreria general de funciones
  require 'lib/general.inc.php';
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  ValidaSesion( );
  
  # Recibe parametros
  $origen = RecibeParametroHTML('origen');
  $clave = RecibeParametroNumerico('clave');
  if(empty($origen))
    $origen = PAGINA_INICIO;
    
  # Crea cookie con identificador de sesion y redirige al home del sistema
  CambiaIdioma( );
  
  # Regresa a la pagina de origen
  if(!empty($clave))
    echo "<html><body><form name='lang' method='post' action='$origen'>
  <input type='hidden' name='clave' value='$clave'>
</form>
<script>
  document.lang.submit();
</script></body></html>";
  else
    header("Location: ".$origen);
  
?>