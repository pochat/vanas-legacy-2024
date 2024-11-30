
<?php
# Libreria de funciones	
require("../lib/self_general.php");


# Verifica que exista una sesion valida en el cookie y la resetea
$fl_usuario = ValidaSesion(False,0, True);
# Intituto del usuario
$fl_instituto = ObtenInstituto($fl_usuario);

$fl_criterio=RecibeParametroNumerico('fl_criterio');
$no_porcentaje=RecibeParametroNumerico('rangeInput');
$peso_criterio=RecibeParametroNumerico('peso_criterio');
$fl_identificador=RecibeParametroNumerico('fl_identificador');
$fl_alumno=RecibeParametroNumerico('fl_alumno');
$fl_leccion_sp=RecibeParametroNumerico('fl_leccion_sp');
$fl_programa_sp=RecibeParametroNumerico('fl_programa_sp');
$fg_calcula_promedio=RecibeParametroNumerico('fg_calcula_promedio');

#Se calcula el equivalente con respecto a su peso
$no_porcentaje_final= ($no_porcentaje * $peso_criterio) / 100 ;

#Verifica si la leccion es creada por el instituto.
$Query="SELECT b.fl_instituto FROM c_leccion_sp a
			    JOIN c_programa_sp b ON a.fl_programa_sp=b.fl_programa_sp where a.fl_leccion_sp=$fl_leccion_sp ";
$rol=RecuperaValor($Query);
$fl_leccion_de_instituto=$rol['fl_instituto'];



#Eliminamos 
EjecutaQuery("DELETE FROM c_calculo_criterio_temp WHERE fl_criterio=$fl_criterio and fl_alumno=$fl_alumno AND fl_programa_sp=$fl_programa_sp AND fl_leccion_sp=$fl_leccion_sp ");

#se realiza insret tabla temporal
$Query="INSERT INTO c_calculo_criterio_temp (fl_alumno,fl_leccion_sp,fl_programa_sp,fl_criterio,no_porcentaje,no_porcentaje_real) ";
$Query.="VALUES ($fl_alumno,$fl_leccion_sp,$fl_programa_sp,$fl_criterio,$no_porcentaje_final,$no_porcentaje)";
EjecutaQuery($Query);


$Query="UPDATE c_com_criterio_teacher SET no_porcentaje_equivalente=$no_porcentaje WHERE fl_criterio=$fl_criterio and fl_alumno=$fl_alumno AND fl_programa_sp=$fl_programa_sp AND fl_leccion_sp=$fl_leccion_sp ";
EjecutaQuery($Query);

#Sumamos todos los que pernetencen al identifiacador.
$Query ="SELECT SUM(no_porcentaje) FROM c_calculo_criterio_temp  WHERE fl_alumno=$fl_alumno AND fl_programa_sp=$fl_programa_sp AND fl_leccion_sp=$fl_leccion_sp ";
$row=RecuperaValor($Query);
$no_suma=$row[0];


$Query="UPDATE c_com_criterio_teacher SET no_porcentaje_equivalente=$no_suma WHERE  fl_alumno=$fl_alumno AND fl_programa_sp=$fl_programa_sp AND fl_leccion_sp=$fl_leccion_sp AND fg_com_final='1' ";
EjecutaQuery($Query);


$Query="UPDATE k_calificacion_teacher SET no_calificacion=$no_suma WHERE  fl_alumno=$fl_alumno AND fl_programa_sp=$fl_programa_sp AND fl_leccion_sp=$fl_leccion_sp  ";
EjecutaQuery($Query);


?>

<div class="chart"  data-percent="<?php echo $no_suma; ?>" id="final_total">
    <span class="percent" style="font:18px Arial;"> <?php echo number_format($no_suma); ?> </span>
</div>

<!-----
<?php echo number_format($no_suma); ?>
    
    ----->

<script>
    $(document).ready(function () {
    $('#final_total').easyPieChart({
			animate: 2000,
			scaleColor: false,
			lineWidth: 8,
			lineCap: 'square',
			value:'10',
			size: 100,
			trackColor: '#EEEEEE',
			barColor: '#92D099'
		});

		$('#final_total').css({
            width: 100 + 'px',
            height: 100 + 'px'
        });
		$('#final_total .percent').css({
			"line-height": 100 + 'px'
		})

    });
</script>

<!-----------pintamos el border del rango seleccionado------------>
<?php 

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
              $('#divborder_cero".$fl_criterio."_".$fl_calificacion_criterio."').addClass('border');
             </script>
             ";

     }else{

         echo"<script>
             $('#divborder_cero".$fl_criterio."_".$fl_calificacion_criterio."').removeClass('border');
             </script>";
         
     }


	 
	 
}
?>




