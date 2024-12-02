<?php

#
# MRA: Funciones para formas de aplicacion
#


# Inicio de pagina
function PresentaHeaderAF( ) {
  
  echo "<html>
<head>
<meta http-equiv='cache-control' content='max-age=0' />
<meta http-equiv='cache-control' content='no-cache' />
<meta http-equiv='expires' content='0'>
<meta http-equiv='pragma' content='no-cache'>
<link type='text/css' href='".PATH_ADM_CSS."/theme/jquery-ui-1.8rc3.custom.css' rel='stylesheet'>
<script type='text/javascript' src='".PATH_ADM_JS."/jquery-1.4.2.min.js'></script>
<script type='text/javascript' src='".PATH_ADM_JS."/jquery-ui-1.8rc3.custom.min.js'></script>
<link href='".PATH_CSS."/vanas.css' rel='stylesheet' type='text/css' />
</head>
<body leftmargin='0' topmargin='0' marginwidth='0' marginheight='0'>";
}


# Termina el cuerpo y cierra la pagina
function PresentaFooterAF( ) {
  
  echo "
</body>
</html>";
}

?>