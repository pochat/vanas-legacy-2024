<?php
  
  # Libreria de funciones
  require("../common/lib/cam_general.inc.php");
  
  # Verifica que exista una sesion valida en el cookie y la resetea
  $fl_alumno = ValidaSesion(False);
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(!ValidaPermisoCampus(FUNC_ALUMNOS)) {
    MuestraPaginaError(ERR_SIN_PERMISO);
    exit;
  }
  
  # Presenta contenido de la pagina
  $titulo = "Assignments";
  PresentaHeader($titulo);
  
  # Recupera el programa y term que esta cursando el alumno
  $fl_programa = ObtenProgramaAlumno($fl_alumno);
  $fl_grupo = ObtenGrupoAlumno($fl_alumno);
  $fl_term = ObtenTermAlumno($fl_alumno);
  
  # Recupera los nivles del programa
  $Query  = "SELECT count(a.fl_leccion), a.no_grado, b.nb_programa ";
  $Query .= "FROM c_leccion a, c_programa b ";
  $Query .= "WHERE a.fl_programa=b.fl_programa ";
  $Query .= "AND a.fl_programa=$fl_programa ";
  $Query .= "GROUP BY a.no_grado ";
  $Query .= "ORDER BY a.no_grado";
  $rs = EjecutaQuery($Query);
  
  # Recupera los distintos fl_term en los que ha estado un alumno
  $Query  = "SELECT a.fl_term ";
  $Query .= "FROM k_alumno_term a, k_term b, c_periodo c ";
  $Query .= "WHERE a.fl_term=b.fl_term AND b.fl_periodo=c.fl_periodo AND a.fl_alumno=$fl_alumno ";
  $Query .= "ORDER BY c.fe_inicio, b.no_grado";
  $consulta = EjecutaQuery($Query);
  
  for($tot_grados = 0; $row = RecuperaRegistro($rs); $tot_grados++) {
    $tot_lecciones[$tot_grados] = $row[0];
    $no_grado[$tot_grados] = $row[1];
    $nb_programa = str_uso_normal($row[2]);
    $row_term = RecuperaRegistro($consulta);
    
    # Recupera las lecciones del grado
    $Query  = "SELECT a.fl_leccion, a.no_semana, a.ds_titulo, b.fl_semana ";
    $Query .= "FROM c_leccion a LEFT JOIN k_semana b ";
    $Query .= "ON (a.fl_leccion=b.fl_leccion AND b.fl_term=$row_term[0]) ";
    $Query .= "WHERE a.fl_programa=$fl_programa ";
    $Query .= "AND a.no_grado=$no_grado[$tot_grados] ";
    $Query .= "ORDER BY a.no_semana";
    $rs2 = EjecutaQuery($Query);
    for($j = 0; $row2 = RecuperaRegistro($rs2); $j++) {
      $fl_leccion[$tot_grados][$j] = $row2[0];
      $no_semana[$tot_grados][$j] = $row2[1];
      $ds_titulo[$tot_grados][$j] = str_uso_normal($row2[2]);
      $fl_semana[$tot_grados][$j] = $row2[3];
    }
  }
  
  # Presenta datos los cursos impartidos
  echo "
              <tr>
                <td colspan='2' height='10' class='blank_cells'></td>
              </tr>
              <tr>
                <td colspan='2' height='568' class='blank_cells'>
                  <table border='".D_BORDES."' cellpadding='0' cellspacing='0' width='100%'>
                    <tr>
                      <td colspan='3' valign='top' height='80' class='division_line'>
                        <br>
                        <table border='".D_BORDES."' cellpadding='0' cellspacing='0' width='100%'>
                          <tr>
                            <td width='10'>&nbsp;</td>
                            <td valign='top'>
                              <table border='".D_BORDES."' cellpadding='0' cellspacing='0' width='100%'>
                                <tr>
                                  <td colspan='3' class='assignments_course'>$nb_programa</td>                                  
                                </tr>
                                <tr>
                                  <td colspan='3' height='10'>&nbsp;</td>                                  
                                </tr>";
  $factor_promedio_t = 0;
  for($i = 0; $i < $tot_grados; $i++) {
    echo "
                                <tr>
                                  <td colspan='3' class='assignments_term'>Term $no_grado[$i]</td>                                  
                                </tr>";
    $factor_promedio_g = 0;
    $suma_cal_g = 0;
    for($j = 0; $j < $tot_lecciones[$i]; $j++) 
    {
      if(!empty($no_semana[$i][$j]))
      {
        echo "
                                <tr>
                                  <td width='100' class='assignments_lesson'>Week ".$no_semana[$i][$j]."</td>
                                  <td class='assignments_lesson'>".$ds_titulo[$i][$j]."</td>
                                  <td width='200' class='assignments_lesson'>";
        
        # Revisa si hay calificacion para el alumno en esta leccion
        $Query  = "SELECT b.cl_calificacion, b.ds_calificacion, b.fg_aprobado, b.no_equivalencia ";
        $Query .= "FROM k_entrega_semanal a, c_calificacion b ";
        $Query .= "WHERE a.fl_promedio_semana=b.fl_calificacion ";
        $Query .= "AND a.fl_alumno=$fl_alumno ";
        // $Query .= "AND a.fl_grupo=$fl_grupo ";
        $Query .= "AND a.fl_semana=".$fl_semana[$i][$j];
        $row = RecuperaValor($Query);
        $suma_cal_g += $row[3];
        $suma_cal_t += $row[3];
        if(!empty($row[0]))
        {
          echo "
                                    $row[0] $row[1]";
          $factor_promedio_g++;
          $factor_promedio_t++;
        }
        else
          echo "
                                    &nbsp;";
        echo "
                                  </td>
                                </tr>";
      }
    }
    $promedio_g = round(($suma_cal_g / $factor_promedio_g)*100)/100;
    $promedio_g1 = round($suma_cal_g / $factor_promedio_g);
    $Query = "SELECT cl_calificacion FROM c_calificacion WHERE no_min <= $promedio_g1 AND no_max >= $promedio_g1";
    $prom_g = RecuperaValor($Query);
    echo "
                                <tr>
                                  <td colspan='2'>&nbsp;</td>
                                  <td class='assignments_lesson'>
                                    Term  $no_grado[$i] GPA:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
    if($promedio_g > 0)
      echo "
                                    $prom_g[0]&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$promedio_g%";
    else
      echo "                        &nbsp;";
    echo "
                                  </td>                                  
                                </tr>
                                <tr>
                                  <td colspan='3' height='10'></td>                                  
                                </tr>";
  }
  $promedio_t = round(($suma_cal_t / $factor_promedio_t)*100)/100;
  $Query = "SELECT cl_calificacion FROM c_calificacion WHERE no_min <= $promedio_t AND no_max >= $promedio_t";
  $prom_t = RecuperaValor($Query);
  echo "
                                <tr>
                                  <td colspan='3' class='assignments_course'>".ObtenEtiqueta(524).":&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$prom_t[0]&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$promedio_t%</td>
                                </tr>
                              </table>
                            </td>
                            <td width='10'>&nbsp;</td>
                          </tr>
                        </table>
                        <br>
                      </td>
                    </tr>";
  
  PresentaFooter( );
  
?>