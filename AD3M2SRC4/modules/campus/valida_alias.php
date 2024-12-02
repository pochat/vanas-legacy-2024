<?php
  # Libreria de funciones
  require '../../lib/general.inc.php';

  # Verifica que exista una sesion valida en el cookie y la resetea
  $fl_usuario = ValidaSesion();
  
  # Recibe parametros
  $ds_alias = RecibeParametroHTML('ds_alias');
  $fl_usuario = RecibeParametroNumerico('fl_usuario');
  
  # No puede existir dos personas con el mismo alias
  $row = RecuperaValor("SELECT 1 FROM c_usuario WHERE fl_usuario!=$fl_usuario AND ds_alias='".$ds_alias."'");
  $alias_existe = $row[0];
  if(!empty($alias_existe))
    $fg_error = true;
  else
    $fg_error = false;
  
  $result["resultado"] = array("fg_error" => $fg_error, "QUERY"=>"SELECT 1 FROM c_usuario WHERE fl_usuario!=$fl_usuario AND ds_alias='".$ds_alias."'");
  echo json_encode((Object)$result);
?>