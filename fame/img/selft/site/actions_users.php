<?php 
	# Libreria de funciones
	// require("../../modules/common/lib/cam_general.inc.php");
	// require("../lib/layout_self.php");
	// require("../lib/self_func.php");
  require("../lib/self_general.php");
  // $fl_insituto = ObtenInstituto($fl_usuario);
  $fl_action = RecibeParametroNumerico('fl_action');
  $ds_titulo = RecibeParametroHTML('ds_titulo');
  
  
?>

<div class="modal-dialog">
  
  <div class="modal-content">
  <?php
  if($fl_action == 1 || $fl_action == 2 || $fl_action == 3 || $fl_action == 4){
  ?>
    <!-- Header -->
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
        ×
      </button>
      <h5 class="modal-title" id="myModalLabel"><i class="fa fa-exclamation-triangle"></i> <strong><?php echo $ds_titulo; ?></strong></h5>
    </div>
    
    <!-- Body -->
    <div class="modal-body">
      <div class="row smart-form">
      <?php
      if($fl_action == 1  || $fl_action == 3){
        CampoTexto('ds_email', $ds_email, 'form-control', False, '', "Email Address", "fa-envelope-o", "col-md-12");
        CampoTexto('ds_fname', $ds_fname, 'form-control', False, '', "Firs Name", "fa-user", "col-md-12");
        CampoTexto('ds_lname', $ds_lname, 'form-control', False, '', "Last Name", "fa-user", "col-md-12");        
      }
      else{
        CampoArchivo('fl_archivo', 20, 'form-control', '', '1');
      }
      CampoOculto('fl_perfil', $fl_perfil);
      ?>
      </div>
    </div>
    
    <!-- Footer -->
    <div class="modal-footer">
        <div class="row">
          <div class="col-md-12">
            <button type="button" class="btn btn-default" data-dismiss="modal">
              <i class="fa fa-times-circle"></i> Cancel
            </button>
            <button type="button" class="btn btn-primary">
              <i class="fa fa-check-circle"></i> Send Invite
            </button>
          </div>
        </div>
    </div>
  <?php
  }
  else{
  ?>
    <div class="modal-body modal-footer">
      <div class="row">
        <div class="col-md-8 text-align-center"><h4><strong><?php echo $ds_titulo; ?> users selected </strong></h4>
        </div>
        <div class="col-md-4">
          <button type="button" class="btn btn-default" data-dismiss="modal">
            <i class="fa fa-times-circle"></i> Cancel
          </button>
          <button type="button" class="btn btn-primary">
            <i class="fa fa-check-circle"></i> Aceptar
          </button>
        </div>
      </div>
    </div>
  <?php
  }
  ?>
  </div><!-- /.modal-content -->
  
</div>
<!-- Eliminamos el width que tiene en el campus -->
<script>
  $('.modal-dialog').css('width', '50%');
  $('.modal-dialog').css('margin', '10% 15% 15% 25%');  
</script>
