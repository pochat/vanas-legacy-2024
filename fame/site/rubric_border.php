
<?php
# Libreria de funciones	
require("../lib/self_general.php");

# Verifica que exista una sesion valida en el cookie y la resetea
$fl_usuario = ValidaSesion(False,0, True);
# Obtenemo el instituto
$fl_instituto = ObtenInstituto($fl_usuario);

$fl_criterio=RecibeParametroNumerico('fl_criterio');
$no_porcentaje=RecibeParametroNumerico('rangeInput');
$fl_leccion_sp=RecibeParametroNumerico('fl_leccion_sp');


#Verifica si la leccion es creada por el instituto.
$Query="SELECT b.fl_instituto FROM c_leccion_sp a
			    JOIN c_programa_sp b ON a.fl_programa_sp=b.fl_programa_sp where a.fl_leccion_sp=$fl_leccion_sp ";
$rol=RecuperaValor($Query);
$fl_leccion_de_instituto=$rol['fl_instituto'];



#Recuperamos las escalas.
$Queryb="SELECT fl_calificacion_criterio,cl_calificacion,ds_calificacion,fg_aprobado,no_equivalencia,no_min,no_max 
FROM c_calificacion_criterio ";
$Queryb.="WHERE 1=1 ";
if(!empty($fl_leccion_de_instituto)){
    $Queryb.="AND fl_instituto=$fl_instituto ";
}else{
    $Queryb.="AND fl_instituto is null ";
}
$rsb = EjecutaQuery($Queryb);
$contador_escalas=0;
for($ib=1;$rowb=RecuperaRegistro($rsb);$ib++) {
	 $fl_calificacion_criterio=$rowb['fl_calificacion_criterio'];
	 $contador_escalas++;
	 $no_min=$rowb['no_min'];
	 $no_max=$rowb['no_max'];
	 
	 
     if(($no_porcentaje>=$no_min)&&($no_porcentaje<=$no_max)){
         
         echo"
             <script>
			  $(document).ready(function () {
              $('#divborder_cero".$fl_criterio."_".$fl_calificacion_criterio."').addClass('border');
			    });
             </script>
             ";

     }else{

         echo"<script>
			   $(document).ready(function () {
				$('#divborder_cero".$fl_criterio."_".$fl_calificacion_criterio."').removeClass('border');
			   });
             </script>";
         
     }


	 
	 
}







