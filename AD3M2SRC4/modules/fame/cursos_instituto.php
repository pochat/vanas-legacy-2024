<?php

  # Libreria de funciones
  require("../../lib/general.inc.php");
  
  # Recibe parametros
  $fl_instituto = RecibeParametroNumerico('fl_instituto');
  $cheked=RecibeParametroBinario('cheked');
  $fl_programa_sp=RecibeParametroNumerico('fl_programa_sp');

  if(!empty($cheked)){
      $Query="SELECT MAX(no_orden) FROM k_orden_desbloqueo_curso_alumno WHERE fl_instituto=$fl_instituto ";
      $row=RecuperaValor($Query);
      $no_orden=$row[0];
      $no_orden=$no_orden +1;
      $Query="INSERT INTO k_orden_desbloqueo_curso_alumno(no_orden,fl_instituto,fl_programa_sp,fg_motivo,fe_creacion)VALUES($no_orden,$fl_instituto,$fl_programa_sp,'PU',CURRENT_TIMESTAMP) ";
      $pro=EjecutaInsert($Query);

  }else{
      $Query="DELETE FROM k_orden_desbloqueo_curso_alumno WHERE fl_instituto=$fl_instituto AND fl_programa_sp=$fl_programa_sp ";
      EjecutaQuery($Query);
  }





?>

