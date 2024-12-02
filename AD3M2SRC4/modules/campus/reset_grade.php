<?php
# Libreria de funciones
require '../../lib/general.inc.php';
 
$fl_semana=RecibeParametroNumerico('fl_semana');
$fl_usuario=RecibeParametroNumerico('fl_usuario');


$Query="UPDATE k_entrega_semanal set fl_promedio_semana=null WHERE fl_semana=$fl_semana and fl_alumno=$fl_usuario";
EjecutaQuery($Query);




?>