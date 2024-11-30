<?php 

  # Obten el intituto del administrador
  function ObtenInstituto($p_admin){
    $row = RecuperaValor("SELECT fl_instituto FROM c_usuario_sp WHERE fl_usuario_sp=$p_admin");
    return $row[0];
  }
  
  # Funcion para obtener el numero de licencias dependiendo del administrador
  function ObtenNumLicencias($p_admin){
    $row = RecuperaValor("SELECT no_licencias FROM k_current_plan WHERE fl_usuario_sp=$p_admin");
    return $row[0];
  }
  
  # Funcion para obtener el numero de usuarios por escuela
  function ObtenNumeroUserInst($p_instituto){
    $row = RecuperaValor("SELECT COUNT(*) FROM c_usuario_sp WHERE fl_instituto=$p_instituto");
    return $row[0];
  }
?>