<?php
  # Libreria de funciones
  require("../../common/lib/cam_general.inc.php");
  require_once('../../../AD3M2SRC4/lib/tcpdf/config/lang/eng.php');
  require_once('../../../AD3M2SRC4/lib/tcpdf/tcpdf.php');
  

  # Sobreescribe metodos para funciones de Header y Footer
  class MYPDF extends TCPDF {    
    public function Header( ) {
    }
    public function Footer() {
    }
  }
  
  # Recibimos parametros
  $anio = RecibeParametroNumerico('anio', True);
  $origen = RecibeParametroHTML('origen',False,True);
  if($origen=="history")
    $fl_alumno = ValidaSesion(False);
  else
    $fl_alumno = RecibeParametroNumerico('fl_alumno',True);
  $fl_term = RecibeParametroNumerico('fl_term',True);
  $row= RecuperaValor("SELECT b.nb_programa, a.fl_programa FROM k_term a, c_programa b WHERE a.fl_programa=b.fl_programa AND fl_term=$fl_term");
  $nb_programa= $row[0];
  $fl_programa= $row[1];
  $num_meses_anio = (RecibeParametroNumerico('num_meses_anio',True)) / 1;
  $monto = RecibeParametroHTML('monto',False,True);
  
  # Obtenemos el full o part time del programal
  $Query = "SELECT fg_fulltime FROM c_programa WHERE fl_programa=$fl_programa ";
  $row = RecuperaValor($Query);
  if($row[0]==1)
    $full_time=$num_meses_anio;
  else
    $part_time=$num_meses_anio;
    
  # Obtemos la informacion del alumno 
  $Query  = "SELECT a.cl_sesion, CONCAT(a.ds_nombres,' ',a.ds_apaterno,'',a.ds_amaterno) ds_nombres, ";
  $Query .= "Concat(b.ds_add_number,' ', b.ds_add_street) ds_num_street, ";
  $Query .= "concat(b.ds_add_city,' ', b.ds_add_state,' ', b.ds_add_zip) ds_city_state_zip, a.ds_login ";
  $Query .= "FROM c_usuario a, k_ses_app_frm_1 b, k_programa_costos c ";
  $Query .= "WHERE a.cl_sesion=b.cl_sesion AND b.fl_programa=c.fl_programa AND fl_usuario = $fl_alumno ";
  $row = RecuperaValor($Query);
  $cl_sesion = $row[0];
  $ds_nombres = str_texto($row[1]);
  $ds_num_street = str_texto($row[2]);
  $ds_city_state_zip = $row[3];
  $ds_login = $row[4];
  
  # Recupera el program start date 
  $Query  = "SELECT ".ConsultaFechaBD('c.fe_inicio',FMT_FECHA)." ";
  $Query .= "FROM k_term b, c_periodo c, k_alumno_term d ";
  $Query .= "WHERE b.fl_periodo=c.fl_periodo ";
  $Query .= "AND b.fl_term=d.fl_term AND d.fl_alumno='$fl_alumno' ";
  $Query .= "AND no_grado=1 ";
  $row2 = RecuperaValor($Query);
  $fe_inicio = $row2[0];
  $fe_inicio = DATE_FORMAT(date_create($fe_inicio),'Y-m-d');
  $mes_ini_curso = DATE_FORMAT(date_create($fe_inicio),'m');
  $anio_ini_curso = DATE_FORMAT(date_create($fe_inicio),'Y');
  
  # Obtenemos los meses que cubren lo pagos
  # Obtenemos su nombre para mostrarlos en la tabla
  $anios_direrencia = $anio - $anio_ini_curso;
  switch($anios_direrencia) {
    case 0:
      $mes_ini = $mes_ini_curso;
      $mes_fin = $mes_ini + $num_meses_anio - 1;
      if($mes_fin < 10)
        $mes_fin = '0'.$mes_fin;
      break;
    case 1:
      $fe_final = strtotime('+ '.((12 - $mes_ini_curso + 1) + $num_meses_anio - 1).' month', strtotime($fe_inicio));
      $mes_fin = date('m', $fe_final);
      $mes_ini = '01';
      break;
    case 2:
      $fe_final = strtotime('+ '.((12 - $mes_ini_curso + 1) + 12 + $num_meses_anio - 1).' month', strtotime($fe_inicio));
      $mes_fin = date('m', $fe_final);
      $mes_ini = '01';
      break;
  }

  # Genera un documento PDF //P//mm//A4
  $pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
  $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);
  $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
  $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
  $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
  $pdf->SetMargins(5, 3, 5);
  $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
  $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
  $pdf->SetAutoPageBreak(TRUE,5);
  $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO); 
  $pdf->AddPage("P");
  
  # Codigo HTML
  $style= '
  <style>
    .encabezado{padding-top:3.5px; }
     .ul{padding-left: 0pt; color: black; font-family:Helvetica; font-style: normal; font-weight: normal; text-decoration: none; font-size: 5.8pt;}
     .T1td1{
    font-family:Helvetica;
    font-weight:bold;
    font-size:5pt;
    border-top:1px #cccccc solid;
    border-left:1px #cccccc  solid;
    border-bottom:1px #cccccc  solid;
    border-right:0.1px #cccccc solid;
    }
    .T1td2{
    font-family:Helvetica;
    font-size:5pt;
    border-top:1px #cccccc solid;
    border-left:0.1px #cccccc  solid;
    border-bottom:1px #cccccc  solid;
    border-right:1px #cccccc solid;
    }
    .T2{
    font-family:Helvetica;
    font-weight:bold;
    font-size:6.5pt;
    border:0.1px #cccccc solid;
    
    }
    .Titulos{
    font-family:Helvetica;
    font-size:4.5pt;
    text-align:center;
    border-left:0.1px #cccccc solid;
    border-bottom:0.1px #cccccc solid;
    border-right:0.1px #cccccc solid;
    }
    .TitulosT2{
    font-family:Helvetica;
    font-size:5pt;
    margin-top:10px;
    border-left:0.1px #cccccc solid;
    border-bottom:0.1px #cccccc solid;
    border-right:0.1px #cccccc solid;
    }
    .Titulosleft{
    font-family:Helvetica;
    font-size:4.5pt;
    text-align:center;
    border-top:0.1px #cccccc  solid;
    border-right:0.1px #cccccc  solid;
    border-bottom:0.1px #cccccc  solid;
    border-left:0.9px #cccccc  solid;
    }
    .2left{
    font-family:Helvetica;
    font-size:15px;
    text-align:center;
    border-top:0.1px #cccccc  solid;
    border-right:0.1px #cccccc  solid;
    border-bottom:0.1px #cccccc  solid;
    }
    .tablaright{
    font-family:Helvetica;
    font-size:4.5pt;
    text-align:center;
    border-left:0.1px #cccccc  solid;
    border-top:0.1px #cccccc  solid;
    border-right:0.1px #cccccc  solid;
    border-bottom:0.1px #cccccc  solid;
    }
    .footer{
    font-family:Helvetica;
    font-size:4.8pt;
    height:1px;
    }
    .footer2{
    font-family:Helvetica;
    font-size:4.8pt;
    height:1px;
    padding-top:3px;
    }
    .datos{
    font-family:Helvetica;
    font-size:5pt;
    }
    .totals{
    font-family:Helvetica;font-size:3.5 font-weight:bold;pt;text-align:center; border-left:0.1px #cccccc solid;
    border-bottom:0.1px #cccccc solid;    border-right:0.1px #cccccc solid;" text-align:right;
    }
     </style>';
    $lu ='
    <table class="ul">
      <tr>
        <td width="335" style="border-top:1px #cccccc  solid;">
          <ul>
            <li>Issue this certificate to a student who was enrolled during the calendar year in a qualifying educational program or a specified educational program at a post-secondary institution, such as a college or university, or at an institution certified by Employment and Social Development Canada (ESDC) (formerly Human Resources and Skills Development Canada (HRSDC)).
          </li>
          <li>Tuition fees paid in respect of the calendar year to any one institution have to be more than $100. Fees paid to a post-secondary institution have to be for courses taken at the post-secondary level. Fees paid to an institution certified by ESDC have to be for courses taken to get or improve skills in an occupation, and the student has to be 16 years of age or older before the end of the year.
          </li>
          <li><strong>Do not enter the cost of textbooks on this form.</strong> Students calculate the education <strong>and</strong> textbook amounts <strong>based on the number of months</strong> indicated in Box B or C below.
          </li>
          </ul>
        </td>
        <td width="365"  style="border-top:1px #cccccc  solid;">
          <ul>
          <li>D&eacute;livrez ce certificat &agrave; un &eacute;tudiant qui &eacute;tait inscrit, au cours de l&#39;ann&eacute;e civile, &agrave; un programme de formation admissible ou &agrave; un programme de formation d&eacute;termin&eacute; dans un &eacute;tablissement postsecondaire, comme un coll&egrave;ge ou une universit&eacute;, ou dans un &eacute;tablissement reconnu par Emploi et D&eacute;veloppement social Canada (EDSC) (anciennement Ressources humaines et D&eacute;veloppement des comp&eacute;tences Canada (RHDCC)).
          </li>
          <li>Les frais de scolarit&eacute; pay&eacute;s &agrave; un &eacute;tablissement quelconque pour une ann&eacute;e civile doivent d&eacute;passer &nbsp;&nbsp;&nbsp;&nbsp; 100 $. Les frais pay&eacute;s &agrave; un &eacute;tablissement postsecondaire doivent viser des cours de niveau postsecondaire. Les frais pay&eacute;s &agrave; un &eacute;tablissement reconnu par EDSC doivent viser des cours suivis en vue d&#39;acqu&eacute;rir ou d&#39;am&eacute;liorer des comp&eacute;tences professionnelles, et l&#39;&eacute;tudiant doit avoir 16 ans ou plus avant la fin de l&#39;ann&eacute;e.
          </li>
          <li><strong>N&#39;inscrivez pas le co&ucirc;t des manuels sur ce formulaire.</strong> L&#39;&eacute;tudiant calcule les montants relatifs aux &eacute;tudes <strong>et</strong> pour <strong>manuels d&#39;apr&egrave;s le nombre de mois</strong> indiqu&eacute; dans les cases B ou C ci-dessous.
          </li>
          </ul>
        </td>
      </tr>
    </table>';
    
    
    $tabla = '
    <table border="0">
    <tbody>
    <tr>
    <td colspan="2" class="T1td1" height="15px" width="553">&nbsp;&nbsp;&nbsp;Name of program or course &ndash; Nom du programme ou du cours<div class="datos">&nbsp;&nbsp;&nbsp;'.$nb_programa.'</div></td>
    <td class="T1td2" width="143x">&nbsp;&nbsp;&nbsp;Student number &ndash; Num&eacute;ro d&#39;&eacute;tudiant
    <div class="datos">&nbsp;&nbsp;&nbsp;'.$ds_login.'</div></td>
    </tr>
    <tr>
    <td width="410" align="left" style="font-size:14px;">&nbsp;&nbsp;&nbsp;Name and address of student - Nom et adresse de l&#39;&eacute;tudiant
      <br /><div class="datos">&nbsp;&nbsp;'.$ds_nombres.'<br />&nbsp;&nbsp;'.$ds_num_street.'<br />&nbsp;&nbsp;'.$ds_city_state_zip.'</div></td>
    <td  colspan="2" align="left" width="286">
    <table border="0" cellpadding="2">
    <tbody>
    <tr>
    <td height="5px" width="135" colspan="4"  class="TitulosT2" align="center">&nbsp;Session periods, part-time and full-time <br>P&eacute;riodes d&#39;&eacute;tudes &agrave; temps partiel <br>et &agrave; temps plein</td>
    <td height="5px" width="82" rowspan="3" class="2left" align="center"><b>A</b><br>Eligible tuition fees,<br>part-time and full-time<br> sessions<br />Frais de scolarit&eacute; <br>admissibles pour &eacute;tudes &agrave; <br>temps partiel et &agrave; temps <br>plein</td>
    <td height="5px" width="60" colspan="2" class="tablaright" align="center" height="5px">Number of months for: Nombre de mois &agrave; :</td>
    </tr>
    <tr>
    <td height="15px" class="Titulos" colspan="2">&nbsp;From &ndash; De</td>
    <!--<td class="Titulos">1</td>-->
    <td class="Titulosleft" colspan="2">To &ndash; &Agrave;</td>
    <!--<td class="Titulos">1</td>-->
    <!--<td height="5px" class="Titulos">1</td>-->
    <td height="5px" class="Titulos" rowspan="2"><b>B Part-time Temps partiel</b></td>
    <td height="5px" class="Titulos" rowspan="2"><b>C Full-time Temps plein</b></td>
    </tr>
    <tr>
    <td height="10px" class="Titulos">&nbsp;Y &ndash; A</td>
    <td height="10px" class="Titulos">&nbsp;M</td>
    <td height="10px" class="Titulosleft">&nbsp;Y &ndash; A</td>
    <td height="10px" class="Titulos">&nbsp;M</td>
    <!--<td height="5px" class="Titulos">1</td>-->
    <!--<td height="5px" class="Titulos">1</td>
    <td height="5px" class="Titulos">1</td>-->
    </tr>
    <tr>
    <td height="12px" class="Titulos">'.$anio.'</td>
    <td class="Titulos">'.$mes_ini.'</td>
    <td class="Titulosleft">'.$anio.'</td>
    <td class="Titulos">'.$mes_fin.'</td>
    <td class="Titulos" align="right">'.$monto.'</td>
    <td class="Titulos">'.$part_time.'</td>
    <td class="Titulos">'.$full_time.'</td>
    </tr>
    <tr>
    <td height="12px" class="Titulos"></td>
    <td class="Titulos"></td>
    <td class="Titulosleft"></td>
    <td class="Titulos"></td>
    <td class="Titulos" align="right"></td>
    <td class="Titulos"></td>
    <td class="Titulos"></td>
    </tr>
    <tr>
    <td height="12px" class="Titulos"></td>
    <td class="Titulos"></td>
    <td class="Titulosleft"></td>
    <td class="Titulos"></td>
    <td class="Titulos" align="right"></td>
    <td class="Titulos"></td>
    <td class="Titulos"></td>
    </tr>
    <tr>
    <td height="12px" class="Titulos"></td>
    <td class="Titulos"></td>
    <td class="Titulosleft"></td>
    <td class="Titulos"></td>
    <td class="Titulos" align="right"></td>
    <td class="Titulos"></td>
    <td class="Titulos"></td>
    </tr>
    <tr>
    <td colspan="4" height="10px" class="totals"><b>Totals<br>Totaux</b></td>
    <td class="Titulos" align="right">'.$monto.'</td>
    <td class="Titulos">'.$part_time.'</td>
    <td class="Titulos">'.$full_time.'</td>
    </tr>
    <tr>
    <td colspan="7" height="10px" class="footer">Name and address of educational institution &ndash; Nom et adresse de l&#39;&eacute;tablissement d&#39;enseignement<div style="font-family:Helvetica; font-size:16.5px;">'.ObtenEtiqueta(52).','.ObtenEtiqueta(521).','.ObtenEtiqueta(518).'-'.ObtenEtiqueta(519).'- www.vanas.ca</div>
    </td>
    </tr>
    </tbody>
    </table>
    </td>
    </tr>
    <tr>
      <td colspan="7" class="footer2" style=" border-bottom-width:1.3px; border-bottom-style:dashed; border-bottom-color:#C3C3C3;" height="20px"><b>Information for students:</b> See the back of slip 1. If you want to transfer all or part of your tuition, education, and textbook amounts, complete the back of slip 2.<div><b>Renseignements pour les &eacute;tudiants :</b> Lisez le verso du feuillet 1. Si vous d&eacute;sirez transf&eacute;rer une partie ou la totalit&eacute; de vos frais de scolarit&eacute; et de vos montants relatifs aux &eacute;tudes et pour manuels, remplissez le verso du feuillet 2.</div>
      </td>
    </tr>
    </tbody>
    </table>';

  # Primera Hoja
  $datos1 = array(1=>'For student', 'For designated individual', 'For educational institution');
  $datos2 = array(1=>'Pour l&#39;&eacute;tudiant', 'Pour la personne design&eacute;e', 'Pour l&#39;&eacute;tablissement d&#39;enseignement');
  $tot = count($datos1);
  $htmlcontent = '';
  for($i=1;$i<=$tot;$i++){
    $htmlcontent1 = '
    <table class="encabezado" >
      <tbody>
        <tr>
          <td width="220">
            <img src="../../../images/Vanas_tax.jpg" width="175" height="18.5"/>
          </td>
          <td width="240" style="font-family:Helvetica; font-weight:bold; font-size:22px; text-align:center;">Tuition, education, and textbook amounts certificate <br>Certificat pour frais de scolarit&eacute;, montant relatif aux &eacute;tudes <br/>et montant pour manuels</td>
          <td width="250" style="font-family:Helvetica; text-align:center; font-size:16px;">
            <table border="0">
              <tbody>
                <tr>
                  <td style="text-align: center; padding-top:10px; font-family:Helvetica; font-size:27px; font-weight:bold;" colspan="2">Protected B / Prot&eacute;g&eacute; B</td>
                  <td style="text-align: right; font-family:Helvetica; font-size:23px;" colspan="2">T2202A (14)&nbsp;&nbsp;&nbsp;&nbsp;</td>
                </tr>
                <tr>
                  <td style="text-align: center; font-family:Helvetica; font-size:23px;" colspan="2">when completed / une fois rempli</td>
                  <td width="100" style="text-align: right; font-family:Helvetica; font-size:19px;"> '.$datos1[$i].' </td>
                  <td style="text-align: left; font-family:Helvetica; font-size:45px; font-weight:bold;" rowspan="2">&nbsp;'.$i.'</td>
                </tr>
                <tr>
                  <td width="220" style="text-align: right; font-family:Helvetica; font-size:19px;" colspan="3"> '.$datos2[$i].' </td>
                </tr>
              </tbody>
            </table>
          </td>
        </tr>
      </tbody>
    </table>'.$lu.$tabla;
    $htmlcontent = $htmlcontent.$htmlcontent1;
  }
  
  
  # hoja 2 del documento
  $htmlcontent2 = '<br />
  <style>
    .hoja2{color: black; font-family:Helvetica; font-style: normal; font-weight: normal; font-size: 5.8pt;}
   .hoja2right{color: black; font-family:Helvetica; font-style: normal; font-weight: normal; font-size: 5.8pt;}
  </style>
  <table border="0" cellpadding="3">
    <tr>
      <td width="330"  class="hoja2">
        <ul>
          <li>Complete <b>Schedule 11</b>, Tuition, education, and textbook amounts, to calculate the <b>federal amount</b> you can claim on line 323 of Schedule 1, Federal tax; the maximum amount you can transfer to a designated individual; and the amount, if any, you can carry forward to a future year.
          </li>
          <li>Also complete provincial or territorial <b>Schedule (S11)</b>, if you resided in a province or territory other than Quebec on December 31, to calculate the <b>provincial</b> or <b>territorial amount</b> you can claim on line 5856 of Form 428; the maximum amount you can transfer to a designated individual; and the amount, if any, you can carry forward to a future year.
          </li>
          <li>You can claim a <b>full-time</b> education amount if you were enrolled in a <b>qualifying<br />educational program</b> as a full-time student. Such a program lasts at least three consecutive weeks and requires a minimum of 10 hours of course instruction or work each week in the program (excluding study time). For more information on this and on the <b>textbook amount,</b> see Pamphlet P105, Students and income tax at <b>www.cra.gc.ca/forms.</b>
          </li>
          <li>You can claim a <b>part-time</b> education amount if you were enrolled in a <b>specified educational program.</b> Such a program lasts at least three consecutive <b>weeks</b> and requires a minimum of 12 hours of instruction <b>or</b> work each month on courses in the program.
          </li>
          <li>You can claim a <b>full-time</b> education and textbook amount if you were enrolled as a part-time  student in a <b>qualifying educational program</b> and you qualify for the disability amount, or you could not be enrolled full-time in such a program because of a mental or physical impairment, as certified by a medical doctor, optometrist, audiologist, occupational therapist, psychologist, speech-language pathologist, or physiotherapist.
          </li>
          <ul style="list-style-type:disc; font-size:5pt;">
            <li>If you qualified for the part-time education amount for 2013 and you still meet the eligibility<br />requirements in 2014, you do not need to send a new Form T2201, Disability tax credit<br />certificate, to claim the full-time education amount.
            </li>
            <li>If this is a new claim, you must submit a completed and certified Form T2201, Disability tax<br />credit certificate to claim the full-time education amount.
            </li>
            <li>If you could not be enrolled full-time in a qualifying educational program because of a<br />mental or physical impairment, you must submit a signed letter from a medical doctor,<br />optometrist, audiologist, occupational, therapist, psychologist, speech-language<br />pathologist, or physiotherapist, stating this.
            </li>
          </ul>
          <li>For information on the <b>unused current-year</b> tuition, education, and textbook amounts you can transfer, see line 323 in your General income tax and benefit guide and, if applicable,<br />line 5856 in the provincial or territorial pages of your forms book.
          </li>
        </ul>
      </td>
      <td width="9"></td>
      <td width="375" class="hoja2right">
        <ul>
          <li>Remplissez l&#39;<b>annexe 11</b>, Frais de scolarit&eacute;, montant relatif aux &eacute;tudes et montant pour manuels, pour calculer le <b>montant f&eacute;d&eacute;ral</b> que vous pouvez demander &agrave; la ligne 323 de l&#39;annexe 1, Imp&ocirc;t f&eacute;d&eacute;ral, le montant maximum que vous pouvez transf&eacute;rer &agrave; une personne d&eacute;sign&eacute;e et, s&#39;il y a lieu, le montant que vous pouvez reporter &agrave; une ann&eacute;e future.
          </li>
          <li>Sauf si vous r&eacute;sidiez au Qu&eacute;bec, remplissez aussi l&#39;<b>annexe</b> provinciale ou territoriale<b> (S11)</b> de la province ou du territoire  o&ugrave; vous habitiez le 31 d&eacute;cembre pour calculer le <b>montant provincial</b> ou <b>territorial</b> que vous pouvez demander &agrave; la ligne 5856 du formulaire 428, le montant maximum que vous pouvez transf&eacute;rer &agrave; une personne d&eacute;sign&eacute;e et, s&#39;il y a lieu, le montant que vous pouvez reporter &agrave; une ann&eacute;e future.
          </li>
          <li>Vous pouvez demander un montant relatif aux &eacute;tudes &agrave; <b>temps plein</b> si vous &eacute;tiez inscrit &agrave; un <b>programme de formation admissible</b> comme &eacute;tudiant &agrave; temps plein. Un tel programme doit durer au moins trois semaines cons&eacute;cutives et exiger un minimum de 10 heures d&#39;enseignement ou de travail chaque semaine sans compter les heures d&#39;&eacute;tude. Pour en savoir plus sur ce sujet et &agrave; propos du montant <b>pour manuels</b>, consultez la brochure P105, Les &eacute;tudiants et l&#39;imp&ocirc;t, &agrave; <b>www.arc.gc.ca/formulaires.</b>
          </li>
          <li>Vous pouvez demander un montant relatif aux &eacute;tudes &agrave; <b>temps partiel</b> si vous &eacute;tiez inscrit &agrave; un <b>programme de formation d&eacute;termin&eacute;.</b> Un tel programme doit durer au moins trois semaines<br />cons&eacute;cutives et exiger un minimum de 12 heures d&#39;enseignement ou de travail chaque mois.
          </li>
          <li>Si vous &eacute;tiez inscrit &agrave; temps partiel &agrave; un <b>programme de formation admissible</b> et que vous avez droit au montant pour personnes handicap&eacute;es, ou que vous ne pouviez pas &ecirc;tre inscrit &agrave; temps plein &agrave; un tel programme en raison d&#39;une d&eacute;ficience mentale ou physique attest&eacute;e par un m&eacute;decin, un optom&eacute;triste, un audiologiste, un ergoth&eacute;rapeute, un psychologue, un orthophoniste, ou un physioth&eacute;rapeute, vous pouvez demander un montant relatif aux &eacute;tudes &agrave; <b>temps plein</b>.
          </li>
          <ul style="list-style-type:disc; font-size:4.5pt;">
            <li>Si vous vous qualifiez pour le montant relatif aux &eacute;tudes &agrave; temps partiel pour l&#39;ann&eacute;e 2013 et que vous remplissez toujours les<br />conditions d&#39;admissibilit&eacute; en 2014, vous n&#39;avez pas besoin d&#39;envoyer un nouveau formulaire T2201, Certificat pour le cr&eacute;dit<br />d&#39;imp&ocirc;t pour personnes handicap&eacute;es pour demander le cr&eacute;dit du montant relatif aux &eacute;tudes &agrave; temps plein.
            </li>
            <li>S&#39;il s&#39;agit d&#39;une nouvelle demande, vous devez soumettre un formulaire T2201, Certificat pour le cr&eacute;dit d&#39;imp&ocirc;t pour<br />personnes handicap&eacute;es, d&ucirc;ment rempli et certifi&eacute; pour demander le montant relatif aux &eacute;tudes &agrave; temps plein.
            </li>
            <li>Si vous ne pouvez pas &ecirc;tre inscrit &agrave; temps plein dans un programme de formation admissible en raison d&#39;une d&eacute;ficience<br /> mentale ou physique,&nbsp; vous devez pr&eacute;senter une lettre sign&eacute;e par un m&eacute;decin, un optom&eacute;triste, un audiologiste, un<br />ergoth&eacute;rapeute, un psychologue ou un physioth&eacute;rapeute attestant cette situation.
            </li>
          </ul>
          <li>Pour obtenir des pr&eacute;cisions sur la <b>partie inutilis&eacute;e pour l&#39;ann&eacute;e</b> courante de vos frais de scolarit&eacute; et de vos montants relatifs aux &eacute;tudes et pour manuels que vous pouvez transf&eacute;rer, lisez la ligne 323 de votre Guide g&eacute;n&eacute;ral d&#39;imp&ocirc;t et de prestations et, s&#39;il y a lieu, la ligne 5856 des renseignements provinciaux ou territoriaux de votre cahier de formulaires.
          </li>
        </ul>
      </td>
    </tr>
    <tr>
      <td class="hoja2" height="53 px" style="border-bottom-width:1.3px; border-bottom-style:dashed; border-bottom-color:#C3C3C3;"><table cellpadding="4" border="1px"><tr><td ><b>Complete this area if you were enrolled in an institution certified by Employment <br />and Social Development Canada:</b><br />I was enrolled in the course(s) titled _____________________________ to get<br /> or improve skills in the occupation of _________________________________.</td></tr></table>
      </td>
      <td style="border-bottom-style:dashed; border-bottom-color:#C3C3C3; border-weight:1px;"></td>
      <td class="hoja2right" style="border-bottom-style:dashed; border-bottom-color:#C3C3C3; border-weight:1px;"><table cellpadding="4" border="1px"><tr><td><b>Remplissez cette section si vous &eacute;tiez inscrit &agrave; un &eacute;tablissement reconnu par Emploi et<br />D&eacute;veloppement social Canada :</b><br />J&#39;atteste que j&#39;&eacute;tais inscrit au(x) cours intitul&eacute;(s) ____________________________ en vue d&#39;acqu&eacute;rir <br />ou d&#39;am&eacute;liorer des comp&eacute;tences professionnelles pour exercer un emploi de _______________________.</td></tr></table>
      </td>
    </tr>
    <tr>
      <td class="hoja2">
        <ul>
        <li>You can transfer your unused current-year tuition, education, and textbook amounts to<br /><b>one</b> designated individual. That individual can be either your spouse or common-law partner, your parent or grandparent, or your spouse&#39;s or common-law partner&#39;s parent or grandparent. You cannot transfer your unused current-year amounts to your parent or grandparent, or your spouse&#39;s or common-law partner&#39;s parent or grandparent, if your spouse or common-law partner claims the <b>spouse or common-law partner amount or amounts transferred from your spouse or common-law partner</b> on his/her tax return.
        </li>
        <li>If you transfer unused amounts to your spouse or common-law partner, he or she has to complete <b>federal Schedule 2</b>, Federal amounts transferred from your spouse or common- law partner, and, if applicable, <b>provincial</b> or <b>territorial</b> Schedule <b>(S2)</b>, Provincial (or territorial) amounts transferred from your spouse or common-law partner.
        </li>
        </ul>
      </td>
      <td>&nbsp;</td>
      <td class="hoja2">
        <ul>
        <li>Vous pouvez transf&eacute;rer la partie inutilis&eacute;e pour l&#39;ann&eacute;e courante de vos frais de scolarit&eacute; et de vos montants relatifs aux &eacute;tudes et pour manuels &agrave; <b>une</b> personne d&eacute;sign&eacute;e, soit votre &eacute;poux ou conjoint de fait, soit l&#39;un de vos parents ou grands-parents (ou ceux de votre &eacute;poux ou conjoint de fait). Cependant, vous ne pouvez pas transf&eacute;rer la partie inutilis&eacute;e pour l&#39;ann&eacute;e courante &agrave; l&#39;un de vos  parents ou grands- parents (ou ceux de votre &eacute;poux ou conjoint de fait) si votre &eacute;poux ou conjoint de fait demande le <b>montant pour &eacute;poux ou conjoint de fait ou les montants transf&eacute;r&eacute;s de l&#39;&eacute;poux ou conjoint de fait</b> dans sa d&eacute;claration de revenus.
        </li>
        <li>Si vous transf&eacute;rez une partie du montant inutilis&eacute; de vos frais de scolarit&eacute; et de vos montants relatifs aux
        &eacute;tudes et pour manuels &agrave; votre &eacute;poux ou conjoint de fait, il doit remplir l&#39;<b>annexe 2  f&eacute;d&eacute;rale</b>, Montants f&eacute;d&eacute;raux transf&eacute;r&eacute;s de votre &eacute;poux ou conjoint de fait. S&#39;il y a lieu, il doit aussi remplir l&#39;<b>annexe<br />provinciale</b> ou <b>territoriale (S2)</b>, Montants provinciaux (ou territoriaux) transf&eacute;r&eacute;s de votre &eacute;poux ou conjoint de fait.
        </li>
        </ul>
     </td>
    </tr>
    <tr>
      <td class="hoja2"><table cellpadding="2" border="1px"><tr><td><b>Designation for the transfer of an amount to a spouse or common-law partner,<br />parent, or grandparent</b></td></tr></table></td>
      <td></td>
      <td class="hoja2"><table cellpadding="2" border="1px"><tr><td><b>D&eacute;signation pour le transfert d&#39;un montant &agrave; un &eacute;poux ou conjoint de fait, ou &agrave; un des parents ou<br />grands-parents</b></td></tr></table></td>
    </tr>
    <tr>
      <td class="hoja2">
        <table>
          <tr>
            <td width="12%">I designate</td>
            <td style="border-bottom:0.1px #CCCCCC solid; width:40.5%;"></td>
            <td width="5%">, my</td>
            <td style="border-bottom:0.1px #CCCCCC solid; width:42.5%;"></td>
            <td width="5%">,</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td style="text-align:center;">(Individual&#39;s name)</td>
            <td>&nbsp;</td>
            <td style="text-align:center;">(Relationship to you)</td>
          </tr>
        </table>
        to claim:
      </td>
      <td></td>
      <td class="hoja2">
      <table>
          <tr>
            <td width="10%">Je d&eacute;signe</td>
            <td style="border-bottom:0.1px #CCCCCC solid; width:42.5%;">&nbsp;</td>
            <td width="10%">, mon (ma)</td>
            <td  style="border-bottom:0.1px #CCCCCC solid; width:37.5%;"></td>
            <td width="5%">,</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td style="text-align:center;">(Nom de la personne)</td>
            <td>&nbsp;</td>
            <td style="text-align:center;">(Lien de parent&eacute;)</td>
          </tr>
        </table>
        comme personne pouvant demander :
      </td>
    </tr>
    <tr>
      <td class="hoja2">
        <table>
          <tr>
            <td width="20">(1) $</td>
            <td width="60" style=" border-bottom:0.1 #CCCCCC solid;"></td>
            <td width="200">&nbsp;on line 324 of his or her <b>federal Schedule 1,</b> or on line 360</td>
          </tr>
          <tr>
            <td></td>
            <td style="text-align:center;">Federal amount</td>
            <td>&nbsp;of his or her <b>federal Schedule 2,</b> as applicable;</td>
          </tr>
        </table>
      </td>
      <td></td>
      <td class="hoja2">
        <table>
          <tr>
            <td width="20">(1)</td>
            <td width="80" style=" border-bottom:0.1 #CCCCCC solid;"></td>
            <td width="220" >$ &nbsp;&agrave; la ligne 324 de son <b>annexe 1 f&eacute;d&eacute;rale</b> ou &agrave; la ligne 360 de son</td>
          </tr>
          <tr>
            <td></td>
            <td style="text-align:center;">Montant f&eacute;d&eacute;ral</td>
            <td>&nbsp;&nbsp;&nbsp;&nbsp;<b>annexe 2 f&eacute;d&eacute;rale,</b> selon le cas;</td>
          </tr>
        </table>
      </td>
    </tr>
    <tr>
      <td class="hoja2">
        <table>
          <tr>
            <td width="20">(2) $</td>
            <td width="60" style=" border-bottom:0.1 #CCCCCC solid;"></td>
            <td width="200">&nbsp;on line 5860 of his or her <b>provincial</b> or <b>territorial Form 428,</b></td>
          </tr>
          <tr>
            <td></td>
            <td style="text-align:center;">Provincial or</td>
            <td>&nbsp;or on line 5909 of his or her <b>provincial</b> or <b>territorial</b></td>
          </tr>
          <tr>
            <td></td>
            <td style="text-align:center;">territorial amount</td>
            <td>&nbsp;<b>Schedule (S2),</b> as applicable.</td>
          </tr>
        </table>
      </td>
      <td></td>
      <td class="hoja2">
         <table>
          <tr>
            <td width="20">(2) </td>
            <td width="80" style=" border-bottom:0.1 #CCCCCC solid;"></td>
            <td width="230">$ &nbsp; &agrave; la ligne 5860 de son <b>formulaire 428 provincial</b> ou <b>territorial</b> ou</td>
          </tr>
          <tr>
            <td></td>
            <td style="text-align:center;">Montant provincial ou</td>
            <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&agrave; la ligne 5909 de son <b>annexe provinciale</b> ou <b>territoriale (S2),</b></td>
          </tr>
          <tr>
            <td></td>
            <td style="text-align:center;">territorial</td>
            <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;selon le cas.</td>
          </tr>
        </table>
      </td>
    </tr>
    <tr>
      <td class="hoja2">
        <table cellpadding="4">
          <tr>
            <td width="40"><b>Note 1:</b> </td>
            <td width="250">Line (1) above cannot be more than line 23 of your <b>federal Schedule 11.</b></td>
          </tr>
          <tr>
            <td><b>Note 2:</b></td>
            <td>If you resided in a province or territory other than Quebec on December 31,<br />
              line (2) above cannot be more than line 19 (line 23 for Yukon and Nunavut)<br />
              of your <b>provincial</b> or <b>territorial Schedule (S11).</b> If you resided<br />
              in Quebec on December 31, an entry is not required on line (2) above.
            </td>
          </tr>
          <tr>
            <td><b>Note 3:</b></td>
            <td>If you did not reside in the same province or territory as the designated<br />
              individual on December 31, special rules may apply. For more<br />
              information, call <b>1-800-959-8281.</b>
            </td>
          </tr>
        </table>
      </td>
      <td></td>
      <td class="hoja2">
        <table>
          <tr>
            <td width="50"><b>Remarque 1 :</b> </td>
            <td width="300">Le montant indiqu&eacute; &agrave; la ligne 1 ci-dessus ne peut pas d&eacute;passer
              le montant de la ligne 23 <br />de votre <b>annexe 11 f&eacute;d&eacute;rale.</b>
            </td>
          </tr>
          <tr>
            <td><b>Remarque 2 :</b> </td>
            <td>Si vous &eacute;tiez r&eacute;sident d&#39;une province ou d&#39;un territoire autre que le Qu&eacute;bec<br />
              le 31 d&eacute;cembre, le montant indiqu&eacute; &agrave; la ligne 2 ci-dessus ne peut pas d&eacute;passer celui<br />
              de la ligne 19 (ligne 23 pour le Yukon et le Nunavut) de votre <b>annexe provinciale ou</b><br />
              <b>territoriale (S11).</b> Si vous &eacute;tiez r&eacute;sident du Qu&eacute;bec le 31 d&eacute;cembre, vous n&#39;avez pas<br />
              &agrave; inscrire un montant &agrave; la ligne 2 ci-dessus.
            </td>
          </tr>
          <tr>
            <td><b>Remarque 3 :</b> </td>
            <td>Si, le 31 d&eacute;cembre, vous ne r&eacute;sidiez pas dans la m&ecirc;me province ou le m&ecirc;me territoire que<br />
              la personne d&eacute;sign&eacute;e, des r&egrave;gles sp&eacute;ciales peuvent s&#39;appliquer. Pour en savoir plus,<br />
              composez le <b>1-800-959-7383.</b>
            </td>
          </tr>
        </table>
      </td>
    </tr>
    <tr>
      <td class="hoja2" align="center">
        <table style="font-size:15px; font-family:Helvetica; text-align:left;" cellpadding="2px" border="0.5px" >
        <tr>
        <td  width="180" height="20px"><b>Student&#39;s name (print) and signature</b></td>
        <td width="90" height="20px" style="border-top:0.1px #cccccc solid;border-left:0.1px #cccccc solid;border-bottom:0.1px #cccccc solid;border-right:0.1px #cccccc solid;"><b>Social insurance number</b></td>
        <td width="50" height="20px"><b>Date</b></td>
        </tr>
        </table>
        <table>
          <tr>
            <td colspan="3" align="right">See the privacy notice on your return.</td>
          </tr>
        </table>
      </td>
      <td></td>
      <td class="hoja2" align="center">
        <table style="font-size:15px; font-family:Helvetica; text-align:left;" cellpadding="2px" border="0.5px">
        <tr>
        <td width="190" height="20px"><b>Nom (lettres moul&eacute;es) et signature de l&#39;&eacute;tudiant</b></td>
        <td width="85" height="20px"><b>Num&eacute;ro d&#39;assurance sociale</b></td>
        <td width="45" height="20px"><b>Date</b></td>
        <td width="30">
        </td>
        </tr>
        </table>
        <table>
          <tr>
            <td colspan="4" align="right">Consultez l&#39;avis de confidentialit&eacute; dans votre d&eacute;claration.</td>
          </tr>
        </table>
      </td>
    </tr>
    <tr>
      <td style="border-bottom-width:1.3px; border-bottom-style:dashed; border-bottom-color:#C3C3C3;" colspan="3"></td>
    </tr>
  </table>';
  // output the HTML content
  $pdf->writeHTML($style.$htmlcontent, true, 0, true, 0);
  $pdf->AddPage();
  $pdf->writeHTML($htmlcontent2, true, 0, true, 0);
  
  $nombre_archivo = 'T2202A.'.$ds_nombres.' '.$anio.'.pdf';
  //Close and output PDF document
  $pdf->Output($nombre_archivo, 'I');
?>