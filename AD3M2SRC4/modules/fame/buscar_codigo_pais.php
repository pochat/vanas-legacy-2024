<?php
  
  # Libreria de funciones
  require '../../lib/general.inc.php';
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  ValidaSesion( );
  
  # Recibe parametros
$fl_pais=RecibeParametroNumerico('fl_pais');



 #Busca el codigo de telefono del pais seleccionado
 $Query="SELECT ds_no_codigo_area FROM c_pais  WHERE fl_pais=$fl_pais";
 $row=RecuperaValor($Query);
 $ds_codigo_pais=!empty($row[0])?$row[0]:NULL;

 if(empty($fl_pais)){
     $ds_codigo_pais="<i class='fa fa-globe' aria-hidden='true'></i>";
 }
 

 Forma_CampoOculto('ds_codigo_pais',$ds_codigo_pais);

?>


<?php echo $ds_codigo_pais."&nbsp;&nbsp;"; ?>


