<?php
  
  # La libreria de funciones
  require '../../lib/general.inc.php';
  
  # Recibe parametros
  $criterio = RecibeParametroHTML('criterio');
  $actual = RecibeParametroNumerico('actual');
  
  # Consulta para el listado
  $Query  = "SELECT a.fl_provincia, a.ds_provincia '".ObtenEtiqueta(812)."', b.ds_pais '".ObtenEtiqueta(287)."', CONCAT(' ',a.mn_PST,' ') '".ObtenEtiqueta(813)."', ";
  $Query .= "CONCAT(' ',a.mn_GST,' ') '".ObtenEtiqueta(814)."', CONCAT(' ',a.mn_HST,' ') '".ObtenEtiqueta(815)."', CONCAT(' ',a.mn_tax,' ') '".ObtenEtiqueta(816)."', ds_notas '".ObtenEtiqueta(818)."' ";
  $Query .= "FROM k_provincias a, c_pais b WHERE a.fl_pais=b.fl_pais ";
  if(!empty($criterio)) {
    switch($actual) {
      case 1: $Query .= "AND a.ds_provincia LIKE '%$criterio%' "; break;
      case 2: $Query .= "AND b.ds_pais LIKE '%$criterio%' "; break;
      case 3: $Query .= "AND a.mn_PST LIKE '%$criterio%' "; break;
      case 4: $Query .= "AND a.mn_GST LIKE '%$criterio%'"; break;
      case 5: $Query .= "AND a.mn_HST LIKE '%$criterio%'"; break;
      case 6: $Query .= "AND a.mn_tax LIKE '%$criterio%'"; break;
      default:
        $Query .= "AND (a.ds_provincia LIKE '%$criterio%' OR b.ds_pais LIKE '%$criterio%' OR a.mn_PST LIKE '%$criterio%' OR a.mn_GST LIKE '%$criterio%'  ";
        $Query .= "OR  a.mn_HST LIKE '%$criterio%' OR a.mn_tax LIKE '%$criterio%') ";
    }
  }
  
  # Exporta el resultado a CSV
  $nom_arch = PATH_EXPORT."/provincias_".date('Ymd')."_".rand(1000,9000).".csv";
  ExportaQuery($nom_arch, $Query);
  
  # Descarga el archivo
  header("Location: $nom_arch");
  
?>