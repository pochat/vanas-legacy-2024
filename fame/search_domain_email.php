<?php

  # Libreria de funciones
  require_once("../lib/sp_general.inc.php");

  # Recibe  datos
  $ds_first_name= RecibeParametroHTML('ds_firts_name');
  $ds_last_name = RecibeParametroHTML('ds_last_name');
  $ds_email = RecibeParametroHTML('ds_email');
  $fg_aceptar = RecibeParametroBinario('fg_aceptar');

  #Obtenemos el dominio perteneciente al cuenat de cooreo electronico.
  $dominio=explode("@",$ds_email);
  $nombre=$dominio[0];
  $dominio=$dominio[1];

  $dominio_campus = ObtenConfiguracion(116);
  $ds_dominio="".$dominio_campus."/login.php"; #bueno

  #Verificamos si la cuenta de correo ya esta activo entonces , ya no se le enviara el correo y mostrar mensaje de que la cuenta ya esta regitrada.
  $Query="SELECT COUNT(1),fg_system FROM c_usuario WHERE ds_email='$ds_email' AND fg_system='F' ";
  $row=RecuperaValor($Query);
  $ds_email_registrado=$row[0];
  $fg_system=str_texto($row['fg_system']);

  if($fg_system != 'F'){
  
      $titulo=ObtenEtiqueta(915);
      $contenido="<strong><i class=\"fa fa-check-circle fa-3\"></i></strong>&nbsp;".ObtenEtiqueta(1522);
      $sms=str_uso_normal(ObtenEtiqueta(916));
      $classtexto="text-success";
      
  }else{
  
      $titulo=ObtenEtiqueta(915); 
      $contenido="<strong><i class=\"fa fa-times-circle fa-3\"></i></strong>&nbsp;".ObtenEtiqueta(1524);
      $sms=ObtenEtiqueta(1523);
      $classtexto="text-danger";
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
  boton que se ejecua automaticamente
</button>

<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header text-center">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title text-center" id="myModalLabel" style="font-size:23px;"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i>&nbsp;<?php echo $titulo; ?></h4>
      
      
      </div>
      <div class="modal-body">

          <h1 class="text-center <?php echo $classtexto;?>"> <?php echo $contenido; ?></h1>
          <br />
          <p class="text-center" style="font-size:15px;"> <?php echo $sms; ?></p>
          
          

      </div>
      <div class="modal-footer text-center">
          <?php 
          if($ds_email_registrado==0){
          ?>

        <!--<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>-->
        <a href="<?php echo $ds_dominio ?>" class="btn btn-primary" style="font-size:14px; border-radius: 10px; border-color: #2c699d;"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <i class="fa fa-check-circle" aria-hidden="true"></i>&nbsp;  <?php echo ObtenEtiqueta(917) ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a>
      
        <?php 
          }else{
      
        ?>

          <a  class="btn btn-primary" style="font-size:14px;border-radius: 10px; border-color: #2c699d;" data-dismiss="modal" > &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <?php echo ObtenEtiqueta(1534);?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a>


          <?php 
          }
          ?>

      </div>
    </div>
  </div>
</div>


<?php


?>

<script>
    
    document.getElementById("asignar").click();//clic automatico que se ejuta y sale modal

   // $('#formulario').load(document.URL + ' #formulario');//ACTUALIZA FORM
</script>
