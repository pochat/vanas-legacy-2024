<?php
  
  # Libreria de funciones
  require '../../lib/general.inc.php';

  # Recibe parametros
  #fl_criterio de la tabla c_criterio
  $clave=RecibeParametroHTML("fl_registro");
  $ds_descripcion_esp = str_replace(array_keys($replacements), $replacements, $_POST["ds_descripcion_esp"]);
  $fl_calificacion = RecibeParametroHTML("fl_calificacion");
  $fg_criterio=RecibeParametroNumerico("nb_criterio");
  $ds_descripcion_esp=trim($ds_descripcion_esp);

  #verificamos si existe ese criterio
  if(empty($clave)){

    # Si no existe ELIMINAMOS EL CRITERIO
    $Query="DELETE FROM k_criterio_fame WHERE fl_criterio IS NULL  AND  fl_calificacion_criterio=$fl_calificacion ;";

    EjecutaQuery($Query);

    $Query ="UPDATE k_criterio_fame SET ds_descripcion_esp='".$ds_descripcion_esp."' WHERE fl_criterio='".$clave."' AND fl_calificacion_criterio='".$fl_calificacion."';";
    $fl_criterio_fame=EjecutaInsert($Query);

  }else{

      
       #Verificar si existe.
       $Query="SELECT COUNT(*) from k_criterio_fame  WHERE fl_criterio='$clave' AND fl_calificacion_criterio='$fl_calificacion' "; 
       $ro=RecuperaValor($Query);

       if($ro[0]){
           $Query ="UPDATE k_criterio_fame SET ds_descripcion_esp='".$ds_descripcion_esp."' WHERE fl_criterio='".$clave."' AND fl_calificacion_criterio='".$fl_calificacion."';";
           EjecutaQuery($Query);
       }else{
           
           $Query="INSERT INTO k_criterio_fame (fl_criterio,fl_calificacion_criterio, ds_descripcion_esp) ";
           $Query.="VALUES($clave,$fl_calificacion, '$ds_descripcion_esp')";
           EjecutaInsert($Query);
       }

  }

?>