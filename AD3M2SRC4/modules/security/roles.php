<?php
  
  # La libreria de funciones
  require '../../lib/general.inc.php';
  
  # Recibe parametros
  $criterio = RecibeParametroHTML('criterio');
  $actual = RecibeParametroNumerico('actual');
  
  # Consulta para el listado
  $Query  = "SELECT a.fl_perfil, nb_perfil '".ETQ_NOMBRE."', ds_perfil '".ETQ_DESCRIPCION."', ";
  $Query .= "(SELECT COUNT(1) FROM c_usuario b WHERE a.fl_perfil=b.fl_perfil) '".ObtenEtiqueta(106)."|right' ";
  $Query .= "FROM c_perfil a ";
  $Query .= "WHERE fg_admon='1' ";
  if(!empty($criterio)) {
    switch($actual) {
      case 1: $Query .= "AND nb_perfil LIKE '%$criterio%' "; break;
      case 2: $Query .= "AND ds_perfil LIKE '%$criterio%' "; break;
      default: $Query .= "AND (nb_perfil LIKE '%$criterio%' OR ds_perfil LIKE '%$criterio%') ";
    }
  }
  $Query .= "ORDER BY nb_perfil";
  
  # Muestra pagina de listado
  PresentaPaginaListado(FUNC_PERFILES, $Query, TB_LN_IUD, True, False, array(ETQ_NOMBRE, ETQ_DESCRIPCION));
  
?>