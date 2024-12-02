<?php

  # La libreria de funciones
  require '../../lib/general.inc.php';
  
  # Recibe parametros
  $criterio = RecibeParametroHTML('criterio');
  $actual = RecibeParametroNumerico('actual');

  $Query  = "SELECT fl_backups,ds_archivo '".ObtenEtiqueta(725)."', ";
  $Query .= "".ConsultaFechaBD('fe_ini_back',FMT_FECHA)." '".ObtenEtiqueta(60)."',";
  $Query .= "".ConsultaFechaBD('fe_fin_back',FMT_FECHA)." '".ObtenEtiqueta(513)."' ";
  $Query .= "FROM c_backups WHERE 1=1 ";
  if(!empty($criterio)) {
    switch($actual) {
      case 1: $Query .= "AND ds_archivo LIKE '%$criterio%' "; break;
      case 2: $Query .= "AND fe_ini_back LIKE '%$criterio%' "; break;
      case 3: $Query .= "AND fe_fin_back LIKE '%$criterio%' "; break;
      default:
        $Query .= "AND ds_archivo LIKE '%$criterio%' OR ";
        $Query .= "fe_ini_back LIKE '%$criterio%' OR fe_fin_back LIKE '%$criterio%' ";
    }
  }
  $Query .= "ORDER BY fe_ini_back,fe_fin_back ";
  # Muestra pagina de listado
  PresentaPaginaListado(FUNC_CONTENIDOS, $Query, TB_LN_NUD, True, False, array(ObtenEtiqueta(725),ObtenEtiqueta(60),ObtenEtiqueta(513)));
  
?>
