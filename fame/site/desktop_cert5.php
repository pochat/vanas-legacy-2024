<?php 
	# Libreria de funciones
  require("../lib/self_general.php");
  
  # Obtenemos el usuario y el instituto
  $fl_usuario = ValidaSesion(False,0, True);
  $fl_instituto = ObtenInstituto($fl_usuario);
  $fl_programa = RecibeParametroNumerico("fl_programa");
  

  # Obtenemos el nombre del programa 
  $nb_programa = ObtenNombreCourse($fl_programa);
  # Obtenemos el nombre del usuario
  $Query1  = "SELECT ds_nombres, ds_apaterno, ds_amaterno, ds_email, kusd.ds_phone_number ";
  $Query1 .= "FROM c_usuario us ";
  $Query1 .= "LEFT JOIN k_usu_direccion_sp kusd ON(kusd.fl_usuario_sp=us.fl_usuario) ";
  $Query1 .= "WHERE fl_usuario=$fl_usuario ";
  $row1 = RecuperaValor($Query1);
  $ds_nombres = str_texto($row1[0]);
  $ds_apaterno = str_texto($row1[1]);
  $ds_amaterno = str_texto($row1[2]);
  $ds_email = str_texto($row1[3]);
  $ds_phone_number = str_texto($row1[4]);
  
  $info_user ="
  <div class='row'>
    <div class='col col-md-12 col-lg-6'>
    <small><h5 class='no-margin'>".ObtenEtiqueta(1192)."</h5></small>
    <h5 class='no-margin'><strong>".$nb_programa."</strong></h5>
    </div>
    <div class='col col-md-12 col-lg-3'>
    <small><h5 class='no-margin'>".ObtenEtiqueta(1193)."</h5></small>
    <h5 class='no-margin'><strong>".$ds_nombres."</strong></h5>
    </div>
    <div class='col col-md-12 col-lg-3'>
    <small><h5 class='no-margin'>".ObtenEtiqueta(1194)."</h5></small>
    <h5 class='no-margin'><strong>".$ds_apaterno."</strong></h5>
    </div>
  </div>
  <hr style='margin-top:10px;'/>";

  # Informamos al administrador que el usuario desea un certificado valido    
  # Insertamos el registro del pedido
  $Query  = "UPDATE k_usuario_doc SET fg_card='1' WHERE fl_usuario=$fl_usuario AND fl_programa=$fl_programa AND fg_tipo_doc='2'";
  EjecutaQuery($Query);
  
  # Variables para Stripe
  $mn_amount = ObtenConfiguracion(106);
  # Verificamos si paga tax
  $mn_tax = Tax_Can_User($fl_usuario);
?>
<!-- Paso 5 pago por Stripe -->
<div class="modal-dialog" role="document" id="modal_actions"  style='width: 55%; margin: 3% 10% 15% 25%;'>
  <div class="modal-content">
    <!-- Header del ceritificado -->
    <div class="modal-header">
      <!--<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>-->
      <h4 class="modal-title" id="gridModalLabel">
        <i class="fa fa-exclamation-triangle"></i> <strong> <?php echo ObtenEtiqueta(1153); ?></strong>
      </h4>
    </div>
    <div class="modal-body padding-10">      
      <?php
      // echo $info_user;
      # Vamos a recibir los datos y los vmos a ingresar a la BD
      $fe_birh = RecibeParametroFecha('fe_birh');
      $fe_birh = ValidaFecha($fe_birh);
      $fl_pais = RecibeParametroNumerico('fl_pais');
      $fl_state = RecibeParametroNumerico('fl_state');
      $ds_state = RecibeParametroHTML('ds_state');
      if($fl_pais==38)
        $ds_state = $fl_state;     
      $ds_city = RecibeParametroHTML('ds_city');
      $ds_number = RecibeParametroHTML('ds_number');
      $ds_street = RecibeParametroHTML('ds_street');
      $ds_zip = RecibeParametroHTML('ds_zip');
      $ds_phone_number = RecibeParametroHTML('ds_phone_number');
      
      # Buscamos si esta insertdo en la tabla solo actualiza si no lo inserta
      if(!ExisteEnTabla('k_usu_direccion_sp', 'fl_usuario_sp', $fl_usuario)){
        $Query  = "INSERT INTO k_usu_direccion_sp (fl_usuario_sp,fl_pais,ds_state,ds_city,ds_number,ds_street,ds_zip,ds_phone_number) ";
        $Query .= "VALUES ($fl_usuario, $fl_pais, '$ds_state', '$ds_city', '$ds_number', '$ds_street', '$ds_zip', '$ds_phone_number') ";        
      }
      else{
        $Query  = "UPDATE k_usu_direccion_sp SET fl_pais = $fl_pais,ds_state = '$ds_state',ds_city = '$ds_city',ds_number = '$ds_number' ";
        $Query .= ",ds_street = '$ds_street',ds_zip = '$ds_zip',ds_phone_number = '$ds_phone_number' WHERE fl_usuario_sp = $fl_usuario ";        
      }
      // ECHO $Query;
      EjecutaQuery($Query);     
      EjecutaQuery("UPDATE c_usuario SET fe_nacimiento='$fe_birh' WHERE fl_usuario=$fl_usuario");  
      ?>               
      <div class="row">
        <div class="col-lg-1 col-md-12"></div>
        <div class="col-lg-10 col-md-12">
        <?php
          # Generamos la descripcion del pago
          $ds_descripcion = $ds_nombres." ".$ds_apaterno." payment for the program certificate ".$nb_programa;          
          FormaStripe('frm_stripe', $mn_amount, $mn_tax, "site/charge.php", $fl_programa, $ds_descripcion);  
        ?>
        </div>
        <div class="col-lg-1 col-md-12"></div>
      </div>      
    </div>
    <div class="modal-footer text-align-center hidden"> 
      <div class="col-sm-3 col-lg-3"></div>    
      <div class="col-sm-6 col-lg-6">
      <a type="button" class="btn btn-primary btn-lg btn-block" data-dismiss="modal" id="btn_final">
        <i class="fa fa-check-circle"></i> close
      </a>
      </div>
      <div class="col-sm-3 col-lg-3"></div>
    </div>
  </div>
</div>
<script>
$("#btn_final").on('click', function(){
  $('#certificado').modal('toggle');
});
</script>
  