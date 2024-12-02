<?php
  
  # La libreria de funciones
  require '../../lib/general.inc.php';
  
  # Recibe parametros
  $criterio = RecibeParametroHTML('criterio');
  $actual = RecibeParametroNumerico('actual');
  
  # Consulta para el listado 
  $Query  = "SELECT SUBSTRING_INDEX(fl_share_face, '_', -1),".ConsultaFechaBD('fe_share', FMT_FECHA)." '".ObtenEtiqueta(792)."',ds_ruta_entregable '".ObtenEtiqueta(793)."', ";
  $Query .= "CONCAT(ds_nombres,' ',ds_apaterno) '".ObtenEtiqueta(794)."', CONCAT(' ',no_share,' ') '".ObtenEtiqueta(795)."', CONCAT(' ',no_visto,' ') '".ObtenEtiqueta(796)."' ";
  $Query .= "FROM k_entregable a, k_share b, c_usuario c ";
  $Query .= "WHERE a.fl_entregable=b.fl_entregable AND b.fl_alumno=c.fl_usuario ";
  if(!empty($criterio) OR $criterio==0) {
    switch($actual) {
      case 1: $Query .= "AND fe_share LIKE '%$criterio%' "; break;
      case 2: $Query .= "AND ds_ruta_entregable LIKE '%$criterio%' "; break;
      case 3: $Query .= "AND ds_nombres LIKE '%$criterio%' OR ds_apaterno LIKE '%$criterio%' "; break;
      case 4: $Query .= "AND no_share LIKE '%$criterio%' "; break;
      case 5: $Query .= "AND no_visto LIKE '%$criterio%' "; break;
      default:
        $Query .= "AND (fe_share LIKE '%$criterio%' OR ds_ruta_entregable LIKE '%$criterio%' OR ds_nombres LIKE '%$criterio%' OR ds_apaterno LIKE '%$criterio%' ";
        $Query .= "OR no_share LIKE '%$criterio%' OR no_visto LIKE '%$criterio%') ";
    }
  }
  $Query .= "ORDER BY fe_share DESC";
  
  
  # Muestra pagina de listado
  $search =  array(ObtenEtiqueta(792), ObtenEtiqueta(793), ObtenEtiqueta(794),ObtenEtiqueta(795),ObtenEtiqueta(796));
  PresentaPaginaListado(126, $Query, TB_LN_NUN, True, False,$search,'','','','../../../public/preview.php','fa-vimeo-square','');
  
?>