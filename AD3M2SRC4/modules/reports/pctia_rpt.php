<?php

    /*
      =======    ====     ====     ====   =======
      /==////    /==/==   /==/==  /==/==  /==////
      /==       /==  /==  /== /==/== /==  /==
      /=======  /=======  /== /===== /==  /======
      /==////   /==///==  /==  /===  /==  /==///=
      /==       /==  /==  /==  /===  /==  /==
      /==       /==  /==  /==   //   /==  /=======
      ///       ///  ///  ///        ///  ////////

        **** Oficial Transcript
        **** creator: loomtek
        **** update: jcampos
        **** update: 19/08/2021

    /** Add the Group Class and Global Class data
    *   to the Live class data.
    */

    /** Load required libraries */
    require('../../lib/general.inc.php');
    require_once('../../lib/tcpdf/config/lang/eng.php');
    require_once('../../lib/tcpdf/tcpdf.php');

    /**
     * @var $clave
     * Retrieve the Student ID
     */
    $clave = RecibeParametroNumerico('clave', True);


    if (!empty(RecibeParametroNumerico('unofficial', True))) {
      $fg_unofficial = RecibeParametroNumerico('unofficial', True);
    }else{
      $fg_unofficial = 0;
    }
    
    
    # Recupera datos del aplicante
    $Query="SELECT ds_login, ds_nombres, ds_apaterno, ds_amaterno, ".ConsultaFechaBD('fe_nacimiento', FMT_CAPTURA)." fe_nacimiento, cl_sesion, no_promedio_t 
              FROM c_usuario a, c_perfil b, c_alumno c 
              WHERE a.fl_perfil=b.fl_perfil 
              AND a.fl_usuario=c.fl_alumno 
              AND fl_usuario=$clave";

    $row = RecuperaValor($Query);

    $ds_login = str_texto($row[0]);
    $ds_nombres = str_texto($row[1]);
    $ds_apaterno = str_texto($row[2]);
    $ds_amaterno = str_texto($row[3]);
    $fe_nacimiento = $row[4];
    $cl_sesion = $row[5];
    $no_promedio_t = $row[6];

    $roww = RecuperaValor("SELECT cl_calificacion, fg_aprobado FROM c_calificacion WHERE no_min <= ROUND($no_promedio_t) AND no_max >= ROUND($no_promedio_t)");

    $calificacion = $roww[0];

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
	$Query="SELECT nb_periodo 
	        FROM c_programa a, k_term b, c_periodo c, k_alumno_term d 
	        WHERE a.fl_programa=b.fl_programa 
	        AND b.fl_periodo=c.fl_periodo 
	        AND b.fl_term=d.fl_term AND d.fl_alumno='$fl_usuario' 
	        AND no_grado=1 ";

	$row2 = RecuperaValor($Query);

	$nb_periodo = $row2[0];

  # Recupera datos de Official Transcript
  $Query="SELECT ".ConsultaFechaBD('fe_fin', FMT_FECHA)." fe_fin,
         ". ConsultaFechaBD('fe_completado', FMT_FECHA)." fe_completado, 
         ".ConsultaFechaBD('fe_emision', FMT_FECHA)." fe_emision, 
         ".ConsultaFechaBD('fe_graduacion', FMT_FECHA)." fe_graduacion 
          FROM k_pctia 
          WHERE fl_alumno = $clave 
          AND fl_programa = $fl_programa ";

  $row = RecuperaValor($Query);

  $fe_fin_temp = explode("-", $row[0]);
  $fe_fin = substr(ObtenNombreMes($fe_fin_temp[1]),0,3).' '.$fe_fin_temp[0].', '.$fe_fin_temp[2];
  $fe_completado_temp = explode("-", $row[1]);
  $fe_completado = substr(ObtenNombreMes($fe_completado_temp[1]),0,3).' '.$fe_completado_temp[0].', '.$fe_completado_temp[2];
  $fe_emision_temp = explode("-", $row[2]);
  $fe_emision = substr(ObtenNombreMes($fe_emision_temp[1]),0,3).' '.$fe_emision_temp[0].', '.$fe_emision_temp[2];
  $fe_graduacion_temp = explode("-", $row[3]);
  $fe_graduacion = substr(ObtenNombreMes($fe_graduacion_temp[1]),0,3).' '.$fe_graduacion_temp[0].', '.$fe_graduacion_temp[2];
  $fe_graduate = date('Ymd', strtotime($row[3]));
  
  if(!empty($fg_unofficial)){
	  
	  $fe_completado=null;
	  $fe_graduacion=null;
	  $fe_graduate=null;
	  $fe_fin=null;

      $Query = "Select CURDATE() ";
      $row = RecuperaValor($Query);
      $fe_actual = str_texto($row[0]);
      $fe_actual=strtotime('+0 day',strtotime($fe_actual));
      $fe_actual= date('Y-m-d',$fe_actual);
      $date = date_create($fe_actual);
      $fe_actual = date_format($date, 'F j , Y');

      $fe_emision=$fe_actual;
  }

      /**
       * Set id_template
       * 195 -> transcript's template
       */
      $id_template = 195;

      /**
       * get fl_sesion
       */ 
      $Query  = "SELECT fl_sesion ";                    //Query
      $Query .= "FROM c_sesion ";                       //Query
      $Query .= "WHERE cl_sesion = '".$cl_sesion."';";  //Query
      $row = RecuperaValor($Query);                     //get Query from the RecuperaValor() function
      $fl_sesion = $row[0];

      /**
       * Build the contents
       */
      $ds_header = genera_documento($fl_sesion, 1, $id_template);

      // Header Variables Replacement   
      $ds_header = str_replace("#pg_comdate#",$fe_completado,$ds_header);    
      $ds_header = str_replace("#pg_Issdate#",$fe_emision,$ds_header);
      $ds_header = str_replace("#st_num#",$ds_login,$ds_header);



    /*
     * QR Code
     */
    // set link
    $link_qr='campus.vanas.ca/StudentAccreditation.php?clave='.$fl_sesion.'&type=2&data='.$clave;

    // set style for barcode
    $style = array(
      'border' => 0,
      'padding' => 0,
      'fgcolor' => array(0,0,0),
      'bgcolor' => false, //array(255,255,255)
      'module_width' => 1, // width of a single module in points
      'module_height' => 1 // height of a single module in points
    );


    /* +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++ */
    /* +-----------------------------------------------------------------------------------------------+ */
    /* +----------------- Extend the TCPDF class to create custom Header and Footer -------------------+ */
    /* +-----------------------------------------------------------------------------------------------+ */
    /* +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++ */

  class MYPDF extends TCPDF
  {
    /** Page header construction function */
    public function Header() {
      
      global $ds_header;
      global $link_qr;
      global $style;
      global $fg_unofficial;
   
      // get the current page break margin
      $bMargin = $this->getBreakMargin();
      // get current auto-page-break mode
      $auto_page_break = $this->AutoPageBreak;
      // disable auto-page-break
      $this->SetAutoPageBreak(false, 0);
      // set background image
      if ($fg_unofficial == 1) {
        $img_file = "../../images/Protected_paper_vanas_official_transcript_unofficial.jpg";
      }else{
        $img_file = "../../images/Protected_paper_vanas_official_transcript.jpg";
      }
      $this->Image($img_file, 0, 0, 216, 280, '', '', '', false, 300, '', false, false, 0);
      // restore auto-page-break status
      $this->SetAutoPageBreak($auto_page_break, $bMargin);
      // set the starting point for the page content
      $this->setPageMark();


      // set font
      $this->SetFont('dejavusans', '', 8);
      
      $ds_header = str_replace("#currentpage#",$this->getAliasNumPage(),$ds_header);
      $ds_header = str_replace("#num_pages#",$this->getAliasNbPages(),$ds_header);
      
      $this->writeHTML($ds_header, true, 0, true, 0);

       // QRCODE,L : QR-CODE Low error correction
       $this->write2DBarcode(''.$link_qr.'', 'QRCODE,L', 124, 8, 20, 20, $style, 'N');

       $this->SetFont('dejavusans', '', 6);
       $this->SetXY(120, 29);

       $qr_description = 'Verify Academic Credential';
       $this->writeHTML($qr_description, true, false, false, false, '');
    }

    /** Page footer construction function */
    public function Footer() {

      // get the current page break margin
      $bMargin = $this->getBreakMargin();
      // get current auto-page-break mode
      $auto_page_break = $this->AutoPageBreak;
      // disable auto-page-break
      $this->SetAutoPageBreak(false, 0);
      // set bacground image
      $vanasSealPath = "../../images/vanas_seal.png";
      $this->Image($vanasSealPath, 167, 232, 35, 35, '', '', '', false, 300, '', false, false, 0);
      // restore auto-page-break status
      $this->SetAutoPageBreak($auto_page_break, $bMargin);
      // set the starting point for the page content
      $this->setPageMark();


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


    /* +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++ */
    /* +-----------------------------------------------------------------------------------------------+ */
    /* +------------------------------------ Body Construction ----------------------------------------+ */
    /* +-----------------------------------------------------------------------------------------------+ */
    /* +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++ */

  /** @var $Query
   * Retrieve the group, term and teacher
   */
  $Query="SELECT a.fl_grupo, b.fl_term, c.ds_nombres, c.ds_apaterno, b.nb_grupo 
          FROM k_alumno_grupo a 
          LEFT JOIN (c_grupo b LEFT JOIN c_usuario c ON b.fl_maestro = c.fl_usuario) ON a.fl_grupo = b.fl_grupo 
          WHERE fl_alumno = $clave";

  $row1 = RecuperaValor($Query);

  $fl_grupo = $row1[0];
  $fl_term = $row1[1];
  $nb_maestro = $row1[2].'&nbsp;'.$row1[3];
  $ds_grupo = $row1[4];

  # Buscamos todos los terms que haya cursado
  $QueryT="SELECT a.fl_term, b.no_grado, a.no_promedio 
           FROM k_alumno_term a, k_term b LEFT JOIN c_leccion lec ON(lec.fl_programa=b.fl_programa AND lec.no_grado=b.no_grado) 
           LEFT JOIN c_programa pro ON(pro.fl_programa=b.fl_programa AND pro.fl_programa=lec.fl_programa), c_periodo c 
           WHERE a.fl_term=b.fl_term AND b.fl_periodo=c.fl_periodo AND a.fl_alumno=$clave 
           GROUP BY a.fl_term ORDER BY c.fe_inicio, b.no_grado";

  $rs = EjecutaQuery($QueryT);

  # Empezamos a mostrar los terms que ha cursado el estudiante
  $htmlcontent = '<br>
  <table border="0" cellpadding="0" cellspacing="0" width="100%">';
  $factor_promedio = 0;

  for($tot_grados=1;$row=RecuperaRegistro($rs);$tot_grados++){
    $fl_term = $row[0];
    $no_grado = $row[1];

    if ($grado_repetido == $no_grado)
        $recurse = "<b style='color:red;'>".ObtenEtiqueta(853)."</b>";
    else
        $recurse = "";

    $grado_repetido = $no_grado;
    $no_promedio = $row[2];
    $htmlcontent .= '<tr>';

    if($i == 3)
      $htmlcontent .= '
        <td style="width:100%; height:60px;">';
    else
      $htmlcontent .= '<td style="width:100%; height:10px;">';

    $htmlcontent .= '&nbsp;
                    </td>
                  </tr>
                  <tr>
                    <td style="width:15%; height:15px; color:#000000; font-family:Arial; font-size:12px; font-weight:bold; text-align:center;">
                      &nbsp;
                    </td>
                    <td style="width:85%; height:15px; color:#000000; font-family:Arial; font-size:12px; font-weight:bold; text-align:left;">
                      Term '.$no_grado.' '.$recurse.'
                    </td>
                  </tr>
                ';

    # Buscamos las lecciones del estudiante dependiendo del term y programa
    $QueryT1="SELECT lec.fl_leccion, lec.no_semana, lec.ds_titulo, sem.fl_semana, sem.fe_publicacion 
            FROM c_leccion lec, k_semana sem 
            WHERE lec.fl_leccion = sem.fl_leccion 
            AND lec.fl_programa = $fl_programa 
            AND lec.no_grado = $no_grado
            AND sem.fl_term = $fl_term
            ORDER BY lec.no_semana ";

    $rs2 = EjecutaQuery($QueryT1);

    /**  MAIN LOOP to fill data on Transcript */
    for($lecciones=0;$row2=RecuperaRegistro($rs2);$lecciones++){

      $fl_leccion = $row2[0];
      $no_semana = $row2[1];
      $ds_titulo = str_texto($row2[2]);
      $fl_semana = $row2[3];
      $fecha_temp = explode("-",$row2[4]);
      $fe_publicacion = $fecha_temp[1].'/'.$fecha_temp[0];

      //Revisamos si existe rubric.
      $fg_rubric=ExisteRubric($fl_leccion);

      if(!empty($no_semana))

        $Query="SELECT fl_clase, " . ConsultaFechaBD('fe_clase', FMT_CAPTURA) . " fe_clase, ".ConsultaFechaBD('fe_clase', FMT_HORAMIN) . " hr_clase, fg_obligatorio, fg_adicional, b.fl_entrega_semanal, a.fl_grupo 
                FROM k_clase a, k_entrega_semanal b 
                WHERE a.fl_semana=b.fl_semana 
                AND a.fl_grupo=b.fl_grupo 
                AND b.fl_alumno = $clave 
                AND a.fl_semana=" . $fl_semana. " 
                ORDER BY fl_clase ";

      $cons = EjecutaQuery($Query);

      while ($row3 = RecuperaRegistro($cons)) {
          $fl_clase = $row3[0];

          if (!empty($row3[1])) { # Ya se habia puesto una fecha para la clase
              $fe_clase = $row3[1];
              $hr_clase = $row3[2];
          }

          $fg_obligatorio = $row3[3];
          $fg_adicional = $row3[4];
          $fl_grupo = $row3[5];

          if ($fg_adicional == '1') {
              $adicionales++;
              $no_semana = '';
              $ds_titulo = "";
              $row[0] = '';
              $porcentaje = "";
          } else {

              if($fg_rubric==1){
                  # Revisa si hay calificacion para el alumno en esta leccion
                  $Query = "SELECT b.cl_calificacion, b.ds_calificacion, b.fg_aprobado, b.no_equivalencia ";
                  $Query .= "FROM k_entrega_semanal a, c_calificacion b ";
                  $Query .= "WHERE a.fl_promedio_semana=b.fl_calificacion ";
                  $Query .= "AND a.fl_alumno=$clave ";
                  $Query .= "AND a.fl_semana=" . $fl_semana;
                  $row = RecuperaValor($Query);
                  $porcentaje = explode(".",$row[3]);
              }else{
                  $row=null;
                  $porcentaje="";
              }
          }

          # Consulta el estatus de asistencia a live session
          $Query = "SELECT a.fl_live_session, a.fl_usuario, b.nb_estatus, d.fl_semana
          FROM k_live_session_asistencia a, c_estatus_asistencia b, k_live_session c, k_clase d
          WHERE a.cl_estatus_asistencia = b.cl_estatus_asistencia
          AND a.fl_live_session = c.fl_live_session
          AND c.fl_clase = d.fl_clase
          AND c.fl_clase = " . $fl_clase. " 
          AND d.fl_semana = " . $fl_semana. "
          AND a.fl_usuario = $clave";

          $rasis = RecuperaValor($Query);

          switch ($fg_obligatorio) {
              case '0':
                  $obliga = ETQ_NO;
                  break;
              case '1':
                  $obliga = ETQ_SI;
                  break;
              default:
                  $obliga = '';
          }

          if($lecciones % 2 != 0)
            $bgcolor = '';
          else
            $bgcolor = '';

          $htmlcontent .= '
          <tr>
            <td style="width:16%; height:15px; background-color:'.$bgcolor.'; color:#000000; font-family:Arial; font-size:12px; font-weight:normal; text-align:left;">
            '.$fe_publicacion.'</td>
            <td style="width:7%; height:15px; background-color:'.$bgcolor.'; color:#000000; font-family:Arial; font-size:12px; font-weight:normal; text-align:center;">
            '.$no_semana.'</td>
            <td style="width:41%; height:15px; background-color:'.$bgcolor.'; color:#000000; font-family:Arial; font-size:12px; font-weight:normal; text-align:left;">
            '.$ds_titulo.'</td>
            <td style="width:10%; height:15px; background-color:'.$bgcolor.'; color:#000000; font-family:Arial; font-size:12px; font-weight:normal; text-align:center;">
            '.$obliga.'</td>
            <td style="width:10%; height:15px; background-color:'.$bgcolor.'; color:#000000; font-family:Arial; font-size:12px; font-weight:normal; text-align:center;">';

          if (!empty($rasis[0])) {
              $htmlcontent .= "$rasis[2]";
          }
          else {
              $fecha_clase = RecuperaValor("SELECT fe_clase FROM k_clase WHERE fl_semana = " . $fl_semana . " AND fl_grupo = $fl_grupo");
              $diferencia_fechas = strtotime($fecha_clase[0]) + 1200 - time();

              if ($diferencia_fechas <= 0) {
                $ds_rasis = RecuperaValor("SELECT nb_estatus FROM c_estatus_asistencia d WHERE cl_estatus_asistencia=1");
                $htmlcontent .= "$ds_rasis[0]";
              }
              else
                $htmlcontent .= "&nbsp;";
          }

          $htmlcontent .= '</td>
            <td style="width:8%; height:15px; background-color:'.$bgcolor.'; color:#000000; font-family:Arial; font-size:12px; font-weight:normal; text-align:center;">
            '.$row[0].'</td>
            <td style="width:8%; height:15px; background-color:'.$bgcolor.'; color:#000000; font-family:Arial; font-size:12px; font-weight:normal; text-align:center;">
             '.$porcentaje[0].'
            </td>
          </tr>';

          /** Start of Group Clases  */

          #Recupermos las clases de los grupos y las pintamos.
          $Query = "SELECT DISTINCT c.fl_clase_grupo," . ConsultaFechaBD('c.fe_clase', FMT_CAPTURA) . " fe_clase, c.fg_obligatorio,fg_adicional, ''fl_entrega_semanal, a.fl_grupo, '1' fg_grupo_global, e.fl_term ,c.nb_clase,a.nb_grupo
			        FROM c_grupo a 
					JOIN k_alumno_grupo b ON b.fl_grupo=a.fl_grupo
					JOIN k_clase_grupo c ON c.fl_grupo=a.fl_grupo
					JOIN k_semana_grupo d ON d.fl_semana_grupo=c.fl_semana_grupo
					JOIN k_grupo_term e ON e.fl_grupo= a.fl_grupo 
					AND b.fl_alumno = $clave
					AND e.fl_term=$fl_term 
					AND d.no_semana=$no_semana 
					ORDER BY c.fl_clase_grupo ";

          $rp = RecuperaValor($Query);

          $fl_clase_grupo = $rp[0] ?? NULL;

          if (!empty($fl_clase_grupo)) {

              $nb_clase = $rp['nb_clase'];
              $nb_grupo = $rp['nb_grupo'];
              $fe_clase = $rp['fe_clase'];
              $obliga = $rp['fg_obligatorio'];

              if ($obliga == 1)
                  $obliga = ObtenEtiqueta(16);
              else
                  $obliga = ObtenEtiqueta(17);

              #Recupermaos la asistencia. del alumno.
              $Query = "SELECT c.nb_estatus, (SELECT nb_estatus FROM c_estatus_asistencia WHERE cl_estatus_asistencia=1) 
                        FROM k_live_session_grupal a
						JOIN  k_live_session_asistencia_gg b ON b.fl_live_session_gg=a.fl_live_session_grupal
						JOIN c_estatus_asistencia c ON c.cl_estatus_asistencia=b.cl_estatus_asistencia_gg
						WHERE fl_usuario=$clave 
						AND a.fl_clase_grupo=$fl_clase_grupo ";

              $to = RecuperaValor($Query);

              $nb_ausencia = $to['nb_estatus']??NULL;
              $nb_ausent = $to[1]??NULL;

              if (empty($nb_ausencia))
                  $nb_ausencia = $nb_ausent;

              $htmlcontent .= '<tr>
                    <td style="width:16%; height:15px; background-color:' . $bgcolor . '; color:#000000; font-family:Arial; font-size:12px; font-weight:normal; text-align:left;">
                    ' . $fe_publicacion . '</td>
                    <td style="width:7%; height:15px; background-color:' . $bgcolor . '; color:#000000; font-family:Arial; font-size:12px; font-weight:normal; text-align:center;">
                    ' . $no_semana . '</td>
                    <td style="width:41%; height:15px; background-color:' . $bgcolor . '; color:#000000; font-family:Arial; font-size:12px; font-weight:normal; text-align:left;">
                    ' . $nb_clase . '</td>
                    <td style="width:10%; height:15px; background-color:' . $bgcolor . '; color:#000000; font-family:Arial; font-size:12px; font-weight:normal; text-align:center;">
                    ' . $obliga . '</td>
                    <td style="width:10%; height:15px; background-color:' . $bgcolor . '; color:#000000; font-family:Arial; font-size:12px; font-weight:normal; text-align:center;">
                    ' . $nb_ausencia . '
                    </td>
                    <td style="width:8%; height:15px; background-color:' . $bgcolor . '; color:#000000; font-family:Arial; font-size:12px; font-weight:normal; text-align:left;">&nbsp;</td>
                    <td style="width:8%; height:15px; background-color:' . $bgcolor . '; color:#000000; font-family:Arial; font-size:12px; font-weight:normal; text-align:left;">&nbsp;</td>
                    </tr>';
          }

          /** END Group Clases  */

          /** Start of Global Clases  */

          /** Global classes attendance and groups */
          $Query = "SELECT 
                        B.ds_clase nb_clase,
                        C.ds_titulo nb_grupo, 
                        " . ConsultaFechaBD('C.fe_clase', FMT_CAPTURA) . " fe_clase, 
                        C.fg_obligatorio fg_obligatorio,
                        (SELECT nb_estatus FROM c_estatus_asistencia WHERE cl_estatus_asistencia = D.cl_estatus_asistencia_cg) nb_ausencia,
                        (SELECT nb_estatus FROM c_estatus_asistencia WHERE cl_estatus_asistencia = D.cl_estatus_asistencia_cg) nb_ausent
                    FROM k_alumno_cg A 
                        JOIN c_clase_global B ON(A.fl_clase_global = B.fl_clase_global)
                        JOIN k_clase_cg C ON(A.fl_clase_global = C.fl_clase_global)
                        LEFT JOIN k_live_session_asistencia_cg D ON(A.fl_usuario=D.fl_usuario)
                        JOIN k_alumno_term E ON(A.fl_usuario = E.fl_alumno)
                    WHERE A.fl_usuario = $clave 
                    AND E.fl_term = $fl_term 
                    AND C.no_orden = $no_semana 
                    ORDER BY fl_clase_cg";

          $rp = RecuperaValor($Query);

          $fl_clase_grupo = !empty($rp[0]) ? $rp[0] : NULL;

          if ($fl_clase_grupo) {
              $nb_clase = $rp['nb_clase'];
              $nb_grupo = $rp['nb_grupo'];
              $fe_clase = $rp['fe_clase'];
              $obliga = $rp['fg_obligatorio'];
              $nb_ausencia = $rp['nb_ausencia'] ?? NULL;
              $nb_ausent = $rp['nb_ausent'] ?? NULL;

              if ($obliga == 1)
                  $obliga = ObtenEtiqueta(16);
              else
                  $obliga = ObtenEtiqueta(17);

              if (empty($nb_ausencia))
                  $nb_ausencia = $nb_ausent;

              $htmlcontent .= '<tr>
                    <td style="width:16%; height:15px; background-color:' . $bgcolor . '; color:#000000; font-family:Arial; font-size:12px; font-weight:normal; text-align:left;">
                    ' . $fe_publicacion . '</td>
                    <td style="width:7%; height:15px; background-color:' . $bgcolor . '; color:#000000; font-family:Arial; font-size:12px; font-weight:normal; text-align:center;">
                    ' . $no_semana . '</td>
                    <td style="width:41%; height:15px; background-color:' . $bgcolor . '; color:#000000; font-family:Arial; font-size:12px; font-weight:normal; text-align:left;">
                    ' . $nb_clase . '</td>
                    <td style="width:10%; height:15px; background-color:' . $bgcolor . '; color:#000000; font-family:Arial; font-size:12px; font-weight:normal; text-align:center;">
                    ' . $obliga . '</td>
                    <td style="width:10%; height:15px; background-color:' . $bgcolor . '; color:#000000; font-family:Arial; font-size:12px; font-weight:normal; text-align:center;">
                    ' . $nb_ausencia . '
                    </td>
                    <td style="width:8%; height:15px; background-color:' . $bgcolor . '; color:#000000; font-family:Arial; font-size:12px; font-weight:normal; text-align:left;">&nbsp;</td>
                    <td style="width:8%; height:15px; background-color:' . $bgcolor . '; color:#000000; font-family:Arial; font-size:12px; font-weight:normal; text-align:left;">&nbsp;</td>
                    </tr>';
          }

          /** END Global Clases  */

      }
    }

    $row2 = RecuperaValor("SELECT cl_calificacion, fg_aprobado FROM c_calificacion WHERE no_min <= ROUND($no_promedio) AND no_max >= ROUND($no_promedio)");

    $Term_gpa = $row2[0];

    $htmlcontent .= '<tr>
                  <td style="width:15%; height:15px; color:#000000; font-family:Arial; font-size:12px; font-weight:bold; text-align:center;">
                      &nbsp;
                    </td>
                    <td style="width:85%; height:15px; color:#000000; font-family:Arial; font-size:12px; font-weight:bold; text-align:right;">
                      Term '.$no_grado.' '.ObtenEtiqueta(431).':&nbsp;'.$Term_gpa.'&nbsp;&nbsp;'.$no_promedio.'
                    </td>
                </tr>';
  }

  $htmlcontent .= '<tr>
                    <td style="width:100%; height:10px;">
                      &nbsp;
                    </td>
                  </tr>
                  <tr>
                    <td style="width:84%; height:15px; color:#000000; font-family:Arial; font-size:12px; font-weight:bold; text-align:right;">
                      '.ObtenEtiqueta(524).':
                    </td>
                    <td style="width:8%; height:15px; color:#000000; font-family:Arial; font-size:12px; font-weight:normal; text-align:center;">
                      '.$calificacion.'
                    </td>
                    <td style="width:8%; height:15px; color:#000000; font-family:Arial; font-size:12px; font-weight:normal; text-align:center;">
                     '.round($no_promedio_t).'
                    </td>
                  </tr>
                  <tr>
                    <td style="width:100%; height:5px;">
                      &nbsp;
                    </td>
                  </tr>
                  <tr>
                    <td style="width:100%; height:10px; color:#000000; font-family:Arial; font-size:12px; font-weight:bold; text-align:center; border-top:1px solid black;">
                      &nbsp;
                    </td>
                  </tr>
                  <tr>
                    <td style="width:100%; height:40px; color:#000000; font-family:Arial; font-size:12px; font-weight:bold; text-align:center;">
                      '.ObtenEtiqueta(525).'
                    </td>
                  </tr>
                  <tr>
                    <td style="width:35%; height:120px; color:#000000; font-family:Arial; font-size:12px; font-weight:normal; text-align:center;">
                      '.ObtenEtiqueta(536).'<br/>'.$fe_completado.'
                    </td>
                    <td style="width:30%; height:120px; color:#000000; font-family:Arial; font-size:12px; font-weight:normal; text-align:center;">
                      &nbsp;
                    </td>
                    <td style="width:35%; height:120px; color:#000000; font-family:Arial; font-size:12px; font-weight:normal; text-align:center;">
                      '.ObtenEtiqueta(537).'<br/>'.$fe_graduacion.'
                    </td>
                  </tr>
                </table>
              ';


    /* +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++ */
    /* +-----------------------------------------------------------------------------------------------+ */
    /* +-------------------------------- Create new PDF document --------------------------------------+ */
    /* +-----------------------------------------------------------------------------------------------+ */
    /* +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++ */

  $pdf = new MYPDF('P', 'mm', 'LETTER', true, 'UTF-8', false);
  #$pdf = new TCPDF('P', 'mm', 'LETTER', true);

  // set default header data
  $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);

  //
  $ownerPassword = ObtenConfiguracion(164);
  
  $pdf->SetProtection(array('modify'), '', $ownerPassword, 0, null);


  //do not show header or footer
  #$pdf->SetPrintHeader(false);
  #$pdf->SetPrintFooter(false);

  // set default monospaced font
  $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

  //set margins
  //$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
  $pdf->SetHeaderMargin(5);
  $pdf->SetFooterMargin(5);
  $pdf->SetTopMargin(70);

  //set image scale factor
  $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

  //set some language-dependent strings
  $pdf->setLanguageArray($l);

  // set font
  $pdf->SetFont('helvetica', '', 10);

  // add a page
  $pdf->AddPage("P");

  // output the HTML content
  $pdf->writeHTML($htmlcontent, true, 0, true, 0);

  $nombre_archivo = 'Transcript '.$ds_nombres.' '.$ds_apaterno.'.pdf';
  $nombre_archivo = $ds_nombres.' '.$ds_apaterno.' VANAS Transcripts '.$fe_graduate.'.pdf';

  //Close and output PDF document
  $pdf->Output($nombre_archivo, 'D');

?>