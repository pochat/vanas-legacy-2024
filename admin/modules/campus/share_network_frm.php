<?php
  
  # La libreria de funciones
  require '../../lib/general.inc.php';
  # Verifica que exista una sesion valida en el cookie y la resetea
  ValidaSesion( );
  
  # Recibe parametro
  $clave = RecibeParametroHTML('clave');
  # Buscamos  el fl_entregable
  $row = RecuperaValor("SELECT fl_entregable FROM k_share WHERE fl_share_face LIKE '%$clave%'");
  $clave = $row[0];
  
  
  # Determina si es alta o modificacion
  if(!empty($clave))
    $permiso = PERMISO_DETALLE;
  else
    $permiso = PERMISO_ALTA;
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermiso(FUNC_SHARE_NETWORK, $permiso)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Define el url de facebook
  define('FACEBOOK_URL','http://facebook.com');
  
  # Inicializa variables
  $Query = "SELECT a.fl_entregable, fl_share_face, e.ds_titulo, CONCAT(ds_nombres,' ', ds_apaterno), ".ConsultaFechaBD('fe_share', FMT_CAPTURA)." fe_share, no_share,no_visto ";
  $Query .= "FROM (k_entregable  a, k_entrega_semanal b, k_semana c, c_leccion e, k_share f) ";
  $Query .= "LEFT JOIN c_usuario g ON g.fl_usuario=f.fl_alumno ";
  $Query .= "WHERE a.fl_entrega_semanal=b.fl_entrega_semanal AND b.fl_semana=c.fl_semana ";
  $Query .= "AND c.fl_leccion=e.fl_leccion AND a.fl_entregable=f.fl_entregable ";
  $Query .= "AND a.fl_entregable=".$clave." ";
  $row = RecuperaValor($Query);
  $fl_entregable = $row[0];
  $fl_share_face = $row[1];
  $ds_titulo = str_texto($row[2]);
  $ds_nombres = str_texto($row[3]);
  $fe_share = $row[4];
  $no_share = $row[5];
  $no_visto = $row[6];
  $ds_ruta_entregale = str_texto($row[7]);
  

  
  # Presenta forma de captura
  PresentaHeader( );
  PresentaEncabezado(FUNC_BLOGS);
  
  # Inicia forma de captura
  Forma_Inicia($clave, True);
  
  # Datos Generales
  Forma_CampoInfo(ObtenEtiqueta(797), $fl_share_face);
  Forma_CampoInfo(ObtenEtiqueta(385), $ds_titulo);
  Forma_CampoInfo(ETQ_NOMBRE, $ds_nombres);
  Forma_CampoInfo(ObtenEtiqueta(798), $fe_share);
  Forma_CampoInfo(ObtenEtiqueta(799), $no_share);
  Forma_CampoInfo(ObtenEtiqueta(800), $no_visto);
  Forma_CampoInfo(ObtenEtiqueta(801), "<a href=".FACEBOOK_URL."/".$fl_share_face." target='_blank'>".FACEBOOK_URL."/".$fl_share_face."</a>");
  
  # Verifica que el usuario tenga permiso para guardar los cambios (solo en modo modificacion)
  Forma_Termina(false);
  
  # Pie de Pagina
  PresentaFooter( );

?>