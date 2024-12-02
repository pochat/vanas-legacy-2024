<?php

  # Libreria de funciones
  require '../../lib/general.inc.php';
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  ValidaSesion( );
  
  # Recibe parametros
  $clave = RecibeParametroNumerico('clave');
  $fg_error = RecibeParametroNumerico('fg_error');
  
  # Determina si es alta o modificacion
  if(!empty($clave))
    $permiso = PERMISO_DETALLE;
  else
    $permiso = PERMISO_ALTA;
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermiso(FUNC_LICENCES, $permiso)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }

  # Inicializa variables
  if(!$fg_error) { // Sin error, viene del listado
    if(!empty($clave)) { // Actualizacion, recupera de la base de datos
        
        #Elimina lo existente en la tabla temporal.
        # Elimina los registro asociados
        EjecutaQuery("DELETE FROM c_princing_temporal WHERE fl_instituto= $clave ");
        
       
      
          #Recuperamos datos del instituto:
          $Query="SELECT I.ds_instituto,ds_pais,fg_princing_default,I.cl_tipo_instituto,I.fl_instituto_rector FROM c_instituto I
          JOIN c_pais P ON P.fl_pais=I.fl_pais 
          WHERE I.fl_instituto=$clave";
          $row=RecuperaValor($Query);
          $nb_instituto=$row['ds_instituto'];
          $ds_pais=$row['ds_pais'];
		  $fg_princing_default=$row['fg_princing_default'];
          $cl_tipo_instituto=$row['cl_tipo_instituto'];
          $fl_instituto_rector=$row['fl_instituto_rector'];
          

          if($cl_tipo_instituto==2){
              $nb_rector='<small class=\'text-muted\'><i>'.ObtenEtiqueta(2524).'</i></small>';
          }else{
              $nb_rector="";
          }

          #Recuperamos datos del isntituto Rector.
          if($fl_instituto_rector){
              $Query="SELECT ds_instituto FROM c_instituto WHERE fl_instituto=$fl_instituto_rector ";
              $ro=RecuperaValor($Query);
              $nb_instituto_rector='<small class=\'text-muted\' ><i>'.$ro['ds_instituto'].'</i></small><br>';
          }else{
              $nb_instituto_rector=""; 
          }
          



		  if($fg_princing_default)
			  $chequed_princing=" checked='checked' ";
		  else
			  $chequed_princing="";
          
          
          #Insertamos lo que existe en c_princin ala tabla temporal.
          
          $Query="SELECT fl_princing,no_ini,no_fin,mn_mensual,mn_anual,ds_descuento_mensual,mn_descuento_licencia,fg_activo FROM c_princing WHERE 1=1 AND fl_instituto=$clave ";
          $rs_actual = EjecutaQuery($Query);
            $tot_registros_actuales = CuentaRegistros($rs_actual);
            $contador=0;
            for($i = 0; $row = RecuperaRegistro($rs_actual); $i++) {
                
                $fl_princing=$row['fl_princing'];
                $no_ini= $row['no_ini'];
                $no_fin= $row['no_fin'];
                $mn_mensual= $row['mn_mensual'];
                $mn_anual= $row['mn_anual'];
                $ds_descuento_mensual=$row['ds_descuento_mensual'];
                $mn_descuento_licencia=$row['mn_descuento_licencia'];
                $fg_activo=$row['fg_activo'];
                $contador++;
                
                
                if(empty($mn_descuento_licencia))
                    $mn_descuento_licencia="NULL";
                
                if(empty($ds_descuento_mensual))
                    $ds_descuento_mensual="NULL";
                if(empty($ds_descuento_anual))
                    $ds_descuento_anual="NULL";
                
                
                if(($no_fin==0)||(empty($no_fin)))
                    $no_fin="NULL";
                
                $Query="INSERT INTO c_princing_temporal (fl_princing,fl_instituto,no_ini ,no_fin,ds_descuento_mensual,mn_mensual,mn_descuento_licencia, ";
                $Query .=" mn_anual,fg_activo ) ";
                $Query.="VALUES ($fl_princing,$clave,$no_ini,$no_fin,$ds_descuento_mensual,$mn_mensual,$mn_descuento_licencia,$mn_anual,'$fg_activo')";
                $fl_princing_temporal=EjecutaInsert($Query);
                
                
            }
          
          
          
          
          
     
      
    }
    else { // Alta, inicializa campos
     
        
        

        
     
    }
  #varuables error
    
    
  }
  else { // Con error, recibe parametros (viene de la pagina de actualizacion)
  
    
  }
  

      
  # Presenta forma de captura
  PresentaHeader( );
  PresentaEncabezado('145');
 

  # Inicia forma de captura
  Forma_Inicia($clave);
  if($fg_error)
    Forma_PresentaError( );
  
  
Forma_Espacio();
 
  
 ?>

<style>
    .jarviswidget .widget-body {
        min-height: 4px !important;
    }
    .ui-spinner-down, .ui-spinner-up {
        background: #0092cd !important;
    }
    .ui-spinner-down {
        background: #0092cd !important;
    }
    .ui-spinner-down:active, .ui-spinner-down:focus, .ui-spinner-down:hover {
        background: #0092cd !important;
    }
    .table {
    margin-bottom: 0px !important;
}
</style>

<?php


echo"

<script>
function PresentaTabla(){
//alert('entro');


    var fl_accion=1;
              
    
       $.ajax({
           type: 'POST',
            url: 'presenta_tabla.php',
            data: 'fl_accion=' + fl_accion +
                  '&fl_instituto='+ $clave ,
            async: true,
            success: function (html) {
                $('#presenta_tabla').html(html);
            }
        });



}
  
 </script>

";


echo"
<div class='row'>
        <div class='col-md-1'>
            &nbsp;
       </div>
        <div class='col-md-10 text-center'  style='font-weight:bold;font-size:1em;background:;'>
        
                <br />
                
                    <table class='table table-bordered' width='100%'>
                                             <thead>
												<tr>
													<th colspan='5' style='font-size:1.2em;' class='text-left'>
                                                    ".ObtenEtiqueta(2524).': '.$nb_instituto_rector."
                                                    ".ObtenEtiqueta(933).": ".$nb_instituto." <br/> ".ObtenEtiqueta(934).": ".$ds_pais."
                                                    <br>Pricing Default: ";
													 ?>
													 
														<span class="onoffswitch">
															<input name="princi_default" class="onoffswitch-checkbox" <?php echo $chequed_princing;?> id="princi_default" type="checkbox">
															<label class="onoffswitch-label" for="princi_default"> 
																<span class="onoffswitch-inner" style="font-size:12px;" data-swchon-text="On" data-swchoff-text="Off"></span> 
																<span class="onoffswitch-switch"></span> 
															</label> 

														</span> 
													 
													 <script>

                        $(document).ready(function () {

                            $('#princi_default').change(function () {

                                if ($('#princi_default').is(':checked')) {

                                    var checke= 1;
                                } else {
                                    var checke= 0;

                                }
                               
								
                                //pasamos epor ajax el valor para indicar que el playlist sera publico o privado
                                $.ajax({
                                    type: 'POST',
                                    url: 'define_precios_default.php',
                                    data: 'fl_instituto=<?php echo $clave; ?>'+
                                          '&checke='+checke,
                                          

                                    async: true,
                                    success: function (html) {
										
										if(checke==1){
											
											
											$.smallBox({
												title : "&nbsp;",
												content : "<?php echo ObtenEtiqueta(2330); ?>",
												color : "#739E73",
												timeout: 4000,
												icon : "fa fa-check"
											});
											
											
											
										}else{
											
											
											$.smallBox({
												title : "&nbsp;",
												content : "<?php echo ObtenEtiqueta(2331); ?>",
												color : "#C46A69",
												timeout: 4000,
												icon : "fa fa-warning shake animated"
											});
											
											
											
											
										}
										
										
										
                                        //$('#presenta_opc_renovacion').html(html);
                                    }
                                });



                            });


                        });
                         </script>

													 
													 
													 
													 <?php
	echo"												 
													 </th>
												
												</tr>

											</thead>
                   </table>
        
                  <br/>
        
                     <table class='table table-bordered' width='100%'>

                                            <thead>
												<tr>
													<th colspan='6' style='font-weight:bold;font-size:1.2em;' class='text-center'>".ObtenEtiqueta(1512)." </th>
												
												</tr>

                                               


											</thead>

                                             <tbody> 
        
                                               <tr>
                                                    <td width=\"25%\" class=\"text-center\" >".ObtenEtiqueta(1501)."<p><em style='color:#888686;'>".ObtenEtiqueta(1504)."</em> </p></td>
                                                     <td width=\"15%\" class=\"text-center\">".str_uso_normal(ObtenEtiqueta(1749))."<p><em style='color:#888686;'> </em></p></td>
                                                    <td width=\"15%\" class=\"text-center\" >".str_uso_normal(ObtenEtiqueta(1551))."<p><em style='color:#888686;'></em> </p></td>
                                                    <td width=\"15%\" class=\"text-center\">".str_uso_normal(ObtenEtiqueta(1502))."<p><em style='color:#888686;'>".ObtenEtiqueta(1505)." </em></p></td>
                                                    <td width=\"15%\" class=\"text-center\">".ObtenEtiqueta(1503)."<p><em style='color:#888686;'>".ObtenEtiqueta(1506)."</em></p></td>
                                                     <td width=\"15%\" class=\"text-center\">
                                                     
                                                            <a class=\"btn btn-primary\" href=\"javascript:void(0)\"  onClick=\"AddPrincing()\"><i class=\"fa fa-plus fa-1x\"></i> ".ObtenEtiqueta(10)."</a>

                                                     </td>
                                                    
                                                </tr>
                                                
                                               </tbody>
                                               
                                               </table>
          
        </div>
        
        <div class='col-md-1'>
            &nbsp;
       </div>
        
</div>

";




echo"
<div class='row'>
    <div class='col-md-1'>
            &nbsp;
       </div>

    
    <div class='col-xs-10 col-sm-12 col-lg-10' id='presenta_tabla'>
               


    </div>
    
    <div class='col-md-1'>
            &nbsp;
       </div>
</div>



";






echo"<script>
PresentaTabla();
</script>";


echo"
<script>

function AddPrincing(){


var fl_accion=2;
              
       $.ajax({
           type: 'POST',
           
            url: 'agregar_precio.php',
            data: 'fl_accion=' + fl_accion +
                  '&fl_instituto='+ $clave ,
            async: true,
            success: function (html) {
            }
        });
        
PresentaTabla();
}



</script>


";


#Mostrara el rpincing para liberacion de cursos.
if($clave==1){

 
    
    $Query="SELECT ds_descuento_mensual, ds_descuento_anual,mn_mensual,mn_anual FROM c_princing_course WHERE 1=1 ";
    $row=RecuperaValor($Query);
    $porcentaje_mes=$row['ds_descuento_mensual']; 
    $porcentaje_anio=$row['ds_descuento_anual'];
    $mn_mes=$row['mn_mensual'];
    $mn_anio=$row['mn_anual'];
    

    echo"<br><br>
<div class='row'>
       <div class='col-md-1'>
            &nbsp;
       </div>
        <div class='col-md-10 text-center'  >
        
                <br />
                
                    <table class='table table-bordered' width='100%'>
                                             <thead>
												<tr>
													<th colspan='5' style='font-size:1.2em;' class='text-left'>".ObtenEtiqueta(2124).":
                                                     </th>
												
												</tr>

											</thead>
                                            
                                            <tbody>
                                            
                                                 <tr style='font-weight:bold;font-size:1em;'>
                                                  
                                                    <td width=\"15%\" class=\"text-center\">".ObtenEtiqueta(1705)."<p><em style='color:#888686;font-weight:bold;font-size:1em;'>% Discount </em></p></td>
                                                    <td width=\"15%\" class=\"text-center\" >".ObtenEtiqueta(1706)."<p><em style='color:#888686;font-weight:bold;font-size:1em;'>% Discount</em> </p></td>
                                                    <td width=\"15%\" class=\"text-center\">".ObtenEtiqueta(1705)."<p><em style='color:#888686;font-weight:bold;font-size:1em;'> </em></p></td>
                                                    <td width=\"15%\" class=\"text-center\">".ObtenEtiqueta(1706)."<p><em style='color:#888686;font-weight:bold;font-size:1em;'> </em></p></td>
                                                    
                                                    
                                                </tr>
                                            
                                            
                                            
                                                <tr>
                                                    <td>    <div class='input-group input-group-sm' align='center'>
                                                              <span class='input-group-addon'>%</span>
                                                              <input type='text' class='form-control input-sm' placeholder='' id='porcentaje_mes' name='porcentaje_mes' value='".$porcentaje_mes."' style='text-align: right;'/>
                                                            </div> 
                                                    </td>
                                                    <td>  <div class='input-group input-group-sm' align='center'>
                                                              <span class='input-group-addon'>%</span>
                                                              <input type='text' class='form-control input-sm' placeholder='' id='porcentaje_anio' name='porcentaje_anio' value='".$porcentaje_anio."' style='text-align: right;'/>
                                                            </div> 
                                                   </td>
                                                    <td>  <div class='input-group input-group-sm' align='center'>
                                                              <span class='input-group-addon'>$</span>
                                                              <input type='text' class='form-control input-sm' placeholder='' id='mn_mes' name='mn_mes' value='".$mn_mes."' style='text-align: right;'>
                                                            </div>  
                                                    </td>
                                                    <td>  <div class='input-group input-group-sm' align='center'>
                                                              <span class='input-group-addon'>$</span>
                                                              <input type='text' class='form-control input-sm' placeholder='' id='mn_anio' name='mn_anio' value='".$mn_anio."' style='text-align: right;'>
                                                            </div>
                                                    </td>
                                                </tr>
                                            
                                            </tbody>
                                            
                                            
                   </table>
        
                  <br/>
        </div>
        
        
       <div class='col-md-1'>
            &nbsp;
       </div>
</div>
";


 echo"
 <script>
 
    $('#porcentaje_anio').change(function () {
  
       CalculaPorcntajeCurso();
  
    });
	
	 
    $('#mn_mes').change(function () {
  
       CalculaPorcntajeCurso();
  
    });
	
	 
   // $('#mn_anio').change(function () {
  
    //   CalculaPorcntajeCurso();
  
   // });
	
 
    function CalculaPorcntajeCurso(){
    
    
        var mn_porcentaje_anio = $('#porcentaje_anio').val();
        var mn_mes = $('#mn_mes').val();
        var mn_anio=$('#mn_anio').val();
    
         $('#mn_anio').val(   mn_mes - ( Math.round(mn_mes * mn_porcentaje_anio / 100)));
    }
    
 
 </script>
 
 ";   
   
    
    
}






  
  # Verifica que el usuario tenga permiso para guardar los cambios (solo en modo modificacion)
  if($permiso == PERMISO_DETALLE)
    $fg_guardar = ValidaPermiso(FUNC_LICENCES, PERMISO_MODIFICACION);
  else
    $fg_guardar = True;
  Forma_Termina($fg_guardar);
  
  
  
  
  
  
  # Pie de Pagina
  PresentaFooter( );
  echo"</div>";	
  

  
?>