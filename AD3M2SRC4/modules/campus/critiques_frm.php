<?php
  
  # Libreria de funciones
  require '../../lib/general.inc.php';
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  ValidaSesion( );
  
  # Recibe parametros
  $clave = RecibeParametroNumerico('clave');  
  
  $Query = "SELECT ds_critica_animacion
            FROM k_entrega_semanal 
            WHERE fl_entrega_semanal = $clave";

  $row = RecuperaValor($Query);
?>  
  <script language="javascript">
    var arch = '<?php echo $row[0]; ?>';
    if (arch == "") 
    {
      history.back();
    }
    else
    {
      window.open("preview_critique_flv.php?video="+arch);
      history.back();
    }
  </script>