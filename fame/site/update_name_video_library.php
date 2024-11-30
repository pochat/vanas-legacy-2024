<?php 
# Libreria de funciones
require("../lib/self_general.php");

  # get the list of items id separated by cama (,)
  $nb_video = $_POST['value'];
  $fl_video_contenido = $_REQUEST['name'];

  
  
  
  
  $Query="UPDATE k_video_contenido_sp SET  ds_title_vid='$nb_video'  WHERE fl_video_contenido_sp=$fl_video_contenido ";
  EjecutaQuery($Query);
  
  
  
?>