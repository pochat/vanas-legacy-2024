<?php

  # Libreria de funciones
  require_once("../lib/sp_general.inc.php");
 
  
  
  # Recibe  datos
  $fl_usuario= RecibeParametroHTML('fl_alumno');
  $fl_leccion_sp=RecibeParametroNumerico('fl_leccion_sp');
  
  
  $Query="SELECT no_assigments FROM k_usu_notify WHERE fl_usuario=$fl_usuario ";
  $row=RecuperaValor($Query);
  $no_actual= $row[0] - 1 ; 
  
  
  #Actualizaos el no. de notificaciones que tienne actualmente el alumno.
  $Query="UPDATE k_usu_notify SET no_assigments=$no_actual WHERE fl_usuario=$fl_usuario ";
  EjecutaQuery($Query);
  
  
  $Query="UPDATE k_entrega_semanal_sp SET fg_revisado_alumno='1' WHERE fl_alumno=$fl_usuario AND fl_leccion_sp=$fl_leccion_sp   ";
  EjecutaQuery($Query);
  

  
 ?>
 
  