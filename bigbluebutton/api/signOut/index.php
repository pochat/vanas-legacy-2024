<?php
  
  # Libreria de funciones
  require("../../../modules/common/lib/cam_general.inc.php");
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  $fl_usuario = ValidaSesion(False);
  
  # Identifica el tipo de usuario
  $fl_perfil = ObtenPerfilUsuario($fl_usuario);
  
  # Redirecciona a la pagina inicial de cada tipo
  $pag = SESION_EXPIRADA;
  if($fl_perfil == PFL_ESTUDIANTE)
    $pag = PAGINA_INI_ALU;
  if($fl_perfil == PFL_MAESTRO)
    $pag = PAGINA_INI_MAE;
  
  # Redirecciona a la pagina inicial de cada tipo
  header("Location: ".$pag);
  
?>