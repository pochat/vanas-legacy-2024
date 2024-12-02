<?php
  
  # Libreria de funciones
  require '../../lib/general.inc.php';
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  ValidaSesion( );
  
  # Recibe parametros
 $fl_accion=RecibeParametroNumerico('fl_accion');
$fl_princing=RecibeParametroNumerico('fl_princing');
//$_POST['HOLA'];
$no_ini = RecibeParametroFlotante('no_inicial_');
$no_final = RecibeParametroFlotante('no_final_');
$no_porcentaje=RecibeParametroFlotante('porcentaje_');
$mn_mes=RecibeParametroFlotante('mn_mes_');
$mn_anual=RecibeParametroFlotante('mn_anual_');
$mn_porcentaje_mes=RecibeParametroFlotante('mn_porcentaje_mes');

#actualizamos el registro cada vez que modificamos un campo de la tabla de precios.
$Query="UPDATE c_princing_temporal SET no_ini=$no_ini,no_fin=$no_final, ";
if(!empty($mn_porcentaje_mes))
    $Query.="mn_descuento_licencia=$mn_porcentaje_mes , ";    
if(!empty($no_porcentaje))
$Query.="ds_descuento_mensual=$no_porcentaje ,";
$Query.="mn_mensual=$mn_mes,mn_anual=$mn_anual  WHERE fl_princing=$fl_princing ";
EjecutaQuery($Query);


#Recuperamos el id del princing:

$Query="SELECT fl_princing_temporal FROM c_princing_temporal WHERE fl_princing=$fl_princing ";
$row=RecuperaValor($Query);
$fl_princing_temporal=$row['fl_princing_temporal'];

$fl_princing_temporal_sigu=$fl_princing_temporal + 1;
$no_inicial_sigu=$no_final + 1 ;

$Query ="UPDATE c_princing_temporal SET no_ini=$no_inicial_sigu   WHERE fl_princing_temporal=$fl_princing_temporal_sigu ";
EjecutaQuery($Query);















?>


