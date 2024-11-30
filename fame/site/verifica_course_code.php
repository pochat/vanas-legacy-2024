<?php
# Libreria de funciones	
require("../lib/self_general.php");


# Verifica que exista una sesion valida en el cookie y la resetea
$fl_usuario = ValidaSesion(False,0, True);

# Recibe parametros
$cl_course_code = RecibeParametroHTML('cl_course_code');
$fl_pais=RecibeParametroNumerico('fl_pais');
$fl_estado=RecibeParametroNumerico('fl_estado');
$clave=RecibeParametroNumerico('clave');


#Verifica si hay un codigo existente
if(empty($clave)){  
    $Query="SELECT COUNT(*) FROM c_course_code WHERE cl_course_code='$cl_course_code' AND fl_pais=$fl_pais  ";

}else{
    $Query="SELECT COUNT(*) FROM c_course_code WHERE cl_course_code='$cl_course_code' AND fl_course_code <> $clave ";   

}
    $row=RecuperaValor($Query);


if($row[0]>0){

   echo"<script>
           // document.getElementById('cl_course_code').style.borderColor = 'red';
           // document.getElementById('cl_course_code').style.background = '#fff0f0';
            if ( (cl_course_code.length > 0)&&(nb_course_code.length>0)&&(fl_pais>0)){
		      $('#aceptar').removeClass('disabled');	
		    }else{
		      $('#aceptar').addClass('disabled');
	        }
            $('#msjerror').removeClass('hidden');
            
		</script>	
   ";

}else{
    echo"<script>
	
	      if ( (cl_course_code.length > 0)&&(nb_course_code.length>0)&&(fl_pais>0)){
		      $('#aceptar').removeClass('disabled');	
		  }else{
		      $('#aceptar').addClass('disabled');
	      }
	
	
            //$('#aceptar').removeClass('disabled');
            $('#msjerror').addClass('hidden');
         </script>
   ";
}






?>

