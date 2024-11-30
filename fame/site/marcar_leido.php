<?php

# Libreria de funciones	
require("../lib/self_general.php");

$fg_leido=RecibeParametroHTML('fg_leido');
$fl_alumno=RecibeParametroNumerico('fl_alumno');
$fl_leccion_sp=RecibeParametroNumerico('fl_leccion_sp');


    #Guardamos la confirmacion de visto , por el estudiante. 
    $Query="UPDATE k_entrega_semanal_sp SET  fg_revisado_alumno='$fg_leido'  WHERE fl_alumno=$fl_alumno AND fl_leccion_sp=$fl_leccion_sp ";
    EjecutaQuery($Query);
    

?>



										
   
   
   




