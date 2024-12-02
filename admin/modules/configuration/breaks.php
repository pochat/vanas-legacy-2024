<?php
  
  # La libreria de funciones
  require '../../lib/general.inc.php';
  
  # Recibe parametros
  $criterio = RecibeParametroHTML('criterio');
  $actual = RecibeParametroNumerico('actual');
  
  # Consulta para el listado
  $Query  = "SELECT fl_break, ds_break '".ObtenEtiqueta(650)."', ";
  $Query .= ConsultaFechaBD('fe_ini', FMT_FECHA)." '".ObtenEtiqueta(371)."', ";
  $Query .= ConsultaFechaBD('fe_fin', FMT_FECHA)." '".ObtenEtiqueta(513)."',  DATEDIFF(fe_fin, fe_ini) + 1 as '".ObtenEtiqueta(700)."|right' ";
  $Query .= "FROM c_break ";
  if(!empty($criterio)) {
    switch($actual) {
      case 1: $Query .= "WHERE ds_break LIKE '%$criterio%' "; break;
      default:
        $Query .= "WHERE ds_break LIKE '%$criterio%' ";
    }
  }
 $Query .= "ORDER BY fe_ini";
  
  # Muestra pagina de listado
  $campos = array(ObtenEtiqueta(650), ObtenEtiqueta(371), ObtenEtiqueta(513), ObtenEtiqueta(700));
  PresentaPaginaListado(FUNC_BREAKS, $Query, TB_LN_IUD, True, False, $campos);
  
?>