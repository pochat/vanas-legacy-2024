<?php

# Libreria de funciones
require_once("/lib/self_general.php");
// require_once("../modules/common/lib/cam_general.inc.php");

# Verifica que exista una sesion valida en el cookie y la resetea
// $fl_usuario = ValidaSesion(False);

# Verifica que el usuario tenga permiso de usar esta funcion
// if(!ValidaPermisoCampus(FUNC_ALUMNOS)) {
  // MuestraPaginaError(ERR_SIN_PERMISO);
  // exit;
// }
  header("location:".PATH_SELF."/site/home.php");
?>