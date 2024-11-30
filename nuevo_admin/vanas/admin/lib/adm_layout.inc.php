<?php

#
# MRA: Funciones generales de despliegue
#

# Menu principal del Sistema de Administracion
function ArmaMenu( ) {
  
  # Recupera las descripciones de los modulos
  $Query  = "SELECT fl_modulo, nb_modulo, tr_modulo ";
  $Query .= "FROM c_modulo ";
  $Query .= "WHERE fl_modulo_padre=".MENU_ADMON." ";
  $Query .= "AND fg_admon='1' ";
  $Query .= "AND fg_menu='1' ";
  $Query .= "ORDER BY no_orden";
  $rs = EjecutaQuery($Query);
  for($i = 1; $row = RecuperaRegistro($rs); $i++) {
    $fl_modulo[$i] = $row[0];
    $nb_modulo[$i] = str_texto(EscogeIdioma($row[1], $row[2]));
    $Query  = "SELECT fl_funcion, nb_funcion, tr_funcion, nb_programa ";
    $Query .= "FROM c_funcion ";
    $Query .= "WHERE fl_modulo=$fl_modulo[$i] ";
    $Query .= "AND fg_menu=1 ";
    $Query .= "ORDER BY no_orden";
    $rs2 = EjecutaQuery($Query);
    for($j = 1; $row2 = RecuperaRegistro($rs2); $j++) {
      $fl_funcion[$i][$j] = $row2[0];
      $nb_funcion[$i][$j] = str_texto(EscogeIdioma($row2[1], $row2[2]));
      $nb_programa[$i][$j] = str_texto($row2[3]);
    }
    $tot_submodulos[$i] = $j-1;
  }
  $tot_modulos = $i-1;
  
  # Prepara menu
  $menu = "<table border='".D_BORDES."' width='100%' cellPadding='0' cellSpacing='0' class='css_barra_lateral'>
              <tr><td colspan='3'>&nbsp;</td></tr>
              <tr>
                <td width='20'></td>
                <td>";
  for($i = 1; $i <= $tot_modulos; $i++) {
    $menu .= "                  <b>".$nb_modulo[$i]."</b><br>\n";
    for($j = 1; $j <= $tot_submodulos[$i]; $j++) {
      $menu .= "                  <li><a href='".PATH_MODULOS.$nb_programa[$i][$j]."'>".$nb_funcion[$i][$j]."</a><br></li>\n";
    }
    $menu .= "                  <br>\n";
  }
  $menu .= "
                </td>
                <td width='10'></td>
              </tr>
              <tr><td colspan='3'>&nbsp;</td></tr>
            </table>";
  return $menu;
}

# Primera parte para todas las paginas hasta el inicio del cuerpo
function PresentaHeader( ) {
  
  # Inicializa variables
  $nombre = ObtenNombre( );
  $fecha_actual = date(EscogeIdioma("d-m-Y", "m-d-Y"));
  $Menu = ArmaMenu( );
  $clave = RecibeParametroNumerico('clave');
  echo "
<html xmlns='http://www.w3.org/1999/xhtml' xml:lang='es'>
<head>
<title>".ETQ_TITULO_PAGINA."</title>
<!--favicon-->
<link rel='shortcut icon' href='http://vanas.ca/templates/jm-me/favicon.ico'>
<meta http-equiv='cache-control' content='max-age=0'>
<meta http-equiv='cache-control' content='no-cache'>
<meta http-equiv='expires' content='0'>
<meta http-equiv='pragma' content='no-cache'>
<link type='text/css' href='".PATH_CSS."/theme/jquery-ui-1.8rc3.custom.css' rel='stylesheet' />
<link type='text/css' href='".PATH_CSS."/estilos.css' media='screen' rel='stylesheet' />
<link type='text/css' href='".PATH_CSS."/fileuploader.css' rel='stylesheet' />
<link type='text/css' href='".PATH_CSS."/jquery.lovs.css' rel='stylesheet' />
<link type='text/css' href='".PATH_CSS."/separadores.css' media='screen' rel='stylesheet' />
<link type='text/css' href='".PATH_CSS."/colorbox.css' media='screen' rel='stylesheet' />
<link type='text/css' href='".PATH_CSS."/procesos.css' rel='stylesheet' />
<script type='text/javascript' src='".PATH_JS."/tiny_mce/tiny_mce.js'></script>
<script type='text/javascript' src='".PATH_JS."/fileuploader.js'></script>
<script type='text/javascript' src='".PATH_JS."/jquery.MultiFile.js'></script>
<script type='text/javascript' src='".PATH_JS."/jquery-1.4.2.min.js'></script>
<script type='text/javascript' src='".PATH_JS."/jquery-ui-1.8rc3.custom.min.js'></script>
<script type='text/javascript' src='".PATH_JS."/jquery.lovs.js.php'></script>
<script type='text/javascript' src='".PATH_JS."/colorbox.js'></script>
<script type='text/javascript' src='".PATH_JS."/jquery.colorbox.js'></script>
<script type='text/javascript' src='".PATH_JS."/d3-3.5.5.min.js'></script>
<script type='text/javascript' src='".PATH_JS."/sendtemplate.js.php'></script>
</head>
<body class='css_fondo'>

<div id='vanas_preloader'></div>
<!--No hay registros seleccionados-->
<div id='no_select'>".ObtenEtiqueta(845)."
  <div><button id='btn_noselect'>".ObtenEtiqueta(46)."</button></div>
</div>
<!-- Confirmacion de Enroll Student -->
<div id='enroll_confirmation'>
  <div id='enroll1'>".ObtenEtiqueta(846)."</div>
  <div id='enroll2'>".ObtenEtiqueta(847)."</div>
  <div id='ok_confirmar'>
    <button id='si1'>".ObtenEtiqueta(16)."</button>
    <button id='no1'>".ObtenEtiqueta(17)."</button>
    <button id='si2'>".ObtenEtiqueta(16)."</button>
    <button id='no2'>".ObtenEtiqueta(17)."</button>
  </div>
</div>
<!-- Muestrala Barra de proceso -->
<div id='preloader'>
  <div id='loader'>
    <div id='myProgress'>
      <div id='myBar'></div>      
    </div>    
  </div>
  <div id='strong'>
    <h5 id='seleccionados'></h5>
    <div id='complet'></div>
    <div id='nocomplet'></div>
    <div id='condiciones'></div>
  </div>
  <div id='ok' style='text-align:center; display:none;' ><button onclick=\"location.reload();\">".ObtenEtiqueta(46)."</button></div>
</div>
<!-- Muestra Circulo de proceso -->
<div id='preloaderletter'  style='display:none;'>
  <div id='loaderletter'>&nbsp;</div>
</div>
<center>
<table border='".D_BORDES."' width='90%' cellPadding='0' cellSpacing='0' class='css_default'>
  <tr>
    <td>
      <table border='".D_BORDES."' width='100%' cellPadding='3' cellSpacing='0'>
        <tr>
          <td width='30%' align='left' valign='middle' class='css_encabezado'><a href='". ETQ_LINK_LOGO . "'><img src='" . PATH_IMAGES . "/" . IMG_ADMON . "' border='0' title='". ETQ_ALT_LOGO . "'></a></td>
          <td width='30%' align='center' class='css_encabezado_b'>".ETQ_TITULO_ADMON."</td>
          <td width='20%' align='center' class='css_encabezado_b'><a id='vanas_stable' title='".ObtenEtiqueta(875)."'>".ObtenEtiqueta(875)."</a></td>
          <td width='15%' align='right' valign='top' class='css_encabezado'>
            <br>
            <b>".ETQ_FECHA.": </b>$fecha_actual<br><br>
            <b>".ETQ_USUARIO.": </b>$nombre&nbsp;&nbsp;&nbsp;<a href='".PAGINA_SALIR."'>".ETQ_SALIR."</a>
          </td>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td>
      <table border='".D_BORDES."' width='100%' cellPadding='0' cellSpacing='0'>
        <tr valign='top'>
          <td width='19%' class='css_barra_lateral'>
            $Menu
          </td>
          <td width='1%'>&nbsp;</td>
          <td width='80%' class='css_default'>\n";
}

# Termina el cuerpo y cierra la pagina
function PresentaFooter( ) {
  
  echo "
          </td>
        </tr>
      </table>
    </td>
  </tr>
  <tr class='css_footer'>
    <td align='center'><br>".ETQ_FOOTER."<br><br></td>
  </tr>
</table>
<script>
$(document).ready(function(){
  
  // Agregamos la url y el nombre la funcion  
  var programa_act;  
  if($('#programa_act').val() == 'undefined'){
    programa_act = '".PATH_HOME_V2."/home.php'
  }else{
    var ruta = '".PATH_MODULOS_V2."';
    programa_act = ruta + $('#programa_act').val();
  }
  $('#vanas_stable').attr('href',programa_act);
  
  //$('#vanas_stable').html('<?php echo ObtenEtiqueta(874); ?>');
  
});
</script>
</center>
</body>
</html>";
}

# Presenta el encabezado de la pagina seleccionada en tipo Modulo > Funcion
function PresentaEncabezado($p_funcion) {
  
  # Recupera la descripcion de la funcion
  $Query  = "SELECT a.nb_funcion, a.tr_funcion, b.nb_modulo, b.tr_modulo, a.nb_programa ";
  $Query .= "FROM c_funcion a, c_modulo b ";
  $Query .= "WHERE a.fl_modulo=b.fl_modulo ";
  $Query .= "AND a.fl_funcion=$p_funcion ";
  $row = RecuperaValor($Query);
  $nb_funcion = str_texto(EscogeIdioma($row[0], $row[1]));
  $nb_modulo = str_texto(EscogeIdioma($row[2], $row[3])); 
  
  # Identifica si es una funcion de detalle
  $nb_programa = ObtenProgramaActual( );
  if(strpos($nb_programa, PGM_FORM))
    $forma = "> ".ETQ_DETALLE;
  
  # Prepara el titulo de la pagina dependiendo de la funcion
  echo "<input type='hidden' id='programa_act' value='$row[4]' />
  <table border='".D_BORDES."' width='100%' cellpadding='3' cellspacing='0'>
    <tr>
      <td class='css_nombre_enc'>
        $nb_modulo > $nb_funcion $forma
      </td></tr>
  </table>\n";  
}

# Funcion para mostrar pestanas de tipo folder con ligas
function PresentaFolders($p_nombres=array(), $p_ligas=array(), $p_actual) {
  
  $tot = count($p_nombres);
  $p_actual = $p_actual - 1;
  if(empty($p_actual) || $p_actual < 0 || $p_actual > ($tot-1))
    $p_actual = 0;
  for($i = 0; $i < $tot; $i++) {
    if($i != $p_actual)
      $cadena[$i] = "<li><a href='".$p_ligas[$i]."'><b>".$p_nombres[$i]."</b></a></li>\n";
    else
      $cadena[$i] = "<li class='current'><a href='".$p_ligas[$i]."'><b>".$p_nombres[$i]."</b></a></li>\n";
  }
  echo "
    <span class='preload17a'></span>
    <span class='preload17b'></span>
    <ul class='menu17'>\n";
  for($i = 0; $i < $tot; $i++) {
    echo $cadena[$i];
  }
  echo "    </ul><br>\n";
}

# Funcion para mostrar pestanas de tipo folder con ligas
function PresentaTabs($p_nombres=array()) {
  
  echo "
<script type='text/javascript'>
  $(function() {
    $('#tabs').tabs();
  });
</script>

<div id='tabs'>
  <ul>";
  $tot = count($p_nombres);
  for($i = 0; $i < $tot; $i++)
    echo"
    <li><a href='#tabs-".($i+1)."'>$p_nombres[$i]</a></li>";
  echo "
  </ul>\n";
}

# Funcion para iniciar un tab
function TabIni($p_cual) {
  
  echo "
  <div id='tabs-$p_cual'>\n";
}

# Funcion para cerrar un tab
function TabFin( ) {
  
  echo "
  </div>\n";
}

# Funcion para cerrar un tab
function CierraTabs( ) {
  
  echo "
</div>\n";
}


#
# MRA: Funciones para paginas de listados
#

# Funcion generica para mostrar listado
# Agregamos los icono1 por defaul coloca billete y icono2 por default pone pdf
function PresentaPaginaListado($p_funcion, $p_query, $p_admin=TB_LN_IUD, $p_buscar=False, $p_export=False, $p_campos=array(), $p_href_link='',
  $p_html_arriba='', $p_html_abajo='',$p_href_link2='', $icono1='', $icono2='', $p_seleccionar=False, $p_letter=False, $p_enroll=False) {
  
  # Verifica que exista una sesion valida en el cookie y la resetea 1 hra
  ValidaSesion( );
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermiso($p_funcion, PERMISO_EJECUCION)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Presenta home del sistema
  PresentaHeader( );
  PresentaEncabezado($p_funcion);
  
  echo $p_html_arriba;
  echo "<input type='hidden' id='fl_funcion' value='$p_funcion'></input>";
	# Pagina y presenta tabla
  PresentaListado($p_query, $p_admin, $p_buscar, $p_export, $p_campos, $p_href_link, $p_href_link2, $icono1,$icono2, $p_funcion, $p_seleccionar, $p_letter, $p_enroll);
  
	echo $p_html_abajo;
	
  # Pie de Pagina
  PresentaFooter( );
}

# Funcion para armar opciones de campos de busqueda
function ArmaCamposBusqueda(array $p_campos, $p_actual) {
  
  $tot = count($p_campos);
  $campos = "";
  for($i = 0; $i < $tot; $i++) {
    $campos .= "<option value=".($i+1);
    if($i+1 == $p_actual)
      $campos .= " selected";
    $campos .= ">".$p_campos[$i]."</option>";
  }
  return $campos;
}


#
# MRA: Funciones para formas de captura (programas *_frm)
#

function Forma_Inicia($p_clave, $p_multipart=False) {
  
  # Determina el programa para enviar la forma
  $nb_programa = ObtenProgramaNombre(PGM_INSUPD);
  
  # Inicia la forma
  echo "
<center>
<form name='datos' method='post' action='$nb_programa'";
  if($p_multipart)
    echo " enctype='multipart/form-data'";
  echo ">\n";
  Forma_CampoOculto('clave', $p_clave);
  Forma_AbreTabla('90%');
}

function Forma_AbreTabla($p_width='100%') {
  
  echo "
<table border='".D_BORDES."' width='$p_width' cellpadding='3' cellspacing='0' class='css_default'>
  <tr><td width='40%'></td><td width='60%'></td></tr>";
}

function Forma_CierraTabla( ) {
  
  echo "
</table>";
}

function Forma_Termina($p_guardar=False, $p_url_cancelar='', $p_etq_aceptar=ETQ_SALVAR, $p_etq_cancelar=ETQ_CANCELAR, $p_click_cancelar='') {
  
  # Cierra la forma de captura o edicion
  echo "
  <tr><td colspan=2>&nbsp;</td></tr>
  <tr>
    <td colspan=2 align=center>";
  
  # Destino para el boton Cancelar
  if(empty($p_click_cancelar)) {
    if(empty($p_url_cancelar)) {
      $nb_programa = ObtenProgramaBase( );
      $click_cancelar = "parent.location='$nb_programa'";
    }
    else
      $click_cancelar = "parent.location='$p_url_cancelar'";
  }
  else
    $click_cancelar = $p_click_cancelar;
  
  # Muestra el boton para guardar, por omision no se permite
  if($p_guardar)
    echo "
      <button type='button' name='aceptar' onClick='javascript:document.datos.submit();'>".$p_etq_aceptar."</button>&nbsp;&nbsp;&nbsp;";
  
  echo "
      <button type='button' name='cancelar' onClick=\"$click_cancelar\">".$p_etq_cancelar."</button>
    </td>
  </tr>";
  Forma_CierraTabla( );
  echo "
</form>
</center>\n";
}

function Forma_PresentaError( ) {
  
  echo "
  <tr class='css_msg_error'>
    <td>&nbsp;</td>
    <td align='left'>".ETQ_ERROR."</td>
  </tr>";
  Forma_Espacio( );
}

function Forma_Sencilla_Ini($p_prompt='', $p_requerido=False) {
  
  if(!empty($p_prompt)) {
    if($p_requerido)
      $p_prompt = "* ".$p_prompt;
    $p_prompt = $p_prompt.":";
  }
  else
    $p_prompt = '&nbsp;';
  echo "
  <tr>
    <td align='right' valign='top' class='css_prompt'>$p_prompt</td>
    <td align='left' valign='top' class='css_etq_texto'>";
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

function Forma_Columnas_Ini($p_titulo='', $p_caja=True, $p_width=array()) {
  
  # Inicia Columnas
  if(!empty($p_titulo))
    Forma_Seccion($p_titulo, $p_caja);
  $tot_columnas = count($p_width);
  Forma_Doble_Ini( );
  echo "
  <table border='".D_BORDES."' width='100%' cellpadding='0' cellspacing='0' class='css_default'>
    <tr>";
  for($i = 0; $i < $tot_columnas; $i++)
    echo "
      <td width='".$p_width[$i]."'></td>";
  echo "
    </tr>
    <tr>";
  Columnas_Mid( );
}

function Columnas_Mid( ) {
  
  echo "
      <td valign='top'>
        <table border='".D_BORDES."' width='100%' cellpadding='3' cellspacing='0' class='css_default'>
          <tr><td width='40%'></td><td width='60%'></td></tr>";
}

function Forma_Columnas_Mid( ) {
  
  # Cambia columna
  echo "
        </table>
      </td>";
  Columnas_Mid( );
}

function Forma_Columnas_Fin( ) {
  
  # Cierra tabla doble
  echo "
        </table>
      </td>
    </tr>
  </table>";
  Forma_Doble_Fin( );
}

function Forma_MuestraTabla($Query, $admin, $p_tabla, $href_link="", $p_ancho="100%") {
  
  echo "
  <tr>
    <td colspan='2' align='center'>";
  MuestraTabla($Query, $admin, $p_tabla, $href_link, $p_ancho);
  echo "
    </td>
  </tr>\n";
  
}

function Forma_Tabla_Ini($p_ancho, $p_tit=array(), $p_ancho_col=array(), $p_nombre='') {
  
  Forma_Doble_Ini( );
  echo "
  <table border='".D_BORDES."' width='$p_ancho' cellpadding='3' cellspacing='0' class='css_default'";
  if(!empty($p_nombre))
    echo " id='$p_nombre'";
  echo ">";
  $tot = count($p_tit);
  if($tot > 0) {
    echo "
    <tr class='css_tabla_encabezado'>";
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
}

function Forma_Tabla_Error($p_span='', $p_error) {
  
  if(!empty($p_error)) {
    if(!empty($p_span))
      $ds_span = "colspan='$p_span'";
    $ds_error = ObtenMensaje($p_error);
    echo "
    <tr class='css_msg_error'>
      <td $ds_span align='left'>$ds_error</td>
    </tr>";
  }
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

function ScriptDivAjax($p_nombre, $p_clave=0, $p_variable=0, $p_variable2=0, $p_func_ini='') {
  
  echo "
  <script>";
  if(empty($p_func_ini))
    echo "
    $.ajax({
      type: 'POST',
      url : '".$p_nombre.".php',
      data: 'clave=$p_clave'+
            '&accion=new'+
            '&variable=$p_variable'+
            '&variable2=$p_variable2',
      async: false,
      success: function(html) {
        $('#".$p_nombre."').html(html);
      }
    });\n";
  else
    echo "
    $p_func_ini";
  echo "
  </script>";
}

function CampoDivAjax($p_nombre, $p_clave=0, $p_variable=0, $p_variable2=0, $p_func_ini='') {
  
  echo "<div id='$p_nombre'></div>";
  ScriptDivAjax($p_nombre, $p_clave, $p_variable, $p_variable2, $p_func_ini);
}

function Forma_Doble_CampoDivAjax($p_nombre, $p_clave=0, $p_variable=0, $p_variable2=0, $p_func_ini='') {
  
  Forma_Doble_Ini( );
  CampoDivAjax($p_nombre, $p_clave, $p_variable, $p_variable2, $p_func_ini);
  Forma_Doble_Fin( );
}

function Forma_CampoDivAjax($p_prompt, $p_requerido=False, $p_nombre, $p_clave=0, $p_variable=0, $p_variable2=0, $p_func_ini='') {
  
  Forma_Sencilla_Ini($p_prompt, $p_requerido);
  echo "<div id='$p_nombre'></div>";
  Forma_Sencilla_Fin( );
  ScriptDivAjax($p_nombre, $p_clave, $p_variable, $p_variable2, $p_func_ini);
}

function Forma_Espacio($p_height=0) {
  
  if(!empty($p_height))
    $ds_height = " height='$p_height'";
  echo "
  <tr><td colspan='2'$ds_height>&nbsp;</td></tr>";
}

function Forma_Seccion($p_titulo='', $p_caja=True) {
  
  Forma_Espacio( );
  if($p_caja)
    $class = 'css_caja';
  else
    $class = 'css_prompt';
  echo "
  <tr class='$class'>
    <td colspan='2' align='center'>$p_titulo</td>
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

function Forma_CampoOculto($p_nombre, $p_valor='') {
  
  echo "
    <input type='hidden' id='$p_nombre' name='$p_nombre' value=\"$p_valor\">\n";
}

function Forma_CampoInfo($p_prompt, $p_texto) {
  
  echo "
  <tr>
    <td align='right' valign='top' class='css_prompt'>";
    if(!empty($p_prompt))
      echo "$p_prompt:";
    else
      echo "&nbsp;";
    echo "</td>
    <td align='left' valign='top' class='css_etq_texto'>$p_texto</td>
  </tr>\n";
}

function Forma_Error($p_error='') {
  
  if(!empty($p_error))
    echo "<tr><td>&nbsp;</td><td align='left' class='css_msg_error'>".ObtenMensaje($p_error)."</td></tr>\n";
}

function CampoTexto($p_nombre, $p_valor, $p_maxlength, $p_size, $p_clase='css_input', $p_password=False, $p_script='') {
  
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

function Forma_CampoTexto($p_prompt, $p_requerido, $p_nombre, $p_valor, $p_maxlength, $p_size, $p_error='', $p_password=False, $p_id='', $fg_visible=True, $p_script='', $p_texto='') {
  
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
    if(!empty($p_prompt))
      echo "$p_prompt:";
    else
      echo "&nbsp;";
    if(!empty($p_id)) echo "</div>";
    echo "</td>
      <td align='left' valign='middle'>";
    if(!empty($p_id)) echo "<div id='$p_id' style='display:$ds_visible;'>";
    CampoTexto($p_nombre, $p_valor, $p_maxlength, $p_size, $ds_clase, $p_password, $p_script);
    if(!empty($p_texto)) echo "<span class='css_default'>$p_texto</span>";
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

function Forma_CampoTextArea($p_prompt, $p_requerido, $p_nombre, $p_valor, $p_cols, $p_rows, $p_error='', $p_editar=True, $p_puntos=True) {
  
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

function Forma_CampoTinyMCE($p_prompt, $p_requerido, $p_nombre, $p_valor, $p_cols, $p_rows, $p_error='', $p_script_host='') {
  
  if(strpos($p_nombre, 'tr_') === False OR FG_TRADUCCION) {
    $ds_clase = "MCE_".$p_nombre;
    echo "
  <script type='text/javascript'>
  tinyMCE.init({
    mode : 'textareas',
    theme : 'advanced',
    editor_selector : '$ds_clase',
    plugins : 'safari,pagebreak,style,layer,table,save,advhr,advimage,advlink,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,fullscreen,nonbreaking,xhtmlxtras,advlist',
    theme_advanced_buttons1 : 'bold,italic,underline,strikethrough,|,sub,sup,|,forecolor,backcolor,|,styleprops,|,formatselect,fontselect,fontsizeselect',
    theme_advanced_buttons2 : 'justifyleft,justifycenter,justifyright,justifyfull,|,outdent,indent,blockquote,|,bullist,numlist,|,cite,abbr,acronym,del,ins,attribs,|,pastetext,pasteword,|,charmap,iespell,|,insertdate,inserttime',
    theme_advanced_buttons3 : 'tablecontrols,|,visualaid,|,search,replace,|,undo,redo,|,cleanup,code,|,print,|,preview,|,fullscreen',
    theme_advanced_buttons4 : 'link,unlink,anchor,image,|,hr,advhr,|,insertlayer,moveforward,movebackward,absolute,|,nonbreaking,pagebreak',
    theme_advanced_toolbar_location : 'top',
    theme_advanced_toolbar_align : 'left',
    theme_advanced_statusbar_location : 'bottom',
    theme_advanced_resizing : true,
    file_browser_callback : 'fileBrowserCallBack',
    relative_urls : false";
    if($p_script_host){
      echo "
    ,remove_script_host: false";
    }
    echo " 
  });
  
  function fileBrowserCallBack(field_name, url, type, win) {
		var connector = '../../filemanager/browser.html?Connector=connectors/php/connector.php';
		var enableAutoTypeSelection = true;

		var cType;
		tinyfck_field = field_name;
		tinyfck = win;

		switch (type) {
			case 'image':
				cType = 'Image';
				break;
			case 'flash':
				cType = 'Flash';
				break;
			case 'file':
				cType = 'File';
				break;
		}

		if (enableAutoTypeSelection && cType) {
			connector += '&Type=' + cType;
		}

		window.open(connector, 'tinyfck', 'modal,width=600,height=400');
	}
  
  </script>
    <tr>
      <td align='right' valign='top' class='css_prompt'>";
    if($p_requerido) echo "* ";
    echo "$p_prompt:</td>
      <td valign='top'>";
    CampoTextArea($p_nombre, $p_valor, $p_cols, $p_rows, $ds_clase);
    echo "<br></td>
    </tr>\n";
  }
  else
    Forma_CampoOculto($p_nombre, $p_valor);
  if(!empty($p_error))
    Forma_Error($p_error);
}

function CampoLOV($p_folio, $p_val_folio, $p_nombre, $p_valor, $p_size, $p_lov, $p_titulo, $p_tipo_lov, $p_tam_lov, $p_condicion='', $p_clase, $p_var_div='') {
  
  if(!empty($p_condicion))
    $condicion = "$p_condicion.value";
  else
    $condicion = "''";
  Forma_CampoOculto($p_folio, $p_val_folio);
  echo "
      <input type='text' class='$p_clase' id='$p_nombre' name='$p_nombre' value=\"$p_valor\" readonly='readonly' size='$p_size'>
      <a href=\"javascript:jLov('$p_lov',$p_tipo_lov,'$p_titulo',$p_tam_lov,'$p_folio','$p_nombre','',$condicion,'$p_var_div');\">
      <img id='lv_$p_nombre' src='".PATH_IMAGES."/".IMG_EXAMINAR."' title='".ETQ_SELECCIONAR."' width='14' height='14' border='0'></a>";
}

function Forma_CampoLOV($p_prompt, $p_requerido, $p_folio, $p_val_folio, $p_nombre, $p_valor, $p_size, $p_lov, $p_tipo_lov, $p_tam_lov, $p_condicion='', $p_error='', $p_limpiar=False, $p_var_div='') {
  
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
  CampoLOV($p_folio, $p_val_folio, $p_nombre, $p_valor, $p_size, $p_lov, $titulo, $p_tipo_lov, $p_tam_lov, $p_condicion, $ds_clase, $p_var_div);
  if($p_limpiar)
    echo "
    <script type='text/javascript'>
      function LimpiaLOV(folio, campo) {
        $('#'+folio).val('');
        $('#'+campo).val('');
      }
    </script>
    <a href=\"javascript:LimpiaLOV('$p_folio','$p_nombre');\">
    <img src='".PATH_IMAGES."/".IMG_LIMPIAR."' title='".ETQ_LIMPIAR."' width='14' height='14' border='0'></a>";
  if(!empty($p_error))
    echo "<br>$ds_error";
  echo "</td>
  </tr>\n";
}

function CampoRadio($p_nombre, $p_valor, $p_actual, $p_texto='', $p_editar=True, $p_script='') {
  
  echo "<input type='radio' id='$p_nombre' name='$p_nombre' value='$p_valor'";
  if($p_valor == $p_actual) echo " checked";
  if($p_editar == False) echo " disabled=disabled";
  if(!empty($p_script)) echo " $p_script";
  echo "> $p_texto";
}

function Forma_CampoRadio($p_prompt, $p_nombre, $p_valor, $p_actual, $p_texto='', $p_editar=True, $p_script='') {
  
  echo "
  <tr>
    <td align='right' valign='top' class='css_prompt'>";
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
  echo "
  <tr>
    <td>&nbsp;</td>
    <td align='left' valign='top'>";
  CampoRadio($p_nombre, '1', $p_actual, ETQ_SI, $p_editar, $p_script);
  echo "&nbsp;&nbsp;";
  CampoRadio($p_nombre, '0', $p_actual, ETQ_NO, $p_editar, $p_script);
  echo "</td>
  </tr>";
  Forma_Error($p_error);
}

function CampoCheckbox($p_nombre, $p_valor, $p_texto='', $p_regresa='', $p_editar=True, $p_script='') {
  
  echo "<input type='checkbox' id='$p_nombre' name='$p_nombre'";
  if(!empty($p_regresa)) echo " value='$p_regresa'";
  if($p_valor == 1) echo " checked";
  if($p_editar == False) echo " disabled=disabled";
  if(!empty($p_script)) echo " $p_script";
  echo "> $p_texto";
}

function Forma_CampoCheckbox($p_prompt, $p_nombre, $p_valor, $p_texto='', $p_regresa='', $p_editar=True, $p_script='') {
  
  echo "
  <tr>
    <td align='right' valign='middle' class='css_prompt'>$p_prompt:</td>
    <td align='left' valign='middle'>";
  CampoCheckbox($p_nombre, $p_valor, $p_texto, $p_regresa, $p_editar, $p_script);
  echo "</td>
  </tr>\n";
}

function CampoSelect($p_nombre, $p_opc, $p_val, $p_actual, $p_clase='css_input', $p_seleccionar=False, $p_script='') {
  
  $tot = count($p_opc);
  echo "<select id='$p_nombre' name='$p_nombre' class='$p_clase'";
  if(!empty($p_script)) echo " $p_script";
  echo ">\n";
  if($p_seleccionar)
    echo "<option value=0>".ObtenEtiqueta(70)."</option>\n";
  for($i = 0; $i < $tot; $i++) {
    echo "<option value=\"$p_val[$i]\"";
    if($p_actual == $p_val[$i])
      echo " selected";
    echo ">$p_opc[$i]</option>\n";
  }
  echo "</select>";
}

function Forma_CampoSelect($p_prompt, $p_requerido, $p_nombre, $p_opc, $p_val, $p_actual, $p_error='', $p_seleccionar=False, $p_script='') {
  
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
  if(!empty($p_prompt))
    echo "$p_prompt:";
  else
    echo "&nbsp;";
  echo "</td>
    <td align='left' valign='middle' class='css_default'>";
  CampoSelect($p_nombre, $p_opc, $p_val, $p_actual, $ds_clase, $p_seleccionar, $p_script);
  echo "</td>
  </tr>\n";
  if(!empty($p_error))
    echo "<tr><td>&nbsp;</td><td align='left' class='css_msg_error'>$ds_error</td></tr>\n";
}

function CampoSelectBD($p_nombre, $p_query, $p_actual, $p_clase='css_input', $p_seleccionar=False, $p_script='') {
  
  echo "<select id='$p_nombre' name='$p_nombre' class='$p_clase'";
  if(!empty($p_script)) echo " $p_script";
  echo ">\n";
  if($p_seleccionar)
    echo "<option value=0>".ObtenEtiqueta(70)."</option>\n";
  $rs = EjecutaQuery($p_query);
  while($row = RecuperaRegistro($rs)) {
    echo "<option value=\"$row[1]\"";
    if($p_actual == $row[1])
      echo " selected";
    
    # Determina si se debe elegir un valor por traduccion
    $etq_campo = DecodificaEscogeIdiomaBD($row[0]);
    echo ">$etq_campo</option>\n";
  }
  echo "</select>";
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
    <td align='left' valign='middle' class='css_default'>\n";
  CampoSelectBD($p_nombre, $p_query, $p_actual, $ds_clase, $p_seleccionar, $p_script);
  echo "
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
        buttonImage: '".PATH_IMAGES."/".IMG_CALENDARIO."',
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
    Forma_CampoOculto($p_nombre);
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
    Forma_CampoOculto($p_nombre);
}

function Forma_CampoUpload($p_prompt, $p_desc, $p_nombre, $p_valor, $p_ruta, $p_requerido, $p_archivo, $p_size, $p_error='', $p_accept='', $p_maxlength='1') {
  
  if(!empty($p_desc))
    $ds_desc = " ($p_desc)";
  if(!empty($p_valor)) {
    Forma_CampoPreview($p_prompt, $p_nombre, $p_valor, $p_ruta, False, !$p_requerido);
    Forma_CampoArchivo(ObtenEtiqueta(216).$ds_desc, $p_requerido, $p_archivo, $p_size, $p_error, $p_accept, $p_maxlength);
  }
  else
    Forma_CampoArchivo($p_prompt.$ds_desc, $p_requerido, $p_archivo, $p_size, $p_error, $p_accept, $p_maxlength);
}

function Forma_CampoPreview($p_prompt, $p_nombre, $p_valor, $p_ruta, $p_video=False, $p_limpiar=True) {
  
  Forma_Sencilla_Ini($p_prompt);
  echo "<span id='nom_$p_nombre'><a href=";
  if(!$p_video)
    echo "\"javascript:Preview('$p_ruta/$p_valor');\"";
  else
    echo "'preview_flv.php?archivo=$p_valor' target='_blank'";
  echo ">$p_valor</a>";
  if($p_limpiar)
    echo "&nbsp;&nbsp;&nbsp;
        <a href=\"javascript:LimpiaCampo('$p_nombre');\"><img src='".PATH_IMAGES."/".IMG_LIMPIAR."' width='15' height='15' border='0' title='".ETQ_LIMPIAR."'></a>";
  echo "</span>";
  Forma_Sencilla_Fin( );
  Forma_CampoOculto($p_nombre, $p_valor);
}

function DialogoAjax($p_nombre, $p_titulo, $p_width=350, $p_height='auto') {
  
  if($p_height == 'auto')
    $ds_height = "'auto'";
  else
    $ds_height = $p_height;
  echo "
    <script type='text/javascript'>
      $(function() {
        $('#dlg_$p_nombre').dialog({
          autoOpen: false,
          resizable: false,
          width: $p_width,
          height: $ds_height,
          modal: true,
          buttons: {
            '".ETQ_ACEPTAR."': function() {
              if(r_$p_nombre(1))
                $(this).dialog('close');
            },
            '".ETQ_CANCELAR."': function() {
              r_$p_nombre(0);
              $(this).dialog('close');
            }
          }
        });
      });
      
      function $p_nombre( ) {
        
        $('#dlg_$p_nombre').dialog('open');
      }
    </script>
    <div id='dlg_$p_nombre' title='$p_titulo'>\n";
  include "dlg_".$p_nombre.".php";
  echo "\n</div>";
}

function FileUploader($p_nombre, $p_extensions='', $p_size_limit='1024 * 1024 * 1024', $p_autosubmit=False) {
  
  Forma_CampoOculto($p_nombre);
  echo "
    <div id='fu_$p_nombre'></div>
    <script>
      function createUploader(){
        var uploader = new qq.FileUploader({
          element: document.getElementById('fu_$p_nombre'),
          action: '".PATH_LIB."/fileuploader.php',
          allowedExtensions: [$p_extensions],
          sizeLimit: $p_size_limit,
          onComplete:
          function(id, fileName, responseJSON) {
            $('#$p_nombre').val(fileName);
            $('.qq-upload-button').empty();";
  if($p_autosubmit)
    echo "
            document.datos.submit();";
  echo "
          },
          debug: false
        });
      }
      window.onload = createUploader;
    </script>";
}

function Forma_FileUploader($p_prompt, $p_requerido, $p_nombre, $p_extensions='', $p_size_limit='1024 * 1024 * 1024', $p_error='', $p_autosubmit=False) {
  
  if(strpos($p_nombre, 'tr_') === False OR FG_TRADUCCION) {
    echo "
    <tr>
      <td align='right' valign='top' class='css_prompt'>";
    if($p_requerido) echo "* ";
    if(!empty($p_prompt))
      echo "$p_prompt:";
    else
      echo "&nbsp;";
    echo "</td>
      <td align='left' valign='top' class='default'>";
    FileUploader($p_nombre, $p_extensions, $p_size_limit, $p_autosubmit);
    if(!empty($p_error)) {
      $ds_error = ObtenMensaje($p_error);
      echo "<div class='css_msg_error'>$ds_error</div>";
    }
    echo "</td>
    </tr>";
  }
  else
    Forma_CampoOculto($p_nombre);
}

#
# MRA: Acordeon
#

# Funcion para mostrar secciones plegables en acordeon
function Forma_Plegable_Ini($p_titulo='', $p_id='', $p_activo=False, $p_minheight='') {
  
  if(!empty($p_minheight))
    $ds_minheight = "style='min-height: ".$p_minheight."px;'";
  Forma_Doble_Ini( );
  echo "
  <script type='text/javascript'>
    $(function() {
      $('#accordion$p_id').accordion({
        header: 'h3',
        collapsible: true,
        clearStyle: true,";
  if(!$p_activo)
    echo "
        active: false";
  echo "
      });
    });
  </script>
  <div id='accordion$p_id'>
    <h3><a href='#' style='color:black;'>$p_titulo</a></h3>
    <div $ds_minheight>";
  Forma_AbreTabla( );
}

# Funcion para cerrar un tab
function Forma_Plegable_Fin( ) {
  
  Forma_CierraTabla( );
  echo "
  </div>";
  Forma_Doble_Fin( );
}

?>