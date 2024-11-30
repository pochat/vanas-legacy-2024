<?php

#
# MRA: Funciones para formas de captura
#

function Forma_Inicia($p_clave, $p_multipart=False, $p_programa='', $p_width_prompt='33%') {
  
  # Determina el programa para enviar la forma
  if(empty($p_programa))
    $nb_programa = ObtenProgramaNombre(PGM_INSUPD);
  else
    $nb_programa = $p_programa;
  
  # Inicia la forma
  echo "
<center>
<form name='datos' method='post' action='$nb_programa'";
  if($p_multipart)
    echo " enctype='multipart/form-data'";
  echo ">\n";
  Forma_CampoOculto('clave', $p_clave);
  echo "
<table border='".D_BORDES."' width='100%' cellpadding='3' cellspacing='0' class='css_default'>
  <tr><td width='$p_width_prompt'></td><td></td></tr>\n";
}

function Forma_Termina( ) {
  
  # Cierra la forma de captura o edicion
  echo "
  <tr><td colspan=2>&nbsp;</td></tr>
</table>
</form>
</center>\n";
}

function Forma_PresentaError( ) {
  
  echo "
  <tr class='css_msg_error'>
    <td>&nbsp;</td>
    <td align='left'>".ObtenEtiqueta(25)."</td>
  </tr>\n";
}

function Forma_Sencilla_Ini($p_prompt='') {
  
  if(!empty($p_prompt))
    $p_prompt = $p_prompt . ":";
  else
    $p_prompt = '&nbsp;';
  echo "
  <tr>
    <td align='right' valign='top' class='css_prompt'>$p_prompt</td>
    <td align='left' valign='top' class='css_etq_texto'>\n";
}

function Forma_Sencilla_Fin( ) {
  
  echo "
    </td>
  </tr>\n";
}

function Forma_Doble_Ini($p_align='center') {
  
  echo "
  <tr>
    <td colspan='2' align='$p_align' valign='top' class='css_default'>";
}

function Forma_Doble_Fin( ) {
  
  Forma_Sencilla_Fin( );
}

function Forma_Tabla_Ini($p_ancho, $p_tit=array(), $p_ancho_col=array(), $p_nombre='') {
  
  Forma_Doble_Ini( );
  echo "
  <table border='".D_BORDES."' width='$p_ancho' cellpadding='3' cellspacing='0' class='css_default'";
  if(!empty($p_nombre))
    echo " id='$p_nombre'";
  echo ">
  <tr class='css_tabla_encabezado'>";
  $tot = count($p_tit);
  for($i = 0; $i < $tot; $i++) {
    $enc = $p_tit[$i];
    $align = "left";
    $enc = str_replace('|left', '', $enc);
    if(strpos($enc, '|center')) {
      $enc = str_replace('|center', '', $enc);
      $align = "center";
    }
    if(strpos($enc, '|right')) {
      $enc = str_replace('|right', '', $enc);
      $align = "right";
    }
    echo "
    <td width='$p_ancho_col[$i]' align='$align'>$enc</td>";
  }
  echo "
  </tr>\n";
}

function Forma_Tabla_Error($p_span, $p_error) {
  
  $ds_error = ObtenMensaje($p_error);
  echo "
  <tr class='css_msg_error'>
    <td colspan='$p_span' align='left'>$ds_error</td>
  </tr>\n";
}

function Forma_Tabla_Fin( ) {
  
  echo "
  </table>\n";
  Forma_Doble_Fin( );
}

function Forma_Tab_Ini( ) {
  
  echo "
<table border='".D_BORDES."' width='100%' cellpadding='3' cellspacing='0' class='css_default'>
  <tr><td width='25%'></td><td width='75%'></td></tr>\n";
}

function Forma_Tab_Fin( ) {
  
  echo "
</table>\n";
}

function Forma_Espacio( ) {
  
  echo "
  <tr><td colspan='2'>&nbsp;</td></tr>\n";
}

function Forma_Seccion($p_titulo, $p_caja=True) {
  
  Forma_Espacio( );
  if($p_caja)
    $class = 'css_caja';
  else
    $class = 'css_prompt';
  echo "
  <tr class='$class'>
    <td>&nbsp;</td>
    <td align='left' style='padding-bottom: 13px; color:#0092cd;'><h4  style='margin:0px;'>$p_titulo</h4> <hr style='width:40%; position:absolute; left:380px; border: 1px solid #9D9B9B;' /></td>
  </tr>\n";
}

function Forma_Prompt($p_prompt, $p_requerido=False) {
  
  echo "
  <tr>
    <td align='right' valign='top' class='css_prompt'>";
  if($p_requerido) echo "* ";
  echo "$p_prompt:</td>
    <td>&nbsp;</td>
  </tr>\n";
}

function Forma_PromptDoble($p_titulo, $p_requerido=False) {
  
  echo "
  <tr class='$css_prompt'>
    <td colspan='2' align='left'>";
  if($p_requerido) echo "* ";
  echo "$p_titulo</td>
  </tr>\n";
}

function Forma_CampoOculto($p_nombre, $p_valor) {
  
  echo "
    <input type='hidden' id='$p_nombre' name='$p_nombre' value=\"$p_valor\">\n";
}

function Forma_CampoInfo($p_prompt, $p_texto) {
  
  echo "
  <tr>
    <td align='right' valign='middle' class='css_prompt'>";
    if(!empty($p_prompt))
      echo "$p_prompt:";
    else
      echo "&nbsp;";
    echo "</td>
    <td align='left' valign='middle' class='css_etq_texto'>$p_texto</td>
  </tr>\n";
}

function Forma_Error($p_error='') {
  
  if(!empty($p_error))
    echo "<tr><td>&nbsp;</td><td align='left' class='css_msg_error'>".ObtenMensaje($p_error)."</td></tr>\n";
}

function CampoTexto($p_nombre, $p_valor, $p_maxlength, $p_size, $p_clase, $p_password=False,$p_script='') {
  
  if(strpos($p_nombre, 'tr_') === False OR FG_TRADUCCION) {
    if(!$p_password)
      $ds_tipo = 'text';
    else
      $ds_tipo = 'password';
    echo "<input type='$ds_tipo' class='$p_clase' id='$p_nombre' name='$p_nombre' value=\"$p_valor\" maxlength='$p_maxlength' size='$p_size'";
    if($p_password)
      echo " autocomplete='off'";
	  if(!empty($p_script)) echo " $p_script";
    echo ">";
  }
  else
    Forma_CampoOculto($p_nombre, $p_valor);
}

function Forma_CampoTexto($p_prompt, $p_requerido, $p_nombre, $p_valor, $p_maxlength, $p_size, $p_error='', $p_password=False, $p_id='', $fg_visible=True,$p_script='') {
  
  if(strpos($p_nombre, 'tr_') === False OR FG_TRADUCCION) {
    if(!empty($p_error)) {
      $ds_error = ObtenMensaje($p_error);
      $ds_clase = 'css_input_error';
    }
    else {
      $ds_clase = 'css_input';
      $ds_error = "";
    }
    if(!empty($p_id)) {
      if($fg_visible)
        $ds_visible = "inline";
      else
        $ds_visible = "none";
    }
    echo "
    <tr>
      <td align='right' valign='middle' class='css_prompt'>";
    if(!empty($p_id)) echo "<div id='".$p_id."_ppt' style='display:$ds_visible;'>";
    if($p_requerido) echo "* ";
    echo "$p_prompt:";
    if(!empty($p_id)) echo "</div>";
    echo "</td>
      <td align='left' valign='middle'>";
    if(!empty($p_id)) echo "<div id='$p_id' style='display:$ds_visible;'>";
    CampoTexto($p_nombre, $p_valor, $p_maxlength, $p_size, $ds_clase, $p_password,$p_script);
    if(!empty($p_id)) echo "</div>";
    echo "</td>
    </tr>\n";
    if(!empty($p_error)) {
      echo "<tr><td>&nbsp;</td><td align='left' class='css_msg_error'>";
      if(!empty($p_id)) echo "<div id='".$p_id."_err' style='display:$ds_visible;'>";
      echo $ds_error;
      if(!empty($p_id)) echo "</div>";
      echo "</td></tr>";
    }
  }
  else
    Forma_CampoOculto($p_nombre, $p_valor);
}

function CampoTextArea($p_nombre, $p_valor, $p_cols, $p_rows, $p_clase='css_input', $p_editar=True) {
  
  if(strpos($p_nombre, 'tr_') === False OR FG_TRADUCCION) {
    echo "<textarea class='$p_clase' id='$p_nombre' name='$p_nombre' cols=$p_cols rows=$p_rows";
    if($p_editar == False)
      echo " readonly='readonly'";
    echo ">$p_valor</textarea>";
  }
  else
    Forma_CampoOculto($p_nombre, $p_valor);
}

function Forma_CampoTextArea($p_prompt, $p_requerido, $p_nombre, $p_valor, $p_cols, $p_rows, $p_error = '', $p_editar=True, $p_puntos=True) {
  
  if(strpos($p_nombre, 'tr_') === False OR FG_TRADUCCION) {
    if(!empty($p_error)) {
      $ds_error = ObtenMensaje($p_error);
      $ds_clase = 'css_input_error';
    }
    else {
      $ds_clase = 'css_input';
      $ds_error = "";
    }
    if($p_puntos)
      $align = 'right';
    else
      $align = 'left';
    echo "
    <tr>
      <td align='$align' valign='top' class='css_prompt'>";
    if($p_requerido) echo "* ";
    echo $p_prompt;
    if($p_puntos) echo ":";
    echo "</td>
      <td align='left' valign='top' class='css_msg_error'>";
    CampoTextArea($p_nombre, $p_valor, $p_cols, $p_rows, $ds_clase, $p_editar);
    if(!empty($p_error))
      echo "\n<br>$ds_error";
    echo "</td>
    </tr>\n";
  }
  else
    Forma_CampoOculto($p_nombre, $p_valor);
}

function Forma_CampoTinyMCE($p_prompt, $p_requerido, $p_nombre, $p_valor, $p_cols, $p_rows) {
  
  if(strpos($p_nombre, 'tr_') === False OR FG_TRADUCCION) {
    $ds_clase = "MCE_".$p_nombre;
    echo "
  <script type='text/javascript'>
  tinyMCE.init({
    mode : 'textareas',
  theme : 'advanced',
  editor_selector : '$ds_clase',
  plugins : 'safari,pagebreak,style,layer,table,save,advhr,advimage,advlink,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,fullscreen,nonbreaking,xhtmlxtras,advlist',
  theme_advanced_buttons1 : 'bold,italic,underline,strikethrough,|,sub,sup,|,forecolor,backcolor,|,styleprops,|,styleselect,formatselect,fontselect,fontsizeselect',
  theme_advanced_buttons2 : 'justifyleft,justifycenter,justifyright,justifyfull,|,outdent,indent,blockquote,|,bullist,numlist,|,cite,abbr,acronym,del,ins,attribs,|,pastetext,pasteword,|,charmap,iespell,|,insertdate,inserttime',
  theme_advanced_buttons3 : 'tablecontrols,|,visualaid,|,search,replace,|,undo,redo,|,cleanup,code,|,print,|,preview,|,fullscreen',
  theme_advanced_buttons4 : 'link,unlink,anchor,image,media,|,hr,advhr,|,insertlayer,moveforward,movebackward,absolute,|,nonbreaking,pagebreak',
  theme_advanced_toolbar_location : 'top',
  theme_advanced_toolbar_align : 'left',
  theme_advanced_statusbar_location : 'bottom',
  theme_advanced_resizing : true,
  content_css : '/vanas/css/vanas.css',
  });
  </script>
    <tr>
      <td align='right' valign='top' class='css_prompt'>";
    if($p_requerido) echo "* ";
    echo "$p_prompt:</td>
      <td align='left' valign='top'>";
    CampoTextArea($p_nombre, $p_valor, $p_cols, $p_rows, $ds_clase);
    echo "<br></td>
    </tr>\n";
  }
  else
    Forma_CampoOculto($p_nombre, $p_valor);
}

function CampoLOV($p_folio, $p_val_folio, $p_nombre, $p_valor, $p_size, $p_lov, $p_titulo, $p_tipo_lov, $p_tam_lov, $p_condicion='', $p_clase) {
  
  if(!empty($p_condicion))
    $condicion = "$p_condicion.value";
  else
    $condicion = "''";
  Forma_CampoOculto($p_folio, $p_val_folio);
  echo "
      <input type='text' class='$p_clase' id='$p_nombre' name='$p_nombre' value=\"$p_valor\" readonly='readonly' size='$p_size'>
      <img id='lv_$p_nombre' src='".PATH_IMAGES."/".IMG_EXAMINAR."' title='".ETQ_SELECCIONAR."' width='22' height='22'
      onClick=\"jLov('$p_lov',$p_tipo_lov,'$p_titulo',$p_tam_lov,'$p_folio','$p_nombre','',$condicion);\" />";
}

function Forma_CampoLOV($p_prompt, $p_requerido, $p_folio, $p_val_folio, $p_nombre, $p_valor, $p_size, $p_lov, $p_tipo_lov, $p_tam_lov, $p_condicion='', $p_error='') {
  
  if(!empty($p_error)) {
    $ds_error = ObtenMensaje($p_error);
    $ds_clase = 'css_input_error';
  }
  else {
    $ds_clase = 'css_input';
    $ds_error = "";
  }
  $titulo = ETQ_SELECCIONAR." $p_prompt";
  echo "
  <tr>
    <td align='right' valign='middle' class='css_prompt'>";
  if($p_requerido) echo "* ";
  echo "$p_prompt:</td>
    <td align='left' valign='middle' class='css_msg_error'>\n";
  CampoLOV($p_folio, $p_val_folio, $p_nombre, $p_valor, $p_size, $p_lov, $titulo, $p_tipo_lov, $p_tam_lov, $p_condicion, $ds_clase);
  if(!empty($p_error))
    echo "\n<br>$ds_error";
  echo "</td>
  </tr>\n";
}

function CampoRadio($p_nombre, $p_valor, $p_actual, $p_texto = '', $p_editar=True, $p_script='') {
  
  echo "<input type='radio' id='$p_nombre' name='$p_nombre' value='$p_valor'";
  if($p_valor == $p_actual) echo " checked";
  if($p_editar == False) echo " disabled=disabled";
  if(!empty($p_script)) echo " $p_script";
  echo "> $p_texto";
}

function Forma_CampoRadio($p_prompt, $p_nombre, $p_valor, $p_actual, $p_texto='', $p_editar=True, $p_script='') {
  
  echo "
  <tr>
    <td align='right' valign='middle' class='css_prompt'>";
  if($p_prompt)
    echo "$p_prompt:";
  else
    echo "&nbsp;";
  echo "</td>
    <td align='left' valign='middle'>";
  CampoRadio($p_nombre, $p_valor, $p_actual, $p_texto, $p_editar, $p_script);
  echo "</td>
  </tr>\n";
}

function Forma_CampoRadioYN($p_prompt, $p_requerido, $p_nombre, $p_actual, $p_error='', $p_editar=True, $p_script='') {
  
  Forma_PromptDoble($p_prompt, $p_requerido);
  Forma_Error($p_error);
  $etq_si = ObtenEtiqueta(16);
  $etq_no = ObtenEtiqueta(17);
  echo "
  <tr>
    <td align='right' valign='middle' class='css_prompt'>&nbsp;</td>
    <td align='left' valign='middle'>";
  CampoRadio($p_nombre, '1', $p_actual, $etq_si, $p_editar, $p_script);
  echo "&nbsp;&nbsp;";
  CampoRadio($p_nombre, '0', $p_actual, $etq_no, $p_editar, $p_script);
  echo "</td>
  </tr>\n";
}

function CampoCheckbox($p_nombre, $p_valor, $p_texto='', $p_regresa='', $p_editar=True) {
  
  echo "<input type='checkbox' id='$p_nombre' name='$p_nombre'";
  if(!empty($p_regresa)) echo " value='$p_regresa'";
  if($p_valor == 1) echo " checked";
  if($p_editar == False) echo " disabled=disabled";
  echo "> $p_texto";
}

function Forma_CampoCheckbox($p_prompt, $p_nombre, $p_valor, $p_texto='', $p_regresa='', $p_editar=True) {
  
  echo "
  <tr>
    <td align='right' valign='middle' class='css_prompt'>$p_prompt:</td>
    <td align='left' valign='middle'>";
  CampoCheckbox($p_nombre, $p_valor, $p_texto, $p_regresa, $p_editar);
  echo "</td>
  </tr>\n";
}

function Forma_CampoSelect($p_prompt, $p_requerido, $p_nombre, $p_opc, $p_val, $p_actual, $p_error='', $p_seleccionar=False) {
  
  if(!empty($p_error)) {
    $ds_error = ObtenMensaje($p_error);
    $ds_clase = 'css_input_error';
  }
  else {
    $ds_clase = 'css_input';
    $ds_error = "";
  }
  $tot = count($p_opc);
  echo "
  <tr>
    <td align='right' valign='middle' class='css_prompt'>";
  if($p_requerido) echo "* ";
  echo "$p_prompt:</td>
    <td align='left' valign='middle' class='css_default'>
      <select id='$p_nombre' name='$p_nombre' class='$ds_clase'>\n";
  if($p_seleccionar)
    echo "<option value=0>".ObtenEtiqueta(70)."</option>\n";
  for($i = 0; $i < $tot; $i++) {
    echo "<option value=\"$p_val[$i]\"";
    if($p_actual == $p_val[$i])
      echo " selected";
    echo ">$p_opc[$i]</option>\n";
  }
  echo "
      </select>
    </td>
  </tr>\n";
  if(!empty($p_error))
    echo "<tr><td>&nbsp;</td><td align='left' class='css_msg_error'>$ds_error</td></tr>\n";
}

function Forma_CampoSelectBD($p_prompt, $p_requerido, $p_nombre, $p_query, $p_actual, $p_error='', $p_seleccionar=False, $p_script='') {
  
  if(!empty($p_error)) {
    $ds_error = ObtenMensaje($p_error);
    $ds_clase = 'css_input_error';
  }
  else {
    $ds_clase = 'css_input';
    $ds_error = "";
  }
  echo "
  <tr>
    <td align='right' valign='middle' class='css_prompt'>";
  if($p_requerido) echo "* ";
  echo "$p_prompt:</td>
    <td align='left' valign='middle' class='css_default'>
      <select id='$p_nombre' name='$p_nombre' class='$ds_clase'";
  if(!empty($p_script)) echo " $p_script";
  echo ">\n";
  if($p_seleccionar)
    echo "<option value=0>".ObtenEtiqueta(70)."</option>\n";
  $rs = EjecutaQuery($p_query);
  while($row = RecuperaRegistro($rs)) {
    echo "<option value=\"$row[1]\"";
    if($p_actual == $row[1])
      echo " selected";
    echo ">$row[0]</option>\n";
  }
  echo "
      </select>
    </td>
  </tr>\n";
  if(!empty($p_error))
    echo "<tr><td>&nbsp;</td><td align='left' class='css_msg_error'>$ds_error</td></tr>\n";
}

function Forma_Calendario($p_nombre) {
  
  echo "
    <script type='text/javascript'>
    $(function(){
      $('#$p_nombre').datepicker({
        showOn: 'button',
        buttonImage: '".PATH_ADM_IMAGES."/".ObtenNombreImagen(12)."',
        buttonImageOnly: true,
        changeMonth: true,
        changeYear: true,
        dateFormat: '".EscogeIdioma('dd-mm-yy','mm-dd-yy')."',
        showAnim: 'slideDown',
        showOtherMonths: true,
        selectOtherMonths: true,
        showMonthAfterYear: false,
        yearRange: 'c-50:c+2',
        autoSize: true,
        dayNames: [".ETQ_DIAS_SEMANA."],
        dayNamesMin: [".ETQ_DIAS_CORTO."],
        monthNames: [".ETQ_MESES."],
        monthNamesShort: [".ETQ_MESES_CORTO."],
        nextText: '".ETQ_SIGUIENTE."',
        prevText: '".ETQ_ANTERIOR."'
      });
		});
		</script>\n";
}

// p_accept Recibe extensiones admitidas de archivo separados por |
// p_maxlength Total de archivos permitidos 0=Ilimitado
function CampoArchivo($p_nombre, $p_size, $p_clase, $p_accept='', $p_maxlength='1') {
  
  if(strpos($p_nombre, 'tr_') === False OR FG_TRADUCCION) {
    if(!empty($p_accept))
      $ds_accept = "accept='$p_accept'";
    if(!empty($p_maxlength))
      $ds_maxlength = "maxlength='$p_maxlength'";
    $ds_nombre = $p_nombre;
    $ds_clase = $p_clase;
    if(!empty($p_accept) OR $p_maxlength <> '1') {
      $ds_nombre .= "[]";
      $ds_clase = 'multi';
    }
    echo "<input type='file' class='$ds_clase' id='$p_nombre' name='$ds_nombre' size='$p_size' $ds_accept $ds_maxlength>";
  }
  else
    Forma_CampoOculto($p_nombre, "");
}

function Forma_CampoArchivo($p_prompt, $p_requerido, $p_nombre, $p_size, $p_error='', $p_accept='', $p_maxlength='1') {
  
  if(strpos($p_nombre, 'tr_') === False OR FG_TRADUCCION) {
    if(!empty($p_error)) {
      $ds_error = ObtenMensaje($p_error);
      $ds_clase = 'css_input_error';
    }
    else {
      $ds_clase = 'css_input';
      $ds_error = "";
    }
    echo "
    <tr>
      <td align='right' valign='middle' class='css_prompt'>";
    if($p_requerido) echo "* ";
    echo "$p_prompt:</td>
      <td align='left' valign='middle' class='css_msg_error'>";
    CampoArchivo($p_nombre, $p_size, $ds_clase, $p_accept, $p_maxlength);
    if(!empty($p_error))
      echo "\n<br>$ds_error";
    echo "</td>
    </tr>\n";
  }
  else
    Forma_CampoOculto($p_nombre, "");
}

function Forma_CampoUpload($p_prompt, $p_desc, $p_nombre, $p_valor, $p_ruta, $p_requerido, $p_archivo, $p_size, $p_error='', $p_accept='', $p_maxlength='1') {
  
  if(!empty($p_desc))
    $ds_desc = " ($p_desc)";
  if(!empty($p_valor)) {
    Forma_CampoPreview($p_prompt, $p_nombre, $p_valor, $p_ruta);
    Forma_CampoArchivo(ObtenEtiqueta(216).$ds_desc, $p_requerido, $p_archivo, $p_size, $p_error, $p_accept, $p_maxlength);
  }
  else
    Forma_CampoArchivo($p_prompt.$ds_desc, $p_requerido, $p_archivo, $p_size, $p_error, $p_accept, $p_maxlength);
}

function Forma_CampoPreview($p_prompt, $p_nombre, $p_valor, $p_ruta) {
  
  Forma_Sencilla_Ini($p_prompt);
  echo "<span id='nom_$p_nombre'><a href=\"$p_ruta/$p_valor\" target='_blank'>$p_valor</a>&nbsp;&nbsp;
        <a href=\"javascript:LimpiaCampo('$p_nombre');\"><img src='".PATH_COM_IMAGES."/delete.png' width=12 height=12 border=0 title='".ObtenEtiqueta(31)."'></a></span>
<script type='text/javascript'>
function LimpiaCampo(campo) {
  
  $('#'+campo).val('');
  $('#nom_'+campo).html('".ObtenEtiqueta(215)."');  
}
</script>";
  Forma_Sencilla_Fin( );
  Forma_CampoOculto($p_nombre, $p_valor);
}

?>