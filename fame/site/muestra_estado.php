<?php
# Libreria de funciones	
require("../lib/self_general.php");

# Verifica que exista una sesion valida en el cookie y la resetea
$fl_usuario = ValidaSesion(False,0, True);

# Recibe parametros
$fl_pais = RecibeParametroNumerico('fl_pais');
$fl_estado=RecibeParametroNumerico('fl_estado');


  if($fl_pais==38){
?>
            <label class="control-label" style="margin: 7px;"><b><?php echo ObtenEtiqueta(1139);?></b></label>
<?php 
		 
		    $Query = "SELECT CONCAT(ds_provincia,' - ',ds_abreviada), fl_provincia FROM k_provincias WHERE fl_pais=$fl_pais ";
			FAMECampoSelectBD(ObtenEtiqueta(287),'fl_estado', $Query, $fl_estado, 'select2', False, '', $p_valores='', $p_seleccionar_txt = 'Select', $p_seleccionar_val =0);       
					
		  
 echo "
   <script>
	$('#fl_estado').select2({
	});
   </script>";
  }else{
	 echo"<input type='hidden' name='fl_estado' id='fl_estado' value='' > "; 
	 
  }

?>


