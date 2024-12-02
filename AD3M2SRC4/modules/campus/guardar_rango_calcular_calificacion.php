
<?php
# Libreria de funciones
require '../../lib/general.inc.php';


$fl_criterio=RecibeParametroNumerico('fl_criterio');
$no_porcentaje=RecibeParametroNumerico('rangeInput');
$peso_criterio=RecibeParametroNumerico('peso_criterio');
$fl_identificador=RecibeParametroNumerico('fl_identificador');
$cl_sesion=RecibeParametroHTML('fl_alumno');
$fl_programa=RecibeParametroNumerico('fl_programa');
$fg_calcula_promedio=RecibeParametroNumerico('fg_calcula_promedio');


#Se calcula el equivalente con respecto a su peso
$no_porcentaje_final= ($no_porcentaje * $peso_criterio) / 100 ;



#Eliminamos 
EjecutaQuery("DELETE FROM c_calculo_criterio_temp_admin WHERE fl_criterio=$fl_criterio and cl_sesion='$cl_sesion' AND fl_programa=$fl_programa  ");

#se realiza insret tabla temporal
$Query="INSERT INTO c_calculo_criterio_temp_admin (cl_sesion,fl_programa,fl_criterio,no_porcentaje,no_porcentaje_real) ";
$Query.="VALUES ('$cl_sesion',$fl_programa,$fl_criterio,$no_porcentaje_final,$no_porcentaje)";
EjecutaQuery($Query);


$Query="UPDATE c_com_criterio_admin SET no_porcentaje_equivalente=$no_porcentaje WHERE fl_criterio=$fl_criterio  and cl_sesion='$cl_sesion' AND fl_programa=$fl_programa ";
EjecutaQuery($Query);

#Sumamos todos los que pernetencen al identifiacador.
$Query ="SELECT SUM(no_porcentaje) FROM c_calculo_criterio_temp_admin  WHERE cl_sesion='$cl_sesion' AND fl_programa=$fl_programa ";
$row=RecuperaValor($Query);
$no_suma=$row[0];


$Query="UPDATE c_com_criterio_admin SET no_porcentaje_equivalente=$no_suma WHERE  cl_sesion='$cl_sesion' AND fl_programa=$fl_programa AND fg_com_final='1' ";
EjecutaQuery($Query);


$Query="UPDATE k_calificacion_admin SET no_calificacion=$no_suma WHERE  cl_sesion='$cl_sesion' AND fl_programa=$fl_programa ";
EjecutaQuery($Query);


?>

<div class="chart"  data-percent="<?php echo $no_suma; ?>" id="final_total">
    <span class="percent" style="font:18px Arial;"> <?php echo number_format($no_suma); ?> </span>
</div>

<!-----
<?php echo number_format($no_suma); ?>
    
    ----->
<?php   

echo"

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
			'line-height': 100 + 'px'
		})


	  var rangeInput =$no_porcentaje;

        if(rangeInput == 0 ){

          $('#divborder_cero_".$fl_criterio."_1').addClass('border');
          $('#divborder_cero_".$fl_criterio."_2').removeClass('border');
          $('#divborder_cero_".$fl_criterio."_3').removeClass('border');
          $('#divborder_cero_".$fl_criterio."_4').removeClass('border');
          $('#divborder_cero_".$fl_criterio."_5').removeClass('border');

        }else if ( (rangeInput > 0 ) && (rangeInput <= 49 )  ) {

          $('#divborder_cero_".$fl_criterio."_1').removeClass('border');
          $('#divborder_cero_".$fl_criterio."_2').addClass('border');
          $('#divborder_cero_".$fl_criterio."_3').removeClass('border');
          $('#divborder_cero_".$fl_criterio."_4').removeClass('border');
          $('#divborder_cero_".$fl_criterio."_5').removeClass('border');


        }else if ((rangeInput > 49 )&& (rangeInput <= 72)  ){
          $('#divborder_cero_".$fl_criterio."_1').removeClass('border');
          $('#divborder_cero_".$fl_criterio."_2').removeClass('border');
          $('#divborder_cero_".$fl_criterio."_3').addClass('border');
          $('#divborder_cero_".$fl_criterio."_4').removeClass('border');
          $('#divborder_cero_".$fl_criterio."_5').removeClass('border');


        }else if((rangeInput > 72) && (rangeInput <= 85)){
            $('#divborder_cero_".$fl_criterio."_1').removeClass('border');
            $('#divborder_cero_".$fl_criterio."_2').removeClass('border');
            $('#divborder_cero_".$fl_criterio."_3').removeClass('border');
            $('#divborder_cero_".$fl_criterio."_4').addClass('border');
            $('#divborder_cero_".$fl_criterio."_5').removeClass('border');

        }else{
            $('#divborder_cero_".$fl_criterio."_1').removeClass('border');
            $('#divborder_cero_".$fl_criterio."_2').removeClass('border');
            $('#divborder_cero_".$fl_criterio."_3').removeClass('border');
            $('#divborder_cero_".$fl_criterio."_4').removeClass('border');
            $('#divborder_cero_".$fl_criterio."_5').addClass('border');        
        
        
        }

    });
</script>
";

?>