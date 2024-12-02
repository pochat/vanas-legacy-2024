<?php
	
require '../../lib/general.inc.php';

#Recibe Parametros.
$fe_inicio=$_POST['fe_inicio'];
$fe_final=$_POST['fe_final'];
$id=$_POST['id'];

#Se vienen id entonces queire decir que ya viene de la BD.
if((!empty($id))&&($id<>'undefined')){
  $valores=explode('#',$id); 
  $fl_clase_calendar=$valores[0];
  $fl_term=$valores[1];
  $fl_programa=$valores[2];
  $fl_leccion=$valores[3];
  $no_grado=$valores[4];
  $fl_periodo=$valores[5];
  $fl_maestro=$valores[6];
  
  $fe_inicio=str_replace("T"," ",$fe_inicio);
  $fe_inicio=str_replace("Z","",$fe_inicio);
  $fe_final=str_replace("T"," ",$fe_final);
  $fe_final=str_replace("Z","",$fe_final);



  #Actualizamos la hora del evento.
  $Query="UPDATE k_clase_calendar SET fe_inicio='$fe_inicio', fe_final='$fe_final' WHERE fl_clase_calendar=$fl_clase_calendar ";
  EjecutaQuery($Query);

  


}

?>
