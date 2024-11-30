<?php
  
	# Libreria de funciones
	require("../lib/self_general.php");

  $fl_leccion_sp = RecibeParametroNumerico('fl_leccion_sp');
  $fl_usuario_sp = RecibeParametroNumerico('fl_usuario_sp');
    
  # Verificamos si ya podemos activar el boton    
  $row_l = RecuperaValor("SELECT fg_complete FROM k_leccion_usu WHERE fl_usuario_sp=$fl_usuario_sp AND fl_leccion_sp=$fl_leccion_sp");
  $fg_completa = $row_l[0];
  # Vamos activar el boton
  $activa_btn = boton_active($fl_usuario_sp, $fl_leccion_sp);

?>
<script>
  // $(document).ready(function(){    
    var leccion = '<?php echo $fl_leccion_sp; ?>';
    var ele_btn = $("#btn_session_"+leccion);
    var active_btn = '<?php echo $activa_btn; ?>';    
    var fg_completa = '<?php echo $fg_completa; ?>'; 
    if(active_btn==1){
      if(fg_completa==1){
        ele_btn.removeClass('btn-danger').addClass('btn-success').empty().append('<span class="btn-label"><i class="fa fa-check-square-o" aria-hidden="true"></i></span><?php echo ObtenEtiqueta(1901); ?>');
      }
      else{
        ele_btn.removeClass('btn-success disabled').addClass('btn-danger').empty().append('<span class="btn-label"><i class="fa fa-check-square-o" aria-hidden="true"></i></span><?php echo ObtenEtiqueta(1902); ?>');
      }
    }
    else{          
        ele_btn.addClass('btn-danger disabled').empty().append('<span class="btn-label"><i class="fa fa-check-square-o" aria-hidden="true"></i></span><?php echo ObtenEtiqueta(1902); ?>');        
    }    
  // });
</script>