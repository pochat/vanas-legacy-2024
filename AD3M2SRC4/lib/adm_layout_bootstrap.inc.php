<?php

#
# MRA: Funciones generales de despliegue
#
# Menu principal del Sistema de Administracion

function Btstrp_ArmaMenu() {

    # Recupera las descripciones de los modulos
    $Query = "SELECT fl_modulo, nb_modulo, tr_modulo ";
    $Query .= "FROM c_modulo ";
    $Query .= "WHERE fl_modulo_padre=" . MENU_ADMON . " ";
    $Query .= "AND fg_admon='1' ";
    $Query .= "AND fg_menu='1' ";
    $Query .= "ORDER BY no_orden";
    $rs = EjecutaQuery($Query);
    for ($i = 1; $row = RecuperaRegistro($rs); $i++) {
        $fl_modulo[$i] = $row[0];
        $nb_modulo[$i] = str_texto(EscogeIdioma($row[1], $row[2]));
        $Query = "SELECT fl_funcion, nb_funcion, tr_funcion, nb_programa ";
        $Query .= "FROM c_funcion ";
        $Query .= "WHERE fl_modulo=$fl_modulo[$i] ";
        $Query .= "AND fg_menu=1 ";
        $Query .= "ORDER BY no_orden";
        $rs2 = EjecutaQuery($Query);
        for ($j = 1; $row2 = RecuperaRegistro($rs2); $j++) {
            $fl_funcion[$i][$j] = $row2[0];
            $nb_funcion[$i][$j] = str_texto(EscogeIdioma($row2[1], $row2[2]));
            $nb_programa[$i][$j] = str_texto($row2[3]);
        }
        $tot_submodulos[$i] = $j - 1;
    }
    $tot_modulos = $i - 1;

    # Tes de iconos
    $iconos = array(1 => "fa-windows", "fa-vimeo-square", " fa-dropbox", "fa-folder", "fa-gears", "fa-unlock-alt ", "fa-archive");
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
        <a href="' . PAGINA_INICIO . '">
          <img src="' . PATH_HOME . '/bootstrap/img/avatars/sunny.png" alt="me" class="online" /> 
          <span class="text-align-center">
            ' . ObtenNombre() . '<!--&nbsp(
            ' . date(EscogeIdioma("d-m-Y", "m-d-Y")) . ')-->
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
    for ($i = 1; $i <= $tot_modulos; $i++) {
        $menu .= '
          <li id="mod_' . $fl_modulo[$i] . '">
            <a href="#" title="' . $nb_modulo[$i] . '"><i class="fa ' . $iconos[$i] . '"></i> <span class="menu-item-parent">' . $nb_modulo[$i] . '</span></a>
            <ul>';
        for ($j = 1; $j <= $tot_submodulos[$i]; $j++) {
            $menu .= '
              <li id="fun_' . $fl_funcion[$i][$j] . '">
                <a href="' . PATH_MODULOS . $nb_programa[$i][$j] . '" title="' . $nb_funcion[$i][$j] . '" onclick="Nav_active(' . $fl_modulo[$i] . ',' . $fl_funcion[$i][$j] . ');"><span class="menu-item-parent">' . $nb_funcion[$i][$j] . '</span></a>
              </li>';
        }
        $menu .= ' 
            </ul>	
          </li>';
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

function Btstrp_PresentaHeader() {

    # Inicializa variables
    $Menu = ArmaMenu();
    $clave = RecibeParametroNumerico('clave');

    $page_title = ETQ_TITULO_PAGINA;

    # Incluimos el header
    include(SP_HOME . "/AD3M2SRC4/bootstrap/inc/header.php");
    echo $Menu;
    echo "
        <div id='vanas_preloader'></div>
        <!--No hay registros seleccionados-->
        <div id='no_select'>" . ObtenEtiqueta(845) . "
          <div><button id='btn_noselect'>" . ObtenEtiqueta(46) . "</button></div>
        </div>
        <!-- Confirmacion de Enroll Student -->
        <div id='enroll_confirmation'>
          <div id='enroll1'>" . ObtenEtiqueta(846) . "</div>
          <div id='enroll2'>" . ObtenEtiqueta(847) . "</div>
          <div id='ok_confirmar'>
            <button id='si1'>" . ObtenEtiqueta(16) . "</button>
            <button id='no1'>" . ObtenEtiqueta(17) . "</button>
            <button id='si2'>" . ObtenEtiqueta(16) . "</button>
            <button id='no2'>" . ObtenEtiqueta(17) . "</button>
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
          <div id='ok' style='text-align:center; display:none;' ><button onclick=\"location.reload();\">" . ObtenEtiqueta(46) . "</button></div>
        </div>
        <!-- Muestra Circulo de proceso -->
        <div id='preloaderletter'  style='display:none;'>
          <div id='loaderletter'>&nbsp;</div>
        </div>
        <!-- MAIN PANEL -->
        <div id='main' role='main'> ";
}

# Termina el cuerpo y cierra la pagina

function Btstrp_PresentaFooter() {
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
    include(SP_HOME . "/AD3M2SRC4/bootstrap/inc/scripts.php");
    include(SP_HOME . "/AD3M2SRC4/bootstrap/inc/footer.php");
}

# Presenta el encabezado de la pagina seleccionada en tipo Modulo > Funcion

function Btstrp_PresentaEncabezado($p_funcion = 0) {

    # Recupera la descripcion de la funcion
    $Query = "SELECT a.nb_funcion, a.tr_funcion, b.nb_modulo, b.tr_modulo ";
    $Query .= "FROM c_funcion a, c_modulo b ";
    $Query .= "WHERE a.fl_modulo=b.fl_modulo ";
    $Query .= "AND a.fl_funcion=$p_funcion ";
    $row = RecuperaValor($Query);
    $nb_funcion = str_texto(EscogeIdioma($row[0], $row[1]));
    $nb_modulo = str_texto(EscogeIdioma($row[2], $row[3]));

    # Identifica si es una funcion de detalle
    $nb_programa = ObtenProgramaActual();
    if (strpos($nb_programa, PGM_FORM))
        $forma = ETQ_DETALLE;


    $breadcrumb = "<li>Home</li>";
    $page_header = "Select an option from submenu";
    if (!empty($p_funcion)) {
        $breadcrumb .= "<li>{$nb_modulo}</li><li>{$nb_funcion}</li><li>{$forma}</li>";
        $page_header = "
    <!-- PAGE HEADER -->
    <i class='fa-fw fa fa-home'></i> 
      " . $nb_funcion . "
    <span><i class='fa fa-chevron-right'></i>
      " . $forma . "
    </span>";
    }

    # Obtemenos el modudlo actual  
    $rowm = RecuperaValor("SELECT fl_modulo FROM c_funcion WHERE fl_funcion='" . $p_funcion . "'");
    echo "
  <input type='hidden' id='act_mod' value='" . $rowm[0] . "' />
  <input type='hidden' id='act_fun' value='" . $p_funcion . "' />
  <!--RIBBON-->
  <div id='ribbon'>
    <span class='ribbon-button-alignment'>
      <span id='refresh' class='btn btn-ribbon' data-action='resetWidgets' data-title='refresh' rel='tooltip' data-placement='bottom' data-original-title='<i class=\"text-warning fa fa-warning\"></i>
      Warning! this will reset all your widget settings.' data-html='true'>
      <i class='fa fa-refresh'></i>
      </span>
    </span>

    <!-- breadcrumb -->
    <ol class='breadcrumb'>
      " . $breadcrumb . "
    </ol>
    <!-- end breadcrumb -->
  </div>
  <!--END RIBBON-->

  <!-- MAIN CONTENT -->
  <div class='padding-5'>
    <!-- row -->
    <div class='row' >
      <!-- col -->
      <div class='col-xs-12 col-sm-7 col-md-7 col-lg-4'>
        <h1 class='page-title txt-color-blueDark'>
          
        </h1>
      </div>
      <!-- end col -->
    </div>
    <!-- end row -->
    <!-- widget grid -->
    <section id='widget-grid' class=''>
      <!-- Row -->
      <div class='row'>
        <!-- NEW WIDGET START -->
        <article class='col-xs-12 col-sm-12 col-md-12 col-lg-12'>";
}

# Funcion para mostrar pestanas de tipo folder con ligas

function Btstrp_PresentaFolders($p_nombres = array(), $p_ligas = array(), $p_actual) {

    $tot = count($p_nombres);
    $p_actual = $p_actual - 1;
    if (empty($p_actual) || $p_actual < 0 || $p_actual > ($tot - 1))
        $p_actual = 0;
    for ($i = 0; $i < $tot; $i++) {
        if ($i != $p_actual)
            $cadena[$i] = "<li><a href='" . $p_ligas[$i] . "'><b>" . $p_nombres[$i] . "</b></a></li>\n";
        else
            $cadena[$i] = "<li class='current'><a href='" . $p_ligas[$i] . "'><b>" . $p_nombres[$i] . "</b></a></li>\n";
    }
    echo "
    <span class='preload17a'></span>
    <span class='preload17b'></span>
    <ul class='menu17'>\n";
    for ($i = 0; $i < $tot; $i++) {
        echo $cadena[$i];
    }
    echo "    </ul><br>\n";
}

#
# MRA: Funciones para formas de captura (programas *_frm)
#

function Btstrp_Forma_Inicia($p_clave, $p_multipart = False) {

    # Determina el programa para enviar la forma
    $nb_programa = ObtenProgramaNombre(PGM_INSUPD);
    # Inicia la forma
    echo "
  <!-- Widget ID (each widget will need unique ID)-->
  <div class='jarviswidget jarviswidget-color-darken' id='wid-id-0' data-widget-editbutton='false' data-widget-deletebutton='false'>
      <!-- widget content -->
      <div class='widget-body'>
        <form name='datos' method='post' action='$nb_programa' class='form-horizontal'";
    if ($p_multipart)
        echo " enctype='multipart/form-data'";
    echo ">";
    Forma_CampoOculto('clave', $p_clave);
    //Forma_AbreTabla('90%'); 
}

function Btstrp_Forma_Termina($p_guardar = False, $p_url_cancelar = '', $p_etq_aceptar = ETQ_SALVAR, $p_etq_cancelar = ETQ_CANCELAR, $p_click_cancelar = '') {

    /* # Cierra la forma de captura o edicion
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
      </center>\n"; */
    # Destino para el boton Cancelar
    if (empty($p_click_cancelar)) {
        if (empty($p_url_cancelar)) {
            $nb_programa = ObtenProgramaBase();
            $click_cancelar = "parent.location='$nb_programa'";
        } else
            $click_cancelar = "parent.location='$p_url_cancelar'";
    } else
        $click_cancelar = $p_click_cancelar;

    echo "
        <footer>               
          
          <div class='col col-sm-12 text-align-center padding-top-15'>";
    # Muestra el boton para guardar, por omision no se permite
    if ($p_guardar)
        echo "
            <button type='button' class='btn btn-primary' name='aceptar' onClick='javascript:document.datos.submit();'>" . $p_etq_aceptar . "</button>&nbsp;&nbsp;&nbsp;";
    echo "
            <button type='button' class='btn btn-default' name='cancelar' onClick=\"$click_cancelar\">" . $p_etq_cancelar . "</button>";
    echo "
          </div>
          <div class='col col-sm-4'></div>
        </footer>
      </form>
    </div>
  </div>";
}

function Btstrp_Forma_PresentaError() {

    /* echo "
      <tr class='css_msg_error'>
      <td>&nbsp;</td>
      <td align='left'>".ETQ_ERROR."</td>
      </tr>"; */
    echo "
  <div class='alert alert-block alert-danger'>	
	<h4 class='alert-heading'><i class='fa fa-check-square-o'></i> Check validation!</h4>
	<p>" . ETQ_ERROR . "</p>
  </div>";

    Forma_Espacio();
}

function Btstrp_Forma_CampoOculto($p_nombre, $p_valor = '') {

    echo "
    <input type='hidden' id='$p_nombre' name='$p_nombre' value=\"$p_valor\">\n";
}

function Btstrp_Forma_CampoInfo($label, $text) {

    return '<div class="form-group">'
            . '<label class="col-xs-12" style="font-size:1.2em;"><strong>' . $label . '</strong></label>'
            . '<span class="col-xs-12" style="">' . $text . '</span>'
            . '</div>';
}

function Btstrp_Forma_Error($p_error = '') {

    if (!empty($p_error)) {
//    echo "<tr><td>&nbsp;</td><td align='left' class='css_msg_error'>".ObtenMensaje($p_error)."</td></tr>\n";
        echo "
    <div class='alert alert-danger text-align-center'>    
      <h4 class='alert-heading'>Please Note!</h4>
      <p>" . ObtenMensaje($p_error) . "</p>    
    </div>";
    }
}

function Btstrp_CampoTexto($p_nombre, $p_valor, $p_maxlength, $p_size, $p_clase = 'css_input', $p_password = False, $p_script = '') {

    if (strpos($p_nombre, 'tr_') === False OR FG_TRADUCCION) {
        if (!$p_password)
            $ds_tipo = 'text';
        else
            $ds_tipo = 'password';
        echo "<input type='$ds_tipo' class='$p_clase' id='$p_nombre' name='$p_nombre' value=\"$p_valor\" maxlength='$p_maxlength' size='$p_size'";
        if ($p_password)
            echo " autocomplete='off'";
        if (!empty($p_script))
            echo " $p_script";
        echo ">";
    } else
        Forma_CampoOculto($p_nombre, $p_valor);
}

function Btstrp_Forma_CampoTexto($p_prompt, $p_requerido, $p_nombre, $p_valor, $p_maxlength, $p_size, $p_error = '', $p_password = False, $p_id = '', $fg_visible = True, $p_script = '', $p_texto = '') {

    if (strpos($p_nombre, 'tr_') === False OR FG_TRADUCCION) {
        if (!empty($p_error)) {
            $ds_error = ObtenMensaje($p_error);
            $ds_clase_err = 'has-error';
            $ds_clase = 'form-control';
        } else {
            $ds_clase = 'form-control';
            $ds_error = "";
            $ds_clase_err = '';
        }
        if (!empty($p_id)) {
            if ($fg_visible)
                $ds_visible = "inline";
            else
                $ds_visible = "none";
        }
        /* echo "
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
          } */

        echo "
    <div id='div_" . $p_nombre . "' class='row form-group " . $ds_clase_err . "'>
      <label class='col-md-4 control-label text-align-right'>
        <strong>";
        if (!empty($p_id))
            echo "<div id='" . $p_id . "_ppt' style='display:$ds_visible;'>";
        if ($p_requerido)
            echo "* ";
        if (!empty($p_prompt))
            echo "$p_prompt:";
        else
            echo "&nbsp;";
        if (!empty($p_id))
            echo "</div>";
        echo "
        </strong>
      </label>
      <div class='col-md-4'>
        <label class='input'>";
        if (!empty($p_id))
            echo "<div id='$p_id' style='display:$ds_visible;'>";
        CampoTexto($p_nombre, $p_valor, $p_maxlength, $p_size, $ds_clase, $p_password, $p_script);
        if (!empty($p_texto))
            echo "<span class='css_default'>$p_texto</span>";
        if (!empty($p_id))
            echo "</div>";
        if (!empty($p_error)) {
            echo "<span class='help-block'><i class='fa fa-warning'></i>" . $ds_error . "</span><input type='hidden' id='err_" . $p_nombre . "' value='1'>";
        }
        echo "
        </label>
      </div>      
    </div>";
    } else
        Forma_CampoOculto($p_nombre, $p_valor);
}

function Btstrp_CampoTextArea($p_nombre, $p_valor, $p_cols, $p_rows, $p_clase = 'css_input', $p_editar = True) {

    if (strpos($p_nombre, 'tr_') === False OR FG_TRADUCCION) {
        echo "<textarea class='$p_clase' id='$p_nombre' name='$p_nombre' cols=$p_cols rows=$p_rows";
        if ($p_editar == False)
            echo " readonly='readonly'";
        echo " placeholder='Text area...' >$p_valor</textarea>";
    } else
        Forma_CampoOculto($p_nombre, $p_valor);
}

function Btstrp_Forma_CampoTinyMCE($p_prompt, $p_requerido, $p_nombre, $p_valor, $p_cols, $p_rows, $p_error = '', $p_script_host = '') {

    if (strpos($p_nombre, 'tr_') === False OR FG_TRADUCCION) {
        $ds_clase = "MCE_" . $p_nombre;
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
        if ($p_script_host) {
            echo "
    ,remove_script_host: false";
        }
        echo " 
  });
  
  function Btstrp_fileBrowserCallBack(field_name, url, type, win) {
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
        if ($p_requerido)
            echo "* ";
        echo "$p_prompt:</td>
      <td valign='top'>";
        CampoTextArea($p_nombre, $p_valor, $p_cols, $p_rows, $ds_clase);
        echo "<br></td>
    </tr>\n";
    } else
        Forma_CampoOculto($p_nombre, $p_valor);
    if (!empty($p_error))
        Forma_Error($p_error);
}

function Btstrp_CampoLOV($p_folio, $p_val_folio, $p_nombre, $p_valor, $p_size, $p_lov, $p_titulo, $p_tipo_lov, $p_tam_lov, $p_condicion = '', $p_clase, $p_var_div = '') {

    if (!empty($p_condicion))
        $condicion = "$p_condicion.value";
    else
        $condicion = "''";
    Forma_CampoOculto($p_folio, $p_val_folio);
    echo "
  <div class='col col-sm-10'>
    <a href=\"javascript:jLov('$p_lov',$p_tipo_lov,'$p_titulo',$p_tam_lov,'$p_folio','$p_nombre','',$condicion,'$p_var_div');\"><input type='text' class='$p_clase' id='$p_nombre' name='$p_nombre' value=\"$p_valor\" readonly='readonly' size='$p_size'></a>
  </div>
  <div class='col col-sm-1 no-padding'>
    <a title='" . ETQ_SELECCIONAR . "' href=\"javascript:jLov('$p_lov',$p_tipo_lov,'$p_titulo',$p_tam_lov,'$p_folio','$p_nombre','',$condicion,'$p_var_div');\"><i class='fa fa-fw fa-lg fa-search'></i></a>
  </div>";
}

function Btstrp_Forma_CampoLOV($p_prompt, $p_requerido, $p_folio, $p_val_folio, $p_nombre, $p_valor, $p_size, $p_lov, $p_tipo_lov, $p_tam_lov, $p_condicion = '', $p_error = '', $p_limpiar = False, $p_var_div = '') {

    if (!empty($p_error)) {
        $ds_error = ObtenMensaje($p_error);
        $ds_clase = 'css_input_error';
    } else {
        $ds_clase = 'form-control';
        $ds_error = "";
    }
    $titulo = ETQ_SELECCIONAR . " $p_prompt";
    /* echo "
      <tr>
      <td align='right' valign='middle' class='css_prompt'>";
      if($p_requerido) echo "* ";
      echo "$p_prompt:</td>
      <td align='left' valign='middle' class='css_msg_error'>\n";
      CampoLOV($p_folio, $p_val_folio, $p_nombre, $p_valor, $p_size, $p_lov, $titulo, $p_tipo_lov, $p_tam_lov, $p_condicion, $ds_clase, $p_var_div);
      if($p_limpiar)
      echo "
      <script type='text/javascript'>
      function Btstrp_LimpiaLOV(folio, campo) {
      $('#'+folio).val('');
      $('#'+campo).val('');
      }
      </script>
      <a href=\"javascript:LimpiaLOV('$p_folio','$p_nombre');\">
      <img src='".PATH_IMAGES."/".IMG_LIMPIAR."' title='".ETQ_LIMPIAR."' width='14' height='14' border='0'></a>";
      if(!empty($p_error))
      echo "<br>$ds_error";
      echo "</td>
      </tr>\n"; */
    echo "
    <div class='row'>
      <label class='col col-sm-4 text-align-right'>
      <strong>";
    if (!empty($p_requerido))
        echo "*";
    echo "$p_prompt</strong></label>
      <div class='col col-sm-4 smart-form'>
        <label class='input'>";
    CampoLOV($p_folio, $p_val_folio, $p_nombre, $p_valor, $p_size, $p_lov, $titulo, $p_tipo_lov, $p_tam_lov, $p_condicion, $ds_clase, $p_var_div);
    echo "</label>
      </div>
    </div>";
}

function Btstrp_CampoRadio($p_nombre, $p_valor, $p_actual, $p_texto = '', $p_editar = True, $p_script = '') {

    echo "<input type='radio' id='$p_nombre' name='$p_nombre' value='$p_valor'";
    if ($p_valor == $p_actual)
        echo " checked";
    if ($p_editar == False)
        echo " disabled=disabled";
    if (!empty($p_script))
        echo " $p_script";
    echo "> <span>$p_texto</span>";
}

function Btstrp_Forma_CampoRadio($p_prompt, $p_nombre, $p_valor, $p_actual, $p_texto = '', $p_editar = True, $p_script = '') {

    /* echo "
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
      </tr>\n"; */
    echo "
    <div class='form-group'>
      <label class='col-md-4 control-label text-align-right'>
        <strong>";
    if ($p_prompt)
        echo "$p_prompt:";
    else
        echo "&nbsp;";
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

function Btstrp_Forma_CampoRadioYN($p_prompt, $p_requerido, $p_nombre, $p_actual, $p_error = '', $p_editar = True, $p_script = '') {

    /* echo "
      <tr>
      <td>&nbsp;</td>
      <td align='left' valign='top'>";
      CampoRadio($p_nombre, '1', $p_actual, ETQ_SI, $p_editar, $p_script);
      echo "&nbsp;&nbsp;";
      CampoRadio($p_nombre, '0', $p_actual, ETQ_NO, $p_editar, $p_script);
      echo "</td>
      </tr>"; */
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

function Btstrp_CampoCheckbox($p_nombre, $p_valor, $p_texto = '', $p_regresa = '', $p_editar = True, $p_script = '') {

    echo "<input class='checkbox' type='checkbox' id='$p_nombre' name='$p_nombre'";
    if (!empty($p_regresa))
        echo " value='$p_regresa'";
    if ($p_valor == 1)
        echo " checked";
    if ($p_editar == False)
        echo " disabled=disabled";
    if (!empty($p_script))
        echo " $p_script";
    echo "> <span>$p_texto</span>";
}

function Btstrp_V_Forma_CampoCheckbox($p_prompt, $p_nombre, $p_valor, $p_texto = '', $p_regresa = '', $p_editar = True, $p_script = '') {

    /* echo "
      <tr>
      <td align='right' valign='middle' class='css_prompt'>$p_prompt:</td>
      <td align='left' valign='middle'>";
      CampoCheckbox($p_nombre, $p_valor, $p_texto, $p_regresa, $p_editar, $p_script);
      echo "</td>
      </tr>\n"; */
    echo "
  <div class='form-group'>
    <label class='col-xs-9 control-label text-align-right'>
      <strong>$p_prompt</strong>
    </label>
    <div class='col-xs-3'>
      <div class='checkbox'>
        <label>";
    CampoCheckbox($p_nombre, $p_valor, $p_texto, $p_regresa, $p_editar, $p_script);
    echo "
        </label>
      </div>
    </div>     
  </div>";
}

function Btstrp_CampoSelect($p_nombre, $p_opc, $p_val, $p_actual, $p_clase = 'css_input', $p_seleccionar = False, $p_script = '') {

    $tot = count($p_opc);
    echo "<select id='$p_nombre' name='$p_nombre' class='$p_clase'";
    if (!empty($p_script))
        echo " $p_script";
    echo ">\n";
    if ($p_seleccionar)
        echo "<option value=0>" . ObtenEtiqueta(70) . "</option>\n";
    for ($i = 0; $i < $tot; $i++) {
        echo "<option value=\"$p_val[$i]\"";
        if ($p_actual == $p_val[$i])
            echo " selected";
        echo ">$p_opc[$i]</option>\n";
    }
    echo "</select>";
}

function Btstrp_Forma_CampoSelect($p_prompt, $p_requerido, $p_nombre, $p_opc, $p_val, $p_actual, $p_error = '', $p_seleccionar = False, $p_script = '') {

    $ds_clase = 'form-control';
    if (!empty($p_error)) {
        $ds_error = ObtenMensaje($p_error);
        $ds_clase_err = 'has-error';
    } else {
        $ds_error = "";
        $ds_error_err = "";
    }
    /* echo "
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
      echo "<tr><td>&nbsp;</td><td align='left' class='css_msg_error'>$ds_error</td></tr>\n"; */
    echo "
  <div class='form-group $ds_clase_err'>
    <label class='col-md-4 control-label text-align-right'>
      <strong>";
    if ($p_requerido)
        echo "* ";
    if (!empty($p_prompt))
        echo "$p_prompt:";
    else
        echo "&nbsp;";
    echo "
      </strong>
    </label>
    <div class='col-md-4'>";
    CampoSelect($p_nombre, $p_opc, $p_val, $p_actual, $ds_clase, $p_seleccionar, $p_script);
    if (!empty($p_error))
        echo "<span class='help-block'><i class='fa fa-warning'></i> $ds_error</span>";
    echo "
    </div>     
  </div>";
}

function Btstrp_CampoSelectBD($p_nombre, $p_query, $p_actual, $p_clase = 'css_input', $p_seleccionar = False, $p_script = '') {

    echo "<select id='$p_nombre' name='$p_nombre' class='$p_clase'";
    if (!empty($p_script))
        echo " $p_script";
    echo ">\n";
    if ($p_seleccionar)
        echo "<option value=0>" . ObtenEtiqueta(70) . "</option>\n";
    $rs = EjecutaQuery($p_query);
    while ($row = RecuperaRegistro($rs)) {
        echo "<option value=\"$row[1]\"";
        if ($p_actual == $row[1])
            echo " selected";

        # Determina si se debe elegir un valor por traduccion
        $etq_campo = DecodificaEscogeIdiomaBD($row[0]);
        echo ">$etq_campo</option>\n";
    }
    echo "</select>";
}

function Btstrp_Forma_CampoSelectBD($p_prompt, $p_requerido, $p_nombre, $p_query, $p_actual, $p_error = '', $p_seleccionar = False, $p_script = '') {

    $ds_clase = 'form-control';
    if (!empty($p_error)) {
        $ds_error = ObtenMensaje($p_error);
        $ds_clase_err = 'has-error';
    } else {
        $ds_error = "";
        $ds_error_err = "";
    }
    /* echo "
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
      echo "<tr><td>&nbsp;</td><td align='left' class='css_msg_error'>$ds_error</td></tr>\n"; */
    echo "
  <div class='form-group $ds_clase_err'>
    <label class='col-md-4 control-label text-align-right'>
      <strong>";
    if ($p_requerido)
        echo "* ";
    if (!empty($p_prompt))
        echo "$p_prompt:";
    else
        echo "&nbsp;";
    echo "
      </strong>
    </label>
    <div class='col-md-4'>";
    CampoSelectBD($p_nombre, $p_query, $p_actual, $ds_clase, $p_seleccionar, $p_script);
    if (!empty($p_error))
        echo "<span class='help-block'><i class='fa fa-warning'></i> $ds_error</span>";
    echo "
    </div>     
  </div>";
}

function Btstrp_Forma_CampoSelectComB($p_nombre, $p_valor, $p_maxlength, $p_size, $p_pais) {
    echo "
  <div class='row'>
    <label class='col col-sm-4 text-align-right'><strong>" . ObtenEtiqueta(285) . ":</strong></label>
    <div class='col col-sm-4'>";
    if ($fl_pais == 38)
        CampoTexto($p_nombre, $p_valor, $p_maxlength, $p_size, $p_clase = 'form-control');
    else {
        $Query = "SELECT ds_pais, fl_pais FROM c_pais ORDER BY ds_pais";
        CampoSelectBD($p_nombre, $Query, $p_actual, $p_clase = 'form-control', $p_seleccionar = True);
    }
    echo "
    </div>
  </div>";
}

function Btstrp_Forma_Calendario($p_nombre) {

    echo "
    <script type='text/javascript'>
    $(function(){
      $('#$p_nombre').datepicker({
        showOn: 'button',
        buttonImage: '" . PATH_IMAGES . "/" . IMG_CALENDARIO . "',
        buttonImageOnly: true,
        changeMonth: true,
        changeYear: true,
        dateFormat: '" . EscogeIdioma('dd-mm-yy', 'mm-dd-yy') . "',
        showAnim: 'slideDown',
        showOtherMonths: true,
        selectOtherMonths: true,
        showMonthAfterYear: false,
        yearRange: 'c-50:c+2',
        autoSize: true,
        dayNames: [" . ETQ_DIAS_SEMANA . "],
        dayNamesMin: [" . ETQ_DIAS_CORTO . "],
        monthNames: [" . ETQ_MESES . "],
        monthNamesShort: [" . ETQ_MESES_CORTO . "],
        nextText: '" . ETQ_SIGUIENTE . "',
        prevText: '" . ETQ_ANTERIOR . "'
      });
		});
    /*Al elemento se le cambia de clase*/    
    $('#div_" . $p_nombre . "').removeClass('form-control');
    if($('#err_" . $p_nombre . "').val()=='1')
      $('#div_" . $p_nombre . "').attr('class','row smart-form has-error')
    else
      $('#div_" . $p_nombre . "').attr('class','row smart-form')
    $('#$p_nombre').removeClass('form-control');
    $('#$p_nombre').attr('class','datepicker');
		</script>\n";
}

function Btstrp_Forma_CampoTextArea($p_prompt, $p_requerido, $p_nombre, $p_valor, $p_cols, $p_rows, $p_error='', $p_editar=True, $p_puntos=True) {
 
  if(strpos($p_nombre, 'tr_') === False OR FG_TRADUCCION) {
    if(!empty($p_error)) {
      $ds_error = ObtenMensaje($p_error);
      $ds_clase_err = 'has-error';
      $ds_clase = 'form-control custom-scroll';
    }
    else {
      $ds_clase_err = '';
      $ds_clase = 'form-control custom-scroll';
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
    <div class='form-group $ds_clase_err'>
      <label>";
        if($p_requerido) echo "* ";
        echo $p_prompt;
        if($p_puntos)  echo ":";
    echo "
      </label>
        ";
        Btstrp_CampoTextArea($p_nombre, $p_valor, $p_cols, $p_rows, $ds_clase, $p_editar);
        if(!empty($p_error)){          
          echo "<span class='help-block'><i class='fa fa-warning'></i>".$ds_error."</span>";
        }
    echo "  
    </div>";
  }
  else
    Forma_CampoOculto($p_nombre, $p_valor);
}

?>