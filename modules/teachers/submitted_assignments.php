<?php
  
  # Libreria de funciones
  require("../common/lib/cam_general.inc.php");
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  $fl_maestro = ValidaSesion(False);
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermisoCampus(FUNC_MAESTROS)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Recibe parametros
  $no_tab = RecibeParametroNumerico('tab', True);
  if(empty($no_tab) OR $no_tab > 3)
    $no_tab = 1;
  $tabs = array(1 => "".ObtenEtiqueta(704)."", 2 => "".ObtenEtiqueta(706)."", 3 => "".ObtenEtiqueta(705)."");
  $tabs[$no_tab] = "<span class='current_week'>".$tabs[$no_tab]."</span>";
  
  # Inicia pagina
  $titulo = "Submitted Assignments";
  PresentaHeader($titulo);
  
  # Presenta separadores
  echo "
  <script type='text/javascript' src='".PATH_COM_JS."/frmAssignGrade.js.php'></script>
  <div id='dlg_grade'><div id='dlg_grade_content'></div></div>
              <tr>
                <td colspan='2' height='10' class='blank_cells'></td>
              </tr>
              <tr>
                <td colspan='2' valign='top' height='568' class='blank_cells'>
                  <table border='".D_BORDES."' cellpadding='0' cellspacing='0' width='100%'>
                    <tr>
                      <td valign='top' height='50' class='division_line'>
                        <table border='".D_BORDES."' cellpadding='0' cellspacing='0' width='100%'>
                          <tr><td colspan='3' height='5'>&nbsp;</td></tr>
                          <tr>
                            <td width='5'>&nbsp;</td>
                            <td>
                              <ul id='maintab' class='week_tabs'>
                                <li><a href='submitted_assignments.php?tab=1'>$tabs[1]</a></li>
                                <li><a href='submitted_assignments.php?tab=3'>$tabs[3]</a></li>
                                <li><a href='submitted_assignments.php?tab=2'>$tabs[2]</a></li>
                              </ul>
                            </td>
                            <td width='5'>&nbsp;</td>
                          </tr>
                        </table>
                      </td>
                    </tr>
                  </table>
                  <table border='".D_BORDES."' cellpadding='0' cellspacing='0' width='100%'>
                    <tr>
                      <td width='10'>&nbsp;</td>
                      <td valign='top' width='720' align='center'>
                        <table border='".D_BORDES."' cellpadding='5' cellspacing='0' width='100%'>
                          <tr>
                            <td width='80'></td>
                            <td width='100'></td>
                            <td></td>
                            <td width='100'></td>
                            <td width='100'></td>
                          </tr>";
  
  # Recupera los grupos del maestro que tengan alumnos activos
  $fe_actual = ObtenFechaActual( );
  $Query  = "SELECT a.fl_grupo, a.nb_grupo, b.fl_semana, DATE_FORMAT(b.fe_entrega, '%c') 'fe_entrega_m', ";
  $Query .= "DATE_FORMAT(b.fe_entrega, '%e, %Y') 'fe_entrega_da', DATE_FORMAT(b.fe_calificacion, '%c') 'fe_calificacion_m', ";
  $Query .= "DATE_FORMAT(b.fe_calificacion, '%e, %Y') 'fe_calificacion_da', c.no_grado, c.no_semana, c.ds_titulo, ";
  $Query .= "c.fg_animacion, c.fg_ref_animacion, c.no_sketch, c.fg_ref_sketch, d.nb_programa, DATEDIFF(b.fe_entrega, '$fe_actual') no_dias ";
  $Query .= "FROM c_grupo a, k_semana b, c_leccion c, c_programa d ";
  $Query .= "WHERE a.fl_term=b.fl_term ";
  $Query .= "AND b.fl_leccion=c.fl_leccion ";
  $Query .= "AND c.fl_programa=d.fl_programa ";
  $Query .= "AND a.fl_maestro=$fl_maestro ";
  $Query .= "AND c.no_semana <= (";
  $Query .= "SELECT MAX(f.no_semana) FROM k_semana e, c_leccion f ";
  $Query .= "WHERE e.fl_leccion=f.fl_leccion ";
  $Query .= "AND TO_DAYS(e.fe_publicacion) <= TO_DAYS('$fe_actual') ";
  $Query .= "AND f.fl_programa=c.fl_programa ";
  $Query .= "AND f.no_grado=c.no_grado ";
  $Query .= "AND e.fl_term=a.fl_term ";
  $Query .= ") ";
  $Query .= "AND EXISTS(SELECT 1 FROM k_alumno_grupo g, c_usuario h WHERE g.fl_alumno=h.fl_usuario AND h.fg_activo='1' AND g.fl_grupo=a.fl_grupo) ";
  $Query .= "AND EXISTS(";
  if($no_tab == 1 OR $no_tab == 2) {
    $Query .= "SELECT 1 FROM k_entrega_semanal i, c_usuario j WHERE i.fl_alumno=j.fl_usuario AND i.fl_grupo=a.fl_grupo AND i.fl_semana=b.fl_semana ";
    if($no_tab == 1) { // Assignments to grade
      $Query .= "AND i.fl_promedio_semana IS NULL ";
      $Query .= "AND EXISTS(SELECT 1 FROM k_entregable k WHERE k.fl_entrega_semanal=i.fl_entrega_semanal) ";
    }
    if($no_tab == 2) // Grading History
      $Query .= "AND i.fl_promedio_semana IS NOT NULL ";
  }
  else {
    $Query .= "SELECT 1 FROM k_alumno_grupo k ";
    $Query .= "WHERE k.fl_grupo=a.fl_grupo ";
    $Query .= "AND NOT EXISTS (SELECT 1 FROM k_entrega_semanal m, k_entregable n WHERE m.fl_entrega_semanal=n.fl_entrega_semanal AND m.fl_alumno=k.fl_alumno AND m.fl_semana=b.fl_semana) ";
    $Query .= "AND NOT EXISTS (SELECT 1 FROM k_entrega_semanal o WHERE o.fl_alumno=k.fl_alumno AND o.fl_semana=b.fl_semana AND o.fl_promedio_semana IS NOT NULL) ";
  }
  $Query .= ") ";
  $Query .= "AND (c.fg_animacion='1' OR c.fg_ref_animacion='1' OR c.no_sketch > 0 OR c.fg_ref_sketch='1') ";
  $Query .= "ORDER BY d.no_orden, c.no_grado, c.no_semana DESC, a.nb_grupo";
  $rs = EjecutaQuery($Query);
  $tot_grupos = CuentaRegistros($rs);
  while($row = RecuperaRegistro($rs)) {
    $fl_grupo = $row[0];
    $nb_grupo = str_uso_normal($row[1]);
    $fl_semana = $row[2];
    $fe_entrega = ObtenNombreMes($row[3])." ".$row[4];
    $fe_calificacion = ObtenNombreMes($row[5])." ".$row[6];
    $no_grado = $row[7];
    $no_semana = $row[8];
    $ds_titulo = str_uso_normal($row[9]);
    $fg_animacion = $row[10];
    $fg_ref_animacion = $row[11];
    $no_sketch = $row[12];
    $fg_ref_sketch = $row[13];
    $nb_programa = str_uso_normal($row[14]);
    $no_dias = $row[15];
    
    # Requerimientos de la leccion
    $ds_animacion = "No assignment";
    if($fg_animacion == '1')
      $ds_animacion = "Assignment";
    $ds_ref_animacion = "No assignment reference";
    if($fg_ref_animacion == '1')
      $ds_ref_animacion = "Assignment reference";
    if($no_sketch == '0')
      $ds_sketch = "No sketches";
    elseif($no_sketch == '1')
      $ds_sketch = "1 sketch";
    else
      $ds_sketch = "$no_sketch sketches";
    $ds_ref_sketch = "No sketch reference";
    if($fg_ref_sketch == '1')
      $ds_ref_sketch = "Sketch reference";
    
    # Inicia bloque de Programa - Grado
    if($nb_programa <> $nb_programa_ant OR $no_grado <> $no_grado_ant OR $no_semana <> $no_semana_ant) {
      echo "
                          <tr><td colspan='5' class='assignments_course'>$nb_programa, Term $no_grado<br>Week $no_semana: $ds_titulo</td></tr>
                          <tr>
                            <td colspan='5' align='center' class='assignments_lesson'>
                              Submission due date is <b>$fe_entrega</b>, Evaluation due date is <b>$fe_calificacion</b>
                              <br>
                              <b>This lesson requires:</b> $ds_animacion, $ds_ref_animacion, $ds_sketch, $ds_ref_sketch
                            </td>
                          </tr>
                          <tr><td colspan='5' height='5'></td></tr>";
      $nb_programa_ant = $nb_programa;
      $no_grado_ant = $no_grado;
      $no_semana_ant = $no_semana;
    }
    
    # Inicia bloque de Grupo
    echo "
                          <tr><td colspan='5' class='assignments_term'>Group $nb_grupo</td></tr>";
    
    # Presenta registros para el tab seleccionado
    if($no_tab == 1 OR $no_tab == 2)
      require("sa_pending.inc.php");
    else
      require("sa_p_submission.inc.php");
  }
  if($tot_grupos == 0) {
    switch($no_tab) {
      case 1: $no_hay = "You have no assignments to grade."; break;
      case 2: $no_hay = "You have no graded active students."; break;
      case 3: $no_hay = "You have no students pending upload."; break;
    }
    echo "
                          <tr>
                            <td colspan='5' align='center' class='assignments_lesson'>
                              {$no_hay}
                            </td>
                          </tr>
                          <tr><td colspan='5' height='5'></td></tr>";
  }
  echo "
                        </table>
                      </td>
                      <td width='10'>&nbsp;</td>
                    </tr>
                    <tr>
                      <td colspan='3'>&nbsp;</td>
                    </tr>";
  
  PresentaFooter( );
  
?>