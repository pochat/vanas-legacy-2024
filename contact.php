<?php
  
  # Libreria de funciones
  require("lib/sp_general.inc.php");
  require("lib/sp_forms.inc.php");
  
  # Recibe parametros
  $fg_error = RecibeParametroNumerico('fg_error');
	
  # Inicializa variables
  if(!$fg_error) { // Sin error, entra por primera vez
    $area_contacto = "0";
    $area_contacto_err = "";
    $nombre = "";
    $nombre_err = "";
    $telefono = "";
    $telefono_err = "";
    $email = "";
    $email_err = "";
    $comentarios = "";
    $comentarios_err = "";
  }
  else { // Con error, recibe parametros (viene de la pagina de actualizacion)
    if($fg_error <> 32000) {
      $area_contacto = RecibeParametroHTML('area_contacto');
      $area_contacto_err = RecibeParametroNumerico('area_contacto_err');
      $nombre = RecibeParametroHTML('nombre');
      $nombre_err = RecibeParametroNumerico('nombre_err');
      $telefono = RecibeParametroHTML('telefono');
      $telefono_err = RecibeParametroNumerico('telefono_err');
      $email = RecibeParametroHTML('email');
      $email_err = RecibeParametroNumerico('email_err');
      $comentarios = RecibeParametroHTML('comentarios');
      $comentarios_err = RecibeParametroNumerico('comentarios_err');
    }
  }
  
  # Recupera los datos del contenido
  $Query  = "SELECT ds_titulo, tr_titulo, ds_contenido, tr_contenido ";
  $Query .= "FROM c_pagina ";
  $Query .= "WHERE cl_pagina=".PAG_CONTACTO;
  $row = RecuperaValor($Query);
  $titulo = str_uso_normal(EscogeIdioma($row[0], $row[1]));
  $contenido = str_uso_normal(EscogeIdioma($row[2], $row[3]));
  
  # Toma la imagen default
  $img_archivo = IMG_DEFAULT;
  
  # Header
  PresentaHeader(SEC_CONTACTO, True);
  
  # Cuerpo de la pagina
  echo "
    <img src='".SP_IMAGES."/$img_archivo' width='720' height='150' border='0'>
    <table border='".D_BORDES."' width='720' valign='top' cellspacing='0' cellpadding='0'>
      <tr>
        <td width='20' height='20'>&nbsp;</td>
        <td>&nbsp;</td>
        <td width='20'>&nbsp;</td>
      </tr>
      <tr>
        <td height='30'>&nbsp;</td>
        <td><b>$titulo</b></td>
        <td>&nbsp;</td>
      </tr>";
  if(!empty($fg_error) AND $fg_error <> 32000) {
    echo "
      <tr><td colspan='3' height='20'>&nbsp;</td></tr>
      <tr>
        <td>&nbsp;</td>
        <td align='center' valign='top' class='css_msg_error'>".ObtenEtiqueta(25)."</td>
        <td>&nbsp;</td>
      </tr>";
  }
  if($fg_error == 32000) {
    echo "
      <tr><td colspan='3' height='20'>&nbsp;</td></tr>
      <tr>
        <td>&nbsp;</td>
        <td align='center' valign='top' class='css_msg_error'>".ObtenEtiqueta(73)."</td>
        <td>&nbsp;</td>
      </tr>";
  }
  echo "
      <tr>
        <td>&nbsp;</td>
        <td valign='top'><br>$contenido</td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td colspan='3' height='20'>&nbsp;</td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td align='center' valign='top'>
          <table border='".D_BORDES."' width='100%' valign='top' cellspacing='0' cellpadding='3'>
          <form name='datos' action='".PGM_CONTACTO."' method='post' enctype='multipart/form-data'>
            <tr>
              <td width='30%' align='right'>* ".ObtenEtiqueta(240).":</td>
              <td><select name='area_contacto' id='area_contacto' onChange='javascript:pide_anexo()' class='css_input'>
                <option value='0'>".ObtenEtiqueta(70)."</option>\n";
    if($area_contacto == 0)
      $display = "inline";
    else
      $display = "none";
    $opc_prompt[0] = "<div id='prompt_anexo_0' style='display:$display;'></div>\n";
    $opc_campo[0] = "<div id='campo_anexo_0' style='display:$display;'></div>\n";
    $rs = EjecutaQuery("SELECT fl_contacto, ds_area, tr_area, fg_anexo, ds_etq_anexo, tr_etq_anexo FROM c_contacto ORDER BY no_orden");
    for($i = 1; $row = RecuperaRegistro($rs); $i++) {
      $opcion = EscogeIdioma($row[1], $row[2]);
      $fg_anexo[$i] = $row[3];
      echo "<option value='$row[0]'";
      if($row[0] == $area_contacto) {
        echo " selected='selected'";
        $display = "inline";
      }
      else
        $display = "none";
      echo ">$opcion</option>\n";
      $opc_prompt[$i] = "<div id='prompt_anexo_$i' style='display:$display;'>";
      $opc_campo[$i] = "<div id='campo_anexo_$i' style='display:$display;'>";
      if($fg_anexo[$i] == 1) {
        $texto = EscogeIdioma($row[4], $row[5]);
        $opc_prompt[$i] .= "$texto:";
        $opc_campo[$i] .= "<input name='archivo_$row[0]' type='file' id='archivo_$row[0]' size='50' class='css_input'/>";
      }
      $opc_prompt[$i] .= "</div>\n";
      $opc_campo[$i] .= "</div>\n";
    }
    $tot_opc = $i;
    echo "
              </select>
              <span class='css_msg_error'>".ObtenMensaje($area_contacto_err)."</span></td>
            </tr>";
    Forma_CampoTexto(ObtenEtiqueta(18), True, 'nombre', $nombre, 80, 40, $nombre_err);
    Forma_CampoTexto(ObtenEtiqueta(280), True, 'telefono', $telefono, 30, 40, $telefono_err);
    Forma_CampoTexto(ObtenEtiqueta(121), True, 'email', $email, 50, 40, $email_err);
    echo "
            <tr>
              <td valign='top' align='right'>* ".ObtenEtiqueta(72).":</td>
              <td><textarea name='comentarios' cols='60' rows='5' id='comentarios' class='css_input'>$comentarios</textarea>
                <br />
                <span class='css_msg_error'>".ObtenMensaje($comentarios_err);
    
    # Completa los tipos validos de archivo para anexos
    if($comentarios_err == 207)
      echo " ".ObtenConfiguracion(19);
    
    echo "</span></td>
            </tr>
  <script language='javascript'>
    function pide_anexo() {
      var opc = $('#area_contacto').attr('selectedIndex');\n";
      
    for($i = 0; $i < $tot_opc; $i++) {
      echo "
      $('#prompt_anexo_$i').attr('style', 'display:none;');
      $('#campo_anexo_$i').attr('style', 'display:none;');\n";
    }
    echo "
      $('#prompt_anexo_'+opc).attr('style', 'display:inline;');
      $('#campo_anexo_'+opc).attr('style', 'display:inline;');
    }
  </script>
            <tr>
              <td align='right'>\n";
    for($i = 0; $i < $tot_opc; $i++)
      echo $opc_prompt[$i];
    echo "
              </td>
              <td>\n";
    for($i = 0; $i < $tot_opc; $i++)
      echo $opc_campo[$i];
    echo "
              </td>
            <tr>
              <td>&nbsp;</td>
              <td><button type='button' id='buttons' OnClick='javascript:document.datos.submit();'>".ObtenEtiqueta(330)."</button></td>
            </tr>
          </form>
          </table>
        </td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td colspan='3' height='20'>&nbsp;</td>
      </tr>
    </table>\n";
  
  # Footer
  PresentaFooter( );
  
?>