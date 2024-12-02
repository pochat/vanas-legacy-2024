<?php
  
  # Libreria de funciones
  require '../../lib/general.inc.php';
  require '../../../modules/liveclass/bbb_api.php';
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  $fl_usuario = ValidaSesion( );
  
  # Recibe parametros
  $clave = RecibeParametroNumerico('clave');
  $fg_error = RecibeParametroNumerico('fg_error');
  
  # Determina si es alta o modificacion
  if(!empty($clave))
    $permiso = PERMISO_DETALLE;
  else
    $permiso = PERMISO_ALTA;
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermiso(FUNC_GRUPOS, $permiso)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Inicializa variables
  if(!$fg_error) { // Sin error, viene del listado
    if(!empty($clave)) { // Actualizacion, recupera de la base de datos
      $Query  = "SELECT a.fl_term, fl_maestro, ds_login, nb_grupo, fl_programa, fl_periodo, no_grado ";
      $Query .= "FROM c_grupo a, c_usuario b, k_term c ";
      $Query .= "WHERE a.fl_maestro=b.fl_usuario ";
      $Query .= "AND a.fl_term=c.fl_term ";
      $Query .= "AND fl_grupo=$clave";
      $row = RecuperaValor($Query);
      $fl_term = $row[0];
      $fl_maestro = $row[1];
      $ds_login = str_texto($row[2]);
      $nb_grupo = str_texto($row[3]);
      $fl_programa = $row[4];
      $fl_periodo = $row[5];
      $no_grado = $row[6];
    }
    else { // Alta, inicializa campos
      $fl_term = "";
      $fl_maestro = "";
      $ds_login = "";
      $nb_grupo = "";
      $fg_dia_sesion = 1;
    }
    $fl_term_err = "";
    $fl_maestro_err = "";
    $nb_grupo_err = "";
  }
  else { // Con error, recibe parametros (viene de la pagina de actualizacion)
    $fl_term = RecibeParametroNumerico('fl_term');
    $fl_term_err = RecibeParametroNumerico('fl_term_err');
    $fl_maestro = RecibeParametroNumerico('fl_maestro');
    $fl_maestro_err = RecibeParametroNumerico('fl_maestro_err');
    $ds_login = RecibeParametroHTML('ds_login');
    $nb_grupo = RecibeParametroHTML('nb_grupo');
    $nb_grupo_err = RecibeParametroNumerico('nb_grupo_err');
    $fl_programa = RecibeParametroNumerico('fl_programa');
    $fl_periodo = RecibeParametroNumerico('fl_periodo');
    $no_grado = RecibeParametroNumerico('no_grado');
    $fg_dia_sesion = RecibeParametroNumerico('fg_dia_sesion');
  }
  
  # Presenta forma de captura
  PresentaHeader( );
  PresentaEncabezado(FUNC_GRUPOS);
  
  # Funciones para manejo de sesiones en vivo para lecciones
  echo "<script src='".PATH_JS."/frmGroups.js.php'></script>";
  
  # Inicia forma de captura
  Forma_Inicia($clave, True);
  if($fg_error)
    Forma_PresentaError( );
  
  # Campos de captura
  $concat = array('nb_programa', "' ('", 'ds_duracion', "')'", "' - '", 'nb_periodo', "' - ".ObtenEtiqueta(375)." '", 'no_grado');
  if(empty($clave)) {    
    $Query  = "SELECT ".ConcatenaBD($concat)." 'nb_term', fl_term ";
    $Query .= "FROM k_term a, c_programa b, c_periodo c ";
    $Query .= "WHERE a.fl_programa=b.fl_programa ";
    $Query .= "AND a.fl_periodo=c.fl_periodo ";
    $Query .= "AND fg_activo='1' AND b.fg_archive='0' ";
    $Query .= "ORDER BY nb_programa, no_grado";
    Forma_CampoSelectBD(ObtenEtiqueta(422), False, 'fl_term', $Query, $fl_term);
  }
  else {
    $Query  = "SELECT ".ConcatenaBD($concat)." 'nb_term' ";
    $Query .= "FROM k_term a, c_programa b, c_periodo c ";
    $Query .= "WHERE a.fl_programa=b.fl_programa ";
    $Query .= "AND a.fl_periodo=c.fl_periodo AND b.fg_archive='0' ";
    $Query .= "AND fl_term=$fl_term";
    $row = RecuperaValor($Query);
    Forma_CampoInfo(ObtenEtiqueta(422), $row[0]);
    Forma_CampoOculto('fl_term', $fl_term);
  }
  Forma_Espacio( );
  Forma_CampoLOV(ObtenEtiqueta(421), True, 'fl_maestro', $fl_maestro, 'ds_login', $ds_login, 20, 
      LOV_MAESTROS, LOV_TIPO_RADIO, LOV_CHICO, '', $fl_maestro_err);
  Forma_Espacio( );
  Forma_CampoTexto(ObtenEtiqueta(420), True, 'nb_grupo', $nb_grupo, 50, 20, $nb_grupo_err);
  Forma_Espacio( );
  if(empty($clave))
  {
    $opc = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');
    $val = array(1, 2, 3, 4, 5, 6, 7);
    Forma_CampoSelect(ObtenEtiqueta(427), False, 'fg_dia_sesion', $opc, $val, $fg_dia_sesion);
    Forma_Espacio( );
  }
  
  # Asignacion de alumnos
  if(!empty($clave)) {
    $tit = array(ETQ_SELECCIONAR.'|center', ObtenEtiqueta(424), ETQ_NOMBRE, ObtenEtiqueta(426));
    $ancho_col = array('15%', '25%', '35%', '25%');
    Forma_Tabla_Ini('60%', $tit, $ancho_col);
    $Query  = "SELECT fl_usuario, ds_login, ";
    $concat = array('ds_nombres', "' '", 'ds_apaterno', "' '", NulosBD('ds_amaterno', ''));
    $Query .= ConcatenaBD($concat)." '".ETQ_NOMBRE."' ";
    $Query .= "FROM c_usuario a, k_ses_app_frm_1 b ";
    $Query .= "WHERE a.cl_sesion=b.cl_sesion ";
    $Query .= "AND a.fl_perfil=".PFL_ESTUDIANTE." ";
    $Query .= "AND b.fl_programa=$fl_programa ";
    $Query .= "AND b.fl_periodo=$fl_periodo ";
    $Query .= "ORDER BY ds_login";
    $rs = EjecutaQuery($Query);
    for($tot_alumnos = 0; $row = RecuperaRegistro($rs); $tot_alumnos++) {
      $Query  = "SELECT fl_alumno, a.fl_grupo, nb_grupo ";
      $Query .= "FROM k_alumno_grupo a, c_grupo b ";
      $Query .= "WHERE a.fl_grupo=b.fl_grupo ";
      $Query .= "AND fl_alumno=$row[0]";
      $row2 = RecuperaValor($Query);
      if($tot_alumnos % 2 == 0)
        $clase = "css_tabla_detalle";
      else
        $clase = "css_tabla_detalle_bg";
      if($row2[1] == $clave)
        $incluido = 1;
      else
        $incluido = 0;
      echo "
      <tr class='$clase'>
        <td align='center'>";
      CampoCheckbox('fl_alumno_'.$tot_alumnos, $incluido, '', $row[0]);
      echo "</td>
        <td>$row[1]</td>
        <td>$row[2]</td>
        <td>$row2[2]</td>
      </tr>\n";
    }
    Forma_Tabla_Fin( );
    Forma_CampoOculto('tot_alumnos', $tot_alumnos);
    Forma_CampoOculto('fl_programa', $fl_programa);
    Forma_CampoOculto('fl_periodo', $fl_periodo);
    Forma_CampoOculto('no_grado', $no_grado);
    Forma_Espacio( );
  }
  else
    Forma_CampoOculto('tot_alumnos', 0);
  
  # Fechas iniciales para cada semana
  if(!empty($clave)) {
    Forma_Doble_CampoDivAjax('div_lecciones', $clave, $fg_error, 0, $p_func_ini='');
    Forma_Espacio( );
  }
  
  
  # Verifica que el usuario tenga permiso para guardar los cambios (solo en modo modificacion)
  if($permiso == PERMISO_DETALLE)
    $fg_guardar = ValidaPermiso(FUNC_GRUPOS, PERMISO_MODIFICACION);
  else
    $fg_guardar = True;
  Forma_Termina($fg_guardar);
  
  # Pie de Pagina
  PresentaFooter( );
  
?>