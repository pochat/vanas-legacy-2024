<?php
  # Librerias
  require '../../lib/general.inc.php';
  
  # Consulta para el listado
  $Query  = "SELECT  CONCAT( a.ds_nombres,' ', a.ds_apaterno ), c.nb_perfil, ";
  $Query .= "CASE a.fg_activo WHEN 1 THEN 'Active' ELSE 'Inactive' END status, '2 days ago'  'last_login', '0.01 GB' 'usage' ";
  $Query .= "FROM c_usuario a,  c_perfil c WHERE a.fl_perfil = c.fl_perfil ";
  $rs = EjecutaQuery($Query);
  $registros = CuentaRegistros($rs);
?>
{
  "data": [
  <?php 
  for($i=1;$row=RecuperaRegistro($rs);$i++){
    $ds_ruta_foto = "";
    $ds_nombre = $row[0];
    $nb_perfil = $row[1];
    $status = $row[2];
    if($status == "Active")
      $color = "success";
    else
      $color = "danger";
    $last_login = $row[3];
    $usage = $row[4];
    # Porel momento ponemos la imagen de algunos
    $ruta_foto = PATH_ALU_IMAGES."/avatars/ak9207150322_ava19970.jpg";
    echo '
    {
      "id": "<div class=\'project-members\'><a href=\'#\' rel=\'tooltip\' data-placement=\'top\' data-html=\'true\' data-original-title=\''.$ds_nombre.'\'><img src=\''.$ruta_foto.'\' class=\'online\' alt=\'user\' width=\'30px\' height=\'30px\'></a></div>",
      "name": "'.$ds_nombre.'",
      "perfil": "'.$nb_perfil.'",
      "status": "<span class=\'label label-'.$color.'\'>'.$status.'</span>",
      "lastlogin": "'.$last_login.'",
      "usange": "'.$last_login.'"
    }';
    if($i<=($registros-1))
      echo ",";
    else
      echo "";      
  }
  ?>
  ]
}