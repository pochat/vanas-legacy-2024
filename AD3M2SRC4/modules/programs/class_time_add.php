<?php

  # Librerias
  require '../../lib/general.inc.php';
  
  # Recibe Parametros
  $fl_class_time = RecibeParametroNumerico('fl_class_time');
  $fl_class_time_programa=RecibeParametroNumerico('fl_class_time_programa');
  $fg_opc=RecibeParametroNumerico('fg_opc');
  $timepicker=RecibeParametroHTML('timepicker');
  $cl_dia=RecibeParametroNumerico('cl_dia');
  
  if($fg_opc==1){  
	  #Inserta el registro.
	  $Query="INSERT INTO k_class_time_programa (fl_class_time,cl_dia,no_hora,fe_creacion,fe_ulmod)";
	  $Query.="VALUES($fl_class_time,$cl_dia,'',CURRENT_TIMESTAMP,CURRENT_TIMESTAMP) ";
	  $fl_class_time=EjecutaInsert($Query);  
	  
	  if($fl_class_time){
		 $fg_correct=1;
		 $fl_class_time_programa=$fl_class_time;
	  }else{
		 $fg_correct=0; 
		 $fl_class_time_programa=0;
	  }

  }
  if($fg_opc==2){

	  #Elimina registro.
	  $Query="DELETE FROM k_class_time_programa WHERE fl_class_time_programa=$fl_class_time_programa ";
	  EjecutaQuery($Query);
	  $fg_correct=1;

  }
   if($fg_opc==3){

       #descomponemos la hora para 
	  #Obtenemos el dominio perteneciente al cuenat de cooreo electronico.
       $time=explode(" ",$timepicker);
	   $no_hora=$time[0];
	   $tiempo=$time[1];

	   $Query1="UPDATE k_class_time_programa SET cl_dia='$cl_dia',no_hora='$no_hora',ds_tiempo='$tiempo'  WHERE fl_class_time_programa=$fl_class_time_programa ";
	   EjecutaQuery($Query1);
	   $fg_correct=1;

   }

  echo json_encode((Object)array(
      'fg_correct' => $fg_correct,
	  'fl_class_time_programa'=>$fl_class_time_programa
    ));

?>