<?php


require '../../AD3M2SRC4/lib/general.inc.php';

 # Recibe  datos de envio e email.
  $ds_first_name= RecibeParametroHTML('fname');
  $ds_last_name = RecibeParametroHTML('lname');
  $ds_email = RecibeParametroHTML('email');
  
 #Rcibe datos generales de registro.
 
 $ds_name_school= RecibeParametroHTML('ds_name_school');
 $fl_country= RecibeParametroNumerico('fl_country');
 $ds_pass1= RecibeParametroHTML('ds_pass1'); 
 $confirm_pass=RecibeParametroHTML('ds_pass2'); 
 $cl_clave_pais=RecibeParametroHTML('cl_iso_pais');
 $ds_coddigo_pais2=RecibeParametroHTML('ds_coddigo_pais2');#siempre viene vacio no importa:
 $ds_codigo_pais=RecibeParametroHTML('ds_codigo_pais');
 $ds_codigo_telefono=RecibeParametroHTML('ds_codigo_telefono');
 $ds_numero_telefono=RecibeParametroHTML('ds_numero_telefono');
 
 
 #Identificamos al instituto.
 $Query="SELECT fl_instituto FROM c_instituto WHERE ds_instituto='$ds_name_school'  ";
 $row=RecuperaValor($Query);
 $fl_instituto=$row['fl_instituto'];
 
 
 #perfil 13 es de teacher
 #Verificamos si la cuenta de correo ya esta activo entonces , ya no se le enviara el correo y mostrar mensaje de que la cuenta ya esta regitrada.
 $Query="SELECT COUNT(1),fg_system FROM c_usuario WHERE  ds_email='$ds_email' AND fg_system='F' ";
 $row=RecuperaValor($Query);
 $ds_email_registrado=$row[0];
 $fg_system=$row['fg_system'];
 
 if($fg_system != 'F'){
 
    /*MJD EL ENVIO DE COOREOS A TEACHES ES LIBRE
     
     #verificamos cuantos correos ha enviado este instituto SIN CONTAR EL ADMIN.
     $Query="SELECT COUNT(*) FROM k_envio_email_reg_selfp WHERE fl_invitado_por_instituto=$fl_instituto AND fg_tipo_registro<>'A' ";
     $row=RecuperaValor($Query);
     $no_correos_enviados=$row[0];
     
     $no_correos_permitidos=ObtenConfiguracion(102)  ;
     
     */
    //     if($no_correos_enviados < $no_correos_permitidos){ ##presenta send si esta dentro del rango permitido de envio de correos
            
                     
             
             $icono="<i class=\"fa fa-check-circle fa-3\"></i>";
             $texto_modal=ObtenEtiqueta(954);
             $ds_name=$ds_first_name." ".$ds_last_name.", ".$ds_email;
             $etq_boton=ObtenEtiqueta(955);
             $glypicon="<i class=\"fa fa-check-circle\" aria-hidden=\"true\"></i>";
     
    /*     }else{
     
                 echo"
		                    <style>
                            .text-success {
                                color: #DD452C !important;
                                }
                            </style>
                          ";
             
     
             
             #mostrara mensaje que el modo tiral solo se pueden enviar N numero de corroes.
             $icono="<i class=\"fa fa-times-circle fa-3\"></i>";
             $texto_modal="Trial mode ".ObtenConfiguracion(102)." students maximum ";  
             $etq_boton=ObtenEtiqueta(955);
             $glypicon="<i class=\"fa fa-check-circle\" aria-hidden=\"true\"></i>";
             
             
         }
     
     */
      
      
      
      
      
      
 }else{
 
     echo"
		<style>
        .text-success {
            color: #DD452C !important;
            }
        </style>
      ";
     
      $icono="<i class=\"fa fa-times-circle fa-3\"></i>";
      $texto_modal=ObtenEtiqueta(1523);
      $ds_name=$ds_first_name." ".$ds_last_name.", ".$ds_email;
      $etq_boton=ObtenEtiqueta(1534);
      $glypicon="<i class=\"fa fa-times-circle\" aria-hidden=\"true\"></i>";
     
      
 }
 
 

 
 
  
    echo"
		<style>
		.modal-dialog {
		width: 500px !important;
		margin: 250px auto !important;
		</style>
		";
    
?>                                                                                                                    

                    <button type="button" class="btn btn-primary btn-lg hidden" data-toggle="modal" data-target="#myModal" id="asignar">
                    Launch demo modal
                    </button>

                    <!-- Modal -->
                    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                    <div class="modal-dialog" role="document">
                    <div class="modal-content">
                    <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title text-center" id="myModalLabel" style="font-size:23px;"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i>&nbsp;<?php echo ObtenEtiqueta(956); ?></h4>
                    </div>
                    <div class="modal-body text-center">
                 
																											 

                  
                                 <h1 class="text-center text-success" style="font-size:22px;"><strong class="text-center">&nbsp;&nbsp;&nbsp;<?php echo $icono ;?></strong>&nbsp;<?php echo $texto_modal ?>&nbsp;&nbsp;&nbsp;</h1>

                    
		                        <p class='text-center' style='font-size:16px;'><?php echo $ds_name; ?></p>



                    </div>
                    <div class="modal-footer text-center">
	                    <button type="button" class="btn btn-primary" data-dismiss="modal"  style="font-size: 14px;border-radius: 10px; ">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $glypicon; ?>&nbsp;<?php echo $etq_boton; ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</button>
                                                                                                                                 
                    </div>
                    </div>
                    </div>
                    </div>


<?php 

 if ($ds_email_registrado==0){
 

?>
<script>
    $('#email').val('');
    $('#fname').val('');
    $('#lname').val('');
    $('#bot').attr('disabled', true);//se desabilita el boton de envio de mensajes
    $('#tabs5').attr('disabled', true);//se desabilita el boton 4 e impide la finalizacion del registro 
    //abre el modal color boxx
    document.getElementById('asignar').click();//clic automatico que se ejuta y sale modal
    $('#names').removeClass('has-success');  //remueva la classe suces
    $('#apellido').removeClass('has-success');  //remueva la classe suces
    $('#correo').removeClass('has-success');  //remueva la classe suces
</script>
 
 <?php 
 
 }else{
 ?>


<script>

    $('#bot').attr('disabled', true);//se desabilita el boton de envio de mensajes
    $('#tabs5').attr('disabled', true);//se desabilita el boton 4 e impide la finalizacion del registro 
    document.getElementById('asignar').click();//clic automatico que se ejuta y sale modal
</script>

<?php
 }
 
 ?>
 
 
