<?php
  
  # Libreria de funciones
  require("../lib/sp_general.inc.php");
  require("../lib/sp_session.inc.php");
  require("../lib/sp_forms.inc.php");
  require("app_form.inc.php");
  
  # Recupera sesion del cookie
  $clave = SP_RecuperaSesion( );
  
  # Si no es una sesion valida redirige a la forma inicial
  if(empty($clave)) {
    header("Location: ABSP4MDSFSDF8V_frm.php");
    exit;
  }
  
  # Reinicia la sesion
  SP_ActualizaSesion($clave);
  
  # Recupera los datos del contenido
  $Query  = "SELECT ds_titulo, tr_titulo, ds_contenido, tr_contenido ";
  $Query .= "FROM c_pagina ";
  $Query .= "WHERE cl_pagina=".PAG_CANCELA_PAGO;
  $row = RecuperaValor($Query);
  $titulo = str_uso_normal(EscogeIdioma($row[0], $row[1]));
  $contenido = str_uso_normal(EscogeIdioma($row[2], $row[3]));
  
  # Header
  PresentaHeaderAF( );
  
  # Cuerpo de la pagina
  echo "
    <table border='".D_BORDES."' width='100%' valign='top' cellspacing='0' cellpadding='0' class='app_form'>
      <tr>
        <td width='20' height='20'>&nbsp;</td>
        <td>&nbsp;</td>
        <td width='20'>&nbsp;</td>
      </tr>
      <tr>
        <td height='30'>&nbsp;</td>
        <td><b>$titulo</b></td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td valign='top'><br>$contenido</td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td colspan='3' height='20'>&nbsp;</td>
      </tr>
    </table>";
  
  # Footer
  PresentaFooterAF( );
  
?>