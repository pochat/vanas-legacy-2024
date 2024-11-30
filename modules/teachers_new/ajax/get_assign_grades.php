<?php
  
  # Libreria de funciones
  require("../../common/lib/cam_general.inc.php");
  
  # Recibe parametros
  $fl_entrega_semanal = RecibeParametroNumerico('fl_entrega_semanal');
  $clave = RecibeParametroNumerico('clave');
  $fl_usuario = RecibeParametroNumerico('fl_usuario');
  $tab = RecibeParametroHTML('tab');
  
  
  # Recupera datos de la entrega
  $fe_actual = ObtenFechaActual( );
  $concat = array('b.ds_nombres', "' '", 'b.ds_apaterno');
  $Query  = "SELECT a.fl_alumno, a.fl_grupo, a.fg_entregado, a.fl_promedio_semana, a.fl_semana, d.no_semana, ";
  $Query .= ConcatenaBD($concat)." 'ds_nombre', ";
  $Query .= "DATE_FORMAT(c.fe_entrega, '%c') fe_entrega_mes, DATE_FORMAT(c.fe_entrega, '%e, %Y') fe_entrega_r, ";
  $Query .= "DATE_FORMAT(a.fe_entregado, '%c') fe_entregado_mes, DATE_FORMAT(a.fe_entregado, '%e, %Y %H:%i') fe_entregado_r, ";
  $Query .= "DATEDIFF(c.fe_entrega, a.fe_entregado)+1 dif_entrega, DATEDIFF(c.fe_entrega, '$fe_actual')+1 dif_hoy ";
  $Query .= "FROM k_entrega_semanal a, c_usuario b, k_semana c, c_leccion d ";
  $Query .= "WHERE a.fl_alumno=b.fl_usuario ";
  $Query .= "AND a.fl_semana=c.fl_semana ";
  $Query .= "AND c.fl_leccion=d.fl_leccion ";
  $Query .= "AND a.fl_entrega_semanal=$fl_entrega_semanal ";
  $row = RecuperaValor($Query);
  $fl_alumno = $row[0];
  $fl_grupo = $row[1];
  $fg_entregado = $row[2];
  $fl_promedio_semana = $row[3];
  $fl_semana = $row[4];
  $no_semana = $row[5];
  $ds_nombre = str_uso_normal($row[6]);
  $fe_entrega = ObtenNombreMes($row[7])." ".$row[8];
  $fe_entregado = ObtenNombreMes($row[9])." ".$row[10];
  if(empty($row[9]))
    $fe_entregado = "<b>Pending</b>";
  if((!empty($row[11]) AND $row[11] >= 0) OR $row[12] >= 0) {
    $ds_en_tiempo = "Yes";
    $fg_en_tiempo = True;
  }
  else {
    $ds_en_tiempo = "No";
    $fg_en_tiempo = False;
  }
  
  # Recupera la fecha programada para la clase en linea
  $Query  = "SELECT DATE_FORMAT(a.fe_clase, '%c') fe_clase_mes, DATE_FORMAT(a.fe_clase, '%e, %Y %H:%i') fe_clase_r, ";
  $Query .= "DATEDIFF(a.fe_clase, '$fe_actual') dif_clase, b.fl_live_session, a.fg_obligatorio ";
  $Query .= "FROM k_clase a LEFT JOIN k_live_session b ";
  $Query .= "ON (a.fl_clase=b.fl_clase) ";
  $Query .= "WHERE a.fl_semana=$fl_semana ";
  $Query .= "AND a.fl_grupo=$fl_grupo ";
  $cons = EjecutaQuery($Query);
  $faltas = 0;
  $sesiones = 1;
  while($row1 = RecuperaRegistro($cons))
  {
    $fe_clase[$sesiones] = ObtenNombreMes($row1[0])." ".$row1[1];
    $dif_clase = $row1[2];
    $fl_live_session = $row1[3];
    
    # Revisa la asistencia a la clase en linea
    $fg_asistencias = True;
    if($dif_clase >= 0)
      $fe_asistencia[$sesiones] = "(Not checked in yet)";
    else
      $fe_asistencia[$sesiones] = "(Absent)";
    $Query  = "SELECT b.cl_estatus_asistencia, ";
    $Query .= "DATE_FORMAT(b.fe_asistencia, '%c') fe_clase_mes, DATE_FORMAT(b.fe_asistencia, '%e, %Y %H:%i') fe_clase_r ";
    $Query .= "FROM k_live_session a, k_live_session_asistencia b ";
    $Query .= "WHERE a.fl_live_session=b.fl_live_session ";
    $Query .= "AND a.fl_live_session=$fl_live_session ";
    $Query .= "AND b.fl_usuario=$fl_alumno ";
    $row2 = RecuperaValor($Query);
    if($row2[0] == 2 OR $row2[0] == 3) { // 1=No asistio, 2=Asistio, 3=Retardo
      $ds_asistencias[$sesiones] = "Yes";
      $fe_asistencia[$sesiones] = ObtenNombreMes($row2[1])." ".$row2[2];
    }
    else
    {
      $fg_asistencias = False;
      $ds_asistencias[$sesiones] = "No";
      if($row1[4] == '1')
        $faltas++;
    }
    $sesiones++;
  }
  # Calcula calificacion maxima
  $row = RecuperaValor("SELECT MAX(no_equivalencia) FROM c_calificacion");
  $max_calificacion = $row[0];
  if(!$fg_asistencias)
    $max_calificacion -= ObtenConfiguracion(43) * $faltas;
  if(!$fg_en_tiempo)
    $max_calificacion -= ObtenConfiguracion(44);
  /*if($fg_entregado == '0')
    $max_calificacion = 0; */ //jgfl
  $row = RecuperaValor("SELECT MAX(no_equivalencia), cl_calificacion FROM c_calificacion WHERE no_equivalencia <= $max_calificacion GROUP BY no_equivalencia DESC");
  $ds_max_calificacion = $row[1];
  
  # Dialogo para asignar calificacion
  if(!empty($tab))
    echo "<form name='datos1' id='datos1'>";
  else
    echo "<form name='datos1' id='datos1' method='POST' action='".PATH_N_MAE_PAGES."/assign_grades.php'>";
  echo "  
    <p>Grade for <b>$ds_nombre</b> on week <b>$no_semana</b>:</p>
    <select name='fl_calificacion' id='fl_calificacion'>
      <option value=0>Pending</option>";
  $Query  = "SELECT fl_calificacion, cl_calificacion, ds_calificacion, fg_aprobado ";
  $Query .= "FROM c_calificacion WHERE no_equivalencia <= $max_calificacion ORDER BY no_min DESC";
  $rs = EjecutaQuery($Query);
  while($row = RecuperaRegistro($rs)) {
    //if($fg_entregado == '1' OR $row[0] == CALIFICACION_NA) {jgfl
      echo "
      <option value=$row[0]";
      if($row[0] == $fl_promedio_semana)
        echo " selected";
      echo ">$row[1] ".str_uso_normal($row[2])."</option>";
    //}
  }
  echo "
    </select>";
  if(!$fg_asistencias OR !$fg_en_tiempo OR $fg_entregado == '0')
    echo " (Max grade limit: <b>$ds_max_calificacion</b>)";
  else
    echo " (No max grade limit)";
  echo "
    <input type='hidden' name='fl_entrega_semanal' id='fl_entrega_semanal' value='$fl_entrega_semanal'>
    <input type='hidden' name='clave' id='clave' value='$clave'>
    <input type='hidden' name='fl_usuario' id='fl_usuario' value='$fl_usuario'>
    <input type='hidden' name='tab' id='tab' value='$tab'>
  </form>";
  echo "
  <p>&nbsp;</p>";
  for($i=1; $i<$sesiones; $i++)
    echo "
    <p>Timely assistance to live session: <b>".$ds_asistencias[$i]."</b><br>Live session: ".$fe_clase[$i]."<br>Check in: ".$fe_asistencia[$i]."</p>";
  echo "
  <p>Timely assignation upload: <b>$ds_en_tiempo</b><br>Submission due date: $fe_entrega<br>Submission date: $fe_entregado</p>";
  
?>