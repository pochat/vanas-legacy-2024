<?php

# Libreria de funciones
require_once("../modules/common/lib/cam_general.inc.php");

# Verifica que exista una sesion valida en el cookie y la resetea
// $fl_usuario = ValidaSesion(False);

# Verifica que el usuario tenga permiso de usar esta funcion
// if(!ValidaPermisoCampus(FUNC_ALUMNOS)) {
  // MuestraPaginaError(ERR_SIN_PERMISO);
  // exit;
// }
  	
//you can add your custom css in $page_css array.
//Note: all css files are inside css/ folder
//$page_css[] = "";
include("lib/inc/header.php");

//require UI configuration (nav, ribbon, etc.)
// require_once("lib/inc/config.ui.php");

//include left panel (navigation)
//follow the tree in inc/config.ui.php
include("lib/inc/nav.php");

?>
<!-- CONTENT STARTS HERE -->
<div id="main" role="main">
	<?php
		include("lib/inc/ribbon.php");
		// include("lib/inc/contacts.php");
	?>
	<div id="content"></div>
</div>
<!-- CONTENT ENDS HERE -->

<?php 
	//include required scripts
	include("lib/inc/scripts.php");
	//include footer
	include("lib/inc/footer.php"); 
?>