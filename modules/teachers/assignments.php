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
  
  # Presenta cuerpo de la pagina
  $titulo = "Assignments";
  PresentaHeader($titulo);
  
  # Recupera los programas de los grupos que tiene asignados el profesor
  $Query  = "SELECT DISTINCT b.fl_programa, c.nb_programa ";
  $Query .= "FROM c_grupo a, k_term b, c_programa c ";
  $Query .= "WHERE a.fl_term=b.fl_term ";
  $Query .= "AND b.fl_programa=c.fl_programa ";
  $Query .= "AND a.fl_maestro=$fl_maestro ";
  $Query .= "ORDER BY c.no_orden";
  $rs = EjecutaQuery($Query);
  for($tot_programas = 0; $row = RecuperaRegistro($rs); $tot_programas++) {
    $fl_programa[$tot_programas] = $row[0];
    $nb_programa[$tot_programas] = str_uso_normal($row[1]);
    
    # Recupera los grados de cada programa
    $Query  = "SELECT count(fl_leccion), no_grado ";
    $Query .= "FROM c_leccion ";
    $Query .= "WHERE fl_programa=$fl_programa[$tot_programas] ";
    $Query .= "GROUP BY no_grado ";
    $Query .= "ORDER BY no_grado";
    $rs2 = EjecutaQuery($Query);
    for($tot_grados = 0; $row2 = RecuperaRegistro($rs2); $tot_grados++) {
      $tot_lecciones[$tot_programas][$tot_grados] = $row2[0];
      $no_grado[$tot_programas][$tot_grados] = $row2[1];
      
      # Recupera las lecciones del grado
      $Query  = "SELECT fl_leccion, no_semana, ds_titulo ";
      $Query .= "FROM c_leccion ";
      $Query .= "WHERE fl_programa=$fl_programa[$tot_programas] ";
      $Query .= "AND no_grado=".$no_grado[$tot_programas][$tot_grados]." ";
      $Query .= "ORDER BY no_semana";
      $rs3 = EjecutaQuery($Query);
      for($k = 0; $row3 = RecuperaRegistro($rs3); $k++) {
        $fl_leccion[$tot_programas][$tot_grados][$k] = $row3[0];
        $no_semana[$tot_programas][$tot_grados][$k] = $row3[1];
        $ds_titulo[$tot_programas][$tot_grados][$k] = str_uso_normal($row3[2]);
      }
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
                              <table border='".D_BORDES."' cellpadding='0' cellspacing='0' width='100%'>";
  for($i = 0; $i < $tot_programas; $i++) {
    echo "
                                <tr>
                                  <td colspan='2' class='assignments_course'>$nb_programa[$i]</td>                                  
                                </tr>
                                <tr>
                                  <td colspan='2' height='10'>&nbsp;</td>                                  
                                </tr>";
    for($j = 0; $j < $tot_grados; $j++) {
      echo "
                                <tr>
                                  <td colspan='2' class='assignments_term'>Term ".$no_grado[$i][$j]."</td>                                  
                                </tr>";
      for($k = 0; $k < $tot_lecciones[$i][$j]; $k++) {
        echo "
                                <tr>
                                  <td width='100' class='assignments_lesson'>Week ".$no_semana[$i][$j][$k]."</td>
                                  <td class='assignments_lesson'>".$ds_titulo[$i][$j][$k]."</td>
                                </tr>";
      }
      echo "
                                <tr>
                                  <td colspan='2' height='10'></td>                                  
                                </tr>";
    }
  }
  if($tot_programas == 0)
    echo "
                                <tr>
                                  <td colspan='2' align='center' class='assignments_lesson'>
                                  You have no groups assigned.
                                  </td>
                                </tr>";
  echo "
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