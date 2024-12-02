<?php 
  # Libreria de funciones
  require '../../lib/general.inc.php';

  # get the list of items id separated by cama (,)
  $list_order = $_POST['list_order'];
  $clave = $_REQUEST['clave'];

  # convert the string list to an array
  $list = explode(',' , $list_order);
  $i = 1 ;
  foreach($list as $id) {
    EjecutaQuery("UPDATE k_criterio_curso SET no_orden = $i WHERE fl_criterio = $id AND fl_programa = $clave");
    $i++ ;
  }
?>