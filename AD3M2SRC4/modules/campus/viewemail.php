<?php

  /* +-----------------------------------------------------------------------------------------------+ */
  /* +-------------------------------- Libreria de funciones ----------------------------------------+ */
  /* +-----------------------------------------------------------------------------------------------+ */
  require '../../lib/general.inc.php';
  require_once('../../lib/tcpdf/config/lang/eng.php');
  require_once('../..//lib/tcpdf/tcpdf.php');


  /* +-----------------------------------------------------------------------------------------------+ */
  /* +-------------------------------- Recibimos parametros -----------------------------------------+ */
  /* +-----------------------------------------------------------------------------------------------+ */
  $fl_alumno_template = RecibeParametroNumerico('fl_alumno_template', True);
  $fl_sesion = RecibeParametroNumerico('fl_sesion', True);


  /* +-----------------------------------------------------------------------------------------------+ */
  /* +------------------------ Obtenemos los datos de la persona ------------------------------------+ */
  /* +-----------------------------------------------------------------------------------------------+ */
  $Query  = "SELECT ds_fname, ds_mname, ds_lname ";
  $Query .= "FROM k_ses_app_frm_1 a, c_sesion b ";
  $Query .= "WHERE a.cl_sesion=b.cl_sesion AND fl_sesion='$fl_sesion'";
  $row = RecuperaValor($Query);
  $ds_fname = str_texto($row[0]);
  $ds_mname = str_texto($row[1]);
  $ds_lname = str_texto($row[2]);
  $ds_nombre = $ds_fname." ".$ds_mname." ".$ds_lname;



  $Query  = "SELECT DATE_FORMAT(s.fe_envio,'%d-%m-%Y'), DATE_FORMAT(s.fe_envio,'%M-%d-%Y'),s.ds_header, s.ds_body, s.ds_footer, ";
  $Query .= "(SELECT nb_template FROM k_template_doc i WHERE i.fl_template=s.fl_template), fl_template ";
  $Query .= "FROM k_alumno_template s WHERE s.fl_alumno_template=$fl_alumno_template";
  $row = RecuperaValor($Query);
  $Date = $row[0];
  $Date2 = $row[1];
  $ds_cuerpo = str_ascii(str_texto($row[3]));
  $nb_template = $row[5];
  $fl_template = $row[6];


  /* +-----------------------------------------------------------------------------------------------+ */
  /* +-------------------------------- Get cl_sesion ------------------------------------------------+ */
  /* +-----------------------------------------------------------------------------------------------+ */
  $Query  = "SELECT cl_sesion ";                      //Query
  $Query .= "FROM c_sesion ";                        //Query
  $Query .= "WHERE fl_sesion=$fl_sesion";           //Query
  $row = RecuperaValor($Query);                       //get Query from the RecuperaValor() function
  $cl_sesion = $row[0];                               //set the cl_sesion from the results


  # Get Student data
  $Query  = "SELECT ds_nombres, ds_apaterno, ds_amaterno, nb_programa,fl_usuario ";
  $Query .= "FROM k_ses_app_frm_1 a, c_programa b, c_usuario c ";
  $Query .= "WHERE a.fl_programa=b.fl_programa AND a.cl_sesion=c.cl_sesion ";
  $Query .= "AND a.cl_sesion='$cl_sesion'";
  $row = RecuperaValor($Query);
  $ds_nombres = str_texto($row[0]);
  $ds_apaterno = str_texto($row[1]);
  $ds_amaterno = str_texto($row[2]);
  $nb_programa = $row[3];
  $fl_alumno = $row[4];

  // Set full name (first name, last name)
  $fullname = $ds_nombres." ".$ds_amaterno." ".$ds_apaterno;


  /* +-----------------------------------------------------------------------------------------------+ */
  /* +----------------------------------- Get fe_graduate -------------------------------------------+ */
  /* +-----------------------------------------------------------------------------------------------+ */
  $Query  = "SELECT fe_graduacion ";
  $Query .= "FROM k_pctia ";
  $Query .= "WHERE fl_alumno=$fl_alumno";
  $row = RecuperaValor($Query);

  if(!empty($row[0])){
    $fe_graduacion = date('F jS, Y', strtotime($row[0]));
    $fe_graduate = date('Ymd', strtotime($row[0]));
  }
  else{
    $fe_graduacion = "";
  }


  /* +-----------------------------------------------------------------------------------------------+ */
  /* +-------------------------------- Diploma PDF --------------------------------------------------+ */
  /* +-----------------------------------------------------------------------------------------------+ */
  if($fl_template == 194){

    # Recupera datos del aplicante: forma 1
    $Query  = "SELECT b.fl_programa, c.fl_periodo ";
    $Query .= "FROM k_ses_app_frm_1 a, c_programa b, c_periodo c, c_pais d, c_pais e ";
    $Query .= "WHERE a.fl_programa=b.fl_programa ";
    $Query .= "AND a.fl_periodo=c.fl_periodo ";
    $Query .= "AND a.ds_add_country=d.fl_pais ";
    $Query .= "AND a.ds_eme_country=e.fl_pais ";
    $Query .= "AND cl_sesion='$cl_sesion'";

    $row = RecuperaValor($Query);

    $fl_programa = $row[0];

    # Recupera datos de Official Transcript
    $Query="SELECT ".ConsultaFechaBD('fe_fin', FMT_FECHA)." fe_fin,
    ". ConsultaFechaBD('fe_completado', FMT_FECHA)." fe_completado,
    ".ConsultaFechaBD('fe_emision', FMT_FECHA)." fe_emision,
    ".ConsultaFechaBD('fe_graduacion', FMT_FECHA)." fe_graduacion
    FROM k_pctia
    WHERE fl_alumno = $fl_alumno
    AND fl_programa = $fl_programa ";

    $row = RecuperaValor($Query);

    $fe_fin_temp = explode("-", $row[0]);
    $fe_fin = substr(ObtenNombreMes($fe_fin_temp[1]),0,3).' '.$fe_fin_temp[0].', '.$fe_fin_temp[2];

    //get diploma/certificate
    $Query="SELECT ds_credential FROM k_programa_costos where fl_programa=$fl_programa ";
    $row=RecuperaValor($Query);
    $ds_credential=$row['ds_credential'];


    // count the strings
    $nb_program_string_count = strlen($nb_programa." ".$ds_credential);
    $fullname_string_count = strlen($fullname);

    /**
     * Set header, body and footer
     */
    $ds_header = genera_documento($fl_sesion, 1, $fl_template);
    $ds_cuerpo = genera_documento($fl_sesion, 2, $fl_template);
    $ds_footer = genera_documento($fl_sesion, 3, $fl_template);

    // Variables Replacement
    $ds_cuerpo = str_replace("#pg_emisiond#",$fe_graduacion,$ds_cuerpo);
    $ds_cuerpo = str_replace("#st_full_name#",$fullname,$ds_cuerpo);

    $ds_cuerpo = str_replace("#fe_diploma#", $fe_fin, $ds_cuerpo);

    // resize for a large full name
    if($fullname_string_count >= 23){
      $name_div = '<div class="names" style="font-size:85px; font-weight:bold; font-family:Arial">';
      $new_name_div = '<div class="names" style="font-size:58px; font-weight:bold; font-family:Arial">';
      $ds_cuerpo = str_replace($name_div,$new_name_div,$ds_cuerpo);
    }


    /**
     * QR Code
     */
    // set link
    $link_qr='campus.vanas.ca/StudentAccreditation.php?clave='.$fl_sesion.'&type=1&data='.$fl_alumno;

    // set style for barcode
    $style = array(
      'border' => 0,
      'padding' => 0,
      'fgcolor' => array(0,0,0),
      'bgcolor' => false, //array(255,255,255)
      'module_width' => 1, // width of a single module in points
      'module_height' => 1 // height of a single module in points
    );

    /**
     * PDF Code
     */
    // create new PDF document
    $pdf = new TCPDF('P', 'mm', 'LETTER', true);

    //do not show header or footer
    $pdf->SetPrintHeader(false);
    $pdf->SetPrintFooter(false);

    // set default monospaced font
    $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

    //set margins
    //$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
    $pdf->SetHeaderMargin(5);
    $pdf->SetFooterMargin(5);


    //set image scale factor
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

    //set some language-dependent strings
    $pdf->setLanguageArray($l);

    // ---------------------------------------------------------

    // set font
    $pdf->SetFont('dejavusans', '', 10);

    // add a page
    $pdf->AddPage("P");

    //Background
    $bMargin = $pdf->getBreakMargin();
    //set auto page breaks
    $pdf->SetAutoPageBreak(false, 0);
    // image path
    $img_file = "../../images/VANAS_Diploma_2021_blank.png";
    // inserte image
    $pdf->Image($img_file, 0, 0, 216, 280, '', '', '', false, 300, '', false, false, 0);
    // image is a pageMark
    $pdf->setPageMark();

    // Set Margins
    $left = 25;     // left margin  39
    $top = 0;       // top margin   87
    $right = 20;    // right margin 20
    $pdf->SetMargins($left,$top,$right,true);

    //Margin of the body
    $body = '
    <div>
      '.$ds_cuerpo.'
    </div>';

    // output the HTML content
    $pdf->writeHTML($body, true, 0, true, 0);


    // QRCODE,L : QR-CODE Low error correction
    $pdf->write2DBarcode(''.$link_qr.'', 'QRCODE,L', 165, 224, 23, 23, $style, 'N');

    // print date and Sign
    if($nb_program_string_count <= 31){

        //if($fullname_string_count <= 23) //19
        //{
      //  $pdf->SetXY(29, 195); //define la altura date fecha. no existe algo defina exactamente la logica
        //}
        //if($fullname_string_count > 23) //19
        //{
        //	$pdf->SetXY(29, 205); //define la altura date fecha.
        //}
      //$qr_description = $fe_fin;
      //$pdf->writeHTML($qr_description, true, false, false, false, '');

      // set bacground image
      $vanasSignature = "../../images/diploma-vanas-signature.png";

      // Validation large for full name
      if($fullname_string_count >= 23){
        $pdf->Image($vanasSignature, 35, 205, 35, 35, '', '', '', false, 300, '', false, false, 0);
      }else{
        $pdf->Image($vanasSignature, 35, 208, 35, 35, '', '', '', false, 300, '', false, false, 0);
      }

      // set the starting point for the page content
      $pdf->setPageMark();

    }
    if($nb_program_string_count > 31){
      // Validate large for full name
      //if($fullname_string_count >= 23){
      //  $pdf->SetFont('dejavusans', '', 15);
      //  $pdf->SetXY(29, 205);
      //}else{
      //  $pdf->SetFont('dejavusans', '', 15);


            //if($fullname_string_count <= 23) //19
            //{
            //$pdf->SetXY(29, 205); //define la altura date fecha. no existe algo defina exactamente la logica
            //}
            //if($fullname_string_count > 23) //19
            //{
            //	$pdf->SetXY(29, 205); //define la altura date fecha.
            //}


      //  }

      //$qr_description = $fe_fin;
      //$pdf->writeHTML($qr_description, true, false, false, false, '');

      // set bacground image
      $vanasSignature = "../../images/diploma-vanas-signature.png";

      // Validation large for full name
      if($fullname_string_count >= 23){
        $pdf->Image($vanasSignature, 35, 216, 35, 35, '', '', '', false, 300, '', false, false, 0);
      }else{
        $pdf->Image($vanasSignature, 35, 218, 35, 35, '', '', '', false, 300, '', false, false, 0);
      }

      // set the starting point for the page content
      $pdf->setPageMark();
    }

    #Identificamos si es Diploma , Certificate
    $Query="SELECT DISTINCT  ds_credential
          FROM k_programa_costos b WHERE b.fl_programa=$fl_programa ";
    $row=RecuperaValor($Query);
    $ds_credential=$row[0];



    //Output file name
    $nombre_archivo = $ds_nombres.' '.$ds_apaterno.' VANAS '.$ds_credential.' '.$fe_graduate.'.pdf';

      // replace special characters in the full name
    $nombre_archivo = str_replace("aacute","a",$nombre_archivo);
    $nombre_archivo = str_replace("eacute","e",$nombre_archivo);
    $nombre_archivo = str_replace("iacute","i",$nombre_archivo);
    $nombre_archivo = str_replace("oacute","o",$nombre_archivo);
    $nombre_archivo = str_replace("uacute","u",$nombre_archivo);

    // uml
    $nombre_archivo = str_replace("auml","a",$nombre_archivo);
    $nombre_archivo = str_replace("euml","e",$nombre_archivo);
    $nombre_archivo = str_replace("iuml","i",$nombre_archivo);
    $nombre_archivo = str_replace("ouml","o",$nombre_archivo);
    $nombre_archivo = str_replace("uuml","u",$nombre_archivo);

    // uml
    $nombre_archivo = str_replace("atilde","a",$nombre_archivo);
    $nombre_archivo = str_replace("etilde","e",$nombre_archivo);
    $nombre_archivo = str_replace("itilde","i",$nombre_archivo);
    $nombre_archivo = str_replace("otilde","o",$nombre_archivo);
    $nombre_archivo = str_replace("utilde","u",$nombre_archivo);


    // replace special characters in the full name
    $nombre_archivo = str_replace("Aacute","A",$nombre_archivo);
    $nombre_archivo = str_replace("Eacute","E",$nombre_archivo);
    $nombre_archivo = str_replace("Iacute","I",$nombre_archivo);
    $nombre_archivo = str_replace("Oacute","O",$nombre_archivo);
    $nombre_archivo = str_replace("Uacute","U",$nombre_archivo);

    // uml
    $nombre_archivo = str_replace("Auml","A",$nombre_archivo);
    $nombre_archivo = str_replace("Euml","E",$nombre_archivo);
    $nombre_archivo = str_replace("Iuml","I",$nombre_archivo);
    $nombre_archivo = str_replace("Ouml","O",$nombre_archivo);
    $nombre_archivo = str_replace("Uuml","U",$nombre_archivo);

    // tilde
    $nombre_archivo = str_replace("Atilde","A",$nombre_archivo);
    $nombre_archivo = str_replace("Etilde","E",$nombre_archivo);
    $nombre_archivo = str_replace("Itilde","I",$nombre_archivo);
    $nombre_archivo = str_replace("Otilde","O",$nombre_archivo);
    $nombre_archivo = str_replace("Utilde","U",$nombre_archivo);
    $nombre_archivo = str_replace("Ntilde","N",$nombre_archivo);

    //Close and output PDF document
    $pdf->Output($nombre_archivo, 'D');

  }

  /* +-----------------------------------------------------------------------------------------------+ */
  /* +-------------------------------- Transcripts PDF ----------------------------------------------+ */
  /* +-----------------------------------------------------------------------------------------------+ */

  if($fl_template == 195){
      /**
       * Set header, body and footer
       */
      $ds_header = genera_documento($fl_sesion, 1, $fl_template);
      $ds_cuerpo = genera_documento($fl_sesion, 2, $fl_template);
      $ds_footer = genera_documento($fl_sesion, 3, $fl_template);

      /**
       * Recupera datos del aplicante
       */
      $Query="SELECT ds_login, ds_nombres, ds_apaterno, ds_amaterno, ".ConsultaFechaBD('fe_nacimiento', FMT_CAPTURA)." fe_nacimiento, cl_sesion, no_promedio_t
                FROM c_usuario a, c_perfil b, c_alumno c
                WHERE a.fl_perfil=b.fl_perfil
                AND a.fl_usuario=c.fl_alumno
                AND fl_usuario=$fl_alumno";

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


      /**
       * Recupera datos del aplicante: forma 1
       */
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

      //Set data into variables
      $ds_fname = str_texto($row[0]);
      $ds_mname = str_texto($row[1]);
      $ds_lname = str_texto($row[2]);
      $fe_birth = $row[3];
      $nb_programa = $row[4];
      $nb_periodo_temp = explode("-", $row[5]);
      $nb_periodo = substr(ObtenNombreMes($nb_periodo_temp[1]),0,3).' '.$nb_periodo_temp[0].', '.$nb_periodo_temp[2];;
      $fl_programa = $row[6];
      $fl_periodo = $row[7];


      /**
       *  Recupera el fl_alumno
       */
      $Query  = "SELECT fl_usuario ";
      $Query .= "FROM c_usuario ";
      $Query .= "WHERE cl_sesion='$cl_sesion'";

      $row2 = RecuperaValor($Query);

      // Set fl_usuario
      $fl_usuario = $row2[0];

      /*
       * Recupera el program start date
       */
      $Query="SELECT nb_periodo
      FROM c_programa a, k_term b, c_periodo c, k_alumno_term d
      WHERE a.fl_programa=b.fl_programa
      AND b.fl_periodo=c.fl_periodo
      AND b.fl_term=d.fl_term AND d.fl_alumno='$fl_usuario'
      AND no_grado=1 ";

      $row2 = RecuperaValor($Query);

      // Set nb_periodo data
      $nb_periodo = $row2[0];

      /**
       * Recupera datos de Official Transcript
       */
      $Query="SELECT ".ConsultaFechaBD('fe_fin', FMT_FECHA)." fe_fin, ". ConsultaFechaBD('fe_completado', FMT_FECHA)." fe_completado, ".ConsultaFechaBD('fe_emision', FMT_FECHA)." fe_emision, ".ConsultaFechaBD('fe_graduacion', FMT_FECHA)." fe_graduacion
      FROM k_pctia
      WHERE fl_alumno = $fl_alumno
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

      // Header Variables Replacement
      $ds_header = str_replace("#pg_comdate#",$fe_completado,$ds_header);
      $ds_header = str_replace("#pg_Issdate#",$fe_emision,$ds_header);
      $ds_header = str_replace("#st_num#",$ds_login,$ds_header);



      /*
      * QR Code
      */
      // set link
      $link_qr='campus.vanas.ca/StudentAccreditation.php?clave='.$fl_sesion.'&type=2&data='.$fl_alumno;

      // set style for barcode
      $style = array(
        'border' => 0,
        'padding' => 0,
        'fgcolor' => array(0,0,0),
        'bgcolor' => false, //array(255,255,255)
        'module_width' => 1, // width of a single module in points
        'module_height' => 1 // height of a single module in points
      );


    /* +-----------------------------------------------------------------------------------------------+ */
    /* +-------------------------------------- Header and footer --------------------------------------+ */
    /* +-----------------------------------------------------------------------------------------------+ */
    class MYPDF extends TCPDF {

      /** Page header construction function */
      public function Header() {

        global $ds_header;
        global $link_qr;
        global $style;

        // get the current page break margin
        $bMargin = $this->getBreakMargin();
        // get current auto-page-break mode
        $auto_page_break = $this->AutoPageBreak;
        // disable auto-page-break
        $this->SetAutoPageBreak(false, 0);
        // set bacground image
        $img_file = "../../images/Protected_paper_vanas_official_transcript.jpg";
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

        $this->SetFont('dejavusans', '', 6);
        $this->SetXY(120, 29);

        $qr_description = 'Verify Academic Credential';
        $this->writeHTML($qr_description, true, false, false, false, '');

        // QRCODE,L : QR-CODE Low error correction
       $this->write2DBarcode(''.$link_qr.'', 'QRCODE,L', 124, 8, 20, 20, $style, 'N');
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

       /* +-----------------------------------------------------------------------------------------------+ */
      /* +-------------------------------------- build html contents ------------------------------------+ */
      /* +-----------------------------------------------------------------------------------------------+ */

      /** @var $Query
       * Retrieve the group, term and teacher
       */
      $Query="SELECT a.fl_grupo, b.fl_term, c.ds_nombres, c.ds_apaterno, b.nb_grupo
      FROM k_alumno_grupo a
      LEFT JOIN (c_grupo b LEFT JOIN c_usuario c ON b.fl_maestro = c.fl_usuario) ON a.fl_grupo = b.fl_grupo
      WHERE fl_alumno = $fl_alumno";

      $row1 = RecuperaValor($Query);

      // Set group data
      $fl_grupo = $row1[0];
      $fl_term = $row1[1];
      $nb_maestro = $row1[2].'&nbsp;'.$row1[3];
      $ds_grupo = $row1[4];

      # Buscamos todos los terms que haya cursado
      $QueryT="SELECT a.fl_term, b.no_grado, a.no_promedio
      FROM k_alumno_term a, k_term b LEFT JOIN c_leccion lec ON(lec.fl_programa=b.fl_programa AND lec.no_grado=b.no_grado)
      LEFT JOIN c_programa pro ON(pro.fl_programa=b.fl_programa AND pro.fl_programa=lec.fl_programa), c_periodo c
      WHERE a.fl_term=b.fl_term AND b.fl_periodo=c.fl_periodo AND a.fl_alumno=$fl_alumno
      GROUP BY a.fl_term ORDER BY c.fe_inicio, b.no_grado";

      $rs = EjecutaQuery($QueryT);

      # Empezamos a mostrar los terms que ha cursado el estudiante
      $htmlcontent = '<table border="0" cellpadding="0" cellspacing="0" width="100%">';
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
                    AND b.fl_alumno = $fl_alumno
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
                      $Query .= "AND a.fl_alumno=$fl_alumno ";
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
              AND a.fl_usuario = $fl_alumno";

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
                <td style="width:8%; height:15px; background-color:'.$bgcolor.'; color:#000000; font-family:Arial; font-size:12px; font-weight:normal; text-align:left;">
                '.$fe_publicacion.'</td>
                <td style="width:7%; height:15px; background-color:'.$bgcolor.'; color:#000000; font-family:Arial; font-size:12px; font-weight:normal; text-align:center;">
                '.$no_semana.'</td>
                <td style="width:49%; height:15px; background-color:'.$bgcolor.'; color:#000000; font-family:Arial; font-size:12px; font-weight:normal; text-align:left;">
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
              AND b.fl_alumno = $fl_alumno
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
                WHERE fl_usuario=$fl_alumno
                AND a.fl_clase_grupo=$fl_clase_grupo ";

                  $to = RecuperaValor($Query);

                  $nb_ausencia = $to['nb_estatus']??NULL;
                  $nb_ausent = $to[1]??NULL;

                  if (empty($nb_ausencia))
                      $nb_ausencia = $nb_ausent;

                  $htmlcontent .= '<tr>
                        <td style="width:8%; height:15px; background-color:' . $bgcolor . '; color:#000000; font-family:Arial; font-size:12px; font-weight:normal; text-align:left;">
                        ' . $fe_publicacion . '</td>
                        <td style="width:7%; height:15px; background-color:' . $bgcolor . '; color:#000000; font-family:Arial; font-size:12px; font-weight:normal; text-align:center;">
                        ' . $no_semana . '</td>
                        <td style="width:49%; height:15px; background-color:' . $bgcolor . '; color:#000000; font-family:Arial; font-size:12px; font-weight:normal; text-align:left;">
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
                            JOIN k_live_session_asistencia_cg D ON(A.fl_usuario=D.fl_usuario)
                            JOIN k_alumno_term E ON(A.fl_usuario = E.fl_alumno)
                        WHERE A.fl_usuario = $fl_alumno
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
                        <td style="width:8%; height:15px; background-color:' . $bgcolor . '; color:#000000; font-family:Arial; font-size:12px; font-weight:normal; text-align:left;">
                        ' . $fe_publicacion . '</td>
                        <td style="width:7%; height:15px; background-color:' . $bgcolor . '; color:#000000; font-family:Arial; font-size:12px; font-weight:normal; text-align:center;">
                        ' . $no_semana . '</td>
                        <td style="width:49%; height:15px; background-color:' . $bgcolor . '; color:#000000; font-family:Arial; font-size:12px; font-weight:normal; text-align:left;">
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
    </table>';
      /* +-----------------------------------------------------------------------------------------------+ */
      /* +-------------------------------------- Build PDF ----------------------------------------------+ */
      /* +-----------------------------------------------------------------------------------------------+ */
        $pdf = new MYPDF('P', 'mm', 'LETTER', true, 'UTF-8', false);
        #$pdf = new TCPDF('P', 'mm', 'LETTER', true);

        // set default header data
        $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);

        // Set protection
        $pdf->SetProtection(array('modify'), '', null, 0, null);

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

        $nombre_archivo = $ds_nombres.' '.$ds_apaterno.' VANAS Transcripts '.$fe_graduate.'.pdf';

            // replace special characters in the full name
        $nombre_archivo = str_replace("aacute","a",$nombre_archivo);
        $nombre_archivo = str_replace("eacute","e",$nombre_archivo);
        $nombre_archivo = str_replace("iacute","i",$nombre_archivo);
        $nombre_archivo = str_replace("oacute","o",$nombre_archivo);
        $nombre_archivo = str_replace("uacute","u",$nombre_archivo);

        // uml
        $nombre_archivo = str_replace("auml","a",$nombre_archivo);
        $nombre_archivo = str_replace("euml","e",$nombre_archivo);
        $nombre_archivo = str_replace("iuml","i",$nombre_archivo);
        $nombre_archivo = str_replace("ouml","o",$nombre_archivo);
        $nombre_archivo = str_replace("uuml","u",$nombre_archivo);

        // uml
        $nombre_archivo = str_replace("atilde","a",$nombre_archivo);
        $nombre_archivo = str_replace("etilde","e",$nombre_archivo);
        $nombre_archivo = str_replace("itilde","i",$nombre_archivo);
        $nombre_archivo = str_replace("otilde","o",$nombre_archivo);
        $nombre_archivo = str_replace("utilde","u",$nombre_archivo);
        $nombre_archivo = str_replace("ntilde","n",$nombre_archivo);


        // replace special characters in the full name
        $nombre_archivo = str_replace("Aacute","A",$nombre_archivo);
        $nombre_archivo = str_replace("Eacute","E",$nombre_archivo);
        $nombre_archivo = str_replace("Iacute","I",$nombre_archivo);
        $nombre_archivo = str_replace("Oacute","O",$nombre_archivo);
        $nombre_archivo = str_replace("Uacute","U",$nombre_archivo);

        // uml
        $nombre_archivo = str_replace("Auml","A",$nombre_archivo);
        $nombre_archivo = str_replace("Euml","E",$nombre_archivo);
        $nombre_archivo = str_replace("Iuml","I",$nombre_archivo);
        $nombre_archivo = str_replace("Ouml","O",$nombre_archivo);
        $nombre_archivo = str_replace("Uuml","U",$nombre_archivo);

        // tilde
        $nombre_archivo = str_replace("Atilde","A",$nombre_archivo);
        $nombre_archivo = str_replace("Etilde","E",$nombre_archivo);
        $nombre_archivo = str_replace("Itilde","I",$nombre_archivo);
        $nombre_archivo = str_replace("Otilde","O",$nombre_archivo);
        $nombre_archivo = str_replace("Utilde","U",$nombre_archivo);
        $nombre_archivo = str_replace("Ntilde","N",$nombre_archivo);



        //Close and output PDF document
        $pdf->Output($nombre_archivo, 'D');

  }


  /* +-----------------------------------------------------------------------------------------------+ */
  /* +-------------------------------- Other PDF's --------------------------------------------------+ */
  /* +-----------------------------------------------------------------------------------------------+ */
  if($fl_template != 194 && $fl_template != 195){

    # Obtenemos los datos del template seleccinado
    if(empty($ds_cuerpo))
      $ds_cuerpo = genera_documento($fl_sesion, 2, $fl_template);

    // create new PDF document
    $pdf = new TCPDF('P', 'mm', 'LETTER', true);

    //do not show header or footer
    $pdf->SetPrintHeader(false);
    $pdf->SetPrintFooter(false);

    // set default monospaced font
    $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

    //set margins
    $pdf->SetMargins(1.0, PDF_MARGIN_TOP, 1.0);
    $pdf->SetMargins(1.0, 1000, 1.0);
    $pdf->SetHeaderMargin(5);
    $pdf->SetFooterMargin(50);

    //set auto page breaks
    $pdf->SetAutoPageBreak(TRUE, 5);

    //set image scale factor
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

    //set some language-dependent strings
    $pdf->setLanguageArray($l);

    // ---------------------------------------------------------

    // set font
    $pdf->SetFont('dejavusans', '', 10);

    # Datos del mensaje
    $htmlcontent = '
    <table>
      <tbody>
        <tr>
          <td>
            '.$ds_header.'
          </td>
        </tr>
        <tr>
          <td>
            '.$ds_cuerpo.'
          </td>
        </tr>
      </tbody>
    </table>';

    class ConPies extends TCPDF {
      //header
      function Header(){
        $fl_alumno_template = RecibeParametroNumerico('fl_alumno_template', True);
        $fl_sesion = RecibeParametroNumerico('fl_sesion', True);
        $row = RecuperaValor("SELECT ds_header, fl_template FROM k_alumno_template WHERE fl_alumno_template=$fl_alumno_template");
        $header = str_ascii(str_texto($row[0]));
        $fl_template = $row[1];
        if(empty($header))
          $header = genera_documento($fl_sesion, 1, $fl_template);
        $this->writeHTML($header, true, 0, true, 0);
      }
      //footer
      function Footer(){
        $fl_alumno_template = RecibeParametroNumerico('fl_alumno_template', True);
        $fl_sesion = RecibeParametroNumerico('fl_sesion', True);
        $this->SetY(-20);
        $row = RecuperaValor("SELECT ds_footer, fl_template FROM k_alumno_template WHERE fl_alumno_template=$fl_alumno_template");
        $footer = str_ascii(str_texto($row[0]));
        $fl_template = $row[1];
        if(empty($footer))
          $footer = genera_documento($fl_sesion, 3, $fl_template);
        $this->writeHTML($footer, true, 0, true, 0);
      }
    }

    // creamos un nuevo objeto usando la clase extendida ConPies
    $pdf = new ConPies();
    $pdf->SetFont('times','',10);

    // add a page
    $pdf->AddPage("P");

    // output the HTML content
    $pdf->writeHTMLCell(180, 100, 10,30,utf8_decode($ds_cuerpo), 0, 0, false, true,'',true);

    # Nombre del archivo
    $nombre_archivo = $ds_nombre.' '.$nb_template.' '.$Date.'.pdf';
    //Close and output PDF document
    $pdf->Output($nombre_archivo, 'D');

  }
