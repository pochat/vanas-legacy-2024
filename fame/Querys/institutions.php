<?php

# Libreria de funciones 
require("../lib/self_general.php");

# Verifica que exista una sesion valida en el cookie y la resetea
$fl_usuario = ValidaSesion(False, 0, True);

# Verifica que el usuario tenga permiso de usar esta funcion
if (!ValidaPermisoSelf(FUNC_SELF)) {
  MuestraPaginaError(ERR_SIN_PERMISO);
  exit;
}
# Obtenemo el instituto
$fl_instituto = ObtenInstituto($fl_usuario);
$fl_perfil_sp = ObtenPerfilUsuario($fl_usuario);

$Query = "SELECT * FROM c_instituto WHERE fl_instituto_rector=$fl_instituto ";
$rs = EjecutaQuery($Query);
$numeroderegistros = CuentaRegistros($rs);

echo '{"data": [';

for ($i = 1; $row = RecuperaRegistro($rs); $i++) {
  $fl_instituto = $row['fl_instituto'];
  $ds_instituto = $row['ds_instituto'];
  $school_id=$row['school_id'];
  $ds_foto=$row['ds_foto'];
  $fg_privacy=$row['fg_privacy'];
  if($fg_privacy)
      $checked='checked=\'checked\'';
  else
      $checked="";

  if($ds_foto)
  $ds_ruta_foto=PATH_SELF_UPLOADS."/".$fl_instituto ."/".$ds_foto;
  else
      $ds_ruta_foto=PATH_SELF_IMG."/Partner_School_Logo.jpg";
     
    echo '
    {
      "checked": "<label class=\'checkbox no-padding no-margin\'><input class=\'checkbox\' type=\'checkbox\'  '.$checked.'  id=\'ch_' . $fl_instituto . '\' value=\'' . $fl_instituto . '\'  onclick=\'HabilitarInstituto('.$fl_instituto.')\' ><span></span></label><iput type=\'hidden\' id=\'fl_instituto'.$i.'\' name=\'fl_instituto'.$i.'\' value=\''.$fl_instituto.'\'  >",
      
      "img": "<div class=\'project-members\'><input type=\'checkbox\' id=\'user_' . $i . '\' class=\'checkbox\' ><a href=\'javascript:void(0);\' rel=\'tooltip\' data-placement=\'top\' data-html=\'true\' data-original-title=\'' . $ds_instituto . '\'><img src=\'' . $ds_ruta_foto . '\' class=\'' . (!empty($active)?$active:NULL) . '\' style=\'width:25px;\'></a> </div>",
      "name": "<a href=\'index.php#site/institutions_details.php?c='.$fl_instituto.'\' rel=\'tooltip\' >' . $ds_instituto . '</a> ",
     
      "activity": "<a href=\'javascript:url('.$fl_instituto.');\' class=\'btn-u btn-u-dark-blue\'> Login <i class=\'fa fa-external-link\' aria-hidden=\'true\'></i></a>",
      "extra":""
    }';
    if($i<=($numeroderegistros-1))
        echo ",";
      else
        echo "";


 
}
echo ']}';
# End of MAIN FOR LOOP only for PRODUCCION
// $tablaJson = '{"data": ['.$registros.']}';
# Entrega el resultado para mostrar en la tabla solo PRODUCCION
// echo $tablaJson;
?>
