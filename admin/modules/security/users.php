<?php
  
  # La libreria de funciones
  require '../../lib/general.inc.php';
  
  # Recibe parametros
  $criterio = RecibeParametroHTML('criterio');
  $actual = RecibeParametroNumerico('actual');
  
  # Consulta para el listado
  $Query  = "SELECT fl_usuario, ds_login '".ETQ_USUARIO."', ";
  $concat = array('ds_nombres', "' '", 'ds_apaterno', "' '", NulosBD('ds_amaterno', ''));
  $Query .= ConcatenaBD($concat)." '".ETQ_NOMBRE."', ";
  $Query .= "nb_perfil '".ObtenEtiqueta(110)."', ";
  $Query .= ConsultaFechaBD('fe_alta', FMT_FECHA)." '".ObtenEtiqueta(111)."', ";
  $concat = array(ConsultaFechaBD('fe_ultacc', FMT_FECHA), "' '", ConsultaFechaBD('fe_ultacc', FMT_HORA));
  $Query .= "(".ConcatenaBD($concat).") '".ObtenEtiqueta(112)."', ";
  $Query .= "CASE WHEN fg_activo='1' THEN '".ObtenEtiqueta(NO_ETQ_SI)."' ";
  $Query .= "ELSE '".ObtenEtiqueta(NO_ETQ_NO)."' END '".ObtenEtiqueta(113)."|center' ";
  $Query .= "FROM c_usuario a, c_perfil b ";
  $Query .= "WHERE a.fl_perfil = b.fl_perfil ";
  $Query .= "AND fg_admon='1' ";
  $Query .= "AND fl_usuario > ".ADMINISTRADOR." ";
  if(!empty($criterio)) {
    switch($actual) {
      case 1: $Query .= "AND ds_login LIKE '%$criterio%' "; break;
      case 2: $Query .= "AND (ds_nombres LIKE '%$criterio%' OR ds_apaterno LIKE '%$criterio%' OR ds_amaterno LIKE '%$criterio%') "; break;
      case 3: $Query .= "AND (nb_perfil LIKE '%$criterio%' OR ds_perfil LIKE '%$criterio%') "; break;
      case 4: $Query .= "AND ".ConsultaFechaBD('fe_alta', FMT_FECHA)." LIKE '%$criterio%' "; break;
      case 5: 
        $Query .= "AND (".ConsultaFechaBD('fe_ultacc', FMT_FECHA)." LIKE '%$criterio%' ";
        $Query .= "OR ".ConsultaFechaBD('fe_ultacc', FMT_HORA)." LIKE '%$criterio%') ";
        break;
      default:
        $Query .= "AND ( ";
        $Query .= "ds_login LIKE '%$criterio%' ";
        $Query .= "OR ds_nombres LIKE '%$criterio%' ";
        $Query .= "OR ds_apaterno LIKE '%$criterio%' ";
        $Query .= "OR ds_amaterno LIKE '%$criterio%' ";
        $Query .= "OR nb_perfil LIKE '%$criterio%' ";
        $Query .= "OR ds_perfil LIKE '%$criterio%' ";
        $Query .= "OR ".ConsultaFechaBD('fe_alta', FMT_FECHA)." LIKE '%$criterio%' ";
        $Query .= "OR ".ConsultaFechaBD('fe_ultacc', FMT_FECHA)." LIKE '%$criterio%' ";
        $Query .= "OR ".ConsultaFechaBD('fe_ultacc', FMT_HORA)." LIKE '%$criterio%' ";
        $Query .= ") ";
    }
  }
  $Query .= "ORDER BY ds_login";
  
  # Muestra pagina de listado
  $campos = array(ETQ_USUARIO, ETQ_NOMBRE, ObtenEtiqueta(110), ObtenEtiqueta(111), ObtenEtiqueta(112));
  PresentaPaginaListado(FUNC_USUARIOS, $Query, TB_LN_IUD, True, False, $campos);
  
?>