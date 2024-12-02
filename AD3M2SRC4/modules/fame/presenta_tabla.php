<?php
  
  # Libreria de funciones
  require '../../lib/general.inc.php';
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  ValidaSesion( );
  
  # Recibe parametros
$fl_accion=RecibeParametroNumerico('fl_accion');
$fl_princing=RecibeParametroNumerico('fl_princing');
$fl_instituto=RecibeParametroNumerico('fl_instituto');



#Recuperamos datos 




//echo"	<link rel='stylesheet' type='text/css' media='screen' href='".PATH_SELF_CSS."/smartadmin-production.css'>
	//	<link rel='stylesheet' type='text/css' media='screen' href='".PATH_SELF_CSS."/smartadmin-skins.css'>";


echo"
<style>
.ui-spinner {
    width: 70% !important;
    }
</style>

";


echo" 
        <table class='table table-bordered' width='100%'>

                                             <tbody>
";





$Query  = "SELECT fl_princing,no_ini,no_fin,mn_mensual,mn_anual,ds_descuento_mensual,mn_descuento_licencia,fg_activo ";   
$Query .= "FROM c_princing_temporal ";
$Query .= "WHERE 1=1 AND fl_instituto=$fl_instituto ORDER BY fl_princing ASC ";
$rs = EjecutaQuery($Query);
$tot_registros = CuentaRegistros($rs);
$contador=0;
$contador_input=0;
Forma_CampoOculto('tot_registros', $tot_registros);
for($i = 0; $row = RecuperaRegistro($rs); $i++) {
    
    $fl_princing=$row['fl_princing'];
    $no_ini= $row['no_ini'];
    $no_fin= $row['no_fin'];
    $mn_mensual= $row['mn_mensual'];
    $mn_anual= $row['mn_anual'];
    $mn_descuento_licencia=$row['mn_descuento_licencia'];
    $ds_descuento_mensual= $row['ds_descuento_mensual'];
    $fg_activo=$row['fg_activo'];
    
    $contador++;
    
    $contador_input++;
    
    Forma_CampoOculto('fl_princing_'.$i,$fl_princing);
    
    
   
    
    
    
    $fila2 = $contador / 2;
    if (is_int($fila2)) {
         $background = "#fff" ;
    } else 
    
    {
        $background = "#F4F3F3" ;
       
    }
    
    
    

    $no_inicial="no_ini_".$i;
    
    echo"<tr class='css_tabla_detalle_bg' style='background:$background;'>
	            <td width='25%' >
                        <div class='widget-body'>
                                                            <div class='col-xs-12 col-sm-5 col-lg-5'>
                                                                
                                                                
                                                                        <div class='input-group input-group-sm' align='center'>
                                                                         
                                                                          <input type='text' class='form-control input-sm' placeholder='' id='$no_inicial' name='$no_inicial' value='".$no_ini."' style='text-align: right;' readonly />
                                                                        
                                                                         
                                                                          </div>


												                       


                                                            </div>
                                        
                                        
                                       
        ";
    
    
    $no_anterior=$no_inicial;
    
    
    
    $no=$i +1 ;
    $no_inicial_siguiente="no_ini_".$no;
    
    
    
    echo"
<script>



</script>

"; 
    
    $contador=$i+1; 
    $spinner="spinner2_".$contador;
    
   
    
    
   echo"                                                   

                                        <div class='col-xs-12 col-sm-2 col-lg-2'  align='center'>
                                                              
                                                               <i class='fa fa-minus text-center' aria-hidden='true' style='margin-top:10px;'></i>
                                                            </div>
                                                            <div class='col-xs-12 col-sm-5 col-lg-5'>
                                                                 <div class='form-group'>
						
															        <input class='form-control spinner-left'  id='".$spinner."' name='".$spinner."' value='$no_fin' type='text'>
													                
                                                                   
                                                                    </div>
                                                            </div>

                                        </div>
                                        ";
   
   if($contador<>$tot_registros){
   echo"
                                        
                                             <p class='text-danger text-left' id='danger$contador' style='display:none;'><i class=\"fa fa-exclamation-triangle\" aria-hidden=\"true\"></i>
							                                              &nbsp;  ".ObtenEtiqueta(1550)."
						                     </p>
    ";
   
   }
   
   
   echo"
";
   
$smallBox="




";

echo"   
                                   
                                   

<script type='text/javascript'>

    
    $(document).ready(function () {

        // Spinners
        $('#".$spinner."').spinner();
            $('#spinner-decimal').spinner({
                step: 0.01,
                numberFormat: 'n'
            });
        });
    
        
        $('.ui-spinner-button').click(function() { $(this).siblings('input').change(

        ); });

        $('#".$spinner."').spinner().change(function(){
        
               // alert($(this).spinner('value'));//referencia para saber  el no actual del spiner
                 var valor_actual=$(this).spinner('value');
                 var valor_anterior= $('#$no_anterior').val();

                 $('#$no_inicial_siguiente').val(valor_actual + 1); //al texbox siguiente le sumamos uno.
                 
                 if(valor_actual <= valor_anterior){
                    //presenta mensaje de error de que no se permite que el no. sea menor.
                        document.getElementById('danger$contador').style.display = 'inline';

                  }
                  else
                  {
                        document.getElementById('danger$contador').style.display = 'none';
                  
                  }
                  
                  
                  if(valor_actual <=0 ){
                        
                    $('#$spinner').val(1);
                  }
                  
                  
                   ActualizaPrincing$contador($fl_princing);
                  
                ";


echo"
                
            });
    

            
            
       
            
            
            
</script>

";
    
    $id_mensual="mn_mensual_".$i;
    $id_anual="mn_anual_".$i;
    
    
    $id_mensual_anterior=$i-1;
    
    echo"
                
                </td>";
    
     
    $id_porcentaje="mn_porcentaje_".$i;
    
    $cota_input_mes=$contador;
    
    $id_porcentaje_mes="mn_porcentaje_mes_".$cota_input_mes;
    
    if($contador_input==1)
        $input_disable="disabled";
    else
        $input_disable="";
    
    echo"
                <td width='15%' align='center' >
                             <div class='row'>
                                
                                <div class='col-md-12'>
                                    <div class='input-group input-group-sm' align='center'>
                                      <span class='input-group-addon'>%</span>
                                      <input type='text' class='form-control input-sm' placeholder='' id='$id_porcentaje_mes' name='$id_porcentaje_mes' value='".$mn_descuento_licencia."' style='text-align: right;' $input_disable/>
                                    </div>
                                </div>
                
                            </div>
                
                </td>

    ";
    
    
    
    
    
    
    echo"
                <td width='15%' align='center' >
                             <div class='row'>
                               
                                <div class='col-md-12'>
                                    <div class='input-group input-group-sm' align='center'>
                                      <span class='input-group-addon'>%</span>
                                      <input type='text' class='form-control input-sm' placeholder='' id='$id_porcentaje' name='$id_porcentaje' value='".$ds_descuento_mensual."' style='text-align: right;'>
                                    </div>
                                </div>
                
                            </div>
                
                </td>

    ";
    
    if($fg_activo==1)
        $cheked="checked";
    else
        $cheked="";
    
    
    echo"
			    
                <td width='15%' align='center' >
                        <div class='row'>
                                
                                
                                 <div class='col-md-12'>
                                       
                                            <div class='input-group input-group-sm' align='center'>
                                              <span class='input-group-addon'><input type='checkbox' name='check_$i' id='check_$i'  $cheked/>   $</span>
                                              <input type='text' class='form-control input-sm' placeholder='' id='$id_mensual' name='$id_mensual' value='".$mn_mensual."' style='text-align: right;'>
                                            </div>
                                          
                                </div>
                        </div>
                        
                                
                            
                ";
                        
    echo"       </td>
                
                <td width='15%' align='center' >
                             <div class='row'>
                                
                                <div class='col-md-12'>
                                    <div class='input-group input-group-sm' align='center'>
                                      <span class='input-group-addon'>$</span>
                                      <input type='text' class='form-control input-sm' placeholder='' id='$id_anual' name='$id_anual' value='".$mn_anual."' style='text-align: right;'>
                                    </div>
                                </div>
                
                </div>

                ";
             
    echo"
                
                </td>
                
                
                
                
                 <td width='15%' align='center' >   
                     
                 <a href=\"javascript:void(0);\" onclick=\"DeletePrincing($fl_princing)\"  class=\"btn btn-xs btn-default\" style=\"margin-left:5px\"><i class=\"fa  fa-trash-o\"></i></a>
                </div>
          </tr>
                ";
    
    
    
    
echo"
<script>
  $(document).ready(function () {
       
       
       
          
      
  
       //input del primer porcentaje mes.
       $('#mn_porcentaje_mes_$cota_input_mes').change(function () {
            CalculaPorcentaje_mes$contador();
            ActualizaPrincing$contador($fl_princing);
           
        });
  
  
      //input del segundo porcentaje , la que calucla el anual
       $('#$id_porcentaje').change(function () {
      
            CalculaPorcentaje$contador();
            ActualizaPrincing$contador($fl_princing);
           
        });
        
        
        
         $('#$id_mensual').change(function () {
         
            CalculaPorcentaje$contador();
            
            ActualizaPrincing$contador($fl_princing);
        });
        
        
         $('#$id_anual').change(function () {
            ActualizaPrincing$contador($fl_princing);
         });
       
       
        
        
  });      
    
  
    //calcula el imput precio mensual.
function CalculaPorcentaje_mes$contador(){

        var mn_mensual =  $('#mn_mensual_0').val();
        var mn_porcentaje = $('#mn_porcentaje_mes_$cota_input_mes').val();
       
        //alert(mn_mensual)
        
       $('#$id_mensual').val(  mn_mensual - ( parseInt(mn_mensual * mn_porcentaje / 100)));
       
}
  
  
  
  //para calcular el input anual.
function CalculaPorcentaje$contador(){

        var mn_mensual = $('#mn_mensual_0').val();
        var mn_porcentaje = $('#$id_porcentaje').val();

     
        
        $('#$id_anual').val(   mn_mensual - ( Math.round(mn_mensual * mn_porcentaje / 100)));

        

}


  function ActualizaPrincing$contador(fl_princing) {
  
                    var fl_princing=fl_princing;
                    var no_inicial=$('#$no_inicial').val();
                    var no_final=$('#$spinner').val();
                    var mn_porcentaje_mes=$('#mn_porcentaje_mes_$cota_input_mes').val();
                    var porcentaje= $('#$id_porcentaje').val();
                    var mn_mes=$('#$id_mensual').val();
                    var mn_anual=$('#$id_anual').val();
                    var checked=$('#check_$i').val();
                    
                    
                    $.ajax({
                               type: 'POST',
                                url: 'actualiza_princing.php',
                                data: 'fl_princing=' + fl_princing +
                                      '&no_inicial ='+ no_inicial +
                                      '&no_final ='+ no_final +
                                      '&porcentaje ='+ porcentaje +
                                      '&mn_porcentaje_mes='+mn_porcentaje_mes+
                                      '&checked='+ checked +
                                      '&mn_mes =' + mn_mes +
                                      '&mn_anual ='+ mn_anual
                                      ,
                                async: true,
                                success: function (html) {
                                }
                            });
                    
                    
                    
  }     
</script>

";    
    
    
    
    
    
    
    
   
}





echo"

                                             </tbody>
       </table>                                      
";















echo"
<script>

    function DeletePrincing(fl_princing){


    var clave=fl_princing;
              
           $.ajax({
               type: 'POST',
                url: 'eliminar_princing.php',
                data: 'clave=' + clave
                      ,
                async: true,
                success: function (html) {
                }
            });
        
    PresentaTabla();
    }
    </script>


";









?>


