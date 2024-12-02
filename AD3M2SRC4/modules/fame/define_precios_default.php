 <?php
  
  # Libreria de funciones
  require '../../lib/general.inc.php';
  
  # Recibe la clave
  $fl_instituto=RecibeParametroNumerico('fl_instituto');
  $checke=RecibeParametroHTML('checke');
  
  #Actualizamos este instituto como precio default y los demas seran en 0
  if($checke){
	  
	  $Query="UPDATE c_instituto SET fg_princing_default='1' WHERE fl_instituto=$fl_instituto  ";
	  EjecutaQuery($Query);
	  
	  $Query="UPDATE c_instituto SET fg_princing_default='0' WHERE fl_instituto<>$fl_instituto ";  
	  EjecutaQuery($Query);
	  
  }
  
  
?>