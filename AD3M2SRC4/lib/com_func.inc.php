<?php

# Definicion de constantes y funciones para interfase grafica
require_once('com_config.inc.php');
require_once('adodb/adodb.inc.php');
require_once('sha256/sha256.inc.php');

#
# MRA: Funciones para el manejo de base de datos
#

# Inicia conexion a la base de datos
function ConectaBD( ) {

  $db = &ADONewConnection(DATABASE_TYPE);
  $db->setCharset('utf8');
	$SQL = "SET
    	character_set_results    = 'utf8mb4',
    	character_set_client     = 'utf8mb4',
    	character_set_connection = 'utf8mb4',
    	character_set_database   = 'utf8mb4',
    	character_set_server     = 'utf8mb4'";
  $db->execute($SQL);
  $db->debug = D_DEBUG_ADO;
  if(!DATABASE_FG_DSN)
    $db->Connect(DATABASE_SERVER, DATABASE_USER, DATABASE_PWD, DATABASE_NAME);
  else
    $db->Connect(DATABASE_DSN, DATABASE_USER, DATABASE_PWD);
  $err_no = $db->ErrorNo( );
  if(!empty($err_no)) {
    echo "Data base connection error $err_no - ".$db->ErrorMsg( );
    exit;
  }
  return $db;
}

# Ejecuta una consulta, para usar cuando se espera mas de 1 resultado
function EjecutaQuery($p_query) {
  $rs=null;
  if(!empty($p_query)){
    $db = ConectaBD( );
    $rs = $db->Execute($p_query);

  }
  return $rs;
}

# Ejecuta una consulta regresando el ultimo valor de la clave insertada
function EjecutaInsert($p_query) {

  $db = ConectaBD( );
  $rs = $db->Execute($p_query);
  if(DATABASE_TYPE == DATABASE_SLQSERVER)
    $rs = $db->Execute("SELECT @@IDENTITY");
  if(DATABASE_TYPE == DATABASE_MYSQL)
    $rs = $db->Execute("SELECT LAST_INSERT_ID()");
  $row = $rs->FetchRow( );
  return $row[0];
}

# Ejecuta una consulta, para usar cuando se espera mas de 1 resultado, trayendo unicamente una pagina
function EjecutaQueryLimit($p_query, $p_total, $p_inicio) {

  $db = ConectaBD( );
  $rs = $db->SelectLimit($p_query, $p_total, $p_inicio);
  return $rs;
}

# Regresa un arreglo con los campos del siguiente registro, para usar en consultas con mas de 1 resultado
function RecuperaRegistro($p_rs) {

  if($p_rs)
    $row = $p_rs->FetchRow( );
  return htmlentities($row, ENT_QUOTES, "UTF-8");
}

# Regresa un arreglo con los campos del registro, para usar en consultas que recuperan un solo resultado
function RecuperaValor($p_query) {

  $rs = EjecutaQuery($p_query);
  $row = RecuperaRegistro($rs);
  return $row;
}

# Cuenta registros de un cursor
function CuentaRegistros($p_rs) {

  if($p_rs)
    $no_regs = $p_rs->RecordCount( );
  if(empty($no_regs))
    $no_regs = 0;
  return $no_regs;
}

# Cuenta campos de un cursor
function CuentaCampos($p_rs) {

  if($p_rs)
    $no_campos = $p_rs->FieldCount( );
  if(empty($no_campos))
    $no_campos = 0;
  return $no_campos;
}

# Cuenta campos de un cursor
function NombreCampo($p_rs, $p_cual, $p_sin_centrado=False) {

  if($p_rs)
    $campos = $p_rs->FetchField($p_cual);
  if($campos)
    $nombre = $campos->name;
  else
    $nombre = "";

  # Elimina alineacion del encabezado
  if($p_sin_centrado) {
    $nombre = str_replace('|hidden', '', $nombre);
    $nombre = str_replace('|left', '', $nombre);
    $nombre = str_replace('|center', '', $nombre);
    $nombre = str_replace('|right', '', $nombre);
  }

  return $nombre;
}

# Funcion para verificar si existen registros en la tabla para un campo llave y valor dados
function ExisteEnTabla($p_tabla, $p_campo, $p_valor, $p_clave='', $p_valor_clave='', $p_igual=False) {

  if(empty($p_tabla) OR empty($p_campo) OR empty($p_valor))
    return False;

  $Query  = "SELECT count(1) FROM $p_tabla WHERE $p_campo='$p_valor' ";
  if(!empty($p_clave) AND !empty($p_valor_clave)) {
    $Query .= "AND $p_clave";
    if($p_igual)
      $Query .= " = ";
    else
      $Query .= " <> ";
    $Query .= "$p_valor_clave";
  }
  $row = RecuperaValor($Query);
  if($row[0] > 0)
    return True;
  else
    return False;
}

# Exporta una consulta a CVS
function ExportaQuery($p_nom_arch, $p_query) {

  # Abre archivo de salida
  if(!$archivo = fopen($_SERVER['DOCUMENT_ROOT'].$p_nom_arch, "wb")) {
    MuestraPaginaError(ERR_EXPORTAR);
    exit;
  }

  # Exporta los datos
  $rs = EjecutaQuery($p_query);
  $tot_campos = CuentaCampos($rs);
  for($i = 1; $i < $tot_campos; $i++)
    fwrite($archivo, str_replace(",", " ", str_ascii(NombreCampo($rs, $i, True))).",");
  fwrite($archivo, "\n");
  while($row = RecuperaRegistro($rs)) {
    for($i = 1; $i < $tot_campos; $i++)
      fwrite($archivo, str_replace(",", " ", str_ascii(DecodificaEscogeIdiomaBD($row[$i]))).",");
    fwrite($archivo, "\n");
  }

  # Cierra el archivo
  fclose($archivo);
}

# Regresa dos campos separados por || para escoger idioma
function EscogeIdiomaBD($p_base, $p_trad) {

  $concat = array($p_base, "'||'", NulosBD($p_trad));
  $campo = ConcatenaBD($concat);
  return $campo;
}

# Decodifica el resultado de EscogeIdiomaBD
function DecodificaEscogeIdiomaBD($p_base) {

  $campo = $p_base;
  if($lpos = strpos($campo, '||')) {
    $val1 = substr($campo, 0, $lpos);
    $val2 = substr($campo, $lpos+2);
    $campo = EscogeIdioma($val1, $val2);
  }
  return $campo;
}


#
# Funciones de compatibilidad de Bases de Datos
#

function LengthBD($p_campo) {

  $campo = $p_campo;
  if(DATABASE_TYPE == DATABASE_SLQSERVER)
    $campo = "LEN($p_campo)";
  if(DATABASE_TYPE == DATABASE_MYSQL)
    $campo = "LENGTH($p_campo)";
  return $campo;
}

function NulosBD($p_campo, $p_valor='') {

  $campo = $p_campo;
  if(DATABASE_TYPE == DATABASE_SLQSERVER)
    $campo = "ISNULL($p_campo, '$p_valor')";
  if(DATABASE_TYPE == DATABASE_MYSQL)
    $campo = "IFNULL($p_campo, '$p_valor')";
  return $campo;
}

function ConcatenaBD($p_concat=array()) {

  $campo = "";
  $tot = count($p_concat);
  if(DATABASE_TYPE == DATABASE_SLQSERVER) {
    $campo = "(".$p_concat[0];
    for($i = 1; $i < $tot; $i++)
      $campo .= " + ".$p_concat[$i];
    $campo .= ")";
  }
  if(DATABASE_TYPE == DATABASE_MYSQL) {
    $campo = "CONCAT(".$p_concat[0];
    for($i = 1; $i < $tot; $i++)
      $campo .= ", ".$p_concat[$i];
    $campo .= ")";
  }
  return $campo;
}

function ConsultaFechaBD($p_campo, $p_formato) {

  $campo = $p_campo;
  if(DATABASE_TYPE == DATABASE_SLQSERVER) {
    switch($p_formato) {
      case FMT_CAPTURA: $campo = "CONVERT(varchar, $p_campo, ".EscogeIdioma('105','110').")"; break;
      case FMT_FECHA: $campo = "CONVERT(varchar, $p_campo, ".EscogeIdioma('105','110').")"; break;
      case FMT_HORA: $campo = "CONVERT(varchar(8), $p_campo, 114)"; break;
    }
  }
  if(DATABASE_TYPE == DATABASE_MYSQL) {
    switch($p_formato) {
      case FMT_CAPTURA: $campo = "DATE_FORMAT($p_campo, '".EscogeIdioma('%d-%m','%m-%d')."-%Y')"; break;
      case FMT_FECHA: $campo = "DATE_FORMAT($p_campo, '".EscogeIdioma('%d-%m','%m-%d')."-%Y')"; break;
      case FMT_HORA: $campo = "DATE_FORMAT($p_campo, '%H:%i:%s')"; break;
      case FMT_HORAMIN: $campo = "DATE_FORMAT($p_campo, '%H:%i')"; break;
      case FMT_DATETIME: $campo = "DATE_FORMAT($p_campo, '".EscogeIdioma('%d-%m','%m-%d')."-%Y %H:%i')"; break;
    }
  }
  return $campo;
}


#
# MRA: Funciones para manejo de sesiones
#

# Crea o actualiza cookie con numero de sesion, expira en p_tiempo tiempo (en segundos), scope todo el sitio
function ActualizaSesion($p_sesion, $p_admin=True, $p_tiempo=0) {

  # Tiempo de la sesion
  if(empty($p_tiempo)) {
    if($p_admin)
      $p_tiempo = SESION_VIGENCIA;
    else
      $p_tiempo = ObtenConfiguracion(42)*60;
  }

  # Cookie de sesion
  if($p_admin)
    setcookie(SESION_ADMIN, $p_sesion, time( ) + $p_tiempo, "/");
  else
    setcookie(SESION_CAMPUS, $p_sesion, time( ) + $p_tiempo, "/");
  EjecutaQuery("UPDATE c_usuario SET fe_sesion=CURRENT_TIMESTAMP WHERE cl_sesion='$p_sesion'");

  # Reinicializa cookie de idioma o establece el idioma por omision
  $cl_idioma = $_COOKIE[IDIOMA_NOMBRE];
  if(!empty($cl_idioma))
    setcookie(IDIOMA_NOMBRE, $cl_idioma, time( )+IDIOMA_VIGENCIA, "/");
  else
    setcookie(IDIOMA_NOMBRE, IDIOMA_DEFAULT, time( )+IDIOMA_VIGENCIA, "/");
}

# Limpia cookie de sesion
function TerminaSesion($p_admin=True) {

  $fl_usuario = ObtenUsuario($p_admin);
  if(!$p_admin) {
    $row = RecuperaValor("SELECT MAX(fl_usu_login) FROM k_usu_login WHERE fl_usuario=$fl_usuario");
    $fl_usu_login = $row[0];
    if(!empty($fl_usu_login))
      EjecutaQuery("UPDATE k_usu_login SET fe_logout=CURRENT_TIMESTAMP WHERE fl_usuario=$fl_usuario AND fe_logout IS NULL");
    setcookie(SESION_CAMPUS, '', time( )+SESION_VIGENCIA, "/");
    setcookie(SESION_RM, '', time( )+SESION_VIGENCIA, "/");
  }
  else
    setcookie(SESION_ADMIN, '', time( )+SESION_VIGENCIA, "/");
	EjecutaQuery("UPDATE c_usuario SET fe_sesion=NULL, fg_remember_me='0' WHERE fl_usuario='$fl_usuario'");
}

# Cambia el idioma
function CambiaIdioma( ) {

  # Revisa el idioma seleccionado y lo invierte
  $cl_idioma = $_COOKIE[IDIOMA_NOMBRE];
  if($cl_idioma == IDIOMA_ALTERNO || empty($cl_idioma))
    setcookie(IDIOMA_NOMBRE, IDIOMA_DEFAULT, time( )+IDIOMA_VIGENCIA, "/");
  else
    setcookie(IDIOMA_NOMBRE, IDIOMA_ALTERNO, time( )+IDIOMA_VIGENCIA, "/");
}

# Check if the request is made through ajax
function isAjax( ) {
  $ajax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && (strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
  return $ajax;
}

# Verifica que una sesion es valida
function ValidaSesion($p_admin=True, $p_tiempo=0) {

  # Lee la sesion del cookie
  if(!$p_admin) {
    $id_sesion = $_COOKIE[SESION_RM];
    if(empty($id_sesion))
      $id_sesion = $_COOKIE[SESION_CAMPUS];
  }
  else
    $id_sesion = $_COOKIE[SESION_ADMIN];

  # Valida si existe un identificador de sesion en el cookie
  if(empty($id_sesion)) {
    # -2: La sesi&oacute;n ha expirado.
    if(isAjax()){
      echo json_encode((Object) array('location' => SESION_EXPIRADA));
    } else {
      # Si viene de algu correo o algo externo al sistema y no tiene sesion recuperadas
      # envia como parametro el url donde deseaba ingresar para posteriormente hacerlo
      echo "
      <script>
      var hash = location.href.split('#');
      window.location.href='".SESION_EXPIRADA."&ori='+hash[1];
      </script>";
    }
    exit;
  }

  # Recupera el usuario de la sesion
  $row = RecuperaValor("SELECT fl_usuario FROM c_usuario WHERE cl_sesion='$id_sesion'");
  if(empty($row[0])) {
    # -3: La sesi&oacute;n no existe.
    if(isAjax()){
      echo json_encode((Object) array('location' => SESION_NO_EXISTE));
    } else {
      header("Location: ".SESION_NO_EXISTE);
    }
    exit;
  }
  $fl_usuario = $row[0];

  # Actualiza la sesion
  ActualizaSesion($id_sesion, $p_admin, $p_tiempo);

  # Regresa el usuario de la sesion
  return $fl_usuario;
}

# Funcion para verificar si el usuario tiene permiso de entrar a la funcion
function ValidaPermiso($p_funcion, $p_tipo) {

  # Lee la sesion del cookie
  $cl_sesion = $_COOKIE[SESION_ADMIN];

  # Verifica que existe la sesion
  if(empty($cl_sesion))
    return False;

  # Recupera el usuario y su perfil
  $row = RecuperaValor("SELECT fl_usuario, fl_perfil FROM c_usuario WHERE cl_sesion='$cl_sesion'");
  $fl_usuario = $row[0];
  $fl_perfil = $row[1];

  # Verifica que existe el usuario
  if(empty($fl_usuario))
    return False;

  # Verifica si es el Administrador
  if($fl_usuario == ADMINISTRADOR)
    return True;

  # Recupera el tipo de seguridad de la funcion
  $row = RecuperaValor("SELECT fg_tipo_seguridad FROM c_funcion WHERE fl_funcion=$p_funcion");
  $fg_tipo_seguridad = $row[0];

  # Revisa si la funcion es solo para el Administrador
  if($fg_tipo_seguridad == 'A')
    return False;

  # Revisa si la funcion es Gratis
  if($fg_tipo_seguridad == 'X')
    return True;

  # El tipo de seguridad es Restringido ('R'), verifica si el perfil tiene permiso para la funcion
  $Query  = "SELECT COUNT(1) FROM k_per_funcion ";
  $Query .= "WHERE fl_perfil=$fl_perfil ";
  $Query .= "AND fl_funcion=$p_funcion ";
  switch($p_tipo) {
    case PERMISO_EJECUCION    : $Query .= "AND fg_ejecucion = '1' ";    break;
    case PERMISO_DETALLE      : $Query .= "AND fg_detalle = '1' ";      break;
    case PERMISO_MODIFICACION : $Query .= "AND fg_modificacion = '1' "; break;
    case PERMISO_ALTA         : $Query .= "AND fg_alta = '1' ";         break;
    case PERMISO_BAJA         : $Query .= "AND fg_baja = '1' ";         break;
    default: return False;
  }
  $row = RecuperaValor($Query);
  if($row[0] > 0)
    return True;
  else
    return False;
}

# Recupera el usuario logueado
function ObtenUsuario($p_admin=True) {

  # Lee la sesion del cookie
  if(!$p_admin) {
    $id_sesion = $_COOKIE[SESION_RM];
    if(empty($id_sesion))
      $id_sesion = $_COOKIE[SESION_CAMPUS];
  }
  else
    $id_sesion = $_COOKIE[SESION_ADMIN];

  # Valida si existe un identificador de sesion en el cookie
  if(empty($id_sesion))
    return False;

  # Recupera el usuario de la sesion
  $row = RecuperaValor("SELECT fl_usuario FROM c_usuario WHERE cl_sesion='$id_sesion'");
  if(empty($row[0]))
    return False;

  return $row[0];
}

# Recupera el usuario logueado
function ObtenNombre( ) {

  # Lee la sesion del cookie
  $id_sesion = $_COOKIE[SESION_ADMIN];

  # Valida si existe un identificador de sesion en el cookie
  if(empty($id_sesion))
    return False;

  # Recupera el usuario de la sesion
  $row = RecuperaValor("SELECT ds_nombres, ds_apaterno FROM c_usuario WHERE cl_sesion='$id_sesion'");
  if(empty($row[0]))
    return False;

  return str_uso_normal($row[0]." ".$row[1]);
}

# Recupera el perfil del usuario logueado
function ObtenPerfil($p_usuario) {

  # Revisa que se haya recibido el usuario
  if(empty($p_usuario))
    return False;

  # Recupera el perfil del usuario solicitado
  $row = RecuperaValor("SELECT fl_perfil FROM c_usuario WHERE fl_usuario=$p_usuario");
  if(empty($row[0]))
    return False;

  return $row[0];
}


#
# MRA: Funciones para manejo de parametros
#

function RecibeParametroNumerico($p_nombre, $p_get=False, $p_signo=False) {

  if($p_get)
    $var = $_GET[$p_nombre];
  else
    $var = $_POST[$p_nombre];

  if($var == 'on')
    $var = "1";
  if(!ValidaEntero($var, $p_signo))
    $var = "0";

  return $var;
}

function RecibeParametroBinario($p_nombre) {

  $var = $_POST[$p_nombre];
  if(!empty($var))
    $var = "1";
  else
    $var = "0";

  return $var;
}

function RecibeParametroFlotante($p_nombre, $p_get=False) {

  if($p_get)
    $var = $_GET[$p_nombre];
  else
    $var = $_POST[$p_nombre];

  $var = str_float($var);
  if(!ValidaFlotante($var))
    $var = "0.0";

  return $var;
}

function RecibeParametroHTML($p_nombre, $p_utf8=False, $p_get=False) {

  if($p_get)
    $var = $_GET[$p_nombre];
  else
    $var = $_POST[$p_nombre];

  if($p_utf8)
    $var = str_html_bd(utf8_decode($var));
  else
    $var = str_html_bd($var);

  return $var;
}

function RecibeParametroFecha($p_nombre) {

  $var = $_POST[$p_nombre];
  $len = strlen($var);
  if($len > 0) {
    for($i = 0; $i < $len; $i++) {
      $c = $var[$i];
      if($c >= '0' && $c <= '9') // Puede contener numeros 0-9
        continue;
      if($c == '/' || $c == '-') // Puede contener / -
        continue;
      $var[$i] = ' ';
    }
  }

  return $var;
}

function RecibeParametroHoraMin($p_nombre) {

  $var = $_POST[$p_nombre];
  if(!ValidaHoraMin($var))
    $var = substr($var, 0, 5);

  return $var;
}


#
# MRA: Funciones para manejo de cadenas de texto
#

# Funcion para convertir cadenas recuperadas de la base de datos que se van a editar como HTML
function str_html($p_cadena) {

  $cadena = $p_cadena;
  // $cadena = str_replace("&", "&amp;", $cadena);
  $cadena = str_replace("<", "&lt;", $cadena);
  $cadena = str_replace(">", "&gt;", $cadena);
  $cadena = str_replace("\"", "&quot;", $cadena);
  $cadena = str_replace("'", "&#039;", $cadena);
  return($cadena);
}

# Funcion para convertir cadenas recuperadas de la base de datos que se van a editar como texto
function str_texto($p_cadena) {

  $cadena = $p_cadena;
  $cadena = str_replace("<", "&lt;", $cadena);
  $cadena = str_replace(">", "&gt;", $cadena);
  $cadena = str_replace("\"", "&quot;", $cadena);
  $cadena = str_replace("'", "&#039;", $cadena);
  $cadena = str_replace("’", "&#039;", $cadena);
  $cadena = str_replace("“", "&#039;", $cadena);
  $cadena = str_replace("”", "&#039;", $cadena);
  $cadena = str_replace("à", "&#224;", $cadena);
  $cadena = str_replace("â", "&#226;", $cadena);
  $cadena = str_replace("ã", "&#227;", $cadena);
  $cadena = str_replace("ä", "&#228;", $cadena);
  $cadena = str_replace("å", "&#229;", $cadena);
  $cadena = str_replace("æ", "&#230;", $cadena);
  $cadena = str_replace("ç", "&#231;", $cadena);
  $cadena = str_replace("è", "&#232;", $cadena);
  $cadena = str_replace("ê", "&#234;", $cadena);
  $cadena = str_replace("ë", "&#235;", $cadena);
  $cadena = str_replace("ì", "&#236;", $cadena);
  $cadena = str_replace("î", "&#238;", $cadena);
  $cadena = str_replace("ï", "&#239;", $cadena);
  $cadena = str_replace("ò", "&#242;", $cadena);
  $cadena = str_replace("ô", "&#244;", $cadena);
  $cadena = str_replace("õ", "&#245;", $cadena);
  $cadena = str_replace("ö", "&#246;", $cadena);
  $cadena = str_replace("ù", "&#249;", $cadena);
  $cadena = str_replace("û", "&#251;", $cadena);
  $cadena = str_replace("ü", "&#252;", $cadena);
  $cadena = str_replace("ª", "&#170;", $cadena);
  $cadena = str_replace("º", "&#186;", $cadena);
  return($cadena);
}

# Funcion para recuperar cadenas de la base de datos (HTML) para usarse en el sitio
function str_uso_normal($p_cadena) {

  # Sustituye caracteres especiales
  $cadena = $p_cadena;
  $cadena = str_replace("&lt;", "<", $cadena);
  $cadena = str_replace("&gt;", ">", $cadena);
  $cadena = str_replace("&quot;", "\"", $cadena);
  $cadena = str_replace("&#039;", "'", $cadena);
  $cadena = str_replace("&#061;", "=", $cadena);
  return($cadena);
}

# Funcion para convertir cadenas de la base de datos (HTML) con salida ASCII Ej. para exportacion a Excel
function str_ascii($p_cadena) {

  # Sustituye caracteres especiales
  $cadena = $p_cadena;
  //$cadena = str_replace("&#65;ND", "AND", $cadena);
  //$cadena = str_replace("&#97;nd", "and", $cadena);
  //$cadena = str_replace("&#65;nd", "And", $cadena);
  $cadena = str_replace("&aacute;", chr(225), $cadena);
  $cadena = str_replace("&Aacute;", chr(193), $cadena);
  $cadena = str_replace("&eacute;", chr(233), $cadena);
  $cadena = str_replace("&Eacute;", chr(201), $cadena);
  $cadena = str_replace("&iacute;", chr(237), $cadena);
  $cadena = str_replace("&Iacute;", chr(205), $cadena);
  $cadena = str_replace("&oacute;", chr(243), $cadena);
  $cadena = str_replace("&Oacute;", chr(211), $cadena);
  $cadena = str_replace("&uacute;", chr(250), $cadena);
  $cadena = str_replace("&Uacute;", chr(218), $cadena);
  $cadena = str_replace("&uuml;", chr(252), $cadena);
  $cadena = str_replace("&Uuml;", chr(220), $cadena);
  $cadena = str_replace("&ntilde;", chr(241), $cadena);
  $cadena = str_replace("&Ntilde;", chr(209), $cadena);
  $cadena = str_replace("&iquest;", chr(191), $cadena);
  $cadena = str_replace("&copy;", chr(169), $cadena);
  $cadena = str_replace("&reg;", chr(174), $cadena);
  $cadena = str_replace("&#8482;", '™', $cadena);
  $cadena = str_replace("&lt;", "<", $cadena);
  $cadena = str_replace("&gt;", ">", $cadena);
  $cadena = str_replace("&quot;", "\"", $cadena);
  $cadena = str_replace("&#039;", "'", $cadena);
  $cadena = str_replace("&#061;", "=", $cadena);
  return($cadena);
}

# Funcion para convertir cadenas que se van a guardar en la base de datos
function str_html_bd($p_cadena) {

  # Sustituye caracteres especiales
  $cadena = $p_cadena;
  //$cadena = str_replace("AND", "&#65;ND", $cadena);
  //$cadena = str_replace("and", "&#97;nd", $cadena);
  //$cadena = str_replace("And", "&#65;nd", $cadena);
  //$cadena = str_ireplace("and", "", $cadena);
  $cadena = str_replace("SCRIPT", "&#83;CRIPT", $cadena);
  $cadena = str_replace("script", "&#115;cript", $cadena);
  $cadena = str_replace("Script", "&#83;cript", $cadena);
  $cadena = str_ireplace("script", "", $cadena);
  $cadena = str_replace(chr(225), "&aacute;", $cadena);
  $cadena = str_replace(chr(193), "&Aacute;", $cadena);
  $cadena = str_replace(chr(233), "&eacute;", $cadena);
  $cadena = str_replace(chr(201), "&Eacute;", $cadena);
  $cadena = str_replace(chr(237), "&iacute;", $cadena);
  $cadena = str_replace(chr(205), "&Iacute;", $cadena);
  $cadena = str_replace(chr(243), "&oacute;", $cadena);
  $cadena = str_replace(chr(246), "&ouml;", $cadena);
  $cadena = str_replace(chr(211), "&Oacute;", $cadena);
  $cadena = str_replace(chr(250), "&uacute;", $cadena);
  $cadena = str_replace(chr(218), "&Uacute;", $cadena);
  $cadena = str_replace(chr(252), "&uuml;", $cadena);
  $cadena = str_replace(chr(220), "&Uuml;", $cadena);
  $cadena = str_replace(chr(241), "&ntilde;", $cadena);
  $cadena = str_replace(chr(209), "&Ntilde;", $cadena);
  $cadena = str_replace(chr(191), "&iquest;", $cadena);
  $cadena = str_replace(chr(169), "&copy;", $cadena);
  $cadena = str_replace(chr(174), "&reg;", $cadena);
  $cadena = str_replace(chr(180), "&ordf;", $cadena);
  $cadena = str_replace(chr(186), "&ordm;", $cadena);
  $cadena = str_replace('™', "&#8482;", $cadena);
  $cadena = str_replace("<", "&lt;", $cadena);
  $cadena = str_replace(">", "&gt;", $cadena);
  $cadena = str_replace("\"", "&quot;", $cadena);
  $cadena = str_replace("'", "&#039;", $cadena);
  $cadena = str_replace("=", "&#061;", $cadena);
  if(DATABASE_TYPE == DATABASE_MYSQL)
    $cadena = str_replace("\\", "\\\\", $cadena);
  return($cadena);
}

# Funcion para quitar el formato numerico a una cadena
function str_float($p_cadena) {

  $cadena = $p_cadena;
  $cadena = str_replace('$', '', $cadena);
  $cadena = str_replace(',', '', $cadena);
  $cadena = str_replace('%', '', $cadena);
  return($cadena);
}

# Fincion para dar formato de solo texto eliminando codigo HTML
function str_sin_html($p_cadena) {

  # Elimina tags de formato del texto
  $cadena = $p_cadena;
  $cadena = str_replace("&lt;", "<", $cadena);
  $cadena = str_replace("&gt;", ">", $cadena);
  $cadena = str_replace("<a ", "", $cadena);
  $cadena = str_replace("</a>", "", $cadena);
  $cadena = str_replace("<strong>", "", $cadena);
  $cadena = str_replace("</strong>", "", $cadena);
  $cadena = str_replace("<em>", "", $cadena);
  $cadena = str_replace("</em>", "", $cadena);
  $cadena = str_replace("<sub>", "", $cadena);
  $cadena = str_replace("</sub>", "", $cadena);
  $cadena = str_replace("<sup>", "", $cadena);
  $cadena = str_replace("</sup>", "", $cadena);
  $cadena = str_replace("<b>", "", $cadena);
  $cadena = str_replace("</b>", "", $cadena);
  $cadena = str_replace("<p>", "", $cadena);
  $cadena = str_replace("</p>", "", $cadena);
  $cadena = str_replace("<li>", " ", $cadena);
  $cadena = str_replace("</li>", " ", $cadena);
  $cadena = str_replace("<ul>", " ", $cadena);
  $cadena = str_replace("</ul>", " ", $cadena);
  $cadena = str_replace("<hr>", "", $cadena);
  $cadena = str_replace("<hr />", "", $cadena);
  $cadena = str_replace("<br>", " ", $cadena);
  $cadena = str_replace("<br />", " ", $cadena);

  # Elimina la ultima palabara para no presentar palabras truncas
  $cadena = substr($cadena, 0, strrpos($cadena, ' '));
  return($cadena);
}

# Funcion para generar textos de contratos y cartas
function genera_documento($clave, $opc, $correo=False, $firma=False, $no_contrato=1) {
  # Recupera datos de la sesion
  $Query  = "SELECT cl_sesion, fg_inscrito ";
  $Query .= "FROM c_sesion ";
  $Query .= "WHERE fl_sesion=$clave";
  $row = RecuperaValor($Query);
  $cl_sesion = $row[0];
  $fg_inscrito = $row[1];
  #Obtiene el login siempre y cuando ya este inscrito
  if(!empty($fg_inscrito)){
    $row = RecuperaValor("SELECT ds_login, fl_usuario FROM c_usuario WHERE cl_sesion='$cl_sesion'");
    $ds_login = $row[0];
    $fl_alumno = $row[1];
  }
  # Recupera datos del aplicante: forma 1
  $Query  = "SELECT ds_fname, ds_mname, ds_lname, ds_number, ds_alt_number, ds_email, fg_gender, ";
  $Query .= ConsultaFechaBD('fe_birth', FMT_FECHA)." fe_birth, ";
  $Query .= "ds_add_number, ds_add_street, ds_add_city, ds_add_state, ds_add_zip, d.ds_pais, ";
  $Query .= "nb_programa, fl_template, ds_duracion, nb_periodo, a.fl_programa, b.fg_tax_rate, a.ds_add_country, b.fg_fulltime,c.fl_periodo,b.ptib_approval ";
  $Query .= ",passport_number, ";
  $Query .= ConsultaFechaBD('passport_exp_date', FMT_FECHA) . " passport_exp_date ";
  $Query .= "FROM k_ses_app_frm_1 a, c_programa b, c_periodo c, c_pais d, c_pais e ";
  $Query .= "WHERE a.fl_programa=b.fl_programa ";
  $Query .= "AND a.fl_periodo=c.fl_periodo ";
  $Query .= "AND a.ds_add_country=d.fl_pais ";
  $Query .= "AND a.ds_eme_country=e.fl_pais ";
  $Query .= "AND cl_sesion='$cl_sesion'";
  $row = RecuperaValor($Query);
  $nb_programa = $row[14];
  $ds_fname = str_texto($row[0]);
  $ds_mname = str_texto($row[1]);
  $ds_lname = str_texto($row[2]);
  $ds_number = str_texto($row[3]);
  $ds_alt_number = str_texto($row[4]);
  $ds_email = str_texto($row[5]);
  $fg_gender = str_texto($row[6]);
  $fe_birth = $row[7];
  $ds_add_number = str_texto($row[8]);
  $ds_add_street = str_texto($row[9]);
  $ds_add_city = str_texto($row[10]);
  $ds_add_state = str_texto($row[11]);
  $ds_add_zip = str_texto($row[12]);
  $ds_add_country = str_texto($row[13]);
  $fl_programa_search=$row['fl_programa'];
  $fl_periodo=$row['fl_periodo'];
  $ptib_approval = $row['ptib_approval'];
  $passport_number = $row['passport_number'];
  $passport_exp_date = $row['passport_exp_date'];

  $label_ptib_approval = ($row['ptib_approval']) ? ObtenEtiqueta(2687) : ObtenEtiqueta(2688);
  $yes_no_approval = ($row['ptib_approval']) ? 'Yes' : 'No';


  # Si es de canada obtendremos su provicia
  if($row[20] == 38 AND is_numeric($ds_add_state)){
    $row_1 = RecuperaValor("SELECT ds_provincia FROM k_provincias WHERE fl_provincia=$ds_add_state");
    $st_state = $row_1[0];
  }
  else
    $st_state = $ds_add_state;
  $mailing_add = $ds_add_number." ".$ds_add_street.", ".$ds_add_city." ".$st_state.", ".$ds_add_country;
  $ds_duracion = $row[16];
  $nb_periodo = $row[17];
  $fl_programa = $row[18];
  $fg_tax_rate = $row[19];
  $fl_pais = $row[20];
  $fg_fulltime = $row[21];
  if(!empty($fg_fulltime))
    $fg_fulltime = ObtenEtiqueta(278);
  else
    $fg_fulltime = ObtenEtiqueta(279);
  # Obtenemos el periodo inicial cuando ya tenga un term definido
  $Queryp = "SELECT nb_periodo,b.fl_periodo FROM k_term a, c_periodo b ";
  $Queryp .= "WHERE fl_term=(SELECT  MIN(fl_term)FROM k_alumno_term WHERE ";
  $Queryp .= "fl_alumno=$fl_alumno) AND a.fl_periodo=b.fl_periodo ";
  $rowp = RecuperaValor($Queryp);
  if(ExisteEnTabla('k_alumno_term', 'fl_alumno', $fl_alumno)){
      $nb_periodo = $rowp[0];
      $fl_periodo = $rowp['fl_periodo'];
  }




  #recuperamos datos de los calsstimes
  $Querype="SELECT case
            when cl_dia=1 then 'Monday'
            when cl_dia=2 then 'Tuesday'
            when cl_dia=3 then 'Wednesday'
            when cl_dia=4 then 'Thursday'
            when cl_dia=5 then 'Friday'
            when cl_dia=6 then 'Saturday' end cl_dia, no_hora1,no_tiempo1,no_hora2,no_tiempo2 from c_periodo where fl_periodo=$fl_periodo ";
  $rowpe=RecuperaValor($Querype);
  if($rowpe[0])
  {
        $classtime_combined=$rowpe[0]." ".$rowpe[1]." ".$rowpe[2]." to ".$rowpe[3]." ".$rowpe[4];
        $class_time_combined_label = "Combined :" . $classtime_combined;

  }else{
        $classtime_combined="";
        $class_time_combined_label = "";
  }



    #Recuperamos los classtimes dependiendo del periodo y pograma elegido.
    $Queryc = "SELECT fl_class_time,fl_programa FROM k_class_time WHERE fl_programa=$fl_programa
             AND fl_periodo=$fl_periodo ";
    $rsm = EjecutaQuery($Queryc);
    $horarios = "";
    for ($iii = 1; $rowww = RecuperaRegistro($rsm); $iii++) {
        $fl_class_time = $rowww['fl_class_time'];
        $fl_programa_class = $rowww[1];



        $Wqe = "SELECT CASE WHEN cl_dia='1' THEN '" . ObtenEtiqueta(2390) . "'
								  WHEN cl_dia='2' THEN '" . ObtenEtiqueta(2391) . "'
								  WHEN cl_dia='3' THEN '" . ObtenEtiqueta(2392) . "'
								  WHEN cl_dia='4' THEN '" . ObtenEtiqueta(2393) . "'
								  WHEN cl_dia='5' THEN '" . ObtenEtiqueta(2394) . "'
								  WHEN cl_dia='6' THEN '" . ObtenEtiqueta(2395) . "'
								  ELSE '" . ObtenEtiqueta(2396) . "' END dia ,no_hora,ds_tiempo
					  FROM k_class_time_programa WHERE fl_class_time=$fl_class_time
					";
        $rs3 = EjecutaQuery($Wqe);
        $totclass = CuentaRegistros($rs3);

        $tiene_pro = 0;
        for ($mi = 1; $romi = RecuperaRegistro($rs3); $mi++) {

            $nb_di = $romi[0];
            $nd_hora = $romi[1];
            $ampm = $romi[2];

            $horarios .= $nb_di . " " . $nd_hora . " " . $ampm;
            if ($mi <= ($totclass - 1))
                $horarios .= ", ";
            else
                $horarios .= "";

            $tiene_pro = 1;
        }
    }


  # Template 3 es "Contract Email Template", en c_programa puede traer 1 o 2, que son "Short Term Duration Contract" y "Long Term Student Enrolment Contract"
  # Si trae otro numero, es otro fl_template
  if($correo === True)
    $fl_template = 3;
  elseif($correo === False)
    $fl_template = $row[15];
  else
    $fl_template = $correo;
  if($fg_gender == 'M')
    $ds_gender = ObtenEtiqueta(115);
  if($fg_gender == 'F')
    $ds_gender = ObtenEtiqueta(116);
  if($fg_gender == 'N')
      $ds_gender = "Non-Binary";

  # Recupera datos de la sesion
  $Query  = "SELECT fl_pais_campus ";
  $Query .= "FROM c_sesion ";
  $Query .= "WHERE cl_sesion='$cl_sesion' ";
  $row = RecuperaValor($Query);
  $fl_pais_campus=$row['fl_pais_campus'];
  $ds_campus="CANADA";
  $ds_direccion1="270-5489 Byrne Rd, V5J 3J1";
  $ds_direccion2="Burnaby, British Columbia";
  $ds_direccion3="CANADA";
  $ds_direccion_school=ObtenConfiguracion(169);
  $ds_direccion_acreditation_pdf = ObtenEtiqueta(2686);

  if($fl_pais_campus==226){#USA
      $ds_campus="USA";
	  $ds_direccion1="8105 Birch Bay Square St";
	  $ds_direccion2="#103 Blaine, WA 98230";
	  $ds_direccion3="United States";
	  $ds_direccion_school=ObtenConfiguracion(168);
      $ds_direccion_acreditation_pdf = ObtenEtiqueta(2685);
  }


  #Recupera datos adicionales a la forma 1 y del contrato del aplicante
  $Query  = "SELECT no_contrato, mn_app_fee, mn_tuition, mn_costs, ds_costs, mn_discount, ds_discount, mn_tot_tuition, mn_tot_program, ";
  $Query .= "mn_a_due, mn_a_paid, mn_b_due, mn_b_paid, mn_c_due, mn_c_paid, mn_d_due, mn_d_paid, ";
  $Query .= "ds_cadena, ds_firma_alumno, fg_opcion_pago, fe_firma, ds_p_name, ds_education_number, fg_international, ";
  $Query .= "cl_preference_1, cl_preference_2, ds_m_add_number, ds_m_add_street, ds_m_add_city, ds_m_add_state, ds_m_add_zip, ds_pais, ";
  $Query .= "ds_firma_padre, ds_a_email,tax_mn_cost ";
  $Query .= "FROM k_app_contrato a LEFT JOIN c_pais b ON a.ds_m_add_country=b.fl_pais ";
  $Query .= "WHERE cl_sesion='$cl_sesion' ";
  $Query .= "AND no_contrato=$no_contrato ";
  $row = RecuperaValor($Query);
  $app_fee = $row[1];
  $tuition = $row[2];
  $no_costos_ad = $row[3];
  $ds_costos_ad = $row[4];
  $no_descuento = $row[5];
  $ds_descuento = $row[6];
  $mn_tot_tuition = $row[7];
  $mn_tot_program = $row[8];
  $amount_due_a = $row[9];
  $amount_paid_a = $row[10];
  $amount_due_b = $row[11];
  $amount_paid_b = $row[12];
  $amount_due_c = $row[13];
  $amount_paid_c = $row[14];
  $amount_due_d = $row[15];
  $amount_paid_d = $row[16];
  $ds_cadena = $row[17];
  $ds_firma_alumno = $row[18];
  $opc_pago = $row[19];
  $fe_firma = $row[20];
  $ds_p_name = $row[21];
  $ds_education_number = $row[22];
  $fg_international = $row[23];
  $cl_preference_1 = $row[24];
  $cl_preference_2 = $row[25];
  $ds_m_add_number = $row[26];
  $ds_m_add_street = $row[27];
  $ds_m_add_city = $row[28];
  $ds_m_add_state = $row[29];
  $ds_m_add_zip = $row[30];
  $ds_m_add_country = $row[31];
  $ds_firma_padre = $row[32];
  $ds_a_email = $row[33];
  $tax_mn_cost = !empty($row['tax_mn_cost'])?$row['tax_mn_cost']:0;

  $p_mailing_add = $ds_m_add_number." ";
  if(!empty($ds_m_add_street))
    $p_mailing_add .= $ds_m_add_street.", ";
  $p_mailing_add .= $ds_m_add_city." ";
  if(!empty($ds_m_add_state))
    $p_mailing_add .= $ds_m_add_state.", ";
  $p_mailing_add .= $ds_m_add_country;
  if($fg_international == '1')
    $ds_intl_st = "Yes";
  else
    $ds_intl_st = "No";



  # Recupera datos de pagos del curso
  $Query  = "SELECT no_a_payments, ds_a_freq, no_b_payments, ds_b_freq, no_c_payments, ds_c_freq, no_d_payments, ds_d_freq, cl_type, ";
  $Query .= "no_a_interes, no_b_interes, no_c_interes, no_d_interes, no_horas, no_semanas, ds_credential, cl_delivery, ds_language, fe_modificacion, no_horas_week ";
  $Query .= "FROM k_programa_costos a, c_programa b, k_template_doc c ";
  $Query .= "WHERE a.fl_programa = b.fl_programa ";
  $Query .= "AND b.fl_template = c.fl_template ";
  $Query .= "AND a.fl_programa = $fl_programa";
  $row = RecuperaValor($Query);
  $no_a_payments = $row[0];
  $ds_a_freq = $row[1];
  $no_b_payments = $row[2];
  $ds_b_freq = $row[3];
  $no_c_payments = $row[4];
  $ds_c_freq = $row[5];
  $no_d_payments = $row[6];
  $ds_d_freq = $row[7];
  $cl_type = $row[8];
  $no_a_interes = $row[9];
  $no_b_interes = $row[10];
  $no_c_interes = $row[11];
  $no_d_interes = $row[12];
  $no_horas = $row[13];
  $no_semanas = $row[14];
  $ds_credential = $row[15];
  $cl_delivery = $row[16];
  $ds_language = $row[17];
  $fe_modificacion = $row[18];
  $no_horas_week = $row[19];


  #Recovery additional information
  $QueryP="SELECT ds_career,ds_objetives,ds_teaching,ds_evaluation,ds_requeriments,ds_program_org,ds_combinend FROM c_programa WHERE fl_programa=$fl_programa_search ";
  $rowp = RecuperaValor($QueryP);
  $ds_career=html_entity_decode($rowp['ds_career']);
  $ds_objetives=html_entity_decode($rowp['ds_objetives']);
  $ds_teaching=html_entity_decode($rowp['ds_teaching']);
  $ds_evaluation=html_entity_decode($rowp['ds_evaluation']);
  $ds_requeriments=html_entity_decode($rowp['ds_requeriments']);
  $ds_program_org=html_entity_decode($rowp['ds_program_org']);
  $ds_combinend=html_entity_decode($rowp['ds_combinend']);

  $grading_scale='';

    switch ($fl_pais_campus) {

        case '38':

            $symbol = "$";
            break;
        case '226':
            $symbol = "$";
            break;
        case '199':
            $symbol = "€";
            break;
        case '73':
            $symbol = "€";
            break;
        case '80':
            $symbol = "€";
            break;
        case '105':
            $symbol = "€";
            break;
        case '225':
            $symbol = "£";
            break;
        case '153':
            $symbol = "€";
            break;
        default:
            $symbol = "$";
            break;

    }




    /*
    $Queryc="SELECT cl_calificacion,ds_calificacion,fg_aprobado,no_equivalencia,no_min,no_max FROM c_calificacion ORDER BY fl_calificacion asc ";
    $rsc=EjecutaQuery($Queryc);
    $tablegrading='<table border="1" cellpadding="0" cellspacing="0" class="PlainTable11" style="width:100%">
                  <tbody>
                      <tr>
                          <td><strong>Letter</strong></td>
                          <td>
                          <p><strong>Percent</strong></p>
                          </td>
                          <td><strong>Description</strong></td>
                      </tr>';
    for ($xxx = 1; $roxxx = RecuperaRegistro($rsc); $xxx++) {


        $tablegrading.='<tr>
                          <td>'.$roxxx['cl_calificacion'].'</td>
                          <td>
                          <p>'.$roxxx['no_min'].'-'.$roxxx['no_max'].'</p>
                          </td>
                          <td>'.$roxxx['ds_calificacion'].'</td>
                      </tr>';

    }
    $tablegrading.='</tbody>
                  </table>';
    */





  # Calculos pagos
  $total_tuition = number_format($tuition + $no_costos_ad - $no_descuento, 2, '.', '');
  $total = number_format($app_fee + $total_tuition, 2, '.', '');

  #se agrega tipo de credencial al nombre del programa certicate/diploma
  if($fl_template==194)
  {
      $nb_programa = $nb_programa." ".$ds_credential;

  }


  # Recupera datos del template del documento
  switch($opc)
  {
    case 1: $campo = "ds_encabezado"; break;
    case 2: $campo = "ds_cuerpo"; break;
    case 3: $campo = "ds_pie"; break;
    case 4: $campo = "nb_template"; break;
  }
  $Query  = "SELECT $campo FROM k_template_doc WHERE fl_template=$fl_template ";
  $row = RecuperaValor($Query);

  # Sustituye caracteres especiales
  $cadena = $row[0];
  $cadena = str_replace("&lt;", "<", $cadena);
  $cadena = str_replace("&gt;", ">", $cadena);
  $cadena = str_replace("&quot;", "\"", $cadena);
  $cadena = str_replace("&#039;", "'", $cadena);
  $cadena = str_replace("&#061;", "=", $cadena);


  #replace
  $cadena = str_replace("#ds_careers#", $ds_career, $cadena);
  $cadena = str_replace("#ds_objectives#", $ds_objetives, $cadena);
  $cadena = str_replace("#ds_teaching#", $ds_teaching, $cadena);
  $cadena = str_replace("#ds_evaluation#", $ds_evaluation, $cadena);
  $cadena = str_replace("#ds_requirements#", $ds_requeriments, $cadena);
  $cadena = str_replace("#ds_program_org#", $ds_program_org, $cadena);
  $cadena = str_replace("#ds_combined#", $ds_combinend, $cadena);
  $cadena = str_replace("#grading_scale#", $grading_scale, $cadena);

  $cadena = str_replace("#passport_number#", $passport_number, $cadena);
  $cadena = str_replace("#passport_exp_date#", $passport_exp_date, $cadena);



    # Sustituye variables con datos del alumno

  $cadena = str_replace("#st_fname#", "".$ds_fname, $cadena);                      #Student first name
  $cadena = str_replace("#st_mname#", "".$ds_mname, $cadena);                      #Student middle name
  $cadena = str_replace("#st_lname#", "".$ds_lname, $cadena);                      #Student last name
  $cadena = str_replace("#st_pname#", "".$ds_p_name, $cadena);                     #Student previous name
  $cadena = str_replace("#st_ednum#", "".$ds_education_number, $cadena);           #Student personal education number
  $cadena = str_replace("#st_lmadd#", "".$mailing_add, $cadena);                   #Student local mailing address
  $cadena = str_replace("#st_lmaddpc#", "".$ds_add_zip, $cadena);                  #Student local mailing address postal code
  $cadena = str_replace("#st_pmadd#", "".$p_mailing_add, $cadena);                 #Student permanent mailing address
  $cadena = str_replace("#st_pmaddpc#", "".$ds_m_add_zip, $cadena);                #Student permanent mailing address postal code
  $cadena = str_replace("#st_street_no#", $ds_add_number, $cadena);                #Student street number
  $cadena = str_replace("#st_street_name#", $ds_add_street, $cadena);              #Student street
  $cadena = str_replace("#st_city#", $ds_add_city, $cadena);                       #Student city
  $cadena = str_replace("#st_country#", $ds_add_country, $cadena);                 #Student country
  $cadena = str_replace("#st_state#", $st_state, $cadena);                         #Student state
  $cadena = str_replace("#st_code_zip#", $ds_add_zip, $cadena);                    #Student codigo postal
  $cadena = str_replace("#st_pnone#", "".$ds_number, $cadena);                     #Student telephone number
  $cadena = str_replace("#st_aphone#", "".$ds_alt_number, $cadena);                #Student alternative telephone number
  $cadena = str_replace("#st_email#", "".$ds_email, $cadena);                      #Student email address
  $cadena = str_replace("#st_aemail#", "".$ds_a_email, $cadena);                   #Student alternative email address
  $cadena = str_replace("#st_ist#", "".$ds_intl_st, $cadena);                      #International student yes
  $cadena = str_replace("#st_byear#", "".substr($fe_birth,6,4), $cadena);          #Student year of birth
  $cadena = str_replace("#st_bmonth#", "".substr($fe_birth,3,2), $cadena);         #Student month of birth
  $cadena = str_replace("#st_bday#", "".substr($fe_birth,0,2), $cadena);           #Student day of birth
  $cadena = str_replace("#st_gender#", "".$ds_gender, $cadena);                    #Student gender female
  $cadena = str_replace("#st_login#", "".$ds_login, $cadena);                      #Student login
  $cadena = str_replace("#pg_name#", "".$nb_programa, $cadena);                    #Program name
  $cadena = str_replace("#academic_status#", $fg_fulltime, $cadena);               #Full o Part Time Program
  $cadena = str_replace("#hours_week#", $no_horas_week, $cadena);                  #Hour per week course
  $cadena = str_replace("#pg_durationh#", "".$no_horas, $cadena);                  #Program duration in hours
  $cadena = str_replace("#pg_durationw#", "".$no_semanas, $cadena);                #Program duration in weeks
  // $no_duracion = round($no_semanas / 4.3, 0);
  $cadena = str_replace("#pg_durationm#", "".$ds_duracion, $cadena);               #Program duration in months
  $cadena = str_replace("#ds_campus#",$ds_campus,$cadena);                                                      # campus
  $cadena = str_replace("#ds_direccion1#",$ds_direccion1,$cadena);
  $cadena = str_replace("#ds_direccion2#",$ds_direccion2,$cadena);
  $cadena = str_replace("#ds_direccion3#",$ds_direccion3,$cadena);
  $cadena = str_replace("#st_address_campus#",$ds_direccion_school,$cadena);
  $cadena = str_replace("#st_address_campus_accreditation#", $ds_direccion_acreditation_pdf, $cadena);
  $cadena = str_replace("#ptib_approval#", $label_ptib_approval, $cadena);
  $cadena = str_replace("#yes_no_approval#", $yes_no_approval, $cadena);


  # Si el curso es typo 4 deberan ser 3 contratos  uno por cada anio
  # todo esto como lo dice PCTIA que es mutl anios
  if($cl_type==4) //modifica
  {
    switch($no_contrato)
    {
      case 1:
        $no_semanas_i = 0;
        $no_semanas_f = 52;
      break;
      case 2:
        $no_semanas_i = 52;
        $no_semanas_f = 104;
      break;
      case 3:
        $no_semanas_i = 104;
        $no_semanas_f = $no_semanas;
      break;
    }
  }
  else
  {
    # En caso de que el curso dure mas de 18 meses y menos que 24 meses
    # Entonces se enviaran 2 contratos uno por anio
    if($no_semanas>78 AND $no_semenas<104){
      switch($no_contrato){
      case 1:
        $no_semanas_i = 0;
        $no_semanas_f = 52;
      break;
      case 2:
        $no_semanas_i = 52;
        $no_semanas_f = $no_semanas;
      break;
    }

    }
    else{ # Si es curso dura menos de 18 meses entonces solo se enviara contrato
      $no_semanas_i = 0;
      $no_semanas_f = $no_semanas;
    }
  }

  $fecha_inicio = date("M j, Y", strtotime("$nb_periodo + $no_semanas_i weeks"));
  $cadena = str_replace("#pg_stdate#", "".$fecha_inicio, $cadena);  #Program start date
  # Buscamos los breaks que existen entre la fecha_inicio y fecha_final y obtenemos el total de dias
  # para despues sumarlos en la fecha final
  $fecha_final = date("M j, Y",strtotime("$nb_periodo + $no_semanas_f weeks"));
  $Query  = "SELECT SUM(no_days) no_dias FROM c_break WHERE fe_ini  BETWEEN '".date("Y-m-d", strtotime("$nb_periodo + $no_semanas_i weeks"))."'  ";
  $Query .= "AND '".date("Y-m-d",strtotime("$nb_periodo + $no_semanas_f weeks"))."' ";
  $row = RecuperaValor($Query);
  $no_dias = $row[0];
  # Si el no_dias es mayor a cero los sumara al program end date
  if(!empty($no_dias))
    $fecha_final = date("M j, Y",strtotime("+ $no_dias days",strtotime("$nb_periodo + $no_semanas_f weeks")));
  # Obtenemos la fecha final
  $Queryf = "SELECT fe_completado FROM k_pctia WHERE fl_alumno=$fl_alumno";
  $rowf = RecuperaValor($Queryf);
  if(!empty($rowf[0]))
    $fecha_final = date('M j, Y',strtotime($rowf[0]));
  $cadena = str_replace("#pg_edate#", "".$fecha_final, $cadena);                   #Program end date
  $cadena = str_replace("#pg_credential#", "".$ds_credential, $cadena);            #Program credential diploma
  switch ($cl_delivery)
  {
    case 'O': $ds_tipo = "Online (Synchronous)"; break;
    case 'S': $ds_tipo = "On-Site"; break;
    case 'C': $ds_tipo = "Combined (Asynchronous)"; break;
  }

#Sustituye variables del classtime.
$cadena = str_replace("#hr_class_time#", "" . $horarios, $cadena); #Horarios classtimes



    if ($fl_template == 24) {

        $cadena = str_replace("#ds_combined_time#", $classtime_combined, $cadena);

    } else {

        if ($cl_delivery == 'C') {

            $classtime_combined = $horarios_label_online . " " . $class_time_combined_label;
        } else {
            $classtime_combined = "";// $horarios_label_online; //solo muestra online

        }




        $cadena = str_replace("#ds_combined_time#", $classtime_combined, $cadena);
    }


    $cadena = str_replace("#pg_delivery#", "".$ds_tipo, $cadena);                                     #Program delivery on-site
  $cadena = str_replace("#pg_language#", "".$ds_language, $cadena);                                 #Program language
  $cadena = str_replace("#pg_appfee#", $symbol."".number_format($app_fee,2), $cadena);               #Program application fee
  $cadena = str_replace("#pg_tuition#", $symbol."".number_format($tuition,2), $cadena);              #Program tuition
  $cadena = str_replace("#pg_ds_other_cost#", "".$ds_costos_ad, $cadena);                           #Program other cost description
  $cadena = str_replace("#pg_other_cost#", $symbol."".number_format($no_costos_ad,2), $cadena);      #Program other cost
  $cadena = str_replace("#pg_ds_cost_discount#", "".$ds_descuento, $cadena);                        #Program discount description.
  $cadena = str_replace("#pg_cost_discount#", $symbol."".number_format($no_descuento,2), $cadena);   #Program discount.
  $cadena = str_replace("#pg_total_tuition#", $symbol."".number_format($mn_tot_tuition,2, '.', ''), $cadena); #Program total tuition cost
  $cadena = str_replace("#pg_total_cost#", $symbol."".number_format($mn_tot_program,2), $cadena);    #Program total cost
  # Obtendremos el app fee tax y el tuition app fee tax
  # si el aplicante es de canada y el  programa requiere tax rate
  $app_fee_tax = 0;
  $tuition_fee_tax = 0;

    if ($fl_pais == 38 and (!empty($fg_tax_rate) || empty($fg_tax_rate))) {
    if(!empty($ds_add_state)){
      $row_tax = RecuperaValor("SELECT ds_abreviada,mn_tax FROM k_provincias WHERE fl_provincia='$ds_add_state'");
      $ds_abreviada = $row_tax[0];
      $mn_tax_rate = $row_tax[1];
      $app_fee_tax = $app_fee*($mn_tax_rate/100);
      $tuition_fee_tax = $tuition*($mn_tax_rate/100);

    if ((empty($tax_mn_cost)) || ($tax_mn_cost == 0)) {
                #tax FAME.
                if (($ds_costos_ad == "VANAS+ Learning Resources") || ($ds_costos_ad == "VANAS Plus Learning Resources") || ($ds_costos_ad == "VANAS+ Learning Resources")) {

                    $tax_mn_cost = $no_costos_ad * ($mn_tax_rate / 100);
                }

    }








    }
  }
  # Realizamos la suma total que pagara app fee tax mas tuition fee tax y el costo del programa
  $total_costs = $mn_tot_program + $app_fee_tax + $tuition_fee_tax + $tax_mn_cost;
  # Remplazamos los valores del app fee tax y el tuition fee tax
  $cadena = str_replace("#app_fee_tax#", $symbol."".number_format($app_fee_tax,2), $cadena);    #App fee tax
  $cadena = str_replace("#tuition_fee_tax#", $symbol."".number_format($tuition_fee_tax,2), $cadena);    #Tuition fee tax
  $cadena = str_replace("#total_costs#", $symbol."".number_format($total_costs,2), $cadena);    # Total costs
  $cadena = str_replace("#fame_tax#", $symbol."" . number_format($tax_mn_cost,2), $cadena); #fame taxes

  $tax_mn_cost_x_invoice_a = 0;
  $tax_mn_cost_x_invoice_b = 0;
  $tax_mn_cost_x_invoice_c = 0;

  $tax_mn_cost_x_invoice_b_paid = 0;
  $tax_mn_cost_x_invoice_c_paid = 0;


  if (($ds_costos_ad == "VANAS+ Learning Resources") || ($ds_costos_ad == "VANAS Plus Learning Resources") || ($ds_costos_ad == "VANAS+ Learning Resources")) {
    $tax_mn_cost_x_invoice_a = $tax_mn_cost;
    $tax_mn_cost_x_invoice_b = $tax_mn_cost / 2;
    $tax_mn_cost_x_invoice_c = $tax_mn_cost / 4;

    $tax_mn_cost_x_invoice_b_paid = $tax_mn_cost;
    $tax_mn_cost_x_invoice_c_paid = $tax_mn_cost;

  }


    switch ($opc_pago)
  {
    case 1:
      $opc_a = "X";
      $opc_b = "";
      $opc_c = "";
      $opc_d = "";

    break;
    case 2:
      $opc_a = "";
      $opc_b = "X";
      $opc_c = "";
      $opc_d = "";

      break;
    case 3:
      $opc_a = "";
      $opc_b = "";
      $opc_c = "X";
      $opc_d = "";
    break;
    case 4:
      $opc_a = "";
      $opc_b = "";
      $opc_c = "";
      $opc_d = "X";
    break;
  }
  $cadena = str_replace("#py_optionA#", "".$opc_a, $cadena);                                        #Payment option A.
  $cadena = str_replace("#py_optionB#", "".$opc_b, $cadena);                                        #Payment option B.
  $cadena = str_replace("#py_optionC#", "".$opc_c, $cadena);                                        #Payment option C.
  $cadena = str_replace("#py_optionD#", "".$opc_d, $cadena);                                        #Payment option D.
  $cadena = str_replace("#py_paymentsA#", "".$no_a_payments, $cadena);                              #Number of payments option A.
  $cadena = str_replace("#py_paymentsB#", "".$no_b_payments, $cadena);                              #Number of payments option B.
  $cadena = str_replace("#py_paymentsC#", "".$no_c_payments, $cadena);                              #Number of payments option C.
  $cadena = str_replace("#py_paymentsD#", "".$no_d_payments, $cadena);                              #Number of payments option D.
  $cadena = str_replace("#py_freqA#", "".$ds_a_freq, $cadena);                                      #Frequency Payment option A.
  $cadena = str_replace("#py_freqB#", "".$ds_b_freq, $cadena);                                      #Frequency Payment option B.
  $cadena = str_replace("#py_freqC#", "".$ds_c_freq, $cadena);                                      #Frequency Payment option C.
  $cadena = str_replace("#py_freqD#", "".$ds_d_freq, $cadena);                                      #Frequency Payment option D.
  $cadena = str_replace("#py_dueoptionA#", $symbol."".number_format(($amount_due_a+$tax_mn_cost_x_invoice_a),2), $cadena);      #Payment Amount Due option A
  $cadena = str_replace("#py_dueoptionB#", $symbol."".number_format(($amount_due_b + $tax_mn_cost_x_invoice_b),2), $cadena);      #Payment Amount Due option B
  $cadena = str_replace("#py_dueoptionC#", $symbol."".number_format(($amount_due_c + $tax_mn_cost_x_invoice_c),2), $cadena);      #Payment Amount Due option C
  $cadena = str_replace("#py_dueoptionD#", $symbol."".number_format($amount_due_d,2), $cadena);      #Payment Amount Due option D
  $cadena = str_replace("#py_paidoptionA#", $symbol."".number_format(($amount_paid_a + $tax_mn_cost_x_invoice_a),2), $cadena);    #Payment Amount Paid option A
  $cadena = str_replace("#py_paidoptionB#", $symbol."".number_format(($amount_paid_b + $tax_mn_cost_x_invoice_b_paid),2), $cadena);    #Payment Amount Paid option B
  $cadena = str_replace("#py_paidoptionC#", $symbol."".number_format(($amount_paid_c + $tax_mn_cost_x_invoice_c_paid),2), $cadena);    #Payment Amount Paid option C
  $cadena = str_replace("#py_paidoptionD#", $symbol."".number_format($amount_paid_d,2), $cadena);    #Payment Amount Paid option D
  if($firma)
    $fecha = date("M j, Y");
  else
  {
    if(!empty($fe_firma))
      $fecha = date("M j, Y",strtotime("$fe_firma"));
    else
      $fecha = "";
  }
  $cadena = str_replace("#st_signaturedt#", "".$fecha, $cadena);                      #Electronic student signature date
  $cadena = str_replace("#st_signature#", "".$ds_firma_alumno, $cadena);              #Electronic student signature date
  $cadena = str_replace("#st_lg_signature#", "".$ds_firma_padre, $cadena);            #Electronic legal guardian signature
  if(!empty($ds_firma_padre))
    $fecha_papa = $fecha;
  else
    $fecha_papa = '';
  $cadena = str_replace("#st_lg_signaturedt#", "".$fecha_papa, $cadena);              #Electronic legal guardian signature date
  $fe_mod = date("M j, Y",strtotime("$fe_modificacion"));
  $cadena = str_replace("#con_mod_date#", "".$fe_mod, $cadena);                       #Contract template modification date

  # Obtenemos la fecha que se envio el correo. Si no se ha enviado, se muestra fecha actual
  if(ExisteEnTabla('k_alumno_template','fl_template',$fl_template, 'fl_alumno', $clave,True))
    $row = RecuperaValor("SELECT DATE_FORMAT(fe_envio,'%M-%d-%Y') fe_envio_template, DATE_FORMAT(DATE_ADD(fe_envio, INTERVAL ".ObtenConfiguracion(89)." DAY),'%Y/%m/%d') fe_expiration FROM k_alumno_template WHERE fl_alumno=$clave AND fl_template=$fl_template");
  else
    $row = RecuperaValor("SELECT DATE_FORMAT(NOW(),'%M-%d-%Y'), DATE_FORMAT(DATE_ADD(NOW(), INTERVAL ".ObtenConfiguracion(89)." DAY),'%Y/%m/%d') fe_expiration");
  $fe_envio_template = $row[0];
  $fe_expiration = $row[1];
  $cadena = str_replace("#sent_date#", $fe_envio_template, $cadena);               #Fecha de envio del template o correo
  $cadena = str_replace("#fe_expiration#", $fe_expiration, $cadena);               #Days expiration of letter of acceptance

  # Obtenemos el fl_alumno mediante el cl_sesion
  $rowst = RecuperaValor("SELECT fl_usuario FROM c_usuario WHERE cl_sesion='$cl_sesion'");
  $fl_alumno = $rowst[0];

  $rowsa = RecuperaValor("SELECT notation_transcript FROM c_alumno WHERE fl_alumno=$fl_alumno ");
  $ds_notation = ($fl_template==194)?null:$rowsa['notation_transcript'];

  $cadena = str_replace("#ds_notation#", $ds_notation, $cadena); ##notation diplomas and transcripts

    if ((empty($ds_notation)) && $fl_template==194) {//diploma
        $cadena = str_replace("Notation:", "", $cadena);
    }

  # Obtenemos el promedio general del curso
  $QueryGPA  = "SELECT (SELECT cl_calificacion FROM c_calificacion WHERE no_min <= ROUND(no_promedio_t) AND no_max >= ROUND(no_promedio_t)), ";
  $QueryGPA .= "no_promedio_t FROM c_alumno WHERE fl_alumno=$fl_alumno ";
  $row2 = RecuperaValor($QueryGPA);
  $gpa_grl = $row2[0]." ".round($row2[1])."%";
  $row3 = RecuperaValor("");
    if(empty($gpa_grl))
    $gpa_grl = "(No assigment)";
  # Remplazamos el caracter del grado actual y el promedio general
  $rowterm = RecuperaValor("SELECT MAX(fl_term) FROM k_alumno_term WHERE fl_alumno=$fl_alumno");
  $fl_term_actual = $rowterm[0];
  /*$rowgrado = RecuperaValor("SELECT no_grado FROM k_term WHERE fl_term=$fl_term_actual");*/
  $rowgrado=RecuperaValor("SELECT c.no_grado FROM k_alumno_grupo a, c_grupo b, k_term c
                           WHERE  a.fl_grupo=b.fl_grupo AND b.fl_term=c.fl_term AND a.fl_alumno =$fl_alumno ");
  $no_grado = $rowgrado[0];
  $cadena = str_replace("#no_grado#", $no_grado, $cadena);                         # No de grado actual del alumno
  $cadena = str_replace("#program_gpa#", $gpa_grl, $cadena);                           # Promedio general del alumno

  # Obtenemos la calificacion del term
  $QueryTerm  = "SELECT (SELECT cl_calificacion FROM c_calificacion WHERE no_min <=ROUND(no_promedio) AND no_max >=ROUND(no_promedio)), ROUND(no_promedio) ";
  $QueryTerm .= "FROM k_alumno_term WHERE fl_term=$fl_term_actual AND fl_alumno=$fl_alumno";
  $rowc = RecuperaValor($QueryTerm);
  $cl_cal_term = $rowc[0];
  $current_term_promedio = $rowc[1];
  if(empty($current_term_promedio))
    $current_term_promedio = "0";
  $current_term_gpa = $cl_cal_term." ".round($current_term_promedio)."%";
  # Remplazamos el caracter de la calificacion del term actual
  $cadena = str_replace("#current_term_gpa#", $current_term_gpa,$cadena );

  # Obtenemos la calificacion de la ultima semana
  $rowgrupo = RecuperaValor("SELECT fl_grupo FROM k_alumno_grupo WHERE fl_alumno=$fl_alumno");
  $fl_grupo_actual = $rowgrupo[0];
  if(empty($fl_grupo_actual)){
    $rowgrupo1 = RecuperaValor("SELECT fl_grupo FROM c_grupo WHERE fl_term=$fl_term_actual");
    $fl_grupo_actual = $rowgrupo1[0];
  }
  #  Calificacion de la semana actual lo dejo por cualquier otra cosa
  /*$Querys  = "SELECT cl_calificacion, no_equivalencia, a.fl_semana ";
  $Querys .= "FROM k_entrega_semanal a, c_calificacion b ";
  $Querys .= "WHERE a.fl_promedio_semana=b.fl_calificacion ";
  $Querys .= "AND fl_semana=(SELECT MAX(fl_semana) FROM k_entrega_semanal ";
  $Querys .= "WHERE fl_alumno=$fl_alumno AND fl_grupo=$fl_grupo_actual AND fl_promedio_semana IS NOT NULL) ";
  $Querys .= "AND fl_alumno=$fl_alumno ";
  $Querys .= "AND fl_grupo=$fl_grupo_actual ";
  $Querys .= "AND fl_promedio_semana IS NOT NULL";
  $rows = RecuperaValor($Querys);
  $semana_act = $rows[0]." ".$rows[1]." %";
  $cadena = str_replace("#current_week_grade#", $semana_act, $cadena);*/
  $Querysem  = "SELECT i.fl_semana, i.fl_promedio_semana FROM k_entrega_semanal i WHERE i.fl_semana = ( ";
  $Querysem .= "SELECT MAX(a.fl_semana) FROM k_semana a, k_entrega_semanal b ";
  $Querysem .= "WHERE a.fl_semana=b.fl_semana AND fl_term=$fl_term_actual AND b.fl_grupo=$fl_grupo_actual AND b.fl_alumno=$fl_alumno ";
  $Querysem .= "AND  b.fl_promedio_semana>=1 ORDER BY a.fl_semana) AND i.fl_grupo=$fl_grupo_actual AND i.fl_alumno=$fl_alumno ";
  $Querysem .= "AND i.fl_promedio_semana>=1 ";
  $rowsem = RecuperaValor($Querysem);
  $fl_semana_actual = $rowsem[0];
  $fl_promedio_semana = $rowsem[1];

  # semana actual
  $rowsem = RecuperaValor("SELECT no_semana FROM k_semana a, c_leccion b WHERE a.fl_semana=$fl_semana_actual AND a.fl_leccion=b.fl_leccion");
  $no_semana = $rowsem[0];
  $cadena = str_replace("#no_week#", $no_semana, $cadena);

  # Current grade week
  $rowweek = RecuperaValor("SELECT cl_calificacion, no_equivalencia FROM c_calificacion WHERE fl_calificacion=$fl_promedio_semana");
  $current_grade_week = $rowweek[0]." ".round($rowweek[1])."%";
  $cadena = str_replace("#current_week_grade#", $current_grade_week, $cadena);

  # Calificacion Minima aprovada
  $reprovada  = "SELECT cl_calificacion, no_min FROM c_calificacion ";
  $reprovada .= "WHERE no_equivalencia=(SELECT MIN(no_equivalencia) FROM c_calificacion WHERE fg_aprobado='1') ";
  $rowr = RecuperaValor($reprovada);
  $cl_calificacion = $rowr[0];
  $no_equivalencia = round($rowr[1]);
  $calificacion_min = $cl_calificacion." ".$no_equivalencia."%";
  $cadena = str_replace("#minimum_gpa#", $calificacion_min, $cadena);


  return (str_uso_normal($cadena));
}

#
# MRA: Funciones para manejo de fechas
#

# Verifica que la fecha sea valida
# Formatos permitidos
# espanol: ddmmaa ddmmaaaa dd-mm-aa dd-mm-aaaa
# ingles : mmddaa mmddaaaa mm-dd-aa mm-dd-aaaa
# separadores: '-', '/', '.', ' '
function ValidaFecha($p_date) {

  # Valida la longitud de la cadena
  if(strlen($p_date) != 6 AND strlen($p_date) != 8 AND strlen($p_date) != 10)
    return NULL;

  # Obtiene el idioma de la sesion
  # $cl_idioma = ObtenIdioma();

  # Descompone la fecha
  if(strlen($p_date) == 6) {
    //if($cl_idioma == ESPANOL) { // ddmmaa
      $day = substr($p_date, 0, 2);
      $month = substr($p_date, 2, 2);
    //}
    //else { // mmddaa
    //  $month = substr($p_date, 0, 2);
    //  $day = substr($p_date, 2, 2);
    //}
    $year = substr($p_date, 4, 2);
  }
  if(strlen($p_date) == 8) {
    if(strpos($p_date, '.') OR strpos($p_date, '-') OR strpos($p_date, '/') OR strpos($p_date, ' ')) {
      //if($cl_idioma == ESPANOL) { // dd-mm-aa
        $day = substr($p_date, 0, 2);
        $month = substr($p_date, 3, 2);
      //}
      //else { // mm-dd-aa
      //  $month = substr($p_date, 0, 2);
      //  $day = substr($p_date, 3, 2);
      //}
      $year = substr($p_date, 6, 2);
    }
    else {
      //if($cl_idioma == ESPANOL) { // ddmmaaaa
        $day = substr($p_date, 0, 2);
        $month = substr($p_date, 2, 2);
      //}
      //else { // mmddaaaa
      //  $month = substr($p_date, 0, 2);
      //  $day = substr($p_date, 2, 2);
      //}
      $year = substr($p_date, 4, 4);
    }
  }
  if(strlen($p_date) == 10) {
    //if($cl_idioma == ESPANOL) { // dd-mm-aaaa
      $day = substr($p_date, 0, 2);
      $month = substr($p_date, 3, 2);
    //}
    //else { // mm-dd-aaaa
    //  $month = substr($p_date, 0, 2);
    //  $day = substr($p_date, 3, 2);
    //}
    $year = substr($p_date, 6, 4);
  }

  # Valida que los componentes sean numericos
  if(!is_numeric($day) OR !is_numeric($month) OR !is_numeric($year))
    return NULL;

  # Convierte la fecha en timestamp
  $stamp = strtotime($year."-".$month."-".$day);
  if(!is_numeric($stamp))
    return NULL;

  # Verifica que sea una fecha valida
  $day   = date('d', $stamp);
  $month = date('m', $stamp);
  $year  = date('Y', $stamp);
  if(!checkdate($month, $day, $year))
    return NULL;

  # Regresa una cadena con la fecha en formato universal
  $date = $year."-".$month."-".$day;
  return $date;
}

# Verifica que la hora sea valida (horas-minutos)
# Formatos permitidos: H:MM, HH:MM
function ValidaHoraMin($p_hora) {

  # Valida la longitud de la cadena
  $len = strlen($p_hora);
  if($len == 4)
    $p_hora = "0".$p_hora;
  $len = strlen($p_hora);
  if($len != 5)
    return NULL;

  # Verifica el formato H:MM o HH:MM
  $c = $p_hora[2];
  if($c <> ':')
    return NULL;
  for($i = 0; $i < $len; $i++) {
    if($i == 2)
      continue;
    $c = $p_hora[$i];
    if($c >= '0' && $c <= '9') // Puede contener numeros 0-9
      continue;
    return NULL;
  }

  # Descompone la hora
  $horas = substr($p_hora, 0, 2);
  $minutos = substr($p_hora, 3, 2);

  # Valida que la hora sea valida
  if($horas < 0 OR $horas > 23)
    return NULL;

  # Valida que los minutos sean validos
  if($minutos < 0 OR $minutos > 59)
    return NULL;

  # Regresa la hora original
  return $p_hora;
}

# Obtiene el nombre de un mes
function ObtenNombreMes($p_mes) {

  $etq_mes = "";
  switch($p_mes) {
    case  1: $etq_mes = ObtenEtiqueta(460); break;
    case  2: $etq_mes = ObtenEtiqueta(461); break;
    case  3: $etq_mes = ObtenEtiqueta(462); break;
    case  4: $etq_mes = ObtenEtiqueta(463); break;
    case  5: $etq_mes = ObtenEtiqueta(464); break;
    case  6: $etq_mes = ObtenEtiqueta(465); break;
    case  7: $etq_mes = ObtenEtiqueta(466); break;
    case  8: $etq_mes = ObtenEtiqueta(467); break;
    case  9: $etq_mes = ObtenEtiqueta(468); break;
    case 10: $etq_mes = ObtenEtiqueta(469); break;
    case 11: $etq_mes = ObtenEtiqueta(470); break;
    case 12: $etq_mes = ObtenEtiqueta(471); break;
  }
  return $etq_mes;
}


#
# MRA: Funciones de uso general
#

# Obtiene el nombre de un archivo sin la extension
function ObtenNombreArchivo($p_archivo) {

  $archivo = $p_archivo;
  if(substr_count($archivo, '/') > 0)
    $archivo = substr($archivo, strrpos($archivo, '/')+1);
  if(substr_count($archivo, '.') > 0)
    $archivo = substr($archivo, 0, strpos($archivo, '.'));
  return $archivo;
}

# Obtiene la extension de un nombre de archivo
function ObtenExtensionArchivo($p_archivo) {

  $tokens = array( );
  $tokens = explode(".", $p_archivo);
  $extension = $tokens[count($tokens) - 1];
  return $extension;
}

# Regresa el idioma seleccionado de la sesion
function ObtenIdioma( ) {

  # Revisa el idioma de la sesion, toma el valor por omision si no esta definido
  $cl_idioma = $_COOKIE[IDIOMA_NOMBRE];
  if(empty($cl_idioma))
    $cl_idioma = IDIOMA_DEFAULT;

  # Regresa el idioma
  return $cl_idioma;
}

# Regresa el parametro elegido dependiendo del idioma de la sesion
function EscogeIdioma($p_base, $p_trad) {

  # Revisa el idioma de la sesion
  $cl_idioma = ObtenIdioma( );

  # Regresa el parametro elegido, toma el valor por omision si no esta definido
  if($cl_idioma <> IDIOMA_DEFAULT && !empty($p_trad))
    return $p_trad;
  else
    return $p_base;
}

# Funcion para recuperar una variable de configuracion
function ObtenConfiguracion($p_configuracion) {

  # Recupera la variable de la tabla de configuracion
  $row = RecuperaValor("SELECT ds_valor FROM c_configuracion WHERE cl_configuracion=$p_configuracion");
  return str_uso_normal($row[0]);
}

# Funcion para recuperar etiquetas
function ObtenEtiqueta($p_etiqueta) {

  # Recupera la etiqueta
  $row = RecuperaValor("SELECT ds_etiqueta, tr_etiqueta FROM c_etiqueta WHERE cl_etiqueta=$p_etiqueta");
  return str_uso_normal(EscogeIdioma($row[0], $row[1]));
}

# Funcion para recuperar nombre de archivos de imagen
function ObtenNombreImagen($p_imagen) {

  # Recupera el nombre de la imagen
  $row = RecuperaValor("SELECT nb_archivo, tr_archivo FROM c_imagen WHERE cl_imagen=$p_imagen");
  return str_ascii(EscogeIdioma($row[0], $row[1]));
}

# Funcion para recuperar mensajes
function ObtenMensaje($p_mensaje) {

  # Recupera el texto del mensaje
  if($p_mensaje <> "") {
    $row = RecuperaValor("SELECT ds_mensaje, tr_mensaje FROM c_mensaje WHERE cl_mensaje=$p_mensaje");
    return EscogeIdioma($row[0], $row[1]);
  }
  else
    return "";
}

# Recupera el nombre del programa actual
function ObtenProgramaActual() {

  # Determina el nombre del programa en ejecucion
  $nb_programa = $_SERVER['PHP_SELF'];
  $nb_programa = substr($nb_programa, strrpos($nb_programa, '/')+1);
  return $nb_programa;
}

# Recupera el nombre del programa base
function ObtenProgramaBase() {

  # Determina el nombre del programa en ejecucion
  $nb_programa = ObtenProgramaActual();
  $nb_programa = str_replace(PGM_FORM, '', $nb_programa);
  $nb_programa = str_replace(PGM_INSUPD, '', $nb_programa);
  $nb_programa = str_replace(PGM_INSERT, '', $nb_programa);
  $nb_programa = str_replace(PGM_UPDATE, '', $nb_programa);
  $nb_programa = str_replace(PGM_DELETE, '', $nb_programa);
  $nb_programa = str_replace(PGM_EXPORT, '', $nb_programa);
  $nb_programa = str_replace(PGM_REPORT, '', $nb_programa);
  $nb_programa = str_replace(PGM_SEND, '', $nb_programa);
  return $nb_programa;
}

# Recupera el nombre del programa alterno
function ObtenProgramaNombre($p_nombre) {

  # Determina el nombre del programa para la forma de captura
  $nb_programa = ObtenProgramaBase();
  $lon = strpos($nb_programa, '.');
  $nb_programa = substr($nb_programa, 0, $lon);
  $nb_programa .= $p_nombre.".php";
  return $nb_programa;
}

# Verifica que el formato del email sea valido
function ValidaEmail($p_mail) {

  $p_mail = str_ascii($p_mail);
  $len = strlen($p_mail);
  if($len == 0) // Que no este vacio
    return False;
  if(substr_count($p_mail, '@') != 1) // Que tenga un @
    return False;
  if(substr_count($p_mail, '.') < 1) // Que tenga al menos un .
    return False;
  if(strpos($p_mail, '..')) // Que no tenga ..
    return False;
  if(($p_mail[0] == '@') || ($p_mail[0] == '.')) // Que no empiece con @ ni con .
    return False;
  $ult = $len - 1;
  if($p_mail[$ult] == '@') // Que no termine con @
    return False;
  for($i = 0; $i < $len; $i++) {
    $c = $p_mail[$i];
    if($c >= 'A' && $c <= 'Z') // Puede contener letras A-Z
      continue;
    if($c >= 'a' && $c <= 'z') // Puede contener letras a-z
      continue;
    if($c >= '0' && $c <= '9') // Puede contener numeros 0-9
      continue;
    if($c == '@' || $c == '.' || $c == '_' || $c == '-') // Puede contener @ . _ -
      continue;
    return False;
  }
  return True;
}

# Verifica que sea entero
function ValidaEntero($p_valor, $p_signo=False) {

  if(!is_numeric($p_valor)) // Que sea numerico
    return False;
  $len = strlen($p_valor);
  if($len == 0) // Que no este vacio
    return False;
  for($i = 0; $i < $len; $i++) {
    $c = $p_valor[$i];
    if($c >= '0' && $c <= '9') // Puede contener numeros 0-9
      continue;
    if($i == 0 AND $c == '-' AND $p_signo) // Puede ser negativo si se indica en el parametro
      continue;
    return False;
  }
  return True;
}

# Verifica que sea entero
function ValidaFlotante($p_valor) {

  if(!is_numeric($p_valor)) // Que sea numerico
    return False;
  $len = strlen($p_valor);
  if($len == 0) // Que no este vacio
    return False;
  if(substr_count($p_valor, '.') > 1) // Que tenga solo un .
    return False;
  for($i = 0; $i < $len; $i++) {
    $c = $p_valor[$i];
    if($c >= '0' && $c <= '9') // Puede contener numeros 0-9
      continue;
    if($c == '.') // Puede contener .
      continue;
    return False;
  }
  return True;
}

# Presenta pagina de error
function MuestraPaginaError($p_error=ERR_DEFAULT) {

  # Recupera el usuario de la sesion
  $fl_usuario = ObtenUsuario( );
  if(empty($fl_usuario))
    $fl_usuario = ObtenUsuario(False);
  if(empty($fl_usuario))
    $fl_usuario = 0;

  # Prepara el codigo de error a mostrar al usuario
  EjecutaQuery("UPDATE c_usuario SET cl_mensaje=$p_error WHERE fl_usuario=$fl_usuario");
  header("Location: ".PAGINA_ERROR);
  exit;
}

# Genera un thumbnail en destino para una imagen dada en origen
# Las medidas por omision del thumb son 150x150
# Si se reciben ambas dimensiones se ajusta la imagen sin mantener la proporcion original
# Si recibe solo una dimension, se calculara la otra manteniendo la proporcion de la imagen original
# Si se especifica una dimension de lado fija se inicializa la mayor y se ajusta la menor para no perder la proporcion
# Si se especifica una dimension maxima se reducen ambas dimensiones para no excederla y mantener la proporcion
function CreaThumb($p_origen, $p_destino, $p_ancho=0, $p_alto=0, $p_fija_lado=0, $p_max_lado=0) {

  # Abre el archivo con la imagen original
  $original = imagecreatefromjpeg($p_origen);
  if(!$original)
    return False;
  $ancho_orig = imagesx($original);
  $alto_orig = imagesy($original);
  $ratio_orig = $ancho_orig/$alto_orig;
  if($ancho_orig >= $alto_orig)
    $fg_horizontal = True;
  else
    $fg_horizontal = False;

  # Medidas por omision del thumb
  $ancho = 150;
  $alto = 150;

  # Calcula las dimensiones del thumb en base a una dimension maxima
  if($p_max_lado > 0) {
    if($ancho_orig > $p_max_lado OR $alto_orig > $p_max_lado) {
      if($fg_horizontal) {
        $p_ancho = $p_max_lado;
        $p_alto = 0;
      }
      else {
        $p_alto = $p_max_lado;
        $p_ancho = 0;
      }
      if($p_fija_lado > $p_max_lado)
        $p_fija_lado = $p_max_lado;
    }
    else {
      $p_ancho = $ancho_orig;
      $p_alto = $alto_orig;
    }
  }

  # Fija las dimensiones del thumb
  if($p_ancho > 0 AND $p_alto > 0) {
    $ancho = $p_ancho;
    $alto = $p_alto;
  }

  # Calcula las dimensiones del thumb en base a un ancho fijo
  if($p_ancho > 0 AND $p_alto == 0) {
    $ancho = $p_ancho;
    $alto = $p_ancho/$ratio_orig; // Ajusta el alto
  }

  # Calcula las dimensiones del thumb en base a un alto fijo
  if($p_ancho == 0 AND $p_alto > 0) {
    $alto = $p_alto;
    $ancho = $p_alto*$ratio_orig; // Ajusta el ancho
  }

  # Calcula las dimensiones del thumb en base a una dimension dada por lado
  if($p_fija_lado > 0) {
    $ancho = $p_fija_lado;
    $alto = $p_fija_lado;
    if($fg_horizontal) // Calcula el alto
      $alto = $p_fija_lado/$ratio_orig;
    else // Calcula el ancho
      $ancho = $p_fija_lado*$ratio_orig;
  }

  # Genera la nueva imagen
  $thumb = imagecreatetruecolor($ancho, $alto);
  imagecopyresampled($thumb, $original, 0, 0, 0, 0, $ancho, $alto, $ancho_orig, $alto_orig);
  imagejpeg($thumb, $p_destino, 90);
  return True;
}

# Prepara codigo HTML embeviendo imagenes para enviar por correo
function ConvierteHTMLenMail($p_html, $p_headers, $p_kod='iso-8859-1') {

  preg_match_all('~<img.*?src=.([\/.a-z0-9:_-]+).*?>~si', $p_html, $matches);
  $i = 0;
  $paths = array( );

  foreach ($matches[1] as $img) {
    $img_old = $img;
    if(strpos($img, "http://") == false) {
      $uri = parse_url($img);
      $paths[$i]['path'] = $_SERVER['DOCUMENT_ROOT'].$uri['path'];
      $content_id = md5($img);
      $p_html = str_replace($img_old, 'cid:'.$content_id, $p_html);
      $paths[$i++]['cid'] = $content_id;
    }
  }

  $boundary = "--".md5(uniqid(time( )));
  $headers  = $p_headers;
  $headers .= "MIME-Version: 1.0\n";
  $headers .= "Content-Type: multipart/mixed; boundary=\"$boundary\"\n";
  $multipart  = "--$boundary\n";
  $multipart .= "Content-Type: text/html; charset=$p_kod\n";
  $multipart .= "Content-Transfer-Encoding: 8bit\n\n";
  $multipart .= "$p_html\n\n";

  foreach($paths as $path) {
    if(file_exists($path['path']))
      $fp = fopen($path['path'], "r");
    if(!$fp)
      return false;
    $imagetype = substr(strrchr($path['path'], '.' ), 1);
    $file = fread($fp, filesize($path['path']));
    fclose($fp);
    $message_part = "";
    switch ($imagetype) {
      case 'png':
      case 'PNG':
        $message_part .= "Content-Type: image/png";
        break;
      case 'jpg':
      case 'jpeg':
      case 'JPG':
      case 'JPEG':
        $message_part .= "Content-Type: image/jpeg";
        break;
      case 'gif':
      case 'GIF':
        $message_part .= "Content-Type: image/gif";
        break;
    }
    $message_part .= "; file_name=\"$path\"\n";
    $message_part .= 'Content-ID: <'.$path['cid'].">\n";
    $message_part .= "Content-Transfer-Encoding: base64\n";
    $message_part .= "Content-Disposition: inline; filename=\"".basename($path['path'])."\"\n\n";
    $message_part .= chunk_split(base64_encode($file))."\n";
    $multipart .= "--$boundary\n".$message_part."\n";
  }

  $multipart .= "--$boundary--\n";
  return array('multipart' => $multipart, 'headers' => $headers);
}

# Envia correo con HTML
function EnviaMailHTML($p_from_name, $p_from_mail, $p_to, $p_subject, $p_message, $p_bcc='') {

    $p_message = str_replace("&nbsp;", " ", $p_message);
    $p_message = str_replace("&nbsp", " ", $p_message);


/*
  # Inicializa variables de ambiente para envio de correo
  ini_set("SMTP", MAIL_SERVER);
  ini_set("smtp_port", MAIL_PORT);
  ini_set("sendmail_from", MAIL_FROM);

  $to = str_ascii($p_to);
  $subject = str_ascii($p_subject);
  $headers = "From: $p_from_name<$p_from_mail>\r\nReply-To: $p_from_mail\r\n";
  if(!empty($p_bcc))
    $headers .= "Bcc: $p_bcc\r\n";
  $headers = str_ascii($headers);
  $message = ConvierteHTMLenMail($p_message, $headers);
  return mail($to, $subject, $message['multipart'], $message['headers']);
  */

    #Create an instance; passing `true` enables exceptions
    $mail = new PHPMailer(true);
    $smtphost=ObtenConfiguracion(161);
    $mailfrom=ObtenConfiguracion(162);
    $mailpass=ObtenConfiguracion(163);

    if(empty($p_from_name))
        $p_from_name=$p_to;


    //envia copia a admin@vanas.ca
    $admin = ObtenConfiguracion(83);

    try{

        //Server settings
        $mail->SMTPDebug = false;//SMTP::DEBUG_SERVER;                      //Enable verbose debug output
        $mail->isSMTP();                                            //Send using SMTP
        $mail->Host       = $smtphost;                     //Set the SMTP server to send through
        $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
        $mail->Username   = $mailfrom;                     //SMTP username
        $mail->Password   = $mailpass;                               //SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;            //Enable implicit TLS encryption
        $mail->Port       = 587;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

        //Recipients
        $mail->setFrom($mailfrom, $p_subject);
        $mail->addAddress($p_to, $p_from_name);     //Add a recipient
        $mail->addBCC($admin);//copia oculta forever

        if($p_bcc)
            $mail->addBCC($p_bcc);//copia oculta

        //Attachments
        if($attachment)
            $mail->AddStringAttachment($attachment, $nameAttachment, 'base64', 'application/pdf');// attachment
        //$mail->addAttachment($attachment);         //Add attachments
        //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name

        //Content
        $mail->isHTML(true);                                  //Set email format to HTML
        $mail->Subject = $p_subject;
        $mail->Body    = $p_message;
        //$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
        $mail->send();

        $status=true;

    }
    catch (Exception $e)
    {
        $status=false;
    }


    return $status;


}


#
# MRA: Funciones para manejo de zonas horarias (Utiliza AskGeo, Web API http://www.askgeo.com)
#

# Recupera diferencia de horas entre la zona horaria default y la solicitada (p_zona_horaria)
function RecueperaDiferenciaGMT($p_zona_horaria) {

  # Recupera latitud y longitud de la zona horaria default
  $Query  = "SELECT no_gmt, no_latitude, fg_latitude, no_longitude, fg_longitude ";
  $Query .= "FROM c_zona_horaria ";
  $Query .= "WHERE fg_default='1'";
  $row = RecuperaValor($Query);
  $no_gmt_d = $row[0];
  if($no_gmt_d == '')
    return 0;

  # Recupera latitud y longitud de la zona horaria solicitada
  $Query  = "SELECT no_gmt, no_latitude, fg_latitude, no_longitude, fg_longitude ";
  $Query .= "FROM c_zona_horaria ";
  $Query .= "WHERE fl_zona_horaria=$p_zona_horaria";
  $row = RecuperaValor($Query);
  $no_gmt = $row[0];
  if($no_gmt == '')
    return 0;


  // Finalmente se obtiene la diferencia en horas entre las dos zonas horarias
  $diferencia = $no_gmt - $no_gmt_d;

  return $diferencia;
}

function ActualizaDiferenciaGMT($p_perfil, $p_usuario) {

  # Recupera la diferencia de horario de la zona horaria del usuario
  if($p_perfil == PFL_ESTUDIANTE)
    $row = RecuperaValor("SELECT fl_zona_horaria FROM c_alumno WHERE fl_alumno=$p_usuario");
  else
    $row = RecuperaValor("SELECT fl_zona_horaria FROM c_maestro WHERE fl_maestro=$p_usuario");
  if(!empty($row[0]))
    $diferencia = RecueperaDiferenciaGMT($row[0]);
  else
    $diferencia = 0;

  # Escribe cookie con la diferencia de horario
  setcookie("DIF_GMT", $diferencia, time( )+IDIOMA_VIGENCIA, "/");
  return $diferencia;
}

function RecuperaDiferenciaGMT( ) {

  $diferencia = $_COOKIE["DIF_GMT"];
  if(empty($diferencia))
    $diferencia = 0;
  return $diferencia;
}


# Funcion para obtener los meses que conforma el pago si no existe en k_alumno_pago_det lo inserta en caso contrario solo actualizara
function Meses_X_Pago (){
  $Query  = "SELECT fl_alumno, a.fe_pago, CASE fg_refund WHEN '1' THEN mn_refund-a.mn_pagado ELSE a.mn_pagado END mn_pagado, no_pago, fl_alumno_pago FROM k_alumno_pago a,k_term_pago  b ";
  $Query .= "WHERE a.fl_term_pago=b.fl_term_pago AND NOT EXISTS(SELECT * FROM k_alumno_pago_det t WHERE t.fl_alumno_pago=a.fl_alumno_pago)";
  $rs = EjecutaQuery($Query);
  for($i=0;$row=RecuperaRegistro($rs);$i++){
    $mn_pagado = $row[2];
    $no_pago = $row[3];
    $fl_alumno_pago = $row[4];
    $Query2  = "SELECT e.no_grado,".ConsultaFechaBD('h.fe_inicio', FMT_FECHA).", no_semanas, g.fg_opcion_pago, ";
    $Query2 .= "CASE g.fg_opcion_pago WHEN 1 THEN g.mn_a_due WHEN 2 THEN g.mn_b_due WHEN 3 THEN g.mn_c_due WHEN 4 THEN g.mn_d_due END mn_x_due, ";
    $Query2 .= "CASE g.fg_opcion_pago WHEN 1 THEN g.mn_a_paid WHEN 2 THEN g.mn_b_paid WHEN 3 THEN g.mn_c_paid WHEN 4 THEN g.mn_d_paid END mn_x_paid, e.fl_programa, e.fl_term, ";
    $Query2 .= "CONCAT(b.ds_nombres,' ',b.ds_apaterno, ' ' , b.ds_amaterno) ";
    $Query2 .= "FROM c_usuario b,  c_grupo d, k_term e, c_programa f, k_app_contrato g, c_periodo h, k_programa_costos i ";
    $Query2 .= "WHERE  e.fl_term=(SELECT MIN(fl_term) FROM k_alumno_term s WHERE s.fl_alumno=b.fl_usuario) AND d.fl_term=e.fl_term ";
    $Query2 .= "AND e.fl_programa=f.fl_programa AND b.cl_sesion=g.cl_sesion AND e.fl_periodo=h.fl_periodo ";
    $Query2 .= "AND b.fl_usuario=$row[0] AND g.no_contrato=1  AND f.fl_programa = i.fl_programa  ";
    $row2 = RecuperaValor($Query2);
    $no_grado = $row2[0];
    $fe_inicio_pro = $row2[1];
    $no_semanas = $row2[2];
      $meses_duracion = $no_semanas/4;
    $fg_opcion_pago = $row2[3];
    $mn_x_due = $row2[4];
    $mn_x_paid = $row2[5];
    $fl_programa = $row2[6];
    $fl_term = $row2[7];
    $ds_nombres = $row2[8];
    #obtenemos la fecha del term_ini si el grado es mayor a 1
    if($no_grado <> 1){
      $row3 = RecuperaValor("SELECT fl_term_ini FROM k_term a WHERE fl_term=$fl_term AND fl_programa=$fl_programa AND no_grado=$no_grado ");
      $fl_term_ini = $row3[0];
      $row4 = RecuperaValor("SELECT fe_inicio FROM k_term a, c_periodo b WHERE fl_term=$fl_term_ini AND a.fl_periodo = b.fl_periodo");
      $fe_inicio_pro = $row4[0];
    }

    #numero de pagos, meses que cubre un pago
    if(!empty($fg_opcion_pago) AND $mn_x_due<>0 AND $mn_x_paid<>0){
      $numero_pagos =  $mn_x_paid/$mn_x_due;
      $no_meses_op = $meses_duracion/$numero_pagos; //numero de meses por opcion
      $desfase = ($no_pago-1)*$no_meses_op;
      $nuevafecha = strtotime ( "+ ".$desfase." month", strtotime($fe_inicio_pro));
      $fe_mesini_pago = date ( 'd-m-Y' , $nuevafecha );

      $pago_normal_x_mes = $mn_pagado/$no_meses_op;
      $suma=0;
      for($j=0;$j<=$no_meses_op-1;$j++){
        $mes_ini_pago = RecuperaValor("SELECT ADDDATE('".date('Y-m-d',strtotime($fe_mesini_pago))."', INTERVAL $j MONTH)");
        $mes_ini_pago = $mes_ini_pago[0];
        # Estas son las fechas que esta realmente cubriendo el pago
        $dia = substr($mes_ini_pago,8,10);
        $mes = substr($mes_ini_pago,5,2);
        $anio = substr($mes_ini_pago,0,4);
        $marzo = RecuperaValor("SELECT '$anio-$mes'='2015-03'");
        # Inserta los meses que cubre el pago con el fl_alumno_pago y el mes
        $Insert = "INSERT INTO k_alumno_pago_det(fl_alumno_pago, fe_pago,mn_pagado) VALUES($fl_alumno_pago, '$anio-$mes-$dia', $pago_normal_x_mes)";
        EjecutaQuery($Insert);
      }
    }
  }

  # solo va actualizar los registros que ya se haya ganado
  $rs = EjecutaQuery("SELECT fl_alumno_pago_det, fl_alumno_pago, fe_pago FROM k_alumno_pago_det WHERE fg_earned='0' AND fe_pago<'".date('Y-m-01')."'");
  for($i=0;$row=RecuperaRegistro($rs);$i++){
    $fl_alumno_pago_det = $row[0];
    $fl_alumno_pago = $row[1];
    $fe_pago = $row[2]; // este es el mes que esta cubriendo
    EjecutaQuery("UPDATE k_alumno_pago_det  SET fg_earned = '1' WHERE fl_alumno_pago_det = $fl_alumno_pago_det");
  }

  # Actualizar los ganados y no ganados
  $Query_eu = "SELECT fl_alumno_pago FROM k_alumno_pago a WHERE EXISTS(SELECT *FROM k_alumno_pago_det b WHERE a.fl_alumno_pago=b.fl_alumno_pago)";
  $rs_eu = EjecutaQuery($Query_eu);
  for($k=0;$row_eu=RecuperaRegistro($rs_eu);$k++){
    $fl_alumno_pago = $row_eu[0];
    # Actualizzamos lo ganado
    $Earned  = "UPDATE k_alumno_pago a ";
    $Earned .= "SET mn_earned=(SELECT SUM(mn_pagado) FROM k_alumno_pago_det r WHERE r.fl_alumno_pago=a.fl_alumno_pago AND fg_earned='1') ";
    $Earned .= "WHERE fl_alumno_pago=$fl_alumno_pago";
    EjecutaQuery($Earned);
    # Actualizzamos lo no ganado
    $Unearned  = "UPDATE k_alumno_pago a ";
    $Unearned .= "SET mn_unearned=(SELECT SUM(mn_pagado) FROM k_alumno_pago_det r WHERE r.fl_alumno_pago=a.fl_alumno_pago AND fg_earned='0') ";
    $Unearned .= "WHERE fl_alumno_pago=$fl_alumno_pago";
    EjecutaQuery($Unearned);
    # Actualizzamos la cantidad de ganados y no ganados
    $row_e = RecuperaValor("SELECT COUNT(*) FROM k_alumno_pago_det WHERE fl_alumno_pago=$fl_alumno_pago AND fg_earned='1' ");
    $no_earned = $row_e[0];
    $row_t = RecuperaValor("SELECT COUNT(*) FROM k_alumno_pago_det WHERE fl_alumno_pago=$fl_alumno_pago ");
    $total = $row_t[0];
    EjecutaQuery("UPDATE k_alumno_pago SET ds_eu='$no_earned/$total' WHERE fl_alumno_pago=$fl_alumno_pago");
  }

}

function NombreArchivoDecente($p_nombre) {

  # Sustituye caracteres especiales
  $cadena = str_ascii($p_nombre);
  $cadena = str_replace(chr(225), "a", $cadena);
  $cadena = str_replace(chr(193), "A", $cadena);
  $cadena = str_replace(chr(233), "e", $cadena);
  $cadena = str_replace(chr(201), "E", $cadena);
  $cadena = str_replace(chr(237), "i", $cadena);
  $cadena = str_replace(chr(205), "I", $cadena);
  $cadena = str_replace(chr(243), "o", $cadena);
  $cadena = str_replace(chr(211), "O", $cadena);
  $cadena = str_replace(chr(250), "u", $cadena);
  $cadena = str_replace(chr(218), "U", $cadena);
  $cadena = str_replace(chr(202), "", $cadena);
  $cadena = str_replace(chr(234), "", $cadena);
  $cadena = str_replace(chr(252), "u", $cadena);
  $cadena = str_replace(chr(220), "U", $cadena);
  $cadena = str_replace(chr(241), "n", $cadena);
  $cadena = str_replace(chr(209), "N", $cadena);
  $cadena = str_replace("\"", "", $cadena);
  $cadena = str_replace("'", "", $cadena);
  $cadena = str_replace("=", "", $cadena);
  $cadena = str_replace(" ", "_", $cadena);
  return($cadena);
}
// New Function Added by Ulises for substitute common words in different languges
function replaceLangWords($string, $langselect) {

  $search = array('hours', 'minutes', 'Certificate', 'English');

  switch ($langselect) {
    case '1':
      $replace = array('horas', 'minutos', 'Certificado', 'Ingles');
      $string = str_replace($search, $replace, $string);
      return $string;
      break;

    case '2':
      return $string;
      break;

    case '3':
      $replace = array('heures', 'minutes', 'Certificat', 'Anglais');
      $string = str_replace($search, $replace, $string);
      return $string;
      break;

    default:
      return $string;
      break;
  }
}

?>