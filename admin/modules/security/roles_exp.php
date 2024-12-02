<?php
  
  # La libreria de funciones
  require '../../lib/general.inc.php';
  
  # Verifica que exista una sesion valida en el cookie y la resetea 1 hra
  ValidaSesion( );
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermiso(FUNC_PERFILES, PERMISO_EJECUCION)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Recibe parametros
  $criterio = RecibeParametroHTML('criterio');
  $actual = RecibeParametroNumerico('actual');
  
  # Consulta para el listado
  $Query  = "SELECT fl_perfil, nb_perfil '".ETQ_NOMBRE."', ds_perfil '".ETQ_DESCRIPCION."', ";
  $Query .= "CASE WHEN fg_admon=1 THEN '".ObtenEtiqueta(NO_ETQ_SI)."' ";
  $Query .= "ELSE '".ObtenEtiqueta(NO_ETQ_NO)."' END '".ETQ_TITULO_PAGINA."|center' ";
  $Query .= "FROM c_perfil ";
  if(!empty($criterio)) {
    switch($actual) {
      case 1: $Query .= "WHERE nb_perfil LIKE '%$criterio%' "; break;
      case 2: $Query .= "WHERE ds_perfil LIKE '%$criterio%' "; break;
      default: $Query .= "WHERE nb_perfil LIKE '%$criterio%' OR ds_perfil LIKE '%$criterio%' ";
    }
  }
  $Query .= "ORDER BY nb_perfil";
  
  # Genera archivo con el resultado de la consulta
  $nom_arch = PATH_EXPORT."/perfiles_".date('Ymd')."_".rand(1000,9000).".csv";
  if(!$archivo = fopen($_SERVER[DOCUMENT_ROOT].$nom_arch, "wb")) {
    MuestraPaginaError(ERR_EXPORTAR);
    exit;
  }
  
  # Exporta los datos
  fwrite($archivo, str_ascii(ETQ_NOMBRE.",".ETQ_DESCRIPCION.",".ETQ_TITULO_PAGINA.",\n"));
  $Rows = EjecutaQuery($Query);
  while($row = RecuperaRegistro($Rows)) {
    for($i = 0; $i < 3; $i++)
      fwrite($archivo, str_ascii("$row[$i],"));
    fwrite($archivo, "\n");
  }
  
  # Cierra el archivo
  fclose($archivo);
  
  # Descarga el archivo
  header("Location: $nom_arch");
  
?>