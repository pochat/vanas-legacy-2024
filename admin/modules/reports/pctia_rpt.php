<?php
  require('../../lib/general.inc.php');
  require_once('../../lib/tcpdf/config/lang/eng.php');
  require_once('../../lib/tcpdf/tcpdf.php');
  
  $clave = RecibeParametroNumerico('clave', True);
  
  # Recupera datos del aplicante
  $Query  = "SELECT ds_login, ds_nombres, ds_apaterno, ds_amaterno, ";
  $Query .= ConsultaFechaBD('fe_nacimiento', FMT_CAPTURA)." fe_nacimiento, cl_sesion ";
  $Query .= "FROM c_usuario a, c_perfil b, c_alumno c ";
  $Query .= "WHERE a.fl_perfil=b.fl_perfil ";
  $Query .= "AND a.fl_usuario=c.fl_alumno ";
  $Query .= "AND fl_usuario=$clave";
  $row = RecuperaValor($Query);
  $ds_login = str_texto($row[0]);
  $ds_nombres = str_texto($row[1]);
  $ds_apaterno = str_texto($row[2]);
  $ds_amaterno = str_texto($row[3]);
  $fe_nacimiento = $row[4];
  $cl_sesion = $row[5];
  
  # Recupera datos del aplicante: forma 1
  $Query  = "SELECT ds_fname, ds_mname, ds_lname, ";
  $Query .= ConsultaFechaBD('fe_birth', FMT_FECHA)." fe_birth, ";
  $Query .= "nb_programa, ";
  $Query .= ConsultaFechaBD('fe_inicio', FMT_FECHA)." fe_inicio, "; 
  $Query .= "b.fl_programa, c.fl_periodo ";
  $Query .= "FROM k_ses_app_frm_1 a, c_programa b, c_periodo c, c_pais d, c_pais e ";
  $Query .= "WHERE a.fl_programa=b.fl_programa ";
  $Query .= "AND a.fl_periodo=c.fl_periodo ";
  $Query .= "AND a.ds_add_country=d.fl_pais ";
  $Query .= "AND a.ds_eme_country=e.fl_pais ";
  $Query .= "AND cl_sesion='$cl_sesion'";
  $row = RecuperaValor($Query);
  
  $ds_fname = str_texto($row[0]);
  $ds_mname = str_texto($row[1]);
  $ds_lname = str_texto($row[2]);
  $fe_birth = $row[3];
  $nb_programa = $row[4];
  $nb_periodo_temp = explode("-", $row[5]);
  $nb_periodo = substr(ObtenNombreMes($nb_periodo_temp[1]),0,3).' '.$nb_periodo_temp[0].', '.$nb_periodo_temp[2];;
  $fl_programa = $row[6];
  $fl_periodo = $row[7];  
  
		# Recupera el fl_alumno 
	$Query  = "SELECT fl_usuario ";
	$Query .= "FROM c_usuario ";
	$Query .= "WHERE cl_sesion='$cl_sesion'";
	$row2 = RecuperaValor($Query);
	$fl_usuario = $row2[0]; 
  
	# Recupera el program start date 
	$Query  = "SELECT nb_periodo ";
	$Query .= "FROM c_programa a, k_term b, c_periodo c, k_alumno_term d ";
	$Query .= "WHERE a.fl_programa=b.fl_programa ";
	$Query .= "AND b.fl_periodo=c.fl_periodo ";
	$Query .= "AND b.fl_term=d.fl_term AND d.fl_alumno='$fl_usuario' ";
  $Query .= "AND no_grado=1 ";
	$row2 = RecuperaValor($Query);
	$nb_periodo = $row2[0]; 
	
  # Recupera datos de Official Transcript
  $Query = "SELECT ";
  $Query .= ConsultaFechaBD('fe_fin', FMT_FECHA)." fe_fin, ";
  $Query .= ConsultaFechaBD('fe_completado', FMT_FECHA)." fe_completado, ";
  $Query .= ConsultaFechaBD('fe_emision', FMT_FECHA)." fe_emision, ";
  $Query .= ConsultaFechaBD('fe_graduacion', FMT_FECHA)." fe_graduacion ";
  $Query .= "FROM k_pctia ";
  $Query .= "WHERE fl_alumno = $clave ";
  $Query .= "AND fl_programa = $fl_programa ";
  $row = RecuperaValor($Query);
  $fe_fin_temp = explode("-", $row[0]);
  $fe_fin = substr(ObtenNombreMes($fe_fin_temp[1]),0,3).' '.$fe_fin_temp[0].', '.$fe_fin_temp[2];
  $fe_completado_temp = explode("-", $row[1]);
  $fe_completado = substr(ObtenNombreMes($fe_completado_temp[1]),0,3).' '.$fe_completado_temp[0].', '.$fe_completado_temp[2];
  $fe_emision_temp = explode("-", $row[2]);
  $fe_emision = substr(ObtenNombreMes($fe_emision_temp[1]),0,3).' '.$fe_emision_temp[0].', '.$fe_emision_temp[2];
  $fe_graduacion_temp = explode("-", $row[3]);
  $fe_graduacion = substr(ObtenNombreMes($fe_graduacion_temp[1]),0,3).' '.$fe_graduacion_temp[0].', '.$fe_graduacion_temp[2];
  
  // Extend the TCPDF class to create custom Header and Footer
  class MYPDF extends TCPDF 
  {
    //Page header
    public function Header() {
    
      global $clave;
      global $ds_login;
      global $ds_nombres;
      global $ds_apaterno;
      global $ds_amaterno;
      global $fe_nacimiento;
      global $nb_programa;
      global $nb_periodo;
      global $fe_fin;
      global $fe_completado;
      global $fe_emision;
      
        
      $encabezado = '
    <table border="0" cellpadding="0" cellspacing="0" width="100%">
      <tr>
        <td style="width:100%;">
         &nbsp;
        </td>
      </tr>
      <tr>
        <td style="width:20%; height:15px; color:#000000; font-family:Arial; font-size:32px; font-weight:normal; text-align:left;">
          '.ObtenEtiqueta(510).':
        </td>
        <td style="width:40%; height:15px; color:#000000; font-family:Arial; font-size:32px; font-weight:normal; text-align:left;">
          '.$ds_nombres.' '.$ds_amaterno.' '.$ds_apaterno.'
        </td>
        <td rowspan="3" style="width:40%; color:#037EB7; font-family:Tahoma; font-size:32px; text-align:right;">
                <img src="../../images/vanas_logo_transcripts.jpg" />
        </td>
      </tr>
      <tr>
        <td style="width:20%; height:15px; color:#000000; font-family:Arial; font-size:32px; font-weight:normal; text-align:left;">
          '.ObtenEtiqueta(511).':
        </td>
        <td style="width:40%; height:15px; color:#000000; font-family:Arial; font-size:32px; font-weight:normal; text-align:left;">
          '.$ds_login.'
        </td>
      </tr>
      <tr>
        <td style="width:20%; height:15px; color:#000000; font-family:Arial; font-size:32px; font-weight:normal; text-align:left;">
          '.ObtenEtiqueta(120).':
        </td>
        <td style="width:40%; height:15px; color:#000000; font-family:Arial; font-size:32px; font-weight:normal; text-align:left;">
          '.$fe_nacimiento.'
        </td>
      </tr>
      <tr>
        <td style="width:20%; height:15px; color:#000000; font-family:Arial; font-size:32px; font-weight:normal; text-align:left;">
          '.ObtenEtiqueta(512).':
        </td>
        <td style="width:40%; height:15px; color:#000000; font-family:Arial; font-size:32px; font-weight:normal; text-align:left;">
          '.$nb_programa.'
        </td>
        <td style="width:40%; height:15px; color:#000000; font-family:Arial; font-size:32px; font-weight:normal; text-align:right;">
          '.ObtenEtiqueta(520).'
        </td>
      </tr>
      <tr>
        <td style="width:20%; height:15px; color:#000000; font-family:Arial; font-size:32px; font-weight:normal; text-align:left;">
          '.ObtenEtiqueta(60).':
        </td>
        <td style="width:40%; height:15px; color:#000000; font-family:Arial; font-size:32px; font-weight:normal; text-align:left;">
          '.$nb_periodo.'
        </td>
        <td style="width:40%; height:15px; color:#000000; font-family:Arial; font-size:32px; font-weight:normal; text-align:right;">
          '.ObtenEtiqueta(516).'
        </td>
      </tr>
      <tr>
        <td style="width:20%; height:15px; color:#000000; font-family:Arial; font-size:32px; font-weight:normal; text-align:left;">
          '.ObtenEtiqueta(513).':
        </td>
        <td style="width:40%; height:15px; color:#000000; font-family:Arial; font-size:32px; font-weight:normal; text-align:left;">
          '.$fe_fin.'
        </td>
        <td style="width:40%; height:15px; color:#000000; font-family:Arial; font-size:32px; font-weight:normal; text-align:right;">
          '.ObtenEtiqueta(517).'
        </td>
      </tr>
      <tr>
        <td style="width:20%; height:15px; color:#000000; font-family:Arial; font-size:32px; font-weight:normal; text-align:left;">
          '.ObtenEtiqueta(514).':
        </td>
        <td style="width:40%; height:15px; color:#000000; font-family:Arial; font-size:32px; font-weight:normal; text-align:left;">
          '.$fe_completado.'
        </td>
        <td style="width:40%; height:15px; color:#000000; font-family:Arial; font-size:32px; font-weight:normal; text-align:right;">
          '.ObtenEtiqueta(518).'
        </td>
      </tr>
      <tr>
        <td style="width:20%; height:15px; color:#000000; font-family:Arial; font-size:32px; font-weight:normal; text-align:left;">
          '.ObtenEtiqueta(515).':
        </td>
        <td style="width:40%; height:15px; color:#000000; font-family:Arial; font-size:32px; font-weight:normal; text-align:left;">
          '.$fe_emision.'
        </td>
        <td style="width:40%; height:15px; color:#000000; font-family:Arial; font-size:32px; font-weight:normal; text-align:right;">
          '.ObtenEtiqueta(519).'
        </td>
      </tr>
    </table>';
        $this->SetFont('helvetica', '', 10);
        $this->Cell(0, 5, $this->writeHTML($encabezado, true, false, true, false, ''), 0, true, 'J', 0, '', 0, false, 'M', 'B');
        
        $this->SetFont('helvetica', 'B', 10);
        $this->Cell(0, 5, 'OFFICIAL TRANSCRIPT                                 PAGE '.
                    $this->getAliasNumPage().' of '.$this->getAliasNbPages(), 'B', true, 'J', 0, '', 0, false, 'M', 'B');
        $this->Cell(0, 8, '', '', true, '', 0, '', 0, false, 'M', 'B');
        $this->SetFont('helvetica', 'B', 10);
        $this->writeHTMLCell(15, '', '', '', 'Month / Year', 0, 0, 0, true, 'C', true);
        $this->writeHTMLCell(15, '', '', '', 'Week', 0, 0, 0, true, 'C', true);
        $this->writeHTMLCell(95, '', '', '', $nb_programa, 0, 0, 0, true, 'L', true);
        $this->writeHTMLCell(20, '', '', '', 'Mand.', 0, 0, 0, true, 'C', true);
        $this->writeHTMLCell(20, '', '', '', 'Q&A Attnd.', 0, 0, 0, true, 'C', true);
        $this->writeHTMLCell(15, '', '', '', 'Grade', 0, 0, 0, true, 'C', true);
        $this->writeHTMLCell(15, '', '', '', 'Percent', 0, 0, 0, true, 'C', true);
    }

    // Page footer
    public function Footer() {
        $left_column = 'This Transcript is printed on special security paper with a blue background  and the seal of Vancouver Animation School. A raised seal is not required';
        $right_column = 'School Registrar Signature';
        // Position at 15 mm from bottom
        $this->SetY(-20);
        // Set font
        $this->SetFont('helvetica', '', 9);
        $this->writeHTMLCell(110, '', '', '', $left_column, 0, 0, 0, true, 'J', true);
        $this->writeHTMLCell(40, '', '', '', '', 0, 0, 0, true, 'J', true);
        $this->SetFont('helvetica', '', 10);
        $this->writeHTMLCell(0, '', '', '', $right_column, 'T', 0, 0, true, 'C', true);
    }

  }
  
  # Obtiene el grupo, el term y el maestro
  $Query = "SELECT a.fl_grupo, b.fl_term, c.ds_nombres, c.ds_apaterno, b.nb_grupo ";
  $Query .= "FROM k_alumno_grupo a LEFT JOIN (c_grupo b LEFT JOIN c_usuario c ON b.fl_maestro = c.fl_usuario) ON a.fl_grupo = b.fl_grupo ";
  $Query .= "WHERE fl_alumno = $clave";
  $row1 = RecuperaValor($Query);
  $fl_grupo = $row1[0];
  $fl_term = $row1[1];
  $nb_maestro = $row1[2].'&nbsp;'.$row1[3];
  $ds_grupo = $row1[4];
  
  
  # Recupera los nivles del programa
  $Query  = "SELECT count(a.fl_leccion), a.no_grado, b.nb_programa ";
  $Query .= "FROM c_leccion a, c_programa b ";
  $Query .= "WHERE a.fl_programa=b.fl_programa ";
  $Query .= "AND a.fl_programa=$fl_programa ";
  $Query .= "GROUP BY a.no_grado ";
  $Query .= "ORDER BY a.no_grado";
  $rs = EjecutaQuery($Query);
  
  # Recupera los distintos fl_term en los que ha estado un alumno
  /*$Query  = "SELECT a.fl_term ";
  $Query .= "FROM k_alumno_term a, k_term b, c_periodo c ";
  $Query .= "WHERE a.fl_term=b.fl_term AND b.fl_periodo=c.fl_periodo AND a.fl_alumno=$clave ";
  $Query .= "ORDER BY c.fe_inicio, b.no_grado";*/
  $Query  = "SELECT MAX(a.fl_term) ";
  $Query .= "FROM k_alumno_term a, k_term b, c_periodo c ";
  $Query .= "WHERE a.fl_term=b.fl_term AND b.fl_periodo=c.fl_periodo AND a.fl_alumno=$clave ";
  $Query .= "GROUP BY b.no_grado ORDER BY c.fe_inicio, b.no_grado";
  $consulta = EjecutaQuery($Query);
  
  for($tot_grados = 0; $row = RecuperaRegistro($rs); $tot_grados++) {
    $tot_lecciones[$tot_grados] = $row[0];
    $no_grado[$tot_grados] = $row[1];
    $nb_programa = str_uso_normal($row[2]);
    $row_term = RecuperaRegistro($consulta);
    $term_nivel = $row_term[0];
    if(empty($term_nivel))
      $term_nivel = $fl_term;
    
    # Recupera las lecciones del grado
    $Query  = "SELECT a.fl_leccion, a.no_semana, a.ds_titulo, b.fl_semana, b.fe_publicacion ";
    $Query .= "FROM c_leccion a LEFT JOIN k_semana b ";
    $Query .= "ON (a.fl_leccion=b.fl_leccion AND b.fl_term=$term_nivel) ";
    $Query .= "WHERE a.fl_programa=$fl_programa ";
    $Query .= "AND a.no_grado=$no_grado[$tot_grados] ";
    $Query .= "ORDER BY a.no_semana";
    $rs2 = EjecutaQuery($Query);
    for($j = 0; $row2 = RecuperaRegistro($rs2); $j++) {
      $fl_leccion[$tot_grados][$j] = $row2[0];
      $no_semana[$tot_grados][$j] = $row2[1];
      $ds_titulo[$tot_grados][$j] = str_uso_normal($row2[2]);
      $fl_semana[$tot_grados][$j] = $row2[3];
      $fecha_temp = explode("-",$row2[4]); 
      $fe_publicacion[$tot_grados][$j] = $fecha_temp[1].'/'.$fecha_temp[0];
    }
  }
  
  $htmlcontent = '
    <table border="0" cellpadding="0" cellspacing="0" width="100%">
  ';
  
  # Presenta datos los cursos impartidos
  $factor_promedio = 0;
  for($i = 0; $i < $tot_grados; $i++) {
    $htmlcontent .= '
      <tr>';
    if($i == 3)
      $htmlcontent .= '
        <td style="width:100%; height:60px;">';
    else
      $htmlcontent .= '
        <td style="width:100%; height:10px;">';
    $htmlcontent .= '
          &nbsp;
        </td>
      </tr>
      <tr>
        <td style="width:15%; height:15px; color:#000000; font-family:Arial; font-size:35px; font-weight:bold; text-align:center;">
          &nbsp;
        </td>
        <td style="width:85%; height:15px; color:#000000; font-family:Arial; font-size:35px; font-weight:bold; text-align:left;">
          Term '.$no_grado[$i].'
        </td>
      </tr>
    ';
    $adicionales = 0;
    for($j = 0; $j < $tot_lecciones[$i]; $j++) 
    {
      if(!empty($no_semana[$i][$j]))
      {
        if($j % 2 != 0)
          $bgcolor = '#FFFFFF';
        else
          $bgcolor = '#E6E1DE';
          
        $Query  = "SELECT fl_clase, ".ConsultaFechaBD('fe_clase', FMT_CAPTURA)." fe_clase, ";
        $Query .= ConsultaFechaBD('fe_clase', FMT_HORAMIN)." hr_clase, fg_obligatorio, fg_adicional ";
        $Query .= "FROM k_clase a, k_entrega_semanal b ";
        $Query .= "WHERE a.fl_semana=b.fl_semana ";
        $Query .= "AND a.fl_grupo=b.fl_grupo ";
        $Query .= "AND b.fl_alumno=$clave ";
        $Query .= "AND a.fl_semana=".$fl_semana[$i][$j]." ";
        $Query .= "ORDER BY fl_clase";
        $cons = EjecutaQuery($Query);
        while($row2 = RecuperaRegistro($cons))
        {
          $fl_clase[$i][$j] = $row2[0];
          if(!empty($row2[1])) # Ya se habia puesto una fecha para la clase
          { 
            $fe_clase[$i][$j] = $row2[1];
            $hr_clase[$i][$j] = $row2[2];
          }
          $fg_obligatorio[$i][$j] = $row2[3];
          $fg_adicional[$i][$j] = $row2[4];
          
          if($fg_adicional[$i][$j] == '1')
          {
            $adicionales++;
            $no_semana[$i][$j] = '';
            $ds_titulo[$i][$j] = '';
            $row[0] = '';
            $percent[0] = '';
          }
          else
          {
            # Revisa si hay calificacion para el alumno en esta leccion
            $Query  = "SELECT b.cl_calificacion, b.ds_calificacion, b.fg_aprobado, b.no_equivalencia ";
            $Query .= "FROM k_entrega_semanal a, c_calificacion b ";
            $Query .= "WHERE a.fl_promedio_semana=b.fl_calificacion ";
            $Query .= "AND a.fl_alumno=$clave ";
            $Query .= "AND a.fl_semana=".$fl_semana[$i][$j];
            $row = RecuperaValor($Query);
            $percent = explode(".",$row[3]);
            $suma_cal = $suma_cal + $row[3];
            if(!empty($row[3]))
             $factor_promedio++;
          }
          # Consulta el estatus de asistencia a live session
          $Query = "SELECT a.fl_live_session, a.fl_usuario, b.nb_estatus, d.fl_semana
                    FROM k_live_session_asistencia a, c_estatus_asistencia b, k_live_session c, k_clase d
                    WHERE a.cl_estatus_asistencia = b.cl_estatus_asistencia
                    AND a.fl_live_session = c.fl_live_session
                    AND c.fl_clase = d.fl_clase
                    AND c.fl_clase = ".$fl_clase[$i][$j]." 
                    AND d.fl_semana = ".$fl_semana[$i][$j]."
                    AND a.fl_usuario = $clave";
          $rasis = RecuperaValor($Query);

          switch($fg_obligatorio[$i][$j])
          {
          case '0':
            $obliga = ETQ_NO;
          break;
          case '1':
            $obliga = ETQ_SI;
          break;
          default:
            $obliga = '';
          }
          
          $htmlcontent .= '
      <tr>
        <td style="width:8%; height:15px; background-color:'.$bgcolor.'; color:#000000; font-family:Arial; font-size:35px; font-weight:normal; text-align:left;">
          '.$fe_publicacion[$i][$j].'
        </td>
        <td style="width:7%; height:15px; background-color:'.$bgcolor.'; color:#000000; font-family:Arial; font-size:35px; font-weight:normal; text-align:center;">
          '.$no_semana[$i][$j].'
        </td>
        <td style="width:49%; height:15px; background-color:'.$bgcolor.'; color:#000000; font-family:Arial; font-size:35px; font-weight:normal; text-align:left;">
          '.$ds_titulo[$i][$j].'
        </td>
        <td style="width:10%; height:15px; background-color:'.$bgcolor.'; color:#000000; font-family:Arial; font-size:35px; font-weight:normal; text-align:center;">
          '.$obliga.'
        </td>
        <td style="width:10%; height:15px; background-color:'.$bgcolor.'; color:#000000; font-family:Arial; font-size:35px; font-weight:normal; text-align:center;">';
          
          if(!empty($rasis[0]))
          {
            $htmlcontent .= 
          $rasis[2];
          }
          else
          {
            $fecha_clase = RecuperaValor("SELECT fe_clase FROM k_clase WHERE fl_semana = ".$fl_semana[$i][$j]." AND fl_grupo = $fl_grupo");
            $diferencia_fechas = strtotime($fecha_clase[0]) + 1200 - time();
            if($diferencia_fechas <= 0)
            {
              $ds_rasis = RecuperaValor("SELECT nb_estatus FROM c_estatus_asistencia d WHERE cl_estatus_asistencia=1");
              $htmlcontent .= 
          $ds_rasis[0];
            }
            else
              $htmlcontent .= '
          &nbsp;';
          }
          $htmlcontent .= '    
        </td>';
          if(!empty($row[0]))
          {
            $htmlcontent .= '    
        <td style="width:8%; height:15px; background-color:'.$bgcolor.'; color:#000000; font-family:Arial; font-size:35px; font-weight:normal; text-align:center;">
          '.$row[0];
          }
          else
            $htmlcontent .= '
        <td style="width:8%; height:15px; background-color:'.$bgcolor.'; color:#000000; font-family:Arial; font-size:35px; font-weight:normal; text-align:center;">
          &nbsp;';
          $htmlcontent .= '
        </td>
        <td style="width:8%; height:15px; background-color:'.$bgcolor.'; color:#000000; font-family:Arial; font-size:35px; font-weight:normal; text-align:center;">
         '.$percent[0].'
        </td>
      </tr>';  
        }
      }
    }
  }
  $promedio = round(($suma_cal / $factor_promedio)*100)/100;
  $prom = floor($promedio);
  $Query = "SELECT cl_calificacion FROM c_calificacion WHERE no_min <= ".round($promedio)." AND no_max >= ".round($promedio)."";
  $row = RecuperaValor($Query);
  $htmlcontent .= '
      <tr>
        <td style="width:100%; height:10px;">
          &nbsp;
        </td>
      </tr>
      <tr>
        <td style="width:84%; height:15px; color:#000000; font-family:Arial; font-size:35px; font-weight:bold; text-align:right;">
          '.ObtenEtiqueta(524).':
        </td>
        <td style="width:8%; height:15px; color:#000000; font-family:Arial; font-size:35px; font-weight:normal; text-align:center;">
          '.$row[0].'
        </td>
        <td style="width:8%; height:15px; color:#000000; font-family:Arial; font-size:35px; font-weight:normal; text-align:center;">
         '.round($promedio).'
        </td>
      </tr>
      <tr>
        <td style="width:100%; height:5px;">
          &nbsp;
        </td>
      </tr>
      <tr>
        <td style="width:100%; height:10px; color:#000000; font-family:Arial; font-size:35px; font-weight:bold; text-align:center; border-top:1px solid black;">
          &nbsp;
        </td>
      </tr>
      <tr>
        <td style="width:100%; height:40px; color:#000000; font-family:Arial; font-size:35px; font-weight:bold; text-align:center;">
          '.ObtenEtiqueta(525).'
        </td>
      </tr>
      <tr>
        <td style="width:35%; height:120px; color:#000000; font-family:Arial; font-size:35px; font-weight:normal; text-align:center;">
          '.ObtenEtiqueta(536).'<br/>'.$fe_completado.'
        </td>
        <td style="width:30%; height:120px; color:#000000; font-family:Arial; font-size:35px; font-weight:normal; text-align:center;">
          &nbsp;
        </td>
        <td style="width:35%; height:120px; color:#000000; font-family:Arial; font-size:35px; font-weight:normal; text-align:center;">
          '.ObtenEtiqueta(537).'<br/>'.$fe_graduacion.'
        </td>
      </tr>
    </table>
  ';
  
  // create new PDF document
  $pdf = new MYPDF('P', 'mm', 'LETTER', true, 'UTF-8', false);
  #$pdf = new TCPDF('P', 'mm', 'LETTER', true);
  
  // set default header data
  $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);


  //do not show header or footer
  #$pdf->SetPrintHeader(false); 
  #$pdf->SetPrintFooter(false);

  // set default monospaced font
  $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

  //set margins
  //$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
  $pdf->SetHeaderMargin(5);
  $pdf->SetFooterMargin(5);
  $pdf->SetTopMargin(60);

  //set auto page breaks
  $pdf->SetAutoPageBreak(TRUE, 25);

  //set image scale factor
  $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO); 

  //set some language-dependent strings
  $pdf->setLanguageArray($l); 

  // ---------------------------------------------------------

  // set font
  $pdf->SetFont('helvetica', '', 10); 

  // add a page
  $pdf->AddPage("P"); 
    
  // output the HTML content
  $pdf->writeHTML($htmlcontent, true, 0, true, 0); 
  
  $nombre_archivo = 'Transcript '.$ds_nombres.' '.$ds_apaterno.'.pdf';
  //Close and output PDF document
  $pdf->Output($nombre_archivo, 'D');

?>