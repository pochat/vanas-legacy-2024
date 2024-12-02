<?php

# MRA: Funciones para template

class Paginacion {
  var $NumRegsTotal;
  var $NumRegsPag;
  var $NumRegsIni;
  var $NumRegsFin;
  var $NumPagsTotal;
  var $NumPagsMostrar;
  var $PagActual;
  var $PagInicial;
  var $SetActual;
  var $Anteriores;
  var $Siguientes;
  var $Anterior;
  var $Siguiente;
  var $Self;
  var $Criterio;
  var $Actual;
  
  function __construct($Query, $NumRegsPag, $PagActual, $Self, $Criterio, $Actual) {
    
    $this->Self = $Self;
    $this->NumRegsPag = $NumRegsPag;
    $this->NumPagsMostrar = MAXPAGS;
    $this->PagActual = $PagActual;
    $this->SetActual = ceil($PagActual / $this->NumPagsMostrar);
    $this->Criterio = $Criterio;
    $this->Actual = $Actual;
    
    // Pagina de inicio del set
    $this->PagInicial = ($this->NumPagsMostrar * ($this->SetActual - 1)) + 1;
    
    $rs = EjecutaQuery($Query);
    if(!$rs)
      return;
    
    $this->NumRegsTotal = CuentaRegistros($rs);
    $this->NumPagsTotal = ceil($this->NumRegsTotal / $this->NumRegsPag);
    
    // Si el numero de paginas del query es menor que el de las paginas a mostrar  
    if($this->NumPagsTotal < $this->NumPagsMostrar)
      $this->NumPagsMostrar = $this->NumPagsTotal;
    
    // Calcula los registros mostrados
    $this->NumRegsIni = (($this->PagActual * $this->NumRegsPag) - $this->NumRegsPag) + 1;
    $this->NumRegsFin = $this->NumRegsIni + $this->NumRegsPag - 1;
    if($this->NumRegsFin > $this->NumRegsTotal)
      $this->NumRegsFin = $this->NumRegsTotal;
  }
  
  function Link_Paginas( ) {
    
    $Paginas  = "<TABLE border='".D_BORDES."' cellPadding='0' cellSpacing='0'>
      <form name='paginar' method='post' action='$this->Self'>
        <input type='hidden' name='criterio' value='$this->Criterio'>
        <input type='hidden' name='actual' value='$this->Actual'>
        <input type='hidden' name='PagActual'>
      </form>
    <tr>\n";
    $Paginas .= "<td align=center class='css_paginas'>" . $this->Anteriores( ) . "</td>\n";
    $Paginas .= "<td align=center class='css_paginas'>" . $this->Anterior( ) . "</td>\n";
    for($i = $this->PagInicial; ($i < ($this->PagInicial + $this->NumPagsMostrar)) && ($i <= $this->NumPagsTotal); $i++) {
      if($i != $this->PagActual)
        $Paginas .= "<td align=center class='css_paginas'>&nbsp;&nbsp;<a href=\"javascript:Paginacion($i);\">$i</a>&nbsp;&nbsp;</td>\n";
      else
        $Paginas .= "<td align=center class='css_paginas'>&nbsp;&nbsp;$i&nbsp;&nbsp;</td>\n";
    }
    $Paginas .= "<td align=center class='css_paginas'>" . $this->Siguiente( ) . "</td>\n";
    $Paginas .= "<td align=center class='css_paginas'>" . $this->Siguientes( ) . "</td>\n";
    $Paginas .= "</tr></table>";
    
    return $Paginas;
  }
  
  function Anteriores( ) {
    if($this->SetActual > 1)
      $this->Anteriores = "&nbsp;&nbsp;<a href=\"javascript:Paginacion(".(((($this->SetActual-1)*$this->NumPagsMostrar)+1)-$this->NumPagsMostrar).");\">&lt;&lt;</a>&nbsp;&nbsp;";
    else
      $this->Anteriores = "&nbsp;";
    return $this->Anteriores;
  }
  
  function Siguientes( ) {
    if(($this->NumPagsTotal > $this->NumPagsMostrar) && ($this->PagInicial+$this->NumPagsMostrar <= $this->NumPagsTotal))
      $this->Siguientes = "&nbsp;&nbsp;<a href=\"javascript:Paginacion(".(($this->SetActual*$this->NumPagsMostrar)+1).");\">>></a>&nbsp;&nbsp;";
    else
      $this->Siguientes = "&nbsp;";      
    return $this->Siguientes;
  }
  
  function Anterior( ) {
    if($this->PagActual > 1)
      $this->Anterior = "&nbsp;&nbsp;<a href=\"javascript:Paginacion(".($this->PagActual-1).");\">&lt</a>&nbsp;&nbsp;";
    else
      $this->Anterior = "&nbsp;";
    return $this->Anterior;
  }
  
  function Siguiente( ) {
    if($this->PagActual < $this->NumPagsTotal)
      $this->Siguiente = "&nbsp;<a href=\"javascript:Paginacion(".($this->PagActual+1).");\">></a>&nbsp;&nbsp;";
    else
      $this->Siguiente = "&nbsp;";      
    return $this->Siguiente;
  }
}


class Datos_Paginacion {
  var $PagActual;
  var $NumRegsPag;
  var $Criterio;
  var $Actual;
  
  function __construct() {
    
    # Recibe parametros
    $this->PagActual = RecibeParametroNumerico('PagActual');
    if(empty($this->PagActual))
      $this->PagActual = 1;
    $this->NumRegsPag = REGSXPAG;
    $this->Criterio = RecibeParametroHTML('criterio');
    $this->Actual = RecibeParametroNumerico('actual');
    if(empty($this->Actual))
      $this->Actual = 0;
  }
  
  function LeePagActual( ) {
    return $this->PagActual;
  }
  
  function LeeNumRegsPag( ) {
    return $this->NumRegsPag;
  }  
  
  function LeeCriterio( ) {
    return $this->Criterio;
  }
  
  function LeeActual( ) {
    return $this->Actual;
  }
}


function PresentaListado($Query, $admin=TB_LN_IUD, $fg_buscar=False, $fg_export=False, $campos=array(), $href_link = "", $href_link2 = "",$icono1="", $icono2="",$funcion, $p_seleccionar=False, $p_letter=False, $p_enroll=False, $fg_filters=False) {

  # Variables initialization to avoid error
  $num_span=NULL;
  $Filtros=NULL;

  # Determina los nombres de los programas complementarios
  $Self = ObtenProgramaBase( ); // Este programa, para poder paginar
  $href_insert = ObtenProgramaNombre(PGM_FORM); // Programa para insertar
  $href_update = ObtenProgramaNombre(PGM_FORM); // Programa para modificar
  $href_delete = ObtenProgramaNombre(PGM_DELETE); // Programa para borrar
  
  $Datos = new Datos_Paginacion( ); 
  $PagActual  = $Datos->LeePagActual( );
  $NumRegsPag = $Datos->LeeNumRegsPag( );
  $criterio   = $Datos->LeeCriterio( );
  $actual     = $Datos->LeeActual( );
  
  // Ejecuta el query
  $Rows = EjecutaQueryLimit($Query, $NumRegsPag, (($PagActual * $NumRegsPag) - ($NumRegsPag)));
  $Rows = EjecutaQuery($Query);
  //$num_span = CuentaCampos($Rows) + 2;
  $Pags = new Paginacion($Query, $NumRegsPag, $PagActual, $Self, $criterio, $actual);
  
  // Prepara renglon para busqueda y exportacion a Excel
  $Buscar = RenglonBuscarExport($fg_buscar, $fg_export, $campos, $criterio, $actual, $num_span, $Self, $p_letter, $p_enroll);
  
  # Agregamos los Filtros de Busqueda estan por default True
  if($fg_filters)
    $Filtros = str_replace("td", "th", Filters($Rows, $p_seleccionar));
  
  // Crea el encabezado para la tabla
  $Encabezado = Encabezado($Rows, $admin, $href_link, $href_link2, $icono1,$icono2,$funcion,$p_seleccionar);
  
  // Lee los registros que regresa el query
  $Registros = Lee_Registros($Rows, $admin, $href_insert, $href_update, $href_delete, '', $href_link, $href_link2, $icono1,$icono2,$funcion, $p_seleccionar);
  
  // Arma ligas para paginado
  $Paginas = $Pags->Link_Paginas( );
  
  // Prepara renglon con opcion nuevo, paginacion y total de registros
  $BaseTabla = RenglonBase($num_span, $admin, $href_insert, $Paginas, $Pags->NumRegsIni, $Pags->NumRegsFin, $Pags->NumRegsTotal);
  
  
  // Tabla Principal
  /*echo "
  <br>  
  <table border='".D_BORDES."' cellPadding='3' cellSpacing='0' width='100%'>
    $Buscar
    $Encabezado
    $Registros
    $BaseTabla
  </table>\n  
  <!--<div class='smart-form col col-4'>
    <label class='checkbox'>
      <input name='checkbox' id='checkbox' type='checkbox'>
      <i></i>Alexandra
    </label>
    <label class='checkbox'>
      <input name='checkbox1' id='checkbox1' type='checkbox'>
      <i></i>Alexandra
    </label>
  </div>-->";*/

  echo "
  <!-- Widget ID (each widget will need unique ID)-->
  <div class='jarviswidget jarviswidget-color-darken' id='wid-id-0' data-widget-editbutton='false' data-widget-deletebutton='false'>        
    <!-- widget div-->
    <div>
      <!-- widget edit box -->
      <div class='jarviswidget-editbox'>
        <!-- This area used as dropdown edit box -->     
      </div>      
      <!-- end widget edit box -->
      <!-- widget content -->
      <div class='widget-body no-padding'>         
        <!-- Campos busqueda -->
        <div id='btn_multi' class='hidden'><div class='col-sm-7'>$Buscar</div><div class='col-sm-5 text-align-right'></div></div>
        <table id='datatable_fixed_column' class='table table-striped table-hover no-margin no-padding' width='100%'>
          <thead>
            ".$Filtros."
            ".str_replace("td", "th", $Encabezado)."
          </thead>
          <tbody>          
            ".$Registros."
          </tbody>
         <tfooter>
         $BaseTabla
         </tfooter>
        </table>
        <input type='hidden' id='tot_registros' value='".$Pags->NumRegsTotal."' >
      </div>
      <!-- end widget content -->
    </div>
    <!-- end widget div -->
  </div>
  <!-- end widget -->";
  EscribeJS( );
}


function MuestraTabla($Query, $admin, $p_tabla, $href_link="", $p_ancho="100%") {
  
  // Ejecuta el query
  $Rows = EjecutaQuery($Query);
  $num_registros = CuentaRegistros($Rows);
  
  // Crea el encabezado para la tabla
  $Encabezado = Encabezado($Rows, $admin, $href_link);    
  
  // Lee los registros que regresa el query
  $Registros = Lee_Registros($Rows, $admin, '', '', '', $p_tabla, $href_link);
  Div_Start_Responsive();
  print "
  <div class='row col col-sm-12 text-align-center'>
  <TABLE border='".D_BORDES."' cellPadding='3' cellSpacing='0' width='$p_ancho' id='$p_tabla' class='table table-striped'>
    <thead> 
    <tr>
      $Encabezado 
    </tr>
    </thead>
    <tbody>
     $Registros
    </tbody>
   </table>
   </div>";
  Div_close_Resposive();
  // Opcion de insertar abajo para Listas Editables
  if($admin == TB_LE_INN OR $admin == TB_LE_IND OR $admin == TB_LE_IUN OR $admin == TB_LE_IUD) {
    print "
    <div class='row col col-sm-12 text-align-left'>
      <a class='btn btn-primary' href='javascript:InsertaEnTabla($p_tabla);'><i class='fa fa-plus' title='".ETQ_INSERTAR."'></i>&nbsp;".ETQ_INSERTAR."</a>
    </div>
  <input type='hidden' name='regs_ini_$p_tabla' id='regs_ini_$p_tabla' value=$num_registros>
  <input type='hidden' name='tot_regs_$p_tabla' id='tot_regs_$p_tabla' value=$num_registros>
  <input type='hidden' name='regs_borrar_$p_tabla' id='regs_borrar_$p_tabla' value=''>";
  }
}


class Tabla {

  var $NumCampos   = 0;
  var $Encabezado   = "";
  var $Rows = "";
  var $ColSpan = 0;
  var $Registros = "";
  var $href_insert = "";
  var $href_update = "";
  var $href_delete = "";
  var $href_link = "";
  var $Incluir_ColSpan = "N";
  var $Cambiar_Color = true;
  var $IdTabla = "";

  function __construct($Rows, $admin) {
    $this->admin = $admin;
    // Número de campos del query
    $this->NumCampos = CuentaCampos($Rows);
    $this->Rows = $Rows;
    $this->ColSpan = $this->NumCampos + 2;  
  }
  
  function Abre_Renglon($renglon = 0) {
    
    if(!empty($this->IdTabla))
        $Id_Registro = " id=reg_" . $this->IdTabla . "_" . $renglon;
      else
        $Id_Registro = "";
    
    return "<tr$Id_Registro>\n";  
  }
  
  function Cierra_Renglon() {
    return "</tr>\n";  
  }
  
  function Crea_Celda($valor, $clase_css="", $align="left", $ancho="") {
    $Texto_ColSpan = "";
    if($this->Incluir_ColSpan == "S")
      $Texto_ColSpan = "colspan=$this->ColSpan";
    if(!empty($ancho))
      $ancho = "width='$ancho'";
    return "<td align='$align' $clase_css $Texto_ColSpan $ancho>$valor</td>\n";
  }
 
  /*****************************************************
      Función que crea el encabezado de la tabla leyendo
      el nombre de los campos.
  *****************************************************/
  function Filters() {
    // Inicialización de variables
    $Cont = 1;
    // Crea el encabezado para la tabla leyendo los nombres de los campos (Alias del query)      
    while ($Cont < $this->NumCampos) {
      $enc = NombreCampo($this->Rows,$Cont);
      $align = "left";
      
      $input_filter = "<input type='text' class='form-control' placeholder='".ObtenEtiqueta(27).$enc."' />";
      $this->Filtros .= $this->Crea_Celda($input_filter, "class='css_tabla_encabezado' ", "center");
      $Cont++;
    }
  }
  
  function Encabezado($p_seleccionar=False) {
    // Inicialización de variables
    $Cont = 1;
     
    // Crea el encabezado para la tabla leyendo los nombres de los campos (Alias del query)
    // se lequita onChange='javascript:SelTodoLista();'
    if($p_seleccionar)      
      $this->Encabezado .= $this->Crea_Celda("<div class='checkbox'><label><input class='checkbox style-2' type='checkbox' id='ch_todo' title='".ObtenEtiqueta(ETQ_SEL_TODO)."' onChange='javascript:SelTodoLista();'><span></span> </label></div>", "class='txt-color-white' style='background-color:#0092cd;'", "center");
    while ($Cont < $this->NumCampos) {
      $enc = NombreCampo($this->Rows,$Cont);
      $align = "left";
      
      // Determina la alineacion del encabezado
      if(strpos($enc, '|hidden'))
        $enc = '';
      if(strpos($enc, '|left'))
        $enc = str_replace('|left', '', $enc);
      if(strpos($enc, '|center')) {
        $enc = str_replace('|center', '', $enc);
        $align = "center";
      }
      if(strpos($enc, '|right')) {
        $enc = str_replace('|right', '', $enc);
        $align = "right";
      }
      
      if($Cont==0)
        $style = "style='padding: 16px 10px';";
      else
        $style = "";

      if($Cont==1)
        $data = "data-class='expand'";
      else{
        if($Cont==2 || $Cont == 3)
          $data = "data-hide='phone'";
        else
          $data = "data-hide='phone,tablet'";
      }
      
      $this->Encabezado .= $this->Crea_Celda($enc, "class='txt-color-white' style='background-color:#0092cd;'".$data.$style, $align);
      $Cont++;
    }
  }  
  
  function Obten_Filtros(){
    return $this->Filtros;
  }
  function Obtiene_Encabezado() {
    return $this->Encabezado;
  }    
  
  function Concatena_Encabezado($Valor) {
    $this->Encabezado .= $Valor;
  }  
  
  function Crea_Celdas_Vacias($NumCeldas, $clase_css="") {
    $Vacias = "";
    
    for($Cont = 0; $Cont < $NumCeldas; $Cont++)
     $Vacias .= $this->Crea_Celda("&nbsp;", $clase_css, 'center', '1%');
    
    return $Vacias;
  }

  function Recupera_Registros($p_seleccionar=False) {
    
    for($i = 1; $row = RecuperaRegistro($this->Rows); $i++) {
        $this->Registros .= $this->Abre_Renglon($i);
        $this->Registros .= $this->Split_Registro($row, $i, $p_seleccionar);
        $this->Registros .= $this->Cierra_Renglon();
        $this->Cambiar_Color = !$this->Cambiar_Color;
    }
  }

  function Split_Registro($row, $NumReg, $p_seleccionar=False) {
    // Inicialización de variables
    $Clave ="";
    $Datos_Registro = "";
    $fg_seleccionar= "";
    if($this->Cambiar_Color) {
      $clase_css       = "class='css_tabla_detalle'";
      $clase_icono_css = "class='css_tabla_detalle_ico'";
    }
    else {
      $clase_css = "class='css_tabla_detalle_bg'";
      $clase_icono_css = "class='css_tabla_detalle_ico_bg'";
    }
    
    for($Cont = 1; $Cont < $this->NumCampos; $Cont++) {
      $Campo = NombreCampo($this->Rows, $Cont);
      $Clave = NombreCampo($this->Rows, 0);
      $Valor_Clave = $row[0];
      $Valor_Campo = str_texto($row[$Cont]);
      
      // Determina si se debe elegir un valor por traduccion
      $Valor_Campo = DecodificaEscogeIdiomaBD($Valor_Campo);
      
      // Prepara alineacion del campo
      $align = "left";
      $cadena = $Valor_Campo;
      $cadena = str_replace('$', '0', $cadena);
      $cadena = str_replace(',', '0', $cadena);
      $cadena = str_replace('.', '0', $cadena);
      $cadena = str_replace('%', '0', $cadena);
      if(is_numeric($Valor_Campo))
        $align = "right";
      if((strpos($Valor_Campo, '.')||strpos($Valor_Campo, '%')) AND is_numeric($cadena))
        $align = "right";
      if($Valor_Campo == ETQ_SI || $Valor_Campo == ETQ_NO || $Valor_Campo == '--')
        $align = "center";
      
      // Seleccionar
      if($p_seleccionar)
        $fg_seleccionar = $this->Crea_Celda("<div class='checkbox'><label><input class='checkbox style-2' type='checkbox' id='ch_{$NumReg}' value='$Valor_Clave'><span></span> </label></div>", $clase_icono_css, 'center');      

      // Opcion para campos ocultos
      if(!strpos($Valor_Campo, '|hidden')) {
        
        // Opcion para editar en las celdas en Listas Normales
        if($this->admin == TB_LN_NUN OR $this->admin == TB_LN_NUD OR $this->admin == TB_LN_IUN OR $this->admin == TB_LN_IUD)
          $Datos_Registro .= $this->Crea_Celda("<a href=\"javascript:Envia('$this->href_update', $Valor_Clave);\">$Valor_Campo</a>", $clase_css, $align);
        // Opcion para editar en las celdas en Listas Editables
        elseif($this->admin == TB_LE_NUN OR $this->admin == TB_LE_NUD OR $this->admin == TB_LE_IUN OR $this->admin == TB_LE_IUD) {
          $Datos_Registro .= $this->Crea_Celda("<a href=\"javascript:ActualizaEnTabla('$this->IdTabla', $NumReg);\">$Valor_Campo</a>", $clase_css, $align);
          $Datos_Registro .= "<input type=hidden name='".$this->IdTabla."_".$Cont."_reg_".$NumReg."' id='".$this->IdTabla."_".$Cont."_reg_".$NumReg."' value='$Valor_Campo'>\n";
        }
        else
          $Datos_Registro .= $this->Crea_Celda($Valor_Campo, $clase_css, $align);
      }
      else {
        $Valor_Campo = str_replace('|hidden', '', $Valor_Campo);
        $Datos_Registro .= $this->Crea_Celda('', $clase_css, $align);
        $Datos_Registro .= "<input type=hidden name='".$this->IdTabla."_".$Cont."_reg_".$NumReg."' id='".$this->IdTabla."_".$Cont."_reg_".$NumReg."' value='$Valor_Campo'>\n";
      }
    }
    # Dependiendo del icono que haya mandado
    
    # Por default pondra el money y pdf estos iconos debes estar en images
    # Si la funcion es social sharing en el listado mostrar
    if($this->funcion==126){
      if(!empty($this->icono1))
        $icono_money = $this->icono1;        
      if(!empty($this->icono2))
        $icono_pdf = $this->icono2;
      $share = explode(".",$Valor_Clave);
      $href1 = "href=\"$this->href_link2?clave=$share[0]\"";//money    
      $href2 = "href=\"$this->href_link/".(!empty($share[1])?$share[1]:NULL)."\"";//pdf
    }
    else{
      if(empty($this->icono1) AND empty($this->icono2)){
        $icono_money = "fa-money";
        $icono_pdf = "fa-file-pdf-o";
      }
      else{
        $icono_money = $this->icono1;
        $icono_pdf = $this->icono2;
      }
      $href1 = "href=\"$this->href_link2?clave=$Valor_Clave\"";//money
      $href2 = "href=\"$this->href_link?clave=$Valor_Clave\"";//pdf
    }
   
    //agrega otro link adicional en Listas Normales
    if(!empty($this->href_link2))
      $cash = "<a class='btn btn-xs btn-default' title='".ObtenEtiqueta(580)."'$href1 target='_blank'><i class='fa $icono_money'></i></a>";
    else
      $cash = "";
    // Opcion para Link adicional en Listas Normales
    if(($this->admin == TB_LN_NNN OR $this->admin == TB_LN_NND OR $this->admin == TB_LN_NUN OR $this->admin == TB_LN_NUD OR
        $this->admin == TB_LN_INN OR $this->admin == TB_LN_IND OR $this->admin == TB_LN_IUN OR $this->admin == TB_LN_IUD) AND
       (!empty($this->href_link)))
      $pdf = "<a class='btn btn-xs btn-default' title='pdf' $href2 target='_blank'><i class='fa $icono_pdf'></i></a>";
    else
      $pdf ="";
    
    // Opcion para Link adicional en Listas Editables
    if(($this->admin == TB_LE_NNN OR $this->admin == TB_LE_NND OR $this->admin == TB_LE_NUN OR $this->admin == TB_LE_NUD OR
        $this->admin == TB_LE_INN OR $this->admin == TB_LE_IND OR $this->admin == TB_LE_IUN OR $this->admin == TB_LE_IUD) AND
       (!empty($this->href_link))) {
      $lpos = strpos($this->href_link, '|');
      $rpos = strrpos($this->href_link, '|');
      $func = substr($this->href_link, 0, $lpos);
      $img = PATH_IMAGES."/".substr($this->href_link, $lpos+1, $rpos-$lpos-1);
      $alt = substr($this->href_link, $rpos+1);
      $Datos_Registro .= $this->Crea_Celda("<a href=\"javascript:$func($NumReg);\"><img src='$img' width=17 height=16 border=0 title='$alt'></a>", $clase_icono_css, 'center');
    }
    
    // Columna para editar en Listas Normales
    if($this->admin == TB_LN_NUN OR $this->admin == TB_LN_NUD OR $this->admin == TB_LN_IUN OR $this->admin == TB_LN_IUD)
      $editar_ln = "<a class='btn btn-xs btn-default' title='".ETQ_EDITAR."' href=\"javascript:Envia('$this->href_update', $Valor_Clave);\"><i class='fa fa-pencil'></i></a>";
    else
      $editar_ln = "";
    
    // Columna para borrar en Listas Normales
    if($this->admin == TB_LN_NND OR $this->admin == TB_LN_NUD OR $this->admin == TB_LN_IND OR $this->admin == TB_LN_IUD)
      $borrar_ln = "<a class='btn btn-xs btn-default' title='".ETQ_ELIMINAR."' href=\"javascript:Borra('$this->href_delete', $Valor_Clave);\"><i class='fa  fa-trash-o'></i></a>";
    else
      $borrar_ln = "";
    
    // Columna para editar en Listas Editables
    if($this->admin == TB_LE_NUN OR $this->admin == TB_LE_NUD OR $this->admin == TB_LE_IUN OR $this->admin == TB_LE_IUD)
      $editar_le = "<a class='btn btn-xs btn-default' title='".ETQ_EDITAR."' href=\"javascript:ActualizaEnTabla('$this->IdTabla', $NumReg);\"><i class='fa fa-pencil'></i></a>";
    else
      $editar_le = "";
      
    // Columna para borrar en Listas Editables
    if($this->admin == TB_LE_NND OR $this->admin == TB_LE_NUD OR $this->admin == TB_LE_IND OR $this->admin == TB_LE_IUD)
      $borrar_le = "<a class='btn btn-xs btn-default' title='".ETQ_ELIMINAR."' href=\"javascript:BorraEnTabla('$this->IdTabla', $NumReg);\"><i class='fa  fa-trash-o'></i></a>";
    else
      $borrar_le = "";
    
    # Ponemos en todos los iconos en una sola columna
    # Este width lo utilizamos para el ancho de la columna de los iconos
    $width = 0;
    if(!empty($cash))
      $width ++;
    if(!empty($pdf))
      $width ++;
    if(!empty($editar_ln) || !empty($editar_le))
      $width ++;
    if(!empty($borrar_ln) || !empty($borrar_le))
      $width ++;
      
    $width = (3 * $width)+1;
    
    $Datos_Registro .= $this->Crea_Celda($cash.$pdf.$editar_ln.$borrar_ln.$editar_le.$borrar_le, $clase_icono_css."width='".$width."%'", 'center');
    
    // Campo identificador de registro para editar o borrar en Listas Editables
    if($this->admin == TB_LE_NND OR $this->admin == TB_LE_NUN OR $this->admin == TB_LE_NUD OR
       $this->admin == TB_LE_IND OR $this->admin == TB_LE_IUN OR $this->admin == TB_LE_IUD)
      $Datos_Registro .= "<input type=hidden name='fl_".$this->IdTabla."_".$NumReg."' id='fl_".$this->IdTabla."_".$NumReg."' value='".$Valor_Clave."'>\n";
    
    return $fg_seleccionar.$Datos_Registro;
  }

  function Obtiene_Registros() {
    return $this->Registros;
  }    
  
  function ColSpan($Valor) {
    $this->Incluir_ColSpan = $Valor;
  }     
  function Asigna_Link_Insert($url) {
      $this->href_insert = $url;
  }
  
  function Asigna_Link_Update($url) {
    $this->href_update = $url;
  }
  function Asigna_Link_Delete($url) {
    $this->href_delete = $url;
  }
  function Asigna_Link_Href($url) {
    $this->href_link = $url;
  }
  function Asigna_Link_Href2($url2) {
    $this->href_link2 = $url2;
  }
  function Asigna_icono1($icono1) {
    $this->icono1 = $icono1;
  }
  function Asigna_icono2($icono2) {
    $this->icono2 = $icono2;
  }
  function Asigna_funcion($funcion) {
    $this->funcion = $funcion;
  }
  function Asigna_Id_Tabla($p_tabla) {
    $this->IdTabla =$p_tabla;
  }  
  
}


function RenglonBuscarExport($fg_buscar, $fg_export, $campos, $criterio, $actual, $num_span, $Self, $fg_letter=False, $fg_enroll=False) {

  # Variable initialization to avoid error
  $letter=NULL;
  $enroll=NULL;

# Export
if($fg_export){
  $exportar = "             
  <form name='exportar' method='post' action='".ObtenProgramaNombre(PGM_EXPORT)."'>
    <input type='hidden' name='criterio' value='$criterio'>
    <input type='hidden' name='actual' value='$actual'>          
    <a href='javascript:exportar.submit();' title='".ETQ_EXPORTAR."' class='btn btn-default btn-sm'><i class='fa fa-file-excel-o'></i></a>
  </form>";
}

# Send letter multiple
if($fg_letter){
  /*$letter = "
  <a class='btn btn-default btn-sm' title='".ObtenEtiqueta(844)."'  href='javascript:selected();'><i class='fa fa-envelope-o'></i></a>
  <div class='dialog' id='dialog' title='Email sent' style='display: none; font-size:14px;'>";
    $Query = "SELECT nb_template, fl_template FROM k_template_doc a ";
    $Query .= "WHERE fl_categoria=2 ORDER BY fl_template ASC";
    $rs = EjecutaQuery($Query);
    $letter .= "
    <b>".ObtenEtiqueta(153).":</b> &nbsp;
    <!-- Quitamos onChange='javascript:template(true);' por error java PENDIENTE-->
    <select name='fl_template' id='fl_template'>
      <option value='0'>--Select Option---</option>";
    for($i=0;$row=RecuperaRegistro($rs);$i++){
      $letter .= "
      <option value='".$row[1]."'>".$row[0]."</option>";
    }
  $letter .= "
    </select>
    <div id='ds_mensaje'>&nbsp;</div>
    <input type='hidden' id='multiple' value='true'>
  </div>";*/
  $letter = "
  <!--<a class='btn btn-default btn-sm' title='".ObtenEtiqueta(844)."'  href='javascript:selected();'><i class='fa fa-envelope-o'></i></a>-->
  <a href='javascript:selected();' class='btn btn-default btn-sm' title='".ObtenEtiqueta(844)."'><i class='fa fa-send-o'></i>&nbsp;".ObtenEtiqueta(844)."</a>
  <input type='hidden'  id='multiple' value='true'>";
}

# Enroll Student mulitple
if($fg_enroll){
  $enroll = "<a href='javascript:enrollstudent();' class='btn btn-default btn-sm' title='".ObtenEtiqueta(345)."'><i class='fa fa-child'></i>&nbsp;".ObtenEtiqueta(345)."</a>";
}

# Buscar
if($fg_buscar){
  $search = "
  <form action='$Self' method='post' name='Search' class='smart-form'>
  <section class='col col-2'>
    <label class='input'>
      <input type='text' name='criterio' value='$criterio' placeholder='".ETQ_BUSCAR."' >
    </label>
  </section>
  <section class='col col-2'>
    <label class='select'>
      <select name='country'>
        <option value=0>".ETQ_TODOS_CAMPOS."</option>
        ".ArmaCamposBusqueda($campos, $actual)."
      </select> <i></i> 
    </label>
  </section>
  <section class='col col-2'>
    <button name='btn_search' title='".ETQ_EJECUTAR."' class='btn btn-primary btn-sm'><i class='fa fa-search'></i></button>
    <a href='$Self' title='".ETQ_LIMPIAR."' class='btn btn-primary btn-sm' style='color:#FFFFFF;'><i class='fa fa-refresh'></i></a>
  </form>";
}

# Prepara los campos de busqueda
$Buscar = "		  
<!--<fieldset>      
  <div class='row'>-->
    ".$letter."
    ".$enroll."
  <!--</div>
</fieldset>-->";
  
  return $Buscar;
}

function Filters($Rows, $fg_seleccionar=False){
  $Filtros = new Tabla($Rows, $admin);
  $Filtros->Filters();
  if($fg_seleccionar)
    $th = "<th>&nbsp;</th>";
  return "<tr>".$th.$Filtros->Obten_Filtros()."<th>&nbsp;</th></tr>";
}

function Encabezado($Rows, $admin, $href_link = "", $href_link2 = "",$icono1="",$icono2="", $funcion=0, $p_seleccionar=False) {
  $Encabezado = new Tabla($Rows, $admin);
  $Encabezado->Encabezado($p_seleccionar);
  $vacias = 0;
  
  // Opcion para poner un link adicional
  if(!empty($href_link))
    $vacias++;
  if(!empty($href_link2))
    $vacias++;
  if(!empty($icono1))
    $vacias++;
  if(!empty($icono2))
    $vacias++;
  if(!empty($funcion))
    $vacias++;
  
  // Columna para editar en Listas Normales o Editables
  if($admin == TB_LN_NUN OR $admin == TB_LN_NUD OR $admin == TB_LN_IUN OR $admin == TB_LN_IUD OR
     $admin == TB_LE_NUN OR $admin == TB_LE_NUD OR $admin == TB_LE_IUN OR $admin == TB_LE_IUD)
    $vacias++;
  
  // Columna para borrar en Listas Normales o Editables
  if($admin == TB_LN_NND OR $admin == TB_LN_NUD OR $admin == TB_LN_IND OR $admin == TB_LN_IUD OR
     $admin == TB_LE_NND OR $admin == TB_LE_NUD OR $admin == TB_LE_IND OR $admin == TB_LE_IUD)
    $vacias++;
  
  // Crea celdas vacias en renglon de encabezado de tabla
  if($vacias > 0)
    $Encabezado->Concatena_Encabezado($Encabezado->Crea_Celdas_Vacias(1, "style='background-color:#0092cd;' width='10%;'"));
  
  return "<tr>\n".$Encabezado->Obtiene_Encabezado()."</tr>";
}


function Lee_Registros($Rows, $admin, $href_insert, $href_update, $href_delete, $p_tabla, $href_link = '', $href_link2 = '',$icono1="",$icono2="",$funcion=0, $p_seleccionar=False) {
  
  $Registros = new Tabla($Rows, $admin);
  $Registros->Asigna_Link_Insert($href_insert);
  $Registros->Asigna_Link_Update($href_update);  
  $Registros->Asigna_Link_Delete($href_delete);  
  $Registros->Asigna_Id_Tabla($p_tabla);  
  $Registros->Asigna_Link_Href2($href_link2);  
  $Registros->Asigna_Link_Href($href_link);  
  $Registros->Asigna_icono1($icono1);  
  $Registros->Asigna_icono2($icono2);   
  $Registros->Asigna_funcion($funcion);   
  $Registros->Recupera_Registros($p_seleccionar);
    
  return $Registros->Obtiene_Registros( );
}


function RenglonBase($num_span, $admin, $href_insert, $Paginas, $RegIni, $RegFin, $NumRegsTotal) {
  # Base de la tabla
  $num_span = $num_span + 2;
  $BaseTabla = "
    <tr>
      <td colspan='$num_span'>";
      // Opcion de insertar abajo para Listas Normales
      if($admin == TB_LN_INN OR $admin == TB_LN_IND OR $admin == TB_LN_IUN OR $admin == TB_LN_IUD){
        //$BaseTabla .= "
        //<a href=\"javascript:Envia('$href_insert', '');\" class='btn btn-primary btm-sm' title='".ETQ_INSERTAR."' style='color:#FFFFFF;'><i class='fa fa-plus'></i> ".ETQ_INSERTAR."</a>";
        $BaseTabla .= "
        <div style='width: 228px; right: 0px; display: block; padding:0px 50px 10px 0px;' outline='0' class='ui-widget ui-chatbox'>
          <a href=\"javascript:Envia('$href_insert', '');\" class='btn btn-primary btn-circle btn-xl' title='".ETQ_INSERTAR."' style='color:#FFFFFF;'><i class='fa fa-plus'></i></a>
        </div>";
      }
      else
        $BaseTabla .= "&nbsp;";
  $BaseTabla .= " 
      </td>
    </tr>";
  return $BaseTabla;
}


function EscribeJS( ) {
  echo "
	<script>
		function Envia(url, valor) {
			document.parametros.clave.value  = valor;
			document.parametros.action = url;
			document.parametros.submit();
		}
    function Borra(url, valor) {
      var answer = confirm('".str_ascii(ObtenMensaje(MSG_ELIMINAR))."');
      if(answer) {
        document.parametros.clave.value  = valor;
        document.parametros.action = url;
        document.parametros.submit();
      }
		}
    function Paginacion(PagActual) {
      document.paginar.PagActual.value = PagActual;
      document.paginar.submit();
    }
    
    // Listados: Seleccionar todo
    function SelTodoLista( ) {
      var i, tot_registros = $('#tot_registros').val();      
      for(i = 0;  i <= tot_registros; i++) {
        $('#ch_'+i).prop('checked', $('#ch_todo').is(':checked'));
      }
    }
    
    function selected(){
      var i, tot_registros = $('#tot_registros').val(),seleccionados=0;     
      for(i = 0; i <= tot_registros; i++) {        
        if($('#ch_'+i).is(':checked')) {
          seleccionados = seleccionados + 1;          
        }        
      }      

      if(seleccionados == 0){
        $('#vanas_preloader').show();
        $('#no_select').show();
        $('#btn_noselect').click(function(){
          $('#vanas_preloader').hide();
          $('#no_select').hide();
        });
      }
      else{
        showDialog();
        $('#vanas_preloader').css('z-index','1000');
      }
    }
    
    function enrollstudent(){
      var i, tot_registros = $('#tot_registros').val(),seleccionados=0;      
      for(i = 0; i <= tot_registros; i++) {
        if($('#ch_'+i).is(':checked')) {
          seleccionados = seleccionados + 1;
        }
      }      
      
      // Indica si no hay aplicantes seleccionados
      if(seleccionados == 0){
        $('#vanas_preloader').show();
        $('#no_select').show();
        $('#btn_noselect').click(function(){
          $('#vanas_preloader').hide();
          $('#no_select').hide();
        });
      }
      else{
        // Si confirma si desea convertir los aplicantes a students
        var applicantes = 0, convertido = 0, no_convertido = 0, send_email;
        $('#vanas_preloader').show();
        $('#enroll_confirmation').show();
        $('#enroll1').show();        
        $('#si1').show();        
        $('#no1').show();        
        $('#si1').click(function(){
          $('#enroll1').hide();
          $('#si1').hide();
          $('#no1').hide();
          $('#enroll2').show();
          $('#si2').show();
          $('#no2').show();
          $('#si2').click(function(){
            enroll_std(1);
          });
          $('#no2').click(function(){
            enroll_std(0);
          });
        });
        
        $('#no1').click(function(){
          location.reload();
        });
        
        function enroll_std(send_email){
          for(i = 0; i <= tot_registros; i++) {
            if($('#ch_'+i).is(':checked')) {
              // Envia los datos para que envie el correo y guarde el registro
              $.ajax({
                type: 'POST',
                url : '../../modules/campus/enroll_student.php',
                data: 'fl_sesion='+$('#ch_'+i).val()+'&fg_sendemail='+send_email,
                async: false,
                success: function(html){
                  // Si recibe uno lo convirtio
                  if(html == '1'){
                    convertido++;
                  }
                  else{
                    no_convertido++;
                  }
                }
              });
              // Muestra proceso
              applicantes++;
              progress(applicantes, seleccionados, convertido, no_convertido);
            }
          }
        }
      }
    }
     
	</script>
  <script>
  // Funcion para mostra la barra de proceso
    function progress(p_valores=0, p_totales=0, p_convertidos = '', p_noconvertidos = ''){      
      if(p_valores > 0 && p_totales > 0){        
        // Width y contenido para el div myBar
        var valor = (100/p_totales);
        if(p_valores>1){
          valor = valor;
        }        
        // Muestra la barra de proceso
        $('#enroll_confirmation').hide();
        $('#vanas_preloader').show();
        $('#preloader').show();
        //Valores en el div
        // $('#preloader').css('height','20%');
        $('#myProgress').css('width',80 + '%');
        $('#myBar').html(p_valores + '/' + p_totales + '&nbsp;sent');
        $('#myBar').css('width',valor*p_valores + '%');

        // Mensajes
        if(p_valores<p_totales)
          $('#seleccionados').html('(' + p_totales + ') ".ObtenEtiqueta(848)."');
        else{
          $('#seleccionados').html('(' + p_totales + ') ".ObtenEtiqueta(849)."');
          if(p_convertidos != '' || p_noconvertidos != ''){
            // $('#preloader').css('height','25%'); 
            $('#complet').html('".ObtenEtiqueta(850).": ' + p_convertidos);
            $('#nocomplet').html('".ObtenEtiqueta(851).": ' + p_noconvertidos);            
            $('#condiciones').html('".ObtenEtiqueta(852)."');
            // $('#ok').css('padding-top','50%');
          }            
        }
        $('#ok').show();
      }
    }
  </script>
  
  
  <form name=parametros method=post>
    <input type=hidden name=clave>
  </form>\n";
}

?>