<?php
  
  # Libreria de funciones
  require '../../lib/general.inc.php';

  # Recibe parametros
  $clave=RecibeParametroHTML('fl_registro');#fl_criterio de la tabla c_criterio
  $ds_descripcion = RecibeParametroHTML('ds_descripcion');
  $fl_calificacion = RecibeParametroHTML('fl_calificacion');
  $fg_criterio=RecibeParametroNumerico('nb_criterio');
  $ds_descripcion=trim($ds_descripcion);

  #verificamos si existe ese Criterio
  if(empty($clave)){

    # ELIMINAMOS EL CRITERIO
    $Query="DELETE FROM k_criterio_fame WHERE fl_criterio IS NULL  AND  fl_calificacion_criterio=$fl_calificacion   ";

    EjecutaQuery($Query);

    $Query="INSERT INTO k_criterio_fame (fl_calificacion_criterio,ds_descripcion) ";
		$Query.="VALUES($fl_calificacion,'$ds_descripcion')";
		$fl_criterio_fame=EjecutaInsert($Query);

  }else{

    # ELIMINAMOS EL CRITERIO
    $Query="DELETE FROM k_criterio_fame WHERE fl_criterio=$clave  AND  fl_calificacion_criterio=$fl_calificacion   ";

    EjecutaQuery($Query);
      
    $Query="INSERT INTO k_criterio_fame (fl_criterio, fl_calificacion_criterio,ds_descripcion) ";
    $Query.="VALUES($clave,  $fl_calificacion,'$ds_descripcion')";
    $fl_criterio_fame=EjecutaInsert($Query);

  }

?>




 