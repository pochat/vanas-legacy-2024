<?php
  
  # Libreria de funciones
  include_once("general.inc.php");
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  $fl_usuario = ValidaSesion( );
  
  # Recupera los parametros	
  $str_filtro = $_REQUEST['str_filtro'];
  $lov 		    = $_REQUEST['lov'];
  $val_ini    = $_REQUEST['val_ini'];
  $tipo       = $_REQUEST['tipo'];
  $cond       = $_REQUEST['cond'];
  $msg        = "";
  
  # Arma filtro para busqueda
  $filtro = "";
  if(strlen($str_filtro) > 0)
    $filtro = "'%".$str_filtro."%'";
  
  
  # Perfiles de Administracion
  if($lov == LOV_PERFILES) {
    $cols = array(ObtenEtiqueta(110), ETQ_DESCRIPCION);
    $align = array("left", "left");
    $query  = "SELECT fl_perfil, nb_perfil, ds_perfil ";
    $query .= "FROM c_perfil ";
    $query .= "WHERE fg_admon='1' ";
    if(!empty($filtro))
      $query .= "AND (nb_perfil LIKE $filtro OR ds_perfil LIKE $filtro) ";
    $query .= "ORDER BY fg_admon DESC, nb_perfil";
  }
  
  # Usuarios de Administracion, restinge a los perfiles en $cond
  if($lov == LOV_USUARIOS) {
    $cols = array(ETQ_USUARIO, ETQ_NOMBRE, ObtenEtiqueta(110));
    $align = array("left", "left", "left");
    $concat = array('ds_nombres', "' '", 'ds_apaterno');
    $query  = "SELECT fl_usuario, ds_login, ".ConcatenaBD($concat)." 'nb_usuario', nb_perfil ";
    $query .= "FROM c_usuario a, c_perfil b ";
    $query .= "WHERE a.fl_perfil = b.fl_perfil ";
    $query .= "AND fl_usuario > ".ADMINISTRADOR." ";
    $query .= "AND fg_admon='1' ";
    if(!empty($cond))
      $query .= "AND a.fl_perfil IN($cond) ";
    if(!empty($filtro)) {
      $query .= "AND (nb_perfil LIKE $filtro ";
      $query .= "OR ds_login LIKE $filtro ";
      $query .= "OR ds_nombres LIKE $filtro ";
      $query .= "OR ds_apaterno LIKE $filtro) ";
    }
    $query .= "ORDER BY fg_admon DESC, nb_perfil, ds_login";
  }
  
  # Secciones para agregar contenidos
  if($lov == LOV_SECCIONES) {
    $fl_usuario = ObtenUsuario( );
    $cols = array(ObtenEtiqueta(154), ObtenEtiqueta(164), ETQ_DESCRIPCION);
    $align = array("left", "left", "left");
    $query  = "SELECT fl_funcion, ".EscogeIdioma('nb_funcion', 'tr_funcion').", ".EscogeIdioma('nb_modulo', 'tr_modulo').", ds_funcion ";
    $query .= "FROM c_funcion a, c_modulo b, k_flujo_nivel c ";
    $query .= "WHERE a.fl_modulo=b.fl_modulo ";
    $query .= "AND a.fl_flujo=c.fl_flujo ";
    $query .= "AND c.no_nivel=1 ";
    $query .= "AND a.cl_tipo_contenido <> '".TC_PROGRAMA."' ";
    if($fl_usuario != ADMINISTRADOR) {
      $query .= "AND (";
      $query .= "EXISTS(SELECT 1 FROM k_nivel_usuario WHERE fl_flujo=c.fl_flujo AND no_nivel=1 AND fl_usuario=$fl_usuario) ";
      $query .= "OR EXISTS(SELECT 1 FROM k_nivel_perfil WHERE fl_flujo=c.fl_flujo AND no_nivel=1 AND fl_perfil=".ObtenPerfil($fl_usuario).")";
      $query .= ") ";
    }
    if(!empty($cond))
      $query .= "AND cl_tipo_contenido IN($cond) ";
    if(!empty($filtro)) {
      $query .= "AND (nb_modulo LIKE $filtro OR tr_modulo LIKE $filtro ";
      $query .= "OR nb_funcion LIKE $filtro OR tr_funcion LIKE $filtro OR ds_funcion LIKE $filtro) ";
    }
    $query .= "ORDER BY b.fl_modulo_padre, b.no_orden, a.no_orden";
  }
  
  # Templates, considera los asociados al tipo de contenido de una funcion en $cond, si esta vacia no regresa nada
  if($lov == LOV_TEMPLATES) {
    $cols = array(ObtenEtiqueta(153), ETQ_DESCRIPCION);
    $align = array("left", "left");
    $query  = "SELECT a.cl_template, nb_template, ".EscogeIdioma('ds_template', 'tr_template')." ";
    $query .= "FROM c_template a ";
    if(!empty($cond)) {
      $query .= "WHERE EXISTS(";
      $query .= "SELECT 1 FROM k_tipo_contenido_template b, c_funcion c ";
      $query .= "WHERE b.cl_tipo_contenido=c.cl_tipo_contenido ";
      $query .= "AND b.cl_template=a.cl_template ";
      $query .= "AND c.fl_funcion=$cond";
      $query .= ") ";
    }
    else
      $query .= "WHERE 1=2 ";
    if(!empty($filtro)) {
      $query .= "AND (nb_template LIKE $filtro OR ds_template LIKE $filtro OR tr_template LIKE $filtro) ";
    }
    $query .= "ORDER BY nb_template";
  }
  
  # Menus
  if($lov == LOV_MENUS) {
    $cols = array(ObtenEtiqueta(160), ETQ_DESCRIPCION);
    $align = array("left", "left");
    $query  = "SELECT fl_modulo, ".EscogeIdioma('nb_modulo','tr_modulo').", ds_modulo ";
    $query .= "FROM c_modulo ";
    $query .= "WHERE fl_modulo_padre IS NULL ";
    $query .= "AND fg_admon='0' ";
    if(!empty($filtro))
      $query .= "AND (nb_modulo LIKE $filtro OR tr_modulo LIKE $filtro OR ds_modulo LIKE $filtro) ";
    $query .= "ORDER BY no_orden";
  }
  
  # Submenus, considera los hijos del menu base en $cond, si esta vacia no regresa nada
  if($lov == LOV_SUBMENUS) {
    $cols = array(ObtenEtiqueta(164), ETQ_DESCRIPCION);
    $align = array("left", "left", "left");
    $query  = "SELECT fl_modulo, ".EscogeIdioma('nb_modulo','tr_modulo').", ds_modulo ";
    $query .= "FROM c_modulo ";
    if(!empty($cond))
      $query .= "WHERE fl_modulo_padre=$cond ";
    else
      $query .= "WHERE 1=2 ";
    if(!empty($filtro)) {
      $query .= "AND (nb_modulo LIKE $filtro OR ds_modulo LIKE $filtro) ";
    }
    $query .= "ORDER BY no_orden";
  }
  
  # Maestros
  if($lov == LOV_MAESTROS) {
    $cols = array(ObtenEtiqueta(421), ETQ_NOMBRE);
    $align = array("left", "left");
    $concat = array('ds_nombres', "' '", 'ds_apaterno');
    $query  = "SELECT fl_usuario, ds_login, ".ConcatenaBD($concat)." 'nb_usuario' ";
    $query .= "FROM c_usuario ";
    $query .= "WHERE fl_perfil=".PFL_MAESTRO." ";
    if(!empty($filtro)) {
      $query .= "AND (ds_login LIKE $filtro ";
      $query .= "OR ds_nombres LIKE $filtro ";
      $query .= "OR ds_apaterno LIKE $filtro) ";
    }
    $query .= "ORDER BY ds_login";
  }
  
  # Paginas Fijas
  if($lov == LOV_PAGINAS) {
    $cols = array(ObtenEtiqueta(270), ETQ_DESCRIPCION, ETQ_CLAVE);
    $align = array("left", "left", "right");
    $query  = "SELECT cl_pagina, nb_pagina, ds_pagina, cl_pagina ";
    $query .= "FROM c_pagina ";
    $query .= "WHERE fg_fijo='0' ";
    if(!empty($filtro))
      $query .= "AND (nb_pagina LIKE $filtro OR ds_pagina LIKE $filtro OR cl_pagina LIKE $filtro) ";
    $query .= "ORDER BY cl_pagina";
  }
  
  
  #
  # Despliega el LOV seleccionado
  #
  
  # Presenta titulos
  echo "
  <table id='contLista' align='center' width='100%'>
    <tr id='trTitulos'>
      <td width='10%' nowrap='nowrap' align='center'>Sel</td>\n";
  $tot_cols = count($cols);
  $tot_span = $tot_cols + 1;
  for($i = 0; $i < $tot_cols; $i++)
    echo "      <td nowrap='nowrap' align='$align[$i]'>$cols[$i]</td>\n";
  echo "    </tr>
    <tr><td colspan='$tot_span'><hr></td></tr>\n";
  
  # Determina si se selecciona un solo valor de la LOV o mas
  if($tipo == LOV_TIPO_CHKBOX)
    $str_tipo = "checkbox";
  else
    $str_tipo = "radio";
  
  # Separa los valores que estaban seleccionados antes de abrir el LOV en un arreglo para marcarlos por omision
  if(!empty($val_ini)) {
    $val = explode(",", $val_ini);
    $veces = count($val);
  }
  else
    $veces = 0;
  
  # Recupera los datos
  $rs = EjecutaQuery($query);
  for($tot_rows = 0; $row = RecuperaRegistro($rs); $tot_rows++) {
    echo "    <tr>
      <td nowrap='nowrap' align='center'><input name='selLov' type='$str_tipo' value='$row[0]^".DecodificaEscogeIdiomaBD($row[1])."'";
    for($i = 0; $i < $veces; $i++)
      if($val[$i] == $row[0])
        echo " checked";
    echo "></td>\n";
    for($i = 0; $i < $tot_cols; $i++)
      echo "      <td nowrap='nowrap' align='$align[$i]'>".DecodificaEscogeIdiomaBD($row[$i+1])."</td>\n";
    echo "    </tr>\n";
  }
  if($tot_rows == 0)
    echo "    <tr><td colspan='$tot_span'><b>0 ".ETQ_REGISTROS."<br><br>$msg</b></td></tr>\n";
  echo "</table>";
  
?>