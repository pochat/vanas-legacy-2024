<?php
  
  # Libreria de funciones
  require('../../lib/general.inc.php');
  
  # Recibe parametros
  $clave = RecibeParametroNumerico('c', True);
  $no_contrato = RecibeParametroNumerico('con', True);
  
  # Recupera datos de la sesion
  $Query  = "SELECT cl_sesion ";
  $Query .= "FROM c_sesion ";
  $Query .= "WHERE fl_sesion=$clave";
  $row = RecuperaValor($Query);
  $cl_sesion = $row[0];
  
  #Recupera datos adicionales a la forma 1 y del contrato del aplicante
  $Query  = "SELECT ds_contrato, ds_header, ds_footer ";
  $Query .= "FROM k_app_contrato ";
  $Query .= "WHERE cl_sesion='$cl_sesion' ";
  $Query .= "AND no_contrato=$no_contrato ";
  $row = RecuperaValor($Query);
  $ds_cuerpo = str_uso_normal($row[0]);
  $ds_encabezado = str_uso_normal($row[1]);
  $ds_pie = str_uso_normal($row[2]);
  
  
  # Inicia cuerpo de la pagina
  echo "
<html xmlns='http://www.w3.org/1999/xhtml' xml:lang='es'>
<head>
<title>".ETQ_TITULO_PAGINA."</title>
<meta http-equiv='cache-control' content='max-age=0'>
<meta http-equiv='cache-control' content='no-cache'>
<meta http-equiv='expires' content='0'>
<meta http-equiv='pragma' content='no-cache'>
<link type='text/css' href='".PATH_CSS."/theme/jquery-ui-1.8rc3.custom.css' rel='stylesheet' />
<link type='text/css' href='".PATH_CSS."/estilos.css' media='screen' rel='stylesheet' />
<link type='text/css' href='".PATH_CSS."/fileuploader.css' rel='stylesheet' />
<link type='text/css' href='".PATH_CSS."/jquery.lovs.css' rel='stylesheet' />
<link type='text/css' href='".PATH_CSS."/separadores.css' media='screen' rel='stylesheet' />
<script type='text/javascript' src='".PATH_JS."/tiny_mce/tiny_mce.js'></script>
<script type='text/javascript' src='".PATH_JS."/fileuploader.js'></script>
<script type='text/javascript' src='".PATH_JS."/jquery.MultiFile.js'></script>
<script type='text/javascript' src='".PATH_JS."/jquery-1.4.2.min.js'></script>
<script type='text/javascript' src='".PATH_JS."/jquery-ui-1.8rc3.custom.min.js'></script>
<script type='text/javascript' src='".PATH_JS."/jquery.lovs.js.php'></script>
</head>
<body class='css_fondo'>
<center>
  <table border='".D_BORDES."' width='760' cellspacing='0' cellpadding='0'>
    <tr>
      <td align='left'>
        $ds_encabezado $ds_cuerpo $ds_pie
      </td>
    </tr>
    <tr>
      <td align='center' class='default'>
        <br>
        <input type='button' id='buttons' value='".ObtenEtiqueta(74)."' onClick='javascript:window.close();'>
      </td>
    </tr>
   </table>
</body>
</html>";
  
?>