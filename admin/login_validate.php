<?php
  
  # Libreria general de funciones
  require 'lib/general.inc.php';
  
  # Recibe parametros
  $ds_login = RecibeParametroHTML('ds_login');
  $ds_password = RecibeParametroHTML('ds_password');
  
  # Valida el usuario y la contrasena
  $ds_password = sha256($ds_password);
  $row = RecuperaValor("SELECT count(1) FROM c_usuario WHERE ds_login='$ds_login' AND ds_password='$ds_password'");
  if($row[0] != 1) {
    # -1: Usuario o contrase&ntilde;a inv&aacute;lida.
    header("Location: ".SESION_INVALIDO);
    exit;
  }
  
  # Recupera identificador de sesion y estado del usuario
  $row = RecuperaValor("SELECT cl_sesion, fg_activo, fl_perfil FROM c_usuario WHERE ds_login='$ds_login'");
  $cl_sesion = $row[0];
  $fg_activo = $row[1];
  $fl_perfil = $row[2];
  
  # Recupera tipo de perfil del usuario
  $row = RecuperaValor("SELECT fg_admon FROM c_perfil WHERE fl_perfil=$fl_perfil");
  $fg_admon = $row[0];
  
  # Valida que el usuario este activo
  if($fg_activo <> 1 OR $fg_admon <> 1) {
    # -4: El usuario no est&aacute; activo.
    header("Location: ".SESION_INACTIVO);
    exit;
  }
  
  # Actualiza estadisticas de acceso del usuario
  EjecutaQuery("UPDATE c_usuario SET fe_ultacc=CURRENT_TIMESTAMP, no_accesos=no_accesos+1 WHERE cl_sesion='$cl_sesion'");
  
  # Crea cookie con identificador de sesion y redirige al home del sistema
  ActualizaSesion($cl_sesion);
  header("Location: ".PAGINA_INICIO);
  
?>