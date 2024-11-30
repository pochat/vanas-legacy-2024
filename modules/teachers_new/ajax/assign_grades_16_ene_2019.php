<?php
  
  # Libreria de funciones
  require("../../common/lib/cam_general.inc.php");
  # Librerias para el pdf
  require_once('../../../AD3M2SRC4/lib/tcpdf/config/lang/eng.php');
  require_once('../../../AD3M2SRC4/lib/tcpdf/tcpdf.php');
  
  # Recibe parametros
  $fl_entrega_semanal = RecibeParametroNumerico('fl_entrega_semanal');
  $fl_calificacion = RecibeParametroNumerico('fl_calificacion');
  if(empty($fl_calificacion)){
    $fl_calificacion = 'NULL';
  }
  $clave = RecibeParametroNumerico('clave');
  if(!empty($clave))
    $fl_maestro = RecibeParametroNumerico('fl_usuario'); // usuario de admnistracon que asigna la calificacion
  else    
    $fl_maestro = ValidaSesion(False); # Verifica que exista una sesion valida en el cookie y la resetea
  $tab = RecibeParametroHTML('tab');
  
  
  # Verifica que el usuario tenga permiso de usar esta funcion
  if(empty($clave)){
    if(!ValidaPermisoCampus(FUNC_MAESTROS)) {
      MuestraPaginaError(ERR_SIN_PERMISO);
      exit;
    }
  }
  
  # Recupera los datos de la entrega de la semana
  $Query  = "UPDATE k_entrega_semanal SET fl_promedio_semana=$fl_calificacion ";
  $Query .= "WHERE fl_entrega_semanal=$fl_entrega_semanal";
  EjecutaQuery($Query);

  # Don't send notice email if the grade is not actually assigned
  if($fl_calificacion == 'NULL'){
    if(!empty($clave) AND !empty($fl_maestro)){
      echo "<html><body><form name='assig_grade' method='post' action='".PATH_ADM."/modules/campus/students_frm.php'>
      <input type='hidden' name='clave' id='clave' value=$clave>
      </form><script>
      document.assig_grade.submit();
      </script></body></html>"; 
      exit;
    }
    else{
      $result['resultado'] = array(
      "error" => true,
      "mensaje" => "Don't send notice email if the grade is not actually assigned",
      "tab" => $tab
      );  
    }
  }
  
  # Send notification email to the student that has a grade assigned
  # Email Library
  require('/var/www/html/AWS_SES/PHP/com_email_func.inc.php');

  # Load AWS class
  require('/var/www/html/AWS_SES/aws/aws-autoloader.php');  
  use Aws\Common\Aws;

  # Include html parser
  require('/var/www/html/vanas/modules/common/new_campus/lib/simple_html_dom.php'); // produccion
  
  # Initialize Amazon Web Service
  $aws = Aws::factory('/var/www/html/AWS_SES/PHP/config.inc.php');
  
  # Get the client
  $client = $aws->get('Ses');

  # Initialize the sender address
  $from = 'noreply@vanas.ca';

  # Prepare Email Template
  $Query = "SELECT ds_encabezado, ds_cuerpo, ds_pie FROM k_template_doc WHERE fl_template=12 AND fg_activo='1'";
  $grade_template = RecuperaValor($Query);
  $ds_template = str_uso_normal($grade_template[0].$grade_template[1].$grade_template[2]);

  # Create a DOM object
  $ds_template_html = new simple_html_dom();

  # Teacher's info
  $Query = "SELECT ds_nombres, ds_apaterno FROM c_usuario WHERE fl_usuario=$fl_maestro";
  $row = RecuperaValor($Query);
  $te_fname = $row[0];
  $te_lname = $row[1];
  

  # Lesson info
  $Query  = "SELECT b.ds_nombres, b.ds_apaterno, d.no_semana, d.ds_titulo, b.ds_email ";
  $Query .= ",e.ds_add_number, e.ds_add_street, e.ds_add_city, e.ds_add_state, e.ds_add_zip, f.ds_pais, a.fl_alumno, g.nb_programa, b.cl_sesion, a.fl_semana ";
  $Query .= "FROM k_entrega_semanal a ";
  $Query .= "LEFT JOIN c_usuario b ON b.fl_usuario=a.fl_alumno ";
  $Query .= "LEFT JOIN k_semana c ON c.fl_semana=a.fl_semana ";
  $Query .= "LEFT JOIN c_leccion d ON d.fl_leccion=c.fl_leccion ";
  $Query .= "LEFT JOIN k_ses_app_frm_1 e ON e.cl_sesion=b.cl_sesion ";
  $Query .= "LEFT JOIN c_pais f ON e.ds_add_country=f.fl_pais ";
  $Query .= "LEFT JOIN c_programa g ON g.fl_programa=e.fl_programa ";
  $Query .= "WHERE fl_entrega_semanal=$fl_entrega_semanal ";
  $Query .= "AND b.fg_activo='1' ";
  $row = RecuperaValor($Query);
  $st_fname = $row[0];
  $st_lname = $row[1];
  $no_week = $row[2];
  $ds_title = $row[3];
  $ds_email = $row[4];
  $ds_add_number = $row[5];
  $ds_add_street = $row[6];
  $ds_add_city = $row[7];
  $ds_add_state = $row[8];
  $ds_add_zip = $row[9];
  $ds_add_country = $row[10];
  $st_lmadd = $ds_add_number." ".$ds_add_street.", ".$ds_add_city." ".$ds_add_state.",".$ds_add_country;
  $fl_alumno = $row[11];
  $nb_programa = str_texto($row[12]);
  $cl_sesion = str_texto($row[13]);
  # Obtenemos el flsesion del alumno
  $rowsesion = RecuperaValor("SELECT fl_sesion FROM c_sesion WHERE cl_sesion='$cl_sesion' ");
  $fl_sesion = $rowsesion[0];
  $fl_semana = $row[14];
  
  # Vamos actualizar los GPA terms y GPA program_gpa
  $QueryT  = "SELECT SUM(i.no_equivalencia)/COUNT(a.fl_semana), a.fl_term, no_grado, c.fl_alumno ";
  $QueryT .= "FROM k_semana a, k_term b, k_entrega_semanal c, c_calificacion i ";
  $QueryT .= "WHERE a.fl_term=b.fl_term AND a.fl_semana=c.fl_semana AND c.fl_promedio_semana=i.fl_calificacion ";
  $QueryT .= "AND a.fl_term IN(SELECT fl_term FROM k_alumno_term e WHERE e.fl_alumno=c.fl_alumno AND c.fl_alumno=$fl_alumno) ";
  $QueryT .= "GROUP BY a.fl_term ";
  $rsT = EjecutaQuery($QueryT);
  for($i=0;$rowT=RecuperaRegistro($rsT);$i++){    
    EjecutaQuery("UPDATE k_alumno_term SET no_promedio='".$rowT[0]."' WHERE fl_alumno=$rowT[3] AND fl_term=$rowT[1] ");
  }
  
  # Obtenemos el promedio de los terms que curso el student
  $Querypgpa  = "SELECT MAX(a.fl_term) FROM k_alumno_term a, k_term b, c_periodo c ";
  $Querypgpa .= "WHERE a.fl_term=b.fl_term AND b.fl_periodo=c.fl_periodo ";
  $Querypgpa .= "AND a.fl_alumno=$fl_alumno GROUP BY b.no_grado ORDER BY c.fe_inicio, b.no_grado";
  $consulta = EjecutaQuery($Querypgpa);
  for($k=0;$rowpgpa = RecuperaRegistro($consulta);$k++){
    $fl_termt = $rowpgpa[0];
    $row0 = RecuperaValor("SELECT no_promedio FROM k_alumno_term WHERE fl_term=$fl_termt AND fl_alumno=$fl_alumno");
    if($row0[0]>0){
      $promediopgpa++;
      $promedio_x_t += $row0[0];
    }
  }
  # Actuaizamos el program GPA
  EjecutaQuery("UPDATE c_alumno SET no_promedio_t='".round($promedio_x_t/$promediopgpa)."' WHERE fl_alumno=$fl_alumno");

  # Obtenemos la calificacion del term y el grado actual
  $QueryTerm  = "SELECT (SELECT cl_calificacion FROM c_calificacion WHERE no_min <=ROUND(no_promedio) AND no_max >=ROUND(no_promedio)), no_promedio ";
  $QueryTerm .= "FROM k_alumno_term WHERE fl_term=".ObtenTermAlumno($fl_alumno)." AND fl_alumno=$fl_alumno";
  $rowc = RecuperaValor($QueryTerm);
  $cl_cal_term = $rowc[0];
  $no_grado = ObtenGradoAlumno($fl_alumno);
  $current_term_promedio = round($rowc[1]);
  $current_term_gpa = $cl_cal_term." ".$current_term_promedio."%";
  # Obtenemos el promedio general del curso
  $QueryGPA  = "SELECT (SELECT cl_calificacion FROM c_calificacion WHERE no_min <= ROUND(no_promedio_t) AND no_max >= ROUND(no_promedio_t)), ";
  $QueryGPA .= "no_promedio_t FROM c_alumno WHERE fl_alumno=$fl_alumno ";
  $row2 = RecuperaValor($QueryGPA);
  $gpa_grl = $row2[0]." ".round($row2[1])."%";
  $row3 = RecuperaValor("");
    if(empty($gpa_grl))
    $gpa_grl = "(No assigment)";
  $no_promedio_t = $row2[1];
  
  $variables1 = array(
    'st_fname' => $st_fname,
    'st_lname' => $st_lname,
    'te_fname' => $te_fname,
    'te_lname' => $te_lname,
    'no_week' => $no_week,
    'ds_title' => $ds_title,
    'st_lmadd' => $st_lmadd,
    'st_country' => $ds_add_country,
    'st_lmaddpc' => $ds_add_zip,
    'current_term_gpa' => $current_term_gpa,
    'no_grado' => $no_grado,
    'pg_name' => $nb_programa,
    'program_gpa' => $gpa_grl,
    'fl_sesion' => $fl_sesion
  );

  #ecuperamos la cuentas para enviar copias de email.
  $Query="SELECT  a.ds_email_r,b.cl_sesion 
		  FROM k_presponsable a 
		  WHERE b.fl_usuario=$fl_alumno  ";
  $row=RecuperaValor($Query);
  $ds_email_responsable=$row['ds_email_r'];

  # Generate the email template with the variables
  $ds_email_template = GenerateTemplate($ds_template, $variables1);

  # Load the template into html
  $ds_template_html->load($ds_email_template);
  # Get base url (domain)
  $base_url = $ds_template_html->getElementById("login-redirect")->href;
  # Set url path and query string
  $component_week = "week=".$no_week;
  $component_tab = "&tab=critique";
  $ds_template_html->getElementById("login-redirect")->href = $base_url."/modules/students_new/index.php#ajax/desktop.php?".$component_week.$component_tab;
 echo $client;
  SendNoticeMail($client, $from, $ds_email, 'New Grade Assigned', $ds_template_html);
  
  # Si existe algun problema solo con desactivar ya no funcionara este proceso
  if(ObtenConfiguracion(81)){
    # Correo para appy
    // $apply=ObtenConfiguracion(83);
    $apply=ObtenConfiguracion(83);
    
    # Calificacion Minima aprovada
    $reprovada  = "SELECT cl_calificacion, no_min FROM c_calificacion ";
    $reprovada .= "WHERE no_equivalencia=(SELECT MIN(no_equivalencia) FROM c_calificacion WHERE fg_aprobado='1') ";
    $rowr = RecuperaValor($reprovada);
    $cl_calificacion = $rowr[0];
    $no_equivalencia = round($rowr[1]);
    $calificacion_min = $cl_calificacion." ".$no_equivalencia."%";
    
    # Inicializa variables de ambiente para envio de correo adjunto
    ini_set("SMTP", MAIL_SERVER);
    ini_set("smtp_port", MAIL_PORT);
    ini_set("sendmail_from", MAIL_FROM);
    
    # Emelemtos para el envio de correos warnings
    $eol = "\n";
    $separator = md5(time());
    $headers  = 'From: '.$from.' <'.$from.'>'.$eol;
    $headers .= 'Bcc:'.$apply.'' . "\r\n";
    $headers .= 'MIME-Version: 1.0' .$eol;
    $headers .= "Content-Type: multipart/mixed; boundary=\"".$separator."\"";
    
    // Fecha para nombre del archivo
    $row = RecuperaValor("SELECT DATE_FORMAT(CURRENT_DATE(),'%d-%m-%Y') ");
    $Date = $row[0];

    # Verificacion de la calificacion si no es aprovatoria mandara un correo al alumno 
    $row1 = RecuperaValor("SELECT fg_aprobado,cl_calificacion,no_equivalencia FROM c_calificacion WHERE fl_calificacion=$fl_calificacion");
    $aprovado = $row1[0];
    $current_week_grade = $row1[1]." ".round($row1[2])."%";
    if(empty($aprovado) && $fl_calificacion>0){
      
      # Template para calificaciones no aprovadas
      $Query1 = "SELECT ds_encabezado, ds_cuerpo, ds_pie, nb_template FROM k_template_doc WHERE fl_template=30 AND fg_activo='1'";
      $grade_warning = RecuperaValor($Query1);
      $template_grade_warning = str_uso_normal($grade_warning[0].$grade_warning[1].$grade_warning[2]);
      $template_warning = str_texto($grade_warning[3]);
      
      $variables2 = array(
        'st_fname' => $st_fname,
        'st_lname' => $st_lname,
        'te_fname' => $te_fname,
        'te_lname' => $te_lname,
        'no_week' => $no_week,
        'current_week_grade' => $current_week_grade,
        'ds_title' => $ds_title,
        'st_lmadd' => $st_lmadd,
        'st_country' => $ds_add_country,
        'st_lmaddpc' => $ds_add_zip,
        'current_term_gpa' => $current_term_gpa,
        'no_grado' => $no_grado,
        'pg_name' => $nb_programa,
        'program_gpa' => $gpa_grl,
        'fl_sesion' => $fl_sesion,
        'fl_template' => 30,
        'minimum_gpa' => $calificacion_min
      );
      
      # Template
      $Hwarning = GenerateTemplate($grade_warning[0],$variables2);
      $Bwarning = GenerateTemplate($grade_warning[1],$variables2);
      $Fwarning = GenerateTemplate($grade_warning[2],$variables2);
      $ds_email_warning = $Hwarning.$Bwarning.$Fwarning;
      
      # PDF
      class ConPiesweek extends TCPDF {
        // Header 
        function Header(){
          $this->writeHTML($Hwarning, true, 0, true, 0); 
        }
        // Footer
        function Footer(){
          $this->SetY(-20);
          $this->writeHTML($Fwarning, true, 0, true, 0); 
        }
      }

      // Creamos un nuevo objeto usando la clase extendida classpies
      $pdf = new ConPiesweek();
      $pdf->SetFont('times','',10);

      // Add a page
      $pdf->AddPage("P"); 
      
      // Output the HTML content
      $pdf->writeHTMLCell(180, 100, 10,30,$Bwarning, 0, 0, false, true,'',true);
      
      // Nombre del archivo
      $fileName = $fl_sesion."30".$Date.'.pdf';
      
      // Pasamos el archivo a base64
      $fileattweek = $pdf->Output($fileName, 'E'); //genera la codificacion para enviar adjuntado el archivo
      
      // Mensaje email
      $messageW  = "--".$separator.$eol;
      $messageW .= "Content-Type: text/html; boundary=\"".$separator."\" ".$eol.$eol;
      $messageW .= $ds_email_warning.$eol;
      $messageW .= "--".$separator.$eol;
      $messageW .= $fileattweek;
      $messageW .= "--".$separator."--".$eol;
   
      if(mail($ds_email, $template_warning, $messageW, $headers)){        
        # Insertamos los datos del email enviado parq eu se congelen
        $QueryGrade  = "INSERT INTO k_alumno_template (fl_alumno,fl_template,fe_envio,ds_header,ds_body,ds_footer) ";
        $QueryGrade .= "VALUES ($fl_sesion,30,CURRENT_TIMESTAMP,'$Hwarning','$Bwarning','$Fwarning')";
        EjecutaQuery($QueryGrade);
      }
    }
    
    # Verificamos si la Program GPA no es aprobatorio mandara una notification
    $rowPGPA = RecuperaValor("SELECT cl_calificacion, fg_aprobado FROM c_calificacion WHERE no_min <= ROUND($no_promedio_t) AND no_max >= ROUND($no_promedio_t)");
    $aprovadoPGPA = $rowPGPA[1];
    if(empty($aprovadoPGPA)){
      
      # Variables
      $variables3 = array(
        'st_fname' => $st_fname,
        'st_lname' => $st_lname,
        'te_fname' => $te_fname,
        'te_lname' => $te_lname,
        'no_week' => $no_week,
        'ds_title' => $ds_title,
        'st_lmadd' => $st_lmadd,
        'st_country' => $ds_add_country,
        'st_lmaddpc' => $ds_add_zip,
        'current_term_gpa' => $current_term_gpa,
        'no_grado' => $no_grado,
        'pg_name' => $nb_programa,
        'program_gpa' => $gpa_grl,
        'minimum_gpa' => $calificacion_min
      );
      
      # Template para terms no aprovados
      $templatePGPA = "SELECT ds_encabezado, ds_cuerpo, ds_pie, nb_template FROM k_template_doc WHERE fl_template=29 AND fg_activo='1'";
      $rowPGPA1 = RecuperaValor($templatePGPA);    
      $template_PGPA = str_uso_normal($rowPGPA1[0].$rowPGPA1[1].$rowPGPA1[2]);
      $template_warningPGPA = str_texto($rowPGPA1[3]);
      
      # Template
      $headerPGPA = GenerateTemplate(genera_documento($fl_sesion, 1, 29),$variables3);
      $bodyPGPA = GenerateTemplate(genera_documento($fl_sesion, 2, 29),$variables3);
      $footerPGPA = GenerateTemplate(genera_documento($fl_sesion, 3, 29),$variables3);
      $ds_email_warningPGPA = $headerPGPA.$bodyPGPA.$footerPGPA;
      
      # PDF
      class ConPiesterm extends TCPDF {
        // Header 
        function Header(){
          $this->writeHTML($headerPGPA, true, 0, true, 0); 
        }
        // Footer
        function Footer(){
          $this->SetY(-20);
          $this->writeHTML($footerPGPA, true, 0, true, 0); 
        }
      }

      // Creamos un nuevo objeto usando la clase extendida classpies
      $pdf = new ConPiesterm();
      $pdf->SetFont('times','',10);

      // Add a page
      $pdf->AddPage("P"); 
      
      // Output the HTML content
      $pdf->writeHTMLCell(180, 100, 10,30,$bodyPGPA, 0, 0, false, true,'',true);
      
      // Nombre del archivo
      $fileNameterm = $fl_sesion."29".$Date.'.pdf';
      
      // Pasamos el archivo a base64
      $fileattterm = $pdf->Output($fileNameterm, 'E'); //genera la codificacion para enviar adjuntado el archivo
      
      // Mensaje email
      $messagePGPA  = "--".$separator.$eol;
      $messagePGPA .= "Content-Type: text/html; boundary=\"".$separator."\" ".$eol.$eol;
      $messagePGPA .= $ds_email_warningPGPA.$eol;
      $messagePGPA .= "--".$separator.$eol;
      $messagePGPA .= $fileattterm;
      $messagePGPA .= "--".$separator."--".$eol;
      
      if(mail($ds_email, $template_warningPGPA, $messagePGPA, $headers)){               
        # Insertamos los datos del email enviado parq eu se congelen
        $QueryPGAP  = "INSERT INTO k_alumno_template (fl_alumno,fl_template,fe_envio,ds_header,ds_body,ds_footer) ";
        $QueryPGAP .= "VALUES ($fl_sesion,29,CURRENT_TIMESTAMP,'$headerPGPA','$bodyPGPA','$footerPGPA')";
        EjecutaQuery($QueryPGAP);
      }
    }

    # Verificamos si es la penultima semana y su GPA term no aprovado se enviara un mensaje de advertencia
    # Obtenemos la penultima seman del term que esta cursando
    $rowPS = RecuperaValor("SELECT MAX(fl_semana)-1 FROM k_semana WHERE fl_term=(SELECT MAX(fl_term) FROM k_alumno_term WHERE fl_alumno=$fl_alumno)");
    $semana_penultima =$rowPS[0];
    $semana_actual = $fl_semana;
    $rowTGPA = RecuperaValor("SELECT cl_calificacion, fg_aprobado FROM c_calificacion WHERE no_min <= ROUND($current_term_promedio) AND no_max >= ROUND($current_term_promedio)");
    $aprovadoTGPA = $rowTGPA[1];
    if($semana_actual==$semana_penultima AND empty($aprovadoTGPA)){
      # Template para calificaciones no aprovadas
      $templateTGPA = "SELECT ds_encabezado, ds_cuerpo, ds_pie, nb_template FROM k_template_doc WHERE fl_template=31 AND fg_activo='1'";
      $rowTGPA1 = RecuperaValor($templateTGPA);
      $template_TGPA = str_uso_normal($rowTGPA1[0].$rowTGPA1[1].$rowTGPA1[2]);
      $template_warningTGPA = str_texto($rowTGPA1[3]);

      $variables3 = array(
        'st_fname' => $st_fname,
        'st_lname' => $st_lname,
        'te_fname' => $te_fname,
        'te_lname' => $te_lname,
        'no_week' => $no_week,
        'ds_title' => $ds_title,
        'st_lmadd' => $st_lmadd,
        'st_country' => $ds_add_country,
        'st_lmaddpc' => $ds_add_zip,
        'current_term_gpa' => $current_term_gpa,
        'no_grado' => $no_grado,
        'pg_name' => $nb_programa,
        'program_gpa' => $gpa_grl,
        'minimum_gpa' => $calificacion_min
      );
      
      # Template    
      $headerTGPA = GenerateTemplate(genera_documento($fl_sesion, 1, 31));
      $bodyTGPA =  GenerateTemplate(genera_documento($fl_sesion, 2, 31));
      $footerTGPA =  GenerateTemplate(genera_documento($fl_sesion, 3, 31));
      $ds_email_warningTGPA = $headerTGPA.$bodyTGPA.$footerTGPA;
          
      # PDF
      class ConPies extends TCPDF {
        // Header 
        function Header(){
          $this->writeHTML($headerTGPA, true, 0, true, 0); 
        }
        // Footer
        function Footer(){
          $this->SetY(-20);
          $this->writeHTML($footerTGPA, true, 0, true, 0); 
        }
      }

      // Creamos un nuevo objeto usando la clase extendida classpies
      $pdfp = new ConPies();
      $pdfp->SetFont('times','',10);

      // Add a page
      $pdfp->AddPage("P"); 
      
      // Output the HTML content
      $pdfp->writeHTMLCell(180, 100, 10,30,$bodyTGPA, 0, 0, false, true,'',true);
      
      // Nombre del archivo
      $fileNamep = $fl_sesion."31".$Date.'.pdf';
      
      // Pasamos el archivo a base64
      $fileattprogram = $pdfp->Output($fileNamep, 'E'); //genera la codificacion para enviar adjuntado el archivo
      
      // Mensaje email
      $messagePGPA  = "--".$separator.$eol;
      $messagePGPA .= "Content-Type: text/html; boundary=\"".$separator."\" ".$eol.$eol;
      $messagePGPA .= $ds_email_warningTGPA.$eol;
      $messagePGPA .= "--".$separator.$eol;
      $messagePGPA .= $fileattprogram;
      $messagePGPA .= "--".$separator."--".$eol;
      
      if(mail($ds_email, $template_warningTGPA, $messagePGPA, $headers)){
        # Insertamos los datos del email enviado parq eu se congelen
        $QueryTGPA  = "INSERT INTO k_alumno_template (fl_alumno,fl_template,fe_envio,ds_header,ds_body,ds_footer) ";
        $QueryTGPA .= "VALUES ($fl_sesion,31,CURRENT_TIMESTAMP,'$headerTGPA','$bodyTGPA','$footerTGPA')";
        EjecutaQuery($QueryTGPA);    
      }
    }
  }
  
  # Redirige al listado
  if(!empty($clave) AND !empty($fl_maestro) ){
    echo "<html><body><form name='assig_grade' method='post' action='".PATH_ADM."/modules/campus/students_frm.php'>
    <input type='hidden' name='clave' id='clave' value=$clave>
    </form><script>
    document.assig_grade.submit();
    </script></body></html>"; 
    exit;
  }
  else{
    $result['resultado'] = array(
    "error" => false,
    "mensaje" => "",
    "tab" => $tab
    );
  }
  echo json_encode((Object) $result);
?>