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
  
  function Paginacion($Query, $NumRegsPag, $PagActual, $Self, $Criterio, $Actual) {
    
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
  
  function Datos_Paginacion() {
    
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


function PresentaListado($Query, $admin=TB_LN_IUD, $fg_buscar=False, $fg_export=False, $campos=array(), $href_link = "", $href_link2 = "",$icono1="", $icono2="",$funcion, $p_seleccionar=False, $p_letter=False, $p_enroll=False) {

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
  $num_span = CuentaCampos($Rows) + 2;
  $Pags = new Paginacion($Query, $NumRegsPag, $PagActual, $Self, $criterio, $actual);
  
  // Prepara renglon para busqueda y exportacion a Excel
  $Buscar = RenglonBuscarExport($fg_buscar, $fg_export, $campos, $criterio, $actual, $num_span, $Self, $p_letter, $p_enroll);
  // Crea el encabezado para la tabla
  $Encabezado = Encabezado($Rows, $admin, $href_link, $href_link2, $icono1,$icono2,$funcion,$p_seleccionar);
  
  // Lee los registros que regresa el query
  $Registros = Lee_Registros($Rows, $admin, $href_insert, $href_update, $href_delete, '', $href_link, $href_link2, $icono1,$icono2,$funcion, $p_seleccionar);
  
  // Arma ligas para paginado
  $Paginas = $Pags->Link_Paginas( );
  
  // Prepara renglon con opcion nuevo, paginacion y total de registros
  $BaseTabla = RenglonBase($num_span, $admin, $href_insert, $Paginas, $Pags->NumRegsIni, $Pags->NumRegsFin, $Pags->NumRegsTotal);
  
  // Tabla Principal
  echo "
  <br>
  <table border='".D_BORDES."' cellPadding='3' cellSpacing='0' width='100%'>
    $Buscar
    $Encabezado
    $Registros
    $BaseTabla
  </table>\n";
  
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
  
  print "
  <TABLE border='".D_BORDES."' cellPadding='3' cellSpacing='0' width='$p_ancho' id='$p_tabla'>
     <tr>
       $Encabezado 
     </tr>
     $Registros
   </table>
";
  
  // Opcion de insertar abajo para Listas Editables
  if($admin == TB_LE_INN OR $admin == TB_LE_IND OR $admin == TB_LE_IUN OR $admin == TB_LE_IUD) {
    print "
  <TABLE border='".D_BORDES."' cellPadding='3' cellSpacing='0' width='100%'>
     <tr>
       <td class='css_default'><a href='javascript:InsertaEnTabla($p_tabla);'><img src='".PATH_IMAGES."/".IMG_NUEVO."' align=top valign=top width=17 height=16 border=0 title='".ETQ_INSERTAR."'> ".ETQ_INSERTAR."</a></td>
     </tr>
  </table>
  <input type='hidden' name='regs_ini_$p_tabla' id='regs_ini_$p_tabla' value=$num_registros>
  <input type='hidden' name='tot_regs_$p_tabla' id='tot_regs_$p_tabla' value=$num_registros>
  <input type='hidden' name='regs_borrar_$p_tabla' id='regs_borrar_$p_tabla' value=''>
";
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

  function Tabla($Rows, $admin) {
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
    return "<td align=$align $clase_css $Texto_ColSpan $ancho>$valor</td>\n";
  }
 
  /*****************************************************
      Función que crea el encabezado de la tabla leyendo
      el nombre de los campos.
  *****************************************************/
  function Encabezado($p_seleccionar=False) {
    // Inicialización de variables
    $Cont = 1;
    
    // Crea el encabezado para la tabla leyendo los nombres de los campos (Alias del query)
    if($p_seleccionar)
      $this->Encabezado .= $this->Crea_Celda("<input type='checkbox' id='ch_todo' onChange='javascript:SelTodoLista();' title='".ObtenEtiqueta(ETQ_SEL_TODO)."'>", "class='css_tabla_encabezado'", "center");
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
      $this->Encabezado .= $this->Crea_Celda($enc, "class='css_tabla_encabezado'", $align);
      $Cont++;
    }
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
        $fg_seleccionar = $this->Crea_Celda("<input type='checkbox' id='ch_{$NumReg}' value='$Valor_Clave'></input>", $clase_icono_css, 'center');

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
      $href2 = "href=\"$this->href_link/$share[1]\"";//pdf
    }
    else{
      if(empty($this->icono1) AND empty($this->icono2)){
        $icono_money = "money.png";
        $icono_pdf = IMG_PDF;
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
      $Datos_Registro .= $this->Crea_Celda("<a $href1 target='_blank'><img src='".PATH_IMAGES."/".$icono_money."' width=16 height=16 border=0 title='".ObtenEtiqueta(580)."'></a>", $clase_icono_css, 'center');
    // Opcion para Link adicional en Listas Normales
    if(($this->admin == TB_LN_NNN OR $this->admin == TB_LN_NND OR $this->admin == TB_LN_NUN OR $this->admin == TB_LN_NUD OR
        $this->admin == TB_LN_INN OR $this->admin == TB_LN_IND OR $this->admin == TB_LN_IUN OR $this->admin == TB_LN_IUD) AND
       (!empty($this->href_link)))
      $Datos_Registro .= $this->Crea_Celda("<a $href2 target='_blank'><img src='".PATH_IMAGES."/".$icono_pdf."' width=16 height=16 border=0 title='".ETQ_IMPRIMIR."'></a>", $clase_icono_css, 'center');
    
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
      $Datos_Registro .= $this->Crea_Celda("<a href=\"javascript:Envia('$this->href_update', $Valor_Clave);\"><img src='".PATH_IMAGES."/".IMG_EDITAR."' width=17 height=16 border=0 title='".ETQ_EDITAR."'></a>", $clase_icono_css, 'center');
    
    // Columna para borrar en Listas Normales
    if($this->admin == TB_LN_NND OR $this->admin == TB_LN_NUD OR $this->admin == TB_LN_IND OR $this->admin == TB_LN_IUD)
      $Datos_Registro .= $this->Crea_Celda("<a href=\"javascript:Borra('$this->href_delete', $Valor_Clave);\"><img src='".PATH_IMAGES."/".IMG_BORRAR."' width=17 height=16 border=0 title='".ETQ_ELIMINAR."'></a>", $clase_icono_css, 'center');
    
    // Columna para editar en Listas Editables
    if($this->admin == TB_LE_NUN OR $this->admin == TB_LE_NUD OR $this->admin == TB_LE_IUN OR $this->admin == TB_LE_IUD)
      $Datos_Registro .= $this->Crea_Celda("<a href=\"javascript:ActualizaEnTabla('$this->IdTabla', $NumReg);\"><img src='".PATH_IMAGES."/".IMG_EDITAR."' width=17 height=16 border=0 title='".ETQ_EDITAR."'></a>", $clase_icono_css, 'center');
    
    // Columna para borrar en Listas Editables
    if($this->admin == TB_LE_NND OR $this->admin == TB_LE_NUD OR $this->admin == TB_LE_IND OR $this->admin == TB_LE_IUD)
      $Datos_Registro .= $this->Crea_Celda("<a href=\"javascript:BorraEnTabla('$this->IdTabla', $NumReg);\"><img src='".PATH_IMAGES."/".IMG_BORRAR."' width=17 height=16 border=0 title='".ETQ_ELIMINAR."'></a>", $clase_icono_css, 'center');
    
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
  
  // Prepara celda para exportacion
  if($fg_export)
    $exportar = "
      <table border='".D_BORDES."' cellpadding=0 cellspacing=0 width='100%' class='css_default'>
      <form name='exportar' method='post' action='".ObtenProgramaNombre(PGM_EXPORT)."'>
      <input type='hidden' name='criterio' value='$criterio'>
      <input type='hidden' name='actual' value='$actual'>
      <tr>
        <td align='right'><a href='javascript:exportar.submit();'>".ETQ_EXPORTAR."</a></td>
        <td width='20' align='right'><input type=image name='btn_export' src='".PATH_IMAGES."/".IMG_EXCEL."' border=0 title='".ETQ_EXPORTAR."'></td>
      </tr>
      </form>
      </table>\n";
  else
    $exportar = "&nbsp;";
  
  // Prepara celda de busqueda
  if($fg_buscar) {
    $opciones = ArmaCamposBusqueda($campos, $actual);
    $Buscar = "
  <tr>
    <td colspan='$num_span'>
      <table border='".D_BORDES."' width=100% cellPadding=2 cellSpacing=0 class='css_default'>
        <form action='$Self' method='post' name='Search'>
        <tr>
          <td class='css_prompt'>".ETQ_BUSCAR.":</td>
          <td><input type='text' maxlength='60' size='30' class='css_input' name='criterio' value='$criterio'></td>
          <td class='css_prompt'>".ETQ_BUSCAR_EN.":</td>
          <td>
          <select name='actual' class='css_default'>
            <option value=0>".ETQ_TODOS_CAMPOS."</option>
            $opciones
          </select>
          </td>
          <td><input type=image name='btn_search' src='".PATH_IMAGES."/".IMG_BUSCAR."' width=24 height=24 border=0 title='".ETQ_EJECUTAR."'></td>
          <td>&nbsp;</td>
          <td><a href='$Self'><img src='".PATH_IMAGES."/".IMG_LIMPIAR."' width=24 height=24 border='0' title='".ETQ_LIMPIAR."'></a></td>
          </form>";
      # Muestra el icono de send letters
      if($fg_letter){
        $Buscar .= "
          <td>&nbsp;</td>
          <td id='seleccionar_td'>
            <a href='javascript:selected();'><img src='".PATH_IMAGES."/send_message.png' width=20 height=20 border='0' title='".ObtenEtiqueta(844)."' ></a>
            <div  class='dialog' id='dialog' title='Email sent' style='display: none; font-size:14px;'>";
            $Query = "SELECT nb_template, fl_template FROM k_template_doc a ";
            $Query .= "WHERE fl_categoria=2 ORDER BY fl_template ASC";
            $rs = EjecutaQuery($Query);
            $Buscar .= "
            <b>".ObtenEtiqueta(153).":</b> &nbsp;
            <select name='fl_template' id='fl_template' onChange='javascript:template(true);'>
              <option value='0'>--Select Option---</option>";
              for($i=0;$row=RecuperaRegistro($rs);$i++){
              $Buscar .= "
                <option value='".$row[1]."'>".$row[0]."</option>";
              }
            $Buscar .= "</select>
            <div id='ds_mensaje'></div>
            </div>
            </td>";
      }
      # Muestra el icono de enroll student
      if($fg_enroll){
        $Buscar .= "
            <td>&nbsp;</td>
            <td id='enroll_student_td'>
              <a href='javascript:enrollstudent();'><img src='".PATH_IMAGES."/enroll_student.png' width=20 height=20 border='0' title='".ObtenEtiqueta(345)."' ></a>
            <td>";
      }
          $Buscar .= "
          <td width=60% align='right'>$exportar</td>
        </tr>        
      </table>
    </td>
  </tr>\n";
  }
  else
    $Buscar = "
  <tr>
    <td colspan='$num_span' align='right'>$exportar</td>
  </tr>\n";
  
  return $Buscar;
}


function Encabezado($Rows, $admin, $href_link = "", $href_link2 = "",$icono1="",$icono2="", $funcion, $p_seleccionar=False) {
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
    $Encabezado->Concatena_Encabezado($Encabezado->Crea_Celdas_Vacias($vacias, "class='css_tabla_encabezado'"));
  
  return "<tr>\n".$Encabezado->Obtiene_Encabezado( )."</tr>";
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
  
  $BaseTabla = "
  <tr>
    <td colspan='$num_span' class='css_default'>
      <table border='".D_BORDES."' width=100% cellpadding=0 cellspacing=0>
        <tr>
          <td class='css_default' align='left' width='15%'>";
  
  // Opcion de insertar abajo para Listas Normales
  if($admin == TB_LN_INN OR $admin == TB_LN_IND OR $admin == TB_LN_IUN OR $admin == TB_LN_IUD)
    $BaseTabla .= "<a href=\"javascript:Envia('$href_insert', '');\"><img src='".PATH_IMAGES."/".IMG_NUEVO."' align=top valign=top width=17 height=16 border=0 title='".ETQ_INSERTAR."'> ".ETQ_INSERTAR."</a>";
  else
    $BaseTabla .= "&nbsp;";
  
  $BaseTabla .= "</td>
          <td class='css_default' align='center' width='70%'>$Paginas</td>
          <td class='css_default' align='right' width='15%'>&nbsp;</td>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td colspan='$num_span' class='css_default' align='center'>";
  if($NumRegsTotal > 1)
    $BaseTabla .= ETQ_MOSTRANDO." <b>$RegIni-$RegFin</b> ".ETQ_DE." <b>$NumRegsTotal</b> ".ETQ_REGISTROS;
  if($NumRegsTotal == 1)
    $BaseTabla .= "<b>$NumRegsTotal</b> ".ETQ_REGISTRO;
  if($NumRegsTotal == 0)
    $BaseTabla .= "<b>$NumRegsTotal</b> ".ETQ_REGISTROS;
  $BaseTabla .= "<input type='hidden' id='tot_registros' name='tot_registros' value='{$NumRegsTotal}'></td>
  </tr>\n";
  
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
        $('#ch_'+i).attr('checked', $('#ch_todo').is(':checked'));
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
      else
        showDialog();
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
    function progress(p_valores = 0, p_totales = 0, p_convertidos = '', p_noconvertidos = ''){      
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