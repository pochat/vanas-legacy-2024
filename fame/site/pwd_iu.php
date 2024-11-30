<?php
	
 
  	# Libreria de funciones	
	require("../lib/self_general.php");
	
 # Verifica que exista una sesion valida en el cookie y la resetea
  $fl_usuario = ValidaSesion(False,0, True);
  $fl_perfil = ObtenPerfilUsuario($fl_usuario);
  
  # Recibe la clave
  $clave = RecibeParametroNumerico('clave');

	# Recibe parametros
  $ds_password = RecibeParametroHTML('ds_password');
  $ds_password_conf = RecibeParametroHTML('ds_password_conf');
	
	
  # Actualiza el password del usuario
  $ds_password = sha256($ds_password);
  $Query  = "UPDATE c_usuario SET ds_password='$ds_password' ";
  echo$Query .= "WHERE fl_usuario=$clave";
  EjecutaQuery($Query);
  
?>