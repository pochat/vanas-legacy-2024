<?php 
# Libreria de funciones
require("../lib/self_general.php");

  # get the list of items id separated by cama (,)
  $fl_grupo_fame = $_POST['fl_grupo_fame'];
  
  EjecutaQuery('DELETE FROM c_grupo_fame WHERE fl_grupo_fame='.$fl_grupo_fame.' ');
?>