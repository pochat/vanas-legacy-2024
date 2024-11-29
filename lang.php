<?php
  
  # Libreria general de funciones
  require 'lib/sp_general.inc.php';
  
  # Recibe parametros
  $origen = RecibeParametroHTML('origen');
  $contenido = RecibeParametroNumerico('contenido');
  if(empty($origen))
    $origen = PAGINA_INICIO;
    
  # Crea cookie con identificador de sesion y redirige al home del sistema
  CambiaIdioma( );
  
  # Regresa a la pagina de origen
  if(!empty($contenido))
    echo "<html><body><form name='lang' method='post' action='$origen'>
  <input type='hidden' name='contenido' value='$contenido'>
</form>
<script>
  document.lang.submit();
</script></body></html>";
  else
    header("Location: ".$origen);
  
?>