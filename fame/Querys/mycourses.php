<?php

# Libreria de funciones
require("../lib/self_general.php");

# Verifica que exista una sesion valida en el cookie y la resetea
$fl_usuario = ValidaSesion(False, 0, True);

# Verifica que el usuario tenga permiso de usar esta funcion
if (!ValidaPermisoSelf(FUNC_SELF)) {
  MuestraPaginaError(ERR_SIN_PERMISO);
  exit;
}

# Variable initialization to avoid errors
$registros = NULL;

$sufix = langSufix();

# Obtenemo el instituto
$fl_instituto = ObtenInstituto($fl_usuario);
$fl_perfil_sp = ObtenPerfilUsuario($fl_usuario);

$Que = "SELECT fg_b2c FROM c_usuario WHERE fl_usuario=$fl_usuario ";
$ro = RecuperaValor($Que);
$fg_b2c_ = $ro[0];

# Obtenemos los cursos del usuario
$Query  = "SELECT kup.fl_programa_sp, cp.nb_programa" . $sufix . ", cp.nb_thumb, kup.ds_progreso, kup.no_promedio_t, fg_terminado, fg_certificado, ";
$Query .= "ma.ds_ruta_avatar, CONCAT(ds_nombres,' ', ds_apaterno) teacher_name, ";
$Query .= "CASE fg_status_pro WHEN '1' THEN '<b class=\'text-warning\'><i class=\'fa fa-pause\'></i> Pause</b>' END fg_status_pro, ";
$Query .= "kup.fg_pagado, DATE_FORMAT(kup.fe_pagado, '%b %d, %Y') fecha_pagado, kup.fg_status, kup.fl_maestro, fg_status_pro, ";
$Query .= "CASE fg_grade_tea WHEN '1' THEN ROUND((no_prom_quiz+no_prom_teacher)/2,1) ELSE no_prom_quiz  END no_promedio,kup.fe_creacion,kup.fe_inicio_programa, kup.fl_playlist, us.fl_instituto,kup.fl_usu_pro  ";
$Query .= "FROM k_usuario_programa kup ";
$Query .= "LEFT JOIN c_programa_sp cp ON(kup.fl_programa_sp = cp.fl_programa_sp) ";
$Query .= "LEFT JOIN c_usuario us ON(us.fl_usuario= kup.fl_maestro) ";
$Query .= "LEFT JOIN c_maestro ma ON(ma.fl_maestro=kup.fl_maestro) ";
$Query .= "LEFT JOIN k_details_usu_pro DUP ON DUP.fl_usu_pro=kup.fl_usu_pro ";
$Query .= "WHERE fl_usuario_sp=$fl_usuario AND kup.fl_programa_sp!=0 /*AND us.fl_instituto=$fl_instituto*/ ";
//if($fg_b2c_)//todos loa alumno mostrarn los cursos asignados esten o no en progreso. MJD #10-DIC-2019
//  $Query .="AND ds_progreso>=1 ";
//$Query.="ORDER BY kup.fl_usu_pro DESC ";
$Query .= "ORDER BY kup.fl_playlist, fe_creacion DESC ";

$rs = EjecutaQuery($Query);
$numeroderegistros = CuentaRegistros($rs);

for ($i = 1; $row = RecuperaRegistro($rs); $i++) {

  // start random string
  $randString = rand(1000000, 9000000);
  $randString = base64_encode($randString);


  $fl_programa_sp = $row['fl_programa_sp'];
  $nb_programa = $row[1];
  //$nb_programa = htmlentities($row[1], ENT_QUOTES, "UTF-8");
  $nb_thumb = $row['nb_thumb'];
  $ds_progreso = $row['ds_progreso'];
  $no_promedio_t = !empty($row['no_promedio_t']) ? round($row['no_promedio_t']) : ObtenPromedioPrograma($fl_programa_sp, $fl_usuario);
  $fl_usu_pro = $row['fl_usu_pro'];
  $clave = $row['fl_usu_pro'];

  #Verificamos si es solo es quiz o manual assesment.
  $row00 = RecuperaValor("SELECT fg_quizes, fg_grade_tea,no_prom_quiz FROM k_details_usu_pro WHERE fl_usu_pro=$fl_usu_pro");
  $fg_quizes = $row00[0] ?? NULL;
  $fg_grade_tea = $row00[1] ?? NULL;
  $no_promedio_solo_quiz = $row00['no_prom_quiz'];

  if (empty($fg_grade_tea)) {
    $no_promedio_t = $no_promedio_solo_quiz;
  }

  # DO NOT USE THIS OVERWRITES THE PREVIOUS VALUE
  // $no_promedio_t = round($row[15]);

  $fl_playlist = !empty($row['fl_playlist']) ? $row['fl_playlist'] : 'na';
  $fl_instituto_prog = $row['fl_instituto'];

  $Query = "SELECT ds_foto, ds_instituto FROM c_instituto WHERE fl_instituto = " . $fl_instituto_prog;
  $inst_data = RecuperaValor($Query);

  if (empty($inst_data['ds_foto']))
    $ds_foto_instituto = "/fame/img/Partner_School_Logo.jpg";
  else
    $ds_foto_instituto = "/fame/site/uploads/" . $fl_instituto_prog . "/" . $inst_data[0];

  $img_inst = '<br><div style=\'display: flex;\'><img src=\'' . $ds_foto_instituto . '\' alt=\'\' style=\'height:30px;\' rel=\'tooltip\' data-placement=\'top\' data-html=\'true\' data-original-title=\'' . (!empty($inst_data[1]) ? $inst_data[1] : NULL) . '\'>';

  # Recupera el nombre del playlist
  $Query = RecuperaValor("SELECt nb_playlist FROM c_playlist WHERE fl_playlist =" . $fl_playlist . " LIMIT 1");
  $fl_playlistName = !empty($Query[0]) ? $Query[0] : NULL;

  # Define si lleva o no el tag Playlist
  $playlist_tag = !empty($row['fl_playlist']) ? 'Playlist: ' . $fl_playlistName . '<br>' : '';

  # Recupera el orden en el playlist
  $Query = RecuperaValor("SELECT no_orden FROM k_playlist_course WHERE fl_playlist_padre =" . $fl_playlist . " AND fl_programa_sp = " . $fl_programa_sp . " LIMIT 1 ");
  $Order = !empty($Query['no_orden']) ? $Query['no_orden'] : NULL;

  # Recupera el orden anterior del playlist
  $prevPlaylistOrder = empty($Order) ? 0 : $Order - 1;

  $Query = RecuperaValor("SELECT play.fl_programa_sp FROM k_playlist_course play JOIN k_usuario_programa usu ON(play.fl_programa_sp = usu.fl_programa_sp) WHERE play.fl_playlist_padre = " . $fl_playlist . "  AND fl_usuario_sp = " . $fl_usuario . " AND play.no_orden = " . $prevPlaylistOrder);

  $fl_programa_previo = !empty($Query[0]) ? $Query[0] : NULL;

  # Crea el tag del orden del playlist
  $playlistOrder = empty($Order) ? '<span>No</span>' : "<span class='badge bg-color-pink'>" . ltrim($Order, 0) . "</span>";

  # Revisa si se tiene pre requisitos
  $Query = RecuperaValor("SELECT IF ((SELECT ds_progreso FROM k_playlist_course play JOIN k_usuario_programa usu ON(play.fl_programa_sp = usu.fl_programa_sp) WHERE play.fl_playlist_padre = " . $fl_playlist . "  AND fl_usuario_sp = " . $fl_usuario . " AND play.no_orden = " . $prevPlaylistOrder . ")<100, 'YES', 'NO') AS respuesta;");
  $prereq = !empty($Query[0]) ? $Query[0] : NULL;

  $Query = RecuperaValor("SELECT fg_editable FROM c_playlist WHERE fl_playlist = $fl_playlist");
  $follow_order = !empty($Query[0]) ? $Query[0] : NULL;

  if ($follow_order == 1) {
    $prereq = 'NO';
    $playlistOrder = '<span>No</span>';
  }

  if ($fl_programa_sp == 33)
    $no_promedio_t = round($ds_progreso);

  $fe_creacion = $row['fe_creacion'];
  $fe_inicio_programa = $row['fe_inicio_programa'];

  $fe_inicio_pro = GeneraFormatoFecha($fe_creacion);

  if (empty($fe_creacion))
    $fe_inicio_pro = GeneraFormatoFecha($fe_inicio_programa);

  # Obtenemos el GPA
  $Query = "SELECT cl_calificacion, fg_aprobado FROM c_calificacion WHERE no_min <= ROUND($no_promedio_t) AND no_max >= ROUND($no_promedio_t)";
  $prom_t = RecuperaValor($Query);
  $cl_calificacion = $prom_t['cl_calificacion'];
  $fg_aprbado_grl = $prom_t['fg_aprobado'];

  if (!empty($fg_aprbado_grl))
    $GPA = "success";
  else
    $GPA = "danger";

  $fg_terminado = $row['fg_terminado'];
  $fg_certificado = $row['fg_certificado'];
  $ds_ruta_avatar = $row['ds_ruta_avatar'];
  $ds_name_teacher = $row['teacher_name'];
  $fg_status_pro = $row[9];

  if (empty($fg_status_pro))
    $fg_status_pro = 0;

  $fg_pagado = $row['fg_pagado'];
  $fe_pagado = $row['fecha_pagado'];
  $fg_status = $row['fg_status'];
  $fl_maestro = $row['fl_maestro'];
  $fg_status_proo = $row[14];

  if (!empty($fg_certificado))
    $class_cert = "disabled";

  # Botones del listado
  if (empty($ds_progreso))
    #Revisa si se tien pre requisito del playlist
    switch ($prereq) {
      case 'YES':
        //$status = "<button class='btn btn-sm' onclick='reqfunction();'><i class='fa fa-ban'></i> ".ObtenEtiqueta(2055)." </button>";
        // $status = "<button id='".$nb_programa."' type='button' class='btn btn-sm' data-toggle='modal' data-target='#requiredModal'><i class='fa fa-ban'></i> ".ObtenEtiqueta(2055)."</button>";
        $status = "<button class='btn-secondary btn-sm' onclick='required($prevPlaylistOrder, $fl_playlist, $fl_programa_previo)'><i class='fa fa-hand-paper-o'></i> " . ObtenEtiqueta(2055) . "</button>";
        break;

      default:
        $status = "<a class='btn btn-success btn-sm' href='index.php#site/desktop.php?fl_programa=$fl_programa_sp'><i class='fa fa-eye'></i> " . ObtenEtiqueta(1149) . " </a>";
        break;
    }
  else {
    if ($ds_progreso > 0 and $ds_progreso < 100)
      $status = "<a class='btn btn-warning btn-sm' href='index.php#site/desktop.php?fl_programa=$fl_programa_sp'><i class='fa fa-eye'></i> " . ObtenEtiqueta(1095) . "</a>";
    else
      $status = "<a class='btn btn-primary btn-sm' href='index.php#site/desktop.php?fl_programa=$fl_programa_sp'><i class='fa fa-thumbs-up'></i>  " . ObtenEtiqueta(1096) . "</a>";
  }
  if (!empty($fg_status_pro))
    $status = "<a class='btn btn-warning btn-sm' href='javascript:user_pause(" . $fg_status_proo . "," . $fl_programa_sp . "," . $fl_usuario . ");'><i class='fa fa-pause'></i> PAUSE</a>";

  switch ($fg_status) {
      // CASE "RD": $status_color="warning"; $status_txt = "<a href='javascript:Modal_Certificado(".$fl_programa_sp.");' class='disabled'>".ObtenConfiguracion(98)."</a>"; break;
    case "RD":
      $status_color = "success";
      $status_txt = ObtenConfiguracion(98);
      break;
    case "RT":
      $status_color = "info";
      $status_txt = ObtenConfiguracion(99);
      break;
    case "SD":
      $status_color = "success";
      $status_txt = ObtenConfiguracion(100);
      break;
    default:
      $status_color = "primary";
      $status_txt = ObtenConfiguracion(97);
      break;
  }

  if (empty($fg_pagado)) {
    if (empty($fg_terminado))
      $certificado = "<span class='label label-warning'>" . ObtenEtiqueta(1097) . "</span>";
    else
      $certificado = "<span class='label'><a href='https://go.myfame.org/fame/fame_transcript_frm.php?aGFzUGFzc3dvcmQ=1&c=".$clave."&u=".$fl_usuario."&i=".$fl_instituto."&p=".$fl_programa_sp."".$randString."' target='_blank' class='btn btn-" . $status_color . " btn-sm'>" . $status_txt . "</a></span>";
  } else {
    $certificado = "<label class='text-" . $status_color . "'><strong>" . ObtenEtiqueta(1151) . ":<br>" . $fe_pagado . "</strong><br>(" . $status_txt . ")</label>";
  }

  # Ruta de la imagen del programa
  $ruta_thumb = PATH_HOME . '/modules/fame/uploads/' . $nb_thumb;

  # Ruta de la foto del teacher
  if (!empty($fl_maestro))
    $ds_ruta_foto_tec = ObtenAvatarUsuario($fl_maestro);
  else
    $ds_ruta_foto_tec = $ds_ruta_avatar;

  # Si al alumno no ha recibido una calificacion no maracara nada
  if (!empty($ds_progreso))
    $calificacion = "<span class='label label-" . $GPA . "'>" . $cl_calificacion . " (" . $no_promedio_t . "%)</span>";
  else
    $calificacion = ObtenEtiqueta(1039);

  /** JSON CREATION FOR RESPONSE **/
  $registro = (object)[
    'checkbox' => '<label class=\'checkbox no-padding no-margin\'><input class=\'checkbox\' type=\'checkbox\' id=\'ch_' . $i . '\' value=\'' . $fl_usuario . '\'></label><small class=\'text-muted\' style=\'visibility: hidden;\'>' . $fl_playlist . '-' . $Order . '</small>',
    'id' => '<div class=\'project-members\'><a href=\'javascript:void(0)\' rel=\'tooltip\' data-placement=\'top\' data-html=\'true\' data-original-title=\'' . $nb_programa . '\'><img src=\'' . $ruta_thumb . '\'  style=\'display:block; margin:auto; width:80%;\'></a></div>',
    'nb_programa' => $nb_programa . '<br><small class=\'text-muted\'>' . $playlist_tag . 'Course assigned on: ' . $fe_inicio_pro . '</small>' . $img_inst,
    'ds_progreso' => '<div class=\'progress progress-xs\' data-progressbar-value=\'' . $ds_progreso . '\'><div class=\'progress-bar\'></div></div> ' . $fg_status_pro,
    'no_promedio_t' => $calificacion,
    'status' => $status,
    'fg_certificado' => $certificado,
    'fl_programa_sp' => $fl_programa_sp . ' ALL',
    'ds_ruta_foto_tec' => '<div class=\'project-members\'><a href=\'javascript:void(0)\' rel=\'tooltip\' data-placement=\'top\' data-html=\'true\' data-original-title=\'' . $ds_name_teacher . '\'><img src=\'' . $ds_ruta_foto_tec . '\'  style=\'display:block; margin:auto; width:50%;\'></a> </div>',
    'assigment' => '<a class=\'btn btn-success btn-sm\' href=\'index.php#site/desktop_upload.php?fl_programa=' . $fl_programa_sp . '\'><i class=\'fa fa-upload\'></i> Upload</a>',
    'order' => $playlistOrder
  ];

  if ($i <= ($numeroderegistros - 1))
    $registros .= json_encode($registro) . ', ';
  else
    $registros .= json_encode($registro);
}

# End of MAIN FOR LOOP only for PRODUCCION
$tablaJson = '{"data": [' . $registros . ']}';

# Entrega el resultado para mostrar en la tabla solo PRODUCCION
echo $tablaJson;
