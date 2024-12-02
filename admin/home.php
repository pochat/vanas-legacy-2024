<?php
  
  # Libreria general de funciones
  require 'lib/general.inc.php';
  
  # Verifica que exista una sesion valida en el cookie y la resetea 1 hra
  ValidaSesion( );
  
  # Presenta home del sistema
  PresentaHeader( );
  echo "Select an option from submenu<br><br>";
  # Corremos la funcion para 
  Meses_X_Pago();
  # Actualizamos la calificacion de los estudiantespor term quehaya cursado
  $Query0 = "SELECT fl_alumno FROM c_alumno ORDER BY fl_alumno ";
  $rs0 = EjecutaQuery($Query0);
  for($j=0;$row0=RecuperaRegistro($rs0);$j++){
    $Query  = "SELECT SUM(i.no_equivalencia)/COUNT(a.fl_semana), a.fl_term, no_grado, c.fl_alumno ";
    $Query .= "FROM k_semana a, k_term b, k_entrega_semanal c, c_calificacion i ";
    $Query .= "WHERE a.fl_term=b.fl_term AND a.fl_semana=c.fl_semana AND c.fl_promedio_semana=i.fl_calificacion ";
    $Query .= "AND a.fl_term IN(SELECT fl_term FROM k_alumno_term e WHERE e.fl_alumno=c.fl_alumno AND c.fl_alumno=$row0[0]) ";
    $Query .= "GROUP BY a.fl_term ";
    $rs = EjecutaQuery($Query);
    for($i=0;$row=RecuperaRegistro($rs);$i++){
      EjecutaQuery("UPDATE k_alumno_term SET no_promedio='".$row[0]."' WHERE fl_alumno=$row[3] AND fl_term=$row[1] ");
    }
  }
  PresentaFooter( );
  
?>