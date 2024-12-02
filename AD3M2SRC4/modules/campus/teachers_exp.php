<?php

  # La libreria de funciones
  require '../../lib/general.inc.php';
  
  # Verifica que exista una sesion valida en el cookie y la resetea 1 hra
  ValidaSesion( );
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermiso(FUNC_MAESTROS, PERMISO_EJECUCION)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Recibe parametros
  $criterio = RecibeParametroHTML('criterio');
  $actual = RecibeParametroNumerico('actual');
  
  $Query  = "SELECT fl_usuario, ds_login '".ETQ_USUARIO."', ";
  $concat = array('ds_nombres', "' '", 'ds_apaterno', "' '", NulosBD('ds_amaterno', ''));
  $Query .= ConcatenaBD($concat)." '".ETQ_NOMBRE."', ";
  $Query .= "nb_perfil '".ObtenEtiqueta(110)."', ";
  $Query .= ConsultaFechaBD('fe_alta', FMT_FECHA)." '".ObtenEtiqueta(111)."', ";
  $concat = array(ConsultaFechaBD('fe_ultacc', FMT_FECHA), "' '", ConsultaFechaBD('fe_ultacc', FMT_HORA));
  $Query .= "(".ConcatenaBD($concat).") '".ObtenEtiqueta(112)."', ";
  $Query .= "CASE WHEN fg_activo=1 THEN '".ObtenEtiqueta(NO_ETQ_SI)."' ";
  $Query .= "ELSE '".ObtenEtiqueta(NO_ETQ_NO)."' END '".ObtenEtiqueta(113)."|center', a.ds_email '".ObtenEtiqueta(121)."' ";
  $Query .= "FROM c_usuario a, c_perfil b ";
  $Query .= "WHERE a.fl_perfil = b.fl_perfil ";
  $Query .= "AND a.fl_perfil=".PFL_MAESTRO." ";
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
  
  # Genera archivo con el resultado de la consulta
  $nom_arch = PATH_EXPORT."/teachers_".date('Ymd')."_".rand(1000,9000).".csv";
  if(!$archivo = fopen($_SERVER[DOCUMENT_ROOT].$nom_arch, "wb")) {
    MuestraPaginaError(ERR_EXPORTAR);
    exit;
  }
  
  # Exporta los datos
  $enc  = ObtenEtiqueta(42).",".ETQ_USUARIO.",".ETQ_NOMBRE.",".ObtenEtiqueta(110).",".ObtenEtiqueta(111).",";
  $enc .= ObtenEtiqueta(112).",".ObtenEtiqueta(113).",".ObtenEtiqueta(121).",\n";
  
  fwrite($archivo, str_ascii($enc));
  $Rows = EjecutaQuery($Query);
  while($row = RecuperaRegistro($Rows)) {
    for($i = 0; $i < 10; $i++)
      fwrite($archivo, str_ascii("$row[$i],"));
    fwrite($archivo, "\n");
  }
  
  # Cierra el archivo
  fclose($archivo);
  
  # Descarga el archivo
  header("Location: $nom_arch");
  
?>