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
 
 
 
 $ds_name=$ds_first_name." ".$ds_last_name;
 
 
 echo"
 <p class='text-center' style='font-size:19px;'>$ds_name, $ds_email </p>
 
 ";
	

                           
?>