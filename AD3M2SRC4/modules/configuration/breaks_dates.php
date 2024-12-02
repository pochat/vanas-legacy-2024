<?php
  
  # Libreria de funciones
  require '../../lib/general.inc.php';
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  ValidaSesion( );
  
  # Recibe Parametros
  $fe_ini = RecibeParametroFecha('fe_ini');
  $fe_fin = RecibeParametroFecha('fe_fin');
  
  # Prepara fechas en formato para insertar
  if(!empty($fe_ini))
    $fe_ini = ValidaFecha($fe_ini);
  if(!empty($fe_fin))
    $fe_fin = ValidaFecha($fe_fin);
   
  # Inicializamos los dias
  $dias = 0;
  # Recorremos
  for($i=$fe_ini;$i<=$fe_fin;$i = date("Y-m-d", strtotime($i ."+ 1 days"))){    
      $dia = date("D", strtotime($i." 0 days"));    
      $dias ++;
      if($dia == "Sat" || $dia == "Sun"){
        //echo "<strong style='color:red;'>".$i."</strong><br/>";
        $dias --;     
      }
      /*else{
        echo $i."<br/>";      
      }*/
  }
  
  # Enviamos el resultado
  echo $dias;
?>