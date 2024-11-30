<?php
  
  # Libreria de funciones
  require("../common/lib/cam_general.inc.php");
  
  # Recibe parametros
  $fl_comentario = RecibeParametroNumerico('fl_comentario');
  
  # Elimina comentario del post
  EjecutaQuery("DELETE FROM k_f_comentario WHERE fl_comentario=$fl_comentario");
  
?>