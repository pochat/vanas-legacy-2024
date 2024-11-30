<?php
  
  # Libreria de funciones
  require("../common/lib/cam_general.inc.php");
  
  # Recibe parametros
  $fl_usuario = RecibeParametroNumerico('fl_usuario');
  $fl_post = RecibeParametroNumerico('fl_post');
  
  # Recupera el tema del post
  $row = RecuperaValor("SELECT fl_tema FROM k_f_post WHERE fl_post=$fl_post AND fl_usuario=$fl_usuario");
  $fl_tema = $row[0];
  
  # Elimina el post
  EjecutaQuery("DELETE FROM k_f_post WHERE fl_post=$fl_post AND fl_usuario=$fl_usuario");
  
  # Elimina comentarios del post
  EjecutaQuery("DELETE FROM k_f_comentario WHERE fl_post=$fl_post");
  
  # Actualiza contador de posts
  EjecutaQuery("UPDATE c_f_tema SET no_posts=no_posts-1 WHERE fl_tema=$fl_tema");
  
  # Actualiza contador de temas nuevos por usuario
  EjecutaQuery("UPDATE k_f_usu_tema SET no_posts=no_posts-1 WHERE fl_tema=$fl_tema AND fl_usuario<>$fl_usuario AND no_posts > 0");
  
?>