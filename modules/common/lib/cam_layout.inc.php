<?php

#
# Funciones generales de despliegue para el Modulo de Estudiantes
#

# Inicio de pagina
function PresentaInicioPagina( ) {
  
  echo "
<!--DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'-->
<html xmlns='http://www.w3.org/1999/xhtml'>
<head>
  <meta http-equiv='cache-control' content='max-age=0' />
  <meta http-equiv='cache-control' content='no-cache' />
  <meta http-equiv='expires' content='0'>
  <meta http-equiv='pragma' content='no-cache'>
  <title>Vancouver Animation Online Campus</title>\n
  <link type='text/css' href='".PATH_COM_CSS."/theme/jquery-ui-1.8rc3.custom.css' rel='stylesheet' />
  <link type='text/css' href='".PATH_COM_CSS."/campus.css' rel='stylesheet' />
  <link type='text/css' href='".PATH_COM_CSS."/demos.css' rel='stylesheet' />
  <link type='text/css' href='".PATH_COM_CSS."/fileuploader.css' rel='stylesheet' />
  <link type='text/css' href='".PATH_LIB."/js_mediaelement/mediaelementplayer.css' rel='stylesheet' />
  <link type='text/css' href='".PATH_COM_CSS."/jquery.jqzoom.css' rel='stylesheet' />
  <script type='text/javascript' src='".PATH_ADM_JS."/tiny_mce/tiny_mce.js'></script>
  <script type='text/javascript' src='".PATH_JS."/AC_RunActiveContent.js'></script>
  <script type='text/javascript' src='".PATH_JS."/swfobject.js'></script>
  <script type='text/javascript' src='".PATH_COM_JS."/2leveltab.js'></script>
  <script type='text/javascript' src='".PATH_COM_JS."/fileuploader.js'></script>
  <script type='text/javascript' src='".PATH_COM_JS."/jquery.MultiFile.js'></script>
  <script type='text/javascript' src='".PATH_COM_JS."/jquery-1.4.2.js'></script>
  <script type='text/javascript' src='".PATH_COM_JS."/jquery-ui-1.8rc3.custom.min.js'></script>
  <script type='text/javascript' src='".PATH_COM_JS."/jquery.ui.widget.js'></script>
  <script type='text/javascript' src='".PATH_COM_JS."/jquery.ui.mouse.js'></script>
  <script type='text/javascript' src='".PATH_COM_JS."/jquery.ui.slider.js'></script>
  <script type='text/javascript' src='".PATH_COM_JS."/jquery.jqzoom-core.js'></script>
  <script type='text/javascript' src='".PATH_COM_JS."/frmStreamingVideo.js.php'></script>
</head>
<body leftmargin='0' topmargin='0' marginwidth='0' marginheight='0' class='site_background'>
  <div id='dlg_video'><div id='dlg_video_content'></div></div>";
}


# Header para todas las paginas
function PresentaHeader($titulo, $p_alumno='') {
 
  $fl_usuario = ObtenUsuario(False);
  $fl_perfil = ObtenPerfil($fl_usuario);
  $nb_usuario = ObtenNombreUsuario($fl_usuario);
  $ruta_avatar = ObtenAvatarUsuario($fl_usuario);
  
  # Revisa si se esta visualizando el escritorio de un alumno (para desplegar status)
  if(empty($p_alumno))
    $fl_alumno = $fl_usuario;
  else
    $fl_alumno = $p_alumno;
  
  PresentaInicioPagina( );
  echo "
      <table border='".D_BORDES."' cellpadding='0' cellspacing='0' align='left' width='1104'>
        <tr>
          <td colspan='3' class='header_border'>
            <table border='".D_BORDES."' cellpadding='0' cellspacing='0' width='100%'>
              <tr>
                <td width='795' height='100' class='header'>
                  <img src='".SP_IMAGES."/".ObtenNombreImagen(204)."' border='none'/>
                </td>
                <td width='32' class='notifications'>";
  PresentaNotificacion(1, $fl_usuario, $fl_perfil);
  echo "
                </td>
                <td width='32' class='notifications'>";
  PresentaNotificacion(2, $fl_usuario, $fl_perfil);
  echo "
                </td>
                <td width='32' class='notifications'>";
  PresentaNotificacion(3, $fl_usuario, $fl_perfil);
  echo "
                </td>
                <td width='10' class='notifications'>&nbsp;</td>
                <td class='header_menu'>
                  <table border='".D_BORDES."' cellpadding='0' cellspacing='0' width='100%' class='header_menu'>
                    <tr>
                      <td colspan='4' align='right'>
                        <a href='profile.php' class='header_links'>Welcome<br>$nb_usuario</a>
                      </td>
                      <td rowspan='2' width='5'>&nbsp;</td>
                      <td rowspan='2' width='80' height='104'>
                        <a href='profile.php' title='My profile'><img src='$ruta_avatar' border='none'/></a>
                      </td>
                    </tr>
                    <tr>
                      <td height='24' width='15' class='notifications'></td>
                      <td class='notifications'>";
  if($fl_perfil == PFL_MAESTRO)
    echo "<a href='blog.php'>";
  else
    echo "<a href='desktop.php'>";
  echo "<img src='".SP_IMAGES."/".ObtenNombreImagen(211)."' width='16' height='16' border='0' title='Home'></a></td>
                      <td class='notifications'><a href='community.php'><img src='".SP_IMAGES."/".ObtenNombreImagen(212)."' width='16' height='16' border='0' title='Community'></a></td>
                      <td class='notifications'><a href='".PAGINA_SALIR."' class='header_links'><img src='".SP_IMAGES."/".ObtenNombreImagen(216)."' width='16' height='16' border='0' title='Logout'></a></td>
                    </tr>
                  </table>
                </td>
              </tr>
            </table>
          </td>
        </tr>";
  
  # Presenta menu en Columna izquierda
  if($fl_perfil == PFL_MAESTRO) {
    $menu = MENU_MAESTROS;
    $path_nodo = PAGINA_NOD_MAE;
    $pag_fija = 6;
  }
  else {
    $menu = MENU_ALUMNOS;
    $path_nodo = PAGINA_NOD_ALU;
    $pag_fija = 4;
    # Obtenemos si el alumno ya se graduo o no jgfl
    # Si se graduo muestra solo el payment history
    # En caso contrario muestra todo el menu
    $Query  = "SELECT fg_graduacion, fg_activo FROM k_pctia a, c_usuario b ";
    $Query .= "WHERE a.fl_alumno=b.fl_usuario AND b.fl_perfil= ".PFL_ESTUDIANTE." AND fl_alumno = $fl_usuario ";
    $row = RecuperaValor($Query);
    $fg_activo = $row[1];
    if(!empty($fg_activo)){
      $fl_moduloo = "";
      $fl_funcionn = "";
    }
    else{
      $fl_moduloo = "AND fl_modulo='27' ";
      $fl_funcionn = "AND fl_funcion = 98 ";
    }
  }
  
  # Recupera las descripciones de los modulos
  $Query  = "SELECT fl_modulo, nb_modulo, tr_modulo ";
  $Query .= "FROM c_modulo ";
  $Query .= "WHERE fl_modulo_padre=$menu ";
  $Query .= " ".$fl_moduloo." AND fg_menu='1' "; //jgfl
  $Query .= "ORDER BY no_orden";
  $rs = EjecutaQuery($Query);
  for($i = 1; $row = RecuperaRegistro($rs); $i++) {
    $fl_modulo[$i] = $row[0];
    $nb_modulo[$i] = str_texto(EscogeIdioma($row[1], $row[2]));
    $Query  = "SELECT fl_funcion, nb_funcion, tr_funcion, nb_flash_default, tr_flash_default ";
    $Query .= "FROM c_funcion ";
    $Query .= "WHERE fl_modulo=$fl_modulo[$i] ";
    $Query .= "AND fg_menu='1' ".$fl_funcionn." "; //jgfl
    $Query .= "ORDER BY no_orden";
    $rs2 = EjecutaQuery($Query);
    for($j = 1; $row2 = RecuperaRegistro($rs2); $j++) {
      $fl_funcion[$i][$j] = $row2[0];
      $nb_funcion[$i][$j] = str_texto(EscogeIdioma($row2[1], $row2[2]));
      $nb_icono[$i][$j] = str_uso_normal(EscogeIdioma($row2[3], $row2[4]));
    }
    $tot_submodulos[$i] = $j-1;
  }
  $tot_modulos = $i-1;
  
  # Recupera contenido de la pagina fija
  $Query  = "SELECT ds_contenido, tr_contenido ";
  $Query .= "FROM c_pagina ";
  $Query .= "WHERE cl_pagina=$pag_fija";
  $row = RecuperaValor($Query);
  $contenido = str_uso_normal(EscogeIdioma($row[0], $row[1]));
  
  // MDB Livesession
  $liveSessionDisplay = "";
  $clavesModulosTeacher = Array();
  $clavesModulosTeacher["liveSession"] = 76;
  $clavesModulosTeacher["desk"] = 13;
  $clavesModulosStudent = Array();
  $clavesModulosStudent["liveSession"] = 45;
  $clavesModulosStudent["desk"] = 16;
  
  # RDG Recupera temas de foro
  $rs = EjecutaQuery("SELECT fl_tema, nb_tema, ds_ruta_imagen FROM c_f_tema ORDER BY no_orden");
  $t = 0;
  while($row = RecuperaRegistro($rs))
  {
    $t++;
    $fl_tema[$t] = $row[0];
    $nb_tema[$t] = $row[1];
    $ds_ruta_imagen[$t] = $row[2];
    
    # Recupera numero de posts no leidos por usuario y tema
    $rs2 = EjecutaQuery("SELECT no_posts FROM k_f_usu_tema WHERE fl_usuario = $fl_usuario AND fl_tema = $fl_tema[$t]");
    $row2 = RecuperaRegistro($rs2);
    $no_posts[$t] = $row2[0];
  }
  
  # Presenta menu
  echo "
        <tr>
          <td width='182' valign='top' class='left_colum'>
            <table border='".D_BORDES."' cellpadding='0' cellspacing='0' align='center'>
              <tr>
                <td width='150'>&nbsp;</td>
              </tr>";
  for($i = 1; $i <= $tot_modulos; $i++) {
    # RDG Presenta menu de foro
    if ( $t>0 && ($fl_modulo[$i] == $clavesModulosTeacher["desk"] || $fl_modulo[$i] == $clavesModulosStudent["desk"]) )
    {
      echo "
              <tr>
                <td class='left_menu'>
                  <table border='".D_BORDES."' width='100%' cellpadding='0' cellspacing='0'>
                    <tr><td colspan='2'>".ObtenEtiqueta(561)."</td></tr>";
      for($k=1; $k<=$t; $k++)
      {
        echo "
                      <tr>
                        <td class='left_menu_icon'>";
        if(!empty($ds_ruta_imagen[$k]))
          echo "
                          <img src='".SP_IMAGES."/".$ds_ruta_imagen[$k]."' width='16' height='16' border='0'>";
        else
          echo "&nbsp;";
        echo "
                        </td>
                        <td><a href='forum.php?theme=".$fl_tema[$k]."' class='links_left_menu'>".$nb_tema[$k]."";
        if(!empty($no_posts[$k]))
          echo "
                              <b>(".$no_posts[$k].")</b>";
        echo "                
                            </a></td>
                      </tr>";
      }
      echo "
                  </table>
                </td>
              </tr>";
    }
    
    echo "
              <tr>
                <td class='left_menu'>
                  <table border='".D_BORDES."' width='100%' cellpadding='0' cellspacing='0'>
                    <tr><td colspan='2'>$nb_modulo[$i]</td></tr>";
    
    for($j = 1; $j <= $tot_submodulos[$i]; $j++) {
      // MDB Livesession
      // Todos los mOdulos se muestran en el menU izquierdo con la descripciOn de la base de datos
      // Si es el mOdulo de livesession para el sitio de teachers o el de alumnos
      // Entonces mostramos la liga para acceder con formato de reminder
      if ( $fl_funcion[$i][$j] != $clavesModulosTeacher["liveSession"] && $fl_funcion[$i][$j] != $clavesModulosStudent["liveSession"]) {
          echo "
                <tr>
                  <td class='left_menu_icon'>";
          if(!empty($nb_icono[$i][$j]))
            echo "<img src='".SP_IMAGES."/".$nb_icono[$i][$j]."' width='16' height='16' border='0'>";
          else
            echo "&nbsp;";

          echo "   </td>
                   <td><a href='$path_nodo?node=".$fl_funcion[$i][$j]."' class='links_left_menu'>".$nb_funcion[$i][$j]."</a></td>
                </tr>";
       }
       else {
        // MDB Livesession
        // Se almacena en la variable el cOdigo html de la opciOn livesession para mostrarla siempre al final
        // de las opciones del menU sin importar si es para estudiante o profesor.
        // *** Para profesores, los mostraba como segunda opciOn y se veIa mal
        
        if($fl_perfil == PFL_ESTUDIANTE) {
          $fl_programaAux = ObtenProgramaAlumno($fl_usuario);
          $no_gradoAux = ObtenGradoAlumno($fl_usuario);
          $no_semanaAux = ObtenSemanaLiveSessionStudent($fl_usuario);
          $ds_tituloAux = ObtenTituloLeccion($fl_programaAux, $no_gradoAux, $no_semanaAux);
          
          $grupo = ObtenGrupoAlumno($fl_usuario);
          $fl_semana = ObtenFolioSemanaAlumno($fl_usuario, $no_semanaAux);
          $fechaLiveSession = ObtenLiveSessionActualStudent($grupo, $fl_semana);
          $folioClase = ObtenFolioLiveSessionStudent($grupo, $fl_semana);
          $grupo = ObtenNombreGrupo($grupo);
        }
        
        if($fl_perfil == PFL_MAESTRO) {
          $ds_tituloAux = ObtenTituloLeccionTeacher($fl_usuario);
          $fechaLiveSession = ObtenFechaLiveSessionTeacher($fl_usuario);
          $folioClase = ObtenFolioLiveSessionTeacher($fl_usuario);
          $grupo = ObtenGrupoTeacher($fl_usuario);
        }
        
        $fg_link_disponible = ObtenLiveSessionDisponible($folioClase);
        $liveSessionDisplay = "
                <tr>
                  <td colspan='2' align='right'>
                    <table border='".D_BORDES."' width='100%' cellpadding='0' cellspacing='0'>
                      <tr>
                        <td colspan='2'>&nbsp;</td>
                      </tr>
                      <tr>
                        <td width='25' valign='middle' align='center' class='right_column_reminders1'>";
        if($fg_link_disponible)
          $liveSessionDisplay .= "<a href='../liveclass/LiveSession.php?folio=$folioClase' target='_blank'><img src='".SP_IMAGES."/".ObtenNombreImagen(213)."' border='none' width='16' height='16'/></a>";
        else
          $liveSessionDisplay .= "&nbsp;";
        $liveSessionDisplay .= "
                        </td>
                        <td width='125' align='left' class='right_column_reminders2'>
                          <b>".$nb_funcion[$i][$j]."<br>" . $grupo . "</b><br>". $ds_tituloAux . "<br>". $fechaLiveSession."
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>";
      }
    }
    
    // MDB Livesession
    // Despliegue de la opciOn del Live session al final de las opciones del menU Desk
    if ( $fl_modulo[$i] == $clavesModulosTeacher["desk"] || $fl_modulo[$i] == $clavesModulosStudent["desk"] )
      echo $liveSessionDisplay;
    
    echo "
                  </table>
                </td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>";
  }
  if(!empty($contenido))
    echo "
              <tr>
                <td class='left_menu'>
                  <table border='".D_BORDES."' width='100%' cellpadding='0' cellspacing='0' align='center'>
                    <tr>
                      <td width='10'>&nbsp;</td>
                      <td class='tinymce_space'>$contenido</td>
                      <td width='10'>&nbsp;</td>
                    </tr>
                  </table>
                </td>
              </tr>";
  echo "
              <tr>
                <td>&nbsp;</td>
              </tr>
            </table>
          </td>
          <td width='740' valign='top'>
            <table border='".D_BORDES."' width='100%' cellpadding='0' cellspacing='0'>
              <tr>
                <td width='500' height='40' class='section_title'>$titulo</td>
                <td width='240' class='student_status'>";
  
  # Info de status al lado del titulo
  if($fl_perfil == PFL_ESTUDIANTE OR !empty($p_alumno)) {
    $fl_programa = ObtenProgramaAlumno($fl_alumno);
    $nb_programa = ObtenNombreProgramaAlumno($fl_alumno);
    $no_grado = ObtenGradoAlumno($fl_alumno);
    $no_semana = ObtenSemanaActualAlumno($fl_alumno);
    $ds_titulo = ObtenTituloLeccion($fl_programa, $no_grado, $no_semana);
    $ds_status = ObtenStatusAlumno($fl_alumno);
    if($fl_alumno == $fl_usuario)
      echo "$nb_programa, ".ObtenEtiqueta(422)." $no_grado<br>".ObtenEtiqueta(390)." $no_semana: $ds_titulo<br>".ObtenEtiqueta(500).": $ds_status";
    else
      echo "$nb_programa, ".ObtenEtiqueta(422)." $no_grado<br>".ObtenEtiqueta(390)." $no_semana: $ds_titulo";
  }
  echo "</td>
              </tr>";
}


function PresentaNotificacion($p_notificacion, $p_usuario, $p_perfil) {
  
  # Notificaciones de Noticias
  if($p_notificacion == 1) {
    
    # Borra notificaciones de noticias anteriores para todos los usuarios
    $fe_actual = "STR_TO_DATE('".ObtenFechaActual( )."', '%Y-%m-%d %H:%i:%s')";
    $Query  = "DELETE FROM k_not_blog WHERE fl_blog IN(";
    $Query .= "SELECT fl_blog FROM c_blog WHERE DATE_ADD(fe_blog, INTERVAL ".ObtenConfiguracion(18)." DAY) < $fe_actual)";
    EjecutaQuery($Query);
    $Query  = "SELECT COUNT(1) FROM k_not_blog a, c_blog b ";
    $Query .= "WHERE a.fl_blog=b.fl_blog ";
    $Query .= "AND b.fe_blog <= $fe_actual ";
    $Query .= "AND a.fl_usuario=$p_usuario";
    $row = RecuperaValor($Query);
    $imagen = 206;
    if($row[0] == 0) {
      $ds_tooltip = "No unread school messages";
      $imagen = 205;
    }
    elseif($row[0] == 1)
      $ds_tooltip = "$row[0] unread school message";
    else
      $ds_tooltip = "$row[0] unread school messages";
    echo "<a href='blog.php'><img src='".SP_IMAGES."/".ObtenNombreImagen($imagen)."' border='none' title='$ds_tooltip'/></a>";
  }
  
  # Notificaciones de Comentarios en el desktop (estudiantes) / Nuevos trabajos por calificar (maestros)
  if($p_notificacion == 2) {
    if($p_perfil == PFL_MAESTRO) {
      $Query  = "SELECT COUNT(1) ";
      $Query .= "FROM k_entrega_semanal a, c_grupo b ";
      $Query .= "WHERE a.fl_grupo=b.fl_grupo ";
      $Query .= "AND a.fg_entregado='1' ";
      $Query .= "AND (a.fl_promedio_semana IS NULL OR a.fl_promedio_semana='') ";
      $Query .= "AND b.fl_maestro=$p_usuario";
      $row = RecuperaValor($Query);
      $imagen = 221;
      if($row[0] == 0) {
        $ds_tooltip = "No pending submitted assignments";
        $imagen = 220;
      }
      elseif($row[0] == 1)
        $ds_tooltip = "$row[0] pending submitted assignment";
      else
        $ds_tooltip = "$row[0] pending submitted assignments";
      echo "<a href='submitted_assignments.php'><img src='".SP_IMAGES."/".ObtenNombreImagen($imagen)."' border='none' title='$ds_tooltip'/></a>";
    }
    else {
      $Query  = "SELECT COUNT(1) ";
      $Query .= "FROM k_com_entregable a, k_entrega_semanal b ";
      $Query .= "WHERE a.fl_entrega_semanal=b.fl_entrega_semanal ";
      $Query .= "AND b.fl_alumno=$p_usuario ";
      $Query .= "AND a.fg_leido='0'";
      $row = RecuperaValor($Query);
      $imagen = 208;
      if($row[0] == 0) {
        $ds_tooltip = "No new comments";
        $imagen = 207;
      }
      elseif($row[0] == 1)
        $ds_tooltip = "$row[0] new comment";
      else
        $ds_tooltip = "$row[0] new comments";
      echo "<a href='user_comments.php'><img src='".SP_IMAGES."/".ObtenNombreImagen($imagen)."' border='none' title='$ds_tooltip'/></a>";
    }
  }
  
  # Notificaciones de mensajes directos
  if($p_notificacion == 3) {
    $row = RecuperaValor("SELECT COUNT(1) FROM k_mensaje_directo WHERE fl_usuario_dest=$p_usuario AND fg_leido='0'");
    $imagen = 210;
    if($row[0] == 0) {
      $ds_tooltip = "No unread private messages";
      $imagen = 209;
    }
    elseif($row[0] == 1)
      $ds_tooltip = "$row[0] unread private message";
    else
      $ds_tooltip = "$row[0] unread private messages";
    echo "<a href='messages.php'><img src='".SP_IMAGES."/".ObtenNombreImagen($imagen)."' border='none' title='$ds_tooltip'/></a>";
  }
}


# Separadores de semanas y asignaciones para Desktop
function PresentaSeparadores($p_alumno, $p_semana, $p_tab, $p_otro_alumno=False, $p_maestro=False, $p_supervisor=False, $p_rc=False) {
  
  # Tabla de separadores
  echo "
              <tr>
                <td colspan='2' class='blank_cells'>
                  <table border='".D_BORDES."' cellpadding='0' cellspacing='0' width='100%'>
                    <tr>
                      <td height='5'>&nbsp;</td>
                    </tr>
                    <tr>
                      <td valign='top' height='60' class='division_line'>
                        <table border='".D_BORDES."' cellpadding='0' cellspacing='0' width='100%'>
                          <tr><td colspan='3' height='5'>&nbsp;</td></tr>
                          <tr>
                            <td width='5'>&nbsp;</td>
                            <td>
                              <ul id='weektab' class='week_tabs'>";
  
  # Presenta semanas basado en las lecciones del nivel que esta cursando el alumno
  $fl_programa = ObtenProgramaAlumno($p_alumno);
  $no_grado = ObtenGradoAlumno($p_alumno);
  $no_semana = ObtenSemanaActualAlumno($p_alumno);
  $Query  = "SELECT no_semana FROM c_leccion WHERE fl_programa=$fl_programa AND no_grado=$no_grado ORDER BY no_semana";
  $rs = EjecutaQuery($Query);
  while($row = RecuperaRegistro($rs)) {
    $etq = "Week $row[0]";
    if($row[0] <= $no_semana OR $p_supervisor) {
      if($row[0] <> $p_semana) {
        if(!$p_rc)
          echo "\n<li><a href='desktop.php?student=$p_alumno&week=$row[0]&tab=$p_tab'>$etq</a></li>";
        else
          echo "\n<li>$etq</li>";
      }
      else
        echo "\n<li><span class='current_week'>$etq</span></li>";
    }
    else
      echo "\n<li>$etq</li>";
  }
  echo "
                              </ul>
                            </td>
                            <td width='5'>&nbsp;</td>
                          </tr>";
  
  # Etiquetas para separadores de trabajo
  $tabs = array(
    "lecture"        => "Video Lecture",
    "brief"          => "Video Brief",
    "assignment"     => "Assignment",
    "assignment_ref" => "Assignment Ref",
    "sketch"         => "Sketch",
    "sketch_ref"     => "Sketch Ref",
    "critique"       => "Critique"
  );
  if(!$p_rc) {
    $links = array(
      "lecture"        => "<a href='desktop.php?student=$p_alumno&week=$p_semana&tab=lecture'>".$tabs["lecture"]."</a>",
      "brief"          => "<a href='desktop.php?student=$p_alumno&week=$p_semana&tab=brief'>".$tabs["brief"]."</a>",
      "assignment"     => "<a href='desktop.php?student=$p_alumno&week=$p_semana&tab=assignment'>".$tabs["assignment"]."</a>",
      "assignment_ref" => "<a href='desktop.php?student=$p_alumno&week=$p_semana&tab=assignment_ref'>".$tabs["assignment_ref"]."</a>",
      "sketch"         => "<a href='desktop.php?student=$p_alumno&week=$p_semana&tab=sketch'>".$tabs["sketch"]."</a>",
      "sketch_ref"     => "<a href='desktop.php?student=$p_alumno&week=$p_semana&tab=sketch_ref'>".$tabs["sketch_ref"]."</a>",
      "critique"       => "<a href='desktop.php?student=$p_alumno&week=$p_semana&tab=critique'>".$tabs["critique"]."</a>"
    );
  }
  else {
    $links = array(
      "lecture"        => $tabs["lecture"],
      "brief"          => $tabs["brief"],
      "assignment"     => "<a href=\"javascript:pauseVideo();CambiaTab($p_alumno,$p_semana,'assignment');\">".$tabs["assignment"]."</a>",
      "assignment_ref" => "<a href=\"javascript:pauseVideo();CambiaTab($p_alumno,$p_semana,'assignment_ref');\">".$tabs["assignment_ref"]."</a>",
      "sketch"         => "<a href=\"javascript:pauseVideo();CambiaTab($p_alumno,$p_semana,'sketch');\">".$tabs["sketch"]."</a>",
      "sketch_ref"     => "<a href=\"javascript:pauseVideo();CambiaTab($p_alumno,$p_semana,'sketch_ref');\">".$tabs["sketch_ref"]."</a>",
      "critique"       => $tabs["critique"]
    );
  }
  $links[$p_tab] = "<span class='current_assignment'>".$tabs[$p_tab]."</span>";
  
  # Deshabilita la opcion de lecture
  # Los estudiantes no pueden ver el lecture de otros estudiantes
  # Ni los estudiantes ni los maestros pueden ver lectures de mas de dos semnas atras
  # El supervisor puede ver todo
  if(($p_semana < $no_semana-2 OR ($p_otro_alumno AND !$p_maestro)) AND !$p_supervisor)
  {
    $links["lecture"] = $tabs["lecture"];
    $links["brief"] = $tabs["brief"];
  }
  
  # Presenta separadores de funcion
  echo "
                          <tr>
                            <td>&nbsp;</td>
                            <td>
                              <ul id='maintab' class='assignment_tabs'>
                                <li>".$links["lecture"]."</li>
                                <li>".$links["brief"]."</li>
                                <li>".$links["assignment"]."</li>
                                <li>".$links["assignment_ref"]."</li>
                                <li>".$links["sketch"]."</li>
                                <li>".$links["sketch_ref"]."</li>
                                <li>".$links["critique"]."</li>
                              </ul>
                            </td>
                            <td>&nbsp;</td>
                          </tr>
                        </table>
                      </td>
                    </tr>
                  </table>
                </td>
              </tr>";
  
  // MDB 20/SEP/2011
  // Agregue la funcion pauseVideo que se usa para cortar los audios cuando existe un video
  // y se cambia de pestania.
  // No puede estar en una libreria porque los divs son dinamicos y no siempre estaran incluidos los .js
  echo "     <script type='text/javascript'>
              function pauseVideo() {
                var videoRC = document.getElementById('recordCritique');
                if (videoRC != null) {
                  if (!videoRC.paused)
                    videoRC.pause();
                }
              }
              </script>
    ";
}


function PresentaVideoHTML5($p_path, $p_file, $p_width='720', $p_height='405', $p_id='recordCritique') { 
  echo "
  <video tabindex='0' id='$p_id' width='$p_width' height='$p_height' controls='controls'>
    <source src='".$p_path.$p_file."' type='video/ogg'>
	<source src='".$p_path.$p_file."' type='video/mp4'>
  </video>
  <div id='seekInfo' style='display:none;'></div>";
  
  // MDB 15/FEB/2013 Las librerias de JS se incluyen con cada llamado a PresentaVideoHTML5 que se hace con ajax
  // Esto hace que se incluya mas de una vez la libreria que tiene el codigo del cuadro por cuadro
  // y se repite el evento que captura las teclas de flechas haciendo que se brinque el video.
  // El elemento libCritiqueIncluidas se usa en recordCritique.js para evitar que se dupliquen las funciones.
  echo "
	<script type='text/javascript' src='".PATH_LIB."/js_player/smpte_test_universal.js'></script>
  <script type='text/javascript' src='".PATH_LIB."/js_player/jquery.jkey-1.2.js'></script>";
  
  if($p_id == 'recordCritique') {
    echo "
      <script type='text/javascript' src='".PATH_COM_JS."/recordCritique.js'></script>
    ";
    
    echo "<script>
            var div_aux = $('<div />').appendTo('body');
            div_aux.attr('id', 'libCritiqueIncluidas');          
            $('#libCritiqueIncluidas').html('True');
          </script>";    
    
  }
}


function PresentaVideoHTML5Critique($p_path, $p_file) {
  
  echo "
  <video id='one' width='720' height='405' controls='controls'>
    <source src='".$p_path."".$p_file."' type='video/mp4'>
	<source src='".$p_path."".$p_file."' type='video/ogg'>
  </video>
  <script type='text/javascript' src='".PATH_COM_JS."/critiquevideos.js'></script>";
}


function PresentaVideoHTML5Webcam($p_path, $p_file) {
  
  $webcam_top     = "10px";
  $webcam_left    = "10px";
  $webcam_width   = "250px";
  $webcam_height  = "188px";
  echo "<div style='position:absolute;width:$webcam_width;height:$webcam_height;top:$webcam_top;left:$webcam_left;'>
  <video id='two' width='250' height='188'>
  <source src='".$p_path."".$p_file."' type='video/mp4'>
    <source src='".$p_path."".$p_file."' type='video/ogg'>
  </video></div>";
}


function PresentaVideo($path, $file, $p_width=720, $p_height=405) {
  
  echo "
                        <script type='text/javascript'>
                          var flashvars = {};
                          // video width
                          var videoWidth = $p_width;
                          // video height
                          var videoHeight = $p_height;
                          // Main Video Path
                          flashvars.videoFilePath = '".$path."".$file."';
                          // Video buffer time (seconds)
                          flashvars.videoBufferTime = '5'
                          // automatically start video playing when first start video player. (yes/no)
                          flashvars.autoStartVideoPlay = 'no';
                          // Auto Repeat at end of the video. (yes/no)
                          flashvars.autoRepeat = 'no';
                          // Video starting volume (Max: 100 Min: 0)
                          flashvars.videoStartVolume= '75';
                          // Show Advertisement video (yes/no)
                          flashvars.showAdvertisementVideo = 'no';
                          // Advertisement Video Path
                          flashvars.advertisementVideoPath = '".SP_VIDEOS."';
                          // Video Title Text
                          flashvars.titleTxt = \"<font color='#9999FF' size='15'>Vancouver Animation School</font>\";
                          // Video Description Text
                          flashvars.descriptionTxt = '';
                          // Show Logo
                          flashvars.logoDisplay = 'no';
                          // Logo Image Path
                          flashvars.logoImagePath = '".SP_IMAGES."/logo.jpg';
                          // Logo Position
                          flashvars.logoPlacePosition = 'top-left';
                          // Logo Margin Space
                          flashvars.logoMargin = '20';
                          // Logo Width
                          flashvars.logoWidth = '86';
                          // Logo Height
                          flashvars.logoHeight = '38';
                          // Logo Transparency Value (100 : Solid  50 : Semi Transparency)
                          flashvars.logoTransparency = '60';
                          // Define video bar color (blue, green, orange, purple, white, red)
                          // random : it will select color randomly
                          flashvars.videoBarColorName = 'white';
                          // Define volume bar color (blue, green, orange, purple, white, red)
                          // random : it will select color randomly
                          flashvars.volumeBarColorName = 'white';
                          // Show Cover Image
                          flashvars.coverImageDisplay = 'no';
                          // cover image path. You can use SWF, PNG, JPG or GIF
                          flashvars.coverImagePath = '".SP_IMAGES."/';
                          // Auto Hide Control Panel and Mouse
                          //flashvars.hideControlPanelAndMouse='yes'
                          flashvars.hideControlPanel='yes'
                          flashvars.hideMouse='no'
                          // Auto Hide time (second)
                          flashvars.hideTime='1'
                          var params = {};
                            params.scale = 'exactfit';
                            params.allowfullscreen = 'true';
                            params.salign = 't';
                            params.bgcolor = '000000';
                            params.wmode = 'opaque';
                            
                          var attributes = {};
                          swfobject.embedSWF('".SP_FLASH."/video_player8_flashvars.swf', 'myContent$file', videoWidth, videoHeight, '9.0.0', false, flashvars, params, attributes);
                        </script>
                        <div id='myContent$file'>
                          <h1>Alternative content</h1>
                          <p><a href='http://www.adobe.com/go/getflashplayer'><img src='http://www.adobe.com/images/shared/download_buttons/get_flash_player.gif' alt='Get Adobe Flash player' /></a></p>
                        </div>";
}

function PresentaVideoJWP($p_file) {
  
  $file = ObtenNombreArchivo($p_file);
  $streamer = "rtmp://".ObtenConfiguracion(60)."/oflaDemo";
  $image = SP_IMAGES."/PosterFrame_White.jpg";
  $width = ObtenConfiguracion(13);
  $height = ObtenConfiguracion(14) + 25;
  $bufferTime = ObtenConfiguracion(56);
  echo "
  <object id='player' classid='clsid:D27CDB6E-AE6D-11cf-96B8-444553540000' name='player' width='$width' height='$height' style='z-index: 1;'>
    <param name='movie' value='".SP_FLASH."/player.swf' />
    <param name='allowfullscreen' value='true' />
    <param name='allowscriptaccess' value='always' />
    <param name='wmode' value='opaque' />
    <param name='flashvars' value='file=$file&streamer=$streamer&image=$image&bufferlength=$bufferTime&smoothing=false' />
    <embed
      type='application/x-shockwave-flash'
      id='player2'
      name='player2'
      src='".SP_FLASH."/player.swf'
      width='$width'
      height='$height'
      allowscriptaccess='always'
      allowfullscreen='true'
      wmode='opaque'
      flashvars='file=$file&streamer=$streamer&image=$image&bufferlength=$bufferTime&smoothing=false'
    />
  </object>";
}

# Capa con marca de agua para Video Lectures
function PresentaWatermark($p_watermark) {
  
  echo "
  <div id='div_watermark' style='position: absolute; top: 230; left: 200; z-index: 2; font-size: 20; opacity:0.5; color: #FFF;' >
    $p_watermark
  </div>
  <script type='text/javascript'>
    timer = setInterval(\"CambiaEtiqueta()\", 10000);
    function CambiaEtiqueta() {
      var aleat = Math.random() * (405 - 20); // 405 alto del video - 20 alto de la etiqueta
      aleat = Math.round(aleat);
      $('#div_watermark').css('top', parseInt(220) + aleat);
      aleat = Math.random() * (720 - 100); // 720 ancho del video - 100 ancho de la etiqueta
      aleat = Math.round(aleat);
      $('#div_watermark').css('left', parseInt(200) + aleat);
      aleat = Math.random() * (20 - 12); // el font estara entre 12 y 20
      aleat = Math.round(aleat);
      $('#div_watermark').css('font-size', parseInt(12) + aleat);
      $('#div_watermark').html('$p_watermark');
      espera = setTimeout(\"$('#div_watermark').html('')\", 5000);
    }
  </script>";
}

function PresentaPizarron( ) {
  
  echo "
  <script type='text/javascript' src='".PATH_LIB."/js_paint/cp_depends.js'></script>
  <script type='text/javascript' src='".PATH_LIB."/js_paint/CanvasWidget.js'></script>
  <script type='text/javascript' src='".PATH_LIB."/js_paint/CanvasPainter.js'></script>
  <script type='text/javascript' src='".PATH_LIB."/js_paint/CPWidgets.js'></script>
  <script type='text/javascript' src='".PATH_LIB."/js_paint/CPAnimator.js'></script>
  <script type='text/javascript' src='".PATH_LIB."/js_paint/CPDrawing.js'></script>
  <script type='text/javascript' src='".PATH_LIB."/js_paint/pizarron.js'></script>
  <script type='text/javascript' src='".PATH_LIB."/js_jpicker/jpicker-1.1.6.min.js'></script>
  <link rel='stylesheet' href='".PATH_LIB."/js_jpicker/jPicker-1.1.6.min.css' />
  <link rel='stylesheet' href='".PATH_LIB."/js_paint/pizarron.css' />
  
  <div id='paint'>
    <div id='controls'>
      <div class='ctr_btn' id='btn_0' style='background: #CCCCCC;' onclick='setCPDrawAction(0)' onMouseDown=\"setControlLook(0, '#CCCCCC')\" onMouseOver=\"setControlLook(0, '#EEEEEE')\" onMouseOut=\"setControlLook(0, '#FFFFFF')\" title='Pencil'><img src='".PATH_COM_IMAGES."/brush1.png'/></div>
      <div class='ctr_btn' id='btn_1' onclick='setCPDrawAction(1)' onMouseDown=\"setControlLook(1, '#CCCCCC')\" onMouseOver=\"setControlLook(1, '#EEEEEE')\" onMouseOut=\"setControlLook(1, '#FFFFFF')\" title='Brush'><img src='".PATH_COM_IMAGES."/brush2.png'/></div>
      <div class='ctr_btn' id='btn_2' onclick='setCPDrawAction(2)' onMouseDown=\"setControlLook(2, '#CCCCCC')\" onMouseOver=\"setControlLook(2, '#EEEEEE')\" onMouseOut=\"setControlLook(2, '#FFFFFF')\" title='Line'><img src='".PATH_COM_IMAGES."/line.png'/></div>
      <div class='ctr_btn' id='btn_3' onclick='setCPDrawAction(3)' onMouseDown=\"setControlLook(3, '#CCCCCC')\" onMouseOver=\"setControlLook(3, '#EEEEEE')\" onMouseOut=\"setControlLook(3, '#FFFFFF')\" title='Rectangle'><img src='".PATH_COM_IMAGES."/rectangle.png'/></div>
      <div class='ctr_btn' id='btn_4' onclick='setCPDrawAction(4)' onMouseDown=\"setControlLook(4, '#CCCCCC')\" onMouseOver=\"setControlLook(4, '#EEEEEE')\" onMouseOut=\"setControlLook(4, '#FFFFFF')\" title='Circle'><img src='".PATH_COM_IMAGES."/circle.png'/></div>
      <div class='ctr_btn' id='btn_5' onclick='setCPDrawAction(5)' onMouseDown=\"setControlLook(5, '#CCCCCC')\" onMouseOver=\"setControlLook(5, '#EEEEEE')\" onMouseOut=\"setControlLook(5, '#FFFFFF')\" title='Erase'><img src='".PATH_COM_IMAGES."/erase.gif'/></div>
      <div class='ctr_btn' id='togglePizarron'><img src='".PATH_COM_IMAGES."/onoffon.png' title='Turn Off'/></div>
      <div class='ctr_btn' id='selectLineWidth' title='Select line width'><img src='".PATH_COM_IMAGES."/selectLine.png'/></div>
      <div class='ctr_btn' id='selectColor' title='Select color' style='background-color: #FFFF00;'></div>
    </div>
    <canvas id='canvas' width='720' height='375'></canvas>
    <canvas id='canvasInterface' width='720' height='375'></canvas>
    <div id='chooserWidgets'>
      <canvas id='lineWidthChooser' width='275' height='76' style='display:none;'></canvas>
    </div>
    <div id='dlgJPicker'><div id='Expandable'></div></div>
  </div>
  ";
}

function PresentaWebcam($p_entrega_semanal) {
  
  $webcam_top     = "5px";
  $webcam_left    = "10px";
  $webcam_width   = "250px";
  $webcam_height  = "188px";
  $nombreArchivoJNLP = ObtenNombreArchivoJNLP($p_entrega_semanal);
  echo "
  <div id='broadcaster' style='position:absolute;width:$webcam_width;height:$webcam_height;top:$webcam_top;left:$webcam_left;'>No Flash</div>
  <div style='position:absolute;top:195px;background-color:#fed;'><a href='".SP_HOME.DIRECTORIO_JAR."/".$nombreArchivoJNLP."'><img src='".PATH_COM_IMAGES."/record.png' title='Start recording'/></a></div>
  <script type='text/javascript'>
    // <![CDATA[
    var so = new SWFObject('".PATH_LIB."/js_webcam/broadcast.swf?folio=$p_entrega_semanal', 'broadcast', '250', '188', '8', '#FFFFFF');
    so.addParam('allowScriptAccess', 'always');
    so.addVariable('allowResize', canResizeFlash());
    so.write('broadcaster');
    // ]]>
  </script>
  ";
}

# Genera una instancia de TinyMCE
function GeneraTinyMCE($p_nombre, $p_ancho, $p_alto) {
  
  echo "
  <script type='text/javascript'>
    tinyMCE.init({
      mode  : 'none',
      theme : 'advanced',
      editor_selector : '$p_nombre',
      plugins : 'safari',
      theme_advanced_buttons1 : '',
      theme_advanced_buttons2 : '',
      theme_advanced_buttons3 : '',
      theme_advanced_buttons4 : '',
      theme_advanced_toolbar_location : '',
      theme_advanced_toolbar_align : 'left',
      theme_advanced_statusbar_location : 'none',
      theme_advanced_resizing : false,
      relative_urls: false,
      width : '$p_ancho',
      height: '$p_alto'
    });
  </script>";
}


# Presenta Columna derecha y termina el cuerpo de la pagina
function PresentaFooter($p_err=0, $p_semana=0, $p_tipo='') {
  
  # Inicializa variables
  $fl_usuario = ObtenUsuario(False);
  $fl_perfil = ObtenPerfil($fl_usuario);
  $ds_fecha = ObtenFechaActual(True);
  $ds_hora = ObtenHoraActual( );
  $fe_actual = "STR_TO_DATE('".ObtenFechaActual( )."', '%Y-%m-%d %H:%i:%s')";
  if($fl_perfil == PFL_MAESTRO)
    $pag_fija = 7;
  else
    $pag_fija = 5;
  
  # Recupera contenido de la pagina fija
  $Query  = "SELECT ds_contenido, tr_contenido ";
  $Query .= "FROM c_pagina ";
  $Query .= "WHERE cl_pagina=$pag_fija";
  $row = RecuperaValor($Query);
  $contenido = str_uso_normal(EscogeIdioma($row[0], $row[1]));
  
  # Cierra tabla de contenido y presenta barra lateral derecha
  echo "
                  </table>
                </td>
              </tr>
            </table>
          </td>
          <td width='182' class='right_colum' valign='top'>
            <table border='".D_BORDES."' cellpadding='0' cellspacing='0' align='center'>
              <tr>
                <td width='150' height='25' class='right_column_labels'>$ds_fecha<br>$ds_hora</td>
              </tr>
              <tr>
                <td height='12'></td>
              </tr>";
  
  # Tabla de Reminders
  $opt = 0;
  $no_reg = 0;
  $reminder = array( );
  $diferencia = RecuperaDiferenciaGMT( );
  if($fl_perfil == PFL_MAESTRO) {
    #Query para traer fechas de Q&A Live Sessions
    $res = EjecutaQuery("SELECT fl_grupo, nb_grupo, fl_term FROM c_grupo WHERE fl_maestro=$fl_usuario");
    while($row0 = RecuperaRegistro($res))
    {
      $Query1  = "SELECT (DATE_ADD(fe_clase, INTERVAL $diferencia HOUR)), 'Q&A Live Session group ' ";
      $Query1 .= "FROM k_clase ";
      $Query1 .= "WHERE fe_clase >= $fe_actual ";
      $Query1 .= "AND fl_grupo = $row0[0] ";
      $rs1 = EjecutaQuery($Query1);
      
      #Arma arreglo con datos de query
      $no_reg = $no_reg + CuentaRegistros($rs1);
      while($row1 = RecuperaRegistro($rs1))
      {
        $reminder[$opt][0] = $row1[0];
        $reminder[$opt][1] = $row1[1];
        $reminder[$opt][2] = $row0[1];
        $opt++;
      }
    }
    
    #Query para traer fechas lImite de calificaciOn de trabajos para maestros
    $Query2  = "SELECT (DATE_ADD(fe_calificacion, INTERVAL $diferencia HOUR)), 'Evaluation due date ' ";
    $Query2 .= "FROM k_semana ";
    $Query2 .= "WHERE fe_calificacion >= $fe_actual ";
    $Query2 .= "AND fl_term in(SELECT DISTINCT(fl_term) FROM c_grupo WHERE fl_maestro=$fl_usuario) ";
    $rs2= EjecutaQuery($Query2);
    
    #Arma arreglo con datos de query
    $no_reg = $no_reg + CuentaRegistros($rs2);
    while($row1 = RecuperaRegistro($rs2))
    {
      $reminder[$opt][0] = $row1[0];
      $reminder[$opt][1] = $row1[1];
      $reminder[$opt][2] = '';
      $opt++;
    }
  }
  else { // Reminders para alumnos
    #Query para traer fechas de Q&A Live Sessions
    $res = RecuperaValor("SELECT fl_grupo FROM k_alumno_grupo WHERE fl_alumno=$fl_usuario");
    $fl_grupo = $res[0];
    $Query1  = "SELECT (DATE_ADD(fe_clase, INTERVAL $diferencia HOUR)), 'Q&A Live Session ' ";
    $Query1 .= "FROM k_clase ";
    $Query1 .= "WHERE fe_clase >= $fe_actual ";
    $Query1 .= "AND fl_grupo = $fl_grupo ";
    $Query1 .= "ORDER BY fe_clase ";
    $rs1 = EjecutaQuery($Query1);
    
    #Arma arreglo con datos de query
    $no_reg = $no_reg + CuentaRegistros($rs1);
    while($row1 = RecuperaRegistro($rs1))
    {
      $reminder[$opt][0] = $row1[0];
      $reminder[$opt][1] = $row1[1];
      $reminder[$opt][2] = '';
      $opt++;
    }
    
    # Reminder para recordar las fechas de pago 
    
    # Recupera el programa y term que esta cursando el alumno
    $fl_programa = ObtenProgramaAlumno($fl_usuario);
    $fl_term = ObtenTermAlumno($fl_usuario);
    
    # Recupera la sesion
    $Query  = "SELECT cl_sesion ";
    $Query .= "FROM c_usuario ";
    $Query .= "WHERE fl_usuario=$fl_usuario";
    $row = RecuperaValor($Query);
    $cl_sesion = $row[0];
  
    # Recupera el term inicial
    $Query  = "SELECT fl_term_ini ";
    $Query .= "FROM k_term ";
    $Query .= "WHERE fl_programa=$fl_programa";
    $Query .= "AND fl_term=$fl_term";
    $row = RecuperaValor($Query);
    $fl_term_ini = $row[0];
    
    # Recupera el tipo de pago para el curso
    $Query  = "SELECT fg_opcion_pago ";
    $Query .= "FROM k_app_contrato ";
    $Query .= "WHERE cl_sesion='$cl_sesion'"; 
    $row = RecuperaValor($Query);
    $fg_opcion_pago = $row[0];
    
    if(empty($fl_term_ini))
      $fl_term_ini=$fl_term;
    
    # Recupera informacion de los pagos
    $Query  = "SELECT fl_term_pago, fe_pago ";
    $Query .= "FROM k_term_pago ";
    $Query .= "WHERE fl_term=$fl_term_ini ";
    $Query .= "AND no_opcion=$fg_opcion_pago";
    $rs = EjecutaQuery($Query);
    for($i=0; $row = RecuperaRegistro($rs); $i++) {
      $fl_term_pago = $row[0];
      $fe_limite_pago = $row[1];
      
      $Query  = "SELECT fl_term_pago ";
      $Query .= "FROM k_alumno_pago ";
      $Query .= "WHERE fl_term_pago=$fl_term_pago ";
      $Query .= "AND fl_alumno=$fl_usuario";
      $row = RecuperaValor($Query);
      $fl_t_pago = $row[0];
      
      if(empty($fl_t_pago)) {
        if(empty($proximo_pago)){
          $proximo_pago=$fl_term_pago;
          $etiqueta_reminder="Payment due date <br><a href='tuition_payment.php'>Pay now!</a>";
        }
        else {
          $etiqueta_reminder="Payment due date";
        }
      
        $fecha_actual = ObtenFechaActual(); 
        $fe_reminder = strtotime ('-'.ObtenConfiguracion(65).' days', strtotime($fe_limite_pago));
        $fe_reminder = date( 'Y-m-d', $fe_reminder); 

        if ($fecha_actual>=$fe_reminder) {
        
          # Arma arreglo con datos de query
          $reminder[$opt][0] = $fe_limite_pago;
          $reminder[$opt][1] = $etiqueta_reminder;
          $reminder[$opt][2] = '';
          $opt++;
        }
      }
    }
    
    #Query para traer fechas lImite de entrega de trabajos para alumnos
    $res = RecuperaValor("SELECT  MAX(fl_term) FROM k_alumno_term WHERE fl_alumno = $fl_usuario");
    $Query4  = "SELECT (DATE_ADD(fe_entrega, INTERVAL $diferencia HOUR)), 'Submission due date ' ";
    $Query4 .= "FROM k_semana ";
    $Query4 .= "WHERE fe_entrega >= $fe_actual ";
    $Query4 .= "AND fl_term = $res[0] ";
    $rs4 = EjecutaQuery($Query4);
    
    #Arma arreglo con datos de query
    $no_reg = $no_reg + CuentaRegistros($rs4);
    while($row1 = RecuperaRegistro($rs4))
    {
      $reminder[$opt][0] = $row1[0];
      $reminder[$opt][1] = $row1[1];
      $reminder[$opt][2] = '';
      $opt++;
    }
    
    #Query para traer fechas de cumpleanios de classmates
    $Query5  = "SELECT MAKEDATE(CASE WHEN dayofyear(fe_nacimiento) < dayofyear($fe_actual) THEN year($fe_actual)+1 ELSE year($fe_actual) END, ";
    $Query5 .= "        CASE WHEN (dayofyear(fe_nacimiento)>59 ";
    $Query5 .= "              AND ((year(fe_nacimiento)%4=0 AND year(fe_nacimiento)%100>0) OR year(fe_nacimiento)%400=0) ";
    $Query5 .= "                AND ((year($fe_actual)%4>0 OR year($fe_actual)%100=0) ";
    $Query5 .= "                  AND year($fe_actual)%400>0)) ";
    $Query5 .= "            THEN dayofyear(fe_nacimiento)-1 ";
    $Query5 .= "            WHEN (dayofyear(fe_nacimiento)>59 ";
    $Query5 .= "              AND ((year(fe_nacimiento)%4>0 OR year(fe_nacimiento)%100=0) AND year(fe_nacimiento)%400>0) ";
    $Query5 .= "                AND ((year($fe_actual)%4=0 AND year($fe_actual)%100>0) ";
    $Query5 .= "                  OR year($fe_actual)%400=0)) ";
    $Query5 .= "            THEN dayofyear(fe_nacimiento)+1 ";
    $Query5 .= "            ELSE dayofyear(fe_nacimiento) ";
    $Query5 .= "            END) fe_cumple, ";
    $Query5 .= "a.ds_nombres, a.ds_apaterno, ' birthday! ' ";
    $Query5 .= "FROM c_usuario a, k_alumno_grupo b ";
    $Query5 .= "WHERE a.fl_usuario = b.fl_alumno ";
    $Query5 .= "AND MAKEDATE(CASE WHEN dayofyear(fe_nacimiento) < dayofyear($fe_actual) THEN year($fe_actual)+1 ELSE year($fe_actual) END, ";
    $Query5 .= "        CASE WHEN (dayofyear(fe_nacimiento)>59 ";
    $Query5 .= "              AND ((year(fe_nacimiento)%4=0 AND year(fe_nacimiento)%100>0) OR year(fe_nacimiento)%400=0) ";
    $Query5 .= "                AND ((year($fe_actual)%4>0 OR year($fe_actual)%100=0) ";
    $Query5 .= "                  AND year($fe_actual)%400>0)) ";
    $Query5 .= "            THEN dayofyear(fe_nacimiento)-1 ";
    $Query5 .= "            WHEN (dayofyear(fe_nacimiento)>59 ";
    $Query5 .= "              AND ((year(fe_nacimiento)%4>0 OR year(fe_nacimiento)%100=0) AND year(fe_nacimiento)%400>0) ";
    $Query5 .= "                AND ((year($fe_actual)%4=0 AND year($fe_actual)%100>0) ";
    $Query5 .= "                  OR year($fe_actual)%400=0)) ";
    $Query5 .= "            THEN dayofyear(fe_nacimiento)+1 ";
    $Query5 .= "            ELSE dayofyear(fe_nacimiento) ";
    $Query5 .= "            END) >= $fe_actual ";
    $Query5 .= "AND b.fl_grupo = $fl_grupo";
    
    $rs5 = EjecutaQuery($Query5);
    
    #Arma arreglo con datos de query
    $no_reg = $no_reg + CuentaRegistros($rs5);
    while($row1 = RecuperaRegistro($rs5))
    {
      $reminder[$opt][0] = $row1[0];
      $reminder[$opt][1] = $row1[1].' '.$row1[2].' '.$row1[3];
      $reminder[$opt][2] = '';
      $opt++;
    }
  }
  
  # Presenta reminders
  if($no_reg < 5)
    $n = $no_reg;
  else
    $n = 5;
  if($n > 0) {
    echo "
              <tr><td height='15' class='right_column_labels'>Reminders</td></tr>
              <tr><td height='5'></td></tr>
              <tr>
                <td height='100' valign='top'>
                  <table border='".D_BORDES."' cellpadding='0' cellspacing='0' align='left'>";
    $rem = sort($reminder);
    for($i=0; $i<$n; $i++) {
      $var = $reminder[$i][0];
      $mes = substr($var, 5, 2);
      $dia = substr($var, 8, 2);
      $anio = substr($var, 0, 4);
      $hora = substr($var, 11, 5);
      $fecha = ObtenNombreMes($mes)." ".$dia.", ".$anio." ".$hora;
      echo "
                    <tr>
                      <td height='5'></td>
                    </tr>
                    <tr>
                      <td width='25' height='25' valign='middle' align='center' class='right_column_reminders1'>
                        <img src='".SP_IMAGES."/".ObtenNombreImagen(213)."' border='none' width='16' height='16'/>
                      </td>
                      <td width='125' align='left' class='right_column_reminders2'>
                        <b>".$reminder[$i][1].$reminder[$i][2]."</b><br>".$fecha."
                      </td>
                    </tr>";
    }
    echo "
                  </table>
                </td>
              </tr>
              <tr><td height='5'></td></tr>";
  }
  else
    echo "
              <tr><td height='15' class='right_column_labels'>No reminders available</td></tr>
              <tr><td height='5'></td></tr>";
  
  # Forma para subir asignaciones
  if($fl_perfil == PFL_ESTUDIANTE AND !empty($p_semana) AND !empty($p_tipo)) {
    if(!ObtenCalificadoAlumno($fl_usuario, $p_semana)) {
      echo "
              <tr>
                <td height='120' valign='bottom' align='center' class='right_column_upload'>
                  <table border='".D_BORDES."' width='100%' cellpadding='0' cellspacing='0' class='right_column_labels'>
                  <form name='upload_file' method='post' action='upload.php'>
                    <input type='hidden' name='tipo' value='$p_tipo'>
                    <input type='hidden' name='semana' value='$p_semana'>
                    <tr><td colspan='3' height='5'></td></tr>
                    <tr>
                      <td width='30%' height='24' align='right' valign='middle'><img src='".SP_IMAGES."/".ObtenNombreImagen(214)."' border='none' width='16' height='16'/></td>
                      <td width='5'></td>
                      <td align='left'>Work Upload</td>
                    </tr>
                    <tr><td colspan='3' height='5'></td></tr>
                    <tr><td colspan='3'><b>";
      switch($p_tipo) {
        case 'A':  echo "Assignment";     break;
        case 'AR': echo "Assignment Ref"; break;
        case 'S':  echo "Sketch";         break;
        case 'SR': echo "Sketch Ref";     break;
      }
      echo ", Week $p_semana</b></td>
                    </tr>
                    <tr><td colspan='3' height='10'></td></tr>
                    <tr><td colspan='3'>1. Comments for teacher:</td></tr>
                    <tr><td colspan='3'><textarea id='comentarios' name='comentarios' cols='13' rows='3'></textarea></td></tr>
                    <tr><td colspan='3' height='10'></td></tr>
                    <tr><td colspan='3'>2. Select file to upload:</td></tr>
                    <tr><td colspan='3' height='5'></td></tr>
                    <tr>
                      <td colspan='3'>
                        <input type='hidden' name='archivo' id='archivo'>
                        <div id='fu_archivo'></div>
                        <script>
                          function createUploader(){
                            var uploader = new qq.FileUploader({
                              element: document.getElementById('fu_archivo'),
                              action: '".PATH_COM_LIB."/fileuploader.php',
                              allowedExtensions: ['mov', 'jpeg', 'jpg'],
                              sizeLimit: 50 * 1024 * 1024,
                              onSubmit:
                              function(id, fileName) {
                                $('#upload_msg').empty();
                              },
                              onComplete:
                              function(id, fileName, responseJSON) {
                                $('#archivo').val(fileName);
                                $('.qq-upload-button').empty();
                                document.upload_file.submit();
                                $('.qq-upload-success').html('<span class=qq-upload-spinner></span>Converting file,<br>please wait');
                              },
                              debug: false
                            });
                          }
                          window.onload = createUploader;
                        </script>
                      </td>
                    </tr>
                    <tr><td colspan='3'>(Only .mov .jpeg or .jpg)</td></tr>";
      if(!empty($p_err)) {
        switch($p_err) {
          case  1: $ds_error = "Please select a file to upload"; break;
          case  2: $ds_error = "Only JPEGs are allowed for Sketch"; break;
          default: $ds_error = "File succesfully uploaded"; break;
        }
        echo "
                    <tr><td colspan='3' height='10'></td></tr>
                    <tr><td colspan='3' class='right_column_error'><div id='upload_msg'>$ds_error</div></td></tr>";
      }
      echo "
                    <tr><td colspan='3' height='10'></td></tr>
                  </form>
                  </table>
                </td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>";
    }
    else {
      echo "
              <tr>
                <td>Upload for week $p_semana is no longer available.</td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>";
    }
  }
  
  # Muestra contenido dinamico de la barra derecha
  echo "
              <tr>
                <td class='tinymce_space'>$contenido</td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
            </table>
          </td>
        </tr>
        <tr>
          <td colspan='3' height='20' class='footer'>Copyright &copy; 2010-".date('Y')." Vancouver Animation School</td>
        </tr>
      </table>
    </body>
  </html>";
}

?>