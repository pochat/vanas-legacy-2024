<?php
  
  # La libreria de funciones
  require '../../lib/general.inc.php';
  
  # Recibe parametros
  $criterio = RecibeParametroHTML('criterio');
  $actual = RecibeParametroNumerico('actual');
  
  # Consulta para el listado
  $Query  = "SELECT fl_blog, ".ConsultaFechaBD('fe_blog', FMT_FECHA)." '".ObtenEtiqueta(450)."', ds_titulo '".ETQ_TITULO."', ";
  $Query .= "CASE WHEN fg_maestros='1' THEN '".ObtenEtiqueta(NO_ETQ_SI)."' ";
  $Query .= "ELSE '".ObtenEtiqueta(NO_ETQ_NO)."' END '".ObtenEtiqueta(451)."|center', ";
  $Query .= "CASE WHEN fg_alumnos='1' THEN '".ObtenEtiqueta(NO_ETQ_SI)."' ";
  $Query .= "ELSE '".ObtenEtiqueta(NO_ETQ_NO)."' END '".ObtenEtiqueta(452)."|center', ";
  $Query .= "CASE WHEN fg_notificacion='1' THEN '".ObtenEtiqueta(NO_ETQ_SI)."' ";
  $Query .= "ELSE '".ObtenEtiqueta(NO_ETQ_NO)."' END '".ObtenEtiqueta(453)."|center', ds_login '".ObtenEtiqueta(454)."', ";
  $Query .= "no_hits '".ObtenEtiqueta(455)."|right' ";
  $Query .= "FROM c_blog a, c_usuario b ";
  $Query .= "WHERE a.fl_usuario=b.fl_usuario ";
  if(!empty($criterio)) {
    switch($actual) {
      case 1: $Query .= "AND fe_blog LIKE '%$criterio%' "; break;
      case 2: $Query .= "AND ds_titulo LIKE '%criterio%' "; break;
      case 3: $Query .= "AND ds_login LIKE '%$criterio%' "; break;
      default:
        $Query .= "AND (fe_blog LIKE '%$criterio%' OR ds_titulo LIKE '%$criterio%' OR ds_login LIKE '%$criterio%') ";
    }
  }
  $Query .= "ORDER BY fe_blog DESC";
  
  # Muestra pagina de listado
  PresentaPaginaListado(FUNC_BLOGS, $Query, TB_LN_IUD, True, False, array(ObtenEtiqueta(450), ETQ_TITULO, ObtenEtiqueta(454)));
  
?>