<?php 
# Libreria de funciones
require("../lib/self_general.php");

  # get the list of items id separated by cama (,)
  $nb_playlist = $_POST['value'];
  $fl_playlist = $_REQUEST['name'];

  
  
  
  
  $Query="UPDATE c_playlist SET  nb_playlist='$nb_playlist'  WHERE fl_playlist=$fl_playlist ";
  EjecutaQuery($Query);
  
  
  
?>