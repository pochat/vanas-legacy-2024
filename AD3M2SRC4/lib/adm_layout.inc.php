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
  
  # Tes de iconos
  $iconos = array(1 =>"fa-bar-chart","fa-graduation-cap","fa-institution", "fa-table", "fa-facebook","fa-laptop", " fa-cubes", "fa-folder","fa-gears", "fa-unlock-alt ", "fa-archive");
  # Prepara menu
  $menu = '
  <!-- #NAVIGATION -->
  <!-- Left panel : Navigation area -->
  <!-- Note: This width of the aside area can be adjusted through LESS variables -->
  <aside id="left-panel">

    <!-- User info -->
    <div class="login-info">
      <span> <!-- User image size is adjusted inside CSS, it should stay as it -->
      <!-- le quitamos id="show-shortcut" data-action="toggleShortcut" -->
        <a href="'.PAGINA_INICIO.'">
          <img src="'.PATH_IMAGES.'/'.ObtenNombreImagen(19).'" alt="me" class="online" /> 
          <span class="text-align-center">
            '.ObtenNombre( ).'<!--&nbsp(
            '.date(EscogeIdioma("d-m-Y", "m-d-Y")).')-->
              <br/>
          </span>
        </a> 

      </span>
    </div>
    <!-- end user info -->

    <nav>
      <!-- 
      NOTE: Notice the gaps after each icon usage <i></i>..
      Please note that these links work a bit different than
      traditional href="" links. See documentation for details.
      -->

      <ul id="menu">';
      for($i = 1; $i <= $tot_modulos; $i++) {
       $menu .= '
          <li id="mod_'.$fl_modulo[$i].'">
            <a href="#" title="'.$nb_modulo[$i].'"><i id="icono_'.$fl_modulo[$i].'" class="fa '.$iconos[$i].'"></i> <span class="menu-item-parent">'.$nb_modulo[$i].'</span></a>
            <ul>';
        for($j = 1; $j <= $tot_submodulos[$i]; $j++) {
          $menu .= '
              <li id="fun_'.$fl_funcion[$i][$j].'">
                <!--<a href="'.PATH_MODULOS.$nb_programa[$i][$j].'" title="'.$nb_funcion[$i][$j].'" onclick="Nav_active('.$fl_modulo[$i].','.$fl_funcion[$i][$j].');"><span class="menu-item-parent">'.$nb_funcion[$i][$j].'</span></a>-->
                <a href="'.PATH_MODULOS.$nb_programa[$i][$j].'" title="'.$nb_funcion[$i][$j].'"><span class="menu-item-parent">'.$nb_funcion[$i][$j].'</span></a>
              </li>';
        }
        $menu .= ' 
            </ul>	
          </li>' ;
      }					
    $menu .= '
      </ul>
    </nav>
    <span class="minifyme" data-action="minifyMenu"> 
      <i class="fa fa-arrow-circle-left hit"></i> 
    </span>
  </aside>
  <!-- END NAVIGATION -->';
  
  return $menu;
}

# Primera parte para todas las paginas hasta el inicio del cuerpo
function PresentaHeader( ) {
  
  # Inicializa variables
  $Menu = ArmaMenu( );
  $clave = RecibeParametroNumerico('clave');
  
  $page_title = ETQ_TITULO_PAGINA;
  
  # Incluimos el header
  include(SP_HOME."/AD3M2SRC4/bootstrap/inc/header.php");
  echo $Menu;
  echo "
        <div id='vanas_preloader'></div>
        <!--No hay registros seleccionados-->
        <div id='no_select' class='modal-content'>".ObtenEtiqueta(845)."
          <div><button class='btn btn-primary' id='btn_noselect'>".ObtenEtiqueta(46)."</button></div>
        </div>
        <!-- Confirmacion de Enroll Student -->
        <div id='enroll_confirmation' class='modal-content'>
          <div id='enroll1'>".ObtenEtiqueta(846)."</div>
          <div id='enroll2'>".ObtenEtiqueta(847)."</div>
          <div id='ok_confirmar'>
            <button class='btn btn-primary' id='si1'>".ObtenEtiqueta(16)."</button>
            <button class='btn btn-default' id='no1'>".ObtenEtiqueta(17)."</button>
            <button class='btn btn-primary' id='si2'>".ObtenEtiqueta(16)."</button>
            <button class='btn btn-default' id='no2'>".ObtenEtiqueta(17)."</button>
          </div>
        </div>
        <!-- Muestrala Barra de proceso -->
        <div id='preloader' class='modal-content'>
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
          <div id='ok' style='text-align:center; display:none;' ><button class='btn btn-primary' onclick=\"location.reload();\">".ObtenEtiqueta(46)."</button></div>
        </div>
        <!-- Muestra Circulo de proceso -->
        <div id='preloaderletter'  style='display:none;'>
          <div id='loaderletter'>&nbsp;</div>
        </div>
        <!-- MAIN PANEL -->
        <div id='main' role='main'> ";
}

# Termina el cuerpo y cierra la pagina
function PresentaFooter( ) {  
  # Incluimos el header
  echo "        
                </article>
                <!-- WIDGET END -->
              </div>
              <!-- End Row -->
            </section>
            <!-- end widget grid -->
          </div>
          <!-- END MAIN CONTENT -->
        </div>
        <!-- END MAIN PANEL -->
      ";
  include(SP_HOME."/AD3M2SRC4/bootstrap/inc/scripts.php");
  include(SP_HOME."/AD3M2SRC4/bootstrap/inc/footer.php"); 
}

# Presenta el encabezado de la pagina seleccionada en tipo Modulo > Funcion
function PresentaEncabezado($p_funcion=0) {
  
  # Recupera la descripcion de la funcion
  $Query  = "SELECT a.nb_funcion, a.tr_funcion, b.nb_modulo, b.tr_modulo ";
  $Query .= "FROM c_funcion a, c_modulo b ";
  $Query .= "WHERE a.fl_modulo=b.fl_modulo ";
  $Query .= "AND a.fl_funcion=$p_funcion ";
  $row = RecuperaValor($Query);
  $nb_funcion = str_texto(EscogeIdioma($row[0], $row[1]));
  $nb_modulo = str_texto(EscogeIdioma($row[2], $row[3]));
  
  # Identifica si es una funcion de detalle
  $nb_programa = ObtenProgramaActual( );
  $forma='';
  if(strpos($nb_programa, PGM_FORM))
    $forma = ETQ_DETALLE;
  
  
  $breadcrumb = "<li><span>Home</span></li>";
  $page_header = "Select an option from submenu";
  # Obtemenos el modudlo actual  
  $rowm = RecuperaValor("SELECT fl_modulo, nb_programa FROM c_funcion WHERE fl_funcion='".$p_funcion."'");
  if(!empty($p_funcion)){
    $breadcrumb .= "<li><span>{$nb_modulo}</span></li><li><span>{$nb_funcion}</span></li><li><span>{$forma}</span></li>";
    $page_header = "
    <!-- PAGE HEADER -->
    <i class='fa-fw fa fa-home'></i> 
      ".$nb_funcion."
    <span><i class='fa fa-chevron-right'></i>
      ".$forma."
    </span>";
    $modulo = $rowm[0];
    $funcion  = $rowm[1];
  }
  else{
    $modulo = 0;
    $funcion  = 0;
  }

  echo "
  <input type='hidden' id='act_mod' value='".$modulo."' />
  <input type='hidden' id='act_fun' value='".$p_funcion."' />
  <input type='hidden' id='programa_act' value='".$funcion."' />
  <!--RIBBON-->
  <div id='ribbon' style='backcolor:#0092cd; color:#0092cd;'>
    <!--<span class='ribbon-button-alignment'>
      <span id='refresh' class='btn btn-ribbon' data-action='resetWidgets' data-title='refresh' rel='tooltip' data-placement='bottom' data-original-title='<i class=\"text-warning fa fa-warning\"></i>
      Warning! this will reset all your widget settings.' data-html='true'>
      <i class='fa fa-refresh'></i>
      </span>
    </span>-->

    <!-- breadcrumb -->
    <ol class='breadcrumb'>
      ".$breadcrumb."
    </ol>
    <!-- end breadcrumb -->
  </div>
  <!--END RIBBON-->

  <!-- MAIN CONTENT -->
  <div id='content' style='padding:20px 5px 0px 3px;'>
    <!-- row -->
    <div class='row'>
      <!-- col -->
      <div class='col-xs-12 col-sm-7 col-md-7 col-lg-4'>
        <h1 class='page-title txt-color-blueDark'>
          <i id='icono_modulo'></i>
            ".$nb_modulo."
          <span>";
          if(!empty($nb_funcion))
            echo "> ";
  echo      
            $nb_funcion."
          </span>
        </h1>
      </div>
      <!-- end col -->
    </div>
    <!-- end row -->
    <!-- widget grid -->
    <!--<section id='widget-grid' class=''>-->
    <section class=''>
      <!-- Row -->
      <div class='row' style='margin-left:0px; margin-right:0px; '>
        <!-- NEW WIDGET START -->
        <article class='col-xs-12 col-sm-12 col-md-12 col-lg-12' style='padding:3px;'>";
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
  $p_html_arriba='', $p_html_abajo='',$p_href_link2='', $icono1='', $icono2='', $p_seleccionar=False, $p_letter=False, $p_enroll=False, $fg_filters=False) {
  
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
  echo "<input type='hidden' id='fl_funcion' value='$p_funcion'>";
	# Pagina y presenta tabla
  PresentaListado($p_query, $p_admin, $p_buscar, $p_export, $p_campos, $p_href_link, $p_href_link2, $icono1,$icono2, $p_funcion, $p_seleccionar, $p_letter, $p_enroll, $fg_filters);
  
	echo $p_html_abajo;
	
  # Pie de Pagina
  PresentaFooter($p_funcion);
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

function Forma_Inicia($p_clave, $p_multipart=False, $p_id=false) {
  
  # Determina el programa para enviar la forma
  $nb_programa = ObtenProgramaNombre(PGM_INSUPD);  
  # Inicia la forma
  echo "
  <!-- Widget ID (each widget will need unique ID)-->
  <div class='jarviswidget jarviswidget-color-darken' id='wid-id-0' data-widget-editbutton='false' data-widget-deletebutton='false'>
      <!-- widget content -->
      <div class='widget-body' style='padding:0px;'>
        <form name='datos' method='post' ";
		if($p_id==true)
			echo "id='datos'";
	echo "
		action='$nb_programa' class='form-horizontal'";
        if($p_multipart)
          echo " enctype='multipart/form-data'";
        echo ">";
        Forma_CampoOculto('clave', $p_clave);   
  //Forma_AbreTabla('90%');
    
}

function Forma_AbreTabla($p_width='100%') {
  
  /*echo "
<table border='".D_BORDES."' width='$p_width' cellpadding='3' cellspacing='0' class='css_default'>
  <tr><td width='40%'></td><td width='60%'></td></tr>";*/
  echo "
  <fieldset>";
}

function Forma_CierraTabla( ) {
  
  /*echo "
</table>";*/
  echo "
  </fieldset>";
}

function Forma_Termina($p_guardar=False, $p_url_cancelar='', $p_etq_aceptar=ETQ_SALVAR, $p_etq_cancelar=ETQ_CANCELAR, $p_click_cancelar='', $p_click_save='') {
  
  /*# Cierra la forma de captura o edicion
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
  //Forma_CierraTabla( );
  echo "
</form>
</center>\n";*/
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
  
  echo "
        <footer>";

    echo "
          <div style='right: 0px; display: block; padding:0px 50px 10px 0px;' outline='0' class='ui-widget ui-chatbox text-align-center row col-sm-12 col-lg-12 col-md-12'>
          <div class='col col-sm-12 col-lg-10 col-md-12 no-padding text-align-right'>
            <!--<label><strong><h6 class='text-danger'>".ObtenEtiqueta(129)."</h6></strong></label>-->
          </div>
          <div class='col col-sm-12 col-lg-2 col-md-12 no-padding'>";
           if($p_guardar){
				if(empty($p_click_save))
					$p_click_save = "javascript:document.datos.submit();";
				// echo "<a class='btn btn-primary btn-circle btn-xl' title='".$p_etq_aceptar."' name='aceptar' id='aceptar' onClick='javascript:document.datos.submit();'><i class='fa fa-check'></i></a>&nbsp;";
				echo "<a class='btn btn-primary btn-circle btn-xl' title='".$p_etq_aceptar."' name='aceptar' id='aceptar' onClick='".$p_click_save."'><i class='fa fa-check'></i></a>&nbsp;";
			}
    echo "<a class='btn btn-default btn-circle btn-xl' title='".$p_etq_cancelar."' name='cancelar' onClick=\"$click_cancelar\"><i class='fa fa-times'></i></a>
          </div>         
        </footer>
      </form>
    </div>
  </div>";
  
}

function Forma_PresentaError( ) {
  
  /*echo "
  <tr class='css_msg_error'>
    <td>&nbsp;</td>
    <td align='left'>".ETQ_ERROR."</td>
  </tr>";*/
  echo "
  <div class='alert alert-block alert-danger'>	
	<h4 class='alert-heading'><i class='fa fa-check-square-o'></i> Check validation!</h4>
	<p>".ETQ_ERROR."</p>
  </div>";
  
  Forma_Espacio( );
}

function Forma_Sencilla_Ini($p_prompt='', $p_requerido=False, $etq_align='right', $col_sm_etq='col-sm-4') {
  
  if(!empty($p_prompt)) {
    if($p_requerido)
      $p_prompt = "* ".$p_prompt;
    $p_prompt = $p_prompt.":";
  }
  else
    $p_prompt = '&nbsp;';
  /*echo "
  <tr>
    <td align='right' valign='top' class='css_prompt'>$p_prompt</td>
    <td align='left' valign='top' class='css_etq_texto'>";*/
  echo "
  <div class='row'>
    <label class='col $col_sm_etq text-align-$etq_align'><strong>$p_prompt</strong></label>
    <div class='$col_sm_etq'>";
  
}

function Forma_Sencilla_Fin( ) {
  
  /*echo "
    </td>
  </tr>\n";*/
  echo "
    </div>
  </div>";
}

function Forma_Doble_Ini($p_align='center') {
  
  /*echo "
  <tr>
    <td colspan='2' align='$p_align' valign='top' class='css_default'>";*/
  echo "
  <div class='row no-border'>
    <div class='col col-sm-12' align='$p_align'>";
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
  
  /*echo "
  <tr>
    <td colspan='2' align='center'>";
  MuestraTabla($Query, $admin, $p_tabla, $href_link, $p_ancho);
  echo "
    </td>
  </tr>\n";*/
  
  MuestraTabla($Query, $admin, $p_tabla, $href_link, $p_ancho);
  
}

function Forma_Tabla_Ini($p_ancho, $p_tit=array(), $p_ancho_col=array(), $p_nombre='', $p_tresponsive=True) {
  
  Forma_Doble_Ini( );
  if($p_tresponsive)
    Div_Start_Responsive();
  //echo "  
  //<table border='".D_BORDES."' width='$p_ancho' cellpadding='3' cellspacing='0' class='table table-striped table-hover dataTable no-footer has-columns-hidden'";
  echo "  
  <table border='".D_BORDES."' width='100%' cellpadding='3' cellspacing='0' class='table table-striped table-hover dataTable no-footer has-columns-hidden'";
  if(!empty($p_nombre))
    echo " id='$p_nombre'";
  echo ">";
  $tot = count($p_tit);
  if($tot > 0) {
    echo "
    <thead>
    <tr class='txt-color-white'>";
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
      $ancho_col = !empty($p_ancho_col[$i])?$p_ancho_col[$i]:NULL;
      //echo "
      //<th width='$ancho_col' class='text-align-".$align."'>$enc</th>";
      echo "
      <th class='text-align-".$align."' width='$ancho_col' style='background-color:#0092dc;' >$enc</th>";
    }
    echo "    
    </tr>
    </thead>";
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

function Forma_Tabla_Fin($p_tresponsive=True) {
  
  echo "
  </table>";
  if($p_tresponsive)
    Div_close_Resposive();
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
  /*echo "
  <tr><td colspan='2'$ds_height>&nbsp;</td></tr>";*/
  echo "<div class='row'><div class='col col-sm-12'>&nbsp;</div></div>";
}

function Forma_Seccion($p_titulo='', $p_caja=True, $p_align='') {
  
  Forma_Espacio( );
  /*if($p_caja)
    $class = 'css_caja';
  else
    $class = 'css_prompt';
  echo "
  <tr class='$class'>
    <td colspan='2' align='center'>$p_titulo</td>
  </tr>\n";*/
  //echo "<div class='col-md- txt-color-white padding-10'><header class='no-border text-align-center' style='background-color:#0092cd;'>$p_titulo</header></div>";
  echo "<h2 class='no-margin text-align-".$p_align."'><legend><strong>".$p_titulo."</strong></legend></h2>";
}

function Forma_Prompt($p_prompt, $p_requerido=False) {
  
  /*echo "
  <tr>
    <td align='right' valign='top' class='css_prompt'>";
  if($p_requerido) echo "* ";
  echo "$p_prompt:</td>
    <td>&nbsp;</td>
  </tr>\n";*/
  echo "
  <div class='row'>
    <div class='col col-sm-4 text-align-right'><strong>";
  if($p_requerido) echo "* ";
  echo "
      $p_prompt:
    </strong></div>
    <div class='col col-sm-6'>&nbsp;</div>
  </div>";
}

function Forma_PromptDoble($p_titulo, $p_requerido=False) {
  
  /*echo "
  <tr class='$css_prompt'>
    <td colspan='2' align='left'>";
  if($p_requerido) echo "* ";
  echo "$p_titulo</td>
  </tr>\n";*/
  echo "
  <div class='row'>
    <div class='col col-sm-12'>
      <strong>";
  if($p_requerido) echo "* ";
  echo "$p_titulo
      </strong>
    </div>
  </div>";
}

function Forma_CampoOculto($p_nombre, $p_valor='') {
  
  echo "
    <input type='hidden' id='$p_nombre' name='$p_nombre' value=\"$p_valor\">\n";
}

function Forma_CampoInfo($p_prompt, $p_texto, $etq_align='right', $col_sm_etq='col-sm-4') {
  
  /*echo "
  <tr>
    <td align='right' valign='top' class='css_prompt'>";
    if(!empty($p_prompt))
      echo "$p_prompt:";
    else
      echo "&nbsp;";
    echo "</td>
    <td align='left' valign='top' class='css_etq_texto'>$p_texto</td>
  </tr>\n";*/
  echo "
  <div class='row form-group'>
    <label class='$col_sm_etq control-label text-align-$etq_align'>
      <strong>";
  if(!empty($p_prompt))
      echo "$p_prompt:";
  echo "
      </strong>
    </label>
    <div class='col $col_sm_etq'><label class='padding-top-10'>$p_texto</label></div>
  </div>";
}

function Forma_Error($p_error='') {
  
  if(!empty($p_error)){
//    echo "<tr><td>&nbsp;</td><td align='left' class='css_msg_error'>".ObtenMensaje($p_error)."</td></tr>\n";
    echo "
    <div class='alert alert-danger text-align-center'>    
      <h4 class='alert-heading'>Please Note!</h4>
      <p>".ObtenMensaje ($p_error)."</p>    
    </div>";
  }
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

function Forma_CampoTexto($p_prompt, $p_requerido, $p_nombre, $p_valor, $p_maxlength, $p_size, $p_error='', $p_password=False, $p_id='', $fg_visible=True, $p_script='', $p_texto='', $class_div = "form-group", $prompt_aling='right', $col_sm_promt='col-sm-4', $col_sm_cam='col-sm-4') {
  
  if(strpos($p_nombre, 'tr_') === False OR FG_TRADUCCION) {
    if(!empty($p_error)) {
      $ds_error = ObtenMensaje($p_error);
      $ds_clase_err = 'has-error';
      $ds_clase = 'form-control';      
    }
    else {
      $ds_clase = 'form-control';
      $ds_error = "";
      $ds_clase_err = "";
    }
    if(!empty($p_id)) {
      if($fg_visible)
        $ds_visible = "inline";
      else
        $ds_visible = "none";
    }
    /*echo "
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
    }*/
    
    echo "
    <div id='div_".$p_nombre."' class='row ".$class_div." ".$ds_clase_err."'>
      <label class='$col_sm_promt control-label text-align-$prompt_aling'>
        <strong>";
        if(!empty($p_id)) echo "<div id='".$p_id."_ppt' style='display:$ds_visible;'>";
        if($p_requerido) echo "* ";
        if(!empty($p_prompt)) echo "$p_prompt:"; else echo "&nbsp;";
        if(!empty($p_id)) echo "</div>";
    echo "
        </strong>
      </label>
      <div class='$col_sm_cam'>
        <label class='input'>";
        if(!empty($p_id)) echo "<div id='$p_id' style='display:$ds_visible;'>";
        CampoTexto($p_nombre, $p_valor, $p_maxlength, $p_size, $ds_clase, $p_password, $p_script);
        if(!empty($p_texto)) echo "<span class='css_default'>$p_texto</span>";
        if(!empty($p_id)) echo "</div>";
        if(!empty($p_error)){          
          echo "<span class='help-block'><i class='fa fa-warning'></i>".$ds_error."</span><input type='hidden' id='err_".$p_nombre."' value='1'>";
        }
    echo "
        </label>
      </div>      
    </div>";
    
  }
  else
    Forma_CampoOculto($p_nombre, $p_valor);
}

function CampoTextArea($p_nombre, $p_valor, $p_cols, $p_rows, $p_clase='css_input', $p_editar=True) {
  
  if(strpos($p_nombre, 'tr_') === False OR FG_TRADUCCION) {
    echo "<textarea class='$p_clase' id='$p_nombre' name='$p_nombre' cols=$p_cols rows=$p_rows";
    if($p_editar == False)
      echo " readonly='readonly'";
    echo " placeholder='Text area...' >$p_valor</textarea>";
  }
  else
    Forma_CampoOculto($p_nombre, $p_valor);
}

function Forma_CampoTextArea($p_prompt, $p_requerido, $p_nombre, $p_valor, $p_cols, $p_rows, $p_error='', $p_editar=True, $p_puntos=True) {
  
  if(strpos($p_nombre, 'tr_') === False OR FG_TRADUCCION) {
    if(!empty($p_error)) {
      $ds_error = ObtenMensaje($p_error);
      $ds_clase_err = 'has-error';
      $ds_clase = 'custom-scroll';
    } else {
      $ds_clase_err = "";
      $ds_clase = 'custom-scroll';
      $ds_error = "";
    }
    if($p_puntos)
      $align = 'right';
    else
      $align = 'left';
    /*echo "
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
    </tr>\n";*/
    echo "
    <div class='smart-form $ds_clase_err'>
      <label class='col col-sm-4 control-label text-align-right'>
        <strong>";
        if($p_requerido) echo "* ";
        echo $p_prompt;
        if($p_puntos)  echo ":";
    echo "
        </strong>
      </label>
      <div class='col col-sm-4'>
        <label class='textarea'>";
        CampoTextArea($p_nombre, $p_valor, $p_cols, $p_rows, $ds_clase, $p_editar);
        if(!empty($p_error)){          
          echo "<span class='help-block'><i class='fa fa-warning'></i>".$ds_error."</span>";
        }
    echo "</label>
      </div>      
    </div>";
  }
  else
    Forma_CampoOculto($p_nombre, $p_valor);
}

function Forma_CampoTinyMCE($p_prompt, $p_requerido, $p_nombre, $p_valor, $p_cols, $p_rows, $p_error='', $p_script_host='') {
  
  $cl_idioma = ObtenIdioma( );
  if($cl_idioma == 2)
    $lang = "en";
  else{
    if($cl_idioma == 1)
      $lang = "es";
    else
      $lang = "en";
  }

  if(strpos($p_nombre, 'tr_') === False OR FG_TRADUCCION) {
    $ds_clase = "MCE_".$p_nombre;
  
    echo "<div class='row form-group'>";
      echo "<div class='col col-md-12'>";
        echo "<label class='col col-md-6 control-label text-align-right'><strong>";
          if($p_requerido) echo "* ";
          if(!empty($p_prompt)) echo $p_prompt.":";
          echo "</strong>";
        echo "</label>";
      echo "</div>";
    echo "</div>";
    echo "<div class='row form-group'>";
      echo "<div class='col col-md-12'>";
        CampoTextArea($p_nombre, $p_valor, $p_cols, $p_rows, $ds_clase);
      echo "</div>";
    echo "</div>";
  }
  else
    Forma_CampoOculto($p_nombre, $p_valor);
  
  if(!empty($p_error))
    Forma_Error($p_error);
  
    $IMG_FILE_MANAGER = PATH_JS."/ckeditor/ckeditor";
    echo "<script>
      CKEDITOR.replace( '$p_nombre' ,{ 
      // Rutas para file manager
      filebrowserBrowseUrl : '".$IMG_FILE_MANAGER."/responsive_filemanager/filemanager/dialog.php?type=2&editor=ckeditor&fldr=', 
      filebrowserUploadUrl : '".$IMG_FILE_MANAGER."/responsive_filemanager/filemanager/dialog.php?type=2&editor=ckeditor&fldr=', 
      filebrowserImageBrowseUrl : '".$IMG_FILE_MANAGER."/responsive_filemanager/filemanager/dialog.php?type=1&editor=ckeditor&fldr=',
      language: '".$lang."'
      });
    </script>";

}

// ICH 06/10/2016 Funcion original Forma_CampoTinyMCE
/*function Forma_CampoTinyMCE($p_prompt, $p_requerido, $p_nombre, $p_valor, $p_cols, $p_rows, $p_error='', $p_script_host='') {
  
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

		window.open(connector, 'tinyfck', 'modal,width=100%,height=400');
	} 
  </script>";
  
    // echo "<tr>
      // <td align='right' valign='top' class='css_prompt'>";
    // if($p_requerido) echo "* ";
    // echo "$p_prompt:</td>
      // <td valign='top'>";
    // CampoTextArea($p_nombre, $p_valor, $p_cols, $p_rows, $ds_clase);
    // echo "<br></td>
    // </tr>";
    echo "<div class='row form-group'>
          <label class='col col-md-4 control-label text-align-right'><strong>";
          if($p_requerido) echo "* ";
    echo "$p_prompt:</strong></label>
          <div class='col col-md-4'>";
          CampoTextArea($p_nombre, $p_valor, $p_cols, $p_rows, $ds_clase);
    echo "</div>
        </div>";
  }
  else
    Forma_CampoOculto($p_nombre, $p_valor);
  if(!empty($p_error))
    Forma_Error($p_error);

}*/

function CampoLOV($p_folio, $p_val_folio, $p_nombre, $p_valor, $p_size, $p_lov, $p_titulo, $p_tipo_lov, $p_tam_lov, $p_condicion='', $p_clase, $p_var_div='') {
  
  if(!empty($p_condicion))
    $condicion = "$p_condicion.value";
  else
    $condicion = "''";
  Forma_CampoOculto($p_folio, $p_val_folio);
  echo "
  <div class='col col-sm-10'>
    <a href=\"javascript:jLov('$p_lov',$p_tipo_lov,'$p_titulo',$p_tam_lov,'$p_folio','$p_nombre','',$condicion,'$p_var_div');\"><input type='text' class='$p_clase' id='$p_nombre' name='$p_nombre' value=\"$p_valor\" readonly='readonly' size='$p_size'></a>
  </div>
  <div class='col col-sm-1 no-padding'>
    <a title='".ETQ_SELECCIONAR."' href=\"javascript:jLov('$p_lov',$p_tipo_lov,'$p_titulo',$p_tam_lov,'$p_folio','$p_nombre','',$condicion,'$p_var_div');\"><i class='fa fa-fw fa-lg fa-search'></i></a>
  </div>";
}

function Forma_CampoLOV($p_prompt, $p_requerido, $p_folio, $p_val_folio, $p_nombre, $p_valor, $p_size, $p_lov, $p_tipo_lov, $p_tam_lov, $p_condicion='', $p_error='', $p_limpiar=False, $p_var_div='', $class_div='form-group smart-form', $prompt_aling='right', $col_sm_promt='col col-sm-4', $col_sm_cam='col col-sm-4') {  
  if(!empty($p_error)) {
    $ds_error = ObtenMensaje($p_error);
    $ds_clase = 'css_input_error';
    $div_error = 'has-error';
  }
  else {
    $ds_clase = 'form-control';
    $ds_error = "";
    $div_error="";
  }
  $titulo = ETQ_SELECCIONAR." $p_prompt";
  /*echo "
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
  </tr>\n";*/
  echo "
    <div class='row $class_div $div_error'>
      <label class='$col_sm_promt text-align-$prompt_aling'>
      <strong>"; if(!empty($p_requerido)) echo "*"; echo "$p_prompt</strong></label>
      <div class='$col_sm_cam'>
        <label class='input'>";
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
            echo "<div class='col $col_sm_cam'><span class='help-block'><i class='fa fa-warning'></i>".$ds_error."</span></div>";
  echo "</label>   
      </div>
    </div>";
}

function CampoRadio($p_nombre, $p_valor, $p_actual, $p_texto='', $p_editar=True, $p_script='') {
  
  echo "<input type='radio' id='$p_nombre' name='$p_nombre' value='$p_valor'";
  if($p_valor == $p_actual) echo " checked";
  if($p_editar == False) echo " disabled=disabled";
  if(!empty($p_script)) echo " $p_script";
  echo "> <i></i>$p_texto";
}

function Forma_CampoRadio($p_prompt, $p_nombre, $p_valor, $p_actual, $p_texto='', $p_editar=True, $p_script='') {
  
  /*echo "
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
  </tr>\n";*/
  echo "
    <div class='form-group'>
      <label class='col-md-4 control-label text-align-right'>
        <strong>";
        if($p_prompt) echo "$p_prompt:"; else echo "&nbsp;";
    echo "
        </strong>
      </label>
      <div class='col-md-4'>
        <div class='radio'>
          <label>";
            CampoRadio($p_nombre, $p_valor, $p_actual, $p_texto, $p_editar, $p_script);
    echo "
          </label>
        </div>
      </div>     
    </div>";
  
}

function Forma_CampoRadioYN($p_prompt, $p_requerido, $p_nombre, $p_actual, $p_error='', $p_editar=True, $p_script='') {
    
 /* echo "
  <tr>
    <td>&nbsp;</td>
    <td align='left' valign='top'>";
  CampoRadio($p_nombre, '1', $p_actual, ETQ_SI, $p_editar, $p_script);
  echo "&nbsp;&nbsp;";
  CampoRadio($p_nombre, '0', $p_actual, ETQ_NO, $p_editar, $p_script);
  echo "</td>
  </tr>";*/
  echo "
 <div class='form-group'>
  <label class='col-md-4 control-label text-align-right'>";
  Forma_PromptDoble($p_prompt, $p_requerido);
  echo "
  </label>
  <div class='col-md-4'>
      <label class='radio radio-inline'>";
      CampoRadio($p_nombre, '1', $p_actual, ETQ_SI, $p_editar, $p_script);
  echo "
      </label>
      <label class='radio radio-inline'>";
      CampoRadio($p_nombre, '0', $p_actual, ETQ_NO, $p_editar, $p_script);
  echo "
      </label>
    </div>
  </div>";
  Forma_Error($p_error);
}

function CampoCheckbox($p_nombre, $p_valor, $p_texto='', $p_regresa='', $p_editar=True, $p_script='') {
  
  echo "<input class='checkbox' type='checkbox' id='$p_nombre' name='$p_nombre'";
  if(!empty($p_regresa)) echo " value='$p_regresa'";
  if($p_valor == 1) echo " checked";
  if($p_editar == False) echo " disabled=disabled";
  if(!empty($p_script)) echo " $p_script";
  echo "> <span>$p_texto</span>";
}

function Forma_CampoCheckbox($p_prompt, $p_nombre, $p_valor, $p_texto='', $p_regresa='', $p_editar=True, $p_script='', $align_propmt='right', $col_sm_promt='col-sm-4', $col_sm_cam='col-sm-4') {
  
  /*echo "
  <tr>
    <td align='right' valign='middle' class='css_prompt'>$p_prompt:</td>
    <td align='left' valign='middle'>";
  CampoCheckbox($p_nombre, $p_valor, $p_texto, $p_regresa, $p_editar, $p_script);
  echo "</td>
  </tr>\n";*/
  echo "
  <div class='row form-group smart-form '>
    <label class='$col_sm_promt control-label text-align-$align_propmt'>
      <strong>$p_prompt</strong>
    </label>
    <div class='$col_sm_cam'>
      <div class='checkbox'>
        <label>";
          CampoCheckbox($p_nombre, $p_valor, $p_texto, $p_regresa, $p_editar, $p_script);
  echo "
        </label>
      </div>
    </div>     
  </div>";
}

function CampoSelect($p_nombre, $p_opc, $p_val, $p_actual, $p_clase='css_input', $p_seleccionar=False, $p_script='') {
  
  $tot = count($p_opc);
  echo "<select id='$p_nombre' name='$p_nombre' class='select2'";
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

function Forma_CampoSelect($p_prompt, $p_requerido, $p_nombre, $p_opc, $p_val, $p_actual, $p_error='', $p_seleccionar=False, $p_script='', $align_propmt='right', $col_sm_promt='col col-sm-4', $col_sm_cam='col col-sm-4') {
  
  $ds_clase = 'form-control';
  if(!empty($p_error)) {
    $ds_error = ObtenMensaje($p_error);
    $ds_clase_err = 'has-error';
  }
  else {
    $ds_error = "";
    $ds_error_err = "";
    $ds_clase_err = "";
  }
  /*echo "
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
    echo "<tr><td>&nbsp;</td><td align='left' class='css_msg_error'>$ds_error</td></tr>\n";*/
  echo "
  <div id='div_".$p_nombre."' class='row form-group smart-form $ds_clase_err'>
    <label class='$col_sm_promt   control-label text-align-$align_propmt'>
      <strong>";
      if($p_requerido)  echo "* ";
      if(!empty($p_prompt)) echo "$p_prompt:"; else    echo "&nbsp;";
  echo "
      </strong>
    </label>
    <div class='$col_sm_cam'><label class='select'>";
    CampoSelect($p_nombre, $p_opc, $p_val, $p_actual, $ds_clase, $p_seleccionar, $p_script);
    echo "<i></i>";
    if(!empty($p_error))
      echo "<span class='help-block'><i class='fa fa-warning'></i> $ds_error</span>";
  echo "
    </label></div>     
  </div>";
  
}

# p_id_js es utilizada para el select multiple con select2 10/03/2017
function CampoSelectBD($p_nombre, $p_query, $p_actual, $p_clase='', $p_seleccionar=False, $p_script='', $p_valores='', $p_id_js='') {  
  echo "<select id='$p_nombre' name='$p_nombre' class='select2 $p_id_js'";
  if(!empty($p_script)) echo " $p_script";
  // if(!empty($p_data_id)) echo "data-id=".$p_data_id."";
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
  # Si el select es multiple recibimos diferentes valores
  if(!empty($p_valores)){
    echo "    
    <script>
    $(document).ready(function(){
      $(\".$p_id_js\").val([";
    for($k=0;$k<count($p_valores);$k++){
      echo "\"$p_valores[$k]\",";
    }
    echo "
    ]).select2();
    });
    </script>";
  }
}

# p_id_js es utilizada para el select multiple con select2 10/03/2017
function CampoSelectBDF($p_nombre, $p_query, $p_actual, $p_clase='', $p_seleccionar=False, $p_script='', $p_valores='', $p_id_js='') {  
    echo "<select id='$p_nombre' name='$p_nombre' class='select2 $p_id_js'";
    if(!empty($p_script)) echo " $p_script";
    // if(!empty($p_data_id)) echo "data-id=".$p_data_id."";
    echo ">\n";
    if($p_seleccionar)
        echo "<option value=0>".ObtenEtiqueta(70)."</option>\n";
    $rs = EjecutaQuery($p_query);
    while($row = RecuperaRegistro($rs)) {
        echo "<option value=\"$row[3]\"";
        if($p_actual == $row[1])
            echo " selected";
        
        # Determina si se debe elegir un valor por traduccion
        $etq_campo = DecodificaEscogeIdiomaBD($row[0]);
        $etq_periodo = DecodificaEscogeIdiomaBD($row[1]);
        $etq_grado = DecodificaEscogeIdiomaBD($row[2]);
        echo ">$etq_campo <p>Cycle: $etq_periodo</p> <p>Term: $etq_grado</p></option>\n";
    }
    echo "</select>";
    # Si el select es multiple recibimos diferentes valores
    if(!empty($p_valores)){
        echo "    
    <script>
    $(document).ready(function(){
      $(\".$p_id_js\").val([";
        for($k=0;$k<count($p_valores);$k++){
            echo "\"$p_valores[$k]\",";
        }
        echo "
    ]).select2();
    });
    </script>";
    }
}


# p_id_js es utilizada para el select multiple con select2 10/03/2017
function Forma_CampoSelectBD($p_prompt, $p_requerido, $p_nombre, $p_query, $p_actual, $p_error='', $p_seleccionar=False, $p_script='', $etq_align='right',$col_sm_etq = 'col col-md-4', $col_sm_cam = 'col col-md-4', $p_id_js='def') {
  
  $ds_clase = 'form-control';
  if(!empty($p_error)) {
    $ds_error = ObtenMensaje($p_error);
    $ds_clase_err = 'has-error';
  }
  else {
    $ds_error = "";
    $ds_error_err = "";
    $ds_clase_err = "";
  }
  /*echo "
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
    echo "<tr><td>&nbsp;</td><td align='left' class='css_msg_error'>$ds_error</td></tr>\n";*/
  echo "
  <div class='form-group smart-form $ds_clase_err' id='div_$p_nombre'>";
  if($p_prompt=="nolabel"){
      
  }else{
      
      echo"
    <label class='$col_sm_etq control-label text-align-$etq_align'>
      <strong>";
      if($p_requerido)  echo "* ";
      if(!empty($p_prompt)) echo "$p_prompt:"; else    echo "&nbsp;";
      echo "
      </strong>
    </label>";
  }
  echo"
    <div class='$col_sm_cam'><label class='select'>";
    CampoSelectBD($p_nombre, $p_query, $p_actual, $ds_clase, $p_seleccionar, $p_script, '', $p_id_js);
    echo "<i></i>";
    if(!empty($p_error))
      echo "<span class='help-block'><i class='fa fa-warning'></i> $ds_error</span>";
  echo "
    </label></div>     
  </div>";
}

# p_campopais es el nombre del campo del pais
function Forma_CampoSelectCombinado($p_nombre, $p_requerido, $p_valor, $p_maxlength, $p_size, $p_pais, $p_campopais, $p_error='', $div_class = "form-group", $align_propmt='right', $col_sm_promt='col col-sm-4', $col_sm_cam='col col-sm-12' ){
  # Clase 
  $ds_clase = 'form-control';
  # Si hay error
  if(!empty($p_error)) {
    $ds_error = ObtenMensaje($p_error);
    $ds_clase_err = 'has-error';     
    $ds_error = "<span class='help-block'><i class='fa fa-warning'></i>".$ds_error."</span><input type='hidden' id='err_".$p_nombre."' value='1'>";
  } else {
    $ds_clase_err = "";
    $ds_error = "";
  }
  
  # Div que se mostrara  
  echo "
  <div id='div_".$p_nombre."' class='row $div_class $ds_clase_err'>
    <label class='$col_sm_promt text-align-$align_propmt'>";
  if($p_requerido)
    echo "*";
  echo "<strong>".ObtenEtiqueta(285).":</strong></label>
    <div class=' $col_sm_cam' >
      <label class='input' id='div2_".$p_nombre."'>
      </label>
      $ds_error
    </div>
  </div>";

  # Query para las provicias de canada
  $Query  = "SELECT ds_provincia, fl_provincia FROM k_provincias WHERE fl_pais=38 ORDER BY ds_provincia";
  $rs = EjecutaQuery($Query);
  $option = "";
  for($i=0;$row = RecuperaRegistro($rs);$i++){
    $ds_provincia = $row[0];
    $fl_provincia = $row[1];
    
    $option  = $option."<option value=\'$fl_provincia\' ";
    if($fl_provincia == $p_valor)
      $option .= " selected ";
    $option .= " >$ds_provincia</option>";
  }
  $option = $option;

  echo "
  <script>
  $(document).ready(    
    function(){
      // Variables
      var country = '$p_pais', input, select, options='".$option."';
      input   = \"<input type='text' class='$ds_clase' id='$p_nombre' name='$p_nombre' value='$p_valor' maxlength='$p_maxlength' size='$p_size' />\";
      select  = \"<select id='$p_nombre' name='$p_nombre' class='select2'>\";
      select += \"<option value='0'>".ObtenEtiqueta(70)."</option>\";
      select += options;
      select += \"</select><i></i>\";
      
      // Por default va a mostrar el select o campo dependiendo del pais
      if(country == '38'){
        $('#div2_$p_nombre').append(select);
      }
      else{        
        $('#div2_$p_nombre').append(input);
      }
      // Cambios del select
      $('#$p_campopais').change(
        function(){
          $('#div2_$p_nombre').empty();
          if($(this).val()==38){
            $('#div2_$p_nombre').append(select);       
            $('#div2_$p_nombre').addClass('select');       
          }
          else{
            $('#div2_$p_nombre').append(input);
            $(':input#$p_nombre').removeAttr('value');
          }
        }
      );
    }
  );
  </script>";
}

function Forma_Calendario($p_nombre) {
  
  echo "
    <script type='text/javascript'>
    $(function(){
      $('#$p_nombre').datepicker({
        //showOn: 'button',
        //buttonImage: '".PATH_IMAGES."/".IMG_CALENDARIO."',
        //buttonImageOnly: true,
        changeMonth: true,
        changeYear: true,
        dateFormat: '".EscogeIdioma('dd-mm-yy','mm-dd-yy')."',
        showAnim: 'slideDown',
        showOtherMonths: true,
        selectOtherMonths: true,
        showMonthAfterYear: false,
        yearRange: 'c-50:c+2',
        autoSize: false,
        //dayNames: [".ETQ_DIAS_SEMANA."],
        //dayNamesMin: [".ETQ_DIAS_CORTO."],
        //monthNames: [".ETQ_MESES."],
        //monthNamesShort: [".ETQ_MESES_CORTO."],
        prevText : '<',
				nextText : '>'
      });   
		});
    $('#$p_nombre').addClass('hasDatepicker');
    $('<i class=\'icon-append fa fa-calendar\'></i>').insertBefore('#$p_nombre');
    /*Al elemento se le cambia de clase   */ 
    $('#div_".$p_nombre."').removeClass('form-control');
    if($('#err_".$p_nombre."').val()=='1')
      $('#div_".$p_nombre."').attr('class','row smart-form has-error');
    else
      $('#div_".$p_nombre."').attr('class','row form-group smart-form');
    $('#$p_nombre').removeClass('form-control');
    $('#$p_nombre').attr('class','datepicker');
    //$('#div_".$p_nombre."').css('margin-left','-30px');
		</script>";  
}

// p_accept Recibe extensiones admitidas de archivo separados por |
// p_maxlength Total de archivos permitidos 0=Ilimitado
function CampoArchivo($p_nombre, $p_size, $p_clase, $p_accept='', $p_maxlength='1') {
  
  if(strpos($p_nombre, 'tr_') === False OR FG_TRADUCCION) {
    $ds_accept = "";
    $ds_maxlength = "";
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

function Forma_CampoArchivo($p_prompt, $p_requerido, $p_nombre, $p_size, $p_error='', $p_accept='', $p_maxlength='1', $align_propmt='right', $col_sm_promt='col-sm-4', $col_sm_cam='col-sm-4') {
  
  if(strpos($p_nombre, 'tr_') === False OR FG_TRADUCCION) {
    if(!empty($p_error)) {
      $ds_error = ObtenMensaje($p_error);
      $ds_clase = 'css_input_error';
      $ds_clase_err = 'state_error txt-color-red';
      $ds_error_msg = 'note-error txt-color-red';
    }
    else {
      $ds_clase = 'css_input';
      $ds_error = "";
      $ds_clase_err = "";
      $ds_error_msg = "";
    }
    /*echo "
    <tr>
      <td align='right' valign='middle' class='css_prompt'>";
    if($p_requerido) echo "* ";
    echo "$p_prompt:</td>
      <td align='left' valign='middle' class='css_msg_error'>";
    CampoArchivo($p_nombre, $p_size, $ds_clase, $p_accept, $p_maxlength);
    if(!empty($p_error))
      echo "\n<br>$ds_error";
    echo "</td>
    </tr>\n";*/
    echo "
    <div class='row form-group $ds_clase_err'>
      <label class='$col_sm_promt text-align-$align_propmt'><strong>"; if($p_requerido) echo "* "; echo "$p_prompt:</strong></label>
      <div class='$col_sm_cam'>
        <div class='input input-file'>";
        CampoArchivo($p_nombre, $p_size, $ds_clase, $p_accept, $p_maxlength);
    echo "          
        </div>";
      if(!empty($p_error))
        echo "<div class='note $ds_error_msg'>".$ds_error."</div>";
    echo "
      </div>
    </div>";
  }
  else
    Forma_CampoOculto($p_nombre);
}

function Forma_CampoUpload($p_prompt, $p_desc, $p_nombre, $p_valor, $p_ruta, $p_requerido, $p_archivo, $p_size, $p_error='', $p_accept='', $p_maxlength='1', $align_propmt='right', $col_sm_promt='col-sm-4', $col_sm_cam='col-sm-5') {
  $ds_desc ="";
  if(!empty($p_desc))
    $ds_desc = " ($p_desc)";
  if(!empty($p_valor)) {
    //Forma_CampoPreview('', $p_nombre, $p_valor, $p_ruta, False, !$p_requerido, $align_propmt, $col_sm_promt, $col_sm_cam);
    //Forma_CampoArchivo(ObtenEtiqueta(216).$ds_desc, $p_requerido, $p_archivo, $p_size, $p_error, $p_accept, $p_maxlength, $align_propmt, $col_sm_promt, $col_sm_cam);
    Forma_CampoPreviewUpload($p_prompt, $p_nombre, $p_valor, $p_ruta, $p_requerido, $p_archivo, !$p_requerido, $p_size, $p_error, $p_accept, $p_maxlength, $align_propmt, $col_sm_promt, $col_sm_cam);
  }
  else
    Forma_CampoArchivo($p_prompt.$ds_desc, $p_requerido, $p_archivo, $p_size, $p_error, $p_accept, $p_maxlength, $align_propmt, $col_sm_promt, $col_sm_cam);
}

function Forma_CampoPreview($p_prompt, $p_nombre, $p_valor, $p_ruta, $p_video=False, $p_limpiar=True, $etq_align='right', $col_sm_etq='col-sm-4', $col_sm_cam='col-sm-4') {
  
  //Forma_Sencilla_Ini($p_prompt, False, $etq_align, $col_sm_etq);
  //Forma_Sencilla_Fin( );
  echo "<div class='col $col_sm_cam'><span id='nom_$p_nombre'><a id='a1_$p_nombre' href=";
  if(!$p_video)
    echo "\"javascript:Preview('$p_ruta/$p_valor');\"";
  else
    echo "'preview_flv.php?archivo=$p_valor' target='_blank'";
  echo ">$p_valor</a></label>";
  if($p_limpiar)
    echo "
    <a href=\"javascript:LimpiaCampo('$p_nombre');\" class='btn btn-xs btn-default' title='".ETQ_LIMPIAR."'>
      <i class='fa fa-eraser'></i>
    </a>";  
  echo "</span></div>"; 
  //Forma_Sencilla_Fin();
  Forma_CampoOculto($p_nombre, $p_valor);
}

function Forma_CampoPreviewUpload($p_prompt, $p_nombre, $p_valor, $p_ruta, $p_requerido, $p_archivo, $p_limpiar=True, $p_size, $p_error, $p_accept, $p_maxlength, $etq_align='right', $col_sm_etq='col-sm-4', $col_sm_cam='col-sm-4'){
  if(!empty($p_error)) {
    $ds_error = ObtenMensaje($p_error);
    $ds_clase = 'css_input_error';
    $ds_clase_err = 'state_error txt-color-red';
  }
  else {
    $ds_clase = 'css_input';
    $ds_error = "";
    $ds_clase_err = "";
  }
  echo "
  <div class='row smart-form form-group $ds_clase_err'>
    <label class='$col_sm_etq control-label text-align-$etq_align'><strong>";
    if($p_requerido)
      echo "*";
  echo $p_prompt.":
      </strong>
    </label>
    <div class='$col_sm_cam'>
      <div class='panel panel-default no-border'>
        <div class='panel-body status no-border'>
          <div class='who clearfix no-border no-padding padding-top-5'>
            <a id='nom_$p_nombre' href=";
            if(!isset($p_video))
              echo "\"javascript:Preview('$p_ruta/$p_valor');\"><img src='$p_ruta/$p_valor' alt='img' class='busy'>";
            else
              echo "'preview_flv.php?archivo=$p_valor' target='_blank'>$p_valor";
  echo "    </a>
            <span class='name font-sm'> 
              <span class='pull-right font-xs text-muted'>
              <span class='text-muted'>".ObtenEtiqueta(216)."</span>
              <b class='txt-color-red'>";
              if($p_error)
                echo "<h3><strong>".$ds_error."</strong></h3>";
              if($p_limpiar){
                echo "
                <span class='pull-right font-xs text-muted'>
                  <a href=\"javascript:LimpiaCampo('$p_nombre');\" class='btn btn-xs btn-primary' title='".ETQ_LIMPIAR."'>
                    <i class='fa fa-trash-o'></i>
                  </a>
                </span>";
              }
 echo "
              </b>
              <i>";
              Forma_CampoOculto('ds_ruta_foto', !empty($ds_ruta_foto)?$ds_ruta_foto:'');
              CampoArchivo($p_archivo, $p_size, $ds_clase, !empty($ds_accept)?$ds_accept:'', $p_maxlength);                                    
  echo "      </i>
              </span>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>";
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
    /*echo "
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
    </tr>";*/
    if(!empty($p_error)) {
      $ds_error = ObtenMensaje($p_error);
      echo "<div class='css_msg_error'>$ds_error</div>";
    } else {
      $ds_clase_err="";
    }
    echo "
    <div class='form-group'>
      <label class='col col-sm-4 text-align-right'><strong>"; 
      if($p_requerido) echo "* ";
      if(!empty($p_prompt)) echo "$p_prompt:"; else "&nbsp;";
      echo "</strong></label>
      <div class='col col-sm-5'>
        <div class='input input-file $ds_clase_err'>";
        FileUploader($p_nombre, $p_extensions, $p_size_limit, $p_autosubmit);
    echo "          
        </div>";
      if(!empty($p_error))
        echo "<div class='note note-error'>". ObtenMensaje($p_error)."</div>";
    echo "
      </div>
    </div>";
  }
  else
    Forma_CampoOculto($p_nombre);
}

#
# MRA: Acordeon
#

# Funcion para mostrar secciones plegables en acordeon
function Forma_Plegable_Ini($p_titulo='', $p_id='', $p_activo=False, $p_minheight='') {
  
  /*if(!empty($p_minheight))
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
  Forma_AbreTabla( );*/
  echo "
  <div class='widget-body'>			
    <div class='panel-group smart-accordion-default' id='accordion'>
      <div class='panel panel-default'>
        <div class='panel-heading'>
          <h4 class='panel-title'>
            <a class='collapsed' aria-expanded='false' data-toggle='collapse' data-parent='#accordion' href='#accordion$p_id'> 
            <i class='fa fa-lg fa-angle-down pull-right'></i> 
            <i class='fa fa-lg fa-angle-up pull-right'></i><strong>$p_titulo</strong>
            </a>
          </h4>
        </div>
        <div aria-expanded='false' id='accordion$p_id' class='panel-collapse collapse'>
          <div class='panel-body'>";
          //Forma_AbreTabla( );
}

# Funcion para cerrar un tab
function Forma_Plegable_Fin( ) {
  
  Forma_CierraTabla( );
  /*echo "
  </div>";
  Forma_Doble_Fin( );*/
  echo "  
      </div>
    </div>
  </div>";
}

# Funcion para que los div sean resposivos 
function Div_Start_Responsive(){
  echo "
  <div class='table-responsive'>";
}

function Div_close_Resposive(){
  echo "</div>";
}


Function PresentaModal($p_id, $p_header, $p_body, $p_footer, $p_script=""){
  $css_header = $css_header ?? "";
  $css_body = $css_body ?? "";
  $css_footer = $css_footer ?? "";
  echo "
  <div class='modal fade' id='".$p_id."' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true' style='display: none;'>
    <div class='modal-dialog'>
      <div class='modal-content'>
        <div class='modal-header padding-10' $css_header>
          <button type='button' class='close' data-dismiss='modal' aria-hidden='true'>×</button>
          <h4 class='modal-title' id='myModalLabel'>".$p_header."</h4>
        </div>
        <div class='modal-body padding-10' $css_body>
          <div class='row'>
            ".$p_body."
          </div>
        </div>
        <div class='modal-footer padding-10' $css_footer>
        ".$p_footer."
        </div>
      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
  </div>".$p_script;
  
}

# ICH 06/03/2017 - Ult. Mod 29/05/2017
Function CargaImagenDropZone($p_titulo, $p_id, $no_tab, $editar=False, $clave = 0, $fg_error = 0, $ds_img_err = "", $fg_tipo_img = "", $p_script="", $p_tipo_resp="T"){

  $ord = substr("{$p_id}", 11, 1);
  $ds_resp_img = "ds_resp_img_".$ord."_".$no_tab;
  echo "<div class='row'>";

    echo "<label class='col col-sm-12 control-label text-align-left'>";
      echo "<strong>{$p_titulo}</strong>";
        $row = RecuperaValor("SELECT r.ds_respuesta, p.fg_posicion_img FROM k_quiz_respuesta r, k_quiz_pregunta p WHERE r.fl_quiz_pregunta = $clave AND r.no_orden = $ord AND r.no_tab = $no_tab AND r.fl_quiz_pregunta = p.fl_quiz_pregunta");
        $ds_respuesta = str_texto(!empty($row[0])?$row[0]:NULL);
        $fg_posicion_img = str_texto(!empty($row[1])?$row[1]:NULL);
        
        if($fg_posicion_img == "L"){
          $tam_gd_w = "330px";
          $tam_gd_h = "180px";
          $tam_sm_w = "50px;";
          $tam_sm_h = "30px;";
          $padding  = "185px;";
         }
        else{
          $tam_gd_w = "180px";
          $tam_gd_h = "330px";
          $tam_sm_w = "30px;";
          $tam_sm_h = "50px;";
          $padding  = "335px";
        }
        
        require '../campus/preview.inc.php';
        $ruta = PATH_MODULOS."/fame/uploads";       
        $t = $ord."_".$no_tab;
        $padding = "style='padding-top:3px;'";
      echo '
      <a class=\'zoomimg\' href=\'#\'>
        <img src=\''.$ruta.'/'.$ds_respuesta.'\' id=\''.$t.'\' class=\'away no-border\' width=\''.$tam_sm_w.'\' height=\''.$tam_sm_h.'\'>
        <span id=\'div_1_'.$t.'\' style=\'left:-75px; width:'.$tam_gd_w.'; height:'.$tam_gd_h.';\'>
          <div id=\'div_2_'.$t.'\' class=\'modal-dialog demo-modal\' style=\'width:'.$tam_gd_w.'; height:'.$tam_gd_h.';\'>
            <div id=\'div_3_'.$t.'\'class=\'modal-content\' style=\'width:'.$tam_gd_w.'; height:'.$tam_gd_h.'; padding-bottom:'.$padding.';\'>
              <div class=\'modal-body padding-5\'>
                <img class=\'superbox-current-img\' src=\''.$ruta.'/'.$ds_respuesta.'\' id=\'2_'.$t.'\'>
                <br>
              </div><br>
            </div>
          </div>
        </span>
      </a>';
      if($p_tipo_resp=="T"){
        $ds_respuesta = "";
      }
      
      if(empty($clave))
        Forma_CampoOculto("nb_img_prev_{$p_id}", $ds_img_err);   
      else
        Forma_CampoOculto("nb_img_prev_{$p_id}", $ds_respuesta);
      

      if($fg_error){        
        $t = $ord."_".$no_tab;
        $img = $ruta."/".$ds_img_err;
        echo "<script>
          var fg_tipo_img = '$fg_tipo_img';
          if(fg_tipo_img){
            document.getElementById('$t').src = '$img';
            document.getElementById('2_$t').src = '$img';
            document.getElementById('nb_img_prev_mydropzone_$t').value = '$ds_img_err';  
            logo = document.getElementById('$t');
            div_1 = document.getElementById('div_1_$t');
            div_2 = document.getElementById('div_2_$t');
            div_3 = document.getElementById('div_3_$t');
            if(fg_tipo_img == 'P'){
              div_1.style.width = '180px';
              div_1.style.height = '330px';
              div_2.style.width = '180px';
              div_2.style.height = '330px';
              div_3.style.width = '180px';
              div_3.style.height = '330px';
              logo.width = 30;
              logo.height = 50;  
            }else{
              div_1.style.width = '330px';
              div_1.style.height = '180px';
              div_2.style.width = '330px';
              div_2.style.height = '180px';
              div_3.style.width = '330px';
              div_3.style.height = '180px';
              logo.width = 50;
              logo.height = 30;  
            }
          }
        
        </script>";
      }
        
    echo "</label>";
    echo "<div class='col col-sm-12' {$padding}>";
      echo "<div data-widget-editbutton='false'><!-- class='jarviswidget jarviswidget-color-blueLight' -->";
        echo "<div>";
          echo "<div class='widget-body'>";
            echo "<div class='dropzone' id='{$p_id}' style='min-height: 120px; padding:10px 0px 0px 20px'></div>";
          echo "</div>";
        echo "</div>";
      echo "</div>";
    echo "</div>";
  echo "</div>";
  
  echo "<script type='text/javascript'>
    // DO NOT REMOVE : GLOBAL FUNCTIONS!
    $(document).ready(function() {
      pageSetUp();
      Dropzone.autoDiscover = false;
      $('#{$p_id}').dropzone({
        url: 'upload.php?ord={$ord}&no_tab={$no_tab}&editar={$editar}&clave={$clave}',
        // data:  'id=1',
        addRemoveLinks : true,
        maxFilesize: 1024,
        // Solo permite guardar un registro
        maxFiles: 1,        
        acceptedFiles: 'image/*,.jpeg,.jpg,.png,.JPEG,.JPG,.PNG',
        init: function() {
          this.on('error', function(file, message) { 
          alert('".ObtenEtiqueta(1239)."');
          this.removeFile(file); 
          });
        },        
        success: function(file, result){
          var message, status, name;
          message = JSON.parse(result);
          status = message.valores.status;
          name = message.valores.file_name;
          if(status==true){
            $('#nb_img_prev_{$p_id}').val(name);
            {$p_script}
          }
        },
        removedfile: function(file) {
          file.previewElement.remove();
          $('#nb_img_prev_{$p_id}').val('');
          {$p_script}
        }
      });
    })
  </script>";
}

# JGFL 26/09/17
# Funcion para el dropzone
function Forma_DropzoneVideos($clave, $ds_file, $title='Dropzone', $name_dro='dropzone1', $urlfile_drop, $files_types=".mov, .mp4", $name_parametros=array(), $val_parametros=array(), $recargar=true, $ruta_img, $urlfile_proceso, $p_type='', $ruta_preview = "preview_flv.php", $extra = "", $fg_error=0, $vid_title=false, $script_dro=""){
  # Si no hay tipo estar en blanco
  if(!empty($p_type))
    $par_type = "&type=".$p_type;
  else
    $par_type = "";
  # Si hay tiene un documento
  if(!empty($ds_file)){
    echo '
    <!-- Inicio Muestra Imagen y Proceso -->
    <div class="row" id="grl_progress1'.$name_dro.'">
      <style>           
        [data-progressbar-value]::after{
          content: ""
        }
        [data-progressbar-value]::before{
          content: ""
        }
      </style>
      <div class="col col-sm-12 col-lg-1 col-md-12">&nbsp;</div>
      <div class="col col-sm-12 col-lg-2 col-md-12">
        <div class="padding-10">';
        if(!file_exists($ruta_img))
          $ruta_img = SP_IMAGES_W."/PosterFrame_White.jpg";
        if($fg_error==1){          
          echo '<strong>'.$ds_file.'</strong><br><img src="'.$ruta_img.'" class="superbox-img">';
          $edit_reset = false;
        }
        else{
          echo '<a  href="'.$ruta_preview.'?archivo='.$ds_file.'&clave='.$clave.$par_type.$extra.'" target="_blank">
            <img src="'.$ruta_img.'" class="superbox-img">
          </a>';
          $edit_reset = true;
        }
     echo '
        </div>
      </div>
      <div class="col col-sm-12 col-lg-6 col-md-12">
        <div class="padding-10">
          <p>
            <div><strong>'.ObtenEtiqueta(1864).'</strong></div>                
          </p>
          <div class="progress" data-progressbar-value="0" id="grl_progress'.$name_dro.'"><div class="progress-bar" id="progress_hls'.$name_dro.'">0%</div></div>
        </div>
        <div class="checkbox padding-10">
          <label>';
          # Checkbox para resetear video
          CampoCheckbox('fg_reset_video_'.$name_dro, !empty($fg_reset_video)?$fg_reset_video:'', 'Re-start video encoding', '', $edit_reset);
          # Lo utilizamos para verificar el proceso de la conversion
          Forma_CampoOculto('total_convertido'.$name_dro, !empty($total_convertido)?$total_convertido:'');            
    echo '                
          </label>
        </div>
      </div>
      <div class="col col-sm-12 col-lg-3 col-md-12">&nbsp;<input type="hidden" id="camp_progreso_hls'.$name_dro.'" name="camp_progreso_hls'.$name_dro.'"/></div>
    </div>
    <script>
    // Consulta el archivo convertidor
    var error = '.$fg_error.';
    var clave = "'.$clave.'";    
    if(error==0){
    setInterval(function(){
      var total_convertido = $("#total_convertido'.$name_dro.'").val(); 
      
      if(total_convertido<=100){
      $.ajax({
          type: "GET",
          url : "'.$urlfile_proceso.'",
          data: "clave='.$clave.'"+
                "&archivo='.$ds_file.'"+
                "&type='.$p_type.'"
        }).done(function(result){
          var content, tabContainer;
          content = JSON.parse(result);
          progress = content.progress;
          if(!content.error){
            if(progress<=100){
              $("#duration").empty().append(content.duration + "&nbsp;Mins");
              $("#grl_progress'.$name_dro.'").attr("data-progressbar-value", progress);
              $("#progress_hls'.$name_dro.'").empty().append(progress + "%");
              $("#tab'.$name_dro.'").empty().append(progress + "%");
              $("#camp_progreso_hls'.$name_dro.'").empty().val(progress);
              $("#total_convertido'.$name_dro.'").empty().val(progress);
            }
          }
          else{
            // $("#grl_progress1'.$name_dro.'").empty().append("Error upload");
            $("#grl_progress'.$name_dro.'").attr("data-progressbar-value", progress);
            $("#progress_hls'.$name_dro.'").empty().append(progress + "%");
          }
        });
      }
      $("#code_info").addClass("hidden");
    }, 
    4000);
    }
    </script>
    <!-- Fin Muestra Imagen y Proceso -->';
  }
  # Campo para el titulo de video
  if($vid_title==true){
    echo "
    <div class='row smart-form'>
      <label class='col col-sm-12 col-lg-4 col-md-12 text-align-right control-label'>
        <strong>Title Video: </strong>
      </label>
      <div class='col col-sm-12 col-lg-8 col-md-12' id='div_title_v_".$name_dro."'>
        <input type='text' id='title_vid_".$name_dro."' name='title_vid_".$name_dro."' placeholder='Title video' class='form-control'/>
        <div id='msg_err_".$name_dro."' class='note note-error hidden txt-color-red'>You must add a title of the video</div>
      </div>
    </div>";
  }
  # Dropzone    
  echo '
  <!-- Inicio de DROPZONE --->
  <div class="row">
    <style>      
    #'.$name_dro.'.dropzone .dz-default.dz-message{
      background-image: url('.PATH_HOME.'/bootstrap/img/dropzone/spritemap_videos.png);
    }
    </style>
    <div class="col-sm-3">&nbsp;</div>
    <div class="col-xs-12 col-sm-5">
    <input type="hidden" name="nb_video" id="nb_video" value="'.$ds_file.'">
      <div class="row">
        <label class="col col-sm-12 control-label text-align-left">
        <strong>'.$title.'</strong>
        </label>
        <div class="col col-sm-12">
          <div data-widget-editbutton="false">
            <div>
              <div class="widget-body">
                <div class="dropzone" id="'.$name_dro.'" style="min-height: 120px; padding:10px 0px 0px 20px"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <script type="text/javascript">
        // DO NOT REMOVE : GLOBAL FUNCTIONS!
        $(document).ready(function() {
          // pageSetUp();
          Dropzone.autoDiscover = false;
          var progress_lecc1 = $("#progress_leccion'.$name_dro.'");
          $("#'.$name_dro.'").dropzone({
            url: "'.$urlfile_drop.'",
            addRemoveLinks : true,
            maxFilesize: 2048,            
            acceptedFiles: "'.$files_types.'",
            // Solo permite guardar un registro
            maxFiles: 1,           
            accept: function(file, done) {
              var filen = file.name;
              var active_title = "'.$vid_title.'";
              var elem_title = $("#title_vid_'.$name_dro.'");
              var val_title = elem_title.val();
              // Si esta activado el campo de titulo debe agregar texto              
              if(active_title==1 && val_title.length==0){
                $(".dz-error-mark").css("opacity","0.8");
                $(file.previewElement).find(".dz-error-message").text("You must add a title of the video").css("opacity", "0.8").css("margin-left", "100px");
                $("#div_title_v_'.$name_dro.'").addClass("state-error");
                $("#msg_err_'.$name_dro.'").removeClass("hidden");
                 this.removeFile(file);
              }
              else{
                if (filen.indexOf(" ")>0) {
                  $(".dz-error-mark").css("opacity","0.8");
                  $(file.previewElement).find(".dz-error-message").text("The file name should not have spaces, please change name file").css("opacity", "0.8").css("margin-left", "100px");
                }
                else {                
                  done(); 
                }
                $("#div_title_v_'.$name_dro.'").removeClass("state-error");
                $("#msg_err_'.$name_dro.'").addClass("hidden");
              }
            },
            init: function() {
              this.on("error", function(file, message) {                    
              this.removeFile(file); 
              });
              this.on("beforeSend", function(){                  
                $("#upload_videos'.$name_dro.'").modal("toggle");
                progress_lecc1.empty().width("0%").append("0%");
                $(".dz-progress").hide();
              });
              // Proceso del upload
              this.on("uploadprogress", function (file, progress, bytesSent){
                var progress2 = Math.round(progress);
                progress_lecc1.empty().width(progress2 + "%").append(progress2 + "%");
                $(".dz-progress").hide();
              });
              // Enviamos la clave
              this.on("sending", function (file, xhr, formData, e) {               
                var elem_title = $("#title_vid_'.$name_dro.'");
                var val_title = elem_title.val();
                formData.append("title_video", val_title); ';
              # Valores a enviar
              for($i=0;$i<=sizeof($name_parametros)-1;$i++){
                echo 'formData.append("'.$name_parametros[$i].'", "'.$val_parametros[$i].'");';
              }
  echo '
              });
              this.on("processing", function(file){
                 $("#upload_videos'.$name_dro.'").modal("toggle");
              });
              this.on("success", function(file, response) {
                  var obj = jQuery.parseJSON(response)
                  // agregamos el tipo del archivo
                  $("#fg_tipo_video").val(obj.valores.type);
                  $("#fg_upload_videos").val(1);
                  // Guardamos
                  var save = "'.$recargar.'";
                  var active_title = "'.$vid_title.'";
                  var elem_title = $("#title_vid_'.$name_dro.'");
                  if(save==true)
                    document.datos.submit();
                  // Si require de titulo una vez subido limpia
                  if(active_title==1){
                    elem_title.val("");
                    this.removeFile(file);
                  }
              });
            },     
            complete: function(file, result){
              if(file.status == "success"){
                document.getElementById("nb_video").value = file.name;
                '.$script_dro.'
              }
              var progress3 = "100%";
              progress_lecc1.empty().width(progress3).append(progress3);
              $("#upload_videos'.$name_dro.'").modal("toggle");                               
            },                  
            removedfile: function(file, serverFileName){
              var name = file.name;                   
              var element;
              (element = file.previewElement) != null ? 
              element.parentNode.removeChild(file.previewElement) : 
              false;
            }
          });
        })
      </script>
    </div>
  </div>
  <!-- Se muestra cuando esta guardando --->
  <div class="modal fade text-align-center" id="upload_videos'.$name_dro.'" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="row" style="position:relative; top:40%;">
      <div class="col col-lg-4 col-sm-12 col-md-12 col-xs-12">&nbsp;</div>
      <div class="col col-lg-4 col-sm-12 col-md-12 col-xs-12"> 
        <i class="fa fa-cog fa-3x fa-spin txt-color-white"></i><h2><strong class="txt-color-white"> Loading....</strong></h2>
        <div class="progress">
          <div id="progress_leccion'.$name_dro.'" class="progress-bar bg-color-teal" aria-valuetransitiongoal="0" style="width: 0%; background-color:#0092cd !important;" aria-valuenow="0">0%</div>
        </div>
      </div>
      <div class="col col-lg-4 col-sm-12 col-md-12 col-xs-12">&nbsp;</div>
    </div>
  </div>
  <!-- Fin de DROPZONE --->';
}
# Funcion para eliminar directorios
function eliminarDirec($carpeta){
  foreach(glob($carpeta . "/*") as $archivos_carpeta){
    // echo $archivos_carpeta;
    if (is_dir($archivos_carpeta)){
      eliminarDirec($archivos_carpeta);
    }
    else{
      unlink($archivos_carpeta);
    }
  }
  rmdir($carpeta);
}

function Forma_CampoTextoM($p_prompt, $p_requerido, $p_nombre, $p_valor, $p_maxlength, $p_size, $p_error='', $p_password=False, $p_id='', $fg_visible=True, $p_script='', $p_texto='', $class_div = "form-group", $prompt_aling='right', $col_sm_promt='col-sm-4', $col_sm_cam='col-sm-4',$etq_err='') {
    
    if(strpos($p_nombre, 'tr_') === False OR FG_TRADUCCION) {
        if(!empty($p_error)) {
            $ds_error = ObtenMensaje($p_error);
            $ds_clase_err = 'has-error';
            $ds_clase = 'form-control';      
        }
        else {
            $ds_clase = 'form-control';
            $ds_error = "";
            $ds_clase_err = '';
        }
        if(!empty($p_id)) {
            if($fg_visible)
                $ds_visible = "inline";
            else
                $ds_visible = "none";
        }
        
        
        echo "
    <div id='div_".$p_nombre."' class='row ".$class_div." ".$ds_clase_err."'>
      <label class='$col_sm_promt control-label text-align-$prompt_aling'>
        <strong>";
        if(!empty($p_id)) echo "<div id='".$p_id."_ppt' style='display:$ds_visible;'>";
        if($p_requerido) echo "* ";
        if(!empty($p_prompt)) echo "$p_prompt:"; else echo "&nbsp;";
        if(!empty($p_id)) echo "</div>";
        echo "
        </strong>
      </label>
      <div class='$col_sm_cam'>
        <label class='input'>";
        if(!empty($p_id)) echo "<div id='$p_id' style='display:$ds_visible;'>";
        CampoTexto($p_nombre, $p_valor, $p_maxlength, $p_size, $ds_clase, $p_password, $p_script);
        if(!empty($p_texto)) echo "<span class='css_default'>$p_texto</span>";
        if(!empty($p_id)) echo "</div>";
        if(!empty($p_error)){          
            echo "<span class='help-block'><i class='fa fa-warning'></i>".$ds_error."</span><input type='hidden' id='err_".$p_nombre."' value='1'>";
        }
        echo "
        </label>
        <em id='err_$p_id' class='hidden' style='font-size:11px;color:#D56161;font-style: normal;' class='invalid'>$etq_err</em>
      </div>      
    </div>";
        
    }
    else
        Forma_CampoOculto($p_nombre, $p_valor);
}

?>
