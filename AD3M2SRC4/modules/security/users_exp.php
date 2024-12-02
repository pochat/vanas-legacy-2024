<?php

  # La libreria de funciones
  require '../../lib/general.inc.php';
  
  # Verifica que exista una sesion valida en el cookie y la resetea 1 hra
  ValidaSesion( );
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermiso(FUNC_USUARIOS, PERMISO_EJECUCION)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Recibe parametros
  $criterio = RecibeParametroHTML('criterio');
  $actual = RecibeParametroNumerico('actual');
  
  # Consulta para el listado
  $Query  = "SELECT ds_login, ds_nombres, ds_apaterno, ISNULL(ds_amaterno, ''), nb_perfil, ";
  $Query .= "CONVERT(varchar, fe_alta, 106), CONVERT(varchar(20), fe_ultacc, 113), ";
  $Query .= "CASE WHEN fg_activo=1 THEN '".ObtenEtiqueta(NO_ETQ_SI)."' ";
  $Query .= "ELSE '".ObtenEtiqueta(NO_ETQ_NO)."' END, ";
  $Query .= "CASE WHEN fg_genero=1 THEN '".ObtenEtiqueta(115)."' ";
  $Query .= "ELSE '".ObtenEtiqueta(116)."' END, ";
  $Query .= "CONVERT(varchar, fe_nacimiento, 106), ds_email, no_accesos ";
  $Query .= "FROM c_usuario a, c_perfil b ";
  $Query .= "WHERE a.fl_perfil = b.fl_perfil ";
  $Query .= "AND fl_usuario > ".ADMINISTRADOR." ";
  if(!empty($criterio)) {
    switch($actual) {
      case 1: $Query .= "AND ds_login LIKE '%$criterio%' "; break;
      case 2: $Query .= "AND (ds_nombres LIKE '%$criterio%' OR ds_apaterno LIKE '%$criterio%' OR ds_amaterno LIKE '%$criterio%') "; break;
      case 3: $Query .= "AND (nb_perfil LIKE '%$criterio%' OR ds_perfil LIKE '%$criterio%') "; break;
      case 4: $Query .= "AND CONVERT(varchar, fe_alta, 106) LIKE '%$criterio%' "; break;
      case 5: $Query .= "AND CONVERT(varchar(20), fe_ultacc, 113) LIKE '%$criterio%' "; break;
      default:
        $Query .= "AND ( ";
        $Query .= "ds_login LIKE '%$criterio%' ";
        $Query .= "OR ds_nombres LIKE '%$criterio%' ";
        $Query .= "OR ds_apaterno LIKE '%$criterio%' ";
        $Query .= "OR ds_amaterno LIKE '%$criterio%' ";
        $Query .= "OR nb_perfil LIKE '%$criterio%' ";
        $Query .= "OR ds_perfil LIKE '%$criterio%' ";
        $Query .= "OR CONVERT(varchar, fe_alta, 106) LIKE '%$criterio%' ";
        $Query .= "OR CONVERT(varchar(20), fe_ultacc, 113) LIKE '%$criterio%' ";
        $Query .= ") ";
    }
  }
  $Query .= "ORDER BY a.fl_perfil, ds_nombres, ds_apaterno";
  
  # Genera archivo con el resultado de la consulta
  $nom_arch = PATH_EXPORT."/usuarios_".date('Ymd')."_".rand(1000,9000).".csv";
  if(!$archivo = fopen($_SERVER[DOCUMENT_ROOT].$nom_arch, "wb")) {
    MuestraPaginaError(ERR_EXPORTAR);
    exit;
  }
  
  # Exporta los datos
  $enc  = ETQ_USUARIO.",".ObtenEtiqueta(117).",".ObtenEtiqueta(118).",".ObtenEtiqueta(119).",".ObtenEtiqueta(110).",".ObtenEtiqueta(111).",";
  $enc .= ObtenEtiqueta(112).",".ObtenEtiqueta(113).",".ObtenEtiqueta(114).",".ObtenEtiqueta(120).",".ObtenEtiqueta(121).",";
  $enc .= ObtenEtiqueta(122).",\n";
  fwrite($archivo, str_ascii($enc));
  $Rows = EjecutaQuery($Query);
  while($row = RecuperaRegistro($Rows)) {
    for($i = 0; $i < 12; $i++)
      fwrite($archivo, str_ascii("$row[$i],"));
    fwrite($archivo, "\n");
  }
  
  # Cierra el archivo
  fclose($archivo);
  
  # Descarga el archivo
  header("Location: $nom_arch");
  
?>