<?php
# Libreria de funciones
require '../../lib/general.inc.php';


# Verifica que exista una sesion valida en el cookie y la resetea
$fl_usuario = ValidaSesion();

# Recibe parametros

$fl_pais = RecibeParametroNumerico('fl_pais');
$fl_estado=RecibeParametroNumerico('fl_estado');

		 
		    $Query = "SELECT CONCAT(ds_provincia,' - ',ds_abreviada), fl_provincia FROM k_provincias WHERE fl_pais=$fl_pais ";
			Forma_CampoSelectBD(ObtenEtiqueta(1578), False, 'fl_estado', $Query, $fl_estado, !empty($fl_estado_err)?$fl_estado_err:NULL, True,'', 'right', 'col col-sm-4', 'col col-sm-6');
		
		  
 echo "
   <script>
   pageSetUp();
   </script>";

?>


