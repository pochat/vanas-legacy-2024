<?php 
# Libreria de funciones
require("../lib/self_general.php");

  # get the list of items id separated by cama (,)
  $valores = $_POST['valores'];
  $total=$_POST['total'];
 
 $total=substr_count($valores, ',');
 $total=$total+1;
 $valor=explode(",",$valores);
 echo"<ul class='list-inline'>";
 for($i = 0; $i <= $total; $i++){
    
     
     $fl_envio=$valor[$i];
     
     if($fl_envio){
         
         if(is_numeric($fl_envio)) {
             
                     #Recuperamos el email del ya del usuario 
                     $Query="SELECT ds_email FROM c_usuario WHERE fl_usuario=$fl_envio  ";
                     $ro=RecuperaValor($Query);
                     $ds_email=str_texto($ro['ds_email']);

                    /* # Obtenemos el usuario y el programa  
                     $row = RecuperaValor("SELECT  fl_usuario_sp, fl_programa_sp, fe_entregado, fe_inicio_programa, fe_final_programa,ds_progreso FROM k_usuario_programa WHERE fl_usu_pro=$fl_envio");
                     $fl_usuario_sp = $row[0];

                     if($fl_usuario_sp){
                 
                         #Recuperamos el email del ya del usuario 
                         $Query="SELECT ds_email FROM c_usuario WHERE fl_usuario=$fl_usuario_sp  ";
                         $ro=RecuperaValor($Query);
                         $ds_email=str_texto($ro['ds_email']);

                     }else{
                         $Query="SELECT ds_email FROM k_envio_email_reg_selfp WHERE fl_envio_correo=$fl_envio ";
                         $row=RecuperaValor($Query);
                         $ds_email=str_texto($row['ds_email']);
                 
                 
                     }
                     */

                     if($ds_email)
                         echo "<li>".$ds_email."</li>";


             }else{
                 

                 $total2=substr_count($fl_envio, '-');
                 $valor2=explode("-",$fl_envio); $clve_envio=$valor2[1];          
                 for($im = 0; $im <= $total2; $im++){

                     $fl_envio=$valor2[$im];

                     if(is_numeric($fl_envio)) {

                         if($clve_envio<>'FA'){

                             #Recuperamos el email de los que aun no han sido confirmados.
                             $Query="SELECT ds_email FROM k_envio_email_reg_selfp WHERE fl_envio_correo=$fl_envio ";
                             $row=RecuperaValor($Query);
                             $ds_email=str_texto($row['ds_email']);

                             if($ds_email)
                                 echo "<li>".$ds_email."</li>";


                         }else{
                             
                             #Recuperamos el email del ya del usuario que falta su autorizacion.
                             $Query="SELECT ds_email FROM c_usuario WHERE fl_usuario=$fl_envio  ";
                             $ro=RecuperaValor($Query);
                             $ds_email=str_texto($ro['ds_email']);

                             if($ds_email)
                                 echo "<li>".$ds_email."</li>";

                         }


                     }

                     
                 }


             }







                  
             
       
    
     }
     
 }
  echo"</ul>";
  

  
  
  
  
?>