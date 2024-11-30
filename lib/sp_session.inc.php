<?php

#
# MRA: Funciones para manejo de sesiones
#


# Nombre del cookie
define("FORM_SESS", "form_sess");


# Genera una nueva sesion
function SP_GeneraSesion( ) {
  $str = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890";
  $id_sesion = "";
  
  for($i = 0; $i < 32; $i++)
    $id_sesion .= substr($str, rand(0,62), 1);
  $id_sesion = sha256("form_sess para ".$id_sesion);
  SP_ActualizaSesion($id_sesion);
  
  return $id_sesion;
}


# Crea o actualiza cookie con numero de sesion, expira en X tiempo, scope todo el sitio
function SP_ActualizaSesion($p_sesion) {
  
  # Cookie de sesion
  setcookie(FORM_SESS, $p_sesion, time( )+(60*60*4), "/");
}


# Recupera la clave de sesion del cookie
function SP_RecuperaSesion( ) {
  
  $id_sesion = $_COOKIE[FORM_SESS];
  
  return $id_sesion;
}

?>