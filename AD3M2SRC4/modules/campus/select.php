<?php
  # La libreria de funciones
  require '../../lib/general.inc.php';
  
  # Recibe parametros
  $fg_insert = RecibeParametroBinario('fg_insert');
  $fl_registro=RecibeParametroNumerico('fl_registro');
  $nb_programa=RecibeParametroHTML('nb_programa');
  $fg_borra=RecibeParametroNumerico('fg_borra');
  
  if($fg_borra==1){
        EjecutaQuery("DELETE FROM c_export_cvs WHERE  nb_programa='$nb_programa' ");
  
  
  }else{	
  
		  if($fg_insert){
			 $Query="INSERT INTO c_export_cvs (fl_registro,nb_programa)";
			 $Query.="VALUES($fl_registro,'$nb_programa')";
			 EjecutaQuery($Query);
		  }else{
			 EjecutaQuery("DELETE FROM c_export_cvs WHERE fl_registro=$fl_registro AND nb_programa='$nb_programa' ");
		  
		  
		  }
  
 }  
  
?>