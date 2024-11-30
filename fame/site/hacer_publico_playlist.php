<?php 
# Libreria de funciones
require("../lib/self_general.php");

  # get the list of items id separated by cama (,)
  $fl_play_list = $_POST['fl_play_list'];
  $fg_publico= $_POST['fg_publico'];
  

  EjecutaQuery("UPDATE c_playlist SET fg_editable ='$fg_publico' WHERE fl_playlist=$fl_play_list  ");

  
  
?>