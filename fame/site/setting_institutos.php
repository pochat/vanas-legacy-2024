<?php
# Libreria de funciones
require("../lib/self_general.php");
 
# Verifica que exista una sesion valida en el cookie y la resetea
$fl_usuario = ValidaSesion(False,0, True);
$fl_perfil = ObtenPerfilUsuario($fl_usuario);
$fl_instituto = ObtenInstituto($fl_usuario);

# Verifica que el usuario tenga permiso de usar esta funcion
if(!ValidaPermisoSelf(FUNC_SELF)) {  
MuestraPaginaError(ERR_SIN_PERMISO);
exit;
}
$_POST[''];
$fg_valor=RecibeParametroNumerico('valor');
$fl_instituto=RecibeParametroNumerico('fl_instituto');

$Query="UPDATE c_instituto SET fg_privacy='$fg_valor' WHERE fl_instituto=$fl_instituto ";
EjecutaQuery($Query);

?>			  