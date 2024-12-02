<?php
  
  # La libreria de funciones
  require '../../lib/general.inc.php';
  
  # Recibe parametros
  $criterio = RecibeParametroHTML('criterio');
  $actual = RecibeParametroNumerico('actual');
  
  # Recupera el usuario y su perfil
  $fl_usuario = ObtenUsuario( );
  $fl_perfil = ObtenPerfil($fl_usuario);
  
  # Consulta para el listado
  $Query  = "SELECT fl_contenido, lb_funcion '".ObtenEtiqueta(154)."', lb_titulo '".ETQ_TITULO."', ds_estado '".ObtenEtiqueta(200)."', ";
  $Query .= "ds_activo '".ObtenEtiqueta(199)."|center', ds_ini '".ObtenEtiqueta(190)."', ds_fijo '".ObtenEtiqueta(161)."|center' ";
  $Query .= "FROM ( ";
  $Query .= "SELECT a.fl_contenido, nb_funcion, tr_funcion, nb_titulo, tr_titulo, a.no_orden no_orden, ";
  $Query .= EscogeIdioma('nb_funcion','tr_funcion')." lb_funcion, ".EscogeIdioma('nb_titulo','tr_titulo')." lb_titulo, ";
  $Query .= EscogeIdioma('c.ds_nivel', 'c.tr_nivel')." ds_estado, ";
  $Query .= "CASE fg_activo WHEN 1 THEN '".ETQ_SI."' ELSE '".ETQ_NO."' END ds_activo, ";
  $Query .= ConsultaFechaBD('fe_ini', FMT_FECHA)." ds_ini, ";
  $Query .= "CASE a.fg_fijo WHEN 1 THEN '".ETQ_SI."' ELSE '".ETQ_NO."' END ds_fijo ";
  $Query .= "FROM c_contenido a, c_funcion b, k_flujo_nivel c ";
  $Query .= "WHERE a.fl_funcion=b.fl_funcion ";
  $Query .= "AND b.fl_flujo=c.fl_flujo ";
  $Query .= "AND a.no_nivel=c.no_nivel ";
  if($fl_usuario != ADMINISTRADOR) {
    $Query .= "AND (";
    $Query .= "EXISTS(SELECT 1 FROM k_nivel_usuario WHERE fl_flujo=c.fl_flujo AND no_nivel=c.no_nivel AND fl_usuario=$fl_usuario) OR ";
    $Query .= "EXISTS(SELECT 1 FROM k_nivel_usuario WHERE fl_flujo=c.fl_flujo AND no_nivel=c.no_nivel+1 AND fl_usuario=$fl_usuario) OR ";
    $Query .= "EXISTS(SELECT 1 FROM k_nivel_perfil WHERE fl_flujo=c.fl_flujo AND no_nivel=c.no_nivel AND fl_perfil=$fl_perfil) OR ";
    $Query .= "EXISTS(SELECT 1 FROM k_nivel_perfil WHERE fl_flujo=c.fl_flujo AND no_nivel=c.no_nivel+1 AND fl_perfil=$fl_perfil)";
    $Query .= ")";
  }
  $Query .= "AND b.cl_tipo_contenido=".TC_NODO." ";
  $Query .= "ORDER BY b.fl_modulo, b.no_orden, nb_titulo) AS principal ";
  if(!empty($criterio)) {
    switch($actual) {
      case 1: $Query .= "WHERE nb_funcion LIKE '%$criterio%' OR tr_funcion LIKE '%$criterio%' "; break;
      case 2: $Query .= "WHERE nb_titulo LIKE '%$criterio%' OR tr_titulo LIKE '%$criterio%' "; break;
      case 3: $Query .= "WHERE ds_estado LIKE '%$criterio%' "; break;
      default:
        $Query .= "WHERE nb_funcion LIKE '%$criterio%' OR tr_funcion LIKE '%$criterio%' ";
        $Query .= "OR nb_titulo LIKE '%$criterio%' OR tr_titulo LIKE '%$criterio%' OR ds_estado LIKE '%$criterio%' ";
    }
  }
  
  # Muestra pagina de listado
  $opc = array(ObtenEtiqueta(154), ETQ_TITULO, ObtenEtiqueta(200));
  PresentaPaginaListado(FUNC_NODOS, $Query, TB_LN_IUD, True, False, $opc);
  
?>