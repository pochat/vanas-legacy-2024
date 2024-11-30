
<?php
# Libreria de funciones	
require("../../common/lib/cam_general.inc.php");


$fl_criterio=RecibeParametroNumerico('fl_criterio');
$no_porcentaje=RecibeParametroNumerico('rangeInput');


?>





<script>
    $(document).ready(function () {
 

	  var rangeInput =<?php echo $no_porcentaje; ?>;

        if(rangeInput == 0 ){

          $('#divborder_cero_<?php echo $fl_criterio;?>_1').addClass('border');
          $('#divborder_cero_<?php echo $fl_criterio;?>_2').removeClass('border');
          $('#divborder_cero_<?php echo $fl_criterio;?>_3').removeClass('border');
          $('#divborder_cero_<?php echo $fl_criterio;?>_4').removeClass('border');
          $('#divborder_cero_<?php echo $fl_criterio;?>_5').removeClass('border');

        }else if ( (rangeInput > 0 ) && (rangeInput <= 49 )  ) {

          $('#divborder_cero_<?php echo $fl_criterio;?>_1').removeClass('border');
          $('#divborder_cero_<?php echo $fl_criterio;?>_2').addClass('border');
          $('#divborder_cero_<?php echo $fl_criterio;?>_3').removeClass('border');
          $('#divborder_cero_<?php echo $fl_criterio;?>_4').removeClass('border');
          $('#divborder_cero_<?php echo $fl_criterio;?>_5').removeClass('border');


        }else if ((rangeInput > 49 )&& (rangeInput <= 72)  ){
          $('#divborder_cero_<?php echo $fl_criterio;?>_1').removeClass('border');
          $('#divborder_cero_<?php echo $fl_criterio;?>_2').removeClass('border');
          $('#divborder_cero_<?php echo $fl_criterio;?>_3').addClass('border');
          $('#divborder_cero_<?php echo $fl_criterio;?>_4').removeClass('border');
          $('#divborder_cero_<?php echo $fl_criterio;?>_5').removeClass('border');


        }else if((rangeInput > 72) && (rangeInput <= 85)){
            $('#divborder_cero_<?php echo $fl_criterio;?>_1').removeClass('border');
            $('#divborder_cero_<?php echo $fl_criterio;?>_2').removeClass('border');
            $('#divborder_cero_<?php echo $fl_criterio;?>_3').removeClass('border');
            $('#divborder_cero_<?php echo $fl_criterio;?>_4').addClass('border');
            $('#divborder_cero_<?php echo $fl_criterio;?>_5').removeClass('border');

        }else{
            $('#divborder_cero_<?php echo $fl_criterio;?>_1').removeClass('border');
            $('#divborder_cero_<?php echo $fl_criterio;?>_2').removeClass('border');
            $('#divborder_cero_<?php echo $fl_criterio;?>_3').removeClass('border');
            $('#divborder_cero_<?php echo $fl_criterio;?>_4').removeClass('border');
            $('#divborder_cero_<?php echo $fl_criterio;?>_5').addClass('border');        
        
        
        }

    });
</script>