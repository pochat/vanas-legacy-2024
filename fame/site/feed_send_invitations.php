<?php
require("../lib/self_general.php");

# Verifica que exista una sesion valida en el cookie y la resetea
$fl_usuario = ValidaSesion(False,0, True);
$fl_perfil_sp = ObtenPerfilUsuario($fl_usuario);
# Obtenemos el instituto
$fl_instituto = ObtenInstituto($fl_usuario);

# Verifica que el usuario tenga permiso de usar esta funcion
if(!ValidaPermisoSelf(FUNC_SELF)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
}

?>
<script>

function validarnn(e) { // 1
       tecla = (document.all) ? e.keyCode : e.which; // 2
       if (tecla == 8) return true; // 3
       if (tecla == 32) return false;
       if (tecla == 9) return true; // 3
       if (tecla == 11) return true; // 3
       patron = /[0-9 @._A-Za-zÃƒÂ±Ãƒâ€˜'ÃƒÂ¡ÃƒÂ©ÃƒÂ­ÃƒÂ³ÃƒÂºÃƒÂÃƒâ€°ÃƒÂÃƒâ€œÃƒÅ¡Ãƒ ÃƒÂ¨ÃƒÂ¬ÃƒÂ²ÃƒÂ¹Ãƒâ‚¬ÃƒË†ÃƒÅ’Ãƒâ€™Ãƒâ„¢ÃƒÂ¢ÃƒÂªÃƒÂ®ÃƒÂ´ÃƒÂ»Ãƒâ€šÃƒÅ ÃƒÅ½Ãƒâ€Ãƒâ€ºÃƒâ€˜ÃƒÂ±ÃƒÂ¤ÃƒÂ«ÃƒÂ¯ÃƒÂ¶ÃƒÂ¼Ãƒâ€žÃƒâ€¹ÃƒÂÃƒâ€“ÃƒÅ“\s\t-]/; // 4

       te = String.fromCharCode(tecla); // 5
       return patron.test(te); // 6
  }
 
</script>

<div class="row">
			<div class="col-md-1"></div>
			<div class="col-md-10">
			
				<div class="row">	
							
					<div class="col-md-4 text-center">
					<br>
							<i class="fa fa-user-plus  " style="font-size: 135px;color: #ccc;margin-top:15px;"></i>
					</div>
					<div class="col-md-8">
					
					        <h6 class="no-margin" style="font-weight: 100; font-size:medium;"><?php echo str_uso_normal(ObtenEtiqueta(2523));?></h6>
						    <br>
							<div class="smart-form">
								<?php FAMEInputText(ObtenEtiqueta(909),'first_name',$first_name,True,'onkeypress="return validarnn(event);"'); ?>
							</div> 
							<div class="smart-form">
								<?php FAMEInputText(ObtenEtiqueta(910),'last_name',$last_name,True,'onkeypress="return validarnn(event);"'); ?>
							</div> 
							<div class="smart-form">
								<?php FAMEInputText(ObtenEtiqueta(911),'email',$email,True,'onkeypress="return validarnn(event);"'); ?>
								
								<div id="err_email" class="note hidden" style="color:#A90329;"><?php echo ObtenEtiqueta(1533);?></div>
							</div> 
							
							<!--este email ya se envio para desbloquear curso YA EXISTE EN DB de ebvios de emails-->
							<div class="alert alert-danger fade in hidden" style="margin:2px;padding:5px;" id="anteriormente_ya_fue_enviado">
								<i class="fa-fw fa fa-times"></i> 
								<?php echo ObtenEtiqueta(2084); ?>.
							</div> 
							
					</div>
				</div>
			</div>
			<div class="col-md-1"></div>
</div>






